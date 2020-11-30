<?php

/*
 * Filename: OrderReportController.php
 * Description: This file is used for manage Orders Reports
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 09th Jan 2017
 * Modified date: 
 */

namespace App\Modules\Cpmanager\Controllers;

use Illuminate\Support\Facades\Input;
use Session;
use Response;
use Log;
use URL;
use DB;
use PDF;
use Lang;
use Config;
use View;
use Illuminate\Http\Request;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Cpmanager\Models\AdminOrderModel;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Roles\Models\Role;
use App\Modules\Cpmanager\Models\AssignOrderModel;
use App\Modules\Cpmanager\Models\PickerModel;
use App\Modules\CrateManagement\Models\CrateManagement;
use App\Modules\Cpmanager\Controllers\AssignOrderController;

class AdminOrderController extends BaseController {

    public function __construct() {

        $this->order = new OrderModel();
        $this->_category = new CategoryModel();
        $this->_admin = new AdminOrderModel();
        $this->_role = new Role();
        $this->_assign=new AssignOrderModel(); 
        $this->_picker = new PickerModel();
        $this->_crateManagement = new CrateManagement();
        $this->_assignOrderController = new AssignOrderController();
    }


    public function saveTransitStatus() {
        DB::beginTransaction();
        try {
            $data = Input::all();
            $arr = isset($data['data']) ? json_decode($data['data']) : array();
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {

                       $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);

                if ($checkAdminToken > 0) {
                    if (isset($arr->admin_id) && $arr->admin_id != '') {
                        if (isset($arr->order_ids) && $arr->order_ids != '') {
                            if (isset($arr->stock_delivered_by) && $arr->stock_delivered_by != '') {
                                if (isset($arr->de_name) && $arr->de_name != '') {
                                    if (isset($arr->de_mobileno) && $arr->de_mobileno != '') {
                                        $stock_driver_name = (isset($arr->stock_driver_name) && $arr->stock_driver_name != '') ? $arr->stock_driver_name : '';
                                        $stock_vehicle_number = $arr->stock_vehicle_number;
                                        $stock_driver_mobile = (isset($arr->stock_driver_mobile) && $arr->stock_driver_mobile != '') ? $arr->stock_driver_mobile : 0;
                                        $docket_code = $this->order->getRefCode('TR');
                                        $this->_admin->updateOrderStatus($arr->order_ids, '17024', $arr->admin_id);
                                        $this->_admin->updateStockTransfer($arr->order_ids, $arr->stock_delivered_by, $arr->de_name, $arr->de_mobileno, $stock_driver_name, $stock_vehicle_number, $stock_driver_mobile, $docket_code, $arr->admin_id, $arr->vehicle_id);
                                        // $orderController->saveComment($orderId, 'Order Status', array('comment'=>$commentDelivered, 'order_status_id'=>'17024'));    
                                        $arr_orderids = explode(',', $arr->order_ids);
                                        foreach ($arr_orderids as $key => $value) {
                                            $commentDelivered = 'Order Id: ' . $value . ' stock transferred, DE Name : ' . $arr->de_name . ', DE Mobile : ' . $arr->de_mobileno . ', Vehicle Number : ' . $stock_vehicle_number . ', Driver Name : ' . $stock_driver_name . ', Driver Mobile : ' . $stock_driver_mobile;
                                            $this->saveComment($value, 'Order Status', array('comment' => $commentDelivered, 'order_status_id' => '17024'), $arr->admin_id);
                                        }
                                        $orderIds = explode(",", $arr->order_ids);
                                        $this->_assignOrderController->apiUpdateCrateStatus($orderIds, "", 137002, $arr->admin_token, "");
                                        DB::commit();
                                        if (!empty($docket_code)) {
                                            return json_encode(Array(
                                                'status' => "success",
                                                'message' => " Consignment Created Successfully",
                                                'docket_number' => $docket_code
                                            ));
                                        } else {
                                            return json_encode(Array(
                                                'status' => "success",
                                                'message' => "No data",
                                                'data' => []
                                            ));
                                        }
                                    } else {
                                        print_r(json_encode(array('status' => "failed", 'message' => "Please send delivery mobile number", 'data' => [])));
                                    }
                                } else {
                                    print_r(json_encode(array('status' => "failed", 'message' => "Please send delivery name", 'data' => [])));
                                }
                            } else {
                                print_r(json_encode(array('status' => "failed", 'message' => "Please send delivery id", 'data' => [])));
                            }
                        } else {
                            print_r(json_encode(array('status' => "failed", 'message' => "Please send orderids", 'data' => [])));
                        }
                    } else {
                        print_r(json_encode(array('status' => "failed", 'message' => "Please send admin id", 'data' => [])));
                    }
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass admin token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
            DB::rollback();
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }

    public function saveComment($orderId, $commentType, $dataArr,$admin_id) {

      try{  
        $typeId = $this->order->getCommentTypeByName($commentType);    
        $date = date('Y-m-d H:i:s');
        $commentArr = array('entity_id'=>$orderId, 'comment_type'=>$typeId,
                        'comment'=>(string)$dataArr['comment'],
                        'commentby'=>$admin_id,
                        'created_by'=>null,
                        'order_status_id'=>$dataArr['order_status_id'],
                        'created_at'=>(string)$date,
                        'comment_date'=>(string)$date
                        );

       $result= $this->order->saveComment($commentArr);
       return $result;
     } catch (Exception $e) {
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }

    public function getOrdersBasedOnDocket() {    
        try {
           $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {

                       $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);

                if ($checkAdminToken > 0) {
                if(isset($arr->admin_id) && $arr->admin_id!='') {
                  if(isset($arr->legal_entity_id) && $arr->legal_entity_id!='') { 
                  $DataFilter=$this->_role->getFilterData(6,$arr->admin_id);
                  $decode_data=json_decode($DataFilter,true);
                  $sbu_lits = isset($decode_data['sbu']) ? $decode_data['sbu'] : [];
              if(!empty($sbu_lits))
                {
               $decode_sbulist= json_decode($sbu_lits,true);
               $hub = (isset($decode_sbulist[118002])&& !empty($decode_sbulist[118002])) ? $decode_sbulist[118002] : '';
               $start_date = (isset($arr->start_date)&& !empty($arr->start_date)) ? $arr->start_date : date("Y-m-d");
               $end_date = (isset($arr->end_date)&& !empty($arr->end_date)) ? $arr->end_date : date("Y-m-d");
               $flag = (isset($arr->flag)&& !empty($arr->flag)) ? $arr->flag : '';
               
                }else{
                     return json_encode(Array(
                            'status' => "success",
                            'message' => "No data",
                            'data' => []
                        ));
                }
                
                  $data=$this->_admin->getOrdersBasedOnDockets($hub,$start_date,$end_date,$flag);  
                   if (!empty($data)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => " getOrdersBasedOnDocket",
                            'docket_number' => $data
                        ));
                    } else {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "No data",
                            'data' => []
                        ));
                    }

                 } else{                   
                   print_r(json_encode(array('status' => "failed", 'message' => "please send legal_entity_id", 'data' => [])));
                 } 
                 } else{                   
                   print_r(json_encode(array('status' => "failed", 'message' => "please send admin id", 'data' => [])));
                 }   
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass admin token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
       
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }

 public function saveGeoData() {
        
        try {

            $data = Input::all();
            $arr = isset($data['data'])?json_decode($data['data']):array();

            if (isset($arr->token) && !empty($arr->token)) {

                       $checkToken = $this->_category->checkCustomerToken($arr->token);

                if ($checkToken > 0) {
                 
                if(isset($arr->geo_type) && $arr->geo_type!='' ) { 
                if(isset($arr->latitude) && $arr->latitude!='' ) { 
                if(isset($arr->longitude) && $arr->longitude!='' ) {  
                if(isset($arr->user_id) && $arr->user_id!='' ) { 
                
                $arr->accuracy = (isset($arr->accuracy) && $arr->accuracy != '') ? $arr->accuracy : 0;
                $arr->heading = (isset($arr->heading) && $arr->heading != '') ? $arr->heading : 0;
                $arr->route_id = (isset($arr->route_id) && $arr->route_id != '') ? $arr->route_id : 0;
                $arr->speed = (isset($arr->speed) && $arr->speed != '') ? $arr->speed : 0;

                $user_id = $arr->user_id;
                $geoData=DB::connection("mysql-write")->table("geo_track")->where("user_id",$user_id)->orderby("created_at","DESC")->first();
                $last_inserted = isset($geoData->created_at)?$geoData->created_at:"";
                $last_latitude = isset($geoData->latitude)?$geoData->latitude:"";
                $last_longitude = isset($geoData->longitude)?$geoData->longitude:"";
                $last_insert_time = ($last_inserted!="")?strtotime($last_inserted):strtotime('-5 minutes');
                $cur_time = strtotime(date("Y-m-d H:i:s"));
                $dif_sec = $cur_time - $last_insert_time;
                //$dif_mins = $dif_sec/60; //minutes
                //should not accept multiple entries within 1 min, should not insert duplicate lat/long data
                if($dif_sec>50 && ($last_latitude != round($arr->latitude,5) || $last_longitude != round($arr->longitude,5))){
                    $result=$this->_admin->saveGeoDatas($arr);
                    if (!empty($result)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "Inserted geo data",
                            'data' => []
                        ));
                    } else {
                        return json_encode(Array(
                            'status' => "failed",
                            'message' => "Data Not Inserted",
                            'data' => []
                        ));
                    }
                }else{
                    return json_encode(Array(
                        'status' => "success",
                        'message' => "Multiple calls in lessthan 1 min with same lat/long values",
                        'data' => []
                    ));
                }
                 } else {
                    return Array('status' => 'failed', 'message' => 'You have already user_id', 'data' => []);
                }
                  } else{                 
                   print_r(json_encode(array('status' => "failed", 'message' => "please send longitude", 'data' => [])));           
                 }

                   }else{
                   print_r(json_encode(array('status' => "failed", 'message' => "Please send latitude", 'data' => [])));
                 }
                  }else{
                   print_r(json_encode(array('status' => "failed", 'message' => "Please send geo type", 'data' => [])));
                 }
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
                 
                  
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass  token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
      
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
}

 public function getGeoData() {
        
        try {

            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();

            if (isset($arr->token) && !empty($arr->token)) {

                       $checkToken = $this->_category->checkCustomerToken($arr->token);

              if($checkToken > 0) {
               if(isset($arr->user_id) && $arr->user_id!='' ) { 
                 $arr->start_date=(isset($arr->start_date) && $arr->start_date!='')?$arr->start_date:date('Y-m-d');
                 $result=$this->_admin->getGeoDatas($arr);

                if (!empty($result)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "Geodata",
                            'data' => $result
                        ));
                    } else {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "No Data",
                            'data' => []
                        ));
                    }
                                          
                 } else {
                    return Array('status' => 'failed', 'message' => 'You have already user_id', 'data' => []);
                }
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
                                   
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass  token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
         
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
}
/*
  * Function Name: getReturnProductWithReason()
  * Description: Used to get orders with return reasons
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 28 March 2017
  * Modified Date & Reason:
  */

public function getReturnProductWithReason() {
        
        try {

            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();

            if (isset($arr->deliver_token) && !empty($arr->deliver_token)) {

            $checkToken = $this->_category->checkCustomerToken($arr->deliver_token);

              if($checkToken > 0) {
               if(isset($arr->order_id) && $arr->order_id!='' ) { 

               $flag=(isset($arr->flag) && $arr->flag!='')? $arr->flag:0;
               
               if($flag==1)
               {
              
               $result=$this->_admin->getCancelProductWithReason($arr);
               }else{ 
              
               $result=$this->_admin->getReturnProductWithReason($arr);
              }

                if (!empty($result)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "getReturnOrdersWithReason",
                            'data' => $result
                        ));
                    } else {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "No Data",
                            'data' => []
                        ));
                    }
                                          
                 } else {
                    return Array('status' => 'failed', 'message' => 'Please send order_id', 'data' => []);
                }
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
                                   
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass  deliver_token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
         
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
}
/*
  * Function Name: updateProgressFlag()
  * Description: Used to update progress flag or orders
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 13 June 2017
  * Modified Date & Reason:
  */

public function updateProgressFlag() {     
        try {
            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->sales_token) && !empty($arr->sales_token)) {
            $checkToken = $this->_category->checkCustomerToken($arr->sales_token);
            if($checkToken > 0) {
             
               if(isset($arr->order_id) && $arr->order_id!='' ){ 
               if(isset($arr->user_id) && $arr->user_id!='' ){
               $check=$this->_admin->checkProgressFlag($arr->order_id);
                
                if($check==0)
                 { 
                $this->_picker->updatePickStarttime($arr->order_id);
                $status=$this->_admin->updateProgressFlag($arr->order_id,$arr->user_id);
                 if($status)
                    {
                         return json_encode(Array(
                            'status' => "success",
                            'message' => "updateProgressFlag",
                            'data' => ""
                        ));
                   }else{
                        return json_encode(Array(
                            'status' => "failed",
                            'message' => "updateProgressFlag",
                            'data' => ""
                        ));
                  }
               }else{
                    
                      return json_encode(Array(
                            'status' => "failed",
                            'message' => "Already in progress",
                            'data' => ""
                        ));

               }
             
                   
                  } else {
                    return Array('status' => 'failed', 'message' => 'Please send user_id', 'data' => []);
                } 
                                          
                 } else {
                    return Array('status' => 'failed', 'message' => 'Please send order_id', 'data' => []);
                }
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
                                   
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass  deliver_token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
         
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
}


/*
  * Function Name: searchAllProducts()
  * Description: Used to get products based on keyword
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2018
  * Version: v1.0
  * Created Date: 16 June 2018
  * Modified Date & Reason:
  */

public function searchAllProduct() {     
        try {
            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->sales_token) && !empty($arr->sales_token)) {
            $checkToken = $this->_category->checkCustomerToken($arr->sales_token);
            if($checkToken > 0) {
             
               if(isset($arr->keyword) && $arr->keyword!='' ){          
                $result=$this->_admin->searchAllProducts($arr->keyword);
                    if($result)
                    {
                         return json_encode(Array(
                            'status' => "success",
                            'message' => "searchAllProduct",
                            'data' => $result
                        ));
                   }else{
                        return json_encode(Array(
                            'status' => "failed",
                            'message' => "searchAllProduct",
                            'data' => ""
                        ));
                  }
                 
                                          
                 } else {
                    return Array('status' => 'failed', 'message' => 'Please send order_id', 'data' => []);
                }
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
                                   
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass  deliver_token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
         
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
}




}
