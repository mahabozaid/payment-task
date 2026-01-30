<?php

namespace Modules\Payments\Services;

use App\Exceptions\LogicalException;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Orders\Models\Order;
use Modules\Orders\Enums\OrderStatusEnum;
use Modules\Payments\Factories\PaymentGatewayFactory;
use Modules\Payments\Models\Payment;
use Illuminate\Support\Facades\DB;

class PayOrderService
{
    public function execute(int $id, array $data): Payment
    {
        $order = Order::OwnedBy()->findOrFail($id);

       $this->validatePay($order, $data);

        $gateway = PaymentGatewayFactory::make($data['method']);

        $paymentResultDTO = $gateway->pay($order, $data);

        return DB::transaction(function () use ($order, $paymentResultDTO) {
            return Payment::create([
                'order_id' => $order->id,
                'payment_id' => $paymentResultDTO->payment_id,
                'status' => $paymentResultDTO->status,
                'method' => $paymentResultDTO->method,
                'metadata' => $paymentResultDTO->metadata,
            ]);
        });
    }

    private function validatePay(Order $order, array $data)
    {
        if($order->payments()->WithStatuses([PaymentStatusEnum::SUCCESSFUL->value,PaymentStatusEnum::PENDING->value])->exists()){
            throw new LogicalException('This Order has Payment already completed or in progress.');
        }

        if ($order->status !== OrderStatusEnum::CONFIRMED){
                    throw new LogicalException('Payments can only be processed for confirmed orders.');
        }
    }


}
