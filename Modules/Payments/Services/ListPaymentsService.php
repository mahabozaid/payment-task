<?php

namespace Modules\Payments\Services;

use Modules\Payments\Repository\PaymentRepository;

class ListPaymentsService
{
    public function execute()
    {        
       return app(PaymentRepository::class)->getUserPayments();
    }
}
