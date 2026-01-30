<?php

namespace Modules\Orders\Tests\Feature;

use Tests\TestCase;
use Modules\Products\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Enums\OrderStatusEnum;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    /** @test */
    public function it_creates_order_successfully()
    {
        $product1 = Product::create([
            'name' => 'first product',
            'price' => 50,
        ]);

        $product2 = Product::create([
            'name' => 'second product',
            'price' => 30,
        ]);
        $payload = [
            'items' => [
                [
                    'id' => $product1->id,
                    'quantity' => 2,
                    'price' => 50,
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 1,
                    'price' => 30,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order created successfully',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => OrderStatusEnum::PENDING->value,
            'total_price' => 130, 
        ]);

        $order = Order::first();

        $this->assertEquals(2, OrderItem::where('order_id', $order->id)->count());
    }

    /** @test */
    public function it_requires_items()
    {
        $response = $this->postJson('/api/orders', []);

        $response
            ->assertStatus(400)
            ->assertJson(['success' => false, 'message' => 'The items field is required.']);
    }
}
