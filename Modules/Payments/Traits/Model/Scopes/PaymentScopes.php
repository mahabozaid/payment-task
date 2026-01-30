<?php

namespace Modules\Payments\Traits\Model\Scopes;

trait PaymentScopes 
{
    public function scopeWithStatuses($query, array $statuses)
    {
        return $query->whereIn('status', $statuses);
    }
}