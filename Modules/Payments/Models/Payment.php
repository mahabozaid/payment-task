<?php

namespace Modules\Payments\Models;

use App\Models\BaseModel;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\Traits\Model\Scopes\PaymentScopes;
use Modules\Orders\Models\Order;

class Payment extends BaseModel
{
    use PaymentScopes;
    protected $guarded = ['id'];
    protected $casts = [
        'metadata' => 'array',
        'method' => PaymentMethodEnum::class,
        'status' => PaymentStatusEnum::class
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
