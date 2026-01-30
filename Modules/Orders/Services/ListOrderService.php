<?php

namespace Modules\Orders\Services;

use Modules\Orders\Repository\OrderRepository;

readonly class ListOrderService 
{
    public function execute(array $filters = [])
    {
        return app(OrderRepository::class)->listOrders($filters);
    }

}