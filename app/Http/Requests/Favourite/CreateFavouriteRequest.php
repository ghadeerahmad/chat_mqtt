<?php

namespace App\Http\Requests\Favourite;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFavouriteRequest extends FormRequest
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
            'room_id' => ['required', 'exists:rooms,id'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = error_response($validator->errors()->first());
        throw new HttpResponseException($response);
    }
}
