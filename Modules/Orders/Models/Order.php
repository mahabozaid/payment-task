<?php

namespace Modules\Orders\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Orders\database\factories\OrderFactory;
use Modules\Orders\Enums\OrderStatusEnum;
use Modules\Orders\Traits\Model\Scopes\OrderScopes;


class Order extends BaseModel
{
    use OrderScopes, HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['status' => OrderStatusEnum::class];

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    public function items():HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments():HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
