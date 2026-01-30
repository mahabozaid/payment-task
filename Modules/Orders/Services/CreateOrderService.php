<?php


namespace Modules\Orders\Services;

use Modules\Orders\DTO\CreateOrderDTO;
use Modules\Orders\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Models\Order;

readonly class CreateOrderService 
{
    public function __construct(private OrderItemService $orderItemService) 
    {
    }

    public function execute(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {

            $order = Order::create([
                'user_id' => $dto->userId,
                'total_price' => 0,
                'status'  => OrderStatusEnum::PENDING,
            ]);

            $this->orderItemService->syncItems($order, $dto->items);

            $totalPrice = $this->orderItemService->calculateTotal($order);

            $order->update([
                'total_price' => $totalPrice,
            ]);

            return $order->refresh();
        });
    }

}