<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Payments\Services\GetOrderPaymentsService;
use App\Utils\ApiResponse;
use Modules\Payments\Http\Controllers\Requests\PayOrderRequest;
use Modules\Payments\Services\PayOrderService;
use Modules\Payments\Transformers\PaymentResource;
use Illuminate\Http\JsonResponse;
use Modules\Payments\Services\ListPaymentsService;

class PaymentsController extends Controller
{

    public function index()
    {
       $userPayments = app(ListPaymentsService::class)->execute();

       return ApiResponse::success('User payments retrieved successfully', PaymentResource::collection($userPayments));
    }

    public function pay(PayOrderRequest $request, int $id): JsonResponse
    {
        $payment = app(PayOrderService::class)->execute($id, $request->validated());

        return ApiResponse::success('Payment processed successfully', new PaymentResource($payment));
    }

    public function getOrderPayments(int $id):JsonResponse
    { 
       $orderPayments = app(GetOrderPaymentsService::class)->execute($id);

       return ApiResponse::success('Order payments retrieved successfully', PaymentResource::collection($orderPayments));
    }
}
