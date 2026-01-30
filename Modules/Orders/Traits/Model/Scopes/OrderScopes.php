<?php

namespace Modules\Orders\Traits\Model\Scopes;

trait OrderScopes 
{
    public function scopeOwnedBy($query)
    {
        return $query->where('user_id', auth()->id());
    }
}