<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Controllers\BaseController;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;

class CustomerController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customers = Customer::with('documents')->get();
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers  retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(CustomerRequest $request) : JsonResponse
        {
            try{
                $inputs = $request->all();
                $inputs['created_by'] =  auth()->user()->id;                              
                $customer = Customer::create($inputs);
                return $this->sendResponse(new CustomerResource($customer), 'Customer created successfully.');
            }catch(Exception $e){
                return $this->sendError($e->getMessage());
            }
        }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse 
    {
        $customer = Customer::with('documents')->find($customer->id);

        if(is_null($customer)){
            return $this->sendError('Customer not found.');
        }

        return $this->sendResponse(new CustomerResource($customer), 'Customer retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {        
        try{
            $inputs = $request->all();
            $inputs['updated_by'] =  auth()->user()->id; 
            $customer->update($inputs);
            return $this->sendResponse(new CustomerResource($customer), 'Customer updated successfully.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return $this->sendResponse([], 'Customer deleted successfully.');
    }
}
