<?php
    namespace App\Modules\Cpmanager\Controllers;
    use DB;
    use Session;
    use App\Http\Controllers\BaseController;
    use App\Modules\Cpmanager\Models\SrmModel;
    use App\Modules\Cpmanager\Models\CategoryModel;
    use App\Modules\Roles\Models\Role;
    use Illuminate\Support\Facades\Input;
    use App\Modules\Supplier\Models\SupplierModel;
    use App\Modules\Supplier\Controllers\SupplierController;  
    use Response;
    use Lang;
    use Illuminate\Http\Request;
    
    class SrmController extends BaseController {
        
        public function __construct() {
            $this->srmModel = new SrmModel(); 
            $this->categoryModel = new CategoryModel(); 
            $this->_role = new Role(); 
        }

        /*
        * Function name: getPurchaseSlabs
        * Description: getProductSlabs function is used to fetch the slab rates of the product_id passed.
        * Author: Ebutor <info@ebutor.com>
        * Copyright: ebutor 2016
        * Version: v1.0
        * Created Date: 29 August 2016
        * Modified Date & Reason:
           */        
    public function getPurchaseSlabs(){

        try{

            if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }
                
              
                 if(isset($params['product_id'])){
                    
                    $checkProductId = $this->categoryModel->checkProductId($params['product_id']);

                    if($checkProductId>0){
                        $product_id = $params['product_id'];
                        }else{
                    $message = Lang::get('cp_messages.InvalidProductId');        
                        return Array('status' => 'failed', 'message' => $message, 'data' => []);
                    }
                    }else{
                        $message = Lang::get('cp_messages.InvalidProductId');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);
                }

                if(isset($params['srm_token']) && !empty($params['srm_token'])){
                    
                    $checkCustomerToken = $this->categoryModel->checkCustomerToken($params['srm_token']);
          
                    if($checkCustomerToken>0){
                        $user_id = $this->categoryModel->getUserId($params['srm_token']);
                        $user_id = $user_id[0]->user_id;
      
                        }else{

                            $message = Lang::get('cp_messages.InvalidCustomerToken');
                        return Array('status' => 'session', 'message' => $message, 'data' => []);
                    }
                    }else{

                        $user_id = 0;
                        
                    return Array('status' => 'failed', 'message' => 'Invalid customer_token', 'data' => []);
                }

            if(isset($params['le_wh_id']) && !empty($params['le_wh_id'])){
                    $le_wh_id = $params['le_wh_id'];
               
                   $le_wh_id = "'".$le_wh_id."'";
                }else{
                    $message = Lang::get('cp_messages.le_wh_id');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);
                }

  

            $data = $this->srmModel->getPurchaseSlabs($product_id,$le_wh_id,$params['supplier_id']);

            if(!empty($data)){

              return Array('status' => 'success', 'message' => 'getPurchaseSlabs', 'data' => $data);
            }else{

              return Array('status' => 'success', 'message' => 'No data', 'data' => []); 
            }
            
            
        }catch (\Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
            }

       } 

          /*
        * Function name: getPurchasepricehistory
        * Description: getPurchasepricehistory function is used to fetch the purchase history againest the po.
        * Author: Ebutor <info@ebutor.com>
        * Copyright: ebutor 2016
        * Version: v1.0
        * Created Date: 29 sep 2016
        * Modified Date & Reason:
           */        
    public function getPurchasepricehistory(){
     

        try{

            if(isset($_POST['data'])){
                    $params = $_POST['data'];
                    
                    $params= json_decode($params,true); 
                    }else{
                    $params ="";
                }

            if(isset($params['le_wh_id']) && !empty($params['le_wh_id'])){
                   $le_wh_id = $params['le_wh_id'];
               
                 
                }else{
                    $message = Lang::get('cp_messages.le_wh_id');
                    return Array('status' => 'failed', 'message' => $message, 'data' => []);
                }

            //$data = DB::select("CALL getProductSlabs($product_id,$le_wh_id,$user_id)");
            $supplier_id = $params['supplier_id'];
             $product_id = $params['product_id'];

             $data = $this->srmModel->getPurchasepricehistory($le_wh_id,$product_id,$supplier_id);
     
         $res=json_decode(json_encode($data),true);
        //$minmax=$res['result_val'][0];
        //$data1=$minmax[0];
         $data1= array();  
         $minmax= json_decode(json_encode($data['history2']),true);
          foreach($minmax[0] as $key => $value){
             
              $data1[$key] = $value;
          }
          foreach ($data['history'] as $history){
              
              $data1['data'][] = $history;
          }

            
            return Array('status' => 'success', 'message' => 'getPurchaseSlabs', 'data' =>$data1 );
            
            
        }catch (\Exception $e)
            {
                $status = "failed";
                $message = "Internal server error";
                $data = [];
            }

       } 
       
       
 /*End the closing bracket */    

 /*
  * Function Name: getWarehouseList()
  * Description: Used to get complete warehouse
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 28 Sep 2016
  * Modified Date & Reason:
  */
  public function getWarehouseList()
    {

     try
      {  

    if(isset($_POST['data'])){

         $json =$_POST['data'];

         $array = json_decode($json);

          if(isset($array->srm_token) && !empty($array->srm_token))
          {
         
         $checkSrmToken = $this->categoryModel->checkCustomerToken($array->srm_token);
                    if($checkSrmToken>0){
                         
                  $srm_token=$array->srm_token;
                            }else{
                   $srm_token='';
                     //   return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }
        
          }/*else{
       
            // return Array('status' => 'session', 'message' =>'Please send Srm Token', 'data' => []);
             
          }*/

          if(isset($array->supplier_id) && !empty($array->supplier_id))
          {
              
                   $supplier_id=$array->supplier_id;        


              }else{
                  
             $supplier_id='';
                       // return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
          }

          if(isset($array->flag) && !empty($array->flag))
          {
              
             $flag=$array->flag;        

          }else{
                  
             $flag='';
                       // return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
          }
         
         
                  
        if($flag==1)
        {
             
           $data = $this->srmModel->getWarehouseBySupplierId($supplier_id);

        } else{
            

          $data = $this->srmModel->getWarehouseList();

        
        }       
         
 
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getWarehouseList",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => []
      ));
      }
       }else{

            print_r(json_encode(array('status'=>"failed",'message'=>"Please pass required parameters",'data'=>"")));die;

        }
        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  

    }

   /*
  * Function Name: getManufacturerList()
  * Description: Used to get all manufacturer based on legal_entity_id
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 22 Nov 2016
  * Modified Date & Reason:
  */
  public function getManufacturerList()
    {

     try
      {  


    if(isset($_POST['data'])){

         $json =$_POST['data'];

         $array = json_decode($json);


          if(isset($array->srm_token) && !empty($array->srm_token))
          {
         
         $checkSrmToken = $this->categoryModel->checkCustomerToken($array->srm_token);
                    if($checkSrmToken>0){
                         
                  $srm_token=$array->srm_token;
                            }else{
                   //$sales_token='';
                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }
        
          }else{

             return Array('status' => 'session', 'message' =>'Please send Srm Token', 'data' => []);
             
          }

    $data=$this->srmModel->getManufacturerList();
 
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getManufacturerList",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => []
      ));
      }

         }else{

            print_r(json_encode(array('status'=>"failed",'message'=>"Please pass required parameters",'data'=>"")));die;

        }

        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  

    }


      
 /*End the closing bracket */    

 /*
  * Function Name: getManufacturerProducts()
  * Description: Used to get products based on manufacturer_id
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 28 Sep 2016
  * Modified Date & Reason:
  */
  public function getManufacturerProducts()
    {

     try
      {  

    if(isset($_POST['data'])){

         $json =$_POST['data'];

         $array = json_decode($json);

          if(isset($array->srm_token) && !empty($array->srm_token))
          {
         
         $checkSrmToken = $this->categoryModel->checkCustomerToken($array->srm_token);
                    if($checkSrmToken>0){
                         
                  $srm_token=$array->srm_token;
                            }else{
                  // $srm_token='';
                      return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }
        
          }else{
       
            return Array('status' => 'session', 'message' =>'Please send Srm Token', 'data' => []);
             
          }
  $manufacturer_id=(isset($array->manufacturer_id) && $array->manufacturer_id!='')?$array->manufacturer_id:'';
  $data = $this->srmModel->getManufacturerProducts($manufacturer_id);
  
    if (!empty($data->product_id))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getManufacturerProducts",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "No data",
        'data' => ((object) null)
      ));
      }
       }else{

            print_r(json_encode(array('status'=>"failed",'message'=>"Please pass required parameters",'data'=>"")));die;

        }
        }catch (Exception $e)
      {
          
          return Array('status' => "failed", 'message' => "Internal server error", 'data' =>"");
      }  

    }
 /*
  * Function Name: getSupplierList()
  * Description: Used to get complete suppllierlist based on rel_mgnr
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 28 Sep 2016
  * Modified Date & Reason:
  */
  public function getSupplierList()
    {

   if (isset($_POST['data'])) 
   { 
        $data = $_POST['data'];                   
        $arr = json_decode($data); 

        if (isset($arr->srm_token)) 
         {

             if(!empty($arr->srm_token))
             {

                 $checkSrmToken = $this->categoryModel->checkCustomerToken($arr->srm_token);

                  if($checkSrmToken>0)
                  {
                    //  $srm_token= $arr->srm_token;

                   //    $user_data= $this->categoryModel->getUserId($arr->srm_token); 
  
                // $team=$this->_role->getTeamByUser($user_data[0]->user_id);

       if (isset($arr->offset) && $arr->offset != null && $arr->offset >= 0)
      {
      $offset = $arr->offset;
      }
      else
      {
    
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "Offset Not Valid",
        'data' => []
      )));
      die;
      }

    if (isset($arr->offset_limit) && $arr->offset_limit != null 
      && $arr->offset_limit >= 0)
      {
      $offset_limit = $arr->offset_limit;
      }
      else
      {
    
      print_r(json_encode(array(
        'status' => "failed",
        'message' => "offset_limit not valid",
        'data' => []
      )));
      die;
      }    

   
       $data = $this->srmModel->getSupplierList($offset,$offset_limit);

 
        if (!empty($data))
         {
          return json_encode(Array(
            'status' => "success",
            'message' => "getSupplierList",
            'data' => $data
          ));
          }
           else
          {
          return json_encode(Array(
            'status' => "failed",
            'message' => "No data",
            'data' => []
          ));
       }
                       
                    }
                    else
                    {

                        return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);
             
                    }

               }
               else
               {
      
                   print_r(json_encode(array('status'=>"failed",'message'=> "Pass srm token",'data'=> [])));die;

               }
                        
            } 
            else 
            {

        
              print_r(json_encode(array('status'=>"failed",'message'=> "Pass srm token",'data'=> [])));die;

        
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
    

    } 



/*
* Class Name: getInventory
* Description: used to get inventory of product
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28th sept 2016
* Modified Date & Reason: 

*/


 public function getInventory() {

  try{

    $array = json_decode($_POST['data'],true);
        
        if(isset($array['srm_token']) && $array['srm_token']!='') {     
        if(isset($array['product_id']) && $array['product_id']!='') {  
        if(isset($array['le_wh_id']) && $array['le_wh_id']!='') {       

          $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

          //print_r($valToken);exit;
          if($valToken>0)
          {         
            

            $data= $this->srmModel->getInventory($array['le_wh_id'],$array['product_id']);

            return Array('status'=>"success",'message'=> 'getInventory','data'=>$data);              
                           
         
          }else{
             return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);    
          }
         }else{
             return Array('status' => 'failed', 'message' =>'le_wh_id is not sent', 'data' => []);    
        }
       }else{
             return Array('status' => 'failed', 'message' =>'product id is not sent', 'data' => []);    
      }
     } else {
             return Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []);
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
  * Function Name: getSupplierProductLists()
  * Description: Used to get product list based on warehouse and supplier
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 30 Sep 2016
  * Modified Date & Reason:
  */
  public function getSupplierProductLists() {


  try{

 if(isset($_POST['data'])){
    $array = json_decode($_POST['data'],true);
        
        if(isset($array['srm_token']) && $array['srm_token']!='') {     
        if(isset($array['supplier_id']) && $array['supplier_id']!='') {  
        if(isset($array['le_wh_id']) && $array['le_wh_id']!='') {       
      
          $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

          if($valToken>0)
          {         
           

            $data= $this->srmModel->getSupplierProductLists($array['le_wh_id'],$array['supplier_id']);
          
            if(!empty($data->product_id))
             { 
            return Array('status'=>"success",'message'=> 'getSupplierProductLists','data'=>$data);              
              }
              else{

               return Array('status'=>"success",'message'=> 'getSupplierProductLists','data'=>((object) null));              
            
              }             
         
          }else{
             return Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []);    
          }
         }else{
             return Array('status' => 'failed', 'message' =>'le_wh_id is not sent', 'data' => []);    
        }
       }else{
             return Array('status' => 'failed', 'message' =>'supplier_id is not sent', 'data' => []);    
      }
     } else {
             return Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []);
       }

     } else {
             return Array('status' => 'failed', 'message' =>'Data not sent', 'data' => []);
       }

     }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
      }              
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
 public function getSrmProducts()
{

if(isset($_POST['data'])){
                    $params = $_POST['data'];

                    $params= json_decode($params,true);     
               

   if(isset($params['product_ids'])){
                            $product_ids= $params['product_ids'];
                             $product_ids = "'".$product_ids."'";
                            }else{
                           
                            return Array('status' => "failed", 'message' => "Please pass productids", 'data' => []);
                        }


    $data = $this->srmModel->getSrmProducts($product_ids);

   
    if (!empty($data))
      {
      return json_encode(Array(
        'status' => "success",
        'message' => "getSrmProducts",
        'data' => $data
      ));
      }
      else
      {
      return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => $data
      ));
      }

        }else{
            $error = "Please pass required parameters";
            print_r(json_encode(array('status'=>"failed",'message'=>$error,'data'=>"")));die;

                       }
    }  
       

/*
  * Function Name: createSupplier()
  * Description: Function used to new supllier
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 22nd Nov 2016
  * Modified Date & Reason:
  */


public function createSupplier(){

try{

      if(isset($_POST['data'])){

      $array = json_decode($_POST['data'],true);

      if(isset($array['srm_token']) && $array['srm_token']!='') {
      if(isset($array['supplier_info']['organization_name']) && $array['supplier_info']['organization_name']!=''){
      if(isset($array['supplier_info']['organization_type']) && $array['supplier_info']['organization_type']!=''){
      if(isset($array['supplier_info']['org_address1']) && $array['supplier_info']['org_address1']!=''){
      if(isset($array['supplier_info']['org_address2']) && $array['supplier_info']['org_address2']!=''){
      if(isset($array['supplier_info']['org_country']) && $array['supplier_info']['org_country']!=''){
      if(isset($array['supplier_info']['org_state']) && $array['supplier_info']['org_state']!=''){
      if(isset($array['supplier_info']['org_city']) && $array['supplier_info']['org_city']!=''){
      if(isset($array['supplier_info']['org_pincode']) && $array['supplier_info']['org_pincode']!=''){
      if(isset($array['supplier_info']['supplier_rank']) && $array['supplier_info']['supplier_rank']!=''){
      if(isset($array['supplier_info']['supplier_type']) && $array['supplier_info']['supplier_type']!=''){
        if(isset($array['supplier_info']['rm_id']) && $array['supplier_info']['rm_id']!=''){

       if(isset($array['contact_info']['org_firstname']) && $array['contact_info']['org_firstname']!=''){
       if(isset($array['contact_info']['org_lastname']) && $array['contact_info']['org_lastname']!=''){
       if(isset($array['contact_info']['org_email']) && $array['contact_info']['org_email']!=''){
       if(isset($array['contact_info']['org_mobile']) && $array['contact_info']['org_mobile']!=''){ 


      $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);



      if($valToken>0)
      { 

      $userId = $this->srmModel->getUserIdLegalentityID($array['srm_token']);     
      $user_id = $userId[0]->user_id;  
      $legalentityId = $userId[0]->legal_entity_id;


      $reference_erp_code=(isset($array['supplier_info']['reference_erp_code']) && $array['supplier_info']['reference_erp_code']!='')? $array['supplier_info']['reference_erp_code']:'';
      $editSupplierId=(isset($array['supplier_id']) && $array['supplier_id']!='')? $array['supplier_id']:'';

      $data=array(
            
      'organization_name' =>$array['supplier_info']['organization_name'],
      'organization_type'=>$array['supplier_info']['organization_type'],
      'org_site'=>'',
      'org_address1'=>$array['supplier_info']['org_address1'],
      'org_address2'=>$array['supplier_info']['org_address2'],
      'org_country'=>$array['supplier_info']['org_country'],
      'org_state'=>$array['supplier_info']['org_state'],
      'org_city'=>$array['supplier_info']['org_city'],
      'org_pincode'=>$array['supplier_info']['org_pincode'],
      'org_rm'=>$array['supplier_info']['rm_id'],
      'supplier_rank'=>$array['supplier_info']['supplier_rank'],
      'supplier_type'=>$array['supplier_info']['supplier_type'],
      'reference_erp_code'=>$reference_erp_code,
      'date_estb'=>'',

      'org_firstname'=>$array['contact_info']['org_firstname'],
      'org_lastname'=>$array['contact_info']['org_lastname'],
      'org_email'=>$array['contact_info']['org_email'],
      'org_mobile'=>$array['contact_info']['org_mobile'],
      'org_landline'=>'',
      'org_extnumber'=>'',  

      'org_billingaddress_address1'=>$array['supplier_info']['org_address1'],
      'org_billingaddress_address2'=>$array['supplier_info']['org_address2'],
      'org_billingaddress_country'=>$array['supplier_info']['org_country'],
      'org_billingaddress_state'=>$array['supplier_info']['org_state'],
      'org_billingaddress_city'=>$array['supplier_info']['org_city'],
      'org_billingaddress_pincode'=>$array['supplier_info']['org_pincode'],

      'org_bank_acname'=>'',
      'org_bank_name'=>'',
      'org_bank_acno'=>'',
      'org_bank_actype'=>'',
      'org_bank_ifsc'=>'',
      'org_bank_branch'=>'',
      'org_micr_code'=>'',
      'org_curr_code'=>'',

      );

      $jsonData=json_decode(json_encode($data));

      $SupplierModel = new SupplierModel();

      $supplier_details= $SupplierModel->saveSupplier($jsonData,$user_id,$legalentityId,$editSupplierId,1);

      $result=json_decode($supplier_details,true);

      if($result['status']=='true'){

      return json_encode(Array('status' => 'success', 'message' =>'Successfully supplier created', 'data' =>$result));

      }else{

      return json_encode(Array('failed' => 'success', 'message' =>'Failed to process the request', 'data' =>[]));
      }     

}else{
return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => [])); 
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'mobile is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'email is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'lastname is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'firstname is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'reporting manager is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'supplier type is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'supplier rank is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'pincode is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'City is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'state is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'country is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'address2 is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'address1  is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'organization type is not sent', 'data' => []));
}
}else {
return json_encode(Array('status' => 'failed', 'message' =>'organization name is not sent', 'data' => []));
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



/*
  * Function Name: getSupplierMasterlookupdata($user_id)
  * Description: Function used to get supplier masterlookup data
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 22nd Nov 2016
  * Modified Date & Reason:
  */

public function getSupplierMasterlookupdata(){

try{

      if(isset($_POST['data'])){

      $array = json_decode($_POST['data'],true);

      if(isset($array['srm_token']) && $array['srm_token']!='') {

      $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

      if($valToken>0)
      { 


        $userId = $this->srmModel->getUserIdLegalentityID($array['srm_token']);     
        $user_id = $userId[0]->user_id;


        $data=$this->srmModel->getSupplierMasterlookupdata($user_id);


        return Array('status'=>"success",'message'=> 'getSupplierMasterlookupdata','data'=>$data);   


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


 /*
  * Function Name: getSubscribeProducts()
  * Description: Function used to add Subscribe Products for particular supplier
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 25th Nov 2016
  * Modified Date & Reason:
  */


public function getSubscribeProducts(){

try{

      if(isset($_POST['data'])){

      $array = json_decode($_POST['data'],true);

      if(isset($array['srm_token']) && $array['srm_token']!='') {
        if(isset($array['srm_token']) && $array['product_id']!='') {
          if(isset($array['srm_token']) && $array['supplier_id']!='') {
            if(isset($array['srm_token']) && $array['warehouse_id']!='') {
              if(isset($array['flag']) && $array['flag']!='') {

      $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);

      if($valToken>0)
      { 

      $userId = $this->srmModel->getUserIdLegalentityID($array['srm_token']);     
      $created_by = $userId[0]->user_id;


      $Suppliercontroller = new SupplierController();
      $Subscribe_data= $Suppliercontroller->saveTotMapping($array['product_id'],$array['supplier_id'],$array['warehouse_id'],$array['flag'],$created_by);

      return json_encode(Array('status' => 'success', 'message' =>'Successfully '.$Subscribe_data, 'data' =>[]));   


      }else{
      return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => [])); 
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'flag is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'warehouseId is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'supplierId is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'productId is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []));
      }
      }else{
      return json_encode(Array('status' => 'failed', 'message' =>'Data not sent', 'data' => []));        
      }
      }catch (Exception $e)
      {       
      return json_encode(Array('status' => "failed", 'message' => "Internal server error", 'data' => []));
      }

}




 /*
  * Function Name: getManufacturerSubscribedProducts
  * Description: Function used to all products based on subscribe and unsubscribe products
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 2nd Feb 2017
  * Modified Date & Reason:
  */


public function getManufacturerSubscribeProducts(){

try{

      if(isset($_POST['data'])){

      $array = json_decode($_POST['data'],true);

      if(isset($array['srm_token']) && $array['srm_token']!='') {
        if(isset($array['manufacturer_id']) && $array['manufacturer_id']!='') {
          if(isset($array['supplier_id']) && $array['supplier_id']!='') {
            if(isset($array['le_wh_id']) && $array['le_wh_id']!='') {
      $valToken = $this->categoryModel->checkCustomerToken($array['srm_token']);
      if($valToken>0)
      { 
      $data=$this->srmModel->getManufacturerSubscribeProducts($array);
      if(!empty($data))
      {  
      return json_encode(Array('status' => 'success', 'message' =>"getManufacturerSubscribeProducts", 'data' =>$data));
      }else{
       return json_encode(Array('status' => 'success', 'message' =>"No data", 'data' =>[]));
     
      }   
      }else{
      return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => [])); 
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'warehouseId is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'supplierId is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'manufacturerId is not sent', 'data' => []));
      }
      }else {
      return json_encode(Array('status' => 'failed', 'message' =>'srm token is not sent', 'data' => []));
      }
      }else{
      return json_encode(Array('status' => 'failed', 'message' =>'Data not sent', 'data' => []));        
      }
      }catch (Exception $e)
      {       
      return json_encode(Array('status' => "failed", 'message' => "Internal server error", 'data' => []));
      }

}
       
 }