<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\ApiResponseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productName = $request->query('product_name');
        $perPage = $request->query('perPage', 5);

        try {
            $orders = $this->orderService->listAllOrders($productName, $perPage);
            return ApiResponseService::success(OrderResource::collection($orders), 'Orders retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        try {
            $newOrder = $this->orderService->createOrder($validated);
            return ApiResponseService::success(new OrderResource($newOrder), 'Order created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Order = $this->orderService->showOrder($id);
            return ApiResponseService::success(new OrderResource($Order), 'Order retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedOrder = $this->orderService->updateOrder($id, $validated);
            return ApiResponseService::success(new OrderResource($updatedOrder), 'Order updated successfully', 200);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->orderService->deleteOrder($id);
            return ApiResponseService::success(null, 'Order deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Make an order for a customer.
     */
    public function makeOrder(StoreOrderRequest $request, string $customerId)
    {
        $validated = $request->validated();

        try {
            $order = $this->orderService->placeOrder($customerId, $validated);
            return ApiResponseService::success($order, 'Order placed successfully', 201);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
