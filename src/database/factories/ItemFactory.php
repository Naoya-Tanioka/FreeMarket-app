<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model =Item::class;
    
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'brand_name' => $this->faker->optional()->word(),
            'image' => 'storage/items/sample.jpg',
            'condition' => 1,
            'description' => $this->faker->sentence(),
            'price' => 1000,
            'status' => 1,
        ];
    }
}
