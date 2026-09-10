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
            'fullName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(03|05|07|08|09)\d{8}$/'],
            'district' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'driverNote' => ['nullable', 'string', 'max:500'],
            'paymentMethod' => ['required', 'string', 'in:cod,momo,vnpay,zalopay,bank_transfer'],
            'couponCode' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['nullable', 'string', 'in:product,sauce,combo'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.sauce_id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string'],
            'items.*.price' => ['nullable', 'numeric'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.sauce' => ['nullable', 'string'],
            'items.*.spiceLevel' => ['nullable', 'string'],
            'items.*.toppings' => ['nullable', 'array'],
            'items.*.note' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fullName.required' => 'Vui lòng nhập họ và tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'phone.regex' => 'Số điện thoại không hợp lệ (yêu cầu đúng 10 số, bắt đầu bằng 03, 05, 07, 08, 09).',
            'district.required' => 'Vui lòng chọn quận/huyện giao hàng.',
            'address.required' => 'Vui lòng nhập địa chỉ giao hàng chi tiết.',
            'address.min' => 'Địa chỉ giao hàng quá ngắn, vui lòng nhập đầy đủ số nhà, ngõ ngách, tên đường (tối thiểu 10 ký tự).',
            'paymentMethod.required' => 'Vui lòng chọn phương thức thanh toán.',
            'items.required' => 'Giỏ hàng của bạn đang trống.',
            'items.min' => 'Vui lòng chọn ít nhất 1 món ăn trước khi thanh toán.',
        ];
    }
}
