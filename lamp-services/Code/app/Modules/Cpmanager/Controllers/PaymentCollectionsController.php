<?php
  /*
    * Filename: PaymentCollectionsController.php
    * Description: This file is used for payment collections
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor@2017
    * Version: v1.0
    * Created date: 16th Jan 2017
    * Modified date: 
  */
  
  /*
    * PaymentCollectionsController is used to manage Payment Collections
    * @author    Ebutor <info@ebutor.com>
    * @copyright ebutor@2017
    * @package   Payments
    * @version:  v1.0
  */ 
  namespace App\Modules\Cpmanager\Controllers;
  use Illuminate\Support\Facades\Input;
  use Session;
  use Response;
  use Log;
  use URL;
  use DB;
  use Lang;
  use Config;
  use View;
  use Illuminate\Http\Request;
  use Utility;
  use App\Modules\Cpmanager\Models\PaymentCollectionsModel;
  use App\Modules\Cpmanager\Models\CategoryModel;
  use App\Modules\Cpmanager\Views\order;
  use App\Http\Controllers\BaseController;
  use App\Modules\DmapiV2\Models\Dmapiv2Model;
  use App\Modules\Orders\Models\OrderModel;
  

  class PaymentCollectionsController extends BaseController {  
    
    protected $order;
    protected $_Dmapiv2Model;
    protected $_orderModel;
    protected $_AssignOrderModel;
  
    public function __construct() {
      $this->order = new OrderModel();
      $this->_Dmapiv2Model = new Dmapiv2Model();
      $this->_orderModel = new OrderModel();
      $this->_PaymentCollectionsModel = new PaymentCollectionsModel();
      $this->categoryModel = new CategoryModel();
    }

    /**
     *  
     */

    /*
    * Class Name: paymentCollections
    * Description: Display remittance details
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2017
    * Version: v1.0
    * Created Date: 16th Jan 2017
    * Modified Date & Reason: 
    */

    public function getRemittanceDetails() {

          try{


             $params= json_decode($_POST['data'],true); 

             if(isset($params['admin_token']) && $params['admin_token']!='') { 


               if(isset($params['user_id']) && $params['user_id']!='') { 

                  $valToken = $this->categoryModel->checkCustomerToken($params['admin_token']);

                  if($valToken>0) { 


                    //$userDetail = $this->categoryModel->getUserId($params['admin_token']);

                    $result = $this->_PaymentCollectionsModel->getRemittanceDetails($params);  

                    if(empty($result)){
                      $message='Remittance details not found';
                      $data=[];

                    }else
                    {
                      $message='getRemittanceDetails';
                      $data= $result;
                    }

                    return json_encode(Array('status'=>'success','message'=>$message,'data'=>$data)); 


                  } else {


                   return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    


                  } 

                } else {

                   return json_encode(Array('status' => 'failed', 'message' =>'User ID is not sent', 'data' => []));
                }
             } 
              else{


                 return json_encode(Array('status' => 'failed', 'message' =>'Admin token is not sent', 'data' => []));
                }
          
          }catch (Exception $e)
          {
              $status = "failed";
              $message = "Internal server error";
              $data = [];
              return Array('status' => $status, 'message' => $message, 'data' => $data);
          } 
    }



    public function getRemittanceCollectionDetails() {

          try{


             $params= json_decode($_POST['data'],true); 

             if(isset($params['admin_token']) && $params['admin_token']!='') { 


               if(isset($params['remittance_id']) && $params['remittance_id']!='') { 

                  $valToken = $this->categoryModel->checkCustomerToken($params['admin_token']);

                  if($valToken>0) { 

                    $result = $this->_PaymentCollectionsModel->getRemittanceCollectionDetails($params['remittance_id']);  

                    if(empty($result)){
                      $message='Remittance details not found';
                      $data=[];

                    }else
                    {
                      $message='getRemittanceCollectionDetails';
                      $data= $result;
                    }

                    return json_encode(Array('status'=>'success','message'=>$message,'data'=>$data)); 


                  } else {


                   return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    


                  } 

                } else {

                   return json_encode(Array('status' => 'failed', 'message' =>'Remittance ID is not sent', 'data' => []));
                }
             } 
              else{


                 return json_encode(Array('status' => 'failed', 'message' =>'Admin token is not sent', 'data' => []));
                }
          
          }catch (Exception $e)
          {
              $status = "failed";
              $message = "Internal server error";
              $data = [];
              return Array('status' => $status, 'message' => $message, 'data' => $data);
          } 
    }      


    public function submitApprovalStatus() {

          try{


             $params= json_decode($_POST['data'],true); 

             if(isset($params['admin_token']) && $params['admin_token']!='') { 


               if(isset($params['approval_unique_id']) && $params['approval_unique_id']!='' &&
                  isset($params['approval_status']) && $params['approval_status']!='' && 
                  isset($params['approval_comment']) && 
                  isset($params['approval_module']) && $params['approval_module']!='' && 
                  isset($params['table_name']) && $params['table_name']!='' && 
                  isset($params['unique_column']) && $params['unique_column']!='' && 
                  isset($params['current_status']) && $params['current_status']!='') { 

                  $valToken = $this->categoryModel->checkCustomerToken($params['admin_token']);

                  if($valToken>0) { 

                    $this->_PaymentCollectionsModel->submitApprovalStatus($params);  
                    return json_encode(Array('status'=>'success','message'=>'Approval submitted successfully','data'=>[])); 


                  } else {


                   return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    


                  } 

                } else {

                   return json_encode(Array('status' => 'failed', 'message' =>'Approval Unique ID, Approval status, Approval comment, Current status, Approval Module, Table Name, Unique Column all must be sent', 'data' => []));
                }
             } 
              else{


                 return json_encode(Array('status' => 'failed', 'message' =>'Admin token is not sent', 'data' => []));
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
   * getPaymentmodeByOrderId() method is used to get payment mode based on orderid
   * @param orderId,admin_token
   * @return Json
   */


    public function getPaymentmodeByOrderId() {

          try{


             $params= json_decode($_POST['data'],true); 

             if(isset($params['admin_token']) && $params['admin_token']!='') { 


               if(isset($params['order_id']) && $params['order_id']!='') { 

                  $valToken = $this->categoryModel->checkCustomerToken($params['admin_token']);

                  if($valToken>0) { 

                    $result = $this->_PaymentCollectionsModel->getPaymentmodeByOrderId($params['order_id']);  

                    if(empty($result)){
                      $message='Order details not found';
                      $data=[];

                    }else
                    {
                      $message='getPaymentmodeByOrderId';
                      $data= $result;
                    }

                    return json_encode(Array('status'=>'success','message'=>$message,'data'=>$data)); 


                  } else {


                   return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    


                  } 

                } else {

                   return json_encode(Array('status' => 'failed', 'message' =>'Order ID is not sent', 'data' => []));
                }
             } 
              else{


                 return json_encode(Array('status' => 'failed', 'message' =>'Admin token is not sent', 'data' => []));
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
   * updatePaymentmodeByOrderId() method is used to get payment mode based on orderid
   * @param orderId,admin_token
   * @return Json
   */


    public function updatePaymentmodeByOrderId() {

          try{


             $params= json_decode($_POST['data'],true); 

             if(isset($params['admin_token']) && $params['admin_token']!='') { 


               if(isset($params['order_id']) && $params['order_id']!='') { 
               if(isset($params['payment_mode']) && $params['payment_mode']!='') {

                  $valToken = $this->categoryModel->checkCustomerToken($params['admin_token']);

                  if($valToken>0) { 

                    $result = $this->_PaymentCollectionsModel->updatePaymentmodeByOrderId($params['order_id'],$params['payment_mode']);  

                    if($result){
                      $message='Payment Mode has been changed successfully';
                      $data=[];

                    }else
                    {
                      $message='Sorry we could not process your request';
                      $data= [];
                    }

                    return json_encode(Array('status'=>'success','message'=>$message,'data'=>$data)); 


                  } else {


                   return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    


                  } 

                } else {

                   return json_encode(Array('status' => 'failed', 'message' =>'Payment Mode is not sent', 'data' => []));
                }

              } else {

                 return json_encode(Array('status' => 'failed', 'message' =>'Order ID is not sent', 'data' => []));
              }

             } 
              else{


                 return json_encode(Array('status' => 'failed', 'message' =>'Admin token is not sent', 'data' => []));
                }
          
          }catch (Exception $e)
          {
              $status = "failed";
              $message = "Internal server error";
              $data = [];
              return Array('status' => $status, 'message' => $message, 'data' => $data);
          } 
    }      



}