<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Orders\Services\DeleteOrderService;
use Modules\Orders\Transformers\OrderResource;
use Modules\Orders\Services\ListOrderService;
use Modules\Orders\Http\Requests\ListOrderRequest;
use Modules\Orders\Services\UpdateOrderService;
use Modules\Orders\DTO\UpdateOrderDTO;
use Modules\Orders\Http\Requests\UpdateOrderRequest;
use App\Utils\ApiResponse;
use Modules\Orders\Services\CreateOrderService;
use Modules\Orders\DTO\CreateOrderDTO;
use Modules\Orders\Http\Requests\CreateOrderRequest;
use Modules\Orders\Models\Order;
use Modules\Orders\Services\ShowOrderService;

class OrdersController extends Controller
{
    public function index(ListOrderRequest $request)
    {
       $orders = app(ListOrderService::class)->execute($request->validated());
       $collection = OrderResource::collection($orders);

       return ApiResponse::success('Orders retrieved successfully', $collection);
    }

    public function store(CreateOrderRequest $request)
    {
        $dto = CreateOrderDTO::fromRequest($request->validated());
        $order = app(CreateOrderService::class)->execute($dto);
        $resource = new OrderResource($order);
        
        return ApiResponse::success('Order created successfully', $resource, code: 201, httpStatus: 201);
    }

    public function update(UpdateOrderRequest $request, int $id) 
    {
        $data = $request->validated();
        $order = Order::OwnedBy()->findOrFail($id);
        $data['order'] = $order;
        $dto = UpdateOrderDTO::fromRequest($data);
        $order = app(UpdateOrderService::class)->execute($dto);
        $resource = new OrderResource($order);


        return ApiResponse::success('Order updated successfully', $resource);
    }

    public function show(int $id) 
    {
       $order= app(ShowOrderService::class)->execute($id);
       $resource = new OrderResource($order);

        return ApiResponse::success('Order retrieved successfully', $resource);
    }

    public function destroy($id) 
    {
        app(DeleteOrderService::class)->execute($id);

        return ApiResponse::success('Order deleted successfully');
    }
}
