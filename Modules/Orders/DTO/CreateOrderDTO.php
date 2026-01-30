<?php

namespace Modules\Orders\DTO;

final readonly class CreateOrderDTO
{
    public function __construct(
        public int $userId,
        public array $items
    ) {}

    public static function fromRequest(array $data): self
    {
        $userId = auth()->id();
        return new self(
            $userId,
            $data['items']
        );
    }
}
