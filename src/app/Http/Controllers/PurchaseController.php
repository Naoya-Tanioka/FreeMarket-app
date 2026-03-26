<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $user = auth()->user();

        $item = DB::table('items')->where('id', $item_id)->first();

        if (!$item) {
            abort(404);
        }

        $profile = DB::table('profiles')
            ->where('user_id', $user->id)
            ->first();

        $sessionAddress = session('purchase_address_' . $item_id);

        $shipping = [
            'post_code' => $sessionAddress['post_code'] ?? ($profile->post_code ?? ''),
            'address'   => $sessionAddress['address'] ?? ($profile->address ?? ''),
            'building'  => $sessionAddress['building'] ?? ($profile->building ?? ''),
        ];

        return view('purchase', compact('item', 'shipping'));
    }

    public function editAddress($item_id)
    {
        $user = auth()->user();

        $profile = DB::table('profiles')
            ->where('user_id', $user->id)
            ->first();

        $sessionAddress = session('purchase_address_' . $item_id);

        $shipping = [
            'post_code' => $sessionAddress['post_code'] ?? ($profile->post_code ?? ''),
            'address'   => $sessionAddress['address'] ?? ($profile->address ?? ''),
            'building'  => $sessionAddress['building'] ?? ($profile->building ?? ''),
        ];

        return view('purchase_address', compact('item_id', 'shipping'));
    }

    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'post_code' => 'required',
            'address'   => 'required',
            'building'  => 'nullable',
        ], [
            'post_code.required' => '郵便番号を入力してください',
            'address.required'   => '住所を入力してください',
        ]);

        session([
            'purchase_address_' . $item_id => [
                'post_code' => $request->post_code,
                'address'   => $request->address,
                'building'  => $request->building,
            ]
        ]);

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    public function store(Request $request, $item_id)
    {
        $request->validate([
            'payment_method' => 'required|in:1,2',
        ], [
            'payment_method.required' => '支払い方法を選択してください',
        ]);

        $user = auth()->user();

        $item = DB::table('items')->where('id', $item_id)->first();

        if (!$item) {
            abort(404);
        }

        // すでに購入済みなら購入させない
        if ((int)$item->status === 3) {
            return redirect()->route('items.detail', ['item_id' => $item_id]);
        }

        // 二重購入防止
        $ordered = DB::table('orders')
            ->where('item_id', $item_id)
            ->exists();

        if ($ordered) {
            return redirect()->route('items.detail', ['item_id' => $item_id]);
        }

        $profile = DB::table('profiles')
            ->where('user_id', $user->id)
            ->first();

        $sessionAddress = session('purchase_address_' . $item_id);

        $shippingPostCode = $sessionAddress['post_code'] ?? ($profile->post_code ?? '');
        $shippingAddress  = $sessionAddress['address'] ?? ($profile->address ?? '');
        $shippingBuilding = $sessionAddress['building'] ?? ($profile->building ?? '');

        DB::transaction(function () use (
            $item_id,
            $user,
            $request,
            $shippingPostCode,
            $shippingAddress,
            $shippingBuilding,
        ) {
            DB::table('orders')->insert([
                'item_id'            => $item_id,
                'buyer_id'           => $user->id,
                'payment_method'     => $request->payment_method,
                'shipping_post_code' => $shippingPostCode,
                'shipping_address'   => $shippingAddress,
                'shipping_building'  => $shippingBuilding,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            DB::table('items')
                ->where('id', $item_id)
                ->update([
                    'status' => 3,
                    'updated_at' => now(),
                ]);
        });

        session()->forget('purchase_address_' . $item_id);

        return redirect()->route('profile.show', ['tab' => 'buy']);
    }
}