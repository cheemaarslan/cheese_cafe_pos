<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => [
                'required',
                'string',
                'max:190',
                Rule::unique('blogs', 'title')->ignore($this->route('blog.id'))
            ],
            'description' => ['required', 'string'],
            'content'     => ['nullable', 'string'],
            'author'      => ['nullable', 'string', 'max:190'],
            'category'    => ['nullable', 'string', 'max:190'],
            'status'      => ['required', 'numeric', 'max:24'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']
        ];
    }
}
