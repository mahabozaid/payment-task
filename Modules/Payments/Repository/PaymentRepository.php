<?php

namespace Modules\Payments\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Orders\Models\Order;
use Modules\Payments\Models\Payment;

readonly class PaymentRepository 
{
   public function __construct(private Payment $model) {}
   public function getOrderPayments(Order $order):LengthAwarePaginator
   {
       return $order->payments()
                ->select('id', 'order_id', 'payment_id', 'status', 'method','created_at')
                ->paginate();
   }

   public function getUserPayments():LengthAwarePaginator
   {
      return $this->model->whereIn('order_id', function ($q) {
                     $q->select('id')->from('orders')->where('user_id', auth()->id());
                  })
                  ->select('id', 'order_id', 'payment_id', 'status', 'method','created_at')
                  ->paginate();
   }
}