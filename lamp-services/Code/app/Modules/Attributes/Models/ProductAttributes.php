<?php

namespace App\Modules\Attributes\Models;
use \Log;
use \DB;
use \Session;
use \Response;
use App\Central\Repositories\RoleRepo;
use Illuminate\Support\Facades\Cache;

class ProductAttributes extends \Eloquent
{

    protected $table = 'product_attributes'; // table name
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function __construct()
    {
        $this->roleRepo = new RoleRepo;
    }

    // model function to store product data to database

    public function saveProductAttributes($data)
    {
        
        try
        {
            
            if(!isset($data['product']['attribute_set_id']) || empty($data['product']['attribute_set_id']))
            {
                if(isset($data['attributes']))
                {
                    
                    foreach ($data['attributes'] as $attributeCode => $attributeValue)
                    {
                        
                        $attributeId = DB::table('attributes')->where('attribute_code', $attributeCode)->pluck('attribute_id')->all();
                        
                        if(!empty($attributeId))
                        {

                            $updateArray['product_id'] = $data['product_id']; 
                            $updateArray['attribute_id'] = $attributeId; 
                            $updateArray['value'] = $attributeValue; 
                            $productAttrId = DB::table('product_attributes')->where(array('product_id' => $data['product_id'], 'attribute_id' => $attributeId))->pluck('id')->all();
                            if(!empty($productAttrId))
                            {
                                DB::table('product_attributes')->where(array('product_id' => $data['product_id'], 'attribute_id' => $attributeId))->update($updateArray);
                            }else{
                                DB::table('product_attributes')->insert($updateArray);
                            }    
                        }
                        else
                        {
                            
                            $attributeId = DB::table('attributes')->where('name','LIKE','%'.$attributeCode.'%')->pluck('attribute_id')->all();
                            
                            if(!empty($attributeId))
                            {

                            $updateArray['product_id'] = $data['product_id']; 
                            $updateArray['attribute_id'] = $attributeId; 
                            $updateArray['value'] = $attributeValue;
                                $productAttrId = DB::table('product_attributes')
                                        ->where(array('product_id' => $data['product_id'], 'attribute_id' => $attributeId))->pluck('id')->all();
                                $last = DB::getQueryLog();
                                //\Log::info(end($last));
                                if(!empty($productAttrId))
                                {
                                    DB::table('product_attributes')
                                            ->where(array('product_id' => $data['product_id'], 'attribute_id' => $attributeId))
                                            ->update($updateArray);
                                }else{
                            DB::table('product_attributes')->insert($updateArray);
                                }
//                                DB::table('product_attributes')->insert($updateArray);
                            $last = DB::getQueryLog();
                            //\Log::info(end($last));
                            }
                            
                        }
                    }
                }
                
                return;
            }

            $attributeList = $this->getAttributesListById($data['product']['attribute_set_id']);
            
            $productData = $data['attributes'];
            $productId = isset($data['product_id']) ? $data['product_id'] : 0;
            $manufacturerId = isset($data['product']['manufacturer_id']) ? $data['product']['manufacturer_id'] : 0;
            
            if (!empty($productId))
            {
                foreach ($attributeList as $attribute)
                {
                    $productAttributeValue = isset($productData[$attribute->attribute_code]) ? $productData[$attribute->attribute_code] : ' ';
                    $attributeProductData = DB::table('product_attributes')
                            ->where('attribute_id', $attribute->attribute_id)
                            ->where('product_id', $productId)
                            ->first(array('id', 'value'));                    
                    if($attribute->input_type == 'file' && !empty($productAttributeValue) && $productAttributeValue != '' && $productAttributeValue != ' ')
                    {
                        $folderName = $manufacturerId.'/instructions/';
                        $filePath = $this->upload($folderName, $productAttributeValue);
                        $productAttributeValue = $filePath;
                    }
                    $updateData['value'] = $productAttributeValue;
                    if(gettype($attributeProductData) == 'object')
                    {
                        if($productAttributeValue != '' && $productAttributeValue != ' ')
                        {
                            DB::table('product_attributes')
                                ->where('id', $attributeProductData->id)
                                ->update($updateData);
                        }
                    }else{
                        if($productId && $attribute->attribute_id)
                        {
                            DB::table('product_attributes')->insert(['product_id' => $productId, 'attribute_id' => $attribute->attribute_id, 'value' => $productAttributeValue]);
                        }
                    }
                }
                return true;
            } else
            {
                return false;
            }
        } catch (Exception $ex)
        {
            return $ex;
        }
    }
    function is_filter_enabled($attrId,$attrSetId,$status)
    {

         $rs =   DB::table('attribute_set_mapping')
                ->where('attribute_set_id', $attrSetId)
                ->where('attribute_id',$attrId)
                ->update(array('is_filterable'=>$status));
             return 1;
        /*$isvarientSet   =   "";
        if($status == 1){
            $isvarientSet   =   DB::table('attribute_set_mapping')
                                ->select('is_filterable')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('is_filterable', 1)
                                ->count();
       }
       if($isvarientSet >0){
            $rs =   DB::table('attribute_set_mapping')
                ->where('attribute_set_id', $attrSetId)
                    ->where('attribute_id',$attrId)
                    ->update(array('is_filterable'=>$status));
            return 1;
        }else{
            return 0;
        }*/
    }
    
    function vr_third_enabled($attrId,$attrSetId,$status)
    {
        DB::enablequerylog();
         $isvarientSet   =   "";
        $isSecondaryVarientSet="";
        $isThirdVarientSetChecking="";
        if($status == 1){
            $isSecondaryVarientSet   = DB::table('attribute_set_mapping')
                                ->select('is_third_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('is_third_varient', 1)
                                ->count();
           
       }
       if($isSecondaryVarientSet == 0){
             $isvarientSetChecking   = DB::table('attribute_set_mapping')
                                ->select('is_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('attribute_id',$attrId)
                                ->where('is_varient', 1)
                                ->count();
             $isSecondVarientSetChecking   = DB::table('attribute_set_mapping')
                                ->select('is_secondary_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('attribute_id',$attrId)
                                ->where('is_secondary_varient', 1)
                                ->count();

            if($isvarientSetChecking == 0  )
            {
                if($isSecondVarientSetChecking == 0 )
                {
                    $rs =  DB::table('attribute_set_mapping')
                        ->where('attribute_set_id', $attrSetId)
                        ->where('attribute_id',$attrId)
                        ->update(array('is_third_varient'=>$status));
                         return 1;
                }
                else
                {
                    return 3;
                }
            }else 
            {
                 return 2;
            }                
           
        }else{
            return 0;
        }
    }
    function is_vr_secondary_enabled($attrId,$attrSetId,$status)
    {
        DB::enablequerylog();
        $isvarientSet   =   "";
        $isSecondaryVarientSet="";
        $isThirdVarientSetChecking="";
        if($status == 1){
            $isSecondaryVarientSet   =   DB::table('attribute_set_mapping')
                                ->select('is_secondary_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('is_secondary_varient', 1)
                                ->count();
           
       }
       if($isSecondaryVarientSet == 0){
             $isvarientSetChecking   = DB::table('attribute_set_mapping')
                                ->select('is_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('attribute_id',$attrId)
                                ->where('is_varient', 1)
                                ->count();
             $isThirdVarientSetChecking   = DB::table('attribute_set_mapping')
                                ->select('is_third_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('attribute_id',$attrId)
                                ->where('is_third_varient', 1)
                                ->count();
                 
            if($isvarientSetChecking == 0)
            {
                if($isThirdVarientSetChecking == 0)
                {
                     $rs =  DB::table('attribute_set_mapping')
                            ->where('attribute_set_id', $attrSetId)
                            ->where('attribute_id',$attrId)
                            ->update(array('is_secondary_varient'=>$status));
                             return 1;

                }
                else
                {
                    return 3;
                }
                
            }else
            {
                 return 2;
            }                
           
        }else{
            return 0;
        }
    }
    function is_vr_enabled($attrId,$attrSetId,$status)
    {
        $isvarientSet   =   "";
        $isSecondaryVarientSet="";
        $isThirdVarientSetChecking="";
        if($status == 1){
            $isvarientSet   =   DB::table('attribute_set_mapping')
                                ->select('is_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('is_varient', 1)
                                ->count();
       }
       if($isvarientSet == 0){
         $isvarientSetChecking   = DB::table('attribute_set_mapping')
                                ->select('is_secondary_varient')
                                ->where('attribute_set_id', $attrSetId)
                                ->where('attribute_id',$attrId)
                                ->where('is_secondary_varient', 1)
                                ->count();
        $isThirdVarientSetChecking = DB::table('attribute_set_mapping')
                                    ->select('is_third_varient')
                                    ->where('attribute_set_id', $attrSetId)
                                    ->where('attribute_id',$attrId)
                                    ->where('is_third_varient', 1)
                                    ->count();
            if($isvarientSetChecking== 0)
            {
                if($isThirdVarientSetChecking == 0)
                {
                    $rs =   DB::table('attribute_set_mapping')
                            ->where('attribute_set_id', $attrSetId)
                            ->where('attribute_id',$attrId)
                            ->update(array('is_varient'=>$status));
                        return 1;
                }else
                {
                    return 3;
                }
                
            }else
            {
                return 2;
            }                  
            
        }else{
            return 0;
        }
    }
    public function upload($folder_name, $file)
    {
        // setting up rules
        $rules = array('image' => 'required',); //mimes:jpeg,bmp,png and for max size max:10000
        // doing the validation, passing post data, rules and the messages
        if (!empty($file))
        {
            $destinationPath = public_path() . '/uploads/products/'; // upload path               
            if (!file_exists($destinationPath . $folder_name))
            {
                $result = \File::makeDirectory($destinationPath . $folder_name, 0775);
            }
            $extension = $file->getClientOriginalExtension(); // getting image extension
            $fileName = rand(11111, 99999) . '.' . $extension; // renameing image
            $file->move($destinationPath . $folder_name, $fileName); // uploading file to given path
            // sending back with message
            return $folder_name . $fileName;
        } else
        {
            // sending back with error message.
            return false;
        }
    }

    public function getAttributeId($key)
    {
        try
        {
            $result = DB::table('attributes')->where('attribute_code', $key)->first(array('attribute_id'));
            if ($result)
            {
                return $result->attribute_id;
            } else
            {
                return 0;
            }
        } catch (Exception $ex)
        {
            
        }
    }
    
    public function getAttributeList($data)
    {
        try
        {
            if(isset($data['attribute_set_id']))
            {
                $attributeSetId = $data['attribute_set_id'];
//                $result = DB::table('attributes_groups')                        
//                        ->join('attribute_mapping', 'attribute_mapping.attribute_map_id', '=', 'attributes_groups.attribute_group_id')
//                        ->join('attributes', 'attributes.attribute_id', '=', 'attribute_mapping.attribute_id')
//                        ->select('attributes.attribute_id', 'attributes.attribute_code', 'attributes.name', 'attributes.input_type', 'attributes.is_dynamic')
//                        ->where('attributes_groups.attribute_group_id', $data['attribute_set_id'])
//                        ->get()->all();
                $productId = isset($data['product_id']) ? $data['product_id'] : 0;
                $productAttributeSetId = 0;
                if($productId)
                {
                    $productAttributeSetId = DB::table('products')->where('product_id', $productId)->pluck('attribute_set_id')->all();
                }                
                if($productId && $productAttributeSetId && ($productAttributeSetId == $attributeSetId))
                {
                    $attributeResult = DB::table('attributes')
                        ->join('attribute_set_mapping', 'attribute_set_mapping.attribute_id', '=', 'attributes.attribute_id')
                        ->join('product_attributes', 'product_attributes.attribute_id', '=', 'attributes.attribute_id')
                        ->where('attribute_set_mapping.attribute_set_id', $data['attribute_set_id'])
                        ->where(['attributes.is_inherited' => 0, 'is_varient' => 0])
                        ->where('product_attributes.product_id', $productId)
                        ->select('attributes.*', 'product_attributes.value')
                        ->get()->all();
                }else{
                    $attributeResult = DB::table('attributes')
                        ->join('attribute_set_mapping', 'attribute_set_mapping.attribute_id', '=', 'attributes.attribute_id')                        
                        ->where('attribute_set_mapping.attribute_set_id', $data['attribute_set_id'])
                        ->where('attributes.is_inherited', 0)                        
                        ->get()->all();
                }
                if(!empty($attributeResult))
                {
                    foreach($attributeResult as $attribute)
                    {
                        if('select' == $attribute->input_type)
                        {
                            
                            $attributeLookup = DB::table('attribute_options')
                                    ->where('attribute_id', $attribute->attribute_id)
                                    ->get(array('option_id', 'option_value'))->all();
                            $attributeOptions = array();
                            if(!empty($attributeLookup))
                            {
                                foreach($attributeLookup as $lookup)
                                {
                                    $attributeOptions[$lookup->option_id] = $lookup->option_value;
                                }
                            }
                            $attribute->options = $attributeOptions;
                        }
                    }
                }
                return $attributeResult;
            }else{
                return 'No Attribute group Id';
            }
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }
    public function addAttributeGroupData($data)
    {
         try
        {
            DB::enableQueryLog();
            $attibuteSetId=$data['attribute_id'];
            $groupId='';
            if($data['groupId']!='no')
            {
               $groupId= $data['groupId'];
            }
            $attributeGroupData['attr.attribute_group_id'] = $groupId;
           $groupRs= DB::table('attribute_set_mapping as attr')
                        ->where('attr.attribute_set_id', $data['attribute_set_id'])
                        ->where('attr.attribute_id', $data['attribute_id'])
                        ->update($attributeGroupData);
                    
                return "thank you";

        }catch(\ErrorException $ex)
        {
            return $ex->getMessage();
        }
    }
    public function getAttributesListById($attibuteSetId)
    {
        
        try
        {
            if(isset($attibuteSetId))
            {
                
                $result = DB::table('attributes')
                        ->join('attribute_set_mapping', 'attribute_set_mapping.attribute_id', '=', 'attributes.attribute_id')
                        ->where('attribute_set_mapping.attribute_set_id', $attibuteSetId)
                        ->get(array('attributes.attribute_id', 'attributes.attribute_code', 'attributes.input_type'))->all();
                
                
                return $result;
            }else{
                return 'No Attribute group Id';
            }
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }
    
    public function getAllAttributes()
    { 
        try
        {
            $userId = Session::get('userId');
            if(Cache::has('attributes_list_'.$userId))
            {
                $attributes_list = Cache::get('attributes_list_'.$userId);
                return json_encode($attributes_list);
            }
            DB::enableQueryLog();
            $allowAddAttributeSet = true;
            $allowEditAttributeSet = true;
            $allowDeleteAttributeSet = true;
            $allowAssignAttributeSet = true;
            $allowAddAttribute = true;
            $allowEditAttribute = true;
            $allowDeleteAttribute = true;
          
                $ag1 = DB::table('attribute_sets')
//                    ->Join('eseal_customer', 'attribute_sets.manufacturer_id', '=', 'eseal_customer.customer_id')
                    ->join('categories', 'categories.category_id', '=', 'attribute_sets.category_id')
                    ->select('attribute_sets.attribute_set_id', 'attribute_sets.attribute_set_name as attribute_set_name','categories.cat_name as cname')
//                    ->where('attribute_sets.manufacturer_id', $manufacturerId)
                    ->get()->all();
             
            $agarr = array();
            $finalagarr = array();
            //return $ag1;
            $ags = json_decode(json_encode($ag1), true);

            foreach ($ags as $ag)
            {
                $attr = DB::table('attribute_set_mapping')
                        ->Join('attribute_sets', 'attribute_sets.attribute_set_id', '=', 'attribute_set_mapping.attribute_set_id')
                        ->Join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id')
                        ->LeftJoin('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attribute_set_mapping.attribute_group_id')
                        ->select('attributes.attribute_id', 'attributes.name as attribute_name', 'attributes_groups.name as attribute_group_name', 'attributes.input_type', 'attribute_set_mapping.attribute_set_id', 'attributes.default_value', 'attributes.is_required', 'attribute_sets.attribute_set_name', 'attribute_sets.attribute_set_id' , 'attribute_set_mapping.is_searchable','attribute_set_mapping.is_varient','attribute_set_mapping.is_secondary_varient','attribute_set_mapping.is_third_varient','attribute_set_mapping.is_filterable')
                        ->where('attribute_set_mapping.attribute_set_id', $ag['attribute_set_id'])
                        ->get()->all();

                $atr = array();
                $atrgrp = array();
                $atrjson = json_decode(json_encode($attr), true);
                //return $atrjson;
                if(!empty($atrjson))
                {
                    foreach ($atrjson as $value)
                    {
                        //return $value;
                         $varient_val=true;
                         $search_enabled=true;
                         $filterble_enabled=true;
                         $secondary_varient_val= true;
                         $third_varient_val= true;
                        $atr['attribute_id'] = $value['attribute_id'];
                        $atr['attribute_set_id'] = $value['attribute_set_id'];
                        $atr['attribute_group_name'] = $value['attribute_group_name'];
                        $atr['attribute_name'] = $value['attribute_name'];
                        $atr['input_type'] = $value['input_type'];
                        $atr['default_value'] = $value['default_value'];
                        $atr['is_required'] = $value['is_required'];
                        $atr['aid'] = $value['attribute_set_id'];
                         $search_btn_status=($value['is_searchable'] == 1)?"checked='true'":"check='false'";
                         $vr_enabled=($value['is_varient']==1)?"checked='true'":"check='false'";
                          $secondary_vr_enabled=($value['is_secondary_varient']==1)?"checked='true'":"check='false'";
                        $third_vr_enabled= ($value['is_third_varient']==1)?"checked='true'":"check='false'";
                         $filter_checke_status=($value['is_filterable'] == 1)?"checked='true'":"check='false'";
                        $varient_val= "<label class='switch '><input class='switch-input vr_status".$value['attribute_id']."' onclick='vr_enabled(".$value['attribute_id'].",".$atr['attribute_set_id'].");'  type='checkbox' ".$vr_enabled." id='vr_enabled_id".$value['attribute_id']."'/><span class='switch-label ' data-on='No' data-off='Yes'></span><span class='switch-handle'></span></label>  ";
                        $secondary_varient_val= "<label class='switch '><input class='switch-input vr_secondary_status".$value['attribute_id']."' onclick='vr_secondary_enabled(".$value['attribute_id'].",".$atr['attribute_set_id'].");'  type='checkbox' ".$secondary_vr_enabled." id='vr_secondary_enabled_id".$value['attribute_id']."'/><span class='switch-label ' data-on='No' data-off='Yes'></span><span class='switch-handle'></span></label>  ";

                         $third_varient_val= "<label class='switch '><input class='switch-input vr_third_status".$value['attribute_id']."' onclick='vr_third_enabled(".$value['attribute_id'].",".$atr['attribute_set_id'].");'  type='checkbox' ".$third_vr_enabled." id='vr_third_enabled_id".$value['attribute_id']."'/><span class='switch-label ' data-on='No' data-off='Yes'></span><span class='switch-handle'></span></label> ";
                   
                        $search_enabled="<label class='switch '><input class='switch-input vr_search".$value['attribute_id']."' id='is_searchble".$value["attribute_id"]."' onclick = 'switchAttributeSearchable('.".$this->roleRepo->encodeData($value["attribute_id"]).'"'. "," .'"'.$this->roleRepo->encodeData($value["attribute_set_id"]).'"'. ",0) '".$search_btn_status." type='checkbox' ".$search_btn_status." /><span class='switch-label ' data-on='No' data-off='Yes'></span><span class='switch-handle'></span></label>";

                        $filterble_enabled="<label class='switch'><input class='switch-input filter_status".$value['attribute_id']."' onclick='checkIsFilterble('.".$this->roleRepo->encodeData($value["attribute_id"]).'"'. "," .'"'.$this->roleRepo->encodeData($value["attribute_set_id"]).'"'. ",0) '".$filter_checke_status." type='checkbox' ".$filter_checke_status." /><span class='switch-label ' data-on='No' data-off='Yes'></span><span class='switch-handle'></span></label>";
                       
                        $atr['is_varient'] = $varient_val;
                        $atr['is_secondary_varient']= $secondary_varient_val;
                         $atr['is_third_varient']= $third_varient_val;
                            
                         $atr['is_search'] = $search_enabled;
                         $atr['is_filterable'] = $filterble_enabled;
                         
                        $atr['actions'] ='';
                            $checkDefaultAttribute=$this->checkDefaultAttribute($value['attribute_id']);
                            if($allowEditAttribute && !$checkDefaultAttribute){
                            $atr['actions'] = $atr['actions'].'<a data-href="/product/editattribute/' .$this->roleRepo->encodeData($value['attribute_id']) . '/' .$value['attribute_set_id'] . '" data-toggle="modal" onclick="getAttributeGroupName('.$ag['attribute_set_id'].');"  data-target="#basicvalCodeModal1"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a><span style="padding-left:10px;" ></span>';
                            }
                            if($allowDeleteAttribute){
                            $atr['actions'] =$atr['actions'].'<a onclick = "delAttributeFromAttSet('.$value['attribute_id'].','.$value['attribute_set_id'].')"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a><span style="padding-left:20px;" ></span>';
                            }
                           
                          
                        $atrgrp[] = $atr;
                    }
                }
                    $agarr['attribute_set_name'] = $ag['attribute_set_name'];
                    $agarr['attribute_set_id'] =$ag['attribute_set_id'];
                    $agarr['category_id'] = $ag['cname'];
                    $agarr['actions'] = '';
                    if($allowAddAttribute){
                    $agarr['actions'] = $agarr['actions'].'<a data-href="product/saveAttributeGroup/" data-toggle="modal" onclick="getAttributeGroupName('.$ag['attribute_set_id'].');" data-target="#basicvalCodeModal"><span class="btn btn-circle btn-icon-only btn-default"><i class="fa fa-plus"></i></span></a><span style="padding-left:10px;" ></span>';
                    }              
                    if($allowEditAttributeSet){
                    $agarr['actions'] = $agarr['actions'].'<a data-href="/product/editattributeset/' .$this->roleRepo->encodeData($ag['attribute_set_id']). '" onclick="getAttributeSetName(\''.$ag['attribute_set_name'].'\');"  data-attributeId="'.$this->roleRepo->encodeData($ag['attribute_set_id']).'" data-toggle="modal" data-target="#editAttributeSet"><span class="btn btn-circle btn-icon-only btn-default"><i class="fa fa-pencil" data-id="'.$ag['attribute_set_name'].'"></i></span></a><span style="padding-right:10px;" ></span>';
                    }
                    if($allowDeleteAttributeSet){
                    $agarr['actions'] = $agarr['actions'].'<a onclick = "deleteAttrSet('."'".$this->roleRepo->encodeData($ag['attribute_set_id'])."'".')"><span class="btn btn-circle btn-icon-only btn-default"><i class="fa fa-trash-o"></i></span></a><span style="padding-left:10px;" ></span>';
                    }
                    /*if($allowAssignAttributeSet){
                        $agarr['actions'] = $agarr['actions'].'<a data-href="/product/editattributeset/' .$this->roleRepo->encodeData($ag['attribute_set_id']). '" data-attributeId="'.$ag['attribute_set_id'].'" data-toggle="modal" data-target="#assignAttributeSet" onclick="getAssignAttribute('.$ag['attribute_set_id'].')"><span class="badge bg-light-blue"><i class="fa fa-pencil"></i></span></a><span style="padding-left:10px;" ></span>';
                    }*/
                    $agarr['children'] = $atrgrp;
                    $finalagarr[] = $agarr;
                
            }
            if(!Cache::has('attributes_list_'.$userId))
            {
//                $expiresAt = \Carbon::now()->addMinutes(10);
                $expiresAt = 10;
                Cache::add('attributes_list_'.$userId, $finalagarr, $expiresAt);
            }
            return json_encode($finalagarr);
        } catch (\ErrorException $ex) {
            return json_encode($ex->getMessage());
        }
    }

    public function saveAttribute($data,$opt)
    {
        try
        {
            $userId = Session::get('userId');
            if(!empty($data))
                {
                //validator
                     $validator = \Validator::make(
                                    array(
                                'name' => isset($data['name']) ? $data['name'] : '',
                                'attribute_type' => isset($data['attribute_type']) ? $data['attribute_type'] : ''
                                    ), array(
                                'name' => 'required',
                                'attribute_type' => 'required'
                                    ));
                    if($validator->fails())
                    {
                        //$data = $this->_product->getProductFields($this->_manufacturerId);
                        $errorMessages = json_decode($validator->messages());
                        $errorMessage = '';
                        if(!empty($errorMessages))
                        {
                            foreach($errorMessages as $field => $message)
                            {
                                $errorMessage = implode(',', $message);
                            }
                        }
                        //return Response::back()->withErrors([$errorMessage]);
                        return Response::json([
                                'status' => false,
                                'message' => $errorMessage
                    ]);
                    }
                //validator
                /*if(isset($data['option_values']))
                {
                    $option_values=$data['option_values'];
                }else{
                    $option_values=0;
                }*/
				$option_values=$opt;
				
                $attributeName = isset($data['name']) ? $data['name'] : '';
                if($attributeName == '')
                {
                    $attributeName = isset($data['attributeFields']['name']) ? $data['attributeFields']['name'] : '';
                    $data = isset($data['attributeFields']) ? $data['attributeFields'] : array();
                }
                if($attributeName != '')
                {
                    $attribute_set_id = $data['attribute_set_value'];
                    unset($data['_method']);
                    unset($data['_token']);
                    unset($data['attribute_set_value']);
                    unset($data['option_values']);
                    
                    //$data['attribute_code'inde] = str_replace(' ', '_', strtolower($data['name']));
                    $checkForDefaultAttribute=$this->checkForDefaultAttribute($data['name']);
                    $checkForMfgCode=$this->checkForAttributes($data['attribute_code']);
                    if($checkForDefaultAttribute || $checkForMfgCode){
                       return Response::json([
                                'status' => false,
                                'message' => 'Attribute with this Code exists.']); 
                    }
                    

                    if(!$checkForMfgCode && !$checkForDefaultAttribute){
                        
                    $attribute_id = DB::table('attributes')->insertGetId([
                                        'attribute_code'=> $data['attribute_code'],
                                        'name'=>$data['attribute_code'],
                                        'input_type'=>$data['input_type'],
                                        'default_value'=>$data['default_value'],
                                        'is_required'=>$data['is_required'],
                                        'validation'=>$data['validation'],
                                        'regexp'=>$data['regexp'],
                                        'lookup_id'=>$data['lookup_id'],
                                        'created_by'=>$userId
                                        ]);
                    $sort_order=DB::table('attribute_set_mapping')->where('attribute_set_id',$attribute_set_id)->max('sort_order'); 
                    if(!empty($option_values) && isset($attribute_id)) {
                        //$values=json_decode($option_values);
                        foreach ($option_values as $key=>$raw) {
                            if($key == 'value')
                            {
                                $values = $raw;
                                foreach($raw as $optval)
                                {
									if(trim($optval) != NULL)
									{
                                    DB::table('attribute_options')->insert([
                                        'attribute_id'=> $attribute_id,
                                        'option_value'=>$optval
                                        ]);
									}
                                }
                            }
                        }
                    }
                    DB::table('attribute_set_mapping')->insert([
                        'attribute_set_id' => $attribute_set_id,
                        'attribute_id' => $attribute_id,
                        'sort_order' =>  $sort_order+1
                    ]);
                    return Response::json([
                                'status' => true,
                                'message' => 'Attribute Sucessfully Created.']);
                    }
                }

            }

        } catch (Exception $ex) {
            return Response::json([
                        'status' => false,
                        'message' => $ex->getMessage()
            ]);
        }
    }
    
    public function saveAttributeGroup($data)
    {
       //  return $data;
        //return $data['attribute_group']['name'];
        try
        {

            if(!empty($data) && isset($data['att_group']))
            {   
                
                 $checkGroup = DB::table('attributes_groups')
                            ->where('name',$data['att_group'])
                             ->where('category_id',$data['cat_id'])
                            ->get()->all();
                if(empty($checkGroup))
                {
                    DB::table('attributes_groups')->insert(['name'=> $data['att_group'],'category_id'=>$data['cat_id']]);
                    return Response::json([
                            'status' => true,
                            'message' => 'Sucessfully Created.'
                    ]);
                }
                return Response::json([
                        'status' => false,
                        'message' => 'Attribute group already exists with this category.'
            ]);
            }
            return Response::json([
                        'status' => false,
                        'message' => 'Unable to save Attribute group'
            ]);
        } catch (Exception $ex) {
            return Response::json([
                        'status' => false,
                        'message' => $ex->getMessage()
            ]);
        }        
    }
    public function getAttributeListDataById($data)
    {
            DB::enablequerylog();
        $getAttListData = DB::table('attribute_set_mapping as att_map')
                                ->join('attribute_sets as att_set', 'att_map.attribute_set_id', '=', 'att_set.attribute_set_id')
                                ->join('attributes as attr', 'attr.attribute_id', '=', 'att_map.attribute_id')
                                ->where('att_set.attribute_set_id', $data['attribute_set_id'])
                                ->select('attr.name','attr.attribute_id','att_map.attribute_set_id','attr.attribute_group_id','att_map.is_varient','att_map.is_secondary_varient','att_map.is_third_varient','att_map.is_searchable','att_map.is_filterable')
                                ->get()->all();

       return $getAttListData;

    }
    
    public function getAttributeListData($att_set_name)
    {

            DB::enablequerylog();
        $getAttListData = DB::table('attribute_set_mapping as att_map')
                                ->join('attribute_sets as att_set', 'att_map.attribute_set_id', '=', 'att_set.attribute_set_id')
                                ->join('attributes as attr', 'attr.attribute_id', '=', 'att_map.attribute_id')
                                ->where('att_set.attribute_set_id', $att_set_name)
                                ->select('attr.name','attr.attribute_id','att_map.attribute_set_id','att_map.attribute_group_id','att_map.is_varient','att_map.is_searchable','att_map.is_secondary_varient','att_map.is_third_varient','att_map.is_filterable','att_set.category_id')
                                ->get()->all();
       return $getAttListData;

    }
   
    public function saveAttributeSet($data)
    {
        //return $data;
        try
        {
              DB::enableQueryLog();
               $userId = Session::get('userId');
            if(!empty($data) && isset($data['attribute_set_name']))
            {
                
                //validator
                    $data['attribute_set']['legal_entity_id']=Session::get('legal_entity_id');
                $inheritFrom = (int) isset($data['attribute_set']['inherit_from']) ? $data['attribute_set']['inherit_from'] : 0;
                unset($data['attribute_set']['inherit_from']);
                $attributeSetName = isset($data['attribute_set_name']) ? $data['attribute_set_name'] : '';
                $defaultAttributes= DB::table('attributes')->where('attribute_type',2)->pluck('attribute_id')->all();
                
                $attributeIds = array();
                if(isset($data['attribute_id']))
                {
                    $attributeIds=$data['attribute_id'];
                    $attributeIds= array_merge($attributeIds,$defaultAttributes);
                
                }else
                {
                    $attributeIds= array_merge($attributeIds,$defaultAttributes);                
                }
                
                if($attributeSetName != '')
                {
                    $checkIfAttributeSetCreated = $this->checkIfAttributesSetCreated($attributeSetName);
                    if(!$checkIfAttributeSetCreated)
                    {
             
                       $checkAttributCat= DB::table('attribute_sets')->where('category_id',$data['category_id'])->first();
                        if($checkAttributCat)
                        {
                             return Response::json([
                                'status' => false,
                                'message' =>"This category is mapped with another Attribute Set. " ]);
                        }else
                        {
                            $attributeSetId = DB::table('attribute_sets')->insertGetId(['attribute_set_name'=> $data['attribute_set_name'],'category_id' => $data['category_id'],'is_active'=>'1','legal_entity_id'=>$userId]);                           
                            Session::forget('attribute_set_id');
                            Session::put('attribute_set_id',$attributeSetId);
                        }   
                    }
                    else
                    {
                         return Response::json([
                                'status' => false,
                                'message' =>"This name is already exists. " ]);
                    }
                
                }  
                if($attributeIds)
                {
                     $attributeSetId = Session::get('attribute_set_id');
                    foreach($attributeIds as $key=>$setAttribute)
                    {
                       /* $checkAttributeSet= DB::table('attribute_set_mapping')
                                        ->where('attribute_set_id', $attributeSetId)
                                        ->where('attribute_id',$setAttribute)
                                        ->first();
                        if($checkAttributeSet)
                        {
                            //log::info("it is aleady existed...");
                        }else
                        {*/
                            if(in_array($setAttribute,$defaultAttributes))
                            {
                                $key=0;
                            }
                             DB::table('attribute_set_mapping')
                                ->insert(['attribute_set_id'=> $attributeSetId,
                                'attribute_id' => $setAttribute,
                                'sort_order'=>$key
                                ]);
                        //}
                    }
                }
                
                return Response::json([
                            'status' => true,
                            'message' => 'Successfully Saved.',
                            'set_name' => $attributeSetName,
                            'set_id' => $attributeSetId,
                            'inherit_from' => $inheritFrom
                ]);
            }
            //return 'hi created';
            return Response::json([
                        'status' => false,
                        'message' => 'Unable to save Attribute set'
            ]);
        } catch (Exception $ex) {
            return Response::json([
                        'status' => false,
                        'message' => $ex->getMessage()
            ]);
        }        
    }
    
    
    public function checkIfAttributesSetCreated($attributeSetName)
    {
        try
        {
            $attributeSetData = DB::table('attribute_sets')->where('attribute_set_name', $attributeSetName)->pluck('attribute_set_id')->all();
            
            if(!empty($attributeSetData))
            {
                return $attributeSetData;
            }else{
                return 0;
            }
        } catch (Exception $exc)
        {
            echo $exc->getTraceAsString();
        }
    }

    public function checkForAttributes($attributeCode)
    {
        try
        {
            if(!empty($attributeCode))
            {
              $checkAttribute = DB::table('attributes')->where('attribute_code',$attributeCode)->get()->all();
              if(!empty($checkAttribute))
                {
                    return $checkAttribute;
                }else{
                    return 0;
                }
            }
      
        } catch (Exception $exc)
        {
            echo $exc->getTraceAsString();
        }
    }    
    //Combined Function
    //check attribute groups and sets
    public function checkIfAttributeSetGroupExists($attributeGroupName,$category_id)
    {
        try
        {
            if(!empty($attributeSetName)&&!empty($attributeGroupName))
            {
               $attribute = DB::table('attributes_groups')
                            ->where('name',$attributeGroupName)
                             ->where('category_id',$category_id)
                            ->get()->all();
                    if(!empty($attribute))
                    {
                       return $attribute;
                    }else{
                        return 0;
                    }
            }
            else
            {
                 return 0;
            }
            
        } catch (Exception $exc)
        {
            echo $exc->getTraceAsString();
        }
    }   
    //check attribute groups and sets
    public function insertNewAttributes( $attributeSetId)
    {
        try
        {
            /*DB::statement('INSERT INTO attributes (attribute_code, name, text, input_type, default_value, is_required, validation,  `regexp`,  lookup_id,  attribute_group_id,  attribute_type, is_inherited) '
                    . '(select attr.attribute_code, attr.name, attr.text, attr.input_type, attr.default_value, attr.is_required, attr.validation,  attr.`regexp`,  attr.lookup_id,  attr.attribute_group_id,  attr.attribute_type, attr.is_inherited '
                    . 'from attribute_sets as aset '
                    . 'join attribute_set_mapping as map on map.attribute_set_id = aset.attribute_set_id '
                    . 'join attributes as attr on attr.attribute_id = map.attribute_id 
             * join attributes_groups as groups on groups.attribute_group_id = attr.attribute_group_id '
                    . 'where aset.attribute_set_name = "Default")');*/
            $getDefaultAttributes = DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    ->join('attributes_groups as groups', 'groups.attribute_group_id', '=', 'attr.attribute_group_id')
                    ->where('aset.attribute_set_name', 'Default')
                    ->get(array('attr.attribute_code', 'attr.name', 'attr.input_type', 'attr.default_value', 'attr.is_required', 'attr.validation',  'attr.regexp',  'attr.lookup_id',  'attr.attribute_group_id',  'attr.attribute_type', 'attr.is_inherited'))->all();
            
            foreach($getDefaultAttributes as $attribute)
            {
                $attributeGroupId = $attribute->attribute_group_id;
                $mfgGroupId = $this->getAttributeGroupId($attributeGroupId);
                $attributes = (array) $attribute;
                unset($attributes['attribute_group_id']);
                $attributes['attribute_group_id'] = $mfgGroupId;
                $attributeId = DB::table('attributes')->insertGetId($attributes);
                $attributeSetMappingData = array();
                $attributeSetMappingData['attribute_set_id'] = $attributeSetId;
                $attributeSetMappingData['attribute_id'] = $attributeId;
                $attributeSetMapId = DB::table('attribute_set_mapping')->insertGetId($attributeSetMappingData);            
            }
            $this->updateAttributeGroups($attributeSetId);
        } catch (\ErrorException $ex)
        {
            return $ex->getMessage();
        }
    }
    
    public function getAttributeGroupId($attributeGroupId)
    {
        try
        {
            $attributeManufacturerId = DB::table('attributes_groups as attr_group')
                    ->join('attributes_groups as attr_group1', 'attr_group1.name', '=', 'attr_group.name')
                    ->where('attr_group1.attribute_group_id', $attributeGroupId)
                    ->first(array('attr_group.attribute_group_id'));
            if(!empty($attributeManufacturerId))
            {
                return $attributeManufacturerId->attribute_group_id;
            }else{
                return $attributeGroupId;
            }
        } catch (\ErrorException $ex)
        {
            return $ex->getMessage();
        }
    }
    
    public function updateAttributeGroups($attributeSetId)
    {
        try
        {
            $attributeGroups = DB::table('attributes_groups as attr_group')
                    ->join('attributes_groups as attr_group1', 'attr_group1.name', '=', 'attr_group.name')
                    ->get(array('attr_group.attribute_group_id as mfg_group_id', 'attr_group1.attribute_group_id as default_id'))->all();
            foreach($attributeGroups as $attributeGroup)
            {
                $attributeGroupData['attribute_group_id'] = $attributeGroup->mfg_group_id;
                DB::table('attributes as attr')
                        ->join('attribute_set_mapping as map', 'map.attribute_id', '=', 'attr.attribute_id')
                        ->where('attr.attribute_group_id', $attributeGroup->default_id)
                        ->where('map.attribute_set_id', $attributeSetId)
                        ->update($attributeGroupData);
            }
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }
    
    public function checkIfGroupsCreated($manufacturerId)
    {
        try
        {
            $groupData = DB::table('attributes_groups')->where('manufacturer_id', $manufacturerId)->get('name')->all();
            $defaultDroupData = DB::table('attributes_groups')->where('manufacturer_id', 0)->get()->all();            
            
            if(empty($groupData))
            {
                return 1;
            }else{
                return 0;
            }
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }
    
     public function checkIfAttributesCreated($attributeSetId, $attributeIds)
    {
        try
        {
            $attributeList = DB::table('attribute_sets')
            ->join('attribute_set_mapping', 'attribute_set_mapping.attribute_set_id', '=', 'attribute_sets.attribute_set_id')
            ->where('attribute_sets.attribute_set_id', 1)
            ->pluck(DB::raw('group_concat(attribute_set_mapping.attribute_id)'))->all();
            $tempArray = array();
            $InsertArray = array();
            if(!empty($attributeList))
            {
                $array = explode(',', $attributeList);
                $attributeIds = array_diff($array, $attributeIds);
            }
            foreach ($attributeIds as $key=>$attr) {
                $tempArray['attribute_id'] = $attr;
                $tempArray['sort_order']=$key;
                $tempArray['attribute_set_id'] = $attributeSetId;
                $InsertArray[] = $tempArray;
            }
            DB::table('attribute_set_mapping')->insert($InsertArray);
            //$groupData = DB::table('attribute_set_mapping')
            //        ->where('attribute_set_id', $attributeSetId)
            //        ->get()->all();
            return 1;
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }
    public  function updateattributeset($data){
        //return $attribute_set_id;

             DB::enableQueryLog();
             $attribute_set_id=$data['attribute_set_id'];
            
        if(!empty($data) && isset($attribute_set_id)){
            //validator
              
                //validator
             $checkIfAttributeSetCreated = $this->checkIfAttributesSetCreated($data['attribute_set_name']);
             //return $checkIfAttributeSetCreated;
            if($checkIfAttributeSetCreated && $checkIfAttributeSetCreated[0]!=$data['attribute_set_id'])
            {
                return Response::json([
                        'status' => false,
                        'message' => 'Attribute set with this name exists.'
            ]);
            } 
            if(isset($data['edit_category_id']))
            {
                /* DB::table('attribute_sets')
                        ->where('attribute_set_id',$attribute_set_id)
                        ->update(array('category_id' => $data['category_id'],'attribute_set_name'=>$data['attribute_set_name']));*/

                DB::table('attribute_sets')
                        ->where('attribute_set_id',$attribute_set_id)
                        ->update(array('attribute_set_name'=>$data['attribute_set_name']));
             

            }
            if(!$checkIfAttributeSetCreated[0] || ($checkIfAttributeSetCreated[0] && $checkIfAttributeSetCreated[0]==$data['attribute_set_id'])){
            
            $FormdAttr=$data['formattributes'];
            $setAttributes=DB::table('attribute_set_mapping')
                   ->join('attributes','attribute_set_mapping.attribute_id','=','attributes.attribute_id')
                   ->where('attribute_set_mapping.attribute_set_id','=',$attribute_set_id)
                   ->first([DB::raw('group_concat(attribute_set_mapping.attribute_id) as attribute_id_list')]);
                  
                 // echo "<prE>";print_R($setAttributes);die;
                 $setAttributesList = []; 
                 // echo $setAttributes->attribute_id_list;die;
                 if(!empty($setAttributes))
                 {
                    $setAttributesList = explode(',', $setAttributes->attribute_id_list);         
                 }
                 $defaultCompareData= array();
                $defaultAttributes= DB::table('attributes')->where('attribute_type',2)->pluck('attribute_id')->all();
                $getAttSetMapData= DB::table('attribute_set_mapping')->where('attribute_set_id',$attribute_set_id)->pluck('attribute_id')->all();
                   foreach ($defaultAttributes as $key)
                   {   
                        $FormdAttr.= ','.$key;                       
                   }
                $formdAttributes=explode(',',$FormdAttr);        
                foreach($setAttributesList as $key=>$setAttribute){
                    if(!in_array($setAttribute,$formdAttributes)){
                        DB::table('attribute_set_mapping')
                        ->where('attribute_set_id',$attribute_set_id)
                        ->where('attribute_id',$setAttribute)
                        ->delete();                       
                    }
                }           
                foreach($formdAttributes as $key=>$formdAttribute){
                        if(in_array($formdAttribute,$setAttributesList))
                        {
                        
                                 DB::table('attribute_set_mapping')
                                ->where('attribute_set_id',$attribute_set_id)
                                ->where('attribute_id',$formdAttribute)
                                ->update(array('sort_order' => $key));                        
                        }
                        if(in_array($formdAttribute,$defaultAttributes))
                        {
                             DB::table('attribute_set_mapping')
                            ->where('attribute_set_id',$attribute_set_id)
                            ->where('attribute_id',$formdAttribute)
                            ->update(array('sort_order' => 0)); 
                        }
                       
                        if(!in_array($formdAttribute,$setAttributesList))
                        {
                            if(!in_array($formdAttribute,$defaultAttributes))
                            {
                                 DB::table('attribute_set_mapping')
                                ->insert(['attribute_set_id'=> $attribute_set_id,
                                 'attribute_id' => $formdAttribute,
                                 'sort_order'=>$key]);
                            }
                            else
                            {
                                 DB::table('attribute_set_mapping')
                                    ->insert(['attribute_set_id'=> $attribute_set_id,
                                 'attribute_id' => $formdAttribute,
                                 'sort_order'=>0]); 
                                   
                            }                        
                        }                    
                }
            return Response::json([
                'status' => true,
                'message'=>$data['attribute_set_name']
              ]);
            }
           
        }
    }
    public function checkForDefaultAttribute($attributeName)
    {
        try
        {
            $attribute = DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    //->join('attributes_groups as groups', 'groups.attribute_group_id', '=', 'attr.attribute_group_id')
                    ->where('aset.attribute_set_name', 'Default')
                    ->where('attr.name',$attributeName)
                    ->get()->all();
            
            if(!empty($attribute))
            {
                return $attribute;
            }else{
                return 0;
            }
        } catch (Exception $exc)
        {
            echo $exc->getTraceAsString();
        }
    }
    public function checkDefaultAttribute($attributeId)
    {
        try
        {
            $attribute = DB::table('attribute_sets as aset')
                    ->join('attribute_set_mapping as map', 'map.attribute_set_id', '=', 'aset.attribute_set_id')
                    ->join('attributes as attr', 'attr.attribute_id', '=', 'map.attribute_id')
                    //->join('attributes_groups as groups', 'groups.attribute_group_id', '=', 'attr.attribute_group_id')
                    ->where('aset.attribute_set_name', 'Default')
                    ->where('attr.attribute_id',$attributeId)
                    ->get()->all();
            
            if(!empty($attribute))
            {
                return $attribute;
            }else{
                return 0;
            }
        } catch (Exception $exc)
        {
            echo $exc->getTraceAsString();
        }
    }       
    
    public function assignGroups($data)
    {
        //return $data;
        try
        {
            if(!empty($data))
            {
                $assign_locations = isset($data['assign_locations']) ? $data['assign_locations'] : array();
                $attribute_set_id = isset($data['attribute_set_id']) ? $data['attribute_set_id'] : '';
                if(!empty($assign_locations))
                {
                    $assigned=DB::table('product_attributesets')->where('attribute_set_id',$attribute_set_id)->get(array('location_id','product_group_id'))->all();
                    foreach($assign_locations as $locationData)
                    {
                        $locationAccess = json_decode($locationData);
                        $insertArray['attribute_set_id'] = $attribute_set_id;
                        $insertArray['location_id'] = $locationAccess->location_val;
                        $insertArray['product_group_id'] = $locationAccess->product_group;
                        $id = DB::table('product_attributesets')->where($insertArray)->pluck('id')->all();
                        if(!$id)
                        {
                            DB::table('product_attributesets')->insert($insertArray);
                        }else{
                            foreach ($assigned as $key => $value) {
                                if($value->product_group_id == $locationAccess->product_group && $value->location_id == $locationAccess->location_val){
                                    unset($assigned[$key]);
                                }
                            }
                        }
                    }
                    //
                    foreach($assigned as $assigneds){
                        DB::table('product_attributesets')->where(array('attribute_set_id'=>$attribute_set_id,'location_id'=>$assigneds->location_id,'product_group_id'=>$assigneds->product_group_id))->delete();
                    }
                    //
                    return Response::json([
                        'status' => true,
                        'message' => 'Inserted locations attributes and groups.'
                    ]);
                }else{
                    return Response::json([
                        'status' => false,
                        'message' => 'Location and product group data required.'
                    ]);
                }
            }else{
                return Response::json([
                    'status' => false,
                    'message' => 'No data.'
                ]);
            }
        } catch (\ErrorException $ex) {
            return $ex->getMessage();
        }
    }
    public function getAllAttributeGroup($cat_id)
    {
             $getAttGroupListData = DB::table('attributes_groups')
                                    ->where('category_id',$cat_id)
                                    ->get()->all();
        return $getAttGroupListData;
    }
}
?>