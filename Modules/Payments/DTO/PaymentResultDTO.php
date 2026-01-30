<?php

namespace Modules\Payments\DTO;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\Enums\PaymentMethodEnum;


readonly class PaymentResultDTO
{
    public function __construct(
        public string $payment_id,
        public PaymentStatusEnum $status,       
        public PaymentMethodEnum $method,       
        public array $metadata = []
    ) {}
}
