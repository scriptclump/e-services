<?php
namespace App\Modules\Categories\Models;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;
use \DB;
class CategoryModel extends \Eloquent {

    protected $table = 'categories'; // table name
    protected $primaryKey = 'product_id';
    public $timestamps = false;
    private $customerRepo;
    private $roleRepo;
    
    public function __construct()
    {
        $this->customerRepo = new CustomerRepo;
        $this->roleRepo = new RoleRepo;
    }
   
    public function getParentCats($page = 1, $pageSize = 1, $orderby='', $filterBy = '')
    {
        $query = $this->where("parent_id", "=", "0");

        if(!empty($orderby))
        {
            $orderClause            = explode(" ", $orderby);
            $query                    = $query->orderby($orderClause[0], $orderClause[1]);  //order by query
        }

        if (!empty($filterBy)) {
            foreach ($filterBy as $filterByEach) {
                $filterByEachExplode = explode(' ', $filterByEach);

                $length = count($filterByEachExplode);
                $filter_query_value = '';
                if ($length > 3) {
                    $filter_query_field = $filterByEachExplode[0];
                    $filter_query_operator = $filterByEachExplode[1];
                    for ($i = 2; $i < $length; $i++)
                        $filter_query_value .= $filterByEachExplode[$i] . " ";
                } else {
                    $filter_query_field = $filterByEachExplode[0];
                    $filter_query_operator = $filterByEachExplode[1];
                    $filter_query_value = $filterByEachExplode[2];
                }

                $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                if (in_array(trim($filter_query_operator), $operator_array)) {
                    $query = $query->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                } else {
                    $query = $query->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }

        $count = $query->count();
                $result = array();
                $result['count'] = $count;
                $query = $query->skip($page * $pageSize)->take($pageSize);
                $result['result'] = $query->get(array("cat_name", "category_id", "parent_id", "is_active", "is_product_class"))->all();
        return $result;

    }

    public function getChildData($page = 1, $pageSize = 1, $catid)
    {
        $query = $this->where("parent_id", "=", $catid);
        $count = $query->count();
                $result = array();
                $result['count'] = $count;
                $query = $query->skip($page * $pageSize)->take($pageSize);
                $result['result'] = $query->get(array("cat_name", "category_id", "parent_id", "is_active", "is_product_class"))->all();
        return $result;
    }

    public function allCategory()
    {
        $query = $this->get(array("cat_name", "category_id", "parent_id", "is_active", "is_product_class"))->all();
        return $query;
    }

    public function allChildCats($catid)
    {
        $query = $this->where("parent_id",$catid)->get(array("cat_name", "category_id", "parent_id", "is_active", "is_product_class"))->all();
        return $query;
    }


    public function getCategories()
    {
        $result = DB::table('categories')->get(array(DB::raw('category_id as id'), DB::raw('name as category_name')))->all();
      
        $returnData = array();
        if(!empty($result))
        {
            foreach ($result as $data) {            
                $returnData[$data->id] = $data->category_name;
            }
        }
        return $returnData;
    }
    
    public function getTreeGridData() {
        
            $categ = DB::Table('categories')
                    //->join('customer_categories', 'customer_categories.category_id', '=', 'categories.category_id')
                    ->select('categories.name', 'categories.category_id', 'categories.is_active', 'categories.parent_id', 'categories.is_product_class')
                    //->where('categories.parent_id', 0)
                    ->get()->all();
       
        // code for parent columns
        $tempCategoryIds = array();
        $finalcategoryparent = array();
        $categoryparent = array();
//        $category = json_decode(json_encode($categ), true);
        //return $cat->id;
        $category = $categ;
        $categoryArray = array();
        if (!empty($category)) {
            foreach ($category as $categoryData) {
                if (isset($categoryData->category_id) && $categoryData->category_id != '') {
                    $categoryArray[] = $categoryData->category_id;
                }
            }
        }
        foreach ($category as $catparent) {
            # code for child columns...            
            if (!empty($categoryArray)) {
                $categchild = DB::Table('categories')
                        ->select('categories.parent_id', 'categories.category_id', 'categories.name', 'categories.status', 'categories.top', 'categories.column', 'categories.sort_order', 'categories.parent_id', 'categories.is_active', 'categories.is_product_class')
                        ->where('categories.parent_id', $catparent->category_id)
                        ->whereIn('categories.category_id', $categoryArray)
                        ->get()->all();
            } else {
                $categchild = DB::Table('categories')
                        ->select('categories.parent_id', 'categories.category_id', 'categories.name', 'categories.status', 'categories.top', 'categories.column', 'categories.sort_order', 'categories.parent_id', 'categories.is_active', 'categories.is_product_class')
                        ->where('categories.parent_id', $catparent->category_id)
                        ->get()->all();
            }
            $categoryparent = array();
            if (!empty($categchild)) {
                $finalcategorychild = array();
                $categorychild = array();
//                $categorychildencode = json_decode(json_encode($categchild), true);
                foreach ($categchild as $catchild) {
                    if (!empty($categoryArray)) {
                        $getprodclass = DB::Table('categories')
                                ->SELECT('categories.name', 'categories.category_id', 'categories.is_active', 'categories.is_product_class')
                                ->whereIn('categories.category_id', $categoryArray)
                                ->where('categories.parent_id', $catchild->category_id)
                                ->get()->all();
                    } else {
                        $getprodclass = DB::Table('categories')
                                ->SELECT('categories.name', 'categories.category_id', 'categories.is_active', 'categories.is_product_class')
                                ->where('categories.parent_id', $catchild->category_id)
                                ->get()->all();
                    }
                    $finalProdClassArr = array();
                    $prod = array();
//                    $prodclass_details = json_decode(json_encode($getprodclass), true);
                    foreach ($getprodclass as $values) {
                        if (!in_array($values->category_id, $tempCategoryIds)) {
                            $finalProdClassArr[] = $values;
                        }
                        $tempCategoryIds[] = $values->category_id;
                    }
                    $catchild->childs = $finalProdClassArr;
                    if (!in_array($catchild->category_id, $tempCategoryIds)) {
                        $finalcategorychild[] = $catchild;
                    }
                    $tempCategoryIds[] = $catchild->category_id;
                }

                $catparent->childs = $finalcategorychild;
                if (!in_array($catparent->category_id, $tempCategoryIds)) {
                    $finalcategoryparent[] = $catparent;
                }
                $tempCategoryIds[] = $catparent->category_id;
            } else {
                if (isset($catparent->parent_id) && $catparent->parent_id == 0) {
                    if (!in_array($catparent->category_id, $tempCategoryIds)) {
                        $finalcategoryparent[] = $catparent;
                    }
                    $tempCategoryIds[] = $catparent->category_id;
                }
            }
        }
//        echo "<pre>";print_r($finalcategoryparent);die;
        return $finalcategoryparent;
    }
    public function getCategoryList($categoryId)
    {
        try
        {
            $resultArray = array();            
            $categoryArray = array();            
            $categoryList = $this->listCategoriesById($categoryId);            
            foreach($categoryList as $category)
            {                
                $index = 0;
                $index2 = 0;
                $index3 = 0;
                $tempArray = array();
                $categoryId = $category->category_id;
                if(in_array($categoryId, $categoryArray))
                {
                    continue;                    
                }
                $categoryArray[] = $categoryId;
                $tempArray[$index] = $category;
                $childCategoryList = $this->listCategoriesById($categoryId);                
                if(count($childCategoryList) == 1)
                {
                    $tempArray[$index]->childs = $childCategoryList;
                }elseif(count($childCategoryList) > 1){
                    $tempArray2 = array();
                    foreach($childCategoryList as $categoryLists)
                    {
                        $tempArray3 = array();
                        $tempArray2[$index2] = $categoryLists;
                        $childCategoryId = $categoryLists->category_id;
                        if(in_array($childCategoryId, $categoryArray))
                        {
                            continue;                    
                        }
                        $categoryArray[] = $childCategoryId;
                        $childCategoryList = $this->listCategoriesById($childCategoryId);
                        if(count($childCategoryList) == 1)
                        {
                            $tempArray2[$index2]->childs = $childCategoryList;                            
                        }elseif(count($childCategoryList) > 1){                            
                            foreach($childCategoryList as $childCategories)
                            {
                                $tempArray4 = array();                                
                                $tempArray3[$index3] = $childCategories;
                                $childChildCategoryId = $childCategories->category_id;
                                if(in_array($childChildCategoryId, $categoryArray))
                                {
                                    continue;                    
                                }
                                $categoryArray[] = $childChildCategoryId;
                                $childChildCategoryList = $this->listCategoriesById($childChildCategoryId);
                                if(count($childChildCategoryList) == 1)
                                {
                                    $tempArray3[$index3]->childs = $childChildCategoryList;                            
                                }elseif(count($childChildCategoryList) > 1){                            
                                    foreach($childChildCategoryList as $childChildCategories)
                                    {
                                        if(in_array($childChildCategories->category_id, $categoryArray))
                                        {
                                            continue;                    
                                        }
                                        $categoryArray[] = $childCategoryId;
                                        $categoryArray[] = $childChildCategories->category_id;
                                        $tempArray4[] = $childChildCategories;
                                    }
                                }
                                if(!empty($tempArray4))
                                {
                                    $tempArray3[$index3]->childs = $tempArray4;
                                }
                                $index3++;
                            }
                        }
                        if(!empty($tempArray3))
                        {
                            $tempArray2[$index2]->childs = $tempArray3;
                        }
                        $index2++;
                    }
                    $tempArray[$index]->childs = $tempArray2;
                    $index++;
                }
                $resultArray[] = $tempArray;
                $index++;
            }
            return ($resultArray);
        } catch (Exception $ex) {
            echo "<pre>";print_r($ex);die;
        }
    }
    
    
    public function listCategoriesById($categoryId)
    {
        if(!$categoryId)
        {
            $result = DB::table('categories')->get(array('category_id','is_active', 'cat_name', 'parent_id'))->all();
        }else{
            $result = DB::table('categories')->where('parent_id', $categoryId)->get(array('category_id', 'cat_name', 'parent_id','is_active'))->all();
        }
        $returnData = array();
        if(!empty($result))
        {
            foreach ($result as $data) {            
                //$returnData[$data->category_id] = $data->name;
                $data->is_active = ($data->is_active==1) ? 'Active' : 'In-Active';
                $returnData[] = $data;
            }
        }
        return $returnData;
    }

    public function insertUploadProducts($slab_data){
        $returnResult = array();
        try{  
            $created_by = $slab_data['created_by'];
            $created_at = date("Y-m-d");
            // Check in the Margins Unique Table
            $getMarginUniqueData = DB::table("category_margin")
                                    ->where('category_id', '=', $slab_data['category_id'])
                                    ->where('dc_id', '=', $slab_data['dc_id'])
                                    ->first();
            $update_flag=0;
            if( $getMarginUniqueData ){
                // if the Record exist in the main table and effective date is lesser than the new date, then update
                if( $getMarginUniqueData->effective_date <= $slab_data['effective_date'] ){
                // also checking for the current date
                    if( $slab_data['effective_date'] <= date('Y-m-d') ){
                        DB::table('category_margin')
                            ->where('cm_id', '=', $getMarginUniqueData->cm_id )
                            ->update(['effective_date' => $slab_data['effective_date'], 
                                    'updated_by' => $created_by, 'updated_at' => $created_at,
                                    'fc_category_margin' => $slab_data['fc_category_margin'],
                                    'dc_category_margin' => $slab_data['dc_category_margin'],
                                    'fc_margin_type' => $slab_data['fc_margin_type'],
                                    'dc_margin_type' => $slab_data['dc_margin_type']]);
                        $update_flag=1;
                    }
                }
                $returnResult['message'] = "Margin(s) Updated Successfully!";
                $returnResult['counter_flag'] = "1";
            }else{
                // inserting
                DB::table("category_margin")->insert($slab_data);
                $update_flag = 1;
                $returnResult['message'] = "Margin(s) Inserted Successfully";
                $returnResult['counter_flag'] = "2";
            }
        }catch(\ErrorException $ex){
            $returnResult['message'] = "Error occures, please check with system admin.";
            $returnResult['counter_flag'] = "3";
        }
        return $returnResult;
    }
    public function getdcID($dccode){
        $dc_type = trim($dccode);
        $dc_type = DB::table("legalentity_warehouses")
                    ->where('le_wh_code', '=', $dc_type)
                    ->first();
        return $dc_type;       
    }
    public function getAllCategories(){
        $categories = DB::table('categories')
                        ->select('cat_name','category_id')
                        ->where('is_product_class',1)->get()->all();
        return $categories;
    }
}