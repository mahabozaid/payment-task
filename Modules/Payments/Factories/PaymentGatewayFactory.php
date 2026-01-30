<?php

namespace Modules\Payments\Factories;

use Modules\Payments\Gateways\CreditCardGateway;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Payments\Gateways\PayPalGateway;
use Modules\Payments\Gateways\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $method): PaymentGatewayInterface
    {
        return match((int)$method) {
            PaymentMethodEnum::CREDIT_CARD->value => new CreditCardGateway(),
            PaymentMethodEnum::PAYPAL->value => new PayPalGateway(),
            default => throw new InvalidArgumentException("Payment method {$method} not supported")
        };
    }
}
