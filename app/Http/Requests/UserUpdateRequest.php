<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'image' => 'image',
            'name' => 'max:50',
            'email' => 'email|max:255',
            'phone_number' => 'max:50',
            'password' => 'max:50',
            'address' => 'max:500',
            'google_place_id' => 'max:255',
        ];
    }
}
