<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;

class PickupMethodUpdateRequest extends FormRequest
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
            'type' => 'required|in:DATE,WEEKDAY',
            'date' => 'required_if:type,DATE|date_format:"m/d/Y"|after:today',
            'weekday' => 'required_if:type,WEEKDAY|comma_array_numeric|comma_array_between:1,7|comma_array_distinct',
            'no_time' => 'required|in:0,1',
            'start_time' => 'required_if:no_time,0|date_format:"H:i"',
            'end_time' => 'required_if:no_time,0|date_format:"H:i"',
            'loc_mappings' => $this->getLocationIdRule(),
            'new_loc' => '',//'array', // To do: check the JSON format
            'delete_loc' => $this->getLocationIdRule(),
        ];
    }
    
    public function messages()
    {
        return [
            'weekday.comma_array_numeric' => trans('validation.numeric', ['attribute' => 'weekday']),
            'weekday.comma_array_between' => trans('validation.between.numeric', ['attribute' => 'weekday', 'min' => 1, 'max' => 7]),
            'weekday.comma_array_distinct' => trans('validation.distinct', ['attribute' => 'weekday']),
        ];
    }
    
    private function getLocationIdRule(){
        return [ Rule::exists('pickup_locations', 'id')->where(function ($query) {
                    $query->where('seller_id', Auth::user()->seller->id);
                }),];
    }
}
