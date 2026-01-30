<?php

namespace Modules\Orders\Transformers;

use Illuminate\Http\Request;
use Modules\Products\Transformers\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'product' => new ProductResource($this->product)
        ];
    }
}
