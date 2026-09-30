<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * The symbol column is not nullable, so a blank symbol is stored as an
     * empty string. This lets currencies such as PKR render amounts bare.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        $this->merge(['symbol' => (string) $this->input('symbol')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name'              => [
                'required',
                'string',
                'max:190',
                Rule::unique("currencies", "name")->ignore($this->route('currency.id'))
            ],
            'symbol'            => ['nullable', 'string', 'max:190'],
            'code'              => ['required', 'string', 'max:20'],
            'is_cryptocurrency' => ['required', 'numeric', 'max:15'],
            'exchange_rate'     => ['nullable', 'numeric', 'min:0', 'max:9999999999999'],
        ];
    }
}
