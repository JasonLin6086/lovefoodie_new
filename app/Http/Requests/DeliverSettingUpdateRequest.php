<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliverSettingUpdateRequest extends FormRequest
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
            'is_free_delivery' => 'in:0,1',
            'free_delivery_price' => 'numeric|between:0,2000',
            'free_delivery_mile' => 'numeric|between:0,10000',
            'is_delivery_fee' => 'in:0,1',
            'store_open_hour' => 'required_if:is_at_store,1|json',
            'is_at_store' => 'in:0,1',
            'order_before_hour' => 'numeric|between:0,500',
            'miles_within' => 'required_if:is_delivery_fee,1|comma_array_numeric|comma_array_between:0,10000', 
            'price' => 'required_if:is_delivery_fee,1|comma_array_numeric|comma_array_between:0,2000', 
        ];
    }
    
    public function messages()
    {
        return [
            'miles_within.comma_array_numeric' => trans('validation.numeric', ['attribute' => 'miles within']),
            'miles_within.comma_array_between' => trans('validation.between.numeric', ['attribute' => 'miles within', 'min' => 0, 'max' => 10000]),
            'price.comma_array_numeric' => trans('validation.numeric', ['attribute' => 'price']),
            'price.comma_array_between' => trans('validation.between.numeric', ['attribute' => 'price', 'min' => 0, 'max' => 2000]),
        ];
    }
}
