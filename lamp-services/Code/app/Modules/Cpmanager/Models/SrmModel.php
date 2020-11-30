<?php
  namespace App\Modules\Cpmanager\Models;
  use \DB;
  use App\Modules\Supplier\Models\SupplierModel;
  
  
  
 
  class SrmModel extends \Eloquent {
      

    public function __construct() {
       // $this->Review = new Review();
      }  

 /*
            * Function name: getPurchaseSlabs
            * Description: getCategories function is used to handle the request of getting all the categories  and give
            response along with the productNo, segment_id and names if no category_id is passed. It returns the products of the category if the categiry_id is passed..
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 24 June 2016
            * Modified Date & Reason:
        */

    public function getPurchaseSlabs($product_id,$le_wh_id,$supplier_id ){
        DB::enableQuerylog();
           
         $data = DB::select("CALL getPurchaseSlabs($product_id,$le_wh_id,$supplier_id)");
         //print_r(DB::getQuerylog());exit;
         return $data;


    }
    
     /*
            * Function name: getPurchasepricehistory
            * Description: getCategories function is used to handle the request of getting all the categories  and give
            response along with the productNo, segment_id and names if no category_id is passed. It returns the products of the category if the categiry_id is passed..
            * Author: Ebutor <info@ebutor.com>
            * Copyright: ebutor 2016
            * Version: v1.0
            * Created Date: 24 June 2016
            * Modified Date & Reason:
        */
   
    public function getPurchasepricehistory($le_wh_id,$product_id,$supplier_id){
   
      $f = explode(",", $le_wh_id);
     $data['result_val'][0]= DB::table('purchase_price_history as pu')
                ->select(DB::raw("MIN(pu.elp) as min,MAX(pu.elp) as max,(SUM(pu.elp)/COUNT(pu.elp)) as avarage"))
              ->whereIn('pu.le_wh_id',$f);
                //->get()->all();
   
         
       $result = DB::table('purchase_price_history as pu')
                ->select(DB::raw("getProductName(pu.product_id) AS product_name,"
                        . "pu.elp AS price,getManfName(pu.supplier_id) AS supplier_name,"
                        . "pu.effective_date,pu.product_id"))
               ->whereIn('pu.le_wh_id', $f)
               ->groupBy('pu.product_id','pu.effective_date');
            
            
     
          if(!empty($product_id))
          {
             $last_result= $result->where('pu.product_id','=',$product_id);
              $last_result2= $data['result_val'][0]->where('pu.product_id','=',$product_id);
              
          }else{
              
              $last_result= $result;
              $last_result2= $data['result_val'][0];
              
          }   
        
          if(!empty($supplier_id))
          {
             $last_result= $result->where('pu.supplier_id','=',$supplier_id);
             $last_result2= $data['result_val'][0]->where('pu.supplier_id','=',$supplier_id);
          }else{
              
              $last_result= $result;
              $last_result2= $data['result_val'][0];
              
          } 

     //return $last_result->get()->all();
       
     $data['history'] =$last_result->get()->all();
     $data['history2'] =$last_result2->get()->all();
    
       return $data;

    }

     /*
* Function Name: getWarehouseList
* Description: getWarehouseList function is used to get all the warehouses
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 Sep 2016
* Modified Date & Reason:
*/
public function getWarehouseList()
  {

  $legal_entity_id = $this->getLegalEntityId();


  $warehouse = DB::table('legalentity_warehouses as lw')
           ->select(DB::raw("lw.le_wh_id,lw.lp_wh_name"))
           ->where("lw.legal_entity_id", "=",  $legal_entity_id)
           ->where("lw.status", "=",1)
           ->where("lw.dc_type", "=",118001)
           ->get()->all();

  return $warehouse;

  }



/*
* Function Name: getManufacturerList
* Description: getManufacturerList function is used to get all the manufacturer
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22 Nov 2016
* Modified Date & Reason:
*/
public function getManufacturerList()
  {

  $manufacturer = DB::table('legal_entities as le')
           ->select(DB::raw("le.business_legal_name,le.legal_entity_id"))
         ->where("le.legal_entity_type_id", "=", 1006)
           ->get()->all();

  return $manufacturer;

  }



/*
* Function Name: getLegalEntityId
* Description: getLegalEntityId function is used to get present legal_entity_id for cp
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22 Nov 2016
* Modified Date & Reason:
*/
public function getLegalEntityId()
  {

  $master = DB::table('master_lookup as ml')
            ->select('description')
            ->where("ml.value", "=", 78001)->get()->all();


  return $master[0]->description;

  }

/*
* Function Name: getWarehouseBySupplierId
* Description: getWarehouseBySupplierId function is used to warehouse based on supplier
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22 Nov 2016
* Modified Date & Reason:
*/
 public function getWarehouseBySupplierId($supplierId) {
        try{
          
            $fieldArr = array('lewh.lp_wh_name', 'lewh.city', 'lewh.address1', 
                 'lewh.pincode', 'lewh.le_wh_id');
            $query = DB::table('legalentity_warehouses as lewh')->select($fieldArr);
            $query->leftJoin('product_tot as lewhmap','lewhmap.le_wh_id','=','lewh.le_wh_id');
            $query->leftJoin('legal_entities as le','le.legal_entity_id','=','lewhmap.supplier_id');
            $query->where("lewh.status", "=",1);
            $query->where("lewh.dc_type", "=",118001);
            $query->groupBy('lewhmap.le_wh_id');
           
            if(!empty($supplierId))
             { 
            $query->where('le.legal_entity_id', $supplierId);
            }
            
            return $query->get()->all();
        }
        catch(Exception $e) {

              return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
   
        }         
    }


/*
* Function Name: getWarehouseBySupplierId
* Description: getWarehouseBySupplierId function is used to warehouse based on supplier
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22 Nov 2016
* Modified Date & Reason:
*/
 public function getManufacturerProducts($manufacturerId) {
        try{
          
          $query= DB::table('products as prod')
                   ->select(DB::raw("group_concat(distinct prod.product_id  separator ',')as product_id"))
                   ->where("prod.manufacturer_id", "=",  $manufacturerId)
                   ->get()->all();


            return $query[0];
        }
        catch(Exception $e) {

              return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
   
        }         
    }

/*
* Function Name: getSupplierList
* Description: getSupplierList function is used to get all the supplier list based on user
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 Sep 2016
* Modified Date & Reason:
*/
public function getSupplierList($offset,$offset_limit)
  {

//db::enablequerylog();
  $supplier = DB::table('legal_entities as le')
           ->select(DB::raw("le.business_legal_name AS supplier_name,
              le.address1,le.address2,
              u.mobile_no AS telephone,
                            le.business_type_id,le.city,le.pincode,le.legal_entity_id AS supplier_id"))
           ->join('users as u','le.legal_entity_id','=','u.legal_entity_id')
         ->where("le.legal_entity_type_id", "=",1002)
         ->where("le.is_approved", 1);
         //->whereIn("le.rel_manager",$users);

   if($offset>0 && $offset_limit>0)
   {       
      $result=$supplier  
             ->skip($offset)
               ->take($offset_limit)
               ->get()->all();
    }else{
      
          $result=$supplier  
               ->get()->all();

    }       


  return $result;

  }

/*
* Function name: getInventory
* Description: function used to get inventory
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28th Sept 2016
* Modified Date & Reason:
*/
 public function getInventory($le_wh_id,$product_id) {
        
        $result = DB::table('inventory')
                    ->select(DB::raw('soh,atp,order_qty,inv_display_mode'))
                    ->where('le_wh_id', '=', $le_wh_id)
                    ->where('product_id', '=', $product_id)
                    ->get()->all();

              if(empty($result)) {

               $data[0]=array(
                'key'=>'soh',
                'value'=>0
               );
               $data[1]=array(
                'key'=>'atp',
                'value'=>0
               );
               $data[2]=array(
                'key'=>'order_qty',
                'value'=>0
               );
               $data[3]=array(
                'key'=>'available_inventory',
                'value'=>0
               );


              } else{

               $data[0]=array(
                'key'=>'soh',
                'value'=>$result[0]->soh
               );
               $data[1]=array(
                'key'=>'atp',
                'value'=>$result[0]->atp
               );
               $data[2]=array(
                'key'=>'order_qty',
                'value'=>$result[0]->order_qty
               );

              
                 $displaymode= $result[0]->inv_display_mode;
                 

                  $query=DB::table('inventory')
                  ->select(DB::raw('('.$displaymode.'-(order_qty+reserved_qty)) as availQty'))
                  ->where('product_id', '=', $product_id)
                  ->where('le_wh_id', '=', $le_wh_id)
                  ->get()->all();


               $data[3]=array(
                'key'=>'available_inventory',
                'value'=>$query[0]->availQty
               );

      
              }             
        
                
         return $data;
    
  }

  /*
* Function Name: getSupplierProductLists
* Description: getSupplierProductLists function is used to get all product_id based on supplier and warehouse
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28 Sep 2016
* Modified Date & Reason:
*/

public function getSupplierProductLists($le_wh_id,$supplier_id)
  {

  $supplier_prod = DB::table('product_tot as pt')
           ->select(DB::raw("group_concat(distinct pt.product_id  separator ',')as product_id,COUNT(pt.product_id) AS  count "))
         ->where("pt.le_wh_id", "=", $le_wh_id)
         ->where("pt.supplier_id", "=",$supplier_id)
		 ->where("pt.subscribe", "=",1)
           ->get()->all();

  return $supplier_prod[0];

  }


  /*
  * Function Name: getSrmProducts()
  * Description: Used to get product list based on supllier
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 2nd Nov 2016
  * Modified Date & Reason:
  */


  public function getSrmProducts($product_ids){

    $t = '@total';

   $result['data']=DB::select("CALL  getSrmProducts($product_ids,$t)");

   $catTotal = DB::select("SELECT @total");
        
  //$result['TotalProducts'] = $catTotal[0]->$t;
   $result['TotalProducts'] = count($result['data']);
  return $result;

  
}

/*
  * Function Name: getUserIdLegalentityID()
  * Description: Used to get user_id & legal_entity_id based on supllier_token
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 22nd Nov 2016
  * Modified Date & Reason:
  */



public function getUserIdLegalentityID($token){

  $query = DB::table("users as u")
      ->select("user_id","legal_entity_id")
      ->where("u.password_token","=",$token)   
      ->get()->all();
      
      return $query; 



 }

 /*
  * Function Name: getSupplierMasterlookupdata($user_id)
  * Description: Used to get supplier data
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 22nd Nov 2016
  * Modified Date & Reason:
  */

 public function getSupplierMasterlookupdata($user_id){


  $Organization_type_data = DB::table("master_lookup")
    ->select("master_lookup_name","value")
    ->where("mas_cat_id","=",47)   
    ->get()->all();


    $data['organization_type']=$Organization_type_data; 


    $supplier_type_data = DB::table("master_lookup")
    ->select("master_lookup_name","value")
    ->where("mas_cat_id","=",89)   
    ->get()->all();


    $data['supplier_type']=$supplier_type_data;  


    $supplier_rank_data = DB::table("master_lookup")
    ->select("master_lookup_name","value")
    ->where("mas_cat_id","=",99)   
    ->get()->all();


    $data['supplier_rank']=$supplier_rank_data;

     #  Relation Managers   
     $SupplierModel = new SupplierModel();

     $data['relation_manager_list']= $SupplierModel->getRm($user_id);
      
      return $data; 



 }




/*
* Function Name: getManufacturerSubscribeProducts
* Description: getManufacturerSubscribeProducts function is used to get all subscribed and unsubscribed products
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22 Nov 2016
* Modified Date & Reason:
*/
 public function getManufacturerSubscribeProducts($array) {
        try{
          
          $query= DB::table('products as prod')
                   ->select(DB::raw(" prod.product_id,prod.product_title,prod.sku,prod.primary_image,
                    getprdSubscriptionStaus(".$array['supplier_id'].",prod.product_id,".$array['le_wh_id'].") as flag"))
                   ->where("prod.manufacturer_id", "=",  $array['manufacturer_id'])
                   ->get()->all(); 
      return $query;
          
        }
        catch(Exception $e) {

              return Array('status' => "failed", 'message' => $e->getMessage(), 'data' =>"");
   
        }         
    }

  }

