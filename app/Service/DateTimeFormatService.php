<?php

namespace App\Service;

trait DateTimeFormatService
{
    //const CONSTANT = 'Hello World';
    
    public function getCreatedAtAttribute($value)
    {
        return date('m/d/Y H:i:s', strtotime($value));
    }
    
    public function getUpdatedAtAttribute($value)
    {
        return date('m/d/Y H:i:s', strtotime($value));
    }
    
}