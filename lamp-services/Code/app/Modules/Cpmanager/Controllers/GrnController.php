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
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\GrnModel;
use App\Modules\WarehouseConfig\Models\WarehouseConfigApi;
use App\Modules\Grn\Models\Inward;
use Utility;
use App\Modules\Roles\Models\Role;
use App\Http\Controllers\BaseController;
use App\Central\Repositories\ProductRepo;
use App\Modules\Grn\Models\Grn;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;

class GrnController extends BaseController {

    public function __construct() {

        $this->_category = new CategoryModel();
        $this->_grn = new GrnModel();
        $this->_grnModel = new Grn();
        $this->_cust = new CustomerRepo();
        $this->_whConfigObj = new WarehouseConfigApi();
    }

    /*
     * Function Name: getPoList()
     * Description: Used to get purchase orderlist which are open and partially recived
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 9 Dec 2016
     * Modified Date & Reason:
     */

    public function getPoList() {

        try {
            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $arr = json_decode($data);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) {
                        if (isset($arr->user_id) && !empty($arr->user_id)) {
                            $user_id = $arr->user_id;
                        } else {
                            print_r(json_encode(array('status' => "failed", 'message' => "Please send user_id", 'data' => [])));
                            die;
                        }
                        $data = $this->_grn->getPoList($user_id);
                        if (!empty($data)) {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "getPoList",
                                'data' => $data
                                    //  'count'=>$count
                            ));
                        } else {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "No data",
                                'data' => []
                                    // 'count' =>$count
                            ));
                        }
                        //   $team=$this->_role->getTeamByUser($user_data[0]->user_id);
                    } else {
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                    }
                } else {
                    print_r(json_encode(array('status' => "failed", 'message' => "Pass grn token", 'data' => [])));
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
    public function getOpenPoList() {

        try {
            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $arr = json_decode($data);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) {                        
                        $data = $this->_grn->getOpenPoList();
                        if (!empty($data)) {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "getOpenPoList",
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
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }    
    public function getAssignedPoList() {

        try {
            if (isset($_POST['data'])) {
                $postData = $_POST['data'];
                $arr = json_decode($postData);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) { 
                        $type = (isset($arr->data_type) && $arr->data_type!='')?$arr->data_type:'assigned';
                        $data = $this->_grn->getAssignedPoList($type);
                        if (!empty($data)) {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "getAssignedOpenPoList",
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
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
    public function getPickerList() {
        try {
            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $arr = json_decode($data);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $roleName = isset($arr->role_name)?$arr->role_name:'Picker';
                    $module_name = isset($arr->module_name)?$arr->module_name:'';
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) {
                        $userData = $this->_category->getUserId($arr->grn_token);
                        $userId = 0;
                        if (is_array($userData) && isset($userData[0])) {
                            $userId = $userData[0]->user_id;
                        }
                        if ($userId > 0) {
                            if($roleName=='Picker'){
                                $roleRepo = new RoleRepo();
                                $users = $roleRepo->getUsersByFeatureCode('PICKR002',$userId);
                                $usersdata = json_decode(json_encode($users,1), 1);
                            }else{
                                $role = $this->_grn->getRoleIdByName($roleName);
                                if (isset($role->role_id) && $role->role_id != '') {
                                    $roleId = $role->role_id;
                                    $roleModel = new Role();
                                    $users = $roleModel->getUsersByLeId(['user_id' => $userId, 'role_id' => $roleId]);
                                    $usersdata = json_decode($users, 1);
                                } else {
                                    return Array('status' => 'session', 'message' => 'Role does not exist', 'data' => []);
                                }
                            }
                            $pickers = array();
                            //$notinclude = [1,3,24];
                            if (isset($usersdata) && is_array($usersdata) && !empty($usersdata)) {
                                foreach ($usersdata as $key => $picker) {
                                    $picker_id = $picker['user_id'];
                                    //if(!in_array($picker_id, $notinclude)){
                                        $userObj = new \App\Modules\Users\Models\Users();
                                        $userInfo = $userObj->getUsers($picker_id);
                                        if (isset($userInfo) && !empty($userInfo)) {
                                            if ($module_name != '' && $module_name == 'orders') {
                                                $assigned = $this->_grn->getAssignedOrdersByPicker($picker_id, 'assigned');
                                                $completed = $this->_grn->getAssignedOrdersByPicker($picker_id, 'completed');
                                            } else {
                                                $assigned = $this->_grn->getAssignedPosByPicker($picker_id, 'assigned');
                                                $completed = $this->_grn->getAssignedPosByPicker($picker_id, 'completed');
                                            }
                                            $userInfo->assigned = isset($assigned->count) ? $assigned->count : 0;
                                            $userInfo->completed = isset($completed->count) ? $completed->count : 0;
                                            $pickers[] = $userInfo;
                                        }
                                    //}
                                }
                            }
                            if (!empty($pickers)) {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "pickerList",
                                'data' => $pickers
                            ));
                        } else {
                            return json_encode(Array(
                                'status' => "success",
                                'message' => "No data",
                                'data' => []
                            ));
                        }
                    
                        } else {
                            return Array('status' => 'session', 'message' => 'User Id should not be empty', 'data' => []);
                        }
                    } else {
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
    public function assignPickertoPO() {

        try {
            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $arr = json_decode($data);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) {
                        if (isset($arr->po_id) && is_array($arr->po_id)  && count($arr->po_id) > 0) {
                            if (isset($arr->picker_id) && $arr->picker_id > 0) {
                                $poArr = ['logistic_associate_id'=>$arr->picker_id];
                                $this->_grn->updatePO($arr->po_id,$poArr);
                                $status = 'success';
                                $msg = 'PO assigned successfully';
                            } else {
                                $status = 'fail';
                                $msg = 'Picker Id should not be empty';
                            }
                        } else {
                            $status = 'fail';
                            $msg = 'PO Id should not be empty';
                        }
                        return json_encode(Array(
                            'status' => $status,
                            'message' => $msg,
                            'data' => []
                        ));
                    } else {
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
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

        try {

            $array = json_decode($_POST['data'], true);

            if (isset($array['picker_token']) && $array['picker_token'] != '') {
                if (isset($array['container_barcode']) && $array['container_barcode'] != '') {


                    $checkPickerToken = $this->categoryModel->checkCustomerToken($array['picker_token']);

                    if ($checkPickerToken > 0) {

                        $user_data = $this->categoryModel->getUserId($array['picker_token']);

                        $Product_Orderdetails = $this->_picker->getBagsbybarcode($array['container_barcode']);
                    }

                    if (empty($Product_Orderdetails)) {
                        $message = 'Prodcut is not found';
                        $data = [];
                    } else {
                        $message = 'Product Details';
                        $data = $Product_Orderdetails;
                    }

                    return Array('status' => 'success', 'message' => $message, 'data' => $data);
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'pack_sku_code is not sent', 'data' => []));
                }
            } else {
                return json_encode(Array('status' => 'failed', 'message' => 'Picker token is not sent', 'data' => []));
            }
        } catch (Exception $e) {
            $status = "failed";
            $message = "Internal server error";
            $data = [];
            return Array('status' => $status, 'message' => $message, 'data' => $data);
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

        try {

            if (isset($_POST['data'])) {

                $array = json_decode($_POST['data']);


                if (isset($array->picker_token) && $array->picker_token != '') {

                    $checkPickerToken = $this->categoryModel->checkCustomerToken($array->picker_token);

                    if ($checkPickerToken > 0) {

                        $user_data = $this->categoryModel->getUserId($array->picker_token);


                        $result = $this->_picker->saveContainerData($array, $user_data[0]->user_id);

                        if (!empty($result)) {

                            return json_encode(Array('status' => 'success', 'message' => "Inserted Succesfully", 'data' => []));
                        }
                    } else {
                        return json_encode(Array('status' => 'session', 'message' => 'You have logged into other Ebutor System', 'data' => []));
                    }
                } else {
                    return json_encode(Array('status' => 'failed', 'message' => 'Pass Orderdata ', 'data' => []));
                }
            } else {
                return json_encode(Array(
                    'status' => "failed",
                    'message' => "No data",
                    'data' => []
                ));
            }
        } catch (Exception $e) {

            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }

    /*
     * Function Name: createGrn()
     * Description: This function is used to submite the grn
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 05 Dec 2016
	 * Modified 19 jan 2017
     * Modified Date & Reason:
     */

    public function createGrn() {
        try {
            DB::beginTransaction();
            $status = 0;
            $message = '';
            $inwardId = 0;
            $requestData = Input::all();
            //Log::info($requestData);
            $data = isset($requestData['data']) ? json_decode($requestData['data'], true) : [];

            //Log::info('API call for GRN creation');
            //Log::info($data);
            if (!empty($data)) {
                $products = isset($data['products']) ? $data['products'] : [];
                $document = isset($data['document']) ? $data['document'] : [];
                $legalEntityId = isset($data['legal_entity_id']) ? $data['legal_entity_id'] : 0;
                $legalWarehouseId = isset($data['le_wh_id']) ? $data['le_wh_id'] : 0;
                $referenceId = isset($data['reference_id']) ? $data['reference_id'] : 0;
                $purchaseOrderNo = isset($data['po_no']) ? $data['po_no'] : 0;
                $createdBy = isset($data['created_by']) ? $data['created_by'] : 0;
                $checkGRN = $this->_grnModel->checkGRNCreated($purchaseOrderNo,$products);
                if($checkGRN){
                if ($legalEntityId > 0) {
                    if ($legalWarehouseId > 0) {
                        unset($data['products']);
                        unset($data['document']);
                        $inwardData = $data;
                        $roleRepo = new RoleRepo();
                        $whdata = $roleRepo->getLEWHDetailsById($legalWarehouseId);
                        $state_code = isset($whdata->state_code)?$whdata->state_code:"TS";
                        $inwardCode = Utility::getReferenceCode("GR",$state_code);

                        $inwardData['inward_code'] = $inwardCode;
                        $inwardData['inward_status'] = 76001;
                        $inwardData['approval_status'] = 57023;
                        if (!empty($products)) {
                            //Log::info($inwardData);
                            $inward_id = $this->_grnModel->grnSave($inwardData);
                            foreach ($products as $product) {
                                $taxType = isset($product['tax_type']) ? $product['tax_type'] : '';
                                unset($product['tax_type']);
                                $productPackDetails = isset($product['pack_details']) ? $product['pack_details'] : [];
                                unset($product['pack_details']);
                                $prodId = isset($product['product_id']) ? $product['product_id'] : 0;
                                $receivedQty = isset($product['received_qty']) ? $product['received_qty'] : 0;
                                $grn_damaged = (int)((isset($product['grn_damaged'])) ? $product['grn_damaged'] : 0);
                                $grn_missed=(int)(isset($product['grn_missed']) ? $product['grn_missed'] : 0);
                                $grn_quarantine = (int)(isset($product['grn_quarantine']) ? $product['grn_quarantine'] : 0);
                                $product['good_qty'] = ($receivedQty - ($grn_damaged + $grn_missed + $grn_quarantine));
                                if ($prodId > 0 && $receivedQty > 0) {
                                    if ($purchaseOrderNo > 0) {
                                        \DB::enableQueryLog();
                                        $remainingPoQty = $this->_grnModel->validateGRN($purchaseOrderNo, $prodId, $receivedQty);
                                        //\Log::info(\DB::getQueryLog());
                                        //Log::info('remainingPoQty');
                                        //Log::info($remainingPoQty);
                                        if (!$remainingPoQty) {
//                                            return json_encode(['status' => 0, 'message' => 'PO Closed unable to create GRN for this PO.', 'grn_id' => 0]);
                                        } else {
                                            $checkQty = ($remainingPoQty - $receivedQty);
                                            //Log::info('checkQty');
                                            //Log::info($checkQty);
                                            if ($checkQty < 0) {
                                                return json_encode(['status' => 0, 'message' => 'PO qty is less than GRN.', 'grn_id' => 0]);
                                            }
                                        }
                                    }
                                    $product['inward_id'] = $inward_id;
                                    
                                    $goodQty = $product['good_qty'];
                                    $rowTotal = $product['row_total'];
                                    $grandTotal = $inwardData['grand_total'];
                                    $discount_on_bill = $inwardData['discount_on_total'];                                    
                                    if($discount_on_bill > 0)
                                    {
                                        $contribution = ($rowTotal/$grandTotal);
                                        $finalRowDiscount = ($contribution * $discount_on_bill);
                                        $finalRowTotal = ($rowTotal - $finalRowDiscount);
                                    }else{
                                        $finalRowTotal = $rowTotal;
                                    }
                                    $elp = ($finalRowTotal/$goodQty);
                                    $product['cur_elp'] = $elp;
                                    //Log::info($product);
                                    $productId = $this->_grnModel->saveGrnProducts($product);
                                    $inputTax = array(
                                        'inward_id' => $inward_id,
                                        'product_id' => $productId,
                                        'transaction_no' => $referenceId,
                                        'transaction_type' => 101001,
                                        'tax_type' => $taxType,
                                        'tax_percent' => isset($product['tax_per']) ? $product['tax_per'] : 0,
                                        'tax_amount' => isset($product['tax_amount']) ? $product['tax_amount'] : 0,
                                        'le_wh_id' => $legalWarehouseId,
                                        'created_by' => $createdBy,
                                    );
                                    //Log::info($inputTax);
                                    $this->_grnModel->saveInputTax($inputTax);

                                    if (!empty($productPackDetails)) {
                                        $tempPackCount = 0;
                                        foreach ($productPackDetails as $productPack) {
                                            $productPackDetails[$tempPackCount]['inward_prd_id'] = $productId;
                                            $tempPackCount++;
                                        }
                                        //Log::info($productPackDetails);
                                        $this->_grnModel->saveGrnProductDetails($productPackDetails);
                                    }
                                } else {
                                    $message = 'Invalid product info.';
                                }                                
                            }
                            //status change
                            $grnController = new \App\Modules\Grn\Controllers\GrnController(1);
                            $checkOrderStatus = $this->_grnModel->checkPOType($purchaseOrderNo);
                            $gds_order_id = "";
                            if(!empty($checkOrderStatus) && count($checkOrderStatus)>0){
                                if($checkOrderStatus->po_so_order_code != '0' && $checkOrderStatus->po_so_order_code !="" ){
                                        $gds_order_id = $this->_grnModel->checkPOSOInvoiceStatus($checkOrderStatus->po_so_order_code);
                                        if($gds_order_id == "" ){
                                            $message = 'PO Order not Invoiced!';
                                        }
                                }else{
                                    $message = 'Order not placed for PO!';
                                }
                            }
                            $grnController->grnUpdateStatus($inward_id, $inwardCode, $purchaseOrderNo, $createdBy);
                                
                            // document submission.
                            if (!empty($document)) {
                                foreach ($document as $documents) {
                                    //Log::info($documents);
                                    $docsArr = array(
                                        'inward_id' => $inward_id,
                                        'doc_ref_no' => $documents['ref_no'],
                                        'doc_ref_type' => $documents['documentType'],
                                        'doc_url' => $documents['url'],
                                        'created_by' => $createdBy,
                                        'created_at' => date('Y-m-d H:i:s')
                                    );

                                    DB::table('inward_docs')->insert($docsArr);
                                }
                            }
                            $status = 1;
                            $message = 'Done';
                            $inwardId = $inwardCode;
                            DB::commit();
                        } else {
                            $message = 'No Products provided.';
                        }
                    } else {
                        $message = 'Please provide warehouse information.';
                    }
                } else {
                    $message = 'Please provide supplier information.';
                }
            } else {
                    $message = 'GRN already created';
            }
            }else{
                $message = 'No data provided.';
            }
            
        } catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            $message = $ex->getMessage();
        }
        return json_encode(['status' => $status, 'message' => $message, 'grn_id' => $inwardId]);
    }
    public function getGRNList() {
        try {
            if (isset($_POST['data'])) {
                $postData = $_POST['data'];
                $arr = json_decode($postData);
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) { 
                        $type = (isset($arr->data_type) && $arr->data_type!='')?$arr->data_type:'';
                        $picker_id = (isset($arr->picker_id) && $arr->picker_id>0)?$arr->picker_id:'';
                        $offset = (isset($arr->offset) && $arr->offset != '') ? $arr->offset : 0;
                        $perpage = (isset($arr->perpage) && $arr->perpage != '') ? $arr->perpage : 10;
                        $data = $this->_grn->getGRNList($type,$picker_id,$offset,$perpage);
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
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
    public function assignPickertoGRN() {
        try {
            if (isset($_POST['data'])) {
                $data = $_POST['data'];
                $arr = json_decode($data);
                $status='';
                $msg=[];
                if (isset($arr->grn_token) && !empty($arr->grn_token)) {
                    $checkGrnToken = $this->_category->checkCustomerToken($arr->grn_token);
                    if ($checkGrnToken > 0) {
                        if (isset($arr->inward_id) && is_array($arr->inward_id) && count($arr->inward_id) > 0) {
                            if (isset($arr->picker_id) && $arr->picker_id > 0) {
                                foreach($arr->inward_id as $inward_id){                                    
                                    if($inward_id!="" && $inward_id>0){
                                        $grnDetails = $this->_grn->getInwardDetailById($inward_id);                                        
                                        //echo '<pre/>';print_r($grnDetails);die;
                                        $inward_code = isset($grnDetails[0]->inward_code)?$grnDetails[0]->inward_code:'';
                                        $wh_id = isset($grnDetails[0]->le_wh_id)?$grnDetails[0]->le_wh_id:'';                                                       
                                        $poArr = ['picker_id'=>$arr->picker_id];
                                        $this->_grn->updateGRN($inward_id,$poArr);
                                        $status = 'success';
                                        $msg[] = $inward_code.' assigned successfully';                                         
                                    }else{
                                        $status = 'fail';
                                        $msg[] = 'Inward Id should not be empty';
                                    }
                                }                               
                            } else {
                                $status = 'fail';
                                $msg[] = 'Picker Id should not be empty';
                            }
                        } else {
                            $status = 'fail';
                            $msg[] = 'PO Id should not be empty';
                        }
                        return json_encode(Array(
                            'status' => $status,
                            'message' => $msg,
                            'data' => []
                        ));
                    } else {
                        return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
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
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
}
