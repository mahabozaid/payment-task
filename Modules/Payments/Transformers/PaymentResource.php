<?php

namespace Modules\Payments\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'order_id'   => $this->order_id,
            'payment_id' => $this->payment_id,
            'status'     => $this->status->name,
            'amount'     => $this->order->total_price,
            'method'     => $this->method->name,
            'created_at' => $this->created_at,
        ];
    }
}
