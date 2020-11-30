<?php

namespace App\Http\Controllers;

use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use DB;
use Log;
use Redirect;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\Cache;

class CategoriesController extends BaseController {

    private  $categoryList;                    

    public function __construct() {   
        try
        {
            $this->categoryList= '<option value="0">Please Select Category ....</option> ';
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });
            parent::Title('Categories');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }

    public function getCategoryList()
    {
        $userId = Session::get('userId');
        if(Cache::tags(['ebutor', 'categories'])->has('get_category_list_'.$userId))
        {
            $categoryList = base64_decode(Cache::tags(['ebutor', 'categories'])->get('get_category_list_'.$userId));
            return $categoryList;
        }else{
            $this->getChildCategories(0,1);
            $expiresAt = 10;
            $tempCategories2 = base64_encode($this->categoryList);
            Cache::tags(['ebutor', 'categories'])->add('get_category_list_'.$userId, $tempCategories2, $expiresAt);
            return $this->categoryList;
        }
    }
    
    public function getAddCategoryList()
    {
        $userId = Session::get('userId');
        if(Cache::tags(['ebutor', 'categories'])->has('get_all_category_list_'.$userId))
        {
            $categoryList = base64_decode(Cache::tags(['ebutor', 'categories'])->get('get_all_category_list_'.$userId));
            return $categoryList;
        }else{
            $this->getAddChildCategories(0,1);
            $expiresAt = 10;
            $tempCategories = base64_encode($this->categoryList);
            Cache::tags(['ebutor', 'categories'])->add('get_all_category_list_'.$userId, $tempCategories, $expiresAt);
            return $this->categoryList;
        }        
    }


    public function getChildCategories($cat_id,$level)
    {
        $rolesObj= new Role();
        $DataFilter= $rolesObj->getFilterData(8, Session::get('userId'));

        $DataFilter=json_decode($DataFilter,true);

        $categoryList = isset($DataFilter['category']) ? $DataFilter['category'] : [];
        
            $cat =DB::table('categories')
            ->where('categories.parent_id', $cat_id)
            ->where('is_active','1')
            ->whereIn('categories.category_id', $categoryList)
            ->get(); 
        
        if (!empty($cat)) 
        {
            foreach($cat as  $cat1)
            { 
                $disabled= ($cat1->is_product_class == '0')? "disabled":"";
                $css_class='';
                switch ($level) {
                    case 1:
                        $css_class='parent_cat';
                        break;
                    case 2:
                        $css_class='sub_cat';
                        break;
                    case 3:
                        $css_class='prod_class';
                        break;

                    default:
                        $css_class='prod_class_'.$level;
                        break;
                }
                $this->categoryList.= '<option value="'.$cat1->category_id.'" class="'.$css_class.'" '.$disabled.' > '.$cat1->cat_name.'</option>';
                $this->getChildCategories($cat1->category_id,$level+1);
            }
        }
        return $this->categoryList;
    }

    public function getAddChildCategories($cat_id,$level)
    {
        $cat =DB::table('categories')
            ->where('categories.parent_id', $cat_id)
            ->where('is_active','1')
            ->select('category_id', 'cat_name')
            ->get();
//        echo "<prE>";print_R($cat);die;
        if (!empty($cat)) 
        {
            foreach($cat as  $cat1)
            { 
                
                $css_class='';
                switch ($level) {
                    case 1:
                        $css_class='parent_cat';
                        break;
                    case 2:
                        $css_class='sub_cat';
                        break;
                    case 3:
                        $css_class='prod_class';
                        break;

                    default:
                        $css_class='prod_class_'.$level;
                        break;
                }
                $this->categoryList.= '<option value="'.$cat1->category_id.'" class="'.$css_class.'"  > '.$cat1->cat_name.'</option>';
                $this->getAddChildCategories($cat1->category_id,$level+1);
            }
        }
            
    }

      
    public function indexAction()
    {
        try
        {
            return View::make('categories/index');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function addAction()
    {
        try
        {
            return View::make('categories/add');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    
   
    public function editAction()
    {
        try
        {
            return View::make('categories/edit');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function deleteAction()
    {
        try
        {
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
