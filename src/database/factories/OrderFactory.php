<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Order::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'payment_method' => 1,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区1-1-1',
            'shipping_building' => 'テストビル',
        ];
    }
}
