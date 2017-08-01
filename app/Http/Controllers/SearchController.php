<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dish;
use App\Category;
use App\Keyword;
use App\Ingredient;
use App\Review;
use App\Seller;
use App\SellerCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use \Illuminate\Support\Facades\Input;
use App\Service\LocationService;

class SearchController extends Controller {
    
    var $default_distance = 50;

    /**
     * @SWG\Get(path="/searches/dishes",
     *   tags={"16 Searches"},
     *   summary="Returns 40 dishes by search keyword",
     *   description="Returns 40 dishes by keyword",
     *   operationId="getDishListByKeyword",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     *   @SWG\Parameter(name="keyword", in="query", required=true, type="string"),
     *   @SWG\Parameter(name="latitude", in="query", required=true, type="number"), 
     *   @SWG\Parameter(name="longitude", in="query", required=true, type="number"),  
     *   @SWG\Parameter(name="distance_to", in="query", required=false, type="number"),  
     *   @SWG\Parameter(name="price_from", in="query", required=false, type="number"), 
     *   @SWG\Parameter(name="price_to", in="query", required=false, type="number"),  
     *   @SWG\Parameter(name="category[]", in="query", required=false, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="orderby", in="query", required=false, type="string", enum={"rating","distance","most_review","price_asc","price_desc"}),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function getDishListByKeyword(Request $request) {
        $error = ['error' => 'No results found, please try with different keywords.'];
        if ($request->has('keyword')) {
            ////////////Keyword
            $DishIdArray = array();
            $keywords = Keyword::search($request->input('keyword'))->get();
            $keywords->unique('id');
            //$keywordDishIdArray = array();
            foreach ($keywords as $keyword) {
                if (!in_array($keyword, $DishIdArray, true)) {
                    array_push($DishIdArray, $keyword->dish_id);
                }
            }
            //////////Ingredient
            $ingredients = Ingredient::search($request->input('keyword'))->get();
            $ingredients->unique('id');
            //$ingredientDishIdArray = array();
            foreach ($ingredients as $ingredient) {
                if (!in_array($ingredient, $DishIdArray, true)) {
                    array_push($DishIdArray, $ingredient->dish_id);
                }
            }
            /////////Review
            $reviews = Review::search($request->input('keyword'))->get();
            $reviews->unique('id');
            //$reviewDishIdArray = array();
            foreach ($reviews as $review) {
                if (!in_array($review, $DishIdArray, true)) {
                    array_push($DishIdArray, $review->dish_id);
                }
            }

            $dishes_dish = Dish::search($request->input('keyword'))->get();
            $dishes_dish->unique('id');
            foreach ($dishes_dish as $dish) {
                if (!in_array($dish, $DishIdArray, true)) {
                    array_push($DishIdArray, $dish->id);
                }
            }
            //all Dishes find in Ingredient, keyword, review and dish
            //$dishesListById = Dish::whereIn("id", $DishIdArray)->with("imagePreview")->get();

            /////////////Category
            $categorys = Category::search($request->input('keyword'))->get()->pluck('id')->toArray();
            $dishIdsByCategory = Dish::whereIn('category_id', $categorys)->get()->pluck('id')->toArray();
            $DishIdArray = array_unique (array_merge ($DishIdArray, $dishIdsByCategory));
                
            $result = Dish::join('sellers', 'dishes.seller_id', 'sellers.id')
                    ->join('locations', 'sellers.id', 'locations.table_id')
                    ->active()
                    ->where('locations.table_name', 'sellers')
                    ->whereIn("dishes.id", $DishIdArray);
            
            // Add price/distance/category where clause
            $result = $this->filterDishes($request, $result);
            
            $result = $result->select('dishes.*', 
                            \DB::raw('ROUND( ( 3959 * acos( cos( radians('.$request->latitude.') ) * cos( radians( locations.latitude ) ) * cos( radians( locations.longitude ) - radians('.$request->longitude.')) + sin( radians('.$request->latitude.') ) * sin( radians( locations.latitude ) ) ) ) , 1) as distance'));
            
            // Sortby 
            switch ($request->orderby) {
                case 'rating': $result = $result->orderBy('rating', 'DESC'); break;
                case 'distance': $result = $result->orderBy('distance'); break;
                case 'most_review': $result = $result->orderBy('rating_count', 'DESC'); break;
                case 'price_asc': $result = $result->orderBy('price'); break;
                case 'price_desc': $result = $result->orderBy('price', 'DESC'); break;
                default: $result = $result->orderBy('id');
            }

            $result = $result->with('imagePreview')->paginate(20)->appends(Input::except(['page']));
            return $result;
        }
        return $error;
    }

    /**
     * @SWG\Get(path="/searches/sellers",
     *   tags={"16 Searches"},
     *   summary="Returns 40 sellers by search keyword",
     *   description="Returns 40 sellers by keyword",
     *   operationId="getSellerListByKeyword",
     *   produces={"application/json"},
     * 
     *   @SWG\Parameter(name="utcoffset", in="header", required=false, type="integer"),
     *   @SWG\Parameter(name="keyword", in="query", required=true, type="string"),
     *   @SWG\Parameter(name="latitude", in="query", required=true, type="number"), 
     *   @SWG\Parameter(name="longitude", in="query", required=true, type="number"), 
     *   @SWG\Parameter(name="distance_to", in="query", required=false, type="number"),  
     *   @SWG\Parameter(name="price_from", in="query", required=false, type="number"), 
     *   @SWG\Parameter(name="price_to", in="query", required=false, type="number"),  
     *   @SWG\Parameter(name="category[]", in="query", required=false, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="option[]", in="query", required=false, type="array", @SWG\Items(type="integer"), collectionFormat="multi"),
     *   @SWG\Parameter(name="orderby", in="query", required=false, type="string", enum={"rating","distance","most_review","price_asc","price_desc"}),
     * 
     *   @SWG\Response(response=200, description="success"),
     *   @SWG\Response(response=422, description="validation fail"),
     * )
     */
    public function getSellerListByKeyword(Request $request) {
        $error = ['error' => 'No results found, please try with different keywords.'];
        if ($request->has('keyword')) {
            ////////////Keyword
            $SellerIdArray = array();
            $keywords = Keyword::search($request->input('keyword'))->get();
            $keywords->unique('id');
            //$keywordDishIdArray = array();
            foreach ($keywords as $keyword) {
                if (!in_array($keyword, $SellerIdArray, true)) {
                    array_push($SellerIdArray, $keyword->seller_id);
                }
            }
            /////////Review
            $reviews = Keyword::search($request->input('keyword'))->get();
            $reviews->unique('id');
            //$reviewDishIdArray = array();
            foreach ($reviews as $review) {
                if (!in_array($review, $SellerIdArray, true)) {
                    array_push($SellerIdArray, $review->seller_id);
                }
            }
            /////////Dish
            $dishes = Dish::search($request->input('keyword'))->get();
            $dishes->unique('id');
            foreach ($dishes as $dish) {
                if (!in_array($dish, $SellerIdArray, true)) {
                    array_push($SellerIdArray, $dish->seller_id);
                }
            }
            /////////Seller
            $sellers = Seller::search($request->input('keyword'))->get();
            $sellers->unique('id');
            foreach ($sellers as $seller) {
                if (!in_array($seller, $SellerIdArray, true)) {
                    array_push($SellerIdArray, $seller->id);
                }
            }
            /////////////Category
            $categorys = Category::search($request->input('keyword'))->get();
            $categorys->unique('id');
            $categoryIdArray = array();
            foreach ($categorys as $category) {
                array_push($categoryIdArray, $category->id);
            }

            $sellerscategories = SellerCategory::whereIn('category_id', $categoryIdArray)->get();
            $sellerscategories->unique('id');
            foreach ($sellerscategories as $sellerscategory) {
                if (!in_array($sellerscategory, $SellerIdArray, true)) {
                    array_push($SellerIdArray, $sellerscategory->seller_id);
                }
            }
            
            // Apply category/price/distance filters onto sellerIdList
            $SellerIdArray = array_intersect($SellerIdArray, $this->filterSellers($request));
            
            //all Sellers found in keyword, review, dish, and sellerCategory
            $result = Seller::join('locations', 'sellers.id', 'locations.table_id')
                    ->join('dishes', 'sellers.id', 'dishes.seller_id')
                    ->active()
                    ->where('locations.table_name', 'sellers')
                    ->whereIn("sellers.id", $SellerIdArray)
                    ->select('sellers.*', 
                            \DB::raw('avg(price) as avg_price'),
                            \DB::raw('ROUND( ( 3959 * acos( cos( radians('.$request->latitude.') ) * cos( radians( locations.latitude ) ) * cos( radians( locations.longitude ) - radians('.$request->longitude.')) + sin( radians('.$request->latitude.') ) * sin( radians( locations.latitude ) ) ) ) , 1) as distance'))
                    ->groupBy('sellers.id');
                    
            // Sortby 
            switch ($request->orderby) {
                case 'rating': $result = $result->orderBy('rating', 'DESC'); break;
                case 'distance': $result = $result->orderBy('distance'); break;
                case 'most_review': $result = $result->orderBy('rating_count', 'DESC'); break;
                case 'price_asc': $result = $result->orderBy('avg_price'); break;
                case 'price_desc': $result = $result->orderBy('avg_price', 'DESC'); break;
                default: $result = $result->orderBy('id');
            }

            $result = $result->with('sellerCategory')->with("dishPreview")->paginate(20)->appends(Input::except(['page']));
            return $result;
        }
        return $error;
    }

    private function filterSellers($request) {
        $sellerIds = Seller::pluck('id')->toArray();
        
        // Get seller_id list with avg dish price between the selected filter
        if ($request->price_from || $request->price_to) {
            $priceFilter = Seller::join('dishes', 'sellers.id', 'dishes.seller_id')
                    ->select('sellers.id', \DB::raw('avg(price) as price'))
                    ->havingRaw('avg(price) between '.$request->price_from.' and '.$request->price_to)
                    ->groupBy('sellers.id')->pluck('id')->toArray();
            $sellerIds = array_intersect($sellerIds, $priceFilter);
        }

        // Get seller_id list with distance between the selected filter, if there is no distance, use 20 miles
        $distance_to = $request->distance_to? $request->distance_to : $this->default_distance;
        $distanceFilter = LocationService::getLocationByRadius($request->latitude, $request->longitude, $distance_to, 'sellers')
                   ->pluck('table_id')->toArray();
        $sellerIds = array_intersect($sellerIds, $distanceFilter);
        
        // Add Category Filter
        if($request->category){
            $categoryFilter = SellerCategory::whereIn('category_id', $request->category)->pluck('seller_id')->toArray(); 
            $sellerIds = array_intersect($sellerIds, $categoryFilter);
        }
        return $sellerIds;
    }
    
    
    private function filterDishes($request, $query) {
        
        // Filter dish by price
        if ($request->price_from || $request->price_to) {
            $query = $query->where('price', '>=', $request->price_from)->where('price', '<=', $request->price_to);
        }

        // Filter dish by user/seller distance. If there is no distance, use 20 miles
        $distance_to = $request->distance_to? $request->distance_to : $this->default_distance;
        $sellerIdByDist = LocationService::getLocationByRadius($request->latitude, $request->longitude, $distance_to, 'sellers')
                    ->pluck('table_id')->toArray();
        $query = $query->whereIn('seller_id', $sellerIdByDist);
        
        // Filter disg by category
        if($request->category){
            $query = $query->whereIn('category_id', $request->category);
        }
        return $query;
    }

}
