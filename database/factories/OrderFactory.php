<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'shipped', 'delivered', 'canceled']),
            'total_price' => fake()->randomFloat(2, 100, 10000),
            'shipping_address' => fake()->address(),
            'billing_address' => fake()->address(),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed']),
        ];
    }
}
