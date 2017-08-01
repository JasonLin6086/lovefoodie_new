<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;

class ShoppingCartExtraCreateRequest extends FormRequest
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
            'seller_id' => $this->getShoppingCartSellerRule(),
            'pickup_type' => 'required|in:DELIVER,GROUP_PICKUP,STORE_PICKUP',
            'pickup_time' => 'required|date_format:"m/d/Y H:i"|after_now',
            'pickup_location_mapping_id' => 'required_if:pickup_type,GROUP_PICKUP|loc_mapping_id',
            'description' => 'string|nullable|max:2000'
        ];
    }
    
    private function getShoppingCartSellerRule(){
        return [ 
                'required',
                Rule::exists('shopping_carts', 'seller_id')->where(function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }),
                ];
    } 
}
