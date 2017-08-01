<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishUpdateRequest extends FormRequest
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
            'category_id' => 'exists:categories,id',
            'topic' => 'max:255',
            'description' => 'max:1000',
            'pickup_time' => 'date_format:"m/d/Y H:i"|greater_than_date:end_date',
            'pickup_method' => 'in:DELIVER,PICKUP',
            'address' => 'max:500',
            'google_place_id' => 'max:255',
            'quantity' => 'integer|between:1,500',
            'end_date' => 'date_format:"m/d/Y H:i"|after:today',
            'price_from' => 'numeric|between:0.01,500',
            'price_to' => 'numeric|between:0.01,500|greater_than_field:price_from',
        ];
    }
}
