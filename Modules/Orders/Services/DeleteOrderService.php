<?php

namespace Modules\Orders\Services;

use Modules\Orders\Models\Order;
use App\Exceptions\LogicalException;
use Modules\Payments\Enums\PaymentStatusEnum;

readonly class DeleteOrderService 
{
    public function execute(int $id)
    {
         $order = Order::OwnedBy()->findOrFail($id);
         if($order->payments()->WithStatuses([PaymentStatusEnum::SUCCESSFUL->value,PaymentStatusEnum::PENDING->value])->exists()){
            throw new LogicalException('This Order has Payment already completed or in progress.');
         }

       return $order->delete();
    }

}