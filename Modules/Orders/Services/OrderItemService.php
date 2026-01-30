<?php

namespace Modules\Orders\Services;

use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;

readonly class OrderItemService
{
    public function syncItems(Order $order, array $items): void
    {
        $productIds = collect($items)->pluck('id')->all();

        $order->items()
            ->when($order->exists, fn ($q) =>
                $q->whereNotIn('product_id', $productIds)
            )
            ->delete();

        $rows = collect($items)->map(fn ($item) => [
            'order_id'   => $order->id,
            'product_id' => $item['id'],
            'quantity'   => $item['quantity'],
            'price'      => $item['price'],
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        OrderItem::upsert(
            $rows,
            ['order_id', 'product_id'],
            ['quantity', 'price', 'updated_at']
        );
    }

    public function calculateTotal(Order $order): float
    {
        return $order->items()
            ->selectRaw('SUM(quantity * price) as total')
            ->value('total') ?? 0;
    }
}

