<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{
    /**
     * Retrieve all orders with optional filtering by product name.
     * 
     * @param mixed $productName
     * @param mixed $perPage
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllOrders($productName = null, $perPage = 5)
    {
        try {
            $query = Order::query();

            if ($productName) {
                $query->product($productName);
            }

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve orders: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new Order with the provided data.
     * 
     * @param array $data
     * @throws \Exception
     * @return Order|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder(array $data)
    {
        try {
            return Order::create($data);
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve a single Order.
     * 
     * @param string $id
     * @throws \Exception
     * @return Order
     */
    public function showOrder(string $id)
    {
        try {
            return Order::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Order: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing Order with the provided data.
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Order
     */
    public function updateOrder(string $id, array $data)
    {
        try {
            $Order = Order::findOrFail($id);
            $Order->update(array_filter($data));

            return $Order;
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update Order: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a Order.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteOrder(string $id)
    {
        try {
            $Order = Order::findOrFail($id);

            return $Order->delete();
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to delete Order: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Place a new order for a customer.
     *
     * @param int $customerId
     * @param array $orderData
     * @return Order
     * @throws \Exception
     */
    public function placeOrder($customerId, array $orderData)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $order = $customer->orders()->create($orderData);
            return $order;
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to place order: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }
}
