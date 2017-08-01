<?php

namespace App\Http\Requests;

use DateTime;
use Auth;
use App\Solution;
use App\Order;
use App\PhoneVerifyCode;
use DB;

class CustomValidator
{

    public static function register()
    {
        \Validator::extend("comma_array_numeric", function ($attribute, $values, $parameters, $validator) {
            if(!is_array($values)){ $values = explode(',', $values); }
            $rules = [
                $attribute => 'numeric',
            ];
            return self::validateArray($attribute, $values, $rules);
        });
        
        \Validator::extend("comma_array_min", function ($attribute, $values, $parameters, $validator) {
            if(!is_array($values)){ $values = explode(',', $values); }
            $min = $parameters[0];
            $rules = [
                $attribute => 'numeric|min:'.$min,
            ];
            return self::validateArray($attribute, $values, $rules);
        });
        
        \Validator::extend("comma_array_max", function ($attribute, $values, $parameters, $validator) {
            if(!is_array($values)){ $values = explode(',', $values); }
            $max = $parameters[0];
            $rules = [
                $attribute => 'numeric|max:'.$max,
            ];
            return self::validateArray($attribute, $values, $rules);
        });   
        
        \Validator::extend("comma_array_between", function ($attribute, $values, $parameters, $validator) {
            if(!is_array($values)){ $values = explode(',', $values); }
            $min = $parameters[0];
            $max = $parameters[1];
            $rules = [
                $attribute => 'numeric|between:'.$min.','.$max,
            ];
            return self::validateArray($attribute, $values, $rules);
        });
        
        \Validator::extend("comma_array_distinct", function ($attribute, $values, $parameters, $validator) {
            if(!is_array($values)){ $values = explode(',', $values); }
            $seen = [];
            foreach ($values as $v) {
                if(in_array($v, $seen)){
                    return false;
                }
                array_push($seen, $v);
            }
            return true;
        }); 
        
        \Validator::extend("comma_array_in_table", function ($attribute, $values, $parameters, $validator) {
            if(!$values){ return true; }
            if(!is_array($values)){ $values = explode(',', $values); }
            $tableName = $parameters[0];
            $columnName = $parameters[1];
            $arr = DB::table($tableName)->get()->pluck($columnName)->toArray();
            foreach ($values as $v) {
                if(!in_array($v, $arr)){
                    return false;
                }
            }
            return true;
        });         
        
        \Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value > $min_value;
        });   
        
        \Validator::extend('greater_than_date', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = DateTime::createFromFormat('m/d/Y H:i', $data[$min_field]);
            $valueDate = DateTime::createFromFormat('m/d/Y H:i', $value);
            return $valueDate > $min_value;
        });    
        
        \Validator::extend('after_now', function($attribute, $value, $parameters, $validator) {
            $now = \Carbon\Carbon::now()->addMinutes(Auth::user()->utc_offset);
            $valueDate = DateTime::createFromFormat('m/d/Y H:i', $value);
            return $valueDate > $now;
        });
        
        \Validator::extend('loc_mapping_id', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            if($data['pickup_type']!='GROUP_PICKUP'){ return true; }
            $seller_mapping_ids = \App\PickupLocationMapping
                    ::join('pickup_methods', 'pickup_location_mappings.pickup_method_id', 'pickup_methods.id')
                    ->where('pickup_methods.seller_id', $data['seller_id'])->select('pickup_location_mappings.id as mapid')->pluck('mapid')->toArray();    
            return in_array($value, $seller_mapping_ids);
        });  
        

        \Validator::extend('is_valid_solution', function($attribute, $value, $parameters, $validator) {
            if(!$value){ return false; }
            $data = $validator->getData();
            $solution = Solution::find($data['solution_id']);
            if(!$solution || $solution->role != $data['role']){ return false; }
            return true;
        });  
        
        \Validator::extend('is_valid_refund', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $solution = Solution::find($data['solution_id']);
            if(!$solution){ return false; }
            
            // If this solution does not require refund, the value should be zero
            if(!$solution->require_refund){ return !$value; }
            
            // If this solution require refund, the value should larger than zero and smaller than order total
            if($solution->require_refund && $value<=0){ return false; }
            $order = Order::find($data['order_id']);
            if($order->total<$value){ return false; }
            return true;
        });  
        
        \Validator::extend('is_valid_signature', function($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $solution = Solution::find($data['solution_id']);
            if(!$solution){ return false; }

            // If this solution require signature, then this field should not be empty
            if($solution->require_signature && !$value){ return false; }
            return true;
        });       
        
        ///=========== Custom Message ==============
        \Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        }); 
        
        \Validator::replacer('greater_than_date', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
        
        \Validator::replacer('comma_array_in_table', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
    }
    
    private static function validateArray($attribute, $values, $rules){
        if ($values) {
            foreach ($values as $v) {
                $data = [
                    $attribute => $v
                ];
                $validator = \Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        }
    }
}
