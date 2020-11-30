<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Utility;
use DB;
use UserActivity;

class ProductInfo extends Model
{
    public $timestamps = false;
    protected $primaryKey = "product_id";
    protected $table = "products";

    public function getCategoryModel()
    {
      return $this->hasOne('App\Modules\Product\Models\CategoryEloquentModel', 'category_id', 'category_id');
    }
    public function getPrimaryImage($product_id)
    {
       return $this->where('product_id',$product_id)->first(array('primary_image'));
    }
	public function getThumbnailImage($product_id)
    {
       return $this->where('product_id',$product_id)->pluck('thumbnail_image')->all();
    }
    public function getBrandModel()
    {
      return $this->hasOne('App\Modules\Product\Models\BrandModel', 'brand_id', 'brand_id');  
    }

    public function legalEntityModel()
    {
      return $this->hasOne('App\Modules\Product\Models\LegalEntityModel', 'legal_entity_id', 'manufacturer_id');
    }
    public function productContentModel()
    {
      return $this->hasOne('App\Modules\Product\Models\productContentEloquentModel', 'product_id', 'product_id'); 
    }
    public function productSlabRates()
    {
      return $this->hasOne('App\Modules\Product\Models\SlabRatesModel', 'product_id', 'product_id');    
    }
    public function productAttributes()
    {
      return $this->hasMany('App\Modules\Product\Models\ProductAttributesModel', 'product_id', 'product_id');     
    }
    public function productAttributeGroups()
    {
        return $this->hasMany('App\Modules\Product\Models\AttributesGroup', 'category_id', 'category_id');     

    }
    public function Attributes()
    {
      return $this->hasMany('App\Modules\Product\Models\AttributesModel', 'attribute_id', 'attribute_id');    
    }
    public function getProductData($product_id)
    {
        return $this->onWriteConnection()->with(array('getCategoryModel' => function($query)
        {
            $query->useWritePdo()->select('category_id','parent_id','cat_name');
        }, 'getBrandModel' => function($brandQuery)
        {
            $brandQuery->useWritePdo()->select('brand_id','brand_name','logo_url');
        },'legalEntityModel' => function($legalEntityQuery)
        {
            $legalEntityQuery->useWritePdo()->select('legal_entity_id','business_legal_name');
        }, 'productContentModel' => function($productContenQuery)
        {
            $productContenQuery->useWritePdo()->select('product_id','description');
        }))->where('product_id',$product_id)->first(array('product_title','primary_image','seller_sku','category_id','brand_id','manufacturer_id','sku','shelf_life','shelf_life_uom','kvi','upc','mrp','primary_image','product_title','upc','product_id','is_sellable','esu','star','is_active','is_approved','cp_enabled','product_group_id','status'));
    }
     public function getAttributeWithAttGroup($category_id,$product_id)
    {

      DB::enablequerylog();
       $getAttData= DB::table('attribute_sets as att_set')
                    ->join('attribute_set_mapping as att_map','att_set.attribute_set_id','=','att_map.attribute_set_id')
                    ->join('attributes AS att','att.attribute_id','=','att_map.attribute_id')
                    ->leftjoin('product_attributes as pro_att',function($join) use($product_id)
                    {
                      $join->on('pro_att.attribute_set_id','=','att_map.attribute_set_id');
                      $join->on('pro_att.attribute_id','=','att_map.attribute_id')->where('pro_att.product_id','=',$product_id);
                    })
                    ->select('att_set.attribute_set_id','pro_att.value','att_map.attribute_id','att_set.category_id','att.attribute_code',
                      'att_map.attribute_group_id','att.name')
                    ->where('att_set.category_id',$category_id)                                   
                    ->groupby('att_map.attribute_id')
                    ->get()->all();
        $attGroup=DB::table('attributes_groups as att_grp')
                      ->join('attribute_set_mapping as att_map','att_map.attribute_group_id','=','att_grp.attribute_group_id')
                      ->where('att_grp.category_id',$category_id)
                      ->orwhere('att_grp.attribute_group_id','1')
                      ->select('att_grp.attribute_group_id','att_grp.name')
                      ->groupby('att_grp.attribute_group_id')
                      ->get()->all();
         $offerPack = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value','sort_order')
            ->where('master_lookup_categories.mas_cat_id','=','102')
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=','Offer Pack')
             ->get()->all();        

        $rs['attribute_data']=$getAttData;
        $rs['att_group']=$attGroup;
        $rs['offer_pack']=$offerPack;
        return $rs;     
    }
   
    public function uniqueProductName($product_title)
    {
      $product_title= ltrim($product_title);
      $data= DB::table('products')->where('product_title',$product_title)->select('product_title')->first();
      if($data!="")
      return "false";
    else
      return "true";
    }
    public function addProductInventoryData($product_id)
    {

      $LeaglEntityData= DB::table('legalentity_warehouses')
                      ->where('legal_entity_id',2)
                      ->get()->all();
      $inventoryData=array();
      $LeaglEntityData= json_decode(json_encode($LeaglEntityData),true);
      foreach ($LeaglEntityData as $value)
      {
          $checkInventoryExist= DB::table('inventory')
                                ->where('product_id',$product_id)
                                ->where('le_wh_id', $value['le_wh_id'])
                                ->select('le_wh_id')
                                ->get()->all();
          if(!$checkInventoryExist)
          {
             $inventoryData[] = array('product_id' => $product_id,
                                          'le_wh_id' => $value['le_wh_id']); 
          }
      }
     $rs= DB::table('inventory')->insert($inventoryData);
      return $rs;
    }
    public function getOfferPackAttId()
    {
        return DB::table('attributes')->where('attribute_code','offer_pack')->pluck('attribute_id')->all();
    }
    public function getAttributeIdValue($product_id,$att_set_id)
    {
    DB::enablequerylog();
      $notInQuery= DB::table('product_attributes')
                  ->select(DB::raw('group_concat(attribute_id) as att_id'))
                  ->where('product_id',$product_id)
                  ->first();
      $notInQuery= json_decode(json_encode($notInQuery),true);
      $queryData=explode(",",$notInQuery['att_id']);
      $checkNewAtt= DB::table('attribute_set_mapping')
                    ->select(DB::raw('group_concat(attribute_id) as att_id'))
                    ->where('attribute_set_id',$att_set_id)
                    ->whereNotIN('attribute_id',$queryData)
                    ->get()->all();
      $checkNewAtt= json_decode(json_encode($checkNewAtt),true);
     $newAttData=explode(",",$checkNewAtt[0]['att_id']);
      if($newAttData[0]!="")
      {
          foreach ($newAttData as $keyValue)
          {
            DB::table('product_attributes')
                ->insert(['attribute_id' =>$keyValue,'product_id'=>$product_id,'attribute_set_id'=>$att_set_id,'language_id'=>'33','created_by'=>Session::get('userId')]);
          }
      }
      $productRs= DB::table('product_attributes as pro_att')
                ->join('attribute_set_mapping AS att_map','att_map.attribute_id','=','pro_att.attribute_id')
                ->join('attributes AS att','att.attribute_id','=','pro_att.attribute_id')
                ->where('pro_att.product_id',$product_id)
                ->where('att_map.attribute_set_id',$att_set_id)
                ->groupby('att_map.sort_order')
                ->get()->all();
      return $productRs;
    }
     public function getAttGroupData($product_id,$att_set_id)
    {
      DB::enablequerylog();
        $productRs= DB::table('product_attributes as pro_att')
                  ->join('attribute_set_mapping AS att_map','att_map.attribute_id','=','pro_att.attribute_id')
                  ->join('attributes AS att','att.attribute_id','=','pro_att.attribute_id')
                  ->select('att_map.attribute_group_id')
                  ->where('pro_att.product_id',$product_id)
                  ->where('att_map.attribute_set_id',$att_set_id)
                  ->groupby('att_map.attribute_group_id')
                  ->get()->all();
 
                  $attGrpId='';
                  $group_id=array();
                foreach ($productRs as $value)
                {
                    $attGrpId.= $value->attribute_group_id.','; 
                }
                $att_group_id= trim($attGrpId,',');
               $group_id= explode(',',$att_group_id);

      $att_groupRs= DB::table('attributes_groups')->select('name','attribute_group_id')->whereIn('attribute_group_id',$group_id)->get()->all();

     return $att_groupRs;
    }
    public function getAttGroupByCategory($category_id)
    {
      DB::enablequerylog();
        $getAttGroupData= DB::table('attributes_groups')
                          ->where('category_id',$category_id)
                          ->get()->all();
                         /* $rs=DB::getquerylog();
                          print_r(end($rs)); die();*/
        return $getAttGroupData;
    }   
    public function getAttributeGroupData($product_id,$category_id)
    {

         $productAttributInfo = DB::table('attribute_set_mapping as att_map')
                              ->join('attributes_groups as att_group','att_group.attribute_group_id','=','att_map.attribute_group_id')
                              ->join('product_attributes as pro_att','pro_att.attribute_id','=','att_map.attribute_id' )
                              ->select('att_group.attribute_group_id','att_group.name as group_name')
                              ->where('pro_att.product_id','=',$product_id)
                              ->where('att_group.category_id','=',$category_id)
                              ->groupby('att_map.attribute_group_id')
                              ->get()->all(); 
            
        return $productAttributInfo;
    }
    //here we are planning 1st get product att, att_group id and from product_att, att_map
    public function getAttributeData($product_id)
    {

         $productAttributInfo = DB::table('product_attributes as pro_att')
                              ->join('attribute_set_mapping as att_map','att_map.attribute_id','=','pro_att.attribute_id')
                              ->join('attributes as att','att.attribute_id','=','pro_att.attribute_id' )
                              ->select('att_map.attribute_group_id','att.attribute_id','att.name','pro_att.value')
                              ->where('pro_att.product_id','=',$product_id)
                              ->get()->all(); 
            
        return $productAttributInfo;
    }
    // This for edit product wise
    public function getEditProductGroupAtt($product_id,$category_id)
    {
      
         $productAttributInfo = DB::table('attribute_set_mapping as att_map')
                              ->join('attributes_groups as att_group','att_group.attribute_group_id','=','att_map.attribute_group_id')
                              ->select('att_group.attribute_group_id','att_group.name as group_name')
                              ->where('att_group.category_id','=',$category_id)
                              ->groupby('att_map.attribute_group_id')
                              ->get()->all(); 
        return $productAttributInfo;
    }
    public function getEditProductAttributeData($product_id,$category_id)
    {
        DB::enablequerylog();

        $att_set_id= DB::table('attribute_sets')
                    ->where('category_id','=',$category_id)
                    ->pluck('attribute_set_id')->all();
            if($att_set_id)
            {
                 $productAttributInfo = DB::table('attributes as att')
                                ->join('attribute_set_mapping as att_map','att_map.attribute_id','=','att.attribute_id')
                                ->leftjoin('product_attributes as pro_att','pro_att.attribute_id','=','att_map.attribute_id')
                              ->select('att.name','att.attribute_id','att_map.attribute_set_id','att_map.attribute_group_id','pro_att.value')
                              ->where('att_map.attribute_set_id','=',$att_set_id)
                              ->groupby('att_map.attribute_id')
                              ->get()->all();
                   
                return $productAttributInfo;
            }
            else
            {
                return "false";
            }
         
            
        
    }
    public function addFreeBieProducts($data)
    {
      DB::enablequerylog();
      UserActivity::userActivityLog("Products", array($data), "Freebie has been added/ updated in product page", "", array("Product_id" => $data['main_product_id']));
      $product_id= $data['main_product_id'];
      $freebie_id=$data['freebie_id'];
      $freebie_description= $data['freebie_product_description'];
      $freebie_mpq= $data['freebie_mpq'];
      $freeBieProduct_id= $data['freeBieProduct_id'];
      $freeBieQty= $data['freeBieQty'];
      $enableStockLimit= isset($data['enable_stock_limit'])?1:0;
      
      $freeBiestate= $data['freebie_state_id'];
      $free_warehouse=$data['freebie_warehouse_id'];
      $time1 = strtotime($data["freebie_start_date"]);
      $time2 = strtotime($data["freebie_end_date"]);

      $freebie_start_date = date('Y-m-d',$time1);
      $freebie_end_date = date('Y-m-d',$time2);
      $stock_limit= isset($data['freebie_stock_limit'])?$data['freebie_stock_limit']:0;
        $url=env('APP_TAXAPI');
        $data['product_id'] = (int)$freeBieProduct_id;
        $data['buyer_state_id'] = '4033';
        $data['seller_state_id'] = '4033';
        $taxData =$this->sendTaxRequest($url,$data);
      $dataArray=['main_prd_id' =>$product_id,'mpq'=>$freebie_mpq,'free_prd_id'=>$freeBieProduct_id,'qty'=>$freeBieQty,'stock_limit'=>$stock_limit,'state_id'=>$freeBiestate,'le_wh_id'=>$free_warehouse,'start_date'=>$freebie_start_date,'end_date'=>$freebie_end_date,'is_stock_limit'=>$enableStockLimit,'freebee_desc'=>$freebie_description];
      if($taxData=='1') 
      {
        return 'false'; 
      }
      else
      {
        if($freebie_id=="")
        {
           $rs= DB::table('freebee_conf')
                  ->insert(['main_prd_id' =>$product_id,'mpq'=>$freebie_mpq,'free_prd_id'=>$freeBieProduct_id,'qty'=>$freeBieQty,'stock_limit'=>$stock_limit,'state_id'=>$freeBiestate,'le_wh_id'=>$free_warehouse,'start_date'=>$freebie_start_date,'end_date'=>$freebie_end_date,'is_stock_limit'=>$enableStockLimit,'freebee_desc'=>$freebie_description]);
        }else
        {
         $rs=DB::table('freebee_conf')->where('free_conf_id',$freebie_id)->update($dataArray);
        }
        DB::table('product_attributes')->where('attribute_id',6)->where('product_id',$product_id)->update(['value'=>'Consumer Pack Outside']);
        DB::table('products')->where('product_id',$product_id)->update(['kvi'=>69001]);
        echo $rs;
      }     
      

      
    }
    static public function sendTaxRequest($url, $data) {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                //CURLOPT_PORT => "3100",
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                ),
            ));
            $response = curl_exec($curl);
			$response = json_decode($response, true);
			$ResponseBody = $response['ResponseBody'];
			
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {

            } else {
                //$response = json_decode($response, true);
                if (is_array($ResponseBody)) {
                    //if ($response['ResponseBody'] == 'Product is Non-Sellable and Freebie') {
					if($response['Status']!=200)
					{
						return 1;
					}
                    //} else {
                    //    return $response['ResponseBody'];
                    //}
                } else {
						return $response['ResponseBody'];
                }
                return $response;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getFreebieProducts($legal_entity_id,$att_id,$product_id)
    {
      $valueArray=array('freebie','Consumer Pack Outside');
     
       return DB::table('products as pro')
                ->join('product_attributes as pro_att','pro_att.product_id','=','pro.product_id')
                ->select('pro.product_title','pro.product_id','pro.sku')
                ->where('legal_entity_id',$legal_entity_id)
                ->where('pro_att.attribute_id','=',$att_id)
                ->where('pro.product_id','!=',$product_id)
                ->where('pro.kvi','=','69010')
                ->where('pro_att.value','=','Freebie')
                ->whereIn('pro_att.value',$valueArray)
                ->get()->all();

    }
    public function categoryLoopLink($category_id)
    {
        $cat =DB::table('categories')
            ->where('categories.category_id', $category_id)
            ->where('is_active','1')
            ->get()->all();

        if (!empty($cat)) 
        {
            $html_code=' >> ';
            foreach($cat as  $cat1)
            {                
                $this->categoryLoopLink($cat1->parent_id);
                $this->categoryList.= $cat1->cat_name.$html_code;
            }
        }
        return   $this->categoryList;
    }
    public function getUserName($user_id)
    {
      $userName=DB::table('users')->select(DB::raw('GetUserName('.$user_id.',2) as name'))->first();
      $userName=json_decode(json_encode($userName->name),1);
      return $userName;
    }
    public function checkProductAttributeSet($pid)
    {
      $duplicate_pid = $this->where("product_id",$pid)->pluck("duplicate_from")->all();
      $duplicate_pid = json_decode(json_encode($duplicate_pid),true);
      if(!empty($duplicate_pid) && $duplicate_pid[0] > 0)
      {
        $dup_id = $duplicate_pid[0];
        $get_att_set_id = DB::table("product_attributes")
                      ->where("product_id",$dup_id)
                      ->select("attribute_set_id")
                      ->first();
        $get_att_set_id = json_decode(json_encode($get_att_set_id),true);
        if(!empty($get_att_set_id))
        {
          $att_set_id = $get_att_set_id['attribute_set_id'];
          $check_att_set_id = DB::table("product_attributes")
                            ->where("product_id",$pid)
                            ->select("attribute_set_id")
                            ->first();

          $check_att_set_id = json_decode(json_encode($check_att_set_id),true);
         if($check_att_set_id['attribute_set_id']=="")
         {
           $pro_att = DB::table("product_attributes")
                    ->where("product_id",$pid)
                    ->update(array("attribute_set_id"=>$att_set_id));
         }
        }
      }
    } 
}
