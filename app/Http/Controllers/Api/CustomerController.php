<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $perPage = $request->query('perPage', 5);

        try {
            $customers = $this->customerService->listAllCustomers($status, $startDate, $endDate, $perPage);
            return ApiResponseService::success(CustomerResource::collection($customers), 'Customers retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        try {
            $newCustomer = $this->customerService->createCustomer($validated);
            return ApiResponseService::success(new CustomerResource($newCustomer), 'Customer created successfully', 201);
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
            $customer = $this->customerService->showCustomer($id);
            return ApiResponseService::success(new CustomerResource($customer), 'Customer retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedCustomer = $this->customerService->updateCustomer($id, $validated);
            return ApiResponseService::success(new CustomerResource($updatedCustomer), 'Customer updated successfully', 200);
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
            $this->customerService->deleteCustomer($id);
            return ApiResponseService::success(null, 'Customer deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
