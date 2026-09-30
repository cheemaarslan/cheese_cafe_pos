<?php

namespace App\Http\Requests;

use App\Enums\Activity;
use App\Enums\OrderType;
use App\Rules\ValidJsonOrder;
use Illuminate\Validation\Rule;
use Smartisan\Settings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'branch_id'        => request('order_type') == OrderType::DELIVERY ? ['nullable', 'numeric'] : ['required', 'numeric'],
            'subtotal'         => ['required', 'numeric'],
            'discount'         => ['nullable', 'numeric'],
            'delivery_charge'  => request('order_type') === OrderType::DELIVERY ? [
                'required',
                'numeric'
            ] : ['nullable'],
            'total'            => ['required', 'numeric'],
            'order_type'       => ['required', 'numeric'],
            'is_advance_order' => ['required', 'numeric'],
            'address_id'       => request('order_type') === OrderType::DELIVERY ? [
                'required',
                'numeric'
            ] : ['nullable'],
            'delivery_time'    => request('order_type') === OrderType::DELIVERY ? [
                'required',
                'string'
            ] : ['nullable'],
            'payment_method'   => ['required', 'numeric', Rule::exists('payment_gateways', 'id')->where(function ($query) {
                $query->where('status', 5);
            })],
            'coupon_id'        => ['nullable', 'numeric'],
            'instruction'      => ['nullable', 'string', 'max:190'],
            'nearest_landmark' => ['nullable', 'string', 'max:190'],
            'source'           => ['required', 'numeric'],
            'token'            => ['nullable', 'numeric'],
            'items'            => ['required', 'json', new ValidJsonOrder],
            'loyalty_point'    => ['nullable', 'array'],
            'loyalty_point.status' => ['nullable', 'numeric'],
            'loyalty_point.percentage' => ['nullable', 'numeric'],
            'loyalty_point.min_order_price' => ['nullable', 'numeric'],
            'loyalty_point.order_price' => ['nullable', 'numeric'],
            'loyalty_point.points' => ['nullable', 'numeric'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (request('order_type') == OrderType::DELIVERY && Settings::group('order_setup')->get("order_setup_delivery") == Activity::DISABLE) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            } else if (request('order_type') == OrderType::TAKEAWAY && Settings::group('order_setup')->get("order_setup_takeaway") == Activity::DISABLE) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            } else if (blank(request('order_type'))) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            }
        });
    }
}