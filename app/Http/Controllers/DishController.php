<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Http\Requests;
use Exception;
use App\Dish;
use App\DishImage;
use App\Seller;
use App\Ingredient;
use App\Keyword;
use Illuminate\Support\Facades\Response;
use App\Service\ImageService;
use App\Http\Requests\DishCreateRequest;
use App\Http\Requests\DishUpdateRequest;

class DishController extends Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
        //return Dish::paginate(40);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    
    /**
     * @SWG\Post(path="/dishes",
     *   tags={"03 Dishes"},
     *   summary="Create a new dish",
     *   description="Create a new dish for seller, include all dish image update (file can upload mutiple, but swagger can not)",
     *   operationId="store",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="category_id", in="formData", required=true, type="integer"),
     *   @SWG\Parameter(name="name", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="price", in="formData", required=true, type="number", format ="float"),
     *   @SWG\Parameter(name="description", in="formData", required=true, type="string"),
     *   @SWG\Parameter(name="isactive", in="formData", required=false, type="string", enum={"1","0"}),
     *   @SWG\Parameter(name="image[]", in="formData", required=true, type="file"),
     *   @SWG\Parameter(name="keywords", in="formData", required=false, type="array", @SWG\Items(type="string"), collectionFormat="multi"),
     *   @SWG\Parameter(name="ingredients", in="formData", required=false, type="array", @SWG\Items(type="string"), collectionFormat="multi"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=400, description="missing required parameters"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function store(DishCreateRequest $request)
    {
        //check the file type jpg.....
        $seller = $request->user()->seller;
        $request->merge(['seller_id'=> $seller->id]);
        if(!$request->image){ abort(400, 'Images are required'); }  
        
        $dish = Dish::create($request->except('image', 'ingredients', 'keywords'));

        // Upload dish images
        $files = Input::file('image');
        $this->storeImage($dish, $files);
         
        // Add keywords and ingredients
        $this->storeKeywords($seller->id, $dish, $request->input('keywords'));
        $this->storeIngredient($dish, $request->input('ingredients'));
        
        return $dish;
    }

    /**
     * @SWG\Get(path="/dishes/{dishid}",
     *   tags={"03 Dishes"},
     *   summary="Returns one specific dish by id",
     *   description="Returns one specific dish info by id",
     *   operationId="show",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="dishid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="dish does not exist"),
     * )
     */
    public function show($id) 
    {   
        return Dish::with(['dishImage', 'keyword', 'ingredient'])->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @SWG\Post(path="/dishes/{dishid}",
     *   tags={"03 Dishes"},
     *   summary="Update the info for the dish",
     *   description="Update any change the dish (file can upload mutiple, but swagger can not)",
     *   operationId="update",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="dishid", in="path", required=true, type="integer"), 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   @SWG\Parameter(name="_method", in="formData", required=true, type="string", enum={"PUT"}),
     *   @SWG\Parameter(name="category_id", in="formData", required=false, type="integer"),
     *   @SWG\Parameter(name="name", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="price", in="formData", required=false, type="number", format ="float"),
     *   @SWG\Parameter(name="description", in="formData", required=false, type="string"),
     *   @SWG\Parameter(name="isactive", in="formData", required=false, type="string", enum={"1","0"}),
     *   @SWG\Parameter(name="image[]", in="formData", required=false, type="file"),
     *   @SWG\Parameter(name="keywords", in="formData", required=false, type="array", @SWG\Items(type="string"), collectionFormat="multi"),
     *   @SWG\Parameter(name="ingredients", in="formData", required=false, type="array", @SWG\Items(type="string"), collectionFormat="multi"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function update(DishUpdateRequest $request, $id)
    {
        $dish = Dish::findOrFail($id);
        $this->authorize('update', $dish);
        
        $dish->update($request->except('image', 'ingredients', 'keywords'));
        
        // Update dish images
        $files = Input::file('image');
        $this->storeImage($dish, $files);
        
        // Update keywords and ingredients
        $this->storeKeywords($dish->seller->id, $dish, $request->input('keywords'));
        $this->storeIngredient($dish, $request->input('ingredients'));   
        
        return $dish;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);
        $this->authorize('delete', $dish);

        $dish->isactive = '0';
        $dish->save();
        return $dish;
    }
    
    /**
     * @SWG\Delete(path="/dishes/dishimages/{dishimageid}",
     *   tags={"03 Dishes"},
     *   summary="Delete a dishImage by id",
     *   description="Delete a dishImage by id",
     *   operationId="destroyImage",
     *   produces={"application/xml", "application/json"},
     *   consumes ={"application/x-www-form-urlencoded" },
     * 
     *   @SWG\Parameter(name="dishimageid", in="path", required=true, type="integer"), 
     *   @SWG\Parameter(name="Authorization", in="header", required=true, type="string"),
     *   
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=401, description="user info is invalid"),
     *   @SWG\Response(response=403, description="action is not permitted"),
     * )
     */
    public function destroyImage(Request $request, $id=0)
    {
        if(!$id){ $id = $request->key; }
        $dishImage = DishImage::findOrFail($id);
        $dish = $dishImage->dish;
        $this->authorize('deleteImage', $dish);
        
        ImageService::deleteDishImages($dishImage);
        DishImage::destroy($id);
        return $dishImage;
    }    
    
    /**
     * @SWG\Get(path="/dishes/newest",
     *   tags={"03 Dishes"},
     *   summary="Returns 40 dishes by desc create time",
     *   description="Returns newest dishes info by create time",
     *   operationId="getListByNewest",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */
     public function getListByNewest(Request $request)
    {
        $dishes = Dish::active()
                ->orderBy('created_at', 'desc')
                ->with('imagePreview')
                ->paginate($this->getPageNo());
        return $dishes;
    }
    /**
     * @SWG\Get(path="/dishes/rating",
     *   tags={"03 Dishes"},
     *   summary="Returns 40 dishes by desc overall_rating",
     *   description="Returns highest dishes info",
     *   operationId="getListByRating",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */    
    public function getListByRating(Request $request)
    {
        $dishes = Dish::active()
                ->orderBy('rating', 'desc')
                ->with('imagePreview')
                ->paginate($this->getPageNo());
        return $dishes;
    }
    /**
     * @SWG\Get(path="/dishes/category/{categoryid}",
     *   tags={"03 Dishes"},
     *   summary="Returns 40 dishes by category",
     *   description="Returns dishes info by category",
     *   operationId="getListByCategory",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="categoryid", in="path", required=true, type="integer"),
     *   @SWG\Parameter(name="Authorization", in="header", required=false, type="string"),
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     * 
     *   @SWG\Response(response=200, description="success"),
     * )
     */    
    public function getListByCategory($id)
    {
        return Dish::active()
                ->where('category_id', $id)
                ->with('imagePreview')
                ->paginate($this->getPageNo());
    }
    
    private function storeKeywords($sellerId, $dish, $keywords)
    {
        if(!is_array($keywords)){ $keywords = explode(',', $keywords); }
        $dish->keyword()->delete();
        
        foreach($keywords as $index=>$keyword){
            if(!$keyword){ continue; }
            Keyword::create([
                'seller_id' => $sellerId,
                'dish_id' => $dish->id,
                'word' => $keyword,
                'order' => $index   
            ]);
        }
    }
    
    private function storeIngredient($dish, $ingredients)
    {
        if(!is_array($ingredients)){ $ingredients = explode(',', $ingredients); }
        $dish->ingredient()->delete();
        
        foreach($ingredients as $index=>$ingredient){
            if(!$ingredient){ continue; }
            Ingredient::create([
                'dish_id' => $dish->id,
                'word' => $ingredient,
                'order' => $index   
            ]);
        }
    }
    
    private function storeImage($dish, $files)
    {
        if(!$files){ return; }
        foreach($files as $index=>$file) {
            if($file->isValid()){
                $dishImage = DishImage::create([
                    'dish_id' => $dish->id,
                    'path' => '',
                    'order' => $index 
                ]);
            
                $fileName = 'dishes/'.$dish->id.'/' . $dishImage->id . '.'.$file->getClientOriginalExtension();
                Storage::put('public/'.$fileName, File::get($file));
                $dishImage->update(['path' => $fileName]);
                
                ImageService::resizeDishImage($dishImage);
            }
        }
    }    
    
}
