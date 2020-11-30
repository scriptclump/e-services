<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use App\Modules\Roles\Models\Role;
use \App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Controllers\accountController;


class HomeModel extends Model
{
    
    public function __construct()
    {
	/* this method is missing and again added*/	
    $this->_role = new Role(); 
    $this->_roleRepo = new RoleRepo(); 
    } 
     /*
    * Function name: valAppidToken
    * Description: used to validate customer id details
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 1st July 2016
    * Modified Date & Reason:
    */
    public function valAppidToken($token)
    {
        
        $data['token_status'] = 0;
            
            $result1 = DB::table('users')
                        ->select(DB::raw('*'))
                         ->where('password_token', '=', $token)
                        ->get()->all();

             if(count($result1)>0)
            {
                $data['token_status'] = 1;
            }   
            
            
            return $data;


    }


     /*
    * Function name: getcustomerId
    * Description: based on  customer token, we are getting customer id 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 7th July 2016
    * Modified Date & Reason:
    */

public function getcustomerId($token)
    {
                           
            $result = DB::table('users')
                        ->select(DB::raw('user_id'))
                         ->where('password_token', '=', $token)
                        ->get()->all();       

                if(empty($result)) 
              {
                 $customerId=0;
              } 

              else
              {

                $data = json_decode(json_encode($result[0]),true);
                $customerId=$data['user_id'];  
                 
              }               
                   
                    return $customerId;


    }    


    /*
     * Function name: ShopbyBrand
     * Description: the function is used to display  topBrands for homepage
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 24th Aug 2016
     * Modified Date & Reason:
     */

    public function ShopbyBrand($le_wh_id, $segmentId, $offset_limit, $offset, $blockedList, $customer_type) {

        $brands = isset($blockedList['brands']) ? $blockedList['brands'] : 0;
        if (is_array($brands)) {
            $brands = implode(',', $brands);
        }
        $ShopbyBrand = DB::select("CALL getCPBrands_ByCust('" . $le_wh_id . "',$segmentId,$offset_limit,$offset,'" . $brands . "',$customer_type)");
        if (empty($ShopbyBrand)) {
            $data = '0';
        } else {
            $topBrands = json_decode(json_encode($ShopbyBrand), true);
            for ($i = 0; $i < count($topBrands); $i++) {
                $data[$i]['id'] = $topBrands[$i]['id'];
                $data[$i]['name'] = $topBrands[$i]['name'];
                $data[$i]['image'][0] = $topBrands[$i]['image'];
                $data[$i]['is_sponsered'] = $topBrands[$i]['is_sponsered'];
                $data[$i]['config_id'] = $topBrands[$i]['config_id'];
            }
        }
        return $data;
    }

    /*
     * Function name: Shopbycategory
     * Description: the function is used to display servicable categories for homepage
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 24th Aug 2016
     * Modified Date & Reason:
     */

    public function ShopbyCategory($le_wh_id, $segmentId, $offset_limit, $offset, $customer_type) {


        $Shopbycategory = DB::select("CALL getCPCategories_ByCust('" . $le_wh_id . "',$segmentId,$offset_limit,$offset,$customer_type)");

        if (empty($Shopbycategory)) {
            $data = '0';
        } else {
            $Shopbycategory = json_decode(json_encode($Shopbycategory), true);
            for ($i = 0; $i < count($Shopbycategory); $i++) {
                $data[$i]['id'] = $Shopbycategory[$i]['id'];
                $data[$i]['name'] = $Shopbycategory[$i]['name'];

                if ($Shopbycategory[$i]['image'] == null) {

                    $data[$i]['image'][0] = "http://s328.photobucket.com/user/mailebutor/media/Haldirams/BHUJIA%20SEV%201KG_zpsui8fyjds.jpg.html";
                } else {
                    $data[$i]['image'][0] = $Shopbycategory[$i]['image'];
                }
            }
        }
        return $data;
    }

    /*
     * Function name: ShopbyManufacturer
     * Description: the function is used to display servicable categories for homepage
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 24th Aug 2016
     * Modified Date & Reason:
     */

    public function ShopbyManufacturer($le_wh_id, $segmentId, $offset_limit, $offset, $blockedList, $customer_type) {

        $manf = isset($blockedList['manf']) ? $blockedList['manf'] : 0;
        if (is_array($manf)) {
            $manf = implode(',', $manf);
        }

        $Manufacturer = DB::select("CALL getCPManufactuers_ByCust('" . $le_wh_id . "',$segmentId,$offset_limit,$offset,'" . $manf . "',$customer_type)");


        if (empty($Manufacturer)) {
            $data = '0';
        } else {
            $ShopbyManufacturer = json_decode(json_encode($Manufacturer), true);

            for ($i = 0; $i < count($ShopbyManufacturer); $i++) {
                $data[$i]['id'] = $ShopbyManufacturer[$i]['id'];
                $data[$i]['name'] = $ShopbyManufacturer[$i]['name'];
                $data[$i]['image'][0] = $ShopbyManufacturer[$i]['image'];
            }
        }
        return $data;
    }


    public function ShopbyCustomPacks($le_wh_id, $segmentId, $offset_limit, $offset, $customer_type) {


        $ShopbyCustomPacks = DB::select("CALL getCPCusPacks_ByCust('" . $le_wh_id . "',$segmentId,$offset_limit,$offset,$customer_type)");
        if (empty($ShopbyCustomPacks)) {
            $data = '0';
        } else {
            $ShopbyCustomPacks = json_decode(json_encode($ShopbyCustomPacks), true);
            for ($i = 0; $i < count($ShopbyCustomPacks); $i++) {
                $data[$i]['id'] = $ShopbyCustomPacks[$i]['id'];
                $data[$i]['name'] = $ShopbyCustomPacks[$i]['name'];
                $data[$i]['products'] = $ShopbyCustomPacks[$i]['product_id'];

                if ($ShopbyCustomPacks[$i]['image'] == null) {

                    $data[$i]['image'][0] = "http://s328.photobucket.com/user/mailebutor/media/Haldirams/BHUJIA%20SEV%201KG_zpsui8fyjds.jpg.html";
                } else {
                    $data[$i]['image'][0] = $ShopbyCustomPacks[$i]['image'];
                }
            }
        }
        return $data;
    }
    /*
    * Class Name: getBanner
    * Description: the function is used to display product Banners  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 7th July 2016
    * Modified Date & Reason: 
     
    */  

    public function getBanner($data)
    {
          
          $le_wh_ids = "'".$data['le_wh_id']."'";
          $query = "CALL getBannerPopups($le_wh_ids,16601)";
         $result = DB::select(DB::raw($query));

        
        return $result;


    }

    public function getPopups($data)
    {
          

         $le_wh_ids = "'".$data['le_wh_id']."'";
          $query = "CALL getBannerPopups($le_wh_ids,16602)";
         $result = DB::select(DB::raw($query));


        
        return $result;


    }

    /*
    * Class Name: getSortingData
    * Description: the function is used to sort by tabs   
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 7th July 2016
    * Modified Date & Reason: 
     
    */ 

     public function getSortingData($lookupCategory){

        $result= DB::table('master_lookup as ml')
                        ->select(DB::raw('ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value'))
                        ->leftJoin('master_lookup_categories as mlc','mlc.mas_cat_id','=','ml.mas_cat_id')
                        ->where('ml.is_active','=','1')
                        ->where('ml.mas_cat_id','=',$lookupCategory)
                        ->orderBy('ml.sort_order')
                        ->get()->all();

       
          return $result;          
   }   


/*
    * Class Name: getSortingDataFilter
    * Description: the function is used to sort data fileds
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26 July 2016
    * Modified Date & Reason: 
     
    */ 

     public function getSortingDataFilter(){

            $result= DB::table('master_lookup as ml')
            ->select(DB::raw('ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value as sort_id'))
            ->leftJoin('master_lookup_categories as mlc','mlc.mas_cat_id','=','ml.mas_cat_id')
            ->where('mlc.is_active','=','1')
            ->where('ml.mas_cat_id','=',65)
            ->get()->all();
          return $result;              


   }   



/*
    * Class Name: versioncheck
    * Description: the function is used to sort data fileds
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 02 Aug 2016
    * Modified Date & Reason: 
     
    */ 

     public function versioncheck($number,$type){

         $result= DB::table('app_version_info')
            ->select(DB::raw('version_number as number,app_type as type'))
            ->where('app_type','=',$type)
            ->where('version_number','>',$number)           
            ->get()->all();
         
   
          return $result;              


   } 

   
  
/*
    * Class Name: getBeatsbyffID
    * Description: the function is used to sort data fileds
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 26 OCt 2016
    * Modified Date & Reason: 
     
    */ 

  public function beatsbyffID($sales_token,$limit,$offset){
    $account = new accountController();
    $usersData=$account->getDataFromToken(1,$sales_token,['user_id','legal_entity_id']);

    if(!isset($usersData->user_id))
      return ["beats" => -1];
    // We check the Permission, to access the All Beats 
    $allBeatsAccess = $this->_roleRepo->checkPermissionByFeatureCode('ALLBEAT1',$usersData->user_id);
    $flag = 0;
    if($allBeatsAccess) $flag = 1;

    // Removed all the Old Code, as its shifted to new home, called Database.
    // From here after the raw query had moved to the Database Procedure
    $beatsData = DB::SELECT('CALL getBeatDetails(?,?,?,?,?,?)',[
      $usersData->user_id,
      $usersData->legal_entity_id,
      NULL, // Hub ID
      $flag, // Flag 0 -> Normal Access, Flag 1 -> Full Access
      $limit,
      $offset
    ]);

    return ["beats" => $beatsData];
  } 
   
   
 /*
    * Class Name: unBilledskus
    * Description: the function is used to share sku products based on  billed and not billed
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 4 Nov 2016
    * Modified Date & Reason: 
     
    */ 

     public function unBilledskus($data){
  
         if(isset($data['start_date']) && $data['start_date']!='') {
               $start_date=$data['start_date'];
               $end_date=$data['end_date'];
         }else {
               $start_date=date('Y-m-d');
               $end_date=date('Y-m-d');
         } 

         $id=(isset($data['id']) && $data['id']!='')? $data['id']:0;
         $beat_id=(isset($data['beat_id']) && $data['beat_id']!='')? $data['beat_id']:0;
         $flag=(isset($data['flag']) && $data['flag']!='')? $data['flag']:0;
         $sort_id=(isset($data['sort_id']) && $data['sort_id']!='')? $data['sort_id']:0;
         $cust_type=(isset($data['customer_type']) && $data['customer_type']!='')? $data['customer_type']:0;

        

         $result=DB::select("CALL getSKUSByffid_ByCust('".$id."','".$data['ff_id']."','".$data['offset']."','".$data['offset_limit']."','".$sort_id."','".$data['is_billed']."','".$start_date."','".$end_date."',$flag,'".$beat_id."',$cust_type)"); 
            

          if(!empty($result[0]->product_id)){

              $final_result['product_id']=$result[0]->product_id;

              $final_result['count']=count(explode(",",$result[0]->product_id));
          }
          else{
               
              $final_result['product_id']='';
              $final_result['count']=''; 

          }          

         return $final_result;  

   } 



    /*
    * Function Name: InsertDeviceDetails
    * Description: the function is used to  insert and update device details  
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 25th Jan 2017
    * Modified Date & Reason: 
     
    */ 

public function InsertDeviceDetails($user_id,$device_id,$ip_address,$platform_id,$reg_id){

$var="insert into device_details 
  (user_id,device_id,app_id,ip_address,registration_id,platform_id,created_at,updated_at)
 values 
 ('".$user_id."','".$device_id."','0','".$ip_address."','".$reg_id."','".$platform_id."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')
  on duplicate key update 
  user_id='".$user_id."',ip_address='".$ip_address."',registration_id='".$reg_id."',platform_id='".$platform_id."', updated_at='".date("Y-m-d H:i:s") . "'";

//echo $var;exit;
$result=DB::statement($var);
       
          return $result;          
   }

  public function checkValidRelation($salesUserId,$customerLegalEntityId)
  {
    if($salesUserId != ""){
      // Earlier, we wrote query here, but now, it had shifted 
      // to his new home (database procedure)
      $query = 'CALL getFFRetailerCheckIn(?,?)';

      $validFFRelation = DB::SELECT($query,[$salesUserId,$customerLegalEntityId]);

      if(isset($validFFRelation[0]->AGGREGATE) and $validFFRelation[0]->AGGREGATE > 0)
        return ["status" => "success", "data" => []];
    
    }else{
      # For Self Orders #
      $query = 'SELECT 
        dhm.dc_id,
        dhm.hub_id
      FROM
        retailer_flat AS rf 
        LEFT JOIN wh_serviceables AS wh 
          ON wh.pincode = rf.pincode 
          AND wh.legal_entity_id = rf.parent_le_id
        JOIN legalentity_warehouses AS lw
          ON lw.legal_entity_id = rf.parent_le_id
          AND rf.hub_id = lw.le_wh_id 
        LEFT JOIN dc_hub_mapping AS dhm 
          ON dhm.hub_id = rf.hub_id
      WHERE rf.legal_entity_id = ?';

      $validSFRelation = DB::selectFromWriteConnection($query,[$customerLegalEntityId]);

      if(!empty($validSFRelation))
        return ["status" => "success", "data" => $validSFRelation];
    }
    $data= array('display' => 0 );

    return ["status" => "failed", "message" => "Improper Dc and Hub Configuration for the retailer or field force", "data" => $data];
  }  
  public function getFFPincode($id){
    $today = date("D");
    $pincode=DB::select(DB::raw("select default_pincode as pincode,pjp_pincode_area_id,pjp_name from pjp_pincode_area where default_pincode is not null and days like '%".$today."%' and  rm_id=".$id));
    return $pincode;
  }
  public function updateRetailerPincodeData($data){

    $parent_le=DB::select(DB::raw("select legal_entity_id FROM users WHERE user_id=".$data['ff_id']));
    $result=DB::update("update retailer_flat set parent_le_id =".$parent_le[0]->legal_entity_id." where legal_entity_id=".$data['legal_entity_id'] ." and legal_entity_type_id not in (1014,1016)");
    $legalUpdate=DB::update("update legal_entities set parent_le_id=".$parent_le[0]->legal_entity_id." where legal_entity_id=".$data['legal_entity_id']." and legal_entity_type_id not in (1014,1016)");


    return $parent_le[0]->legal_entity_id;
  }
  public function shopbyNewsku($le_Wh_id,$limit,$offset)
  {
    $data = DB::select(DB::raw("select DISTINCT(p.product_id)  AS id,product_title AS `name`,thumbnail_image AS image 
            FROM products_inventory_flat p
            JOIN inventory i
            ON p.product_id=i.product_id
            JOIN product_slab_flat ps
            ON ps.product_id=i.product_id
            JOIN product_slab_flat pf 
            ON pf.`product_id`= p.product_id AND pf.`wh_id` IN ($le_Wh_id)
            JOIN product_cpenabled_dcfcwise pce
            ON pce.product_id=i.product_id
            JOIN new_sku
            ON new_sku.`le_wh_id`=i.`le_wh_id` AND new_sku.`product_id`=i.`product_id`
            AND CURDATE() BETWEEN new_sku.`from_date` AND new_sku.`to_date`
            AND pce.le_wh_id IN ($le_Wh_id)
            WHERE i.le_wh_id IN ($le_Wh_id) 
            AND i.soh-(i.order_qty+i.reserved_qty)>0 AND pce.is_sellable=1 AND pce.cp_enabled=1 LIMIT $limit OFFSET $offset"));
    $new_sku=array();
    foreach($data as $product){
      $sku_det = array();
      $sku_det['id'] = $product->id;
      $sku_det['name'] =$product->name;
      $sku_det['image'][0] = $product->image;
      $new_sku[] = $sku_det;
    }
    return $new_sku;
   
  }

}
