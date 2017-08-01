<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    var $agent;
    public function __construct() {
        $this->agent = new Agent();
    }
    
    public function getPageNo(){
        return $this->agent->isMobile()? 10:39;
    }
    
    public function getSmallPageNo(){
        return $this->agent->isMobile()? 5:39;
    }
}
