<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Orders\Enums\OrderStatusEnum;
use Illuminate\Validation\Rules\Enum;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
            'status' => ['sometimes','nullable', new Enum(OrderStatusEnum::class)],
            'items'=>'required|array',
            'items.*.id'=>'required',
            'items.*.quantity'=>'required|integer|min:1',
            'items.*.price'=>'required|numeric|min:0'
        ];
    }
}
