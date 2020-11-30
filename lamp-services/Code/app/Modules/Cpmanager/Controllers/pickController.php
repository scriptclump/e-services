<?php

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
  use Illuminate\Http\Request;
  use App\Modules\Cpmanager\Models\PickerModel;
  use App\Modules\Cpmanager\Models\CategoryModel;
  use App\Modules\Orders\Models\ReturnModel;
  use App\Modules\Orders\Models\PaymentModel;
  use App\Modules\Cpmanager\Models\OrderModel;
  use App\Modules\Roles\Models\Role;
  use App\Http\Controllers\BaseController;
  use App\Central\Repositories\ProductRepo;
  use App\Central\Repositories\RoleRepo;
  use App\Modules\Cpmanager\Models\FieldForceDashboardModel;
  use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
    use App\Modules\Cpmanager\Models\ContainerapiModel;
  use App\Modules\Cpmanager\Models\EcashModel;

  use App\Modules\Orders\Controllers\OrdersController;
  use App\Modules\PurchaseOrder\Models\PurchaseOrder;
  use App\Modules\DmapiV2\Models\Dmapiv2Model;
  use App\Modules\Communication\Models\CommunicationMongoModel;
  use App\Modules\Indent\Models\LegalEntity;

  
 class pickController extends BaseController {
    
    public function __construct() {  

      $this->_picker = new PickerModel();
      $this->categoryModel = new CategoryModel();
      $this->pickerreturn = new ReturnModel();
      $this->paymentmodel = new PaymentModel(); 
      $this->order = new OrderModel();
      $this->repo = new ProductRepo();
      $this->_role = new Role(); 
      $this->_ffdm = new FieldForceDashboardModel();
      $this->_ecash = new EcashModel();
            
    }


    /*
      * Class Name: getpicklistdetails
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: th Oct 2016
     * Modified Date: 10th Jan 2017
      * Modified Date & Reason: 
    */
  public function getpicklistdetails() {
      
    try{

       $array = json_decode($_POST['data'],true);

       if(isset($array['picker_token']) && $array['picker_token']!='') {    
        if(isset($array['order_id']) && $array['order_id']!='') { 

          $valToken = $this->categoryModel->checkCustomerToken($array['picker_token']);

          if($valToken>0) { 
       

         $Picker_Orderdetails=$this->_picker->picklistdetails($array['order_id']);

         if(empty($Picker_Orderdetails)){
              $message='PickList details not found';
              $data=[];

            }else
            {
              $message='PickList details';
              $data= $Picker_Orderdetails;
            }

            return json_encode(Array('status'=>'success','message'=>$message,'data'=>$data)); 

            }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'OrderId is not sent', 'data' => []));
         }
        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Picker token is not sent', 'data' => []));
       }

    }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 

  }
  
  
  
      /*
      * Class Name: getorderdetailbyinvoiceid
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 5th Oct 2016
      * Modified Date & Reason: 
    */
  public function getorderdetailbyinvoice($data = array()) {
   
    try{

        if(count($data)>0){
          $array = $data;
        }else{
          $array = json_decode($_POST['data'],true);
        }
       if(isset($array['picker_token']) && $array['picker_token']!='') {    
        if(isset($array['invoice_id']) && $array['invoice_id']!='') { 
        
        $invoice_id=$this->_picker->getinvoiceidcheck($array['invoice_id']);
    
   if(!empty($invoice_id)) {
                       $invoice_id = $array['invoice_id'];
                         $Picker_Orderdetails=$this->_picker->getorderdetailbyinvoice($invoice_id);

         if(empty($Picker_Orderdetails)){
              $message='Invoice details not found';
              $data=[];

            }else
            {
              $message='Order details by invoice';
              $data= $Picker_Orderdetails;
            }
            

            return Array('status'=>'success','message'=>$message,'data'=>$data); 

 
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Invoice ID is not sent', 'data' => []));
         }
        }
        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Picker token is not sent', 'data' => []));
       }
       
       
                    

    }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 

  }


    /*
  * Function Name: getpickuporderlist()
  * Description: Used to get orderlist for pickgenerated
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 5 Oct 2016
  * Modified Date & Reason:
  */
  public function getPickOrderList()
  {
   try{ 
   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 
         if (isset($arr->flag) && !empty($arr->flag))
         {
            $flag = $arr->flag;
            }
             else
              {
             $flag='';
            }
       if($flag==1)
       {
             if(isset($arr->picker_token) && !empty($arr->picker_token) )
             {
                 $checkPickerToken = $this->categoryModel->checkCustomerToken($arr->picker_token);
                  if($checkPickerToken>0)
                  {
                    //  $srm_token= $arr->srm_token;
                       $user_data= $this->categoryModel->getUserId($arr->picker_token); 
                        if(isset($arr->date) && !empty($arr->date))
                        {
                        $date = $arr->date;
                        }
                        else
                        {
                       $date=date("Y-m-d");
                        }
                        if (isset($arr->status) && !empty($arr->status))
                        {
                        $status = $arr->status;
                        }
                        else
                        {
                        return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Pass status",
                                    'data' => []
                                  ));
                        }

                        if($status==1)
                         {
                          //total pick
                         $status_id='17005,17020';
                           
                         }elseif($status==2) 
                         {
                        //picked
                           //  $status_id=17003;
                          //ready to dispatch
                           $status_id=17005;
                         }else{
                              //picklist generate
                             $status_id=17020;
                         }
                        if(isset($arr->sort_id) && !empty($arr->sort_id))
                        {
                        $sort_id = $arr->sort_id;
                        }
                        else
                        {
                       $sort_id='';
                        }
                         if(isset($arr->sort_type) && !empty($arr->sort_type))
                        {
                        $sort_type = $arr->sort_type;
                        }
                        else
                        {
                       $sort_type='';
                        }
                           $data = $this->_picker->getPickOrderList($user_data[0]->user_id,$date,$status_id,$sort_id,$sort_type);
                           if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getPickupOrderList",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass picker token",'data'=> [])));die;

               }
                        
            
            } 
            else 
            {
        
             if(isset($arr->deliver_token) && !empty($arr->deliver_token))
             {

                 $checkDeliverToken = $this->categoryModel->checkCustomerToken($arr->deliver_token);

                  if($checkDeliverToken>0)
                  {
                       $user_data= $this->categoryModel->getUserId($arr->deliver_token); 
                       

                        if(isset($arr->date) && !empty($arr->date))
                        {
                        $date = $arr->date;
                        }
                        else
                        {
                      
                       $date=date("Y-m-d");
                        }

                          if(isset($arr->sort_id) && !empty($arr->sort_id))
                        {
                        $sort_id = $arr->sort_id;
                        }
                        else
                        {
                      
                       $sort_id='';
                        }

                         if(isset($arr->sort_type) && !empty($arr->sort_type))
                        {
                        $sort_type = $arr->sort_type;
                        }
                        else
                        {
                      
                       $sort_type='';
                        }
                      

                        if (isset($arr->status) && !empty($arr->status))
                        {
                        $status = $arr->status;
                        }
                        else
                        {
                      
                        return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Pass status",
                                    'data' => []
                                  ));
                        }

                        if($status==1)
                         {
                          //total 
                          //replaced 17021 to 17026
                         $status_id='17021,17026,17007,17023,17014,17022,17025';
                           
                         }elseif($status==2) 
                         {
                          //delivered
                             $status_id=17007;
                         }elseif($status==3) {
                            
                           //partially returned
                             $status_id=17023;

                         }elseif($status==4) {
                            
                           //hold
                             $status_id=17014;

                         }elseif($status==5) {
                            
                           //returned
                             $status_id=17022;

                         }else{
                              //invoiced(pending)
                          //replaced 17021 to 17026
                             $status_id='17021,17026,17025';

                         }
                        // $user_data[0]->user_id=1;
                          $latitude = 0;
                          $longitude = 0;
                          if (isset($arr->latitude) && !empty($arr->latitude))
                          {
                            $latitude = $arr->latitude;
                          }
                          if (isset($arr->longitude) && !empty($arr->longitude))
                          {
                            $longitude = $arr->longitude;
                          }
                           $data = $this->_picker->getDeliverOrderList($user_data[0]->user_id,$date,$status_id,$sort_id,$sort_type,$latitude,$longitude);
                           
                          // $count= $this->_picker->getDeliveryCount($user_data[0]->user_id,$date);
                           
                          // $total = $this->_picker->getGrandTotal($user_data[0]->user_id,$date);
                           
                          if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getDeliverOrderList",
                                    'data' => $data
                                   // 'count'=>$count,
                                   // 'total'=>$total
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                    //'count' => $count,
                                    //'total'=>$total
                                  ));
                               }

                     
                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass picker token",'data'=> [])));die;

               }
                        
        
        
            }  
      
       }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

       }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>  []);
      } 
    

    }
    
    
     /*
      * Class Name: getProductbyBarcode
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 5th Oct 2016
     * Modified Date: 11th Jan 2017
      * Modified Date & Reason: 
    */
  public function getProductbyBarcode() {
      
    try{

       $array = json_decode($_POST['data'],true);

       if(isset($array['picker_token']) && $array['picker_token']!='') {    
        if(isset($array['pack_sku_code']) && $array['pack_sku_code']!='') { 
       
                 $checkPickerToken = $this->categoryModel->checkCustomerToken($array['picker_token']);

                  if($checkPickerToken>0)
                  {

                       $user_data= $this->categoryModel->getUserId($array['picker_token']); 
                      $Product_Orderdetails=$this->_picker->getProductbyBarcode($user_data[0]->user_id,$array['pack_sku_code'],$array['offset_limit']);
           
                
                   }
                   
         if(empty($Product_Orderdetails)){
              $message='Prodcut is not found';
              $data=[];

            }else
            {
              $message='Product Details';
              $data= $Product_Orderdetails;
            }

            return Array('status'=>'success','message'=>$message,'data'=>$data); 


          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'pack_sku_code is not sent', 'data' => []));
         }
        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Picker token is not sent', 'data' => []));
       }
 
    }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 

  }
  
  /*
      * Class Name: SavePickList
      * Description: Function used to save picked data 
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 15th Oct 2016
      * Modified Date & Reason: 
    */
  public function SavePickList($data) {
    try{
       //$array = json_decode($_POST['data'],true);
     if (isset($data) && !empty($data)) 
     {   
        $array = json_decode($data,true);
        /*$request['parameters'] = $array;
        $request['apiUrl'] = 'SavePickList';
        $this->_containerapi = new ContainerapiModel();
        $this->_containerapi->logApiRequests($request); */

   if(isset($array['orderdata']) && $array['orderdata']!='') {    
    if(isset($array['orderdata']['order_id']) && $array['orderdata']['order_id']!=''){ 

      if(isset($array['orderdata']['picker_token']) && $array['orderdata']['picker_token']!='') {    
       $checkPickerToken = $this->categoryModel->checkCustomerToken($array['orderdata']['picker_token']);
       if($checkPickerToken>0)
       {
        $user_data = $this->categoryModel->getUserId($array['orderdata']['picker_token']);
        $picker_id=$user_data[0]->user_id;
        $array['orderdata']['picker_id'] = $picker_id;
        // $order_id=$this->_picker->getorderId($array['orderdata']['order_id']);
        $order_id=$array['orderdata']['order_id'];
       } else {
        return json_encode(Array('status' => 'session', 'message' =>'You have logged into other Ebutor System', 'data' => []));
       }
       $order_id=$array['orderdata']['order_id'];

       if(empty($order_id)){

         return Array('status'=>'success','message'=>'OrderId Not valid','data'=>$array); 
       }else{

        $this->_picker->updatePickEndtime($order_id); 

//        $HostUrl=$this->order->getHostURL();        
//        $url='http://'.$HostUrl.'/dmapi/createshipmentbypicklist';
        $det= array();
        $det['api_key'] = Config::get('dmapi.PICKAPIKey');
        $det['secret_key'] = Config::get('dmapi.PICKAPISECRETKey');
        $det['orderdata']=json_encode($array);
/*
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST, sizeof($det));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $det);
               //curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
*/
        Session::put('userId', $picker_id);
        $_orderController = new OrdersController();
        $output = $_orderController->createShipmentByPicklistApiAction($det);
        $arr=json_decode($output->getContent());
        if(!empty($arr)) {
          if($arr->Status==200)
          {
            return json_encode(Array('status' => 'success', 'message' =>$arr->Message, 'data' => []));
          } else {
           return json_encode(Array('status' => 'failed', 'message' =>$arr->Message, 'data' => []));
         }
       } else {
         return json_encode(Array('status' => 'failed', 'message' =>"Empty Response from create shipment api", 'data' => []));
       }
     }
   } else {
     return json_encode(Array('status' => 'failed', 'message' =>'OrderId is not sent', 'data' => []));
   }
 } else {
   return json_encode(Array('status' => 'failed', 'message' =>'Pass Orderdata ', 'data' => []));
 }

} else {
 return json_encode(Array('status' => 'failed', 'message' =>'Pass Picker Token', 'data' => []));
}


}
else
{
 return json_encode(Array(
  'status' => "failed",
  'message' => "No data",
  'data' => []
  ));

}

}catch (Exception $e)
{
  $status = "failed";
  $message = "Internal server error";
  $data = [];
  return Array('status' => $status, 'message' => $message, 'data' => $array);
} 

}  


   /*
      * Class Name: saveContainerData
      * Description: Function used to save picked data 
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 19th Oct 2016
      * Modified Date & Reason: 
    */
   public function saveContainerData() {
      DB::beginTransaction();
        try {
            if (isset($_POST['data'])) {
                
                $dataArr = json_decode($_POST['data'], true);
//                Log::info('savecontainerdata input',$dataArr);
                $logs_aray['data'] = $_POST['data'];
                $logs_aray['orderid'] = $dataArr['order_id'];
                $request['parameters'] = $logs_aray;
                $request['apiUrl'] = 'saveContainerData';
                $this->_containerapi = new ContainerapiModel();
                $this->_containerapi->logApiRequests($request);
                
                $array = json_decode($_POST['data']);
                if (isset($array->picker_token) && $array->picker_token != '') {
                    $checkPickerToken = $this->categoryModel->checkCustomerToken($array->picker_token);
                    if ($checkPickerToken > 0) {
                        $user_data = $this->categoryModel->getUserId($array->picker_token);
                        $user_id = $user_data[0]->user_id;
                    } else {
                        return json_encode(Array('status' => 'session', 'message' => 'You have logged into other Ebutor System', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'Pass Orderdata ', 'data' => []));
                }
                $array->needTransactions=false;
                $savePicklist = array();
                $savePicklist['orderdata']['order_id'] = $dataArr['order_id'];
                $savePicklist['orderdata']['user_id'] = $dataArr['user_id'];
                $savePicklist['orderdata']['cfc'] = $dataArr['cfc'];
                $savePicklist['orderdata']['bags'] = $dataArr['bags'];
                $savePicklist['orderdata']['carats'] = $dataArr['crates'];
                $savePicklist['orderdata']['picker_token'] = $dataArr['picker_token'];
                $savePicklist['orderdata']['cancel_reason_id'] = $dataArr['cancel_reason_id'];

                $product = array();

                foreach ($dataArr['container'] as $container) {
                    foreach ($container['products'] as $prod) {
                        $product[$prod['product_id']]['product_id'] = $prod['product_id'];
                        $product[$prod['product_id']]['orderedQty'] = $prod['orderedQty'];
                        $product[$prod['product_id']]['cancel_reason_id'] = $prod['cancel_reason_id'];
                        $shipqty = $this->_picker->getShipqty($prod['product_id'], $dataArr['order_id']);
                        $product[$prod['product_id']]['shipqty'] = $shipqty;

                        $product[$prod['product_id']]['pickedQty'] = (isset($product[$prod['product_id']]['pickedQty']) ? $product[$prod['product_id']]['pickedQty'] : 0) + $prod['qty'];
                    }
                }

                $savePicklist['orderdata']['products'] = array();
                foreach ($product as $prod) {
                    if ((($prod['shipqty'] > $prod['pickedQty']) || ($prod['pickedQty'] == 0)) && $prod['cancel_reason_id'] == '') {
                        $prod_name = $this->_picker->getProductName($prod['product_id']);
                        DB::rollback();
                        return json_encode(Array('status' => 'failed', 'message' => "Cancellation reason cannot be empty for product " . $prod_name . ".", 'data' => []));
                    }
                    $savePicklist['orderdata']['products'][] = $prod;
                }
                $savePicklist['orderdata']['needTransactions']=false;
                //echo json_encode($savePicklist); exit;

                $apiResult = $this->SavePickList(json_encode($savePicklist));
                $apiResult = json_decode($apiResult, true);

                //print_r($apiResult); 
                // Log::info($apiResult);
                if ($apiResult['status'] == 'failed' || $apiResult['status']=='session') {
                    DB::rollback();
                    return json_encode(Array('status' => $apiResult['status'], 'message' => $apiResult['message'], 'data' => []));
                } else if ($dataArr['cancel_reason_id'] != '' || !empty($dataArr['cancel_reason_id'])) {
                    $result=$this->_picker->updateReserveQty($array->order_id);
                    if($result){
                      DB::commit();
                    }else{
                      DB::rollback();
                    }
                    return json_encode(Array('status' => 'success', 'message' => $apiResult['message'], 'data' => []));
                }
                //(json_decode($_POST['data'], true)); exit;
                // Log::info('pickercontainer going to start');
                if (isset($user_id) && $user_id > 0) {
                   // Log::info('pickercontainer before start');
                    $result = $this->_picker->saveContainerData($array, $user_data[0]->user_id);
                    // Log::info('pickercontainer after complete');
                    //print_r($result); exit;
                    if (!empty($result)) {
                        DB::commit();
                        return json_encode(Array('status' => 'success', 'message' => "Created Shipment successfully", 'data' => []));
                    } else {
                        DB::rollback();
                        return json_encode(Array('status' => 'failed', 'message' => 'Shipment is not Created', 'data' => []));
                    }
                }
            } else {
                DB::rollback();
                return json_encode(Array('status' => "failed", 'message' => "No data", 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            DB::rollback();
            return json_encode(Array('status' => $status, 'message' => $message, 'data' => $data));
        }
    }
   /*
      * Class Name: saveContainerData
      * Description: Function used to save picked data 
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 19th Oct 2016
      * Modified Date & Reason: 
    */
   public function saveTripsheetData() {
        try {
            if (isset($_POST['data'])) {
                
                $dataArr = json_decode($_POST['data'], true);
                $logs_aray['data'] = $_POST['data'];
                $pickCode=$logs_aray['pick_code'] = isset($dataArr['pick_code'])?$dataArr['pick_code']:$dataArr['order_id'];
                $logs_aray['orderid'] = $dataArr['order_id'];
                $request['parameters'] = $logs_aray;
                $request['apiUrl'] = 'saveTripsheetData';
                $this->_containerapi = new ContainerapiModel();
                $this->_containerapi->logApiRequests($request);
                
                $array = json_decode($_POST['data']);
                if (isset($array->picker_token) && $array->picker_token != '') {
                    $checkPickerToken = $this->categoryModel->checkCustomerToken($array->picker_token);
                    if ($checkPickerToken > 0) {
                        $user_data = $this->categoryModel->getUserId($array->picker_token);
                        $user_id = $user_data[0]->user_id;
                    } else {
                        return json_encode(Array('status' => 'session', 'message' => 'You have logged into other Ebutor System', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'Pass Orderdata ', 'data' => []));
                }

                
                

                $pickproducts = array();
                foreach ($dataArr['container'] as $container) {
                    foreach ($container['products'] as $prod) {
                        $pickproducts[$prod['product_id']]['product_id'] = $prod['product_id'];                        
                        $pickproducts[$prod['product_id']]['cancel_reason_id'] = $prod['cancel_reason_id'];                        
                        $pickproducts[$prod['product_id']]['pickedQty'] = (isset($pickproducts[$prod['product_id']]['pickedQty']) ? $pickproducts[$prod['product_id']]['pickedQty'] : 0) + $prod['qty'];
                    }
                }
                
                $orders = $this->_picker->getOrdersByPickCode($pickCode);
                $orderProducts = [];
                if(is_array($orders) && count($orders)>0){
                    foreach($orders as $order){
                        $orderdetails = $this->_picker->getOrderProductdetailsById($order->gds_order_id);
                        //print_r($orderdetails);
                        $savePicklist = array();
                        $savePicklist['orderdata']['order_id'] = $order->gds_order_id;
                        $savePicklist['orderdata']['user_id'] = $dataArr['user_id'];
                        $savePicklist['orderdata']['cfc'] = 0;//$dataArr['cfc'];
                        $savePicklist['orderdata']['bags'] = 0;//$dataArr['bags'];
                        $savePicklist['orderdata']['carats'] = 0;//$dataArr['crates'];
                        $savePicklist['orderdata']['picker_token'] = $dataArr['picker_token'];
                        $savePicklist['orderdata']['cancel_reason_id'] = $dataArr['cancel_reason_id'];

                        $savePicklist['orderdata']['products'] = array();
                        $product = array();
                        foreach($orderdetails as $orderproduct){
                            if(isset($pickproducts[$orderproduct->product_id])){
                                $product['orderedQty'] = $orderproduct->order_qty;                            
                                $shipqty = $this->_picker->getShipqty($orderproduct->product_id, $orderproduct->gds_order_id);
                                $product['shipqty'] = $shipqty;

                                $product['product_id'] = $orderproduct->product_id;                            
                                $totprodpicked = $pickproducts[$orderproduct->product_id]['pickedQty'];

                                $product['cancel_reason_id'] = ($totprodpicked>$orderproduct->order_qty)?'':$pickproducts[$orderproduct->product_id]['cancel_reason_id'];;
                                $product['pickedQty'] = ($totprodpicked>$orderproduct->order_qty)?$orderproduct->order_qty:$totprodpicked;

                                $pickproducts[$orderproduct->product_id]['pickedQty']= ($totprodpicked>$orderproduct->order_qty)?($totprodpicked-$orderproduct->order_qty):($totprodpicked-$totprodpicked);

                                $savePicklist['orderdata']['products'][] = $product;
                            }
                        }
                        /*
                        foreach ($product as $prod) {
                            if ((($prod['shipqty'] > $prod['pickedQty']) || ($prod['pickedQty'] == 0)) && $prod['cancel_reason_id'] == '') {
                                $prod_name = $this->_picker->getProductName($prod['product_id']);
                                return json_encode(Array('status' => 'failed', 'message' => "Cancellation reason cannot be empty for product " . $prod_name . ".", 'data' => []));
                            }
                            $savePicklist['orderdata']['products'][] = $prod;
                        }
                         * 
                         */
                    //echo json_encode($savePicklist); exit;

                    $apiResult = $this->SavePickList(json_encode($savePicklist));
                    $apiResult = json_decode($apiResult, true);
                    
                    // Log::info($apiResult);
                    if ($apiResult['status'] == 'failed' || $apiResult['status']=='session') {
                        $msg[] = Array('status' => $apiResult['status'],'order_code'=>$order->order_code, 'message' => $apiResult['message'], 'data' => []);
                    } else if ($dataArr['cancel_reason_id'] != '' || !empty($dataArr['cancel_reason_id'])) {
                        $result=$this->_picker->updateReserveQty($order->gds_order_id);
                        $msg[] = Array('status' => 'success','order_code'=>$order->order_code, 'message' => $apiResult['message'], 'data' => []);
                    }else {
                        $msg[] = Array('status' => 'success','order_code'=>$order->order_code, 'message' => "Created Shipment successfully", 'data' => []);
                    }
                        
                    }
                    
                    
                }
                return json_encode(Array('status' => 'success', 'message' => "Created Shipment successfully", 'data' => []));
                //return json_encode($msg);
                //print_r($savePicklist);die;                
                /*
                //(json_decode($_POST['data'], true)); exit;
                Log::info('pickercontainer going to start');
                if (isset($user_id) && $user_id > 0) {
                    Log::info('pickercontainer before start');
                    $result = $this->_picker->saveContainerData($array, $user_data[0]->user_id);
                    Log::info('pickercontainer after complete');
                    //print_r($result); exit;
                    if (!empty($result)) {
                        return json_encode(Array('status' => 'success', 'message' => "Created Shipment successfully", 'data' => []));
                    } else {
                        return json_encode(Array('status' => 'failed', 'message' => 'Shipment is not Created', 'data' => []));
                    }
                }*/
                
                
            } else {
                return json_encode(Array('status' => "failed", 'message' => "No data", 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }

    /*
      * Class Name: getPaymentMethod
      * Description: Function used to get payment methods
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 17th Oct 2016
      * Modified Date & Reason: 
    */
  public function getPaymentMethod() {
      
    try{
          
       if (isset($_POST['data'])) 
       {   
       $array = json_decode($_POST['data']);

       if(isset($array->deliver_token) && $array->deliver_token!='') {    
       
          $checkDeliverToken = $this->categoryModel->checkCustomerToken($array->deliver_token);

            if($checkDeliverToken>0)
            {

               $data=$this->_picker->getPaymentMethod();
                
               if (!empty($data))
                {
                   return json_encode(Array(
                     'status' => "success",
                     'message' => "getPaymentMethod",
                     'data' => $data
                     ));
                     }else{
                     return json_encode(Array(
                      'status' => "success",
                       'message' => "No data",
                       'data' => []
                    ));
                  
                   }

              }else{
             
               return Array('status'=>'session','message'=>'You have already logged into Ebutor System','data'=>[]); 
      
              
            }

        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Pass deliver_token ', 'data' => []));
       }


       }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

    }catch (Exception $e)
      {
        
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>[]);
      } 

  } 
  
  

     /*
      * Class Name: getBagsbybarcode
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 5th Oct 2016
      * Modified Date & Reason: 
    */
  public function getBagsbybarcode() {
      
    try{

       $array = json_decode($_POST['data'],true);

       if(isset($array['picker_token']) && $array['picker_token']!='') {    
        if(isset($array['container_barcode']) && $array['container_barcode']!='') { 
       
                 $checkPickerToken = $this->categoryModel->checkCustomerToken($array['picker_token']);

                  if($checkPickerToken>0)
                  {

                       $user_data= $this->categoryModel->getUserId($array['picker_token']); 
                            
                      $Product_Orderdetails=$this->_picker->getBagsbybarcode($array['container_barcode']);
           
                   }

         if(empty($Product_Orderdetails)){
              $message='Prodcut is not found';
              $data=[];

            }else
            {
              $message='Product Details';
              $data= $Product_Orderdetails;
            }

            return Array('status'=>'success','message'=>$message,'data'=>$data); 


          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'pack_sku_code is not sent', 'data' => []));
         }
        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Picker token is not sent', 'data' => []));
       }
 
    }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 

  }



  /*
      * Class Name: getInvoiceByReturn
      * Description: Function used to return Invoice data based of particular order  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 19th Oct 2016
      * Modified Date & Reason: 
    */
 public function getInvoiceByReturn($data=[]) {
         DB::beginTransaction();
    try{

       $array = count($data)>0?$data:json_decode($_POST['data'],true);

       if(isset($array['deliver_token']) && $array['deliver_token']!='') {    
        if(isset($array['order_id']) && $array['order_id']!='') { 
          if(isset($array['invoice_id']) && $array['invoice_id']!='') {

                                                                                                                             
           $valToken = $this->categoryModel->checkCustomerToken($array['deliver_token']);

            if($valToken>0) { 
                $invoiceId = $array['invoice_id'];
                $orderId = $array['order_id'];
                $orderdata = $this->paymentmodel->getOrderDetailByInvoiceId($invoiceId);
                $payment_method_id = isset($orderdata->payment_method_id)?$orderdata->payment_method_id:0;
                
                
                $deliver_array = array('17007','17022','17023','17008');

                if(in_array($orderdata->order_status_id,$deliver_array)){
                    return json_encode(Array('status' => 'failed', 'message' =>'This order is already delivered', 'data' => []));
                }  

                if($payment_method_id==22010 && $array['payment_mode']==22018){
                    return json_encode(Array('status' => 'failed', 'message' =>'COD Order can not be delivered with LOC', 'data' => []));
                }
       
            if($array['flag']==1) { 

             $getInvoiceByReturn=$this->pickerreturn->savereturnApi($array,$array['flag']);
             $data=$getInvoiceByReturn;

             if(empty($getInvoiceByReturn)) {
              $status='failed';
              $message='getInvoiceByReturn';
              $data=[];
             }else {
              $status='success';
              $message='getInvoiceByReturn'; 
              $data=[];             
             }

            }else {              
              
               /**
               * to retrive image path
               */
              if(isset($_FILES['pod_url']))
               {

                if(!empty($_FILES['pod_url']['name']))
                { 

                $allowed = array("application/pdf","image/jpeg", "image/png","image/gif","image/jpg");
                if(!in_array(strtolower($_FILES['pod_url']['type']), $allowed)) {
                   $res['message']="Please upload pdf/jpeg/png/gif";
                                $data['status']="failed";
                                $data['data']="";
                                $final=json_encode($data);
                                print_r($final);die;
                }

                } 

                 $tin =$_FILES['pod_url'];

                $filepath1_move = date("Y-m-d-H-i-s")."_". $_FILES['pod_url']['name'];
                 $filepath1 = "uploads/cp/".$filepath1_move;
                
                 $result = move_uploaded_file($_FILES['pod_url']['tmp_name'], "uploads/cp/".$filepath1_move); 
      
                 $image=$this->repo->uploadToS3($filepath1,'collections',2);
         
    
                 }else{

                 $image ='';
                 }       
             
              $user_data= $this->categoryModel->getUserId($array['deliver_token']); 
              $user_id=$user_data[0]->user_id;
              
              $this->_picker->updateDeliveryEndtime($array['order_id']);
              if($array['amount_return'] == 0) {

                  $deliveredstatus='17007';
                  $this->_picker->ChangeOrderstatus($array,$deliveredstatus);            
                  $this->_picker->Ordercomments($array, $deliveredstatus);

                } else {                            

                   $returnEntry=$this->pickerreturn->savereturnApi($array,$array['flag']); 
                   
                   # OrderHistoryUpdate
                   $this->_picker->getOrderHistoryUpdate($array['order_id'],$user_id);             
         
                  if($returnEntry['status'] == 400) {

                   return json_encode(Array('status'=>'failed','message'=>'internal server error','data'=>$data)); 
            
         
                  
                   }

                }             

        
               #params for ledollection entry  
              $ifsc_code=(isset($array['ifsc_code']) && $array['ifsc_code']!='')?$array['ifsc_code']:0;
              $customer_name=(isset($array['customer_name']) && $array['customer_name']!='')?$array['customer_name']:'';        
              $bank_name=(isset($array['bank_name']) && $array['bank_name']!='')?$array['bank_name']:'';
              $cheque_date=(isset($array['cheque_date']) && $array['cheque_date']!='')?$array['cheque_date']:'';        
              $branch_name=(isset($array['branch_name']) && $array['branch_name']!='')?$array['branch_name']:'';        
              $discount_applied=(isset($array['discount_applied']) && $array['discount_applied']!='')?$array['discount_applied']:0;        
              $discount_type=(isset($array['discount_type']) && $array['discount_type']!='')?$array['discount_type']:"";        
              $discount=(isset($array['discount']) && $array['discount']!='')?$array['discount']:"";        
              $discount_deducted=(isset($array['discount_deducted']) && $array['discount_deducted']!='')?$array['discount_deducted']:0;        
              $ecash_applied=(isset($array['ecash_applied']) && $array['ecash_applied']!='')?$array['ecash_applied']:0;        
              $net_amount=(isset($array['net_amount']) && $array['net_amount']!='')?$array['net_amount']:0;        
               
               // $params['payments']= $array['payments'];
                $params['mode_of_payment']= $array['payment_mode'];
                $params['collection_amount']= $array['amount_collected'];
                $params['invoice']= $array['invoice_id'];
                $params['collected_by']= $user_id;
                $params['collected_on']= date('Y-m-d H:i:s');                
                $params['proof']= $image;
                
                $params['ifsc_code']= $ifsc_code;
                $params['customer_name']= $customer_name;
                $params['bank_name']= $bank_name;
                $params['cheque_date']= $cheque_date;
                $params['branch_name']= $branch_name;
                $params['reference_num']= $array['reference_no'];
                $params['rounded']=$array['round_of_value'];
                $params['discount_applied']= $discount_applied;
                $params['discount_deducted']=$discount_deducted;
                $params['discount_type']=$discount_type;
                $params['discount']=$discount;
                $params['ecash_applied']=$ecash_applied;
                $params['net_amount']=$net_amount;
                if(isset($array['collectable_amt']))
                $params['collectable_amt'] =$array['collectable_amt'];

                $collectionEntry = true;

                if($array['payment_mode']!=22018) {
                  $collectionEntry=$this->paymentmodel->saveCollection($params,$user_id);
                }

           /* if((isset($array['lattitude']) && $array['lattitude']!='0') && (isset($array['longitude']) && $array['longitude']!= '0') ){

                $this->_picker->updateGeoLocation($user_id,$array['order_id'],$array['lattitude'],$array['longitude']);

              }  */              
                
                    if($array['payment_mode']==22018){
                        $orderdata = $this->paymentmodel->getOrderDetailByInvoiceId($invoiceId);
                        $cust_user_id = isset($orderdata->user_id)?$orderdata->user_id:0;
                        $ret_val = isset($orderdata->ret_val)?$orderdata->ret_val:0;
                        $invioce_val = isset($orderdata->invioce_val)?$orderdata->invioce_val:0;
                        $ecash_applied = isset($orderdata->ecash_applied)?$orderdata->ecash_applied:0;
                        $collectable = round($invioce_val-$ret_val-$ecash_applied);
                        if($collectable>0){
                            $this->paymentmodel->deductUserEcash($cust_user_id,$collectable,$orderId,'143001');
                            $this->paymentmodel->sendLocSms($cust_user_id,$invoiceId,'custLOCDeliver',0);
                        }
                    }
                    
                    $products = isset($array['products_info']) ? $array['products_info'] : array();
                    $ordered_qty = isset($array['ordered_qty']) ? $array['ordered_qty'] : 0;
                    $invoiced_qty = isset($array['invoiced_qty']) ? $array['invoiced_qty'] : 0;
                    $delivered_qty = isset($array['delivered_qty']) ? $array['delivered_qty'] : 0;
                    $ord_status_id = 17007;
                    if($delivered_qty == 0){
                      $ord_status_id = 17022;
                    }else if ($invoiced_qty > $delivered_qty){
                      $ord_status_id = 17023;
                    }
                    $this->paymentmodel->calculateCashback($array['order_id'],$array['invoice_id'],['delivered_by'=>$array['user_id'],'ecash_applied'=>$ecash_applied,'amount_return'=>$array['amount_return']],$products,$ord_status_id);

                  if($collectionEntry) {
                  $status='success';
                  $message='Your Order has been delivered successfully';
                  $data=[];                     
                 } else {                   
                  $status='failed';
                  $message='Failed to save deliver details';
                  $data=[];
                }                              
            }

               DB::commit();
               DB::commit();
//              Log::info("Commiting two times");

            return json_encode(Array('status'=>$status,'message'=>$message,'data'=>$data));        
            
          }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'invoice id is not sent', 'data' => []));
          }
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'OrderId is not sent', 'data' => []));
          }
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'deliver token is not sent', 'data' => []));
          }

    }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          //Log::info("Problem getInvoiceByReturn Function");
          DB::rollback();
          //Log::info(json_encode($message));
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 

  }


  /*
      * Class Name: holdReasons
      * Description: Function used to return hold Reasons  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28th Oct 2016
      * Modified Date & Reason: 
    */


  public function holdReasons() {

    try{

       //$array = json_decode($_POST['data'],true);

       $reasons = $this->_picker->HoldReasons();

         if(!empty($reasons))   
            {
             $res['status']="success";
                  $res['message']="HoldReasons";
                  $res['data']=$reasons;

                }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="No HoldReasons found";
                    $res['data']=[];
                  }
  
     $response=json_encode($res); 
           return $response;

    


     }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 


}

/*
      * Class Name: getOrderHold
      * Description: Function used to change the order to Hold status
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28th Oct 2016
      * Modified Date & Reason: 
    */


  public function getOrderHold() {

    try{


      $array = json_decode($_POST['data'],true);

      if(isset($array['deliver_token']) && $array['deliver_token']!='') {    
        if(isset($array['order_id']) && $array['order_id']!='') {

        $valToken = $this->categoryModel->checkCustomerToken($array['deliver_token']);

          if($valToken>0) {
              $count_db = $this->_picker->CheckHoldcount($array['order_id']);
              $count_in_master = $this->_picker->CheckHoldcountMaster();
              $count_in_master = isset($count_in_master[0]) ? $count_in_master[0]->description : "";


              if(!empty($count_db) && isset($count_db[0]->hold_count) && ($count_db[0]->hold_count >= $count_in_master)) {

                  $res['status']="success";
                  $res['message']="Your Order status cannot been changed to Hold";
                  $res['data']=[];
              }

              else {
                $holdstatus='17014';
                $this->_picker->ChangeOrderstatus($array,$holdstatus);

                $result=$this->_picker->Ordercomments($array,$holdstatus);

                if(!empty($result))   
                  {
                    $res['status']="success";
                    $res['message']="Successfully Order status has been changed to Hold";
                    $res['data']=[];

                  }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="Failed to process your request";
                    $res['data']=[];
                  }
                } 
                  $response=json_encode($res); 
                  return $response;

          }else{
           return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          } 
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'OrderId is not sent', 'data' => []));
          }
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'deliver token is not sent', 'data' => []));
          }





      }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      } 
}
  
/*
      * Class Name: getStatusCount
      * Description: Function used to 
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28th Oct 2016
      * Modified Date & Reason: 
    */


  public function getStatusCount() {

    try{


      $array = json_decode($_POST['data'],true);


         if (isset($array['flag']) && !empty($array['flag']))
         {
          
          $flag = $array['flag'];
           
          }else
           {
             
             $flag='';
            
         }



    if($flag==1)
    {
      if(isset($array['picker_token']) && $array['picker_token']!='') {    
   

        $valToken = $this->categoryModel->checkCustomerToken($array['picker_token']);

          if($valToken>0) {


                       $user_data= $this->categoryModel->getUserId($array['picker_token']); 
                       

                        if(isset($array['date']) && !empty($array['date']))
                        {
                        $date = $array['date'];
                        }
                        else
                        {
                      
                       $date=date("Y-m-d");
                        }


                     $count = $this->_picker->getPickerCount($user_data[0]->user_id,$date);
                        
                          if (!empty($count))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "PickerCount",
                                    'data' => $count
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

          }else{
           return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          } 
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Token is not sent', 'data' => []));
          }

      }else{

         if(isset($array['deliver_token']) && $array['deliver_token']!='') {    
   

        $valToken = $this->categoryModel->checkCustomerToken($array['deliver_token']);

          if($valToken>0) {

            
                       $user_data= $this->categoryModel->getUserId($array['deliver_token']); 
                       

                        if(isset($array['date']) && !empty($array['date']))
                        {
                        $date = $array['date'];
                        }
                        else
                        {
                      
                       $date=date("Y-m-d");
                        }


                     $count = $this->_picker->getDeliveryCount($user_data[0]->user_id,$date);
                      
                     $pjp_name = $this->_picker->getBeatName();
                     

                          if (!empty($count))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "DeliverCount",
                                    'data' => $count,
                                    'route_name'=>$pjp_name
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => [],
                                    'route_name'=>[]
                                   // 'count' =>$count
                                  ));
                               }


          }else{
           return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          } 
          } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Token is not sent', 'data' => []));
          }

      }    

      }catch (Exception $e)
      {
          return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
      } 
}



/*
      * Class Name: getCollectiondetails
      * Description: Function used to  retrive collection amount based on delivery id
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 2nd Nov 2016
    *modified 06 feb 2017
      * Modified Date & Reason: 
    */


public function getCollectiondetails() {

try{

       $array = json_decode($_POST['data'],true);

       if(isset($array['deliver_token']) && $array['deliver_token']!='') { 

       $valToken = $this->categoryModel->checkCustomerToken($array['deliver_token']);

            if($valToken>0) { 

             $user_data= $this->categoryModel->getUserId($array['deliver_token']); 
             $user_id=$user_data[0]->user_id; 
      if(isset($array['date']) && !empty($array['date']))
                {
                $date = $array['date'];
                }
                else
                {
               $date=date("Y-m-d");
                }
             $result = $this->_picker->getCollectiondata($date,$user_id);

             if(!empty($result))   
                  {
                    $res['status']="success";
                    $res['message']="getCollectiondetails";
                    $res['data']=$result;

                  }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="No data";
                    $res['data']=[];
                  }
        
                  $response=json_encode($res); 
                  return $response;     


      }else{
       return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
      } 
      
      } else {
         return json_encode(Array('status' => 'failed', 'message' =>'Deliver token is not sent', 'data' => []));
      }



      }catch (Exception $e)
      {
          return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
      } 
}

/** * [putIntorequestLog description]
    * @param  [string] $api_name [Api Name]
    * @param  [string] $data     [Data pushed latter on put this into the mongo central for 
    *                             Work around ]
    * @return [type]           [description]
    */
   public function putIntorequestLog($data) {

       try {

        
           $folder_path = trim(storage_path() . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "Remitance_logs" . DIRECTORY_SEPARATOR . " ");
           
           if(!is_dir($folder_path)){
              mkdir($folder_path,0777,true);
           }

           $write_file =  'remittance.log';
           $file_name = $folder_path . $write_file;
           $write_data = '' . PHP_EOL;
           $write_data .= 'Date:' . date('d-m-Y H:i:s') . PHP_EOL;
           $write_data .= 'Request Data ' . PHP_EOL;
           $write_data .= $data . PHP_EOL;
           $file = fopen($file_name, "a");
           fwrite($file, $write_data);
           fclose($file);
           return true;
       } catch (ErrorException $e) {
           $order_id = 0;
           $message = $e->getTraceAsString();
           return false;
       }
   }


/*
      * Class Name: remittanceHistory
      * Description: Function used to  save collection amount data
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 8th Nov 2016
      * Modified Date & Reason: 
    */


public function remittanceHistory() {

try{ 

       if(isset($_POST['data']) && $_POST['data']!=''){

       $this->putIntorequestLog($_POST['data']);

       $array = json_decode($_POST['data'],true);

       if(isset($array['deliver_token']) && $array['deliver_token']!='') { 
     
     
          //nul validation checking added on 14 dec 2016
          $order_summary_array = sizeof($array['order_summary']);
                      for($i=0;$i<$order_summary_array;$i++)
                      {

                        $hub_id = $array['order_summary'][$i]['hub_id'];
                        $le_wh_id = $array['order_summary'][$i]['le_wh_id'];
                        $invoice_code = $array['order_summary'][$i]['invoice_code'];
                        $total_cash = $array['order_summary'][$i]['total_cash'];
                        $total_cheque = $array['order_summary'][$i]['total_cheque'];
                        $total_online = $array['order_summary'][$i]['total_online'];
                        $total_pos = (isset($array['order_summary'][$i]['total_pos'])) ? $array['order_summary'][$i]['total_pos']:0;
                        $amount_submitted= $array['order_summary'][$i]['amount_submitted'];
                        //$denominations= $array['order_summary'][$i]['denominations'];

                        if($hub_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'hub_id not sent','data'=>"")));  
                          die;
                        }
                        if($le_wh_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'le_wh_id not sent','data'=>"")));  
                          die;
                        }
                        else if($invoice_code == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'Invoice code not sent','data'=>""))); 
                          die;
                        }
                        else if($total_cash == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'Total cash not sent','data'=>""))); 
                          die;
                        }
                        else if($total_cheque == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'Total Cheque not sent','data'=>""))); 
                          die;
                        }
                        else if($total_online == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'Total Online not sent','data'=>""))); 
                          die;
                        }
                        else if($total_pos == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'Total POS not sent','data'=>""))); 
                          die;
                        }
                        else if($amount_submitted == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'Amount Submittednot sent','data'=>""))); 
                          die;
                        }
                                                        
                      }

        if(count($array['order_summary']) != 0 ) {

        $valToken = $this->categoryModel->checkCustomerToken($array['deliver_token']);
       
            if($valToken>0) { 

             $user_data= $this->categoryModel->getUserId($array['deliver_token']); 
             $user_id=$user_data[0]->user_id; 

             $result = $this->_picker->collectionRemittanceHistory($user_id,$array);

             if(!empty($result))   
                  {
                    $res['status']="success";
                    $res['message']="Details submitted successfully";
                    $res['data']=[];

                  }
                  else
                  { 
                    $res['status']="failed";
                    $res['message']="Unable to process your request";
                    $res['data']=[];
                  }
        
                  $response=json_encode($res); 
                  return $response;     


      }else{
       return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
      } 
      } else {
         return json_encode(Array('status' => 'failed', 'message' =>'Already Details Submitted', 'data' => []));
      }
      
      } else {
         return json_encode(Array('status' => 'failed', 'message' =>'deliver token is not sent', 'data' => []));
      }
      } else {
         return json_encode(Array('status' => 'failed', 'message' =>'Please pass the parameters', 'data' => []));
      }

      }catch (Exception $e)
      {
          return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
      } 
}

   /*
  * Function Name: getInvoiceOderlist()
  * Description: Used to get invoice odrer list based on curret date
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 3 Jan 2017
  * Modified Date & Reason:
  */
  public function getInvoiceOderlist()
  {

   try{ 

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 
 
        if(isset($arr->admin_token) && !empty($arr->admin_token) )
        {
                  
                 $checkAdminToken = $this->categoryModel->checkCustomerToken($arr->admin_token);
                
                  if($checkAdminToken>0)
                  {  
                     
                       $date=date("Y-m-d");


                          $beat= (isset($array->beat) && $array->beat!='')? $array->beat:'';
                          $hub= (isset($array->hub) && $array->hub!='')? $array->hub:'';
                        
                           $data = $this->_picker->getInvoiceOderlist($date,$beat,$hub);
                         
                           if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getInvoiceOderlist",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

                     
                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass picker token",'data'=> [])));die;

               }
                         
      
       }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

       }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>  []);
      } 
    

    }




        /*
  * Function Name: getInvoiceOderlist()
  * Description: Used to get invoice odrer list based on curret date
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 3 Jan 2017
  * Modified Date & Reason:
  */
  public function getcontainerbyorder()
  {


 try{ 

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 
 


        if(isset($arr->admin_token) && !empty($arr->admin_token) )
        {
                  
                 $checkAdminToken = $this->categoryModel->checkCustomerToken($arr->admin_token);
                
                  if($checkAdminToken>0)
                  {  

                          $order_id= (isset($arr->order_id) && $arr->order_id!='')? $arr->order_id:'';
                         //$hub= (isset($array->hub) && $array->hub!='')? $array->hub:'';
                       
                           $data = $this->_picker->getcontainerbyorder($order_id);
                         
                           if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getInvoiceOderlist",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

                     
                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass picker token",'data'=> [])));die;

               }
                         
      
       }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

       }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>  []);
      } 
    
}



/*
  * Function Name: UpdateGeo()
  * Description: Used to updatedgeo
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
  public function UpdateGeo()
  {

 try{ 

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 
 


        if(isset($arr->customer_token) && !empty($arr->customer_token) )
        {
                  
                 $checkCustomerToken = $this->categoryModel->checkCustomerToken($arr->customer_token);
                
                  if($checkCustomerToken>0)
                  {  
                  
                  if(isset($arr->user_id) && !empty($arr->user_id))
                    {
                     $user_id = $arr->user_id;
                     }
                     else
                     {
                      
                    return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Please send userid",
                                    'data' => []
                                  ));
                      
                     }
                    
                     if(isset($arr->legal_entity_id) && !empty($arr->legal_entity_id))
                     {
                     $legal_entity_id = $arr->legal_entity_id;
                     }
                     else
                     {
                      
                    return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Please send legal_entity_id",
                                    'data' => []
                                  ));
                      
                     }

                      if(isset($arr->latitude) && !empty($arr->latitude))
                     {
                     $latitude = $arr->latitude;
                     }
                     else
                     {
                      
                    return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Please send latitude",
                                    'data' => []
                                  ));
                      
                     }

                      if(isset($arr->longitude) && !empty($arr->longitude))
                     {
                     $longitude = $arr->longitude;
                     }
                     else
                     {
                      
                    return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Please send longitude",
                                    'data' => []
                                  ));
                      
                     }
         
                           $data = $this->_picker->updateGeo($user_id,$legal_entity_id,$latitude,$longitude);
                         
                           if (!empty($data) && $data>=1)
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => " Geo location updated successfully",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

                     
                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass customer token",'data'=> [])));die;

               }
                         
      
       }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

       }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>  []);
      } 
    
}
    


/*
  * Function Name: getDeDetails()
  * Description: Used to get delivery executive
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 5 Jan 2017
  * Modified Date & Reason:
  */
   public function getDeDetails()
  {

 try{ 

   if(isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 
 
        if(isset($arr->admin_token) && !empty($arr->admin_token) )
        {
                  
                 $checkCustomerToken = $this->categoryModel->checkCustomerToken($arr->admin_token);
                
                  if($checkCustomerToken>0)
                  {  
                   
                   if(isset($arr->user_id) && !empty($arr->user_id))
                   {
                    
                   $user_id=$arr->user_id;

                   }else{
                   
                     return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Please send user_id",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  

                   }

                    if(isset($arr->legal_entity_id) && !empty($arr->legal_entity_id))
                   {
                    
                   $legal_entity_id=$arr->legal_entity_id;

                   }else{
                   
                     return json_encode(Array(
                                    'status' => "failed",
                                    'message' => "Please send legal_entity_id",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  

                   }

                    if(isset($arr->flag) && !empty($arr->flag))
                   {
                    
                   $flag=$arr->flag;

                   }else{
                   
                   $flag='';            

                   }
                  
                  if($flag==1)
                   {

                     $hubs=$this->_ffdm->getWarehouseHubs($user_id,$legal_entity_id,2);
                     $data = $this->_picker->gethubWareName($hubs);
                   }else{
                       //$team=$this->_role->getTeamByUser($user_id);
                       //$data = $this->_picker->getUsersByRoleNameId(['Delivery Executive','Delivery Executive(DC)','Delivery Executive(HUB)','HUB Incharge'],$team);
                        $roleRepo = new RoleRepo();
                        $users = $roleRepo->getUsersByFeatureCode('DELR002');
                        $data = json_decode(json_encode($users,1), 1);
                   }
                         
                           if (!empty($data))
                                 {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "getDeDetails",
                                    'data' => $data
                                  //  'count'=>$count
                                  ));
                                  }
                                   else
                                  {
                                  return json_encode(Array(
                                    'status' => "success",
                                    'message' => "No data",
                                    'data' => []
                                   // 'count' =>$count
                                  ));
                               }

                     
                   //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass customer token",'data'=> [])));die;

               }
                         
      
       }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

       }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>  []);
      } 
    
}




  /*
  * Function Name: getInventoryByProductlist()
  * Description: Used to get invoice odrer list based on curret date
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 3 Jan 2017
  * Modified Date & Reason:
  */
  public function getInventoryByProductlist(){
   
        try{ 
         $data = Input::all();
        if(empty($data)) {
          return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
        }
           $postData = json_decode($data['data'], true);
        if(isset($postData['admin_token']) && !empty($postData['admin_token']) ) {
         $checkAdminToken = $this->categoryModel->checkCustomerToken($postData['admin_token']);
        if($checkAdminToken>0)
        {  
        $reponse= $this->_picker->getInventoryByProductlist($postData['offset_limit']);
        if (!empty($reponse))
        {
            return json_encode(Array( 'status' => "success",  'message' => "getInventoryByProductlist", 'data' => $reponse));
        } else {                
            return json_encode(Array('status' => "success",'message' => "No data",'data' => [] ));
        }

        }
        else{
             return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
        }

        }
        else
        {
        return json_encode(array('status'=>"failed",'message'=> "Pass Admin token",'data'=> []));
        }

      }
        catch(Exception $e) {
    
        return Response::json(array('Status' => 404, 'Message' => 'Failed'));
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }
    public function getApprovalOptions() {
        try {
            $data = Input::all();
            if (empty($data)) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
            }
            $postData = json_decode($data['data'], true);
            if (isset($postData['user_token']) && $postData['user_token'] != '') {                
                $checkAdminToken = $this->categoryModel->checkCustomerToken($postData['user_token']);
                if ($checkAdminToken > 0) {
                    $user_data = $this->categoryModel->getUserId($postData['user_token']);
                    $user_id = isset($user_data[0]->user_id) ? $user_data[0]->user_id : '';
                    $uniqueId = (isset($postData['uniqueId']) && $postData['uniqueId'] != '')?$postData['uniqueId']:'';
                    $status = (isset($postData['approval_status']) && $postData['approval_status'] != '')?$postData['approval_status']:'';
                    $approval_module = (isset($postData['approval_module']) && $postData['approval_module'] != '')?$postData['approval_module']:'Stock Take History';
                    if ($user_id > 0) {
                        if ($uniqueId > 0) {
                            $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                            $status = ($status != 0 && $status != '') ? $status : 'drafted';
                            if ($status != 1) {
                                $res_approval_flow_func = $approvalFlowObj->getApprovalFlowDetails($approval_module, $status, $user_id);
                                $approvalOptions = array();
                                $approvalVal = array();
                                $current_status = '';
                                $financeStatuses = [57118,57032];
                                $acceptStatuses = [57107,57119,57120];
                                if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                                    $current_status=$res_approval_flow_func["currentStatusId"];
                                    if($approval_module =='Purchase Order'){
                                        $srmPoModel = new \App\Modules\Cpmanager\Models\SrmpoOrderModel();
                                        $podetails = $srmPoModel->getPoinfo($uniqueId);
                                        $payment_status =isset($podetails->payment_status)?$podetails->payment_status:'';
                                        $po_status =isset($podetails->po_status)?$podetails->po_status:'';
                                        $approval_status =isset($podetails->approval_status)?$podetails->approval_status:'';
                                    }
                                    foreach ($res_approval_flow_func["data"] as $options) {
                                        if($approval_module =='Purchase Order'){
                                            if($po_status!="87003" && $po_status!="87004"){
                                                if(in_array($approval_status,[57117]) && $po_status=="87001"){
                                                    
                                                }else{
                                                    if(in_array($options['nextStatusId'], $financeStatuses)){
                                                        if ($payment_status == 57118 && $options['nextStatusId'] != 57118) {
                                                            $approvalOptions[] = array('nextStatusId' => $options['nextStatusId'], 'isFinalStep' => $options['isFinalStep'], 'condition' => $options['condition']);
                                                        } else if ($payment_status == $options['nextStatusId'] || $payment_status == 57032) {
                                                            
                                                        } else {
                                                            $approvalOptions[] = array('nextStatusId' => $options['nextStatusId'], 'isFinalStep' => $options['isFinalStep'], 'condition' => $options['condition']);
                                                        }
                                                    } else {
                                                        if(in_array($approval_status, $acceptStatuses) && $options['nextStatusId']==57035){
                                                        }else{
                                                            $approvalOptions[] = array('nextStatusId' => $options['nextStatusId'], 'isFinalStep' => $options['isFinalStep'], 'condition' => $options['condition']);
                                                        }
                                                    }
                                                }
                                            }
                                        }else{
                                            $approvalOptions[] = array('nextStatusId' => $options['nextStatusId'], 'isFinalStep' => $options['isFinalStep'], 'condition' => $options['condition']);
                                        }
                                    }
                                }
                                switch ($approval_module) {
                                    case (string)'Purchase Order':{
                                        $table_name= 'po';
                                        $unique_column= 'po_id';
                                        break;
                                    }
                                    case (string)'Stock Take History':{
                                        $table_name= 'stocktake_history';
                                        $unique_column= 'stock_take_id';
                                        break;
                                    }
                                    case (string)'Purchase Return':{
                                        $table_name= 'purchase_returns';
                                        $unique_column= 'pr_id';
                                        break;
                                    }
                                    default:{
                                        $table_name= 'stocktake_history';
                                        $unique_column= 'stock_take_id';
                                        break;
                                    }
                                }
                                $approvalVal = array('current_status' => $current_status,
                                    'approval_unique_id' => $uniqueId,
                                    'approval_module' => $approval_module,
                                    'table_name' => $table_name,
                                    'unique_column' => $unique_column,
                                );
                                if (isset($approvalOptions) && is_array($approvalOptions) && count($approvalOptions) > 0) {
                                    return json_encode(Array('status' => 'success', 'message' => 'approval options', 'approvalVal' => $approvalVal, 'approvalOptions' => $approvalOptions));
                                } else {
                                    return json_encode(Array('status' => 'success', 'message' => 'No approval options available', 'approvalOptions' => 'no options'));
                                }
                            } else {
                                return json_encode(Array('status' => 'success', 'message' => 'Already Approved', 'data' => []));
                            }
                        } else {
                            return json_encode(Array('status' => 'failed', 'message' => 'stock id should not be empty', 'data' => []));
                        }
                    } else {
                        return json_encode(Array('status' => 'failed', 'message' => 'User id should not be empty', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                }
            } else {
                return json_encode(array('status' => "failed", 'message' => "Please Pass User token", 'data' => []));
            }
        } catch (Exception $e) {
            return Response::json(array('Status' => 404, 'Message' => 'Failed'));
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function submitApprovalStatus() {
        try {
            $params = json_decode($_POST['data'], true);
            if (isset($params['user_token']) && $params['user_token'] != '') {
                $valToken = $this->categoryModel->checkCustomerToken($params['user_token']);
                if ($valToken > 0) {
                    if (isset($params['approval_unique_id']) && $params['approval_unique_id'] != '' &&
                            isset($params['approval_status']) && $params['approval_status'] != '' &&
                            isset($params['approval_comment']) &&
                            isset($params['approval_module']) && $params['approval_module'] != '' &&
                            isset($params['table_name']) && $params['table_name'] != '' &&
                            isset($params['unique_column']) && $params['unique_column'] != '' &&
                            isset($params['current_status']) && $params['current_status'] != '') {

                        $user_data = $this->categoryModel->getUserId($params['user_token']);
                        $user_id = isset($user_data[0]->user_id) ? $user_data[0]->user_id : '';
                        if ($user_id != '') {                            
                            $uniqueIds = explode(',', $params['approval_unique_id']);
                            $current_status = $params['current_status'];
                            $approval_status = $params['approval_status'];
                            $approval_module = $params['approval_module'];
                            $approval_comment = $params['approval_comment'];
                            $table = $params['table_name'];
                            $unique_column = $params['unique_column'];                            
                            $msg = [];
                            if (is_array($uniqueIds)) {
                                foreach ($uniqueIds as $unique_id) {
                                    switch ($approval_module) {
                                        case (string) 'Purchase Order': {
                                                $srmPoModel = new \App\Modules\Cpmanager\Models\SrmpoOrderModel();
                                                $details = $srmPoModel->getPoinfo($unique_id);
                                                break;
                                            }
                                        case (string) 'Stock Take History': {
                                                $details = $this->_picker->getStockTakeDetails($unique_id);
                                                break;
                                            }
                                        case (string) 'Purchase Return': {
                                                $prModel = new \App\Modules\PurchaseReturn\Models\PurchaseReturn();
                                                $details = $prModel->getPRDetails($unique_id);
                                                break;
                                        }
                                        default: {
                                                $msg[] = 'Approval Module not found for ' . $unique_id;
                                                break;
                                            }
                                    }
                                    if (isset($details->approval_status) && $details->approval_status == $current_status) {
                                        $this->_picker->updateStatusAWF($table, $unique_column, $unique_id, $approval_status, $user_id);
                                        $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                                        $nextstatus = explode(',',$approval_status);
                                        $next_status_id = $nextstatus[0];
                                        $approvalFlowObj->storeWorkFlowHistory($approval_module, $unique_id, $current_status, $next_status_id, $approval_comment, $user_id);
                                        $msg[] = 'Approval submitted successfully for ' . $unique_id;
                                    } else {
                                        $msg[] = 'current status not match for ' . $unique_id;
                                    }
                                }
                            }
                            return json_encode(Array('status' => 'success', 'message' => $msg, 'data' => []));
                        } else {
                            return json_encode(Array('status' => 'failed', 'message' => 'User id should not be empty', 'data' => []));
                        }
                    } else {
                        return json_encode(Array('status' => 'failed', 'message' => 'Approval Unique ID, Approval status, Approval comment, Current status, Approval Module, Table Name, Unique Column all must be sent', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                }
            } else {
                return json_encode(Array('status' => 'failed', 'message' => 'User token is not sent', 'data' => []));
            }
        } catch (Exception $e) {
            return Array('status' => 'failed', 'message' => "Internal server error", 'data' => []);
        }
    }
    public function getApprovalHistory() {
        try {
            $data = json_decode($_POST['data'], true);
            if (isset($data['user_token']) && $data['user_token'] != '') {
                $valToken = $this->categoryModel->checkCustomerToken($data['user_token']);
                if ($valToken > 0) {
                        $data['approval_module'];
                        $approval_module = $data['approval_module'];
                        $id = $data['id'];
                        $history = $this->_picker->getApprovalHistory($approval_module, $id);
                        if(is_array($history) && count($history)>0){
                            $msg = 'Approval History';
                            $history_data = $history;
                        }else{
                            $msg = 'No history found';
                            $history_data = [];
                        }
                        return json_encode(Array('status' => 'success', 'message' => 'Approval History', 'data' => $history_data));                        
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                }
            } else {
                return json_encode(Array('status' => 'failed', 'message' => 'User token is not sent', 'data' => []));
            }
        } catch (Exception $e) {
            return Array('status' => 'failed', 'message' => "Internal server error", 'data' => []);
        }
    }
 /*
* Function Name: containermaster()
* Description: Used to get invoice odrer list based on curret date
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2017
* Version: v1.0
* Created Date: 14 Feb 2017
* Modified Date & Reason:
*/
public function containermaster($token = "", $crate_code = array(), $status = "", $transaction_status = "",$le_wh_id=""){
	try{ 
		if(!empty($crate_code) && $status !=''){
			$res= $this->_picker->containerMaster($crate_code, $status, $transaction_status, $token,$le_wh_id);
			return true;
		}
		$data = Input::all();
		$request['parameters'] = $data;
		$request['apiUrl'] = 'containermaster';
		$this->_containerapi = new ContainerapiModel();
		$this->_containerapi->logApiRequests($request);
		if(empty($data)) {
			return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
		}
		$postData = json_decode($data['data'], true);

		//print_r($postData); exit;

		if(isset($postData['admin_token']) && !empty($postData['admin_token']) ) {

			$checkAdminToken = $this->categoryModel->checkCustomerToken($postData['admin_token']);

			$msg='';
      $le_wh_id = isset($postData['le_wh_id'])?$postData['le_wh_id']:env('LE_WH_ID');
			$completedOrderStatus = array(17008, 17009, 17015, 17022, 17007, 17023);//COMPLETED,CANCELLED BY CUSTOMER,CANCELLED BY EBUTOR,RETURNED,DELIVERED,PARTIALLY DELIVERED
			if($checkAdminToken>0)
			{  
				if (isset($postData['crate_code']) && $postData['status'] != '')  {
					$success = array(); $failed = array();
					if($postData['status'] == 136001){
						foreach($postData['crate_code'] as $crate){
							$orderId = $this->_picker->getContainerOrder($crate);
							//print_r($orderId);
							if($orderId != ''){
								$odrStatus = $this->_picker->getOrderStatus($orderId);
								if(!in_array($odrStatus, $completedOrderStatus))
									$failed[] =  $crate;
								else
									$success[] = $crate;
							}
							else
								$success[] = $crate;
						}
					}
					else
						$success = $postData['crate_code'];
                                        if(!empty($success)){
                                            $reponse= $this->_picker->containerMaster($success, $postData['status'], $postData['transaction_status'], $postData['admin_token'],$le_wh_id);
                                        }
                                        $msg = array('Success'=>$success, 'Failed'=>$failed);
					//$msg = json_encode($msg);
				}
				else{
					$msg = 'Please pass valid inputs';
				}           
				if (!empty($reponse))
				{
					return json_encode(Array( 'status' => "success",  'message' => "cratemaster", 'data' => []));
				} else { 
					return json_encode(Array('status' => "success",'message' => $msg,'data' => [] ));
				}
			}
			else{ 
				return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
			}
		}
		else
		{ 
			return json_encode(array('status'=>"failed",'message'=> "Pass Admin token",'data'=> [])); 
		}

	} catch(Exception $e) {
		return Response::json(array('Status' => 404, 'Message' => 'Failed'));
		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
	}

}
/*
* Function Name: checkcontainer()
* Description: Used to get invoice odrer list based on curret date
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2017
* Version: v1.0
* Created Date: 14 feb 2017
* Modified Date & Reason:
*/
  public function checkcontainer(){
   
    try{ 
         $data = Input::all();
          $request['parameters'] = $data;
         $request['apiUrl'] = 'checkcontainer';
          $this->_containerapi = new ContainerapiModel();
          $this->_containerapi->logApiRequests($request);
      
        if(empty($data)) {
          return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
        }
        $postData = json_decode($data['data'], true);
     
        if(isset($postData['admin_token']) && !empty($postData['admin_token']) ) {

                $checkAdminToken = $this->categoryModel->checkCustomerToken($postData['admin_token']);


        $msg='';
        $le_wh_id = isset($postData['le_wh_id'])?$postData['le_wh_id']:env('LE_WH_ID');
         if($checkAdminToken>0)
        {  
            if (isset($postData['crate_code']) && !empty($postData['crate_code']))  {  
                $reponse= $this->_picker->checkContainer($postData['crate_code']);
         if (!empty($reponse))
        {
             $reponse = json_decode(json_encode($reponse), true);
       
                if(isset($reponse[0]['status']) && $reponse[0]['status'] == 136001){
                    if(isset($reponse[0]['transaction_status']) && $reponse[0]['transaction_status'] == 137001){
                        if($this->containermaster($postData['admin_token'], array($postData['crate_code']), 136003, '',$le_wh_id)){
                            $msg = true;
                        } else {
                            $msg = false;
                        }
                    } else {
                        $msg = false;
                    }
                }
                else{
                    $msg = false;
                }
                
            
            return json_encode(Array( 'status' => "success",  'message' =>$msg));
        } else { 
            $msg = false;
            return json_encode(Array('status' => "fail",'message' => $msg));
            
        }
        
        }
        else{ 
            $msg = false;
            return json_encode(Array('status' => "fail",'message' => $msg));

        }
        } else{ 
            return json_encode(array('status'=>"failed",'message'=> "Pass Admin token",'data'=> [])); 
            
        }
  
  }

      }
        catch(Exception $e) {
        return Response::json(array('Status' => 404, 'Message' => 'Failed'));
        Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
    
    }
    /*
    * Function name: retailerCollectionPendingOrders
    * Description: the function is used to get all collection pending orders to be collected by collection agent
    * Author: Raju.A
    * Copyright: ebutor 2018
    * Version: v1.0
    * Created Date: 22th March 2018 :/
    * Modified Date & Reason:
    */
    public function retailerCollectionPendingOrders() {
        try {
            $data = Input::all();
            if (empty($data)) {
                return Response::json(array('status' => "failed", 'message' => "Invalid input data"));
            }
            $postData = json_decode($data['data'], true);
            if (isset($postData['user_token']) && !empty($postData['user_token'])) {
                $checkAdminToken = $this->categoryModel->checkCustomerToken($postData['user_token']);
                if ($checkAdminToken > 0) {
                    if (isset($postData['cust_token']) && !empty($postData['cust_token'])) {
                        $user_data = $this->categoryModel->getUserId($postData['cust_token']);
                        $retailer_id = isset($user_data[0]->user_id) ? $user_data[0]->user_id : '';
                        $retailer_le_id = isset($user_data[0]->legal_entity_id) ? $user_data[0]->legal_entity_id : '';
                        $ordersdata = $this->_picker->retailerCollectionPendingOrders($retailer_le_id);
                        if(count($ordersdata)>0){
                            return json_encode(array('status' => "success", 'message' => "orders list", 'data' => $ordersdata));
                        }else{
                            return json_encode(array('status' => "success", 'message' => "No pending orders", 'data' => []));
                        }
                    } else {
                        return json_encode(array('status' => "failed", 'message' => "Please send customer token", 'data' => []));
                    }
                } else {
                    return json_encode(array('status' => "session", 'message' => "Your Session Has Expired. Please Login Again.", 'data' => []));
                }                
            }
        } catch (Exception $ex) {
            
        }
    }
    /*
      * Class Name: saveCustCollection
      * Description: Function used to save collections collected by collection agent
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2018
      * Version: v1.0
      * Created Date: 22nd March 2018
      * Modified Date & Reason: 
    */
   public function saveCustCollection($data = []) {
        try {
            $array = (count($data) == 0) ? json_decode($_POST['data'], true) : $data;
            if (isset($array['user_token']) && $array['user_token'] != '') {
                if (isset($array['order_id']) && $array['order_id'] != '') {
                    if (isset($array['invoice_id']) && $array['invoice_id'] != '') {
                        $valToken = $this->categoryModel->checkCustomerToken($array['user_token']);
                        if ($valToken > 0) {
                            /**
                             * to retrive image path
                             */
                            if (isset($_FILES['pod_url'])) {
                                if (!empty($_FILES['pod_url']['name'])) {
                                    $allowed = array("application/pdf", "image/jpeg", "image/png", "image/gif", "image/jpg");
                                    if (!in_array(strtolower($_FILES['pod_url']['type']), $allowed)) {
                                        $res['message'] = "Please upload pdf/jpeg/png/gif";
                                        $data['status'] = "failed";
                                        $data['data'] = "";
                                        $final = json_encode($data);
                                        print_r($final);
                                        die;
                                    }
                                }
                                $tin = $_FILES['pod_url'];
                                $filepath1_move = date("Y-m-d-H-i-s") . "_" . $_FILES['pod_url']['name'];
                                $filepath1 = "uploads/cp/" . $filepath1_move;
                                $result = move_uploaded_file($_FILES['pod_url']['tmp_name'], "uploads/cp/" . $filepath1_move);
                                $image = $this->repo->uploadToS3($filepath1, 'collections', 2);
                            } else {
                                $image = '';
                            }


                            $user_data = $this->categoryModel->getUserId($array['user_token']);
                            $user_id = $user_data[0]->user_id;

                            #params for ledollection entry  
                            $ifsc_code = (isset($array['ifsc_code']) && $array['ifsc_code'] != '') ? $array['ifsc_code'] : 0;
                            $customer_name = (isset($array['customer_name']) && $array['customer_name'] != '') ? $array['customer_name'] : '';
                            $bank_name = (isset($array['bank_name']) && $array['bank_name'] != '') ? $array['bank_name'] : '';
                            $cheque_date = (isset($array['cheque_date']) && $array['cheque_date'] != '') ? $array['cheque_date'] : '';
                            $branch_name = (isset($array['branch_name']) && $array['branch_name'] != '') ? $array['branch_name'] : '';
                            $discount_applied = (isset($array['discount_applied']) && $array['discount_applied'] != '') ? $array['discount_applied'] : 0;
                            $discount_deducted = (isset($array['discount_deducted']) && $array['discount_deducted'] != '') ? $array['discount_deducted'] : 0;
                            $ecash_applied = (isset($array['ecash_applied']) && $array['ecash_applied'] != '') ? $array['ecash_applied'] : 0;
                            $net_amount = (isset($array['net_amount']) && $array['net_amount'] != '') ? $array['net_amount'] : 0;

                            // $params['payments']= $array['payments'];
                            $params['mode_of_payment'] = $array['payment_mode'];
                            $params['collection_amount'] = $array['amount_collected'];
                            $params['invoice'] = $array['invoice_id'];
                            $params['collected_by'] = $user_id;
                            $params['collected_on'] = date('Y-m-d H:i:s');
                            $params['proof'] = $image;
                            $params['ifsc_code'] = $ifsc_code;
                            $params['customer_name'] = $customer_name;
                            $params['bank_name'] = $bank_name;
                            $params['cheque_date'] = $cheque_date;
                            $params['branch_name'] = $branch_name;
                            $params['reference_num'] = $array['reference_no'];
                            $params['rounded'] = $array['round_of_value'];
                            $params['discount_applied'] = $discount_applied;
                            $params['discount_deducted'] = $discount_deducted;
                            $params['ecash_applied'] = $ecash_applied;
                            $params['net_amount'] = $net_amount;

                            $collectionEntry = $this->paymentmodel->saveCollection($params, $user_id);
                            if ($collectionEntry) {
                                $status = 'success';
                                $message = 'Collections saved successfully';
                                $data = [];
                                $invoiceId = $array['invoice_id'];
                                $orderId = $array['order_id'];
                                $orderdata = $this->paymentmodel->getOrderDetailByInvoiceId($invoiceId);
                                $cust_user_id = isset($orderdata->user_id)?$orderdata->user_id:0;
                                $cashBackAmt = $array['amount_collected'];                                
                                $this->paymentmodel->updateUserEcash($cust_user_id,$cashBackAmt,0,$orderId,'143002');
                                $data['effective_ecash'] = $this->_ecash->getExistingEcash($cust_user_id);
                                $this->paymentmodel->sendLocSms($cust_user_id,$invoiceId,'custLOC',$cashBackAmt);
                                //$this->paymentmodel->calculateCashback($array['order_id'], $array['invoice_id'], ['delivered_by' => $array['user_id'], 'ecash_applied' => $ecash_applied]);
                            } else {
                                $status = 'failed';
                                $message = 'Failed to save collection details';
                                $data = [];
                            }
                            return json_encode(Array('status' => $status, 'message' => $message, 'data' => $data));
                        } else {
                            return json_encode(Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                        }
                    } else {
                        return json_encode(Array('status' => 'failed', 'message' => 'invoice id is not sent', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'OrderId is not sent', 'data' => []));
                }
            } else {
                return json_encode(Array('status' => 'failed', 'message' => 'user token is not sent', 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }


    public function sendOrderNotify(){
      $data = json_decode($_POST['data'],true);
      $status = 0;
      $message = "";
      if(count($data)>0){
          if(isset($data['order_id'])){
              $order_id = $data['order_id'];
              $invoice_amount = $data['invoice_amount'];
              $OrderModel = new \App\Modules\Orders\Models\OrderModel;
              $orderdata = $OrderModel->getOrderDetailById($order_id);
              if(count($orderdata)>0){
                $user_id = $orderdata->cust_user_id;
                $order_code = $orderdata->order_code;
                $aadhar_id = $orderdata->aadhar_id;
                if($aadhar_id!="" && is_numeric($aadhar_id) && strlen($aadhar_id)==12){
                  $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                  $deviceToken = $approvalFlowObj->getRegIds($user_id);
                  $user_data = $approvalFlowObj->getRegIds($user_id);
                  //Log::info('user_id   '.$user_id);
                  if(isset($deviceToken[0]->registration_id)){
                      $deviceToken = $deviceToken;
                    //    Log::info('deviceToken   '.json_encode($deviceToken));
                      $message = "Notification sent successfully!";
                      $status = 1;
                      $notificationMessage = "Please click here to confirm order - ".$order_code;
                      $params_data = array("order_code"=>$order_code,
                            "loan_amount"=>$invoice_amount,
                            "aadhar_id"=>$aadhar_id,
                            "user_id"=>$user_id);
                      // inserting notification details in a table
                      $mfc_delivery_array = array("order_code"=>$order_code,
                            "loan_amount"=>$invoice_amount,
                            "user_id"=>$user_id);
                      $mfc_Check_delivery_array = array("order_code"=>$order_code,
                            "user_id"=>$user_id);

                      $mfc_delivery_data = $this->_picker->checkMFCDeliveryData($mfc_Check_delivery_array);
                      // insert or update 
                      if(count($mfc_delivery_data) == 0){
                        $this->_picker->insertMFCDeliveryStatus($mfc_delivery_array);
                      }else{
                        $mfc_delivery_id = $mfc_delivery_data->mfc_delivery_id;
                        $mfc_delivery_array['updated_at'] = date("Y-m-d-H-i-s");
                        $whereArray = array("mfc_delivery_id"=>$mfc_delivery_id);
                        $this->_picker->updateMFCDeliverydata($mfc_delivery_array,$whereArray);
                      }

                      $params_data = json_encode($params_data);
                      $pushNotification = $this->repo->pushNotifications($notificationMessage, $deviceToken, "MFC_ORDER_CONFIRM",'Ebutor','','','',$params_data);

                  }else{
                      $message = "No Device found for notification!";
                  }
                }else{
                  $message = "Invalid Aadhar Id!";
                }
              }else{
                $message = "Invalid order!";
              }

          }else{
            $message = "Please send Order Id";
          }

      }else{
        $message = "Invalid params";
      }

      $returnArray = array("status"=>$status,
        "data"=>[],
        "message"=>$message);
      return json_encode($returnArray);
    }

    public function updateMFCOrderStatus(){
      $data = json_decode($_POST['data'],true);
      $status = "failed";
      $message = "";
      if(count($data)>0){
          if(isset($data['order_id'])){
              if(isset($data['user_id'])){
                $user_id =  $data['user_id'];
                $order_code = $data['order_id'];
                $mfc_status_id = $data['status_id'];
                $approval_status = $data['approval_status'];
                $status = "success";
                // entering final disbursement data into mfc order tracking table
                // 17032 - Loan Disbursal from MFC
                // 17031 - Loan Proposal from MFC
                $mfc_track = array("order_code"=>$order_code,
                          "user_id"=>$user_id,
                          "status_id"=>$mfc_status_id);
                $mfc_entry_count = $this->_picker->checkMFCOrderStatusCount($mfc_track);
                $mfc_track['approval_status'] = $approval_status;
                if(count($mfc_entry_count)>0){
                    $mfc_id  = $mfc_entry_count[0]->mfc_id;
                    $mfc_track['updated_at'] = date("Y-m-d-H-i-s");
                    $mfc_entry = $this->_picker->updateMFCOrderStatus($mfc_track,$mfc_id);
                }else{
                    $mfc_entry = $this->_picker->insertMfcOrderTrack($mfc_track);
                }
                
                $OrderModel = new \App\Modules\Orders\Models\OrderModel;
                $poObj = new PurchaseOrder();
                    // getting order id
                $db_order_id = $poObj->getOrderIdByCode($order_code);
                
                if($mfc_status_id == 17031){
                    // 17030 MFC PENDING STATUS
                    
                    if($db_order_id!=""){
                      // getting order is in MFC PENDING
                      $order_status = $OrderModel->getOrderStatusById($db_order_id);
                      if($approval_status == 1 && $order_status->order_status_id == 17030){
                        $data_array = array("order_id"=>$db_order_id);
                        $open_status = 17001;
                        // changimg to open status
                        $this->_picker->ChangeOrderstatus($data_array,$open_status);
                      }
                    }   
                }

                $loan_type = "Proposal";
                $loan_status = "Accepted";
                $open_status = 17001;

                if($approval_status == 0){
                  $loan_status = "Rejected";
                }

                if($mfc_status_id == 17032){
                  $loan_type = "Disbursement";
                  $open_status = 17007;
                  // if disbursement is approved then we are updating delivery column in mfc_delivery_details column
                  if($approval_status == 1){
                    $whereArray = array("order_code"=>$order_code,
                      "user_id"=>$user_id,
                      "is_delivered"=>0);
                    $this->_picker->updateMFCDeliverydata(["is_delivered"=>1],$whereArray);

                  }
                }

                $comment = "Loan ".$loan_type. " ".$loan_status. " From MFC";
                
                $commentArr = array('entity_id'=>$db_order_id, 
                                'comment_type'=>17,
                                'comment'=>(string)$comment,
                                'commentby'=>0,
                                'created_by'=>"",
                                'order_status_id'=>$open_status,
                                'created_at'=>date("Y-m-d-H-i-s"),
                                'comment_date'=>date("Y-m-d-H-i-s")
                                );

                $OrderModel->saveComment($commentArr);

                $loan_status = ($mfc_status_id == 17031) ? "Proposed" : "Disbursed";
                if($mfc_entry>0){
                    $message = "Loan ".$loan_status." Successfully";
                }else{
                  $message = "Loan ".$loan_status." Successfully,But unable to entry!";
                }
              }else{
                $message = "Please send User Id";
              }

          }else{
            $message = "Please send Order Id!";
          }

      }else{
        $message = "Invalid params";
      }

      $returnArray = array("status"=>$status,
        "data"=>[],
        "message"=>$message);
      return json_encode($returnArray);
    }

    public function checkMFCOrderStatus(){
      $data = json_decode($_POST['data'],true);
      $status = 0;
      $message = "";
      if(count($data)>0){
          if(isset($data['order_id'])){
              $order_id = $data['order_id'];
              $OrderModel = new \App\Modules\Orders\Models\OrderModel;
              $orderdata = $OrderModel->getOrderDetailById($order_id);
              if(count($orderdata)>0){
                $user_id = $orderdata->cust_user_id;
                $order_code = $orderdata->order_code;
                $aadhar_id = $orderdata->aadhar_id;
                $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                $deviceToken = $approvalFlowObj->getRegIds($user_id);
                $user_data = $approvalFlowObj->getRegIds($user_id);
                // entering final disbursement data into mfc order tracking table
                // 17032 - Loan Disbursed from MFC
                $mfc_track = array("order_code"=>$order_code,
                          "user_id"=>$user_id,
                          "status_id"=>17032,
                          "approval_status"=>1);
                $mfc_entry_count = $this->_picker->checkMFCOrderStatusCount($mfc_track);
                if(count($mfc_entry_count)>0){
                    $status = 1;
                    // $notificationMessage = "Order Delivered - ".$order_code;
                    // $pushNotification = $this->repo->pushNotifications($notificationMessage, $deviceToken, "MFC_ORDER_DELIVERED");
                    $message = "Loan Disbursed Successfully";
                }else{
                  $message = "Loan Not Yet Disbursed!";
                }
              }else{
                $message = "Invalid order!";
              }

          }else{
            $message = "Please send Order Id";
          }

      }else{
        $message = "Invalid params";
      }

      $returnArray = array("status"=>$status,
        "data"=>[],
        "message"=>$message);
      return json_encode($returnArray);
    }

    public function getMFCDeliveryData(){
      $data = json_decode($_POST['data'],true);
      $status = "failed";
      $message = "";
      $delivery_data = [];
      if(count($data)>0){
          if(isset($data['customer_token'])){
            $customer_token = $data['customer_token'];
            $user_data = $this->categoryModel->getUserId($customer_token);
            if(isset($user_data[0])){
              $user_id = $user_data[0]->user_id;
              $delivery_data = $this->_picker->getMFCDeliveryData($user_id);
              $status = "success";
            }else{
              $message = "Invalid Customer Token!";
            }
          }else{
            $message = "Please send Customer Token!";
          }
      }else{
        $message = "Invalid params";
      }

      $returnArray = array("status"=>$status,
        "data"=>$delivery_data,
        "message"=>$message);
      return json_encode($returnArray);
  }

  public function calculateInstantCashback(){
    $data = json_decode($_POST['data'],true);
    $status = "success";

    $orderId = $data['order_id'];
    $products = $data['products_info'];

    $OrderModel = new \App\Modules\Orders\Models\OrderModel;


    $legal_entity_id = $OrderModel->getOrderInfo(array($orderId),array('cust_le_id'));
    $legal_entity_id = $legal_entity_id[0]->cust_le_id;
    $user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$legal_entity_id)->where("is_parent",1)->first();
    $user_id = isset($user_id->user_id)?$user_id->user_id:0;
    $userId = $user_id;
    $cashbackdata = $this->paymentmodel->calculateInstantCashback($orderId,$products,$userId);

    if($cashbackdata['pendingCashback'] > 0){
      $cashbackdata['cashbackFlag'] = 1;
    }
    if($cashbackdata['cashbackFlag'] == 0){
      $cashbackdata['cashbackToDeduct'] = 0;
    }
    $cashbackdata['finalEcashToDeduct'] = $cashbackdata['cashbackToDeduct'];
    $returnArray = array("status"=>$status,
        "data"=>$cashbackdata,
        "message"=>"Cashback Data");
//    Log::info(json_encode($returnArray));
      return json_encode($returnArray);
  }

  public function sendDeliveryOtp(){

      $data = json_decode($_POST['data'],true);
      $status = "failure";
      if(count($data)>0){
          if(isset($data['flag'])){
            if(isset($data['order_id'])){
                $order_id = $data['order_id'];
                $flag = $data['flag'];
                $OrderModel = new \App\Modules\Orders\Models\OrderModel;
                $orderdata = $OrderModel->getOrderDetailById($order_id);
                $names = $OrderModel->getDeliveryExecutiveName($order_id);
                $dlexename = $names[0]->delivery_name;
                $ffname = $names[0]->ff_name;
                $warehousename = $names[0]->dc_name;
                if(count($orderdata)>0){
                  $is_self = $orderdata->is_self;
                  if($is_self == 1 and $flag == 2){
                    $message = "We are unable to send OTP to FF,Since it is self order!";
                    $data = array("status"=>$status,"data"=>[],"message"=>$message);
                    return json_encode($data);
                  }
                  $otp = mt_rand(100000, 999999);
                  if($flag == 1){
                    // user id of Customer 
                    $user_id = $orderdata->cust_user_id;
                  }else{
                    // user id of ff to deliver partial/full return orders
                    $user_id = $orderdata->created_by;
                  }
                  $order_code = $orderdata->order_code;
                  $this->_leModel = new LegalEntity();
                  $userInfo = $this->_leModel->getUserAndManagerMobileNo($user_id);
                  $mobile_no_manager = $userInfo[0]->manager_mobile_no;
                  $mobile_no = $userInfo[0]->ff_mobile_no;
                  $ffm_userId = $userInfo[0]->user_id_ffm;
                  $date = date('d-m-Y');
                  $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                  $deviceToken = $approvalFlowObj->getRegIds($user_id);
                  $deviceTokenFFm = $approvalFlowObj->getRegIds($ffm_userId);
                  $status = "success";
                  $this->_picker->updateDeliveryOtp($user_id,$otp);
                  $invoice_amount = $data['invoice_amount'];
                  $shop_name = $orderdata->shop_name;
                  $order_status_type = "delivering";
                  $user_type = "retailer";
                  $user_type = "FF";
                  if($flag == 2){
                    $order_status_type = "returning";
                  }
                  $message = "Hi,\n Your OTP for ".$order_status_type." order is ".$otp."  and order no is ".$order_code." of total amount Rs.".$invoice_amount." ";
                  $messageffm = "Order ".$order_code." has been returned from ".$shop_name." store on ".$date." . FF Exec: ".$ffname." , FF Exec Mobile No: ".$mobile_no." , Warehouse Name: ".$warehousename." and Delivery Exec: ".$dlexename." with total amount of Rs. ".$invoice_amount."";
                  $_Dmapiv2Model = new Dmapiv2Model();
                  $_Dmapiv2Model->sendSMS($mobile_no, $message);
                  $_Dmapiv2Model->sendSMS($mobile_no_manager, $messageffm);
                  $notificationMessage = "Order Return  OTP - ".$order_code."-".$otp;
                  $data['status'] = 1;
                  $data['sms_status'] = 1;
                  $data['push_status'] = 0;
                  $data['sms_sent_count'] = 0;
                  $data['push_sent_count'] = 0;
                  $data['mobile_numbers'] = array("mobile_no"=>$mobile_no);
                  $data['mobile_numbers_ffm'] = array("mobile_no_manager"=>$mobile_no_manager);      
                  $data['count_mobile_numbers'] = 1;
                  $data['count_mobile_numbers_ffm'] = 1;
                  $data['message'] = (trim($message));
                  date_default_timezone_set('Asia/Kolkata');
                  $data['created_at'] = date('Y-m-d H:i:s');
                  $data['created_by'] = $user_id;
                  $data['created_by_name'] = '';
                  $communicationMongoModel = new CommunicationMongoModel();
                  $communicationMongoModel->storeData($data);
                  $pushNotification = $this->repo->pushNotifications($notificationMessage, $deviceToken, "push");
                  //$pushNotification = $this->repo->pushNotifications($messageffm, $deviceTokenFFm,"push");
                  $ph  = $mobile_no;
                  $ph1 = substr($ph,3,4);
                  $mobile_no = str_replace($ph1, "XXXX", $mobile_no);
                  $message = "OTP sent successfully to ".$user_type." (".$mobile_no.")!";
                  $status = "success";
                }else{
                  $message = "Invalid order!";
                }

            }else{
              $message = "Please send Order Id";
            }
          }else{
              $message = "Please send flag!";
          }

      }else{
        $message = "Invalid params";
      }
      $data = array("status"=>$status,"data"=>[],"message"=>$message);
      return json_encode($data);
      

  }

  public function verifyDeliveryOtp(){

      $message = "OTP Verified";
      $data = json_decode($_POST['data'],true);
      $status = "failure";
      if(count($data)>0){
          if(isset($data['flag'])){
            if(isset($data['order_id'])){
                $order_id = $data['order_id'];
                $flag = $data['flag'];
                $otp = $data['otp'];
                $OrderModel = new \App\Modules\Orders\Models\OrderModel;
                $orderdata = $OrderModel->getOrderDetailById($order_id);
                if(count($orderdata)>0){
                  $otp = $data['otp'];
                  $order_status_type = "delivered";
                  if($flag == 1){
                    // user id of Customer 
                    $user_id = $orderdata->cust_user_id;
                  }else{
                    // user id of ff to deliver partial/full return orders
                    $user_id = $orderdata->created_by;
                    $order_status_type = "returned";
                  }
                  $order_code = $orderdata->order_code;
                  $mobile_no = $orderdata->phone_no;
                  $approvalFlowObj = new CommonApprovalFlowFunctionModel();
                  $deviceToken = $approvalFlowObj->getRegIds($user_id);
                  $lp_otp = $this->_picker->getDeliveryOtp($user_id);
                  if($lp_otp == $otp){
                    $status = "success";
                    $notificationMessage = "Order ".$order_status_type." - ".$order_code;
                    $pushNotification = $this->repo->pushNotifications($notificationMessage, $deviceToken, "push");
                  }else
                    $message = "Invalid OTP";
                }else{
                  $message = "Invalid order!";
                }

            }else{
              $message = "Please send Order Id";
            }
          }else{
              $message = "Please send flag!";
          }

      }else{
        $message = "Invalid params";
      }

      $data = array("status"=>$status,"data"=>[],"message"=>$message);
      return json_encode($data);
  }


  // function to check inv before verfication
  public function checkInventory(){
      $message = "Success";
      $data = json_decode($_POST['data'],true);
      $status = "failure";
      if(count($data)>0){
        if(isset($data['OrderID'])){
            $order_id = $data['OrderID'];
            $OrderModel = new \App\Modules\Orders\Models\OrderModel;
            $orderdata = $OrderModel->getOrderDetailById($order_id);
            if(count($orderdata)>0){
              $le_wh_id = $orderdata->le_wh_id;
              $products = $data['Details'];
              $errorInvArray = array();
              $totOrderedQty = $OrderModel->getOrderedQtyByOrderId($order_id);
              $totPicQty = 0;
              foreach ($products as $key => $product) {
                $productId = $product['product_id'];
                $pname = $product['ProductName'];
                $qty = $product['QTY'];
                $totPicQty +=$qty;
                $invArr = $OrderModel->getInventory($productId, $le_wh_id);
                $soh = isset($invArr->soh) ? (int)$invArr->soh : 0;
                $flag = 0;
                if($soh > 0 && ($qty > $soh)) {
                  $flag = 1;
                  $pstatus = "Less than available inventory";
                }else if($soh <= 0){
                  $flag = 1;
                  $pstatus = "Zero inventory";
                }
                if($flag == 1){
                  array_push($errorInvArray, array("name"=>$pname,"pick_qty"=>$qty,"status"=>$pstatus));
                }
              }
              if(is_array($errorInvArray) && count($errorInvArray) > 0 ) {
                $data = $errorInvArray;
                $message = "Inventory Error!";
              }else{
                $data = NULL;
                $status = "success";
                $message = "Success";
              }
              
              if($totPicQty < $totOrderedQty){
                $data = NULL;
                $status = "success";
                $message = "Success";
              }
            }else{
              $message = "Invalid order!";
            }
        }else{
          $message = "Please send Order Id";
        }
      }else{
        $message = "Invalid params";
      }
      $data = array("status"=>$status,"data"=>$data,"message"=>$message);
      return json_encode($data);
  }

   public function checkFreeQty($freeData = array()){
    if(count($freeData) > 0){
      $_POST['data'] = $freeData;
    }
    $data = json_decode($_POST['data'],true);
    $status = "success";
    $freeqtybackdata = array();
    $freeqtybackdata['data'] = array(); 
    $freeqtybackdata['pick'] = 0; 
    $message = "Free Qty!";
    if(count($data)>0){
      if(isset($data['customer_token'])){
        $customer_token = $data['customer_token'];
        $user_data = $this->categoryModel->getUserId($customer_token);
        if(isset($user_data[0])){
          $orderId = $data['order_id'];
          $products = $data['products_info'];
          $flag = $data['flag'];
          $OrderModel = new \App\Modules\Orders\Models\OrderModel;

          $legal_entity_id = $OrderModel->getOrderInfo(array($orderId),array('cust_le_id'));
          $legal_entity_id = $legal_entity_id[0]->cust_le_id;
          $user_id = DB::table('users')->select("user_id")->where("legal_entity_id",$legal_entity_id)->where("is_parent",1)->first();
          $user_id = isset($user_id->user_id)?$user_id->user_id:0;
          $userId = $user_id;
          $freeqtybackdata = $this->paymentmodel->checkFreeQty($flag,$orderId,$products,$userId,$legal_entity_id);
          if(count($freeqtybackdata['data'])){
            $status = "false";
          }
        }else{
          $status = "session";
          $message = "Token expired!";
        }
      }else{
        $message = "Please send Customer Token!";
      }
    }
    $pick_total = isset($freeqtybackdata['pick_total'])?$freeqtybackdata['pick_total']:0;

    $returnArray = array("status"=>$status,
        "data"=>array("product"=>$freeqtybackdata['data'],"pick"=>$freeqtybackdata['pick']),"pick_total"=>$pick_total,
        "message"=>$message);
      return json_encode($returnArray);
  
  }
  public function getPendingCashback($user_id){
    $amount = $this->paymentmodel->getPendingCashback($user_id);
    $cashbackdata = array("amount"=>$amount);
    return array("status"=>"success","message"=>"","data"=>$cashbackdata);
  }
}
