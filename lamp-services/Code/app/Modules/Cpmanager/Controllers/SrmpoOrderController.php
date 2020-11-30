<?php
  /*
    * Filename: SrmpoOrderController.php
    * Description: This file is used for manage Srm Orders
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor@2016
    * Version: v1.0
    * Created date: 28th Sept 2016
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
  use Illuminate\Http\Request;
  use App\Modules\Cpmanager\Models\SrmpoOrderModel;
  use App\Modules\Cpmanager\Models\CategoryModel;
  use App\Modules\Roles\Models\Role;
  use App\Central\Repositories\RoleRepo;
  use App\Modules\PurchaseOrder\Controllers\PurchaseOrderController;
  use App\Modules\PurchaseOrder\Models\PurchaseOrder;
  use App\Http\Controllers\BaseController;

  use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
  
  
 class SrmpoOrderController extends BaseController {
    
    public function __construct() {  

      $this->_srmOrder = new SrmpoOrderModel();
      $this->categoryModel = new CategoryModel();
      $this->_role = new Role();
      $this->purchaseorder= new PurchaseOrder();
      $this->PO = new PurchaseOrderController(1); 
       
    }


    /*
      * Class Name: getPolist
      * Description: used to get PO orders list   
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28th Sept 2017
	  *
      * Modified Date & Reason: 
    */
  
  public function getPolist() {

  try{

    $array = json_decode($_POST['data'],true);
        
        if(isset($array['srm_token']) && $array['srm_token']!='') {     
               

          $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

          if($valToken>0)
          { 

            $user_data= $this->categoryModel->getUserId($array['srm_token']); 
            $user_id=$user_data[0]->user_id;         
            $team=$this->_role->getTeamByUser($user_id);

            $supplier_id=(isset($array['supplier_id']) && $array['supplier_id'] != '') ? $array['supplier_id']:'';            
            $orderdata= $this->_srmOrder->getPolist($team,$supplier_id);           

            if(empty($orderdata)){
              $message='PO details not found';
              $data=[];

            }else
            {
              $message='getPolist';
              $data= $orderdata;
            }

            return json_encode(Array('status'=>'success','message'=>$message,'data'=>$data));              
                           
         
          }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
         } else {
             return json_encode(Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []));
       }

     }catch (Exception $e)
      {
          $status = "failed";
          $message = "Internal server error";
          $data = [];
          return Array('status' => $status, 'message' => $message, 'data' => $data);
      }              
 }
 public function getPolistByStatus() {
        try {
            $data = Input::all();
            $array = json_decode($data['data'], true);
            if (isset($array['user_token']) && $array['user_token'] != '') {
                $valToken = $this->categoryModel->checkCustomerToken($array['user_token']);                
                if ($valToken > 0) {
                    $user_data = $this->categoryModel->getUserId($array['user_token']);
                    $user_id = $user_data[0]->user_id;                    
                    $postatus = (isset($array['status']) && $array['status'] != '') ? $array['status'] : 0;
                    $offset = (isset($array['offset']) && $array['offset'] != '') ? $array['offset'] : 0;
                    $perpage = (isset($array['perpage']) && $array['perpage'] != '') ? $array['perpage'] : 10;
                    $totcount = 0;
                    $totcount = $this->_srmOrder->getPolistByStatus($postatus,$offset,$perpage,1,$user_id);
                    $orderdata = $this->_srmOrder->getPolistByStatus($postatus,$offset,$perpage,0,$user_id);
                    if (empty($orderdata)) {
                        $message = 'No data';
                        $data = [];
                    } else {
                        $message = 'getPolist';
                        $data = $orderdata;
                    }
                    return json_encode(Array('status' => 'success', 'message' => $message,'totcount'=>$totcount, 'data' => $data));
                } else {
                    return json_encode(Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                }
            } else {
                return json_encode(Array('status' => 'failed', 'message' => 'srm token is not sent', 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }
    /*
* Function name: getPodetails
* Description: used to get PO order details
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/

    public function getPodetails() {
        try {
            $array = json_decode($_POST['data'], true);
            if (isset($array['srm_token']) && $array['srm_token'] != '') {
                if (isset($array['po_id']) && $array['po_id'] != '') {
                    $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);
                    if ($valToken > 0) {
                        $user_data = $this->categoryModel->getUserId($array['srm_token']);
                        $user_id = $user_data[0]->user_id;
                        $orderdetails = $this->_srmOrder->getPodetails($array['po_id']);
                        if (empty($orderdetails)) {
                            $message = 'PO details not found';
                            $data = [];
                        } else {
                            $approval_flow_func = new CommonApprovalFlowFunctionModel();
                            if (isset($orderdetails['PO_Details']['approval_status_val']) && $orderdetails['PO_Details']['approval_status_val'] != 0 && $orderdetails['PO_Details']['approval_status_val'] != 1) {
                                $status = $orderdetails['PO_Details']['approval_status_val'];
                            } else if (isset($orderdetails['PO_Details']['approval_status_val']) && $orderdetails['PO_Details']['approval_status_val'] == 1) {
                                $status = 57108;
                            } else {
                                $status = 57106;
                            }
                            $payment_status = isset($orderdetails['PO_Details']['payment_status']) ? $orderdetails['PO_Details']['payment_status'] : 0;
                            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Order', $status, $user_id);
                            $current_status = (isset($res_approval_flow_func["currentStatusId"])) ? $res_approval_flow_func["currentStatusId"] : '';
                            $approvalOptions = array();
                            $approvalVal = array();
                            $isApprovalFinalStep = 0;
                            $financeStatuses = [57118, 57032];
                            $acceptStatuses = [57107, 57119, 57120];
                            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                                foreach ($res_approval_flow_func["data"] as $options) {
                                    if ($options['isFinalStep'] == 1) {
                                        $isApprovalFinalStep = $options['isFinalStep'];
                                    }
                                    if (in_array($options['nextStatusId'], $financeStatuses)) {
                                        if ($payment_status == 57118 && $options['nextStatusId'] != 57118) {
                                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                                        } else if ($payment_status == $options['nextStatusId'] || $payment_status == 57032) {
                                            
                                        } else {
                                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                                        }
                                    } else {
                                        if (in_array($current_status, $acceptStatuses) && $options['nextStatusId']==57035) {
                                            
                                        } else {
                                            $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep'] . ',' . $options['conditionId']] = $options['condition'];
                                        }
                                    }
                                }
                            }
                            $approvalButton = 'false';
                            if (is_array($approvalOptions) && count($approvalOptions) > 0) {
                                $approvalButton = 'true';
                            }
                            $orderdetails['PO_Details']['approval_button'] = $approvalButton;
                            $message = 'getPodetails';
                            $data = $orderdetails;
                        }

                        return json_encode(Array('status' => 'success', 'message' => $message, 'data' => $data));
                    } else {
                        return json_encode(Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'PO id not sent', 'data' => []));
                }
            } else {
                return json_encode(Array('status' => 'failed', 'message' => 'srm token is not sent', 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
        }
    }

 /*
* Function name: createPO
* Description: Function is used to create PO 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/

public function createPO() {

  try{

   $array = json_decode($_POST['data'],true);

   if(isset($array['srm_token']) && $array['srm_token']!='') {
    if(isset($array['po_date']) && $array['po_date']!='') {
      if(isset($array['validity']) && $array['validity']!='') {
        if(isset($array['delivery_before']) && $array['delivery_before']!='') {
          if(isset($array['indent_id']) && $array['indent_id']!='') {
            if(isset($array['supplier_id']) && $array['supplier_id']!='') {
              if(isset($array['warehouse_id']) && $array['warehouse_id']!='') {
                if(isset($array['po_type']) && $array['po_type']!='') {
                  if(isset($array['po_total']) && $array['po_total']!='') {
                    if(isset($array['remarks'])) {

                      $products_array = sizeof($array['products']);
            
                      for($i=0;$i<$products_array;$i++)
                      {

                        //$products_array = $array['products'][$i]; 

                        $product_id = $array['products'][$i]['product_id'];
                        $qty = $array['products'][$i]['qty'];
                        $pack_id = $array['products'][$i]['pack_id'];
                        $freeqty = $array['products'][$i]['freeqty'];
                        $freebie_pack_id = $array['products'][$i]['freebie_pack_id'];
                        $total = $array['products'][$i]['total'];
                        $unit_price = $array['products'][$i]['unit_price'];

                        if($product_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'ProductId not sent','data'=>"")));  
                          die;
                        }
                        else if($qty == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'quantity not sent','data'=>""))); 
                          die;
                        }
                        else if($pack_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'pack id not sent','data'=>""))); 
                          die;
                        }
                        else if($freeqty == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'free quantity not sent','data'=>""))); 
                          die;
                        }
                        else if($freebie_pack_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'freebiepack id not sent','data'=>""))); 
                          die;
                        }
                        else if($total == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'product total not sent','data'=>""))); 
                          die;
                        }
                        else if($unit_price == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'unit_price not sent','data'=>""))); 
                          die;
                        }                                    

         


           $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);
            if($valToken>0)
             {  

              
           /**
           * to get user_id based on srm token
           */
            $customer_id = $this->categoryModel->getUserId($array['srm_token']);
            $user_id = $customer_id[0]->user_id;       
         
           /**
           * setting array based on inputs
           */
           $payment_mode=(isset($array['payment_mode']) && $array['payment_mode']!='') ? $array['payment_mode'] : '';
           $logistics_cost=(isset($array['logistics_cost']) && $array['logistics_cost']!='') ? $array['logistics_cost'] : 0;
           $payment_due_date=(isset($array['payment_due_date']) && $array['payment_due_date']!='') ? $array['payment_due_date'] : date('Y-m-d').' 00:00:00';
           $paid_through=(isset($array['paid_through']) && $array['paid_through']!='') ? $array['paid_through'] : '';
           $payment_type=(isset($array['payment_type']) && $array['payment_type']!='') ? $array['payment_type'] : '';
           $payment_ref=(isset($array['payment_ref']) && $array['payment_mode']!='') ? $array['payment_ref'] : '';

            $data['platform_id']=$array['platform_id'];
            $data['po_date']=$array['po_date'];
            $data['indent_id']=$array['indent_id'];
            $data['validity']=$array['validity']; 
            $data['delivery_before']=$array['delivery_before'];
            $data['supplier_list']=$array['supplier_id'];           
            $data['warehouse_list']=$array['warehouse_id'];
            $data['po_type']=$array['po_type'];
            $data['created_by']=$user_id; 
            $data['payment_mode']=$payment_mode;   
            $data['logistics_cost']=$logistics_cost;   
            $data['payment_due_date']=$payment_due_date;
            $data['paid_through']=$paid_through;  
            $data['payment_type']=$payment_type;  
            $data['payment_ref']=$payment_ref;  


            # check freebie with product id
            $checkFreebie=$this->_srmOrder->FindfreebieId($array['products']);     

            $i=0;
            foreach($array['products'] as $products)
            {

            /**
            * po base price calculations
            */            
            $no_of_eaches=$this->_srmOrder->CalculateEaches($products['product_id'],$products['packsize'],$products['pack_id']);        
            $po_baseprice= $no_of_eaches*$products['unit_price'];
            /**
            * Calling tax deatils based on each product
            */

             $taxCalculation=$this->taxCalculation($products['product_id'],$array['srm_token'],$array['warehouse_id']);    

            if(isset($taxCalculation['Tax Percentage'])) {

               $Tax_Percentage=$taxCalculation['Tax Percentage'];
               $Tax_Type=$taxCalculation['Tax Type'];

             }else{

              $checkfreebie=$this->_srmOrder->checkfreebieproduct($taxCalculation['product_id']);

              if($checkfreebie['status']== false){

                return json_encode(array('status'=>"failed",'message'=> 'Selected product ('.$checkfreebie['product_name'].') is not taxable','data'=>[]));
              }else{ 
                $Tax_Percentage=0;
                $Tax_Type='';               
              }      


             }

            
             /**
             * if tax is included below calucations
             */
               
            $totalbaseprice=($products['total']/(100+$Tax_Percentage))*100;

             $po_taxvalue= $products['total']-$totalbaseprice;          

             $data['po_product_id'][$i] = $products['product_id'];

             # defining parent_id
             for ($z=0; $z <count($checkFreebie) ; $z++) {              
              
              if($products['product_id'] == $checkFreebie[$z]['free_prd_id']){        
                 $data['parent_id'][$i] =$checkFreebie[$z]['main_prd_id'];
                 break;
              }else{               
                $data['parent_id'][$i] =0;
              }

             }

             $data['qty'][$i] = $products['qty'];
             $data['packsize'][$i] = $products['pack_id'];            
             $data['freeqty'][$i] = $products['freeqty'];
             $data['freepacksize'][$i] = $products['freebie_pack_id'];
             $data['po_baseprice'][$i] = $po_baseprice;
             $data['pretax'][$products['product_id']] = '1';
             $data['po_taxname'][$products['product_id']] = $Tax_Type;
             $data['unit_price'][$products['product_id']] = $products['unit_price'];
             $data['po_taxper'][$products['product_id']] = $Tax_Percentage;
             $data['po_taxvalue'][$products['product_id']] = $po_taxvalue;
             $data['po_totprice'][$i] = $products['total'];
            
             
             $i++;
            }
             $data['po_grandtotal'] = $array['po_total'];
             $data['po_remarks'] = $array['remarks'];

             

           $PO_data= $this->PO->savePurchaseOrderAction($data);
        
           $result = json_decode($PO_data,true);

           if($result['status']== 200){

            $message="Your PO number ".$result['serialNumber']." has been created successfully";

            return json_encode(Array('status' => 'success', 'message' =>$message, 'data' =>[]));          
            }else{
            return json_encode(Array('status' => 'failed', 'message' =>$result['message'], 'data' => []));    
            }
 
      }else{
         return json_encode(Array('status' => 'session','message' =>'Your Session Has Expired. Please Login Again.','data' => []));  
      } }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'Remarks is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO total is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO type is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'warehouse id is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'supplier id is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'indent id is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'delivery before date is not sent', 'data' => []));    
      }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'validity is not sent', 'data' => []));    
      }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO date is not sent', 'data' => []));    
      }
      } else {
             return json_encode(Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []));
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
* Function name: taxCalculation
* Description: used to creae tax calculation based product
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/

  public function taxCalculation($product_id,$srm_token,$le_wh_id)
  {
                               
      //$url = Config::get('dmapi.TAX_Node_URL');

       $url = env('APP_TAXAPI');
       $statedata=$this->_srmOrder->getStateId($srm_token,$le_wh_id);
   
        $postData = array(
                    'product_id' => $product_id, 
                    'seller_state_id' => $statedata['seller_state_id'],
                    'buyer_state_id' => $statedata['buyer_state_id']
                );

        $postData = json_encode($postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'api_key: testkey',
            'api_secret: testsecret',
            'Content-Type: application/json'
        ));
        
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
        $output = curl_exec($ch);
        curl_close($ch);

        $outputs = json_decode($output, true);

        if($outputs['Status']==200){

          $data=$outputs['ResponseBody'][0];
        }
        else{
          $data['message']=$outputs['ResponseBody'];
          $data['product_id']=$product_id;
          //return $data;
          //return json_encode(array('status'=>"failed",'message'=> $outputs['ResponseBody'],'data'=>[]));
        }

        return $data;     



  }


  /*
* Function name: updatePO
* Description: Function is used to update PO 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/

public function updatePO() {

  try{

   $array = json_decode($_POST['data'],true);

   
  if(isset($array['srm_token']) && $array['srm_token']!='') {
    if(isset($array['po_date']) && $array['po_date']!='') {
      if(isset($array['validity']) && $array['validity']!='') {
        if(isset($array['delivery_before']) && $array['delivery_before']!='') {
          if(isset($array['indent_id']) && $array['indent_id']!='') {
            if(isset($array['supplier_id']) && $array['supplier_id']!='') {
              if(isset($array['warehouse_id']) && $array['warehouse_id']!='') {
                if(isset($array['po_type']) && $array['po_type']!='') {
                  if(isset($array['po_total']) && $array['po_total']!='') {
                    if(isset($array['remarks'])) {
                      if(isset($array['po_id']) && $array['po_id']!='') {

                      $products_array = sizeof($array['products']);
            
                      for($i=0;$i<$products_array;$i++)
                      {

                        //$products_array = $array['products'][$i]; 

                        $product_id = $array['products'][$i]['product_id'];
                        $qty = $array['products'][$i]['qty'];
                        $pack_id = $array['products'][$i]['pack_id'];
                        $freeqty = $array['products'][$i]['freeqty'];
                        $freebie_pack_id = $array['products'][$i]['freebie_pack_id'];
                        $total = $array['products'][$i]['total'];
                        $unit_price = $array['products'][$i]['unit_price'];

                        if($product_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'ProductId not sent','data'=>"")));  
                          die;
                        }
                        else if($qty == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'quantity not sent','data'=>""))); 
                          die;
                        }
                        else if($pack_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'pack id not sent','data'=>""))); 
                          die;
                        }
                        else if($freeqty == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'free quantity not sent','data'=>""))); 
                          die;
                        }
                        else if($freebie_pack_id == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'freebiepack id not sent','data'=>""))); 
                          die;
                        }
                        else if($total == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'product total not sent','data'=>""))); 
                          die;
                        }
                        else if($unit_price == "") {
                          print_r(json_encode(array('status'=>"failed",'message'=> 'unit_price not sent','data'=>""))); 
                          die;
                        }                        


     $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

      if($valToken>0)
       {   

           /**
           * to get user_id based on srm token
           */
            $customer_id = $this->categoryModel->getUserId($array['srm_token']);
            $user_id = $customer_id[0]->user_id;       
         
           /**
           * setting array based on inputs
           */

            $data['platform_id']=$array['platform_id'];
            $data['po_id']=$array['po_id'];
            $data['po_date']=$array['po_date'];
            $data['indent_id']=$array['indent_id'];
            $data['validity']=$array['validity']; 
            $data['delivery_before']=$array['delivery_before'];
            $data['supplier_id']=$array['supplier_id'];           
            $data['warehouse_id']=$array['warehouse_id'];
            $data['po_type']=$array['po_type'];
            $data['updated_by']=$user_id;       

            $i=0;
            foreach($array['products'] as $products)
            {

            /**
            * po base price calculations
            */            
            $no_of_eaches=$this->_srmOrder->CalculateEaches($products['product_id'],$products['packsize'],$products['pack_id']);         
            $po_baseprice= $no_of_eaches*$products['unit_price'];
            /**
            * Calling tax deatils based on each product
            */

             $taxCalculation=$this->taxCalculation($products['product_id'],$array['srm_token'],$array['warehouse_id']);    

            if(isset($taxCalculation['Tax Percentage'])) {

               $Tax_Percentage=$taxCalculation['Tax Percentage'];
               $Tax_Type=$taxCalculation['Tax Type'];

             }else{

              $checkfreebie=$this->_srmOrder->checkfreebieproduct($taxCalculation['product_id']);

              if($checkfreebie['status']== false){

                return json_encode(array('status'=>"failed",'message'=> 'Selected product ('.$checkfreebie['product_name'].') is not taxable','data'=>[]));
              }else{ 
                $Tax_Percentage=0;
                $Tax_Type='';               
              }      


             }

            
             /**
             * if tax is included below calucations
             */
               
            $totalbaseprice=($products['total']/(100+$Tax_Percentage))*100;

             $po_taxvalue= $products['total']-$totalbaseprice;

             $parent_id=(isset($products['parent_id']) && $products['parent_id']!='') ? $products['parent_id'] : 0;

             $data['po_product_id'][$i] = $products['product_id'];
             $data['parent_id'][$i] = $parent_id;
             $data['qty'][$i] = $products['qty'];
             $data['packsize'][$i] = $products['pack_id'];            
             $data['freeqty'][$i] = $products['freeqty'];
             $data['freepacksize'][$i] = $products['freebie_pack_id'];
             $data['po_baseprice'][$i] = $po_baseprice;
             $data['pretax'][$products['product_id']] = '1';
             $data['po_taxname'][$products['product_id']] = $Tax_Type;
             $data['unit_price'][$products['product_id']] = $products['unit_price'];
             $data['po_taxper'][$products['product_id']] = $Tax_Percentage;
             $data['po_taxvalue'][$products['product_id']] = $po_taxvalue;
             $data['po_totprice'][$i] = $products['total'];
            
             
             $i++;
            }

            if(isset($array['delete'])){
            $j=0;
            foreach($array['delete']['product_id'] as $deleteprods)
            {
             $data['delete_product'][$j] = $deleteprods;
             $j++;
            }
           }


             $data['po_grandtotal'] = $array['po_total'];
             $data['po_remarks'] = $array['remarks'];

           $PO_data= $this->PO->updatePOAction($data);
        

           $result = json_decode($PO_data,true);

          if($result['status']== 200){

            $serial_number=$this->purchaseorder->getPoCodeById($array['po_id']);

            $serialNumber=$serial_number->po_code;           

            $message="Your PO number ".$serialNumber." has been updated successfully";

            return json_encode(Array('status' => 'success', 'message' =>$message, 'data' =>[]));          
            }else{
            return json_encode(Array('status' => 'failed', 'message' =>$result['message'], 'data' => []));    
             }
           

       }else{
         return json_encode(Array('status' => 'session','message' =>'Your Session Has Expired. Please Login Again.','data' => []));  
      } }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'Remarks is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO total is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO type is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'warehouse id is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'supplier id is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'indent id is not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'delivery before date is not sent', 'data' => []));    
      }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'validity is not sent', 'data' => []));    
      }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO date is not sent', 'data' => []));    
      }
      } else {
             return json_encode(Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []));
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
* Function name: editPO
* Description: editPO function is used to fetch the slab rates of the product_id passed with respective poid & product id.
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 18th Oct 2016
* Modified Date & Reason:
   */        
public function editPO(){

  try{

    $array = json_decode($_POST['data'],true);

    if(isset($array['srm_token']) && $array['srm_token']!='') {
      if(isset($array['po_id']) && $array['po_id']!='') {
        if(isset($array['product_id']) && $array['product_id']!='') {
           if(isset($array['warehouse_id']) && $array['warehouse_id']!='') {
              if(isset($array['supplier_id']) && $array['supplier_id']!='') {


       $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);
   
        if($valToken>0)
         {  
           /*$userId = $this->categoryModel->getUserId($array['srm_token']);

           $user_id = $userId[0]->user_id;*/
          
           $data = $this->_srmOrder->editPO($array['po_id'],$array['product_id'],$array['warehouse_id'],$array['supplier_id']);
      
            
          return json_encode(Array('status' => 'success', 'message' => 'EditPO', 'data' => $data));


      }else{
             return json_encode(Array('status' => 'session','message' =>'Your Session Has Expired. Please Login Again.','data' => []));      
      }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'Supplier Id not sent', 'data' => []));    
      } 
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'warehouse Id not sent', 'data' => []));    
      }       
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'Product Id not sent', 'data' => []));    
      }
      }else{
             return json_encode(Array('status' => 'failed', 'message' =>'PO ID not sent', 'data' => []));    
      }
      } else {
             return json_encode(Array('status' => 'failed', 'message' =>'srm token not sent', 'data' => []));
      }
            
            
}catch (\Exception $e)
    {
        $status = "failed";
        $message = "Internal server error";
        $data = [];
    }

}



/*
* Function name: getPOMasterlookupdata
* Description: Function used to get PO masterlookup data
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 24th NOv 2016
* Modified Date & Reason:
   */        
    public function getPOMasterlookupdata(){

    try{

    if(isset($_POST['data'])){

    $array = json_decode($_POST['data'],true);

    if(isset($array['srm_token']) && $array['srm_token']!='') {

    $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

    if($valToken>0) { 


    $data=$this->_srmOrder->getPOMasterlookupdata();


    return Array('status'=>"success",'message'=> 'getPOMasterlookupdata','data'=>$data);   


    }else{
    return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => [])); 
    }
    }else {
    return json_encode(Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []));
    }
    }else{
    return json_encode(json_encode(Array('status' => 'failed', 'message' =>'Data not sent', 'data' => [])));        
    }
    }catch (Exception $e)
    {       
    return json_encode(Array('status' => "failed", 'message' => "Internal server error", 'data' => []));
    } 

    }



    public function getPoStatusList() {
        try {
            $array = json_decode($_POST['data'], true);
            //print_r($array);die;
            if (isset($array['user_token']) && $array['user_token'] != '') {
                    $valToken = $this->categoryModel->checkCustomerToken($array['user_token']);
                    if ($valToken > 0) {
                        $user_data= $this->categoryModel->getUserId($array['user_token']); 
                        $user_id=$user_data[0]->user_id;
                        $this->_roleRepo = new RoleRepo();
                        $userDetails = $this->_roleRepo->getUserDetailsByUserId($user_id);
                        $legalEntityId=isset($userDetails->legal_entity_id)?$userDetails->legal_entity_id:0;
                    
                        $allPOApprovalCountArr = $this->purchaseorder->getPoCountByStatus($legalEntityId,1,$user_id);
                        $initiated=isset($allPOApprovalCountArr[57106]) ? (int)$allPOApprovalCountArr[57106] : 0;
                        $created=isset($allPOApprovalCountArr[57029]) ? (int)$allPOApprovalCountArr[57029] : 0;
                        $verified=isset($allPOApprovalCountArr[57030]) ? (int)$allPOApprovalCountArr[57030] : 0;
                        $approved=isset($allPOApprovalCountArr[57031]) ? (int)$allPOApprovalCountArr[57031] : 0;
                        $posit=isset($allPOApprovalCountArr[57033]) ? (int)$allPOApprovalCountArr[57033] : 0;
                        
                        $inspected_full = isset($allPOApprovalCountArr[57034]) ? (int)$allPOApprovalCountArr[57034] : 0;
                        $inspected_part = isset($allPOApprovalCountArr[57122]) ? (int)$allPOApprovalCountArr[57122] : 0;
                        
                        $receivedatdc=($inspected_part+$inspected_full);
                        
                        $accept_full = isset($allPOApprovalCountArr[57107]) ? (int)$allPOApprovalCountArr[57107] : 0;
                        $accept_part = isset($allPOApprovalCountArr[57119]) ? (int)$allPOApprovalCountArr[57119] : 0;
                        $accept_part_closed = isset($allPOApprovalCountArr[57120]) ? (int)$allPOApprovalCountArr[57120] : 0;
                        
                        $checked = $accept_full+$accept_part+$accept_part_closed;
                        $grncreated=isset($allPOApprovalCountArr[57035]) ? (int)$allPOApprovalCountArr[57035] : 0;
                        
                        $allPOCountArr = $this->purchaseorder->getPoCountByStatus($legalEntityId,0,$user_id);
                        
                        $approval_cancel = (isset($allPOApprovalCountArr[57117]) ? (int)$allPOApprovalCountArr[57117] : 0);
			$opened = (isset($allPOCountArr[87001]) ? (int)$allPOCountArr[87001] : 0)-$approval_cancel;
                        $canceled = (isset($allPOCountArr[87004]) ? (int)$allPOCountArr[87004] : 0)+$approval_cancel;
                        
                        $finalApprovalCountArr = $this->purchaseorder->getPoCountByStatus($legalEntityId,2,$user_id);
                        $completed = isset($finalApprovalCountArr[1]) ? (int)$finalApprovalCountArr[1] : 0;
                        
                        $immediatePay = $this->purchaseorder->getPoCountByStatus($legalEntityId,5,$user_id);                        
                        $payments=  array_sum($immediatePay);
                        $partialCountArr = $this->purchaseorder->getPoCountByStatus($legalEntityId,3,$user_id);
                        $partial=isset($partialCountArr[87005]) ? (int)$partialCountArr[87005] : 0;
                        $paid=  $opened+$completed+$partial;
                        $total = array_sum($allPOCountArr);
                        $statusList= [
                            ['name'=>'Initiation','code'=>'57106','count'=>$initiated],
                            ['name'=>'Verification','code'=>'57029','count'=>$created],
                            ['name'=>'Approval','code'=>'57030','count'=>$verified],
                            ['name'=>'Fulfillment','code'=>'57031','count'=>$approved],
                            ['name'=>'Inspection','code'=>'57033','count'=>$posit],
                            ['name'=>'Acceptance','code'=>'57034','count'=>$receivedatdc],
                            ['name'=>'GRN','code'=>'57107','count'=>$checked],
                            ['name'=>'Putaway','code'=>'57035','count'=>$grncreated],
                            ['name'=>'Open','code'=>'87001','count'=>$opened],
                            ['name'=>'Cancelled','code'=>'87004','count'=>$canceled],
                            ['name'=>'Completed','code'=>'57108','count'=>$completed],
                            ['name'=>'Payments','code'=>'57032','count'=>$payments.'/'.$paid],
                            ['name'=>'Total','code'=>'0','count'=>$total],
                        ];
                        $message = 'Status List';
                        $data = $statusList;
                        return json_encode(Array('status' => 'success', 'message' => $message, 'data' => $data));
                    } else {
                        return json_encode(Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []));
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

}