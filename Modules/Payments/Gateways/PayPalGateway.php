<?php

namespace Modules\Payments\Gateways;

use Modules\Orders\Models\Order;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\DTO\PaymentResultDTO;

class PayPalGateway implements PaymentGatewayInterface
{
    public function pay(Order $order, array $data): PaymentResultDTO
    {
       $statuses = [
            PaymentStatusEnum::SUCCESSFUL,
            PaymentStatusEnum::FAILED,
            PaymentStatusEnum::PENDING
        ];

       $status = $statuses[array_rand($statuses)];
       return new PaymentResultDTO(
            payment_id: 'pp_' . uniqid(),
            status: $status,
            method: PaymentMethodEnum::PAYPAL,
            metadata: $data
        );
    }
}
