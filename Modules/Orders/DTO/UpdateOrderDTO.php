<?php

namespace Modules\Orders\DTO;

use Modules\Orders\Models\Order;

final readonly class UpdateOrderDTO
{
    public function __construct(
        public Order $order,
        public array $items,
        public ?int $status = null

    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            order: $data['order'],
            items: $data['items'],
            status: data_get($data, 'status')
        );
    }
}
