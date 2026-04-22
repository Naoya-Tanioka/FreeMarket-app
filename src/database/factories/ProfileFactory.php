<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'post_code' => '123-4567',
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(),
            'image' => 'storage/profiles/default.png',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}