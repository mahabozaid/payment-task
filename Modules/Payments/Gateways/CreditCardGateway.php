<?php

namespace Modules\Payments\Gateways;

use Modules\Orders\Models\Order;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\DTO\PaymentResultDTO;

class CreditCardGateway implements PaymentGatewayInterface
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
            payment_id: 'cc_' . uniqid(),
            status: $status,
            method: PaymentMethodEnum::CREDIT_CARD,
            metadata: $data
        );
    }
}
