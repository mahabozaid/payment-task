<?php

namespace Modules\Payments\Gateways;

use Modules\Orders\Models\Order;
use Modules\Payments\DTO\PaymentResultDTO;

interface PaymentGatewayInterface
{
   public function pay(Order $order, array $data): PaymentResultDTO;
}
