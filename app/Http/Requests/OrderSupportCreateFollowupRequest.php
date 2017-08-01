<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use App\Solution;
use App\Order;

class OrderSupportCreateFollowupRequest extends FormRequest
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
        $solution = Solution::find(Request::get('solution_id'));
        $refundRule = 'numeric|is_valid_refund';
        $signatureRule = 'image|is_valid_signature';
        
        // Check the required column accord to the solution selected
        if($solution){
            if($solution->require_refund){
                $refundRule = 'required|numeric|is_valid_refund';
            }
            
            if($solution->require_signature){
                $signatureRule = 'required|image|is_valid_signature';
            }
        }
        
        return [
            'order_id' => 'required|exists:orders,id',
            'role' => 'required|in:BUYER,SELLER,HELPER',
            'solution_id' => 'required|is_valid_solution',
            'description' => 'max:2000',
            'refund' => $refundRule,
            'signature' => $signatureRule,
        ];
    }
}
