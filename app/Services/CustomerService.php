<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerService
{
    /**
     * Retrieve all customers with optional filtering by their orders status and date range.
     * 
     * @param mixed $status
     * @param mixed $startDate
     * @param mixed $endDate
     * @param mixed $perPage
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllCustomers($status = null, $startDate = null, $endDate = null, $perPage = 5)
    {
        try {

            $query = Customer::query();

            if ($status) {
                $query->status($status);
            }

            if ($startDate && $endDate) {
                $query->dateRange($startDate, $endDate);
            }

            $customers = $query->paginate($perPage);
            return $customers;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve customers: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new customer with the provided data.
     * 
     * @param array $data
     * @throws \Exception
     * @return Customer|\Illuminate\Database\Eloquent\Model
     */
    public function createCustomer(array $data)
    {
        try {
            return Customer::create($data);
        } catch (\Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve a single customer.
     * 
     * @param string $id
     * @throws \Exception
     * @return Customer|Customer[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function showCustomer(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->load('orders');
            return $customer;
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve customer: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing customers with the provided data.
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Customer|Customer[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function updateCustomer(string $id, array $data)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->update(array_filter($data));

            return $customer;
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update customer: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a customer.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteCustomer(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            return $customer->delete();
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to delete customer: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }
}
