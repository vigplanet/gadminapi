<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Seller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryApiController extends Controller
{

    public function getCategories(Request $request){
        try{
        if(isset(auth()->user()->seller->id) && auth()->user()->seller->id != null && auth()->user()->role_id == 3){
            $limit = $request->input('limit');
            $offset = $request->input('offset', 0);
            $filter = $request->input('filter', '');

            $category_id = $request->get('category_id', 0);
            $category_slug = $request->get('slug');

            $seller = auth()->user()->seller;
            $sellerCategoryIds = explode(",", $seller->categories);
            
            if(isset($category_slug) && !empty($category_slug)){
                $category = Category::where('status', 1)->where('slug', $category_slug)->whereIn('id', $sellerCategoryIds)->first();
                if(!$category) {
                    return CommonHelper::responseError(__('no_category_found'));
                }
                $categories = Category::where('status', 1)->where('parent_id', $category->id);
            } else {
                if($category_id > 0) {
                    $hasAccess = false;
                    
                    if(in_array($category_id, $sellerCategoryIds)) {
                        $hasAccess = true;
                    } else {
                        $categoryMap = Category::select('id', 'parent_id')
                            ->where('status', 1)
                            ->get()
                            ->keyBy('id');
                        
                        $currentCategory = $categoryMap->get($category_id);
                        if($currentCategory && $currentCategory->parent_id != 0) {
                            $parentIds = [];
                            $tempCategory = $currentCategory;
                            $maxDepth = 20;
                            $depth = 0;
                            
                            while($tempCategory && $tempCategory->parent_id != 0 && $depth < $maxDepth) {
                                $parentIds[] = $tempCategory->parent_id;
                                $tempCategory = $categoryMap->get($tempCategory->parent_id);
                                $depth++;
                                if(!$tempCategory) break;
                            }
                            
                            if(!empty($parentIds)) {
                                $hasAccess = !empty(array_intersect($parentIds, $sellerCategoryIds));
                            }
                        }
                    }
                    
                    if(!$hasAccess) {
                        return CommonHelper::responseError(__('no_category_found'));
                    }
                    $categories = Category::where('status', 1)->where('parent_id', $category_id);
                } else {
                    $categories = Category::where('status', 1)->whereIn('id', $sellerCategoryIds);
                }
            }

            if ($filter) {
                $categories = $categories->where(function($query) use ($filter) {
                    $query->where('name', 'like', "%{$filter}%")
                          ->orWhere('subtitle', 'like', "%{$filter}%");
                });
            }

            $total = $categories->count();
            
            if(isset($limit) && $limit > 0){
                $categories = $categories->orderBy('row_order', 'ASC')->offset($offset)->limit($limit)->get(['id','name','subtitle','slug','image']);
            } else {
                $categories = $categories->orderBy('row_order', 'ASC')->get(['id','name','subtitle','slug','image']);
            }
            
            $categories = $categories->makeHidden(['image','has_child', 'has_active_child']);

            if(count($categories) > 0){
                return CommonHelper::responseWithData($categories, $total);
            } else {
                return CommonHelper::responseError(__('no_category_found'));
            }
        } else {
            $limit = $request->input('limit');
            $offset = (($request->input('offset'))-1)*$limit;
            $filter = $request->input('filter', '');
        
            $categoriesQuery = Category::orderBy('id', 'DESC');
            
            if ($filter) {
                $categoriesQuery = $categoriesQuery->where(function($query) use ($filter) {
                    $query->where('name', 'like', "%{$filter}%")
                          ->orWhere('subtitle', 'like', "%{$filter}%");
                });
            }
            $total = $categoriesQuery->count();
            if (isset($limit) && !is_null($limit)) {
                $categories = $categoriesQuery->orderBy('id', 'desc')->skip($offset)->take($limit)->get();
            } else {
                $categories = $categoriesQuery->orderBy('id', 'desc')->get();
            }
        
            if($categories->isEmpty()){
                return CommonHelper::responseError('Category not found.');
            }
        
            $categories->makeHidden(['has_child', 'has_active_child']);
        
            return CommonHelper::responseWithData($categories, $total);
        }
    } catch (\Exception $e) {
        return CommonHelper::responseError($e->getMessage());
    }
}

    public function getActiveCategories(){
        $categories = $this->buildCategoryTree(0);
        return CommonHelper::responseWithData($categories);
    }
    private function buildCategoryTree($parentId = 0)
    {
        $categories = Category::where('parent_id', $parentId)
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->get();
        
        $categories->transform(function ($category) {
            $category->cat_active_childs = $this->buildCategoryTree($category->id);
            return $category;
        });
        
        return $categories;
    }
    public function getMainCategories(Request $request){
        $categories = CommonHelper::getMainCategories($request);
        $total = $categories->count();
        if(empty($categories)){
            return  CommonHelper::responseError('Category not found.');
        }
        return CommonHelper::responseWithData($categories,$total);
    }
    public function getCategoriesByRowOrder(){
        $categories = Category::where('parent_id',0)->orderBy('row_order','ASC')->get();
        return CommonHelper::responseWithData($categories);
    }
    public function save(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'subtitle' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png,gif'
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $slug = preg_replace('/\s+/', '-', trim(
            preg_replace('/[^A-Za-z0-9 ]/', '', $request->name)
        ));

        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->subtitle = $request->subtitle;
        $category->slug = $slug;
        $image = '';
        if($request->hasFile('image')){
            $file = $request->file('image');
            $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();
            $image = Storage::disk('public')->putFileAs('categories', $file, $fileName);
        }
        $category->image = $image; 
        $category->web_image = '';
        $category->status = 1;
        $category->parent_id = $request->parent_id;
        $category->meta_title = $request->meta_title ?? "";
        $category->meta_keywords = $request->meta_keywords ?? "";
        $category->schema_markup = $request->schema_markup ?? "";
        $category->meta_description = $request->meta_description ?? "";
        $category->save();
        return CommonHelper::responseSuccess("Category Saved Successfully!");
    }
    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'subtitle' => 'required',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        if(isset($request->id)){ 
            $category = Category::find($request->id);
            $newSlug = preg_replace('/\s+/', '-', trim(
                preg_replace('/[^A-Za-z0-9 ]/', '', $request->name)
            ));
            $count = Category::where('slug', $newSlug)->where('id', '!=', $category->id) ->count();

            if ($count > 0) {
                $newSlug .= '-' . ($count + 1);
            }

            $category->name = $request->name;
            $category->subtitle = $request->subtitle;
            $category->status = $request->status;
            $category->slug = $newSlug;
            $category->parent_id = $request->parent_id;
            $category->meta_title = $request->meta_title ?? "";
            $category->meta_keywords = $request->meta_keywords ?? "";
            $category->schema_markup = $request->schema_markup ?? "";
            $category->meta_description = $request->meta_description ?? "";
            if($request->hasFile('image')){
                @Storage::disk('public')->delete($category->image);
                $file = $request->file('image');
                $fileName = time().'_'.rand(1111,99999).'.'.$file->getClientOriginalExtension();
                $image = Storage::disk('public')->putFileAs('categories', $file, $fileName);
                $category->image = $image;
            }
            $category->save();
        }
        
        return CommonHelper::responseSuccess("Category Updated Successfully!");
    }
    public function delete(Request $request){
        if(isset($request->id)){
            $category = Category::find($request->id);
            if($category){
                @Storage::disk('public')->delete($category->image);
                $category->delete();
                return CommonHelper::responseSuccess("Category Deleted Successfully!");
            }else{
                return CommonHelper::responseSuccess("Category Already Deleted!");
            }
        }
    }


    public function getOptions(Request  $request){
        echo"<option value='0' selected >Select Category</option>";
        $options = CommonHelper::categoryTree(0,'',null,array(), false,array(),$request->exclude_id,0);
    }

    public function updateCategoriesOrder(Request $request){
        $categories = $request->all();
        foreach ($categories as $key => $category){
            $data = Category::find($category["id"]);
            $data->row_order = $category["row_order"];
            $data->save();
        }
        return CommonHelper::responseSuccess("Category Order Updated Successfully!");
    }
    public function countProductCategoryWise(){
        $categories = Category::select('id','name',DB::raw('(SELECT count(id) from `products` WHERE products.category_id = categories.id) AS product_count'))
            ->orderBy('id','ASC')->get();
        return CommonHelper::responseWithData($categories);
    }

    public function getSellerCategories(Request $request){

        CommonHelper::categoryTree(0,'',null,array(), true,array(),'', $request->seller_id);
    }
    public function checkSlug(Request $request, $slug)
    {
        try {
            // Query the database to count the documents that match the slug pattern
            $existingDocumentCount = Category::where('slug', 'like', $slug . '%')->count();

            // Construct the response data
            $responseData = [
                'unique' => $existingDocumentCount === 0,
                'count' => $existingDocumentCount
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Error checking slug uniqueness: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while checking slug uniqueness'], 500);
        }
    }

}
