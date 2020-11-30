<?php
  /*
    * Filename: OrderController.php
    * Description: This file is used for manage retailer & sales orders
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor@2016
    * Version: v1.0
    * Created date: 14th July 2016
    * Modified date: 
  */
  
  /*
    * OrderController is used to manage orders
    * @author    Ebutor <info@ebutor.com>
    * @copyright ebutor@2016
    * @package   Orders
    * @version:  v1.0
  */ 
  namespace App\Modules\Cpmanager\Controllers;
  use DB;
  use Log;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Input;
  use App\Http\Controllers\BaseController;
  use App\Modules\Cpmanager\Models\OrderModel;
  use App\Modules\Cpmanager\Models\ReOrderModel;
  
  class ReOrderController extends BaseController {  
    
    public function __construct() {
      
      $this->_order = new OrderModel(); 
      $this->_reorder = new ReOrderModel();
   
    }
    
  public function reOrdering() {

  try{
     if(isset($_POST['data'])) { 
      
      $array = json_decode($_POST['data'],true);
      if((isset($array['customer_token']) && $array['customer_token']!=''))
      {  
      
      $CustomerToken=$this->_order->validateToken($array['customer_token']);     
      if((isset($CustomerToken['token_status']) && $CustomerToken['token_status']==1)) {
        if((isset($array['legal_entity_id']) && $array['legal_entity_id']!='')) {
           if((isset($array['le_wh_id']) && $array['le_wh_id']!='')) {
            if((isset($array['offset']) && $array['offset']!='' && $array['offset'] >= 0)) {
              if((isset($array['offset_limit']) && $array['offset_limit']!='' && $array['offset_limit'] >= 0)) {
 
                  $result=$this->_reorder->reOrderings($array);
                 if(!empty($result))
                     return json_encode(array('status'=>"success",'message'=>"reOrdering",'data'=>$result));
                  else
                    return json_encode(array('status'=>"success",'message'=>"No Data",'data'=>[]));
              
          } else {             
              return json_encode(array('status'=>"failed",'message'=>"Please send offset_limit",'data'=>[]));
           }
          } else {             
              return json_encode(array('status'=>"failed",'message'=>"Please send offset",'data'=>[]));
           }
         } else {             
              return json_encode(array('status'=>"failed",'message'=>"Please send le_wh_id",'data'=>[]));
           }
           } else {             
              return json_encode(array('status'=>"failed",'message'=>"Please send legal_entity_id",'data'=>[]));
           }
           } else {             
              return json_encode(array('status'=>"session",'message'=>"Your Session Has Expired. Please Login Again.",'data'=>[]));
           }
          } else {
              return json_encode(array('status'=>"failed",'message'=> 'Please pass customer_token','data'=>[])); 
          }   

         } else {
              return json_encode(array('status'=>"failed",'message'=> 'Please pass required parameters','data'=>[])); 
      }   

      }catch (Exception $e)
            {
        Log::info($e->getMessage());
        Log::info($e->getTraceAsString());
            }              
}



}
    
       
    
    
    
    
  
  
  
