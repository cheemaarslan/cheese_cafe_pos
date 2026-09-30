<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltyPointRequest extends FormRequest
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
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_order_price' => ['required', 'numeric', 'min:0'],
            'status'     => ['required', 'numeric', 'max:24'],
        ];
    }
}
