<?php

namespace Modules\Orders\Services;

use Modules\Orders\DTO\UpdateOrderDTO;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Models\Order;

readonly class UpdateOrderService
{
    public function __construct(
        private OrderItemService $orderItemService
    ) {}

    public function execute(UpdateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {

            $this->orderItemService->syncItems($dto->order, $dto->items);

            $totalPrice = $this->orderItemService->calculateTotal($dto->order);
            $status = $dto->status ?? $dto->order->status;

            $dto->order->update([
                'total_price' => $totalPrice,
                'status' => $status
            ]);

            return $dto->order->refresh();
        });
    }
}

