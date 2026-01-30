<?php

namespace Modules\Payments\Enums;
enum PaymentMethodEnum :int
{
    case CREDIT_CARD = 1;
    case PAYPAL = 2;
}