<?php
namespace App\Modules\Attributes\Controllers;
use View;   
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Log;
use DB;
use Response;
use Session;

use App\Http\Controllers\BaseController;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\MarginCalculatorRepo;
use App\Modules\Attributes\Models\Products;
use App\Modules\Attributes\Models\ProductAttributes;
use App\Modules\Attributes\Models\AttributeSet;
use App\Modules\Attributes\Models\AttributeSetMapping;
use Illuminate\Support\Facades\Config;
use Excel;
class ProductController extends BaseController {

    var $FlipkartObj;
    protected $_product;    
    protected $_manufacturerId;
    private $roleRepo;
    private $custRepo;
    private $marginRepo;
    public function __construct() {
         parent::Title('Attributes -Ebutor');
        $product = new Products();
        $productattr = new ProductAttributes();
        $roleRepo= new RoleRepo;
         $this->roleRepo= $roleRepo;
        $this->_roleRepo = new RoleRepo();
        $this->_product = $product;
        $this->_productattr = $productattr;
        $this->_attributesSet = new AttributeSet();
        $this->_attributeSetMap = new AttributeSetMapping();
        $this->childGrid_field_db_match = array(
            'name'                  => 'attributes_groups.name',
            'attribute_code'        => 'attributes.attribute_code',
            'is_varient'            => 'attribute_set_mapping.is_varient',
            'is_secondary_varient'  => 'attribute_set_mapping.is_secondary_varient',
            'is_third_varient'      => 'attribute_set_mapping.is_third_varient',
            'is_filterable'         => 'attribute_set_mapping.is_searchable',
            'is_searchable'         => 'attribute_set_mapping.is_searchable'            
        );
        $this->grid_field_db_match = array(
            'attribute_set_name'    => 'attribute_sets.attribute_set_name',
            'cat_name'              => 'categories.cat_name'
        );
    }     
  

    
    public function productSaveActions($data)
    {
        
        $data['attributes']['brand_name'] = $data['brand_name'];
        
        try
        {
            $productId = isset($data['product_id']) ? $data['product_id'] : '';
            if (isset($data['media']))
            {
                // save product media
                $product_media = new Products\ProductMedia();
                $product_media_id = $product_media->saveProductMedia($data);
            }

            if (isset($data['package']))
            {
                // save package details
                isset($data['package']) ? $data['package']['product_id'] = $productId : '';
                $product_package = new Products\ProductPackage();
                $product_package_id = $product_package->saveProductPackage($data);
            }else{
                DB::table('product_packages')->where('product_id', $productId)->delete();
            }

/*            if (isset($data['pallet']))
            {
                // save pallet details
                $product_pallet = new Products\ProductPallet();
                $product_pallet_id = $product_pallet->saveProductPallet($data);
            }*/
            
            if (isset($data['slab_rate']))
            {
                //echo "<pre/>";print_r($productId);exit;
                // save pallet details
               // isset($data['slab_rate']) ? $data['slab_rate']['product_id'] = $productId : '';
                //$this->_product->saveProductSlabRate($data);
                foreach($data['slab_rate'] as $value){
                    
                    $slab = json_decode($value);
                    
                    $slab1 = $slab->slabPrice;
                    $slab2 = $slab->startRange;
                    $slab3 = $slab->EndRange;
                    

                    $slabprice = DB::Table('products_slab_rates')
                                                  ->insertGetId(array(
                        'start_range' =>$slab1, 'end_range' =>$slab3,'price'=>$slab2,'product_id'=>$productId
                     ));
                    
                    //echo "<pre/>";print_r($slab1);exit;
                    
                }
            }
            
            if (isset($data['location']))
            {
                // save pallet details
                isset($data['location']) ? $data['location']['product_id'] = $productId : '';
                $this->_product->saveProductLocation($data);
            }elseif(isset($data)){
                DB::table('product_locations')->where('product_id', $productId)->delete();
            }
            
            if (isset($data['product_attribute_sets']))
            {
                // save pallet details
                isset($data['product_attribute_sets']) ? $data['product_attribute_sets']['product_id'] = $productId : '';
                $this->_product->saveProductAttributesets($data);
            }else{
                DB::table('product_attributesets')->where('product_id', $productId)->delete();
            }

            if (isset($data['service_center']))
            {
                // save service center details
                isset($data['service_center']) ? $data['service_center']['product_id'] = $productId : '';
                $product_service_center = new Products\ProductServiceCenter();
                $product_service_center_id = $product_service_center->saveProductServiceCenter($data);
            }

            if (isset($data['attributes']))
            {
                   
                $product_attributes = new Products\ProductAttributes();
                $product_attributes_id = $product_attributes->saveProductAttributes($data);
                    
            }
            
            if (isset($data['component_selected']))
            {
                // save pallet details
                isset($data['component_selected']) ? $data['component_selected']['product_id'] = $productId : '';
                $this->_product->saveComponentProducts($data);
            }
            
            if (isset($data['prod_text_det']))
            {
                // save pallet details
                isset($data['prod_text_det']) ? $data['prod_text_det']['product_id'] = $productId : '';
                $this->_product->saveProductsGdsData($data);
            }

            $this->_product->saveCompleteData($productId);
        } catch (ErrorException $ex)
        {
            die($ex);
        }
    }

    public function getElementData()
    {
        $data = Input::all();
    
        if (!empty($data))
        {
            $dataElement = isset($data['data_type']) ? $data['data_type'] : '';
            $dataValue = isset($data['data_value']) ? $data['data_value'] : '';
            switch ($dataElement)
            {
                case 'businessUnits':
                    $result = DB::table('business_units')->where('manufacturer_id', $dataValue)->get(array('business_unit_id', 'name'))->all();
                    break;
                case 'attributeSets':
                    if(is_array($dataValue))
                    {
                        $result = DB::table('attribute_sets as att')
                            ->join('categories as cat', 'cat.category_id', '=', 'att.category_id')
                            ->whereIn('cat.category_id', $dataValue)
                            ->get(array('attribute_set_id', 'attribute_set_name'))->all();

                    }else{
                        $result = DB::table('attribute_sets as att')
                            ->join('categories as cat', 'cat.category_id', '=', 'att.category_id')
                            ->where('cat.category_id', $dataValue)
                            ->get(array('attribute_set_id', 'attribute_set_name'))->all();
                             
                    }
                   
                    if (empty($result))
                    {
                        $result = DB::table('attribute_sets')
                                ->get(array('attribute_set_id', 'attribute_set_name'))->all();
                    }
                    break;
                case 'attributeGroups':
                    $result = DB::table('attributes_groups')
                    ->get(array('attribute_group_id', 'name'))->all();
                    break;
                case 'getAttributeGroups':
                    $attributeSetId = isset($data['attribute_set_id']) ? $data['attribute_set_id'] : 0;
                    $attributeId = isset($data['attribute_id']) ? $data['attribute_id'] : 0;
                    if($attributeId)
                    {
                        $result = DB::table('attributes_groups')
                            ->where('attribute_set_id', $attributeSetId)
                            ->get(array('attribute_group_id', 'name'))->all();
                    }else{
                        $result = DB::table('attributes_groups')
                            ->where('attribute_set_id', $attributeSetId)
                            ->get(array('attribute_group_id', 'name'))->all();                        
                    }                    
                    break;
                case 'locations':
                    $result = DB::table('locations')->where(array('manufacturer_id' => $dataValue, 'is_deleted' => 0))->get(array('location_id', 'location_name'))->all();
                    break;
                case 'location_types':
                    $result = DB::table('location_types')->where(array('manufacturer_id' => $dataValue, 'is_deleted' => 0))->get(array('location_type_id', 'location_type_name'))->all();
                    break;
                case 'groups':
                    $result = DB::table('product_groups')->get(array('group_id', 'name'))->all();
                    break;
                case 'categories':
//                    $result = DB::table('customer_categories as cust')
//                            ->join('categories as cat', 'cat.category_id', '=', 'cust.category_id')
//                            ->where('cust.customer_id', $dataValue)
//                            ->get(array('cat.category_id', 'name'));
                    $result = DB::table('categories')
                            ->get(array(DB::raw('category_id'), DB::raw('name')))->all();
                    
                    break;
                case 'category_childs':
                    $result = DB::table('categories')
                            ->where('parent_id', $dataValue)
                            ->get(['category_id', 'name'])->all();
                  
                    break;
                case 'component_products':
                    $productId = isset($data['product_id']) ? $data['product_id'] : '0';
                    return $this->_product->getManufacturerProducts($dataValue, $productId);
                    break;
                case 'locations_groups':
                    $result1 = DB::table('locations')->where(array('manufacturer_id' => $dataValue, 'is_deleted' => 0))->get(array('location_id as id', 'location_name as name'))->all();
                    $result2 = DB::table('product_groups')->where(array('manufacture_id' => $dataValue))->get(array('group_id as id', 'name'))->all();
                    $result = ['locations' => $result1, 'groups' => $result2];
                    break;
                default:
                    break;
            }
            return json_encode($result);
        } else
        {
            return 'No Data Posted';
        }
    }
    
    public function importFromErp()
    {
        try
        {
            $data = Input::all();
            $productData = new \Products\ProductData();
            return $productData->erpDataImport($data);
        } catch (\ErrorException $ex) {
            return Response::json([
                'status' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }

    /* Categories actions */

    public function getAttributeSets($category_id='') {
        $data = Input::all(); 
        $category_id = isset($data['category_id'])?$data['category_id']:$category_id;
        $categoryList = '';        
        if($category_id==''){
            $result = DB::table('attribute_sets')
                        ->get(array('attribute_sets.attribute_set_id', 'attribute_sets.attribute_set_name'))->all();
        }else{
            $result = DB::table('attribute_sets')
                    ->where('attribute_sets.category_id', $category_id)
                    ->get(array('attribute_sets.attribute_set_id', 'attribute_sets.attribute_set_name'))->all();
        }
        return $result;              
    }

    public function getCategoryList()
    {
        return $this->_product->getCategoryList(0);        
    }

    public function setoptions()
    {
        Session::forget('attri_options');
        $values = Input::all();
        Session::put('attri_options',$values);                
    }



    /* Attributes actions */

    public function saveAttribute()
    {
        $opt = Session::get('attri_options');
        $attributeObj = new ProductAttributes();
        return $attributeObj->saveAttribute(Input::all(),$opt);
    }

    public function editAttribute($attribute_id,$attribute_set_id)
    {
        $attribute_id=$this->roleRepo->decodeData($attribute_id);
        //$attribute_set_id=$this->roleRepo->decodeData($attribute_set_id);
        //return $attribute_set_id;
        $editattribute = DB::Table('attributes')                
                ->join('attribute_set_mapping', 'attribute_set_mapping.attribute_id', '=', 'attributes.attribute_id')
                ->leftjoin('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attribute_set_mapping.attribute_group_id')
                ->select('attributes.*', 'attribute_set_mapping.attribute_set_id', 'attributes_groups.name as attribute_group_name', 'attributes_groups.attribute_group_id')
                ->where('attributes.attribute_id', $attribute_id)
                ->where('attribute_set_mapping.attribute_set_id',$attribute_set_id)
                ->first();
        
        return Response::json($editattribute);
    }

    public function updateAttribute($attribute_id)
    {
        $data=Input::all();
         $userId = Session::get('userId');
                        //validator
                     $validator = \Validator::make(
                                    array(
                                'name' => isset($data['name']) ? $data['name'] : '',
                               'attribute_code'=>isset($data['attribute_code']) ? $data['attribute_code'] : '',
                                'attribute_type' => isset($data['attribute_type']) ? $data['attribute_type'] : ''
                                    ), array(
                                'name' => 'required',
                                'attribute_code' => 'required',
                                'attribute_type' => 'required'
                                    ));
                    if($validator->fails())
                    {
                        $errorMessages = json_decode($validator->messages());
                        $errorMessage = '';
                        if(!empty($errorMessages))
                        {
                            foreach($errorMessages as $field => $message)
                            {
                                $errorMessage = implode(',', $message);
                            }
                        }
                        return Response::json([
                                'status' => false,
                                'message' => $errorMessage
                    ]);
                    }
                //validator
        $attributeCode= $data['attribute_code'];
        $checkDefaultAttribute=$this->_productattr->checkForDefaultAttribute($data['name']);
        $checkAttributeName=$this->_productattr->checkForAttributes($attributeCode);
        //return $checkAttributeName;
        if($checkDefaultAttribute || ($checkAttributeName && $checkAttributeName[0]->attribute_id!=$attribute_id )){
            return Response::json([
                    'status' => false,
                    'message' => 'Attribute already exists with this name.']);
        }
        if($checkDefaultAttribute==0){
        DB::table('attributes')
                ->where('attribute_id', $attribute_id)
                ->update(array(
                    'name' => Input::get('name'),
                    'input_type' => Input::get('input_type'),
                    'attribute_code'=>$attributeCode,
                    'default_value' => Input::get('default_value'),
                   'is_required' => Input::get('is_required'),
                    'validation' => Input::get('validation'),
                    'regexp' => Input::get('regexp'),
                    'lookup_id' => Input::get('lookup_id'),
                    'updated_by'=>$userId,
                    'attribute_type' => Input::get('attribute_type')));
        return Response::json([
                    'status' => true,
                    'message' => 'Sucessfully updated.'
        ]);
       }
    }

    public function deleteAttribute($attribute_id)
    {
        DB::table('attributes')->where('attribute_id', '=', $attribute_id)->delete();
        return Redirect::to('attribute');

    }

    public function saveAttributeGroup()
    {
        $attributeObj = new ProductAttributes();
        return $attributeObj->saveAttributeGroup(Input::all());
    }
    public function getAllAttributeGroup($cat_id)
    {
        $attributeObj = new ProductAttributes();
        return $attributeObj->getAllAttributeGroup($cat_id);
    }
  public function getAttributeListData()
    {
        $attributeObj = new ProductAttributes();
        $att_name=Input::get('attribute_set');
      
        return $attributeObj->getAttributeListData($att_name);
    }

    public function addAttributeGroup()
    {
        $attributeObj = new ProductAttributes();
        $data=Input::all();

        return $attributeObj->addAttributeGroupData($data);
    }

    public function saveAttributeSet()
    {
        $attributeObj = new ProductAttributes();
        return $attributeObj->saveAttributeSet(Input::all());
    }

    public function editAttributeGroup($attribute_group_id)
    {
        $editAttributeGroup = DB::Table('attributes_groups')->where('attribute_group_id', $attribute_group_id)->first();
        return Response::json($editAttributeGroup);
    }

    public function editAttributeSet($attribute_set_id)
    {
        $attribute_set_id=$this->roleRepo->decodeData($attribute_set_id);
        $editAttributeSet = DB::Table('attribute_sets')->where('attribute_set_id', $attribute_set_id)->first();
        $rs=DB::getQueryLog();
//        \Log::info(end($rs));
        return Response::json($editAttributeSet);
    }

    public function updateAttributeGroup($attribute_group_id)
    {
        DB::table('attributes_groups')
                ->where('attribute_group_id', $attribute_group_id)
                ->update(array(
                    'name' => Input::get('name'),
                    'category_id' => Input::get('category_id'),
                    'manufacturer_id' => Input::get('customer_id'),
        ));
        return Response::json([
                    'status' => true,
                    'message' => 'Sucessfully updated.'
        ]);
    }

    public function deleteAttributeGroup($attribute_group_id = null)
    {
        if(!$attribute_group_id)
        {
            $attribute_group_id = Input::get('attribute_group_id');
        }
        DB::table('attributes_groups')->where('attribute_group_id', '=', $attribute_group_id)->delete();
        //return Redirect::to('attribute');
        return Response::json([
            'status' => true,
            'message' => 'Sucessfully deleted.'
        ]);
    }
  public function deleteAttributeSet()
    {
        try
        {   
            DB::enableQueryLog();
            $data=Input::all();
            //return $data;
            $attribute_set_id = $data['attribute_set_id'];
            
       
            $defaultAttributes= DB::table('attributes')->where('attribute_type',2)->pluck('attribute_id')->all();
            $getAttData= DB::table('attribute_set_mapping')
                                ->where('attribute_set_id', '=', $attribute_set_id)
                                ->pluck('attribute_id')->all();
            
            $rs= array_diff($defaultAttributes,$getAttData);
           
            if(!empty($rs) && !empty($getAttData))
            {
               return "Please delect associated attributes.";
            }
            $checkCategeryMapToProduct= DB::table('product_attributes')
                                            ->where('attribute_set_id','=',$attribute_set_id)
                                            ->first();                                       
            if($checkCategeryMapToProduct)
            {
                return "This Attribute Set Category is associated with products.";
            
            }else
            {
                 /*here we have to delect att_set and attribute */
                DB::table('attribute_set_mapping')
                    ->where('attribute_set_id', '=', $attribute_set_id)
                    ->delete();

                DB::table('attribute_sets')->where('attribute_set_id', '=', $attribute_set_id)->delete();
                return "Sucessfully Deleted !!";                   
            }      
        }catch (ErrorException $ex) 
        {
            return Response::json([
                'status' => false,
                'message' => $ex->getMessage()
            ]);
        }        
    }
   
   
   
    public function getAllAttributes()
    {
        $attributeData = new ProductAttributes();
        return $attributeData->getAllAttributes();
    }

    public function delAttributeFromGroup($attribute_id = null, $attribute_set_id = null)
    {   
        $data=Input::all();
        $userId = Session::get('userId');
        
        if(!$attribute_id && !$attribute_set_id)
        {
            $attribute_id = $data['attribute_id'];
            $attribute_set_id = Input::get('attribute_set_id');
             $check_attributes_cat_rs= DB::table('product_attributes as pro_att')
                                           ->where('pro_att.attribute_id',$attribute_id)
                                           ->where('pro_att.attribute_set_id',$attribute_set_id)
                                            ->get()->all();                
            if($check_attributes_cat_rs)
            {
                    return "This Attributes are associated with products.";
            }else
            {
                $defaultAttributes= DB::table('attributes')->where('attribute_type',2)->pluck('attribute_id')->all();
                if(in_array($attribute_id, $defaultAttributes))
                {
                    return "This is default Attribute.";
                }
                else
                {
                     DB::table('attribute_set_mapping')->where('attribute_set_id', '=', $attribute_set_id)->where('attribute_id','=',$attribute_id)->delete();

                        $checkAtt= DB::table('attribute_set_mapping')->where('attribute_id','=',$attribute_id)->get()->all();

                    if(!$checkAtt)
                    {
                        Db::table('attribute_options')
                        ->where('attribute_id','=',$attribute_id)
                        ->delete();
                         Db::table('attributes')
                        ->where('attribute_id','=',$attribute_id)
                        ->delete();
                    }                  
                    return 1;
                }
            }    
        }
    }
    public function getCustomers()
    {
        $custArr = array();
        $finalCustArr = array();
        $customer_details = DB::Table('attributes')->get()->all();
        $cust = json_decode(json_encode($customer_details), true);
        $allowEditAttribute = $this->roleRepo->checkPermissionByFeatureCode('ATT003');
        $allowDeleteAttribute = $this->roleRepo->checkPermissionByFeatureCode('ATT004');
        foreach ($cust as $value)
        {
            if ($value['is_required'] == 1)
            {
                $status1 = 'Yes';
            } else
            {
                $status1 = 'No';
            }

            if ($value['attribute_type'] == 1)
            {
                $status2 = 'Static';
            } elseif ($value['attribute_type'] == 2)
            {
                $status2 = 'Dynamic';
            } else
            {
                $status2 = 'Binding';
            }
            $custArr['attribute_id'] = $value['attribute_id'];
            $custArr['name'] = $value['name'];
            $custArr['text'] = $value['text'];
            $custArr['input_type'] = $value['input_type'];
            $custArr['default_value'] = $value['default_value'];
            $custArr['is_required'] = $status1;
            $custArr['validation'] = $value['validation'];
            $custArr['regexp'] = $value['regexp'];
            $custArr['lookup_id'] = $value['lookup_id'];
            $custArr['attribute_type'] = $status2;
            $custArr['actions'] = '';
            if($allowEditAttribute)
            {
                $custArr['actions'] = $custArr['actions'] . '<span style="padding-left:5px;" ><a data-href="product/editAttribute/' . $value['attribute_id'] . '" data-toggle="modal" data-target="#basicvalCodeModal1"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a></span>';
            }
            if($allowDeleteAttribute)
            {
                $custArr['actions'] = $custArr['actions'] . '<span style="padding-left:5px;" ><a onclick="deleteEntityType(' . $value['attribute_id'] . ')"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a></span><span style="padding-left:50px;" ></span>';
            }
            $finalCustArr[] = $custArr;
        }
        return json_encode($finalCustArr);
    }
    public function seleteAttributes()
    {
         $result = DB::table('attribute_sets')
                        ->get(array('attribute_sets.attribute_set_id', 'attribute_sets.attribute_set_name'))->all();
        
      return json_encode($result);
    }
    
    /*
     * Function : vr_enabled
     * Purpose : To set or unset the varient for attribute
     * Parameter : $attrId -- attribute Id
     *             $attrSetId -- attribute set Id 
     */
    public function vr_third_enabled($attrId,$attrSetId,$status)
    {
        $attributeObj   =   new ProductAttributes();
        $vRStatus       =   $attributeObj->vr_third_enabled($attrId,$attrSetId,$status); 
        return $vRStatus;
    }
    /*
     * Function : vr_enabled
     * Purpose : To set or unset the varient for attribute
     * Parameter : $attrId -- attribute Id
     *             $attrSetId -- attribute set Id 
     */
    public function vr_enabled($attrId,$attrSetId,$status)
    {
        $attributeObj   =   new ProductAttributes();
        $vRStatus       =   $attributeObj->is_vr_enabled($attrId,$attrSetId,$status); 
        return $vRStatus;
    }
    /*
     * Function : vr_secondary_enabled
     * Purpose : To set or unset the secondary varient for attribute
     * Parameter : $attrId -- attribute Id
     *             $attrSetId -- attribute set Id 
     */
    public function vr_secondary_enabled($attrId,$attrSetId,$status)
    {
        $attributeObj   =   new ProductAttributes();
        $vRStatus       =   $attributeObj->is_vr_secondary_enabled($attrId,$attrSetId,$status); 
        return $vRStatus;
    }
    
    public function filter_enabled($attrId,$attrSetId,$status)
    {
         $attributeObj   =   new ProductAttributes();
        $vRStatus       =   $attributeObj->is_filter_enabled($attrId,$attrSetId,$status); 
        return $vRStatus;
    }
    public function getAttributeName($id)
    {
       
        $attributeSetData = DB::Table('attribute_sets')
                            ->where('attribute_set_id',$id)
                            ->select('attribute_set_name')
                            ->first();
            return $attributeSetData->attribute_set_name;
    }
    public function attributes()
    {
        
       DB::enableQueryLog();
        $addAttributesets = true;
        $addAttributegroups = true;
        //print_r('here are the attributes');exit;

        $data = Input::all();

        $postMethod = 0;
        //echo 'manufacturerId => '.$manufacturerId;die;
        parent::Breadcrumbs(array('Home' => '/', 'Product Templates' => '#'));
      
            $attributeSetData = DB::Table('attribute_sets')
                ->select('attribute_set_id', 'attribute_set_name')
                ->get()->all();

            $ag = DB::Table('attributes_groups')
                ->select('attribute_group_id', 'name','category_id')
                ->get()->all();
        
        
        $am = DB::Table('attribute_mapping')
                ->select('id', 'attribute_map_id', 'attribute_id', 'value', 'location_id')
                ->get()->all();

        $data = DB::Table('attributes')->get()->all();

            $cat = DB::Table('categories')->get()->all();
        $parent_Cat = DB::table('categories')->where('categories.parent_id','!=',0)->where('is_product_class','=',0)->get()->all();                
        $product_class= DB::table('categories')->where('categories.parent_id','!=',0)->where('is_product_class','=',1)->get()->all();        
        $attrsets = array();
                //Attributes Data
      
        //Attributes Data
        $userType = 1;//Session::get('userId');
   
        /* $custType=json_encode($custType);*/
        $manufactuerArray = array();
        
        $attribute_sets = $this->getAttributeSets();
        //print_r('wings');exit;
            return View::make('Attributes::attribute',['attributeSetData'=> $attributeSetData,'data'=> $data,'am'=> $am,'ag'=> $ag,'cat'=> $cat,'parent_id'=>$parent_Cat,'product_class'=>$product_class,'attribute_sets'=> $attribute_sets,'manufacturerData'=> $manufactuerArray,'addAttributesets'=>$addAttributesets,'addAttributegroups'=>$addAttributegroups]);    
        
    

    
    }

    /* Attributes actions ends */    
    /* Attributes actions ends */  
    public function addSelectedAttributes($set)
    {
         $setAttributes=DB::table('attributes')->select('attribute_id','name')->get()->all();
         return json_encode($setAttributes);
    }  
    //GetAttributes for User
     public function customerAttributesAll($set)
    {

            DB::enableQueryLog();

             $set=$this->roleRepo->decodeData($set);
             $completeResult = array();

                                 
        $result=DB::table('attributes')->get()->all();

            $allAttributes=array();
            foreach($result as $res)
            {
                $id='_'.$res->attribute_id;
                $allAttributes[$id] = $res->name;

            }
            $completeResult['default'] = $result;
             $defaultAttributes= DB::table('attributes')->where('attribute_type',2)->pluck('attribute_id')->all();
            
           
            $setAttributes = DB::table('attribute_set_mapping as a')
                            ->join('attributes as b','a.attribute_id','=','b.attribute_id')
                            ->select(DB::raw("concat('_',b.attribute_id) attribute_id,b.name"))
                            ->where('a.attribute_set_id',$set)
                            ->whereNotIn('a.attribute_id',$defaultAttributes)
                            ->orderBy('a.sort_order')
                            ->get()->all();
         
            $selectAttributes= array();
            foreach($setAttributes as $setAttribute)
            {
                $selectAttributes[$setAttribute->attribute_id] = $setAttribute->name;
            }
            /*echo '<pre>';
            print_r($selectAttributes);die;*/
            $completeResult['selectedAttr'] = $selectAttributes;
            
            $unselected=array_diff($allAttributes, $selectAttributes);
            $completeResult['unselected'] = $unselected;
            unset($completeResult['default']);  
    return json_encode($completeResult);
    //return $completeResult;
    }
    //GetAttributes for User
    public  function updateattributeset(){
    $attributeObj = new ProductAttributes();

    return $attributeObj->updateattributeset(Input::all());
    }
    public function checkSetAvailability()
    {
      $data=Input::all();
      $attribute_set_id=Input::get('attribute_set_id');
      $attribute = DB::table('attribute_sets');             
      if($attribute_set_id){
        $attrName=$attribute->where('attribute_sets.attribute_set_name',$data['attribute_set_name'])
                            ->where('attribute_set_id','!=',$attribute_set_id)
                            ->pluck('attribute_set_name')->all();             
      } 
      else{
        $attrName=$attribute->where('attribute_sets.attribute_set_name',$data['attribute_set']['attribute_set_name'])
                                ->pluck('attribute_set_name')->all(); 

      }
      if(empty($attrName))
           {
            return json_encode([ "valid" => true ]);
           }                     
          else 
          {
            return json_encode([ "valid" => false ]);
          }      
    }
    public function checkGroupAvailability()
    {
      $data=Input::all();
       $attribute = DB::table('attributes_groups')
            ->where('attributes_groups.name',$data['attribute_group']['name'])
            ->get()->all();     
      if(empty($attribute))
           {
            return json_encode([ "valid" => true ]);
           }                     
          else 
          {
            return json_encode([ "valid" => false ]);
          }      
    }  
    public function checkAttributeAvailability()
    {
      $data=Input::all();
      $attribute_id=Input::get('attribute_id');
    if($attribute_id){
            $defaultAttr= DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    ->whereIn('attr.name',array($data['name']))
                    ->where('attr.attribute_id','!=',$attribute_id)
                    ->where('aset.attribute_set_name','=', 'Default')->get()->all();
            //return $defaultAttr;
            if(empty($defaultAttr)){

                    $attributeSpecific = DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    ->where('attr.name',$data['name'])
                    ->where('attr.attribute_id','!=',$attribute_id)
                   ->get()->all();
            if(empty($attributeSpecific))
               {
                return json_encode([ "valid" => true ]);
               }else{
                return json_encode([ "valid" => false ]);
                }                   
            }else{
                return json_encode([ "valid" => false ]);
            }
    }else{  
            $defaultAttr= DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    ->whereIn('attr.name',array($data['name']))
                    ->where('aset.attribute_set_name','=', 'Default')->get()->all();
            //return $defaultAttr;
            if(empty($defaultAttr)){
            $attributeSpecific = DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    ->where('attr.name',$data['name'])->get()->all();
              if(empty($attributeSpecific))
                   {
                    return json_encode([ "valid" => true ]);
                   }                     
                  else 
                  {
                    return json_encode([ "valid" => false ]);
                  }                   
            }else{
                return json_encode([ "valid" => false ]);
            }
       }
   
    } 
    public function checkDefaultAttributeAvailability()
    {
      $data=Input::all();
            $defaultAttr= DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    ->whereIn('attr.name',array($data['name']))
                    ->where('aset.attribute_set_name','=', 'Default')->get()->all();
            if(empty($defaultAttr)){
                return json_encode([ "valid" => true ]);
               }else{
                return json_encode([ "valid" => false ]);
                }                   
           
    } 
    public function checkAttrAvailability()
    {
      $data=Input::all();
      $attribute_id=Input::get('attribute_id');
      if($attribute_id){
          $attributeSpecific = DB::table('attributes')
                              ->where('attribute_code',$data['attribute_code'])
                              ->where('attribute_id','!=',$data['attribute_id'])->get()->all();
            if(empty($attributeSpecific)){
                return json_encode([ "valid" => true ]);
               }else{
                return json_encode([ "valid" => false ]);
                } 
        }else{
            $attributeSpecific = DB::table('attributes')
                              ->where('attribute_code',$data['attribute_code'])->get()->all();
            if(empty($attributeSpecific)){
                return json_encode([ "valid" => true ]);
               }else{
                return json_encode([ "valid" => false ]);
                } 
        }                  
           
    }              
    
    public function assignGroups()
    {
        $attributeObj = new Products\ProductAttributes();
        $data = Input::all();
        return $attributeObj->assignGroups($data);
    }
    public function getAssignGroupDetails($attribute_set_id)
    {
        $AssignGroupDetails=DB::table('product_attributesets')
                            ->join('locations','locations.location_id','=','product_attributesets.location_id')
                            ->join('product_groups','product_groups.group_id','=','product_attributesets.product_group_id')
                            ->where('product_attributesets.attribute_set_id',$attribute_set_id)->get(array('locations.location_name as location_name','product_groups.name as productgroup','product_attributesets.product_group_id as product_group_id','product_attributesets.location_id as location_id'))->all();
                       return $AssignGroupDetails;
    }   
    public function getoptions($attribute_id)
    {
        $getoptions=DB::table('attribute_options')
                            ->where('attribute_options.attribute_id',$attribute_id)->get(array('attribute_id','option_value','option_name','sort_order'))->all();
        return $getoptions;
    }    
    public function searchAttributes()
    {
       try
       {
            $data=Input::get();
            $attribute_set_id = $this->roleRepo->decodeData($data['attribute_set_id']);
            $attribute_id = $this->roleRepo->decodeData($data['attribute_id']);
            if($data['flag'] == 0)
            {
                $search = 0;
            }elseif($data['flag'] == 1)
            {
                $search = 1;
            }
           DB::table('attribute_set_mapping')
            ->where(array('attribute_set_id'=>$attribute_set_id,'attribute_id'=>$attribute_id))
            ->update(array('is_searchable'=>$search));
            return 1;
       }
        catch (ErrorException $ex) {
            return Response::json([
                'status' => false,
                'message' => $ex->getMessage()
            ]);
        } 
    }
    
    public function getAllStores()
    {
        try
        {
            $channelDetails = DB::table('channel')->select('channnel_name', 'channel_logo')->get()->all();
            return ($channelDetails);
        } catch (\ErrorException $ex)
        {
            Log::info($ex->getMessage());
        }
    }
    public function addAttributesFromExcel() {
        try {
            ini_set('max_execution_time', 1200);
            $data = Input::all();
            $attribute_set_id = $data['attribute_sets'];            
            $message = array();
            if (Input::hasFile('import_file')) {
                $path = Input::file('import_file')->getRealPath();
                $headerRowNumber = 2;
                Config::set('excel.import.startRow', $headerRowNumber);
                Config::set('excel.import.heading', 'slugged');
                $prod_data = Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                    })->get()->all();
                $data = json_decode(json_encode($prod_data));
                $errorMessage = '';
                $culumns = array('group_name','attribute_name','input_type','default_value','is_required','validation','regexp','lookupid','attribute_type');
                $set_map_ids=array();
                $is_required_arr = array('Yes'=>1,'No'=>0);
                $input_type_arr = array('Check Box'=>'checkbox',
                                        'Radio'=>'radio',
                                        'Text'=>'text',
                                        'Text Area'=>'textarea',
                                        'Date'=>'date',
                                        'Date Time'=>'datetime',
                                        'Select Drop Down'=>'select',
                                        'Multi Select Drop Down'=>'multiselect',
                                        'Single Select Drop Down'=>'sdropdown'
                                    );
                $attribute_type_arr = array(
                                            'Static'=>1,
                                            'Dynamic'=>2,
                                            'Binding'=>3,
                                            'TP'=>4,
                                            'QC'=>5
                                        );
                foreach($data as $key=>$values){
                    if($values->attribute_group!='' && $values->attribute_name!='' && $values->input_type!='' && $values->attribute_type!=''){
                        $values->attribute_code=  str_replace(array(')','(','\'','\"','-',' '), array('','','','','','_'), $values->attribute_name);
                        $attr_id =  $this->checkAttrExist($values->attribute_code);
                            $attribute_set_map=array();
                            if($attr_id!=''){
                                $attr_set_map_id=DB::table('attribute_set_mapping')
                                        ->where('attribute_set_id',$attribute_set_id)
                                        ->where('attribute_id',$attr_id)
                                        ->pluck('id')->all();
                                $attr_set_map_id = (isset($attr_set_map_id[0]))?$attr_set_map_id[0]:'';
                                if($attr_set_map_id==''){
                                    $attribute_set_map=array('attribute_set_id' => $attribute_set_id,
                                                             'attribute_id' => $attr_id);                                   
                                    $attr_set_map_id=DB::table('attribute_set_mapping')->insertGetId($attribute_set_map);
                                }
                            }else{
                                $attribute_group_id = DB::table('attributes_groups')
                                            ->where('attributes_groups.name',$values->attribute_group)
                                            ->pluck('attribute_group_id')->all();
                                $attribute_group_id = (isset($attribute_group_id[0]))?$attribute_group_id[0]:'';
                                if($attribute_group_id==''){
                                    $attr_group=array('name'=>$values->attribute_group);
                                    $attribute_group_id=DB::table('attributes_groups')->insertGetId($attr_group);
                                }
                                $attributes=array('attribute_code'=> $values->attribute_code,
                                                   'name'=> $values->attribute_name,
                                                   'input_type'=> $input_type_arr[$values->input_type],
                                                   'default_value'=> $values->default_value,
                                                   'is_required'=> $is_required_arr[$values->is_required],
                                                   'validation'=> $values->validation,
                                                   'regexp'=> $values->regexp,
                                                   'lookup_id'=> $values->lookupid,
                                                   'attribute_type'=> $attribute_type_arr[$values->attribute_type],                                                   
                                                );
                                $attr_id=DB::table('attributes')->insertGetId($attributes);                                
                                $attribute_set_map=array('attribute_set_id' => $attribute_set_id,
                                                        'attribute_id' => $attr_id,'attribute_group_id'=> $attribute_group_id);
                                $attr_set_map_id=DB::table('attribute_set_mapping')->insertGetId($attribute_set_map);                                
                            }
                            $set_map_ids[] = $attr_set_map_id;
                    }else{
                        $set_map_ids[] = 'missing mondatory fields';
                    }
                }
                return $set_map_ids;
            }
        } 
        catch (ErrorException $ex) {
            Log::info($ex->getMessage() . ' -- ' . $ex->getTraceAsString());
        }
    }
    public function checkAttrExist($attribute_code='')
    {
      $attribute_id = DB::table('attributes')
                ->where('attribute_code',$attribute_code)
                ->pluck('attribute_id')->all();
      $attribute_id=(isset($attribute_id[0]))?$attribute_id[0]:'';
      return $attribute_id;
    }

    //HierarchicalGrid Display function

    public function displayNewGridLayout(){

          DB::enableQueryLog();
        $addAttributesets = true;
        $addAttributegroups = true;
        //print_r('here are the attributes');exit;

        $data = Input::all();

        $postMethod = 0;
        //echo 'manufacturerId => '.$manufacturerId;die;
        parent::Breadcrumbs(array('Home' => '/', 'Product Templates' => '#'));
      
            $attributeSetData = DB::Table('attribute_sets')
                ->select('attribute_set_id', 'attribute_set_name')
                ->get()->all();

            $ag = DB::Table('attributes_groups')
                ->select('attribute_group_id', 'name','category_id')
                ->get()->all();
        
        
        $am = DB::Table('attribute_mapping')
                ->select('id', 'attribute_map_id', 'attribute_id', 'value', 'location_id')
                ->get()->all();

        $data = DB::Table('attributes')->get()->all();

            $cat = DB::Table('categories')->get()->all();
        $parent_Cat = DB::table('categories')->where('categories.parent_id','!=',0)->where('is_product_class','=',0)->get()->all();                
        $product_class= DB::table('categories')->where('categories.parent_id','!=',0)->where('is_product_class','=',1)->get()->all();        
        $attrsets = array();
                //Attributes Data
      
        //Attributes Data
        $userType = 1;//Session::get('userId');
   
        /* $custType=json_encode($custType);*/
        $manufactuerArray = array();
        
        $attribute_sets = $this->getAttributeSets();
        //print_r('wings');exit;
            return View::make('Attributes::attributeHierarchicalGridDisplay',['attributeSetData'=> $attributeSetData,'data'=> $data,'am'=> $am,'ag'=> $ag,'cat'=> $cat,'parent_id'=>$parent_Cat,'product_class'=>$product_class,'attribute_sets'=> $attribute_sets,'manufacturerData'=> $manufactuerArray,'addAttributesets'=>$addAttributesets,'addAttributegroups'=>$addAttributegroups]);   
        //return View::make('Attributes::attributeHierarchicalGridDisplay');
    }

    public function getAllAttributeSets(Request $request) {
        $request_input = $request->input();

        $explode = explode('/', $request->input('path'));
        /*$ZoneId = $explode[1];
        $explodefor_stateId = explode(":", $ZoneId);
        $STATEID = $explodefor_stateId[1];
        $countryID = $request->input('country_id');*/
        $orderby_array = "";
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $filter_by = "";
        if ($request->input('$orderby')) {             //checking for sorting
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
            }
            $orderby_array = $order_by . " " . $order_by_type;
        }

        if (isset($request_input['$filter'])) {
            $filter_explode = explode(' and ', $request_input['$filter']);
            foreach ($filter_explode as $filter_each) {
                $filter_each_explode = explode(' ', $filter_each);
                $length = count($filter_each_explode);
                $filter_query_field = '';
                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter_each_explode[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter_each_explode[$length - 2];
                    $filter_query_value = $filter_each_explode[$length - 1];
                } else {
                    $filter_query_field = $filter_each_explode[0];
                    $filter_query_operator = $filter_each_explode[1];
                    $filter_query_value = $filter_each_explode[2];
                }
                $filter_query_field_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                    //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                    if ($filter_query_field_substr == 'startsw') {
                        $filter_query_field_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = $filter_query_field_value_array[1] . '%';

                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $starts_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_by[] = $starts_with_value;
                            } else {
                                $starts_with_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'endswit') {
                        $filter_query_field_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = '%' . $filter_query_field_value_array[1];

                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $ends_with_value = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_by[] = $ends_with_value;
                            } else {
                                $ends_with_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'tolower') {
                        $filter_query_value_array = explode("'", $filter_query_value);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = $filter_query_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = ' = ';
                        } else {
                            $like = ' != ';
                        }
                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $to_lower_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                $filter_by[] = $to_lower_value;
                            } else {
                                $to_lower_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'indexof') {
                        $filter_query_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_query_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = ' like ';
                        } else {
                            $like = ' not like ';
                        }
                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $indexof_value = $this->grid_field_db_match[$key] . $like . $filter_value;
                                $filter_by[] = $indexof_value;
                            } else {
                                $indexof_value = "";
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
                        case 'eq' :
                            $filter_operator = ' = ';
                            break;

                        case 'ne':
                            $filter_operator = ' != ';
                            break;

                        case 'gt' :
                            $filter_operator = ' > ';
                            break;

                        case 'lt' :
                            $filter_operator = ' < ';
                            break;

                        case 'ge' :
                            $filter_operator = ' >= ';
                            break;

                        case 'le' :
                            $filter_operator = ' <= ';
                            break;
                    }

                    if (isset($this->grid_field_db_match[$filter_query_field])) {
                        //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                }
            }
        }

        $data = $this->_attributesSet->getAllData($page, $pageSize, $orderby_array, $filter_by);
        
        $decodedData = $data['result'];
        
        foreach($decodedData as $key=>$value){

            $decodedData[$key]['actions'] = '<a data-href="product/saveAttributeGroup/" data-toggle="modal" onclick="getAttributeGroupName('.$value['attribute_set_id'].');" data-target="#basicvalCodeModal"> <i class="fa fa-plus"></i> </a><span style="padding-left:20px;" ></span><a data-href="/product/editattributeset/' .$this->roleRepo->encodeData($value['attribute_set_id']). '" onclick="getAttributeSetName(\''.$value['attribute_set_name'].'\','.$value['category_id'].','.$value['attribute_set_id'].');"  data-attributeId="'.$this->roleRepo->encodeData($value['attribute_set_id']).'" data-toggle="modal" data-target="#editAttributeSet"><span ><i class="fa fa-pencil" data-id="'.$value['attribute_set_name'].'"></i></span></a><span style="padding-left:20px;" ></span><a onclick = "deleteAttrSet('.$value['attribute_set_id'].')"><span><i class="fa fa-trash-o"></i></span></a>';

            //die;
        }
        $GridData = array();
        //Collecting accesses for Buttons
        /*$taxClassEditAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM003');
        $taxClassDeleteAccess = $this->_roleRepo->checkPermissionByFeatureCode('TM004');
        foreach ($decodedData as $key => $value) {
            $decodedData[$key]['coutryname'] = "India";

            $decodedData[$key]['action'] = '<code style="cursor: pointer;">';
            //check Edit access
            if($taxClassEditAccess == 1){
                $decodedData[$key]['action'] .= '<a data-type="edit" data-id="' . @$value['tax_class_id'] . '" data-toggle="modal" data-target="#createrule-modal"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            }
            else{
                $decodedData[$key]['action'] .= '';
            }

            //check Delete access
            if($taxClassDeleteAccess == 1){
                $decodedData[$key]['action'] .= 
            '<span  style="padding-left:15px;"><i  class="fa fa-times" onclick="deleteVal(' . @$value['tax_class_id'] . ');"></i></span>';
            }
            else{
                $decodedData[$key]['action'] .= '';
            }
            $decodedData[$key]['action'] .= '</code>';
            
            
            $decodedData[$key]['mappingcount'] = "<span align='center'>".$this->_taxmap->taxCountBasedonTaxClassId($value['tax_class_id'])."</span>";
        }*/

        echo json_encode(array('results' => $decodedData, 'TotalRecordsCount' => $data['count']));
    }

    //2nd Level Grid Function
    public function getAttributesDetails(Request $request) {
        $request_input = $request->input();
        //print_r($request->input('path'));die;
        $attribute_set_id = explode(':', $request->input('path'));
        //print_r($explode); die;
        /*$ZoneId = $explode[1];
        $explodefor_stateId = explode(":", $ZoneId);
        $STATEID = $explodefor_stateId[1];
        $countryID = $request->input('country_id');*/
        $orderby_array = "";
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $filter_by = "";
        if ($request->input('$orderby')) {             //checking for sorting
            $order = explode(' ', $request->input('$orderby'));
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->childGrid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->childGrid_field_db_match[$order_query_field];
            }
            $orderby_array = $order_by . " " . $order_by_type;
        }

        if (isset($request_input['$filter'])) {
            $filter_explode = explode(' and ', $request_input['$filter']);
            foreach ($filter_explode as $filter_each) {
                $filter_each_explode = explode(' ', $filter_each);
                $length = count($filter_each_explode);
                $filter_query_field = '';
                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter_each_explode[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter_each_explode[$length - 2];
                    $filter_query_value = $filter_each_explode[$length - 1];
                } else {
                    $filter_query_field = $filter_each_explode[0];
                    $filter_query_operator = $filter_each_explode[1];
                    $filter_query_value = $filter_each_explode[2];
                }
                $filter_query_field_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                    //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                    if ($filter_query_field_substr == 'startsw') {
                        $filter_query_field_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = $filter_query_field_value_array[1] . '%';

                        foreach ($this->childGrid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $starts_with_value = $this->childGrid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_by[] = $starts_with_value;
                            } else {
                                $starts_with_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'endswit') {
                        $filter_query_field_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = '%' . $filter_query_field_value_array[1];

                        foreach ($this->childGrid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $ends_with_value = $this->childGrid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_by[] = $ends_with_value;
                            } else {
                                $ends_with_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'tolower') {
                        $filter_query_value_array = explode("'", $filter_query_value);
                        //extracting the input filter value between single quotes, example: 'value'

                        $filter_value = $filter_query_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = ' = ';
                        } else {
                            $like = ' != ';
                        }
                        foreach ($this->childGrid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $to_lower_value = $this->childGrid_field_db_match[$key] . $like . $filter_value;
                                $filter_by[] = $to_lower_value;
                            } else {
                                $to_lower_value = "";
                            }
                        }
                    }

                    if ($filter_query_field_substr == 'indexof') {
                        $filter_query_value_array = explode("'", $filter_query_field);
                        //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_query_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = ' like ';
                        } else {
                            $like = ' not like ';
                        }
                        foreach ($this->childGrid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                //getting the filter field name
                                $indexof_value = $this->childGrid_field_db_match[$key] . $like . $filter_value;
                                $filter_by[] = $indexof_value;
                            } else {
                                $indexof_value = "";
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
                        case 'eq' :
                            $filter_operator = ' = ';
                            break;

                        case 'ne':
                            $filter_operator = ' != ';
                            break;

                        case 'gt' :
                            $filter_operator = ' > ';
                            break;

                        case 'lt' :
                            $filter_operator = ' < ';
                            break;

                        case 'ge' :
                            $filter_operator = ' >= ';
                            break;

                        case 'le' :
                            $filter_operator = ' <= ';
                            break;
                    }

                    if (isset($this->childGrid_field_db_match[$filter_query_field])) {
                        //getting appropriate table field based on grid field
                        $filter_field = $this->childGrid_field_db_match[$filter_query_field];
                    }

                    $filter_by[] = $filter_field . $filter_operator . $filter_query_value;
                }
            }
        }

        $data = $this->_attributeSetMap->getDependentData($page, $pageSize, $orderby_array, $filter_by, $attribute_set_id[1]);
        $decodedData = $data['result'];//$decodedData = json_decode($data['result'], true);
        foreach($decodedData as $key=>$value)
        {
            $vr_enabled=($value['is_varient']==1)?"checked='true'":"check='false'";
            $secondary_vr_enabled=($value['is_secondary_varient']==1)?"checked='true'":"check='false'";
            $third_vr_enabled= ($value['is_third_varient']==1)?"checked='true'":"check='false'";
            $filter_checke_status=($value['is_filterable'] == 1)?"checked='true'":"check='false'";
             $search_btn_status=($value['is_searchable'] == 1)?"checked='true'":"check='false'";
            $decodedData[$key]['is_varient']="<label class='switch'><input class='switch-input vr_status".$value['attribute_id']."' onclick='vr_enabled(".$value['attribute_id'].",".$value['attribute_set_id'].");'  type='checkbox' ".$vr_enabled." id='vr_enabled_id".$value['attribute_id']."'/><span class='switch-label' data-on='Yes' data-off='No'></span><span class='switch-handle'></span></label>";

            $decodedData[$key]['is_secondary_varient']="<label class='switch'><input class='switch-input vr_secondary_status".$value['attribute_id']."' onclick='vr_secondary_enabled(".$value['attribute_id'].",".$value['attribute_set_id'].");'  type='checkbox'".$secondary_vr_enabled." id='vr_secondary_enabled_id".$value['attribute_id']."'/><span class='switch-label' data-on='Yes' data-off='No'></span><span class='switch-handle'></span></label>";

            $decodedData[$key]['is_third_varient']="<label class='switch'><input class='switch-input vr_third_status".$value['attribute_id']."' onclick='vr_third_enabled(".$value['attribute_id'].",".$value['attribute_set_id'].");'  type='checkbox'".$third_vr_enabled." id='vr_third_enabled_id'/><span class='switch-label' data-on='Yes' data-off='No'></span><span class='switch-handle'></span></label>";

             $decodedData[$key]['is_searchable']='<label class="switch"><input class="switch-input is_searchble'.$value['attribute_id'].'" id="is_searchble'.$value["attribute_id"].'" onclick ="switchAttributeSearchable('.$value["attribute_id"].', '.$value["attribute_set_id"].',0)" '.$search_btn_status.' type="checkbox"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';

            $decodedData[$key]['is_filterable']="<label class='switch'><input class='switch-input filter_status".$value['attribute_id']."' onclick='checkIsFilterble(".$value["attribute_id"].",".$value["attribute_set_id"].",0)' ".$filter_checke_status." type='checkbox'/><span class='switch-label' data-on='Yes' data-off='No'></span><span class='switch-handle'></span></label>";

             $decodedData[$key]['actions']='<a data-href="/product/editattribute/'.$this->roleRepo->encodeData($value['attribute_id']).'/'.$value['attribute_set_id'].'" data-toggle="modal" onclick="getAttributeGroupName('.$value['attribute_set_id'].');"  data-target="#basicvalCodeModal1"><i class="fa fa-pencil"></i></a><span style="padding-left:10px;"></span>&nbsp;&nbsp;<a onclick ="delAttributeFromAttSet('.$value['attribute_id'].','.$value['attribute_set_id'].')"><i class="fa fa-trash-o"></i></a><span style="padding-left:20px;"></span>';
        }
        $GridData = array();
        echo json_encode(array('resultData' => $decodedData, 'TotalRecordsCount' => $data['count']));
    }
}