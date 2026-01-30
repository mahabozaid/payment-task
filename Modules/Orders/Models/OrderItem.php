<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Products\Models\Product;

class OrderItem extends Model
{
    protected $guarded = ['id'];

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
