<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProblemCode;

class ProblemCodeController extends Controller
{

    /**
     * @SWG\Get(path="/problems/parentcode/{parent_code}",
     *   tags={"12 Problems"},
     *   summary="Returns all problems for the parent_code",
     *   description="Returns all problems for the parent_code, the root problems have parent_code = '0'.",
     *   operationId="viewProblems",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="parent_code", in="path", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */ 
    public function viewProblems($id = '0'){
        return ProblemCode::where('parent_code', '=', $id)->get();
    }
    
    public function viewProblemsById($id){
        return $this->viewProblems($id);
    }
    
    /**
     * @SWG\Get(path="/problems/{problemid}",
     *   tags={"12 Problems"},
     *   summary="Returns one specific problem by id",
     *   description="Returns one specific problem by id",
     *   operationId="show",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="problemid", in="path", required=true, type="string"),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=404, description="id does not exist"),
     * )
     */
    public function show($id)
    {
        return ProblemCode::findOrFail($id);
    }

}
