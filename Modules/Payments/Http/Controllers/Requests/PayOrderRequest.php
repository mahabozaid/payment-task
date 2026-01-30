<?php

namespace Modules\Payments\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Payments\Enums\PaymentMethodEnum;
use Illuminate\Validation\Rules\Enum;

class PayOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
            'method' => ['required', new Enum(PaymentMethodEnum::class)],
            'data' => 'required|array',
        ];
    }
}
