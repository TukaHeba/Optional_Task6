<?php

namespace App\Http\Requests\Order;

use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * This method is called before validation starts to clean or normalize inputs.
     * 
     * Capitalize the first letter and trim white spaces if provided
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'product_name' => $this->product_name ? ucwords(trim($this->product_name)) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255|min:2',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
            'status' => 'required|in:pending,completed',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Define human-readable attribute names for validation errors.
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_name' => 'product name',
            'quantity' => 'product quantity',
            'price' => 'product price',
            'status' => 'order status',
            'customer_id' => 'customer ID',
        ];
    }

    /**
     * Define custom error messages for validation failures.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'integer' => 'The :attribute must be an integer.',
            'numeric' => 'The :attribute must be a valid number.',
            'string' => 'The :attribute must be a valid string.',
            'in' => 'The selected :attribute is invalid. Allowed values are: pending, completed.',
            'exists' => 'The selected :attribute is invalid.',
            'min.string' => 'The :attribute must be at least :min characters.', 
            'min.numeric' => 'The :attribute must be at least :min.', 
        ];
    }

    /**
     * Handle validation errors and throw an exception.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator The validation instance.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(
            ApiResponseService::error($errors, 'A server error has occurred', 403)
        );
    }
}
