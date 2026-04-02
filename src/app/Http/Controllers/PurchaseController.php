<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

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

        $stripePaymentMethod = $request->payment_method == 1 ? 'konbini' : 'card';

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = Session::create([
            'mode' => 'payment',
            'payment_method_types' => [$stripePaymentMethod],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int)$item->price,
                    'product_data' => [
                        'name' => $item->name,
                        'images' => [asset($item->image)],
                    ],
                ],
            ]],
            'success_url' => route('purchase.success', ['item_id' => $item_id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.cancel', ['item_id' => $item_id]),
            'metadata' => [
                'item_id' => $item_id,
                'buyer_id' => $user->id,
                'payment_method' => $request->payment_method,
                'shipping_post_code' => $shippingPostCode,
                'shipping_address' => $shippingAddress,
                'shipping_building' => $shippingBuilding,
            ],
        ]);

        return redirect($checkoutSession->url);
    }

    public function success($item_id)
    {
        return redirect()->route('profile.show', ['tab' => 'buy']);
    }

    public function cancel($item_id)
    {
        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    /**
     * Stripe Webhook
     * checkout.session.completed 受信時に orders 作成 + items.status更新
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata ?? null;

            if (!$metadata) {
                return response('No metadata', 400);
            }

            $itemId = (int) $metadata->item_id;
            $buyerId = (int) $metadata->buyer_id;

            // すでに注文済みなら何もしない
            $alreadyOrdered = DB::table('orders')
                ->where('item_id', $itemId)
                ->exists();

            if (!$alreadyOrdered) {
                DB::transaction(function () use ($metadata, $itemId, $buyerId) {
                    DB::table('orders')->insert([
                        'item_id' => $itemId,
                        'buyer_id' => $buyerId,
                        'payment_method' => (int) $metadata->payment_method,
                        'shipping_post_code' => $metadata->shipping_post_code,
                        'shipping_address' => $metadata->shipping_address,
                        'shipping_building' => $metadata->shipping_building,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('items')
                        ->where('id', $itemId)
                        ->update([
                            'status' => 3,
                            'updated_at' => now(),
                        ]);
                });
            }
        }

        return response('Webhook handled', 200);
    }
}