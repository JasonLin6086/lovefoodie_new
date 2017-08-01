<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupLocationCreateRequest extends FormRequest
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
            'description' => 'string|nullable|max:100',
            'address' => 'required|max:500',
            'google_place_id' => 'required|max:255',
            'order' => 'numeric|between:0,1000'
        ];
    }
}
