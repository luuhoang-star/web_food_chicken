<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'district' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'driverNote' => 'nullable|string|max:500',
            'paymentMethod' => 'required|string|in:cod,momo,vnpay,zalopay,bank_transfer',
            'couponCode' => 'nullable|string|max:50',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'nullable|string|in:product,sauce,combo',
            'items.*.product_id' => 'nullable|integer',
            'items.*.sauce_id' => 'nullable|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sauce' => 'nullable|string',
            'items.*.spiceLevel' => 'nullable|string',
            'items.*.toppings' => 'nullable|array',
            'items.*.note' => 'nullable|string',
        ];
    }
}
