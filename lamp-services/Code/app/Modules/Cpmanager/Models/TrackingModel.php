<?php
namespace App\Modules\Cpmanager\Models;	
use \DB;



class TrackingModel extends \Eloquent {
    
   
    
  /*
    * Class Name: checkTrackingId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
    
    function checkTrackingId($tracking_id){
    DB::enableQueryLog();
    $result = DB::Table('gds_orders_ship_details as track')
                    //->join('customer_categories', 'customer_categories.category_id', '=', 'categories.category_id')
                    ->select(DB::raw('count(track.tracking_id) as count'))
                    ->where('track.tracking_id','=',$tracking_id)
                    ->get()->all();
    
   // print_r(DB::getQueryLog());
      return $result[0]->count;
    }
    
    /*
    * Class Name: checkCustomerToken
    * Description: Check customer token for tracking the details
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
     function checkCustomerToken($customer_token){
        DB::enableQueryLog();
        $result = DB::Table('users as user')
                        ->select(DB::raw('count(user.password_token) as count'))
                        ->where('user.password_token','=',$customer_token)
                        ->get()->all();

      return $result[0]->count;
        
       
    }
     /*
    * Class Name: getCustomerId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
     function getCustomerId($customer_token){
    $result = DB::table('users as u')
                           ->select(DB::raw("u.user_id"))
                           ->where('u.password_token','=',$customer_token)
                           ->get()->all();

         //print_r(DB::getQueryLog());
         return $result;
         
    }
 /*
    * Class Name: getTrackingData
    * Description: Get the tracking data from gds_orders,ship track,master loo etc tables
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
    function getTrackingData($tracking_id){
        
        $result = DB::table('gds_orders_ship_details as gosd')
                ->select(DB::raw("gosd.tracking_id AS tracking_id,gosd.gds_order_id AS order_id, gr.total AS order_total,gr.ship_total AS total,gop.payment_method_id AS payment_method, gstd.ship_addr1 AS shipaddress,
     gstd.ship_addr2 as shipaddress2,gstd.ship_city as shipping_city,gstd.ship_postcode AS postcode,gr.order_date AS orderdate"))
            
                ->leftJoin('gds_orders as gr','gr.gds_order_id','=','gosd.gds_order_id')
                ->leftJoin('gds_ship_track_details as gstd','gstd.gds_ship_id','=','gosd.order_ship_id')
                ->leftJoin('gds_orders_payment as gop','gop.gds_order_id','=','gr.gds_order_id')
                ->leftJoin('master_lookup as ml','ml.value','=','gop.payment_method_id')
                ->where('gosd.tracking_id','=',$tracking_id)
                ->get()->all();

         //print_r(DB::getQueryLog());
        // print_r($result);
         return $result;

    }
    
     /*
    * Class Name: checkTrackingId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
    function ExpectedDelDate($tracking_id){
    $shippedDate = $this->db->query("Select ot.expected_delivery_date from oc_order_tracking ot where ot.docket_no = '".$tracking_id."'");

    if(!empty($shippedDate->row['expected_delivery_date'])){
                    return $shippedDate->row['expected_delivery_date'];	
            }else{
                    return "Not Available";
            }



    }
 /*
    * Class Name: checkTrackingId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
    function shippedDate($tracking_id){
       DB::enableQueryLog(); 
         $result = DB::table('gds_ship_track_details as gosd')
                ->select(DB::raw('gosd.tracking_id as tracking'))    
                ->where('gosd.tracking_id','=',$tracking_id)
                ->get()->all();
         
          
        if($result){
            
          return $result;  
        }
    
      //Old   
    $shippedDate = $this->db->query("Select ot.shipped_date from oc_order_tracking ot where ot.docket_no = '".$tracking_id."'");

            if(!empty($shippedDate->row['shipped_date'])){
                    return $shippedDate->row['shipped_date'];
            }else{
                    return "Not Available";
            }	
    }
 /*
    * Class Name: processingDate
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
    function processingDate($tracking_id){
        

            $processingDate = $this->db->query("select oh.date_added from oc_order_history oh
     where oh.order_id in
      (select opv.order_id from oc_order_product_variant opv where opv.tracking_id = '".$tracking_id."' ) and oh.order_status_id in (2,15)");
            if(!empty($processingDate->row['date_added'])){
                    return $processingDate->row['date_added'];
            }else{
                    return "Not Available";
            }

    }

 /*
    * Class Name: checkTrackingId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  

    function deliveryDate($tracking_id){
         $deliveryDate = $this->db->query("Select ot.delivered_date from oc_order_tracking ot where ot.docket_no = '".$tracking_id."'");

    if(!empty($deliveryDate->row['delivered_date'])){
                    return $deliveryDate->row['delivered_date'];	
            }else{
                    return "Not Available";
            }

    }

     /*
    * Class Name: checkTrackingId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
     function cancelDate($tracking_id){
        $cancelDate = $this->db->query("select oh.date_added from oc_order_history oh
     where oh.order_id in
      (select opv.order_id from oc_order_product_variant opv where opv.tracking_id = '".$tracking_id."' ) and oh.order_status_id = 7");
            if(!empty($cancelDate->row['date_added'])){
                    return $cancelDate->row['date_added'];
            }else{
                    return "Not Available";
            }

    }
    /*
    * Class Name: checkTrackingId
    * Description: Check the order tracking on from gds order ship table and get
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */  
    function returnDate($tracking_id){
            $returnDate = $this->db->query("select oh.date_added from oc_order_history oh
     where oh.order_id in
      (select opv.order_id from oc_order_product_variant opv where opv.tracking_id = '".$tracking_id."' ) and oh.order_status_id in (11,17)");
            if(!empty($returnDate->row['date_added'])){
                    return $returnDate->row['date_added'];
            }else{
                    return "Not Available";
            }
    }

/*
    * Class Name: CheckPincode
    * Description: Check the pincodes in serviceable areas.
    * get the tracking id count    
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 19 July 2016
    * Modified Date & Reason:
    */  
    function CheckPincode($pincode){
      $result = DB::table('wh_serviceables as ws')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT(ws.le_wh_id) SEPARATOR ',')) as `le_wh_id`"))
                ->where('ws.pincode','=',$pincode)
                                ->get()->all();


              if(!empty($result))
                {
                 
                 $data=$result[0]->le_wh_id;

                }else{

                $data='';
                }

     
  
       return $data;
           
           
 
    
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
        //print_r($appId);exit;
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


    public function getFFGeoData($ff_manager_id) {
    try{

          return DB::select(DB::raw("CALL getFFsGeoByManagerId($ff_manager_id)"));

        }
        catch(Exception $e) {

            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }


	}