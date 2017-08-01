<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerCreateRequest extends FormRequest
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
            'category_id' => 'required|comma_array_distinct|comma_array_in_table:categories,id',
            'icon' => 'required|image',
            'kitchen_name' => 'required|max:255',
            'phone_number' => 'required|max:50',
            'phone_verify_code' => 'required|max:4',
            'email' => 'required|email|max:255',
            'address' => 'required|max:500',
            'google_place_id' => 'required|max:255',
        ];
    }
    
    public function messages()
    {
        return [
            'category_id.comma_array_distinct' => trans('validation.distinct', ['attribute' => 'category_id']),
        ];
    }
    
}
