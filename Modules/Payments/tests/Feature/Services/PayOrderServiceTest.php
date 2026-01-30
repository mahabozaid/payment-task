<?php

namespace Modules\Payments\tests\Feature\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Payments\Services\PayOrderService;
use Modules\Orders\Models\Order;
use Modules\Payments\Models\Payment;
use Modules\Orders\Enums\OrderStatusEnum;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Auth\Models\User;
use App\Exceptions\LogicalException;

class PayOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PayOrderService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PayOrderService();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_successfully_creates_payment_for_confirmed_order()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);
        $paymentData = [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'amount' => 100
        ];

        $result = $this->service->execute($order->id, $paymentData);

        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals($order->id, $result->order_id);
        $this->assertNotNull($result->payment_id);
        $this->assertContains($result->status, [
            PaymentStatusEnum::SUCCESSFUL,
            PaymentStatusEnum::PENDING,
            PaymentStatusEnum::FAILED
        ]);
        $this->assertEquals(PaymentMethodEnum::PAYPAL, $result->method);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
        ]);
    }

    /** @test */
    public function it_throws_exception_when_order_is_not_confirmed()
    {
        $order = $this->createOrder(OrderStatusEnum::PENDING);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('Payments can only be processed for confirmed orders.');

        $this->service->execute($order->id, [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'amount' => 100
        ]);
    }

    /** @test */
    public function it_throws_exception_when_order_has_successful_payment()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);

        Payment::create([
            'order_id' => $order->id,
            'payment_id' => '123456',
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'method' => PaymentMethodEnum::PAYPAL,
        ]);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('This Order has Payment already completed or in progress.');

        $this->service->execute($order->id, [
            'method' => PaymentMethodEnum::CREDIT_CARD->value,
            'amount' => 100
        ]);
    }

    /** @test */
    public function it_throws_exception_when_order_has_pending_payment()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);

        Payment::create([
            'order_id' => $order->id,
            'payment_id' => '123456',
            'status' => PaymentStatusEnum::PENDING,
            'method' => PaymentMethodEnum::PAYPAL,
        ]);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage('This Order has Payment already completed or in progress.');

        $this->service->execute($order->id, [
            'method' => PaymentMethodEnum::CREDIT_CARD->value,
            'amount' => 100
        ]);
    }

    /** @test */
    public function it_allows_payment_when_previous_payment_failed()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);
        Payment::create([
            'order_id' => $order->id,
            'payment_id' => '123456',
            'status' => PaymentStatusEnum::FAILED,
            'method' => PaymentMethodEnum::PAYPAL,
        ]);

        $result = $this->service->execute($order->id, [
            'method' => PaymentMethodEnum::CREDIT_CARD->value,
            'amount' => 100
        ]);

        $this->assertInstanceOf(Payment::class, $result);
        
        $this->assertEquals(2, Payment::where('order_id', $order->id)->count());
    }

    /** @test */
    public function it_throws_exception_when_order_not_found()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->execute(1234, [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'amount' => 100
        ]);
    }

    /** @test */
    public function it_throws_exception_when_order_not_owned_by_user()
    {
        $otherUser = User::create(['name' => 'Other User', 'email' => 'other@example.com', 'password' => bcrypt('password')]);
        $order = Order::create([
            'user_id' => $otherUser->id,
            'status' => OrderStatusEnum::CONFIRMED,
            'total_price' => 100,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->execute($order->id, [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'amount' => 100
        ]);
    }

    /** @test */
    public function it_creates_payment_with_correct_method()
    {
        // Test PayPal
        $order1 = Order::create([
            'user_id' => $this->user->id,
            'total_price' => 100,
            'status' => OrderStatusEnum::CONFIRMED,
        ]);

        $result1 = $this->service->execute($order1->id, [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'amount' => 100
        ]);

        $this->assertEquals(PaymentMethodEnum::PAYPAL, $result1->method);

        // Test Credit Card
        $order2 = Order::create([
            'user_id' => $this->user->id,
            'total_price' => 100,
            'status' => OrderStatusEnum::CONFIRMED,
        ]);

        $result2 = $this->service->execute($order2->id, [
            'method' => PaymentMethodEnum::CREDIT_CARD->value,
            'amount' => 100
        ]);

        $this->assertEquals(PaymentMethodEnum::CREDIT_CARD, $result2->method);
    }

    /** @test */
    public function it_stores_payment_metadata()
    {
        $order = $this->createOrder(OrderStatusEnum::CONFIRMED);
        $paymentData = [
            'method' => PaymentMethodEnum::PAYPAL->value,
            'amount' => 100,
            'currency' => 'USD',
        ];

        $result = $this->service->execute($order->id, $paymentData);

        $this->assertNotNull($result->metadata);
        $this->assertIsArray($result->metadata);
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
