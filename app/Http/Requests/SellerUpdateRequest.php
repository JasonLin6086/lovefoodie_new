<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerUpdateRequest extends FormRequest
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
            'category_id' => 'comma_array_distinct|comma_array_in_table:categories,id',
            'icon' => 'image',
            'kitchen_name' => 'max:255',
            'phone_number' => 'max:50',
            'phone_verify_code' => 'max:4',
            'email' => 'email|max:255',
            'address' => 'max:500',
            'google_place_id' => 'max:255',
            'isactive' => 'in:0,1',
        ];
    }
    
    public function messages()
    {
        return [
            'category_id.comma_array_distinct' => trans('validation.distinct', ['attribute' => 'category_id']),
        ];
    }
    
}
