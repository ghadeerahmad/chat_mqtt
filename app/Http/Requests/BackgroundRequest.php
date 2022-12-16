<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BackgroundRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'file' => ['nullable', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'title' => ['nullable', 'string', 'max:50'],
            'price' => ['nullable', 'numeric'],
            'is_default' => ['nullable', 'in:1,0'],
            'background_id' => ['nullable', 'exists:backgrounds,id'],
        ];
    }
}
