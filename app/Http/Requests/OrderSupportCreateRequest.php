<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderSupportCreateRequest extends FormRequest
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
            'order_id' => 'required|exists:orders,id',
            'problem_code_id' => $this->getValidProblemCodeRule(),
            'description' => 'max:2000'
        ];
    }
    
    private function getValidProblemCodeRule(){
        return [ 
                'required',
                Rule::exists('problem_codes', 'id')->where(function ($query) {
                    $query->where('final_problem', true);
                }),
                ];
    }
}
