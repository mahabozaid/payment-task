<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Orders\Enums\OrderStatusEnum;
use Illuminate\Validation\Rules\Enum;

class ListOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
              'status'   => 'sometimes|nullable|array',
              'status.*' => [new Enum(OrderStatusEnum::class)],
        ];
    }
}
