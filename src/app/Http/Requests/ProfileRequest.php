<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name'        => ['required', 'string', 'max:50'],
            'post_code' => ['required', 'string', 'max:8'],
            'address'    => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
{
    return [
        'name.required' => 'ユーザー名は必須です。',
        'post_code.required' => '郵便番号は必須です。',
        'address.required' => '住所は必須です。',
    ];
}
}
