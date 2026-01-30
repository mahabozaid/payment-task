<?php

namespace Modules\Payments\Services;

use Modules\Payments\Repository\PaymentRepository;
use Modules\Orders\Repository\OrderRepository;

class GetOrderPaymentsService
{
    public function execute(int $id)
    {
        $order = App(OrderRepository::class)->getOrder($id);   
        
        return app(PaymentRepository::class)->getOrderPayments($order);
    }
}
