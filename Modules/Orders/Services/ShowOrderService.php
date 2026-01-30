<?php

namespace Modules\Orders\Services;

use Modules\Orders\Repository\OrderRepository;

readonly class ShowOrderService 
{
    public function execute(int $id)
    {
        return app(OrderRepository::class)->getOrderDetails($id);
    }

}