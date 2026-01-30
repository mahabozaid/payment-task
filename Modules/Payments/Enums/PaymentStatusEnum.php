<?php

namespace Modules\Payments\Enums;
enum PaymentStatusEnum :int
{
    case PENDING    = 1;
    case SUCCESSFUL = 2;
    case FAILED     = 3;
}