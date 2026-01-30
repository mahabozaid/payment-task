<?php

namespace Modules\Payments\tests\Unit\Services;

use Tests\TestCase;
use Modules\Orders\Enums\OrderStatusEnum;
use Modules\Orders\Models\Order;
use Modules\Payments\Gateways\PayPalGateway;
use Modules\Payments\Gateways\CreditCardGateway;
use Modules\Payments\DTO\PaymentResultDTO;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\Enums\PaymentMethodEnum;

class PayOrderService1Test extends TestCase
{
    /** @test */
    public function paypal_gateway_returns_payment_result_dto_with_valid_status()
    {
        $order = $this->createOrder();

        $gateway = new PayPalGateway();

        $dto = $gateway->pay($order, ['amount' => 50]);

        $this->assertInstanceOf(PaymentResultDTO::class, $dto);
        $this->assertStringStartsWith('pp_', $dto->payment_id);
        $this->assertContains($dto->status, [
            PaymentStatusEnum::SUCCESSFUL,
            PaymentStatusEnum::FAILED,
            PaymentStatusEnum::PENDING
        ]);
        $this->assertEquals(PaymentMethodEnum::PAYPAL, $dto->method);
        $this->assertEquals(['amount' => 50], $dto->metadata);
    }

    /** @test */
    public function credit_card_gateway_returns_payment_result_dto_with_valid_status()
    {
        $order = $this->createOrder();

        $gateway = new CreditCardGateway();

        $dto = $gateway->pay($order, ['amount' => 100]);

        $this->assertInstanceOf(PaymentResultDTO::class, $dto);
        $this->assertStringStartsWith('cc_', $dto->payment_id);
        $this->assertContains($dto->status, [
            PaymentStatusEnum::SUCCESSFUL,
            PaymentStatusEnum::FAILED,
            PaymentStatusEnum::PENDING
        ]);
        $this->assertEquals(PaymentMethodEnum::CREDIT_CARD, $dto->method);
        $this->assertEquals(['amount' => 100], $dto->metadata);
    }

    /** @test */
    public function payment_result_dto_has_correct_metadata()
    {
        $order = $this->createOrder();
        $metadata = ['order_ref' => 'ORD-123', 'amount' => 75];

        $gateway = new PayPalGateway();
        $dto = $gateway->pay($order, $metadata);

        $this->assertEquals($metadata, $dto->metadata);
    }

    /** @test */
    public function paypal_and_credit_card_payment_ids_are_unique()
    {
        $order = $this->createOrder();

        $paypalGateway = new PayPalGateway();
        $ccGateway = new CreditCardGateway();

        $dto1 = $paypalGateway->pay($order, []);
        $dto2 = $paypalGateway->pay($order, []);
        $dto3 = $ccGateway->pay($order, []);
        $dto4 = $ccGateway->pay($order, []);

        $this->assertNotEquals($dto1->payment_id, $dto2->payment_id);
        $this->assertNotEquals($dto3->payment_id, $dto4->payment_id);
    }

    /** @test */
    public function all_possible_statuses_can_be_returned()
    {
        $order = $this->createOrder();
        $gateway = new PayPalGateway();

        $statusesSeen = [];

        for ($i = 0; $i < 50; $i++) {
            $dto = $gateway->pay($order, []);
            $statusesSeen[$dto->status->value ?? $dto->status] = true;
        }

        $expectedStatuses = [
            PaymentStatusEnum::SUCCESSFUL->value,
            PaymentStatusEnum::FAILED->value,
            PaymentStatusEnum::PENDING->value,
        ];

        foreach ($expectedStatuses as $status) {
            $this->assertArrayHasKey($status, $statusesSeen);
        }
    }

    private function createOrder($overrides = [])
    {
        return new Order([
            'user_id' => 1,
            'status' => OrderStatusEnum::CONFIRMED,
            'total' => 100,
        ]); 
    }
}
