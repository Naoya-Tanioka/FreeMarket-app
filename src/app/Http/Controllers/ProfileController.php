<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();

        $profile = DB::table('profiles')
            ->where('user_id', $user->id)
            ->first();

        $tab = $request->query('tab', 'sell');

        if ($tab === 'buy') {
            $items = DB::table('items')
                ->join('orders', 'items.id', '=', 'orders.item_id')
                ->where('orders.buyer_id', $user->id)
                ->select('items.*')
                ->orderByDesc('items.created_at')
                ->get();
        } else {
            $items = DB::table('items')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('mypage', compact('user', 'profile', 'items', 'tab'));
    }
   public function edit()
    {
        $user = auth()->user();

        $profile = DB::table('profiles')
            ->where('user_id', $user->id)
            ->first();

        return view('profile', compact('user','profile'));
    }

    // update() は後でここにまとめる（今ある update をそのまま移す）
    public function update(ProfileRequest $request)
{
    $user = auth()->user();

    DB::transaction(function () use ($request, $user) {
        // users.name 更新
        DB::table('users')->where('id', $user->id)->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);

        // user_addresses（デフォルト住所）更新 or 作成
        $exists = DB::table('profiles')
            ->where('user_id', $user->id)
            ->exists();

        $data = [
            'name' => $request->name,
            'post_code' => $request->post_code,
            'address'    => $request->address,
            'building'    => $request->building,
            'updated_at'  => now(),
        ];

        if ($request->hasFile('image')) {
                $path = $request->file('image')->store('profile_images', 'public');
                $data['image'] = 'storage/' . $path;
            }

        if ($exists) {
            DB::table('profiles')
                ->where('user_id', $user->id)
                ->update($data);
        } else {
            DB::table('profiles')->insert($data + [
                'user_id' => $user->id,
                'created_at' => now(),
            ]);
        }
    });

    return redirect()->route('items.index'); // 更新後は商品一覧へ
}
}
