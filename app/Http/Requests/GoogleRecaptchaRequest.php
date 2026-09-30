<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleRecaptchaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_google_recaptcha_enable' => ['nullable'],
            'site_key'                   => ['nullable', 'string'],
            'private_key'                => ['nullable', 'string'],
            'version'                    => ['nullable', 'string'],
        ];
    }
}
