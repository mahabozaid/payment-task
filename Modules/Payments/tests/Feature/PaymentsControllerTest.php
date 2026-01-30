<?php

namespace Modules\Payments\tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Orders\Models\Order;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Orders\Enums\OrderStatusEnum;

class PaymentsControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user,'api');
    }

    /** @test */
    public function it_processes_payment_successfully()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);

        $payload = [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'data' => [
                'amount' => 150,
                'card' => '1123555-52255'
            ]
        ];

        $response = $this->postJson("api/payments/orders/$order->id/pay", $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Payment processed successfully',
                 ]);

       

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'method' => PaymentMethodEnum::PAYPAL->value
        ]);
    }

    /** @test */
    public function it_fails_if_order_not_confirmed()
    {
        $order = $this->createOrder(OrderStatusEnum::PENDING);
        $payload = [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'data' => ['amount' => 100]
        ];

        $response = $this->postJson("/api/payments/orders/{$order->id}/pay", $payload);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Payments can only be processed for confirmed orders.'
        ]);
    }

    /** @test */
    public function it_fails_if_user_does_not_own_order()
    {
        $otherUserOrder = Order::create([
            'user_id' => User::factory()->create()->id,
            'status' => OrderStatusEnum::CONFIRMED,
            'total_price' => 200
        ]);

        $payload = [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'data' => ['amount' => 200]
        ];

        $response = $this->postJson("/api/payments/orders/{$otherUserOrder->id}/pay", $payload);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

     /** @test */
    public function pay_endpoint_requires_valid_method_and_data()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);
        $response = $this->postJson("/api/payments/orders/{$order->id}/pay", []);
        $response->assertStatus(400); 

        $response = $this->postJson("/api/payments/orders/{$order->id}/pay", [
            'method' => 'INVALID_METHOD',
            'data' => ['amount' => 100]
        ]);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'The selected method is invalid.']);
    }

    private function createOrder(OrderStatusEnum $status)
    {
       return Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => $status,
            'total_price' => 100,
        ]);
    }
}
