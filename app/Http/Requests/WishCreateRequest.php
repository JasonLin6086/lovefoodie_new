<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishCreateRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'topic' => 'required|max:255',
            'description' => 'required|max:1000',
            'pickup_time' => 'required|date_format:"m/d/Y H:i"|greater_than_date:end_date',
            'pickup_method' => 'required|in:DELIVER,PICKUP',
            'address' => 'required|max:500',
            'google_place_id' => 'required|max:255',
            'quantity' => 'required|integer|between:1,500',
            'end_date' => 'required|date_format:"m/d/Y H:i"|after:today',
            'price_from' => 'required|numeric|between:0.01,500',
            'price_to' => 'required|numeric|between:0.01,500|greater_than_field:price_from',
        ];
    }
}
