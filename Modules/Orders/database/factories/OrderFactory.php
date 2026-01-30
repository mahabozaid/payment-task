<?php

namespace Modules\Orders\database\factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Enums\OrderStatusEnum;
use Modules\Auth\Models\User;
use Modules\Orders\Models\Order;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 0, 1000),
            'status' => OrderStatusEnum::CONFIRMED
        ];
    }
}
