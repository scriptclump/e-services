<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Cpmanager\Views\order;
use App\Central\Repositories\CustomerRepo;
use DB;
use Log;
use views;
use view;
use Config;
use Cache;



class OrderModel extends Model
{
     
/*
* Function name: valAppidToken
* Description: used to validate Appid & customer id details
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 1st July 2016
* Modified Date & Reason:
*/
 public function validateToken($token) {
    
    $data['token_status'] = 0;
        
        $result1 = DB::table('users')
                    ->select(DB::raw('user_id'))
                    ->where('password_token', '=', $token)
                    ->useWritePdo()
                    ->get()->all();

         if(count($result1)>0)        
         $data['token_status'] = 1;
                
         return $data;
}


/*
* Function name: isFreebie
* Description: to Check whether the product is freebies or not
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 23rd June 2017
* Modified Date & Reason:
*/

public function isFreebie($product_id = null)
{
  $count = DB::table('freebee_conf')
      ->where('free_prd_id',$product_id)
      ->count();
  if($count > 0) return 1;
  return 0;
}

/*
* Function name: getcustomerId
* Description: based on  customer token, we are getting customer id 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 15thth July 2016
* Modified Date & Reason:
*/

public function getcustomerId($token)
{
   // print_r($token);exit;
                
        $result = DB::table('users')
                    ->select(DB::raw('user_id'))
                     ->where('password_token', '=', $token)
                     ->useWritePdo()
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
* Function name: valSalesToken
* Description: used to validate sales token  details
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 20th Aug 2016
* Modified Date & Reason:
*/
 public function valSalesToken($sales_token) {
    
    $data['sales_token'] = 0;
        
        $result1 = DB::table('users as us')
                    ->select(DB::raw('us.user_id'))
                    ->leftJoin('legal_entities as le','le.legal_entity_id','=','us.legal_entity_id')
                    ->where('us.password_token', '=', $sales_token)
                    ->where('le.legal_entity_type_id', '=','1007')
                    ->get()->all();

         if(count($result1)>0)        
         $data['sales_token'] = 1;

       //print_r($data);exit;
                
         return $data;
}

/*
* Function name: isDiscountApplicable   
* Description: function used to validate the Discounts before placing the Order   
* Author: Ebutor <info@ebutor.com>    
* Copyright: ebutor 2017    
* Version: v1.0   
* Created Date: 22st June 2017    
* Modified Date & Reason:
    20-jul-17: Update Discount Validation   
*/    
public function isDiscountApplicable($discount,$discountOn,$discountType,$discountOnValues = null)   
{   
 // Log::info("isDiscountApplicable ".$discount.", ".$discountOn.", ".$discountType.", ".$discountOnValues);
  //DB::enableQueryLog();
  // $today = date("Y-m-d H:i:s");
  $isDiscount = 0;
  if($discountOnValues == null)
  {
    $result =  
      DB::select(
        'select discount from `customer_discounts` where
          (`discount_on` = ? and 
           `discount_type` = ? and 
           NOW() BETWEEN 
           `discount_start_date` and 
           `discount_end_date`
          ) limit 1',array($discountOn,$discountType)
           );
      $result = isset($result[0])?$result[0]:$result;
  }
  else
  {
    $result = 
      DB::select(
        'select discount from `customer_discounts` where
          (`discount_on` = ? and 
           `discount_on_values` = ? and 
           `discount_type` = ? and 
           NOW() BETWEEN 
           `discount_start_date` and 
           `discount_end_date`
          ) limit 1',array($discountOn,$discountOnValues,$discountType)
           );

      $result = isset($result[0])?$result[0]:$result;
  }
  //Log::info(DB::getQueryLog());
  if(isset($result->discount) and ($result->discount != $discount))
  {
    //Log::info("disc False");
    //Log::info($result->discount);
    //Log::info($discount);
    return false;
  }

  if(!isset($result->discount) and $discount != null )
  {
    //Log::info("no value seting");
    return false;
  }
  return true; 
}
/*
* Function name: Cartdetails
* Description: function used to get cart details of respective customer
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 2nd Aug 2016
* Modified Date & Reason:
*/

public function Cartdetails($data)
{  

   $cart=DB::table('cart as c')
                ->select(DB::raw('c.user_id,c.product_id,c.rate,p.mrp,c.quantity,c.total_price as prodtotal,p.sku'))
                ->leftJoin('users as us','us.user_id','=','c.user_id')
                ->leftJoin('products as p','p.product_id','=','c.product_id')
                ->where('us.password_token', '=', $data['customer_token'])
                ->get()->all();

                if(empty($cart)) {
                      print_r(json_encode(array('status'=>"failed",'message'=> "Cart is empty ",'data'=>[]))); die;

                }
             
                return $cart;
}

public function Cartdetails_new($data) {
   $cart=DB::table('cart as c')
                ->select(DB::raw('c.cart_id,c.user_id,c.product_id,c.rate,
                p.mrp,c.quantity,c.dit_quantity,c.total_price as prodtotal,
                p.sku,c.le_wh_id,c.le_wh_id_list,c.is_slab,c.hub_id as hub,c.esu_quantity,
                c.parent_id,c.star,c.product_slab_id,c.prmt_det_id,
                c.esu,c.freebee_qty,c.freebee_mpq,c.discount_type,c.discount,c.discount_on'))
                ->leftJoin('users as us','us.user_id','=','c.user_id')
                ->leftJoin('products as p','p.product_id','=','c.product_id')
                //->where('us.password_token', '=', $data['customer_token'])
                ->useWritePdo()
                ->whereIn('c.cart_id', $data['cartId'])
                ->where('c.status', '=', 1)
                 ->GROUPBY('c.product_id') 
                ->get()->all();
                if(empty($cart)) {
                       return json_encode(array('status'=>"failed",'message'=> "Cart is empty ",'data'=>[]));
                }
                return $cart;
}

/*
* Function name: getShippingAddress
* Description: function used to get ShippingAddress details based on address id
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 2nd Aug 2016
* Modified Date & Reason:
*/

public function getShippingAddress($address_id,$legal_entity_id) {

      if(!empty($address_id)) { 
         
        $result = DB::table('legalentity_warehouses as lew')
      ->select('lew.legal_entity_id','lew.contact_name as Firstname','lew.address1 as Address','lew.address2 as Address1','lew.phone_no as telephone','lew.city as City','lew.pincode as pin','z.name as state','coun.name as country','lew.email')
      ->leftJoin('countries as coun','coun.country_id','=','lew.country')
      ->leftJoin('zone as z','z.zone_id','=','lew.state')
      ->where('lew.le_wh_id',$address_id)
      ->GROUPBY('lew.le_wh_id')
      ->get()->all();


      } else {    

      $result = DB::table('legal_entities as le')
      ->select('le.legal_entity_id','le.business_legal_name','user.firstname as Firstname','le.address1 as Address','le.address2 as Address1','user.mobile_no as telephone','le.locality','le.landmark','le.city as City','le.pincode as pin','z.name as state','coun.name as country','user.email_id as email')
      ->leftJoin('users as user','user.legal_entity_id','=','le.legal_entity_id')
      ->leftJoin('countries as coun','coun.country_id','=','le.country')
      ->leftJoin('zone as z','z.zone_id','=','le.state_id')
      ->where('le.legal_entity_id',$legal_entity_id)
      ->get()->all();
    }
      return $result;
    }  
  /*
* Function name: GetAreaID
* Description: function used to get AreaId details based on legal_entity_id
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 14 Sept 2016
* Modified Date & Reason:
*/

public function GetAreaID($legal_entity_id){
  
       $result = DB::table('customers')
      ->select('area_id')
      ->where('le_id',$legal_entity_id)
      ->get()->all();

        if(empty($result)){
              $area_id='';
        }else{
              $area_id=$result[0]->area_id;
        }
   
        return $area_id;
    }
  
/*
* Function Name: getCustomerData()
* Description: getCustomerData function is used to get  the customer data of the customer_token passed .
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22 July 2016
* Modified Date & Reason:
*/

public function getCustomerData($customer_id){
  
  $result = DB::table('users as u')
  ->select(DB::raw("u.user_id as customerId,u.firstname AS `firstname`,u.lastname AS `lastname`,`le`.`business_legal_name` AS `company`, `u`.`mobile_no` AS `telephone`,u.email_id"))
  ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
  ->where('u.user_id','=',$customer_id)
  ->useWritePdo()
  ->get()->all();
//print_r($result);exit;
  
  if(!empty($result)){
    return $result[0];  
  } else
  {
    return;
  }  
 } 
      
/*
* Function name: val_product
* Description: used to validate Product id details
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 13th July 2016
* Modified Date & Reason:
*/ 




public function val_product($para)
{

 $productid_status=1;  
            
     foreach($para as $val) {
             
         $product=DB::table('products')
                ->select(DB::raw('product_id'))
                ->where('product_id', '=', $val->product_id)
                ->get()->all();
        

            if(empty($product)) {
               $productid_status = 0;  
            }
            else {
                 $data = json_decode(json_encode($product[0]),true);
                 $productid_status= count($data) ;
            }              
        }

            return $productid_status;
}

/*
* Function name: checkInventory
* Description: used to validate quantity of product in add order 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 13th July 2016
* Modified Date & Reason:
*/ 
public function checkInventory_new($Cartdata,$segmentId)
{

        $cartdata=json_decode(json_encode($Cartdata),true);

         foreach($cartdata as $data) {

         if(($data['le_wh_id'] == 0) || empty($data['le_wh_id'])) {

                   $query=DB::table('products')
                  ->select(DB::raw('product_title'))
                  ->where('product_id', '=', $data['product_id'])
                  ->get()->all();
            $product_name=json_decode(json_encode($query[0]->product_title),true);          
                     
            print_r(json_encode(array('status'=>"failed",'message'=> "Ordered quantity is more than available quantity for the product ".$product_name."and available stock is upto 0",'data'=>[]))); die;
                
          } else {  

                $checkInventory=DB::table('inventory')
                  ->select(DB::raw('inv_display_mode'))
                  ->where('product_id', '=', $data['product_id'])
                  ->where('le_wh_id', '=', $data['le_wh_id'])
                  ->get()->all();

                 $displaymode= $checkInventory[0]->inv_display_mode;

                 // DB::enableQueryLog();  

                  $query=DB::table('inventory')
                  ->select(DB::raw('('.$displaymode.'-(order_qty+reserved_qty)) as availQty'))
                  ->where('product_id', '=', $data['product_id'])
                  ->where('le_wh_id', '=', $data['le_wh_id'])
                  ->get()->all();

               //  print_r(DB::getQueryLog()); exit;    
                 

                 $avail_quantity= $query[0]->availQty; 
              
              if(($data['quantity']) > $avail_quantity) {

                $query=DB::table('products')
                  ->select(DB::raw('product_title'))
                  ->where('product_id', '=', $data['product_id'])
                  ->get()->all();
            $product_name=json_decode(json_encode($query[0]->product_title),true);          
                     
             print_r(json_encode(array('status'=>"failed",'message'=> "Ordered quantity is more than available quantity for the product ".$product_name."and available stock is upto ".$avail_quantity,'data'=>[]))); die;

                 
              }          
          }        
}
}



public function checkInventory($Cartdata,$le_wh,$segmentId)
{

        $cartdata=json_decode(json_encode($Cartdata),true);
        foreach($cartdata as $data) {
         
         $warehouseId=  DB::select("SELECT GetCPInventoryStatus(".$data['product_id'].",'".$le_wh."',$segmentId,4) as le_wh_id ");

         $le_wh_id=$warehouseId[0]->le_wh_id;

         if(($le_wh_id == 0) || empty($warehouseId)) { 

          print_r(json_encode(array('status'=>"failed",'message'=> "Out of stock",'data'=>[]))); die;
      
          } else { 

                $checkInventory=DB::table('inventory')
                  ->select(DB::raw('inv_display_mode'))
                  ->where('product_id', '=', $data['product_id'])
                  ->where('le_wh_id', '=', $le_wh_id)
                  ->get()->all();

                 $displaymode= $checkInventory[0]->inv_display_mode;

                  if($displaymode== 'soh') { 

                   $query=DB::table('inventory')
                  ->select(DB::raw('(soh-order_qty) as availQty'))
                  ->where('product_id', '=', $data['product_id'])
                  ->where('le_wh_id', '=', $le_wh_id)
                  ->where('inv_display_mode', '=', $displaymode)
                  ->get()->all();
                 } else if($displaymode== 'atp') { 

                    $query=DB::table('inventory')
                    ->select(DB::raw('(atp-order_qty) as availQty'))
                    ->where('product_id', '=', $data['product_id'])
                    ->where('le_wh_id', '=', $le_wh_id)
                    ->where('inv_display_mode', '=', $displaymode)
                    ->get()->all();
                 } else{ 

                  $query=DB::table('inventory')
                  ->select(DB::raw('((soh+atp)-order_qty) as availQty'))
                  ->where('product_id', '=', $data['product_id'])
                  ->where('le_wh_id', '=', $le_wh_id)
                  ->where('inv_display_mode', '=', $displaymode)
                  ->get()->all();
                 }          
                 

                 $avail_quantity= $query[0]->availQty; 
              
              if(($data['quantity']) > $avail_quantity) {

                print_r(json_encode(array('status'=>"failed",'message'=> "Ordered quantity is unavailable and available stock is upto ".$avail_quantity,'data'=>[]))); die;        
              }          
          }        
}
}

/*
* Function name: getwarehouseId
* Description: used to get warehouseId of product 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 2nd Sept 2016
* Modified Date & Reason:
*/ 

public function getwarehouseId($product_id,$le_wh_id,$segmentId)
 {

   $warehouseId=  DB::select("SELECT GetCPInventoryStatus($product_id,'".$le_wh_id."',$segmentId,4) as le_wh_id ");
   return $warehouseId[0]->le_wh_id;
 }

/*
* Function name: checkInventory
* Description: used to validate quantity of product in add order 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 13th July 2016
* Modified Date & Reason:
*/ 

/*public function checkInventory1($cartdata,$le_wh_id,$segmentId)
 {
      foreach($cartdata as $data) {
        if(!empty($data->quantity)) {
 
       $inventory=  DB::select("SELECT GetCPInventoryStatus($data->product_id,'".$le_wh_id."',$segmentId,4) as inventory ");

         if(empty($inventory)){
                 $avail_quantity=0;
          } else {
                 $avail_quantity= json_decode(json_encode($inventory[0]->inventory),true);
          }

          if(($data->quantity) > $avail_quantity) {
                  $query=DB::table('products')
                  ->select(DB::raw('product_title'))
                  ->where('product_id', '=', $data->product_id)
                  ->get()->all();
            $product_name=json_decode(json_encode($query[0]->product_title),true);          
          } 
        }
      }

          if(!empty($product_name)) {                
                print_r(json_encode(array('status'=>"failed",'message'=> "Ordered quantity is unavailable and available stock is upto ".$avail_quantity,'data'=>[]))); die;
          }     
 }*/


/*
* Function name: getProduct
* Description: used to get product details of cart
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 13th July 2016
* Modified Date & Reason:
*/ 

public function getProduct($para) {
      
    foreach($para['products'] as $p)  {

        $query=  DB::select(DB::raw("select oc.product_id ,p.sku,oc.rate as unit_price,oc.quantity,oc.total_price from cart oc
         left join products p on p.product_id=oc.product_id ")); 
    }
  
  
      $prod = json_decode(json_encode($query),true);      
           $data = array();
           
           $i=0;
           foreach($prod as $key => $opv) {
                            
                   $data['products'][$i]['product_id']=$opv['product_id'];
                   $data['products'][$i]['sku'] = $opv['sku'];
                   $data['products'][$i]['unit_price'] = $opv['unit_price'];
                   $data['products'][$i]['total_price'] = $opv['total_price'];                   
                   $data['products'][$i]['quantity'] = $opv['quantity'];
                
            $i++;
           }

           return  $data;
}

/*
* Function name: cartcancel
* Description: used to  cancel cart items 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 14th July 2016
* Modified Date & Reason:
*/ 

public function cartcancel($data) {

  $result = DB::table('users')
                    ->select(DB::raw('user_id'))
                     ->where('password_token', '=', $data['customer_token'])
                    ->get()->all();       

            $data = json_decode(json_encode($result[0]),true);
            $customerId=$data['user_id'];                          

   $cart= DB::table('cart')->where('user_id',$customerId)->delete();
}

/*
* Function name: getCustomerOrder
* Description: function is used to view orders of customer 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 15th July 2016
* Modified Date & Reason:
*/ 


public function getCustomerOrder($customerId,$sales_rep_id,$legal_entity_id,$offset,$offset_limit,$status_id)
 {

 $query= DB::table('gds_orders AS g')
          ->select('g.order_code',
            'g.gds_order_id as order_number',
            'g.order_date as date',
            'g.total as total_amount',
            db::raw("getMastLookupValue(g.order_status_id) as order_status"));
   if((isset($sales_rep_id) && $sales_rep_id!='') && ($customerId == ''))
    {  
       $query->whereIn('g.created_by',$sales_rep_id);
    }
    else 
    {
   if(!empty($legal_entity_id)) { 
     $query->where('g.cust_le_id',$legal_entity_id);
      } 
      else { 
       $legalEntity_id= DB::select(DB::raw("SELECT legal_entity_id from users where user_id='$customerId'"));
       $legal_entity_id=$legalEntity_id[0]->legal_entity_id;
       $user_id= DB::select(DB::raw("SELECT user_id from users  where legal_entity_id='$legal_entity_id'"));
       $Userids = json_decode(json_encode($user_id));
       $str = implode(',', array_map(function($c) {
        return $c->user_id;
       }, $Userids));
       $query->leftJoin('gds_customer as gc','g.gds_cust_id','=','gc.gds_cust_id')
             ->whereRaw('FIND_IN_SET(gc.mp_user_id,"'.$str.'")');
     }
    } 

    if((isset($status_id) && $status_id!=''))
    {
      if($status_id==17001)
      {
       $query->where('g.order_status_id',17001);
     }elseif ($status_id==17007) {
      $query->whereIn('g.order_status_id',[17007,17023]);
     }elseif ($status_id==17022) {
      $query->whereIn('g.order_status_id',[17022]);
     }elseif ($status_id==17009) {
     $query->whereIn('g.order_status_id',[17009,17015,17017]);
     }    
    }

      $tempCount = clone $query;
      $total = $tempCount->get()->all();
      $total = count($total);
      if(!empty($offset_limit))
      {  
      $results= $query->orderBy('g.created_at', 'DESC')
                        ->skip($offset)
                        ->take($offset_limit)
                        ->get()->all(); 
    }else{
     $results= $query->orderBy('g.created_at', 'DESC')->get()->all();
   }
 
      return ['Result' => $results, 'totalOrderCount' => $total];
  }

public function GetOrderstatus($order_id){

     $query=  DB::select(DB::raw("SELECT product_id,order_status FROM gds_order_products  where  
      gds_order_id='$order_id'")); 

     $arr = json_decode(json_encode($query),true); 

              return $data;
}

/*
* Function name: getOrderCode
* Description: functionality used to get order code against Orderid
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 6th Sept 2016
* Modified Date & Reason:
*/ 
       
public function getOrderCode($order_id)
{

   $query= DB::table('gds_orders')
                        ->select(DB::raw('order_code'))
                        ->where('gds_order_id', '=', $order_id)
                        ->get()->all();
             $OrderId=json_decode(json_encode($query[0]),true);          
  
  return $OrderId['order_code'];
}

/*
* Function name: sendsms
* Description: functionality used to send sms to customer related Orders
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 14th July 2016
* Modified Date & Reason:
*/ 
       
public function sendsms($token,$message)
{ 

    $query = DB::table('users')
                        ->select(DB::raw('mobile_no'))
                        ->where('password_token', '=', $token)
                        ->get()->all();
             $telephone=json_decode(json_encode($query[0]),true); 

            
        $ch = curl_init();
        $mno=$telephone['mobile_no']; 
        $msgtext = $message;
        
        if(preg_match( '/^[A-Z0-9]{10}$/', $mno) && !empty($message)) {
        $ch = curl_init();
       // $user='vinil@esealinc.com:eseal@123';
        $user=Config::get('dmapi.DB_USER');
        $receipientno= $mno; 
      //  $senderID='FCTAIL';
        $senderID=Config::get('dmapi.DB_SENDER_ID') ;
        $msgtxt= $msgtext; 
        
        curl_setopt($ch,CURLOPT_URL, Config::get('dmapi.DB_URL'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
       
        $buffer = curl_exec($ch);
        if(empty ($buffer))
        { echo " buffer is empty "; }
        else
        { 
        //echo $buffer;
        //echo 'Message Sent.';
        } 
        curl_close($ch);
     }
    } 
        
public function valOrderid($order_id){

  $query = DB::table("gds_orders as go")
      ->select(DB::raw("count(go.gds_order_id) as count")) 
      ->where("go.gds_order_id","=",$order_id)   
      ->get()->all();
      
      return $query[0]->count;   
}

public function valOrderSku($sku,$order_id){
  

  $query = DB::table("gds_order_products as gop")
      ->select(DB::raw("count(gop.sku) as count")) 
      ->where("gop.sku","=",$sku)   
      ->where("gop.gds_order_id",'=',$order_id)
      ->get()->all();
 
      return $query[0]->count;  
}
public function valStatus($status){

  $query = DB::table("master_lookup as mol")
      ->select(DB::raw("count(mol.master_lookup_id) as count")) 
      ->where("mol.master_lookup_id","=",$status)   
      ->where("mol.mas_cat_id",'=',17)
      ->get()->all();
      
      return $query[0]->count;  
}

public function getName($sku){
   $query = DB::table("products as p")
      ->select("p.product_title as product_name")
      ->where("p.sku","=",$sku)   
      ->get()->all();
      
      return $query;  
}
public function getStatus($status_id){
   $query = DB::table("master_lookup as mol")
      ->select("mol.master_lookup_name")
      ->where("mol.master_lookup_id","=",$status_id)   
      ->get()->all();
      
      return $query[0];
}
public function updateOrderStatus($status,$name, $order_id)
    {
        $query = DB::table("gds_orders as go")
      ->select("go.phone_no","go.email")
      ->where("go.gds_order_id","=",$order_id)   
      ->get()->all();
      
     // return $query[0];
   // $query = $this->db->query("select email from " .DB_PREFIX. "order where order_id = '".(int)$order_id."'");
       if(isset($query[0]->email) && !empty($query[0]->email))  {
        $email=$query[0]->email;         
        $to = "$email";
        $subject = "Order Status :" .$order_id;
        $product_name = $name;
        
        $order_status_name = $status;
        $message = "The status of your order with order id: ".$order_id."  for the product ".$name." has been changed to ".$status."  ";
        
      $txt=$message;
    
    
    $headers = 'From: admin@ebutor.com' . "\r\n" .
    'Reply-To: admin@ebutor.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
     mail($to, $subject, $txt, $headers);
       }
       if(isset($query[0]->phone_no) && !empty($query[0]->phone_no)){
        $ch = curl_init();
      if(preg_match( '/^[A-Z0-9]{10}$/', $query[0]->phone_no) && !empty($message)) {
        $ch = curl_init();
       $user='vinil@esealinc.com:eseal@123';
        $receipientno= $query[0]->phone_no; 
        $senderID='FCTAIL';  
        $msgtxt= $message; 
        $msg = str_replace('"', ' ', $msgtxt);
        $msgtxt = str_replace('{', '', $msg);
        $msgtxt = str_replace('}', '', $msgtxt);
        
        curl_setopt($ch,CURLOPT_URL,  "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
        //print_r($ch);exit;
        $buffer = curl_exec($ch);
        
        //    print_r($buffer);exit;
        if(empty ($buffer))
        { echo " buffer is empty ";
             curl_close($ch);
       // return 0; 
        }
        else
        { curl_close($ch);
         // return 1;
        }
      } 
       }
    

    return 1;
    }

public function getTelephone($gds_order_id){
      $result = DB::table('gds_orders as go')
      ->select(DB::raw(" go.phone_no, go.email ")) 
      ->where("go.gds_order_id","=",$gds_order_id)   
      ->get()->all();
      
      return $result;
    }

/*
* Function name: valOrderProd
* Description: functionality used to validate order id & product id
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 20th July 2016
* Modified Date & Reason:
*/ 

  public function valOrderProd($order_id,$product_id) {

    if(isset($product_id) && $product_id!='')
    {
      

      $query = DB::table("gds_orders as go")
      ->select(DB::raw("count(go.gds_order_id) as count"))
      ->leftJoin('gds_order_products as gop','gop.gds_order_id','=','go.gds_order_id') 
      ->where("go.gds_order_id","=",$order_id)   
      ->where("gop.product_id","=",$product_id) 
      ->get()->all();

           
    }
    else
    {
      
      $query = DB::table("gds_orders as go")
      ->select(DB::raw("count(go.gds_order_id) as count")) 
      ->leftJoin('gds_order_products as gop','gop.gds_order_id','=','go.gds_order_id') 
      ->where("go.gds_order_id","=",$order_id)   
      ->get()->all();
    }  
    
    $data=$query[0]->count;
    
    return $data;
  }
/*
* Function name: getHostURL
* Description: functionality used to get server host URL from DB
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 26th Aug 2016
* Modified Date & Reason:
*/ 

   public function getHostURL(){
   
    $query = DB::table("mp_configuration")
      ->select(DB::raw("key_value")) 
      ->where("key_name","=","URL")   
      ->get()->all();
 
     return $query[0]->key_value;
 }

/*
* Function name: valCancelProd
* Description: functionality used to validate order id & product id in cancel exists or not
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 22nd July 2016
* Modified Date & Reason:
*/ 

   public function valCancelProd($order_id,$product_id){

    if(isset($product_id) && $product_id!='')
    {
  
      $cancel = DB::table("gds_cancel_grid as go")
      ->select(DB::raw("count(go.gds_order_id) as count,go.cancel_status_id as status")) 
      ->leftJoin('gds_order_cancel as goc','goc.cancel_grid_id','=','go.cancel_grid_id')
      ->where("go.gds_order_id","=",$order_id) 
      ->where("go.cancel_status_id","=","17009") 
      ->where("goc.product_id","=",$product_id)   
      ->get()->all();
     } else {

     $cancel = DB::table("gds_cancel_grid as go")
      ->select(DB::raw("count(go.gds_order_id) as count,go.cancel_status_id as status")) 
      ->where("go.cancel_status_id","=","17009")
      ->where("go.gds_order_id","=",$order_id)   
      ->get()->all();
     }
       
    $data['cancel']= $cancel[0]->count;
    $data['cancel_status']= $cancel[0]->status;

     return $data;  
}

   /*
* Function name: getProdetails
* Description: functionality used to view product details based on  order id & product id 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 25th July 2016
* Modified Date & Reason:
*/ 

   public function getProdetails($order_id,$product_id) {
    
     if(isset($product_id) && $product_id!=''){

       $order = DB::table('gds_order_products as gds')
      ->select(DB::raw("gds.product_id,gds.gds_order_id,gds.qty,gds.sku,gds.pname"))
      ->where("gds.gds_order_id","=",$order_id)
      ->where("gds.product_id","=",$product_id)     
      ->get()->all();
     } else {

      
     $order = DB::table('gds_order_products as gds')
      ->select(DB::raw("gds.product_id,gds.gds_order_id,gds.qty,gds.sku"))
      ->where("gds.gds_order_id","=",$order_id)   
      ->get()->all();
     }
       $prod_details = json_decode(json_encode($order),true);    
    
       return $prod_details;
   }

   /*
    * Class Name: returnReasons
    * Description: the function is used to show return reasons
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 25 July 2016
    * Modified Date & Reason: 
     
    */ 

     public function returnReasons(){
     
     try{
         $result= DB::table('master_lookup as ml')
            ->select(DB::raw('ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value'))
            ->where('ml.is_active','=','1')
            ->where('ml.mas_cat_id','=',59)
            ->orderBy('ml.sort_order', 'ASC')
            ->get()->all();
          return $result;
           } catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }          
   }   
   
   /*
    * Class Name: cancelReasons
    * Description: The function is cancel reasons
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 25 July 2016
    * Modified Date & Reason: 
     
    */ 

     public function cancelReasons(){

         $result= DB::table('master_lookup as ml')
            ->select(DB::raw('ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value'))
            ->leftJoin('master_lookup_categories as mlc','mlc.mas_cat_id','=','ml.mas_cat_id')
            ->where('mlc.is_active','=','1')
            ->where('ml.mas_cat_id','=',60)
            ->get()->all();
      
          return $result;              
   }   

/*
    * Class Name: getInvoice
    * Description: The function is display Order invoice id
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 20 Aug 2016
    * Modified Date & Reason: 
     
    */ 

     public function getInvoice($order_id){

         $result= DB::table('gds_invoice_grid as g')
            ->select(DB::raw('g.gds_invoice_grid_id as invoice_id'))
            ->where('g.gds_order_id','=',$order_id)
            ->get()->all();
     
        if(empty($result))
        {

           $result=0;
        }
        else
        {
           return $result[0]->invoice_id; 
        }
   }
   
   /*
    * Class Name: FFLogsUpdate
    * Description: The function is used to update FF logs
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 20 Aug 2016
    * Modified Date & Reason: 
     
    */ 
   
   
   public function FFLogsUpdate($sales_token,$customer_token)
  {
    
    $result = DB::table('users')
                    ->select(DB::raw('user_id,legal_entity_id'))
                    ->where('password_token', '=', $customer_token)
                    ->get()->all();       

            if(empty($result)) 
          {
             $customerId=0;
             $legal_entity_id=0;
          } 

          else
          {

            $data = json_decode(json_encode($result[0]),true);
            $customer_id=$data['user_id'];  
            $legal_entity_id=$data['legal_entity_id']; 
          }               
              

     $ff_id=$this->getcustomerId($sales_token);
 
     $query =  DB::table('ff_call_logs')
                           ->insert(['ff_id' => $ff_id,
                           'user_id' => $customer_id, 
                           'activity' => '107001',
                           'legal_entity_id'=>$legal_entity_id,
                           'created_at' => date("Y-m-d H:i:s")
                           ]); 
 }

 public function FFLogsUpdate_new($data)
  {

   // print_r($data);exit;
    
    $result = DB::table('users')
                    ->select(DB::raw('user_id,legal_entity_id'))
                    ->where('password_token', '=', $data['customer_token'])
                    ->get()->all();       

            if(empty($result)) 
          {
             $customer_id=0;
             $legal_entity_id=0;
          } 

          else
          {

            $datas = json_decode(json_encode($result[0]),true);
            $customer_id=$datas['user_id'];  
            $legal_entity_id=$datas['legal_entity_id']; 
          }               
              

     $ff_id=$this->getcustomerId($data['sales_token']);

      $StartDate=date('Y-m-d 00:00:00');
      $EndDate=date('Y-m-d 23:59:59');

      DB::table('ff_call_logs')
      ->where('ff_id',$ff_id)
      ->where('user_id',$customer_id)
      ->whereBetween('check_in',array($StartDate,$EndDate)) 
      ->update(array('check_out_lat' => $data['latitude'],'check_out_long'=>$data['longitude'],'activity'=> '107001', 'check_out' => date("Y-m-d H:i:s")));
 }

 /*
    * Class Name: ScheduledDeliveryDate
    * Description: The function is used to check ScheduledDeliveryDate is public holiday or not
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14th Oct 2016
    * Modified Date & Reason: 
     
    */ 

 public function ScheduledDeliveryDate($scheduled_delivery_date) {

    $result = DB::table('holiday_list')
                    ->select(DB::raw('holiday_name'))
                    ->where('holiday_date', '=', $scheduled_delivery_date)
                    ->get()->all();       

          if(empty($result)) 
          {             
             $reason=''; 
          } 
          else
          {
            $reason=$result[0]->holiday_name;
          }  

    return $result;
 }
 
  public function generateOrderref($le_wh_id) {
    $result = DB::table('legalentity_warehouses')
                    ->select(DB::raw('state'))
                    ->where('le_wh_id', '=',$le_wh_id)
                    ->first();   
    if(count($result) < 0){
        return false;
    }else{
        $stateId=$result->state;
        $type = 'SO';
               try{
                   $custrepo = new CustomerRepo();
                   $stateCode = DB::table('zone')->select('code')->where('zone_id',$stateId)->first();
                   $stateCode = isset($stateCode->code) ? $stateCode->code : "TS";
                  
                  $serial_num = DB::connection('mysql-write')->select("SELECT * from serial_numbers WHERE state_code=? AND prefix=?",[$stateCode,$type]);
//                  Log::info("serial number before code generate write conn");
  //                Log::info($serial_num);

                   $orderCode = $custrepo->getRefCode($type,$stateId);
    //               Log::info("OrderCode Generate Time==".date("Y:m:d H:i:s"));
      //             Log::info($orderCode);

                   $serial_numArr = DB::connection('mysql-write')->select("SELECT * from serial_numbers WHERE state_code=? AND prefix=?",[$stateCode,$type]);
        //           Log::info("serial number after code generate write conn");
          //         Log::info($serial_numArr);

                   //$this->setOrderReference($orderCode);
                   return $orderCode;
               }catch(\Exception $e){
                Log::info($e->getMessage());
                   //$orderCode = '';
                   //var_dump($e->getMessage());
               }

           return $orderCode; 
    }
  }

 public function getcancelReasons() {
 try{
    
    $result = DB::table('master_lookup')
                    ->select(DB::raw("mas_cat_id as id,master_lookup_name as name,value"))
                    ->where('mas_cat_id',60)
                    ->where('is_active',1)
                    ->whereIn('value',[60001,60002])
                    ->get()->all();   

        return $result;
    } catch(Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
  }
	
	public function getSuccessOrderCount($legal_entity_id) {
		try{
			$result = DB::table('gds_orders')
                    ->select(DB::raw("count(gds_order_id) as count"))
                    ->where('cust_le_id',$legal_entity_id)
                    ->whereIn('order_status_id',[17007,17008,17023])
                    ->first();
			return $result->count;
		} catch(Exception $e) {
			return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
		}    
	}

 public function getFilterOrderStatus() {

 try{
    
    $result = DB::table('master_lookup')
                    ->select(DB::raw("value,master_lookup_name"))
                    ->where('is_active',1)
                    ->whereIn('value',[17001,17022,17009,17007])
                    ->orderBy('sort_order','ASC')
                    ->get()->all();   

        return $result;
    } catch(Exception $e) {
            
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
  }
    public function getProdPacks($cart_id,$product_id) {

 try{
   
   $result=DB::table('cart_product_packs as cp')
                ->select(DB::raw('cp.esu_quantity,cp.esu,cp.star,cp.pack_level,cp.pack_price,cp.pack_qty,cp.pack_cashback'))
                //->where('cp.session_id', '=', $customer_token)
                ->where('cp.cart_id', $cart_id)
                ->where('cp.product_id', '=', $product_id)
                ->useWritePdo()
                ->get()->all();
        return $result;
    } catch(Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
  }
    public function getParentCustId($legal_entity_id) {
        try {
            $result = DB::table('users')
                    ->select(['user_id as cust_user_id'])
                    ->where('legal_entity_id', $legal_entity_id)
                    ->where('is_parent', '=', 1)
                    ->first();            
            return $result;
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
        /*
     * getUserEcash is function to Update user cashback 
     * @param 
     * @return 
     */
    public function getUserEcash($userId){
        try{
            if($userId>0 && $userId != ''){
                $data = DB::selectFromWriteConnection(DB::raw('select * from `user_ecash_creditlimit` as `uec` where `uec`.`user_id` = '.$userId.' limit 1'));                
                return isset($data[0])?$data[0]:[];
            }
        } catch (Exception $ex) {
        }        
    }
    public function custUnDeliveredOrderValue($cust_le_id){
        try{
            if($cust_le_id>0 && $cust_le_id != ''){
                $data = DB::selectFromWriteConnection(DB::raw('select sum(go.total) as order_value,
                        (select sum(cancel_value) from gds_cancel_grid where gds_cancel_grid.gds_order_id=go.gds_order_id) as cancel_value from `gds_orders` as `go` JOIN `gds_orders_payment` AS `gop` ON `gop`.`gds_order_id`=`go`.`gds_order_id` where `go`.`cust_le_id` = '.$cust_le_id.'
                         and go.order_status_id IN (17001,17020,17005,17021,17024,17025,17026,17014) and `gop`.`payment_method_id` = 22018 limit 1'));                
                return isset($data[0])?($data[0]->order_value-$data[0]->cancel_value):0;
        }
        } catch (Exception $ex) {
        }        
    }
    public function getFfDynamicData($data){
      if(isset($data['data_source']) && $data['data_source_name'] && $data['data_input']){
        $val=$data['data_input'];
        if(isset($val['value_array']) && isset($val['key_array'])){
          $keys=$val['key_array'];
          $values=$val['value_array'];
          if($data['data_source'] == 1){
            $query = "select * from ".$data['data_source_name'];
            $keys = array("from_date","to_date","user_id","activity_type","flag");
            foreach ($keys as $keyIndex=>$value) {
              if($keyIndex == 0){
                $query =$query ." where ";
                if(gettype($values[$keyIndex] == "string")){
                  $query = $query.$value."= '".$values[$keyIndex]."'";
                }
                else if(gettype($values[$keyIndex] == "integer" || gettype($values[$keyIndex]) == "double")){
                  $query = $query.$value."= ".$values[$keyIndex];
                }
              }else{
                if(gettype($values[$keyIndex]) == "string"){
                  $query = $query." and ".$value."= '".$values[$keyIndex]."'";
                }
                else if(gettype($values[$keyIndex]) == "integer" || gettype($values[$keyIndex]) == "double"){
                  $query = $query." and ".$value."= ".$values[$keyIndex];
                }
              }              
            }
            $data = DB::select(DB::raw($query));
            return Array('status'=>'success','message'=>'success','data'=>$data);
          }else if($data['data_source'] == 2){
            $procedureParams="";  
            foreach ($values as $index => $valueatIndex) {
              if($index > 0){
                $procedureParams = $procedureParams.",";
              }
              if(gettype($valueatIndex) == "string"){
                $procedureParams = $procedureParams."'".$valueatIndex."'";
              }else if(gettype($valueatIndex) == "integer" || gettype($valueatIndex) == "double"){
                $procedureParams = $procedureParams.$valueatIndex;
              }
            }
            $call="call ".$data['data_source_name']."(".$procedureParams.")";
            $data = DB::select(DB::raw($call));
            return Array('status'=>'success','message'=>'success','data'=>$data);
          }
        }else{
          return Array('status'=>'failed','message'=>'input is required','data'=>[]);
        }          
      }else{
        return Array('status'=>'failed','message'=>'input is required','data'=>[]);
      }
    }
 public function getSalesOrderData($phone_no,$fromDate,$toDate,$flag){
    $query = DB::selectFromWriteConnection(DB::raw("CALL getRetailerSalesData(".$phone_no.",'".$fromDate."','".$toDate."',".$flag.")"));
    return $query;
 }

 public function getOrderStatusById($orderId){
  $orderstatus=DB::selectFromWriteConnection(DB::raw('select order_status_id from gds_orders where gds_order_id='.$orderId.' limit 1'));
  return $orderstatus;
 }

    /**
     * Generate the product recomendation for retailer
     * @param  int $cust_id Customer ID
     * @param  int $limit   Limit of recomendation
     * @param  int $repeat  Number of items repeated in past purchase
     * @return Array        Array of recomended products
     */
    public function generateRecomendedProducts($cust_id, $le_wh_id, $customertype, $limit, $repeat, $isFF){
        $result = [];
        $le_wh_id = "'".$le_wh_id."'";
        // echo 'CALL getRecommendedProducts('.$cust_id.', '.$customertype.', '.$limit.', '.$repeat.')';
        $data = DB::select(DB::raw('CALL getRecommendedProducts('.$isFF.','.$le_wh_id.','.$cust_id.', '.$customertype.', '.$limit.', '.$repeat.')'));
        for($i=0; $i<count($data); $i++){
            $config_data = DB::table('product_pack_config')
                           ->select('star', 'no_of_eaches', 'inner_pack_count', 'esu', 'level')
                           ->where(array('product_id' => $data[$i]->productId, 'is_sellable' => 1))
                           ->orderBy('no_of_eaches', 'DESC')
                           ->get()->all();
            $packs = $this->createPacks($data[$i]->quantity, $config_data, $data[$i]->productId, $data[$i]->cust_id);
            // echo '<pre>';
            // print_r(json_encode($packs));
            // exit;
            $checkDuplicate = DB::table('offline_cart_details')
                           ->select('cart_id')
                           ->where(array('product_id' => $data[$i]->productId, 'cust_id' => $cust_id))
                           ->get()->all();
            if( count($checkDuplicate) == 0){
                $cart = array(
                        "product_id"         => $data[$i]->productId,
                        "parent_id"          => $data[$i]->parentId,
                        "product_image"      => $data[$i]->productImage,
                        "product_title"      => $data[$i]->productTitle,
                        "product_star"       => $data[$i]->packStar,
                     //   "color_code"         => $data[$i]->color_code,
                        "esu"                => $data[$i]->esu,
                        "quantity"           => $data[$i]->quantity,
                        "status"             => $data[$i]->status,
                        "unit_price"         => $data[$i]->newprice,//$data[$i]->unitPrice,
                        "total_price"        => $data[$i]->totalPrice,
                        "margin"             => $data[$i]->margin,
                        "blocked_qty"        => ($data[$i]->blockedQty != "" || $data[$i]->blockedQty != null) ? $data[$i]->blockedQty : 0,
                        "prmt_det_id"        => $data[$i]->prmtDetId,
                        "is_slab"            => $data[$i]->isSlab,
                        "slab_esu"           => $data[$i]->slabEsu,
                        "product_slab_id"    => $data[$i]->productSlabId,
                        "pack_level"         => $data[$i]->packLevel,
                        "pack_type"          => $data[$i]->packType,
                        "freebie_product_id" => $data[$i]->freebieProductId,
                        "freebee_qty"        => $data[$i]->freeqty,
                        "freebee_mpq"        => $data[$i]->freebieMpq,
                        "discount_type"      => $data[$i]->discounttype,
                        "discount"           => $data[$i]->discount,
                        "cashback_amount"    => 0,
                        "le_wh_id"           => $data[$i]->warehouseId,
                        "cust_id"            => $data[$i]->cust_id,
                        "minimum_order_value" => $data[$i]->minimum_order_value,
                        "category_id"          => $data[$i]->categoryId,
                        "product_point"       => $data[$i]->product_point,
                        "is_child"           => 0,
                        "packs"              => json_encode($packs)
                    );
                //dd($cart);
                $cart_id = DB::table('offline_cart_details')->insertGetId($cart);
               // dd($test);
                // Generate the array to render in API
                $product_detail = array(
                            "cart_id"          => $cart_id,
                            "customerId"       => $data[$i]->cust_id,
                            "discount"         => $data[$i]->discount,
                            "esu"              => $data[$i]->esu,
                            "freebieMpq"       => $data[$i]->freebieMpq,
                            "freebieProductId" => $data[$i]->freebieProductId,
                            "isChild"          => $data[$i]->isChild,
                            "freeqty"          => $data[$i]->freeqty,
                            "isSlab"           => $data[$i]->isSlab,
                            "margin"           => $data[$i]->margin,
                            "packLevel"        => $data[$i]->packLevel,
                            "packStar"         => $data[$i]->packStar,
                            "star"             => $data[$i]->star,
                            "packType"         => $data[$i]->packType,
                            "parentId"         => $data[$i]->parentId,
                            "prmtDetId"        => $data[$i]->prmtDetId,
                            "productId"        => $data[$i]->productId,
                            "productImage"     => $data[$i]->productImage,
                            "productSlabId"    => $data[$i]->productSlabId,
                            "productTitle"     => $data[$i]->productTitle,
                            "quantity"         => $data[$i]->quantity,
                            "slabEsu"          => $data[$i]->slabEsu,
                            "status"           => $data[$i]->status,
                            "totalPrice"       => $data[$i]->totalPrice,
                            "unitPrice"        => $data[$i]->newprice, // $data[$i]->unitPrice,
                            "newprice"         => $data[$i]->newprice,
                            "updatedDate"      => $data[$i]->updatedDate,
                            "warehouseId"      => $data[$i]->warehouseId,
                            "blockedQty"       => $data[$i]->blockedQty,
                            "discounttype"     => $data[$i]->discounttype,
                            "cashbackAmount"   => 0,
                            "product_star"     => $data[$i]->packStar,
                            "minimum_order_value" => $data[$i]->minimum_order_value,
                            "categoryId"          => $data[$i]->categoryId,
                            "product_point"    => $data[$i]->product_point,
                            "cust_id"          => $data[$i]->cust_id,
                            "isFF"             => $isFF,
                            "packs"            => $packs);
                array_push($result, $product_detail);
            }
           
        }
        return $result;
    }
    /**
     * Get packs array as per product qty
     * @param  int $total_qty   Total quantity
     * @param  Array $config_data Product configration array
     * @param  int $product_id  Product ID
     * @param  int $cust_id     Retailer ID
     * @return Array            Packs in array
     */
    public function createPacks($total_qty, $config_data, $product_id, $cust_id,  $packs = []){
        //dd($config_data); 

        if( count($config_data) > 0){
           
            $rem = 0;
            for($j=0; $j<count($config_data); $j++){
                $packsize  = $config_data[$j]->no_of_eaches;
                $slab_esu  = $config_data[$j]->esu;
                if($slab_esu > 1){
                  $pack_qty  = $packsize * $slab_esu;
                } else{
                  $pack_qty  = $packsize;
                }
                $packqty   = $packsize * $slab_esu;
                if( $total_qty >= $packqty ){
                    if($packqty == 0){
                      $packqty = 1;
                    } 
                    if($slab_esu == 0){
                      $slab_esu = 1;
                    }                               
                    $qty       = floor( $total_qty / $packqty);
                    $rem       = $total_qty % $packqty;
                    $pack_qty  = $packqty * $qty; //$qty * $packsize;
                    $pack_size = floor( $pack_qty / $slab_esu);
                    $pack = array(
                        "esu"         =>  $slab_esu,
                        "star"        =>  $config_data[$j]->star,
                        "pack_qty"    =>  $pack_qty,
                        "qty"         =>  $qty,
                        "pack_size"   =>  $packsize,
                        "pack_level"  =>  $config_data[$j]->level,
                        "product_id"  =>  $product_id,
                        "customer_id" =>  $cust_id
                    );
                    // echo '<pre>';
                    //  print_r($pack);
                    array_push($packs, $pack);
                    if($rem == 0) break;                                       
                }                
                if( $rem !=0 ){
                    $total_qty = $rem;
                    return  $this->createPacks($total_qty, $config_data, $product_id, $cust_id, $packs);
                }                               
            }
          return $packs;
        }
    }
}