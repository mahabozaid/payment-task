<?php

namespace Modules\Orders\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->name,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'items_count' => $this->items_count ?? $this->items->count(),
            'items' => $this->whenLoaded('items', fn () => OrderItemResource::collection($this->items)),
        ];
    }
}
