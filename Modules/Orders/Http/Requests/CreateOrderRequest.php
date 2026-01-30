<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [
            'items'=>'required|array',
            'items.*.id'=>'required',
            'items.*.quantity'=>'required|integer|min:1',
            'items.*.price'=>'required|numeric|min:0'
        ];
    }
}
