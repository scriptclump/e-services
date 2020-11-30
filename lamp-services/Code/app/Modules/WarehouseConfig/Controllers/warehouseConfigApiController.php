<?php

namespace App\Modules\WarehouseConfig\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Log;
use DB;
use Input;
use Redirect;
use App\Modules\WarehouseConfig\Models\WarehouseConfigApi;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\AdminOrderModel;
use Illuminate\Http\Request;
Class warehouseConfigApiController extends BaseController
{
    public function __construct() 
    {   
        $this->categoryModel = new CategoryModel(); 
         $this->_token = new AdminOrderModel();                
    }                     
    public function getBinLocationbyProId(Request $request)
    {
        $data=$request->all();
        $WarehouseConfig = new WarehouseConfigApi();
        $rs=$WarehouseConfig->getBinLocationbyProIdData($data);
       return $rs;
    }
    public function showInvByBinId(Request $request)
    {
        $data=$request->all();
        $WarehouseConfig = new WarehouseConfigApi();
        $rs=$WarehouseConfig->showInvByBinIdData($data);
        return $rs;
    }
    public function deriveBinCapacityByProductId(Request $request)
    {
        $data=$request->all();
        $WarehouseConfig = new WarehouseConfigApi();
        $rs=$WarehouseConfig->deriveBinCapacityByProductIdData($data);
        return $rs;
    }
    public function checkBinInventoryByBinCode(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $rs=$WarehouseConfig->checkBinInventoryByBinCodeData($data);
        return $rs;        
    }
    public function poGrnProductsWithBinLocation(request $request)
    {
        $postData = $request->all();
        $data=json_decode($postData['data'],true);
         $WarehouseConfig = new WarehouseConfigApi();
        $module_id=(isset($data['module_id']) && $data['module_id']!='')?$data['module_id']:0;
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        if($valToken>0)
        {            
            $rs=$WarehouseConfig->poGrnProductsWithBinLocationData($data);
            return $rs; 
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
       
    }
    public function getProductInfoByEan(Request $request)
    {
        $postData = $request->all();
        $data=json_decode($postData['data'],1);
        $ean_no =$data['ean_no']; 
        $valToken = $this->categoryModel->checkCustomerToken($data['bin_token']);
        $module_id=(isset($data['module_id']) && $data['module_id']!='')?$data['module_id']:0;
        if($module_id==1)
        {
            $valToken  = $this->_token->checkLpToken($data['bin_token']);
        }
        else
        {
           $valToken = $this->categoryModel->checkCustomerToken($data['bin_token']);

        }
        if($valToken>0)
        {
             $WarehouseConfig = new WarehouseConfigApi();
            $rs=$WarehouseConfig->getProductInfoByEanData($ean_no);
            if(!empty($rs))
            {
                return json_encode(array('status'=>"success",'message'=> 'Product information.','data'=>$rs)); 
            }else
            {
                return json_encode(array('status'=>"Failed",'message'=> 'Please configure product pack configuration.','data'=>$rs));
            }
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }
    public function putaway(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        $module_id=(isset($data['module_id']) && $data['module_id']!='')?$data['module_id']:0;
        if($module_id==1)
        {
            $valToken  = $this->_token->checkLpToken($data['token']);
        }
        else
        {
           $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        }
        if($valToken>0)
        {   
            $rs=$WarehouseConfig->putawayModel($data);
            return $rs;
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }
    public function cratesList(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        $module_id=(isset($data['module_id']) && $data['module_id']!='')?$data['module_id']:0;
        if($module_id==1)
        {
            $valToken  = $this->_token->checkLpToken($data['token']);
        }
        else
        {
           $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        }
        if($valToken>0)
        {   
            $rs=$WarehouseConfig->getCratesList($data);
            return $rs;
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }
    public function crateCodeInfo(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        $module_id=(isset($data['module_id']) && $data['module_id']!='')?$data['module_id']:0;
        if($module_id==1)
        {
            $valToken  = $this->_token->checkLpToken($data['token']);
        }
        else
        {
           $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        }
        if($valToken>0)
        {   
            $rs=$WarehouseConfig->getCrateByCrateCode($data);
            return $rs;
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }
    public function binCapacityByProdId(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        return $WarehouseConfig->binCapacityByProdId($data);
    }
    public function binAllocation($id)
    {
        $WarehouseConfig = new WarehouseConfigApi();
        return $WarehouseConfig->putawayBinAllocation($id);
    }    
    public function getProductBinLocation(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        return $WarehouseConfig->productBinLocation($data['ean_no'],$data['wh_id']);
    }
    
    public function salesreturns(Request $request)
    {
       $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        if($valToken>0)
        {   
            $rs=$WarehouseConfig->salesReturnGrid($data);
            return $rs;
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }
    public function assignReturns(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
//        Log::info("assign returns array---------------------");
  //      Log::info(print_r($data,true));
        if(isset($data['bin_type']) && $data['bin_type']!=0)
        {
            return $WarehouseConfig->assignReturns($data['return_ids'],$data['picker_id'],$data['status'],$data['bin_type']);
        }else
        {
            return json_encode(array('status'=>"failed",'message'=> "Invalid bin type.",'data'=>"")); 
        }
       
    }
    public function getAllPutawayLists(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        $module_id=(isset($data['module_id']) && $data['module_id']!='')?$data['module_id']:0;
        if($module_id==1)
        {
            $valToken  = $this->_token->checkLpToken($data['token']);
        }
        else
        {
           $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        }
        if($valToken>0)
        {   
            $rs=$WarehouseConfig->getAllPutawayLists($data);
            return $rs;
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }  
    public function bintobintransfer(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        if(isset($data['token']))
        {
            $valToken = $this->categoryModel->checkCustomerToken($data['token']);
            if($valToken>0)
            {   
               
               $rs=$WarehouseConfig->binToBinTransfer($data);
                return $rs;
            }else
            {
                return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
            } 
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'Pass token id', 'data' => []));  
        }
    }   
    public function showMinMaxQty(Request $request)
    {
        $postData=$request->all();
        $data=json_decode($postData['data'],1);
        $WarehouseConfig = new WarehouseConfigApi();
        $valToken = $this->categoryModel->checkCustomerToken($data['token']);
        if($valToken>0)
        {   
           
           $rs=$WarehouseConfig->showMinMaxQty($data);
            return $rs;
        }else
        {
            return json_encode(Array('status' => 'session', 'message' =>'You have already logged into the Ebutor System', 'data' => []));      
        } 
    }      
    public function putawayBinAllocation($id)
    {

        $WarehouseConfig = new WarehouseConfigApi(); 
        return $WarehouseConfig->putawayBinAllocation($id);
    } 
    public function getGRNList() 
    {
        try {
             $WarehouseConfig = new WarehouseConfigApi();
            if (isset($_POST['data'])) 
            {
                $postData = $_POST['data'];
                $arr = json_decode($postData);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->categoryModel->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) { 
                        $type = (isset($arr->data_type) && $arr->data_type!='')?$arr->data_type:'';
                        $picker_id = (isset($arr->picker_id) && $arr->picker_id>0)?$arr->picker_id:'';
                        $offset = (isset($arr->offset) && $arr->offset != '') ? $arr->offset : 0;
                        $perpage = (isset($arr->perpage) && $arr->perpage != '') ? $arr->perpage : 10;
                        $data = $WarehouseConfig->getGRNList($type,$picker_id,$offset,$perpage);
                        if (!empty($data)) {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "getGRNList",
                                'data' => $data
                            ));
                        } else {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "No data",
                                'data' => []
                            ));
                        }
                    } else {
                        return Array('status' => 'session', 'message' => 'You have already logged into the Ebutor System', 'data' => []);
                    }
                } else {
                    return json_encode(array('status' => "failed", 'message' => "Pass grn token", 'data' => []));
                    die;
                }
            } else {
                return json_encode(Array(
                    'status' => "failed",
                    'message' => "No data",
                    'data' => []
                ));
            }
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
        }
    }
}   