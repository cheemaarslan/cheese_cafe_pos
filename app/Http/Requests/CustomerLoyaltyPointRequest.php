<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoyaltyPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order_id'       => ['nullable', 'exists:orders,id'],
            'order_subtotal' => ['required', 'numeric', 'min:0'],
            'percentage'     => ['required', 'numeric', 'min:0', 'max:100'],
            'points'         => ['required', 'integer', 'min:0'],
            'status'         => ['required', 'integer', 'in:0,1'],
            'earned_at'      => ['nullable', 'date'],
            'expired_at'     => ['nullable', 'date', 'after:earned_at'],
        ];
    }
}