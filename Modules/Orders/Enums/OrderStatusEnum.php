<?php

namespace Modules\Orders\Enums;

enum OrderStatusEnum :int
{
    case PENDING   = 1;
    case CONFIRMED = 2;
    case CANCELLED = 3;
}