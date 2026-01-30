<?php

namespace Modules\Orders\Repository;

use Modules\Orders\Models\Order;

readonly class OrderRepository 
{
   public function __construct(private Order $model) {}

   public function getOrder(int $id)
   {
     return $this->model->OwnedBy()->findOrFail($id);
   }

   public function listOrders(array $data)
   {
      return $this->model->newQuery()->OwnedBy()
                ->select('id', 'total_price', 'status', 'created_at')
                ->withCount('items')
                ->when($data['status'] ?? false, fn ($q, $status) => $q->whereIn('status', $status))
                ->paginate();
   }

   public function getOrderDetails(int $id)
   {
       return $this->model->OwnedBy()
                ->with(['items' => function ($q) {  
                    $q->select('id', 'order_id', 'product_id', 'quantity', 'price');
                },
                'items.product' => function ($q) {
                    $q->select('id', 'name', 'price');
                }
                ])
                ->select('id', 'total_price', 'status', 'created_at','user_id')
                ->findOrFail($id);
   }

   public function getOrderPayments(int $id)
   {
       return $this->model->OwnedBy()
            ->where('id', $id)
            ->payments()
             ->select('id', 'order_id', 'payment_id', 'status', 'method')
             ->findOrFail();
   }
        
    
}