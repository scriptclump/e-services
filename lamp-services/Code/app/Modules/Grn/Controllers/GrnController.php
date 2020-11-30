<?php

/*
 * Filename: InventoryController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 4 July 2016
 * Modified date: 4 July 2016
 */

/*
 * InventoryController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Grn\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Response;
use Log;
use DB;
//use Auth;
use Input;
use Mail;
use Illuminate\Support\Facades\Redirect;
use App\Modules\Grn\Models\Grn;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Indent\Models\Products;
use App\Modules\Grn\Models\Tax;
use App\Modules\Grn\Models\Inward;
use App\Modules\Grn\Models\Dispute;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\ProductRepo;
use DateInterval;
use DateTime;
use Notifications;
use PDF;
use Utility;
use Lang;
use App\Lib\Queue;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Grn\Models\ReturnModel;
use App\Modules\Orders\Models\GdsBusinessUnit;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Cpmanager\Controllers\pickController;
use Cache;
use App\Modules\Cpmanager\Models\CartModel;
use App\Modules\PurchaseOrder\Controllers\PurchaseOrderController;
use App\Modules\Roles\Models\Role;
use App\Modules\Product\Controllers\ProductController;

use App\Modules\Supplier\Models\SuppliertermsModel;
use Carbon\Carbon;


class GrnController extends BaseController {
	
	protected $_grnModel;
	protected $_masterLookup;
	protected $_LegalEntity;
	protected $_productModel;
	protected $_taxModel;
    protected $_inwardModel;
    protected $_dispute;
    protected $_docTypes;
    protected $_roleRepo;
    protected $_productRepo;
    protected $_gdsBus;
    protected $_roleModel;

    public function __construct($forApi=0) {
        date_default_timezone_set('Asia/Kolkata');
        $this->middleware(function ($request, $next) use($forApi){
            if (!Session::has('userId') && $forApi==0) {
                    Redirect::to('/login')->send();
            }
            return $next($request);
        });
        $this->_grnModel = new Grn();
        $this->_masterLookup = new MasterLookup();
        $this->_LegalEntity = new LegalEntity();
        $this->_productModel = new Products();
        $this->_taxModel = new Tax();
        $this->_inwardModel = new Inward();
        $this->_dispute = new Dispute();
        $this->docTypes = array();
        $this->_roleRepo = new RoleRepo();
        $this->_productRepo = new ProductRepo();
        $this->_gdsBus =new GdsBusinessUnit();
        $this->_roleModel = new Role();

        $this->produc_grid_field_db_match = array(
            'grnId' => 'inward_code',
            'poId'   => 'po.po_code',
            'legalsuplier' => 'business_legal_name',
            'dcname' => 'dcname',
            'grnDate' => 'inward.created_at',
            'createdBy' => 'createdBy',
            'ref_no' => 'inward_ref_no',
            'invoice_no' => 'invoice_no',
            'grnvalue' => 'grnvalue',
            'povalue' => 'povalue',
            'item_discount_value' => 'item_discount_value',
        );
    }

    /*
     * indexAction() method is used to list of inventory
     * @param Null
     * @return String
     */

    public function indexAction($status='') {
        try {
            parent::Title('Manage GRN - '.Lang::get('headings.Company'));
            $legalentityId = Session::get('legal_entity_id');  
            $suppliers = $this->_LegalEntity->getLegalEntity($legalentityId);

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN001');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }
            $counts['allCount']  = $this->_inwardModel->getInwardCountByStatus(array(), array('status_id'=>'all'));
            $counts['approvedCount'] = $this->_inwardModel->getInwardCountByStatus(array(), array('status_id'=>'approved'));
            $counts['invoicedCount'] = $this->_inwardModel->getInwardCountByStatus(array(), array('status_id'=>'invoiced'));
            $counts['notapprovedCount'] = $this->_inwardModel->getInwardCountByStatus(array(), array('status_id'=>'notapproved'));
            $counts['notinvoicedCount'] = $this->_inwardModel->getInwardCountByStatus(array(), array('status_id'=>'notinvoiced'));
            $status = empty($status) ? 'all' : $status;
            $createFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN002');
            $featureAccess = array('createFeature'=>$createFeature);
            return view('Grn::index')->with('suppliers', $suppliers)
                                     ->with('status', $status)
                                    ->with('featureAccess', $featureAccess)
                                     ->with('counts', $counts);
        } catch (Exception $e) {
            
        }
    }

    /**
     * downloadAction() method is use for download file
     * @param  Null
     * @return File
     */
    
    public function downloadAction() {
        $fileName = public_path().'/'.Input::get('file');
        return Response::download($fileName);
    }

    /**
     * deleteAction() method is use for delete document of grn
     * @param  Null
     * @return JSON
     */
    
    public function deleteAction() {

        try {
            $id = Input::get('id');
            $docArr = $this->_dispute->getDocumentById($id);
            $inwardId = $docArr->inward_id;
            $poID = DB::selectFromWriteConnection(DB::raw("select po_no from inward where inward_id=$inwardId"));
            $poID = isset($poID[0]->po_no) ? $poID[0]->po_no : 0;
            $filename = isset($docArr->doc_url) ? $docArr->doc_url : '';
            if(!empty($filename) && file_exists(public_path().'/'.$filename)) {
                unlink(public_path().'/'.$filename);
            }

            $this->_dispute->deleteDocuments($id);
            $sessioncheck = Session::get('inwarddocs_'.$poID) ;
                if(is_array($sessioncheck)) {    
                    Session::put('inwarddocs_'.$poID, array_diff(Session::get('inwarddocs_'.$poID), [$id]));
            }
            return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successDelete'),'doc_ref_type' => $docArr->doc_ref_type));
        } catch (Exception $e) {
            return Response::json(array('status'=>200, 'message'=>'Failed'));
        }
    }

    /**
     * createAction() method is use for delete document of grn
     * @param  Null
     * @return View
     */
    
    public function createAction($po_selected = 0) {

        try {
            parent::Title('Create GRN - '.Lang::get('headings.Company'));
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN002');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }
            
            Session::put('inwarddocs', array());
            // Session::set("grn_doc_ref","");
            // Session::set("grn_doc_ref_type","");
            $suppliers = $this->_grnModel->getSuppliers();
            $poList = $this->_grnModel->poList();
            $docTypes = $this->_dispute->getDocumentTypes();
            if(is_array($poList) && count($poList) > 0) {
                foreach($poList as $key=>$po){
                    $poQty = $this->_grnModel->getPOQtyById($po->po_id);
                    $grnQty = $this->_grnModel->getGrnQtyByPOId($po->po_id);
                    if($grnQty->tot_received >= $poQty->totpo_qty){
                        unset($poList[$key]);
                    }
                }
            }
            
            $grnData['suppliers'] = $suppliers;
            $grnData['poList'] = $poList;
            return view('Grn::create')
                    ->with('docTypes', $docTypes)
                    ->with('po_selected', $po_selected)
                    ->with(array('grnData'=>$grnData));
        } 
        catch (Exception $e) {
            return Response::json(array('status'=>200, 'message'=>Lang::get('salesorders.errorInputData')));    
        }
    }

    /**
     * uploadDocumentAction() method is use upload document
     * @param  $request Object
     * @return JSON
     */
    
    public function uploadDocumentAction(Request $request) {

        try{
            $postData = Input::all();
            $inwardId = isset($postData['inward_id']) ? $postData['inward_id'] : 0;
            $poID = isset($postData['po_id']) ? $postData['po_id'] : 0;
            $allow_duplicate = isset($postData['allow_duplicate']) ? $postData['allow_duplicate'] : 0;
            $documentType = isset($postData['documentType']) ? $postData['documentType'] : '';
            $ref_no = isset($postData['ref_no']) ? $postData['ref_no'] : '';
            $ref_no = trim($ref_no);
            if($ref_no != ""  && $documentType != ""){
                $query=DB::select(DB::raw("SELECT * FROM inward_docs i JOIN inward id on i.inward_id=id.inward_id WHERE i.doc_ref_no= '".$ref_no."' AND i.doc_ref_type=".$documentType));
                    
                if(count($query)>0 && $allow_duplicate==0 ){
                    
                    $fail_message="";
                    $comment = '<br/>Please check below Inovice Number is used for other GRN<br/><br/>';
                    $fail_message = '<br/><strong>Inovice Number - '.$ref_no.'</strong><br/><br/>';
                    $comment = $comment . $fail_message;
                    $body = array('template' => 'emails.po', 'attachment' => '', 'name' => 'Hello All', 'comment' => $comment);
                    //$roleRepo = new RoleRepo();
                    //$userEmailArr = $roleRepo->getUsersByRole(['DC Manager']);
                    $notificationObj= new NotificationsModel();
                    $userIdData= $notificationObj->getUsersByCode('GRNINV001');
                    $userIdData=json_decode(json_encode($userIdData));
                    $purchaseOrder = new PurchaseOrder();
                    $userEmailArr = $purchaseOrder->getUserEmailByIds($userIdData);
                    $toEmails = array();
                    if (is_array($userEmailArr) && count($userEmailArr) > 0) {
                        foreach ($userEmailArr as $userData) {
                            
                            $toEmails[] = $userData['email_id'];
                        }
                    }
                    $instance = env('MAIL_ENV');
                    $subject = $instance . 'GRN Duplicate Reference Number Alert!';
                    \Utility::sendEmail($toEmails, $subject, $body);
                    return Response::json(array('status'=>400, 'message'=>Lang::get('inward.alreadyExist') ." for ".$query[0]->inward_code));
                }
            }
            if ($request->hasFile('upload_file')) {
                $extension = Input::file('upload_file')->getClientOriginalExtension();
                if(!in_array($extension, array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'))) {
                    return Response::json(array('status'=>400, 'message'=>Lang::get('inward.alertExtension')));
                }
                /*$destinationPath = public_path().'/uploads/grn_docs';
                $fileName = Input::file('upload_file')->getClientOriginalName();
                $fileName = date('YmdHis').$fileName;
                $success = Input::file('upload_file')->move($destinationPath, $fileName);
                */
                $imageObj = $request->file('upload_file');
                $url = $this->_productRepo->uploadToS3($imageObj,'grn',1);
                if($url!='') {
                    $docsArr = array(
                        'inward_id'=>$inwardId,
                        'doc_ref_no'=>$ref_no,
                        'po_id' =>$poID,
                        'doc_ref_type'=>$documentType,
                        'allow_duplicate'=>$allow_duplicate,
                        //'doc_url'=>'/uploads/grn_docs/'.$fileName, 
                        'doc_url'=>$url,
                        'created_by'=>Session('userId'), 
                        'created_at'=>date('Y-m-d H:i:s')
                    );
                    $inward_doc_id=$this->_dispute->saveDocument($docsArr);
                   // Session::push('inwarddocs_'.$poID, $inward_doc_id);
                    $docTypes = $this->_dispute->getDocumentTypes();
                    $userInfo = $this->_dispute->getLoginUserInfo();
                    $firstname = isset($userInfo->firstname)?$userInfo->firstname:'';
                    $lastname = isset($userInfo->lastname)?$userInfo->lastname:'';
                    $createdBy = $firstname.' '.$lastname;
                    
                    $docType = (isset($docTypes[$docsArr['doc_ref_type']]))?$docTypes[$docsArr['doc_ref_type']]:'';
                    $docText='<tr>
                            <td><input type="hidden" name="docs[]" value="'.$inward_doc_id.'">'.$docType.'</td>
                            <td>'.$docsArr['doc_ref_no'].'</td>
                            <td>'.$createdBy.'</td>
                            <td align="center"><a href="'.$url.'"><i class="fa fa-download"></i></a></td>
                            <td align="center">
                            <a class="delete grn-del-doc" id="'.$inward_doc_id.'" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
                            </td>
                        </tr>';
                    return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successUploaded'),'docText'=>$docText,'doc_ref_type' => $documentType));
                }
            }
            else {
                return Response::json(array('status'=>200, 'message'=>Lang::get('salesorders.errorInputData')));
            }
        }
        catch(Exception $e) {
            return Response::json(array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData')));
        }
    }

    /**
     * getDisputAction() method is use upload document
     * @param  $request Object
     * @param  $inwardId Number
     * @return JSON
     */

    public function getDisputAction($inwardId, Request $request) {
        try{
            $offset = (int)$request->input('$skip');
            $perpage = $request->input('$stop');
            $perpage = isset($perpage) ? $perpage : 10;

            $totalComment = $this->_dispute->getCommentsTransactionId($inwardId, 1);
            $disputesArr = $this->_dispute->getCommentsTransactionId($inwardId, 0, $offset, $perpage);

            $dataArr = array();
            $sno = ($offset+1);
            foreach ($disputesArr as $disput) {
                
                $profile_picture = public_path().$disput->profile_picture;

                $profilePic = '/uploads/LegalEntities/profile_pics/avatar5.png';
                
                if(!empty($disput->profile_picture) && file_exists($profile_picture)) {
                    $profilePic = $disput->profile_picture;
                }
                
                $commentBy = '<div style="row"><div class="col-md-2"><img src="'.$profilePic.'" class="img-circle" with="50" height="50"></div><div class="col-md-10" style="padding-top:10px;">'.$disput->firstname.' '.$disput->lastname.'<br/><span>'.$disput->roleName.'</span></div></div>';

                $dataArr[] = array(     'commentDate'=>$disput->created_at,
                                        'commentBy'=>$commentBy,
                                        'Comment'=>$disput->comments
                                        );
                $sno ++;
            }
            return Response::json(array('data'=>$dataArr, 'totalComment'=>$totalComment));
        }
        catch(Exception $e) {
            return Response::json(array('data'=>array(), 'totalComment'=>0));
        }
    }

    /**
     * createDisputAction() method is use add document and send email to logistic manager
     * @param  $request Object
     * @return JSON
     *
     * Type of transaction (Master Lookup)
     *
     * 101001 - GRN
     * 
     */
    
    public function createDisputAction(Request $request) {

        try{
            $postData = Input::all();
            //print_r($postData);die;                       

            $comment = isset($postData['comment']) ? $postData['comment'] : '';
            $inwardId = isset($postData['inwardId']) ? $postData['inwardId'] : 0;
            if(!$inwardId) {
                return Response::json(array('status'=>400, 'message'=>'Invalid inward id'));
            }

            if(empty($comment)) {
                return Response::json(array('status'=>400, 'message'=>'Please enter comment'));
            }
            
            $inwaredArr = $this->_inwardModel->getInwardCodeById($inwardId); 
            $inward_code = isset($inwaredArr->inward_code) ? $inwaredArr->inward_code : '';
            $legalentityId = Session::get('legal_entity_id');
            $userId = Session('userId');

            $disTransArr = $this->_dispute->getDisputIdByTransactionId($inwardId);
            $disputeId = isset($disTransArr->dispute_id) ? $disTransArr->dispute_id : 0;
            if(!$disputeId) {
                $disputeArr = array('legal_entity_id'=>$legalentityId, 
                            'dispute_type'=>'', 
                            'transaction_type'=>'101001', 
                            'transaction_id'=>$inwardId, 
                            'transaction_date'=>date('Y-m-d H:i:s'), 
                            'reported_by'=>$userId, 
                            'reported_at'=>date('Y-m-d H:i:s'));
                $disputeId = $this->_dispute->saveDispute($disputeArr);
            }
            
            if($disputeId) {
                $historyArr = array('dispute_id'=>$disputeId, 
                            'comments'=>$comment, 
                            'created_by'=>$userId, 
                            'created_at'=>date('Y-m-d H:i:s'));

                $this->_dispute->saveHistrory($historyArr);
            }

            if(isset($postData['notifyByEmail'])) {
                // send email
                $body = array('template'=>'emails.grncomment', 'attachment'=>'', 'name'=>'Hello All', 'comment'=>$comment);

                $userEmailArr = $this->_inwardModel->getUserEmailByRoleName(['Logistics Manager', 'Finance Manager']);
                $toEmails = array();
                
                if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                    foreach($userEmailArr as $userData){
                        $toEmails[] = $userData->email_id;
                    }
                }
                $subject = 'GRN - GRN#'.$inward_code.' '.date('d-m-Y');
                Utility::sendEmail($toEmails, $subject, $body);
            }            
            return Response::json(array('status'=>200, 'message'=>Lang::get('inward.successCommented')));
        }
        catch(Exception $e) {
            return Response::json(array('status'=>400, 'message'=>Lang::get('salesorders.errorInputData')));
        }
    }

    private function getInwardCode($state_code='TS') {
        $serialNumber = Utility::getReferenceCode("GR",$state_code);
        return $serialNumber;
    }

    /**
     * storeGrnData() method is use to save grn
     * @param  Null
     * @return JSON
     * 
     */
    
    public function storeGrnData(){
        try {
            $data = Input::all();
            // print_r($data);die;
            $reference_id = $data['reference_id'];
            $invoice_id = $data['invoice_id'];
            $invoice_date = date('Y-m-d',strtotime($data['invoice_date']));
            $grn_supplier = $data['grn_supplier'];
            $warehouse = $data['warehouse'];
            $discount_on_bill = $data['discount_on_bill'];
            $on_bill_discount_type = (isset($data['on_bill_discount_type']) && $data['on_bill_discount_type'] == 'on') ? 1 : 0;
            $on_bill_discount_value = $discount_on_bill;
            $discount_on_bill_options = (isset($data['discount_on_bill_options']) && $data['discount_on_bill_options'] == 'on') ? 1 : 0;
            if($discount_on_bill_options) {
                $discount_on_bill = 0;
            }elseif($discount_on_bill > 0 && $on_bill_discount_type == 1){
                $discount_on_bill = isset($data['discount_on_bill_value']) ? $data['discount_on_bill_value'] : 0;
            }
            $shippingcost = $data['shippingcost'];
            $po_id = $data['po_id'];
            $checkProducts = [];
            if (isset($data['grn_product_id']) && is_array($data['grn_product_id'])) {
                foreach ($data['grn_product_id'] as $key => $productId) {
                    $checkProducts[$key]['product_id'] = $productId;
                    $checkProducts[$key]['received_qty'] = $data['grn_received'][$key];
                }
            }
            if($po_id > 0) {
                $checkGRN = $this->_grnModel->checkGRNCreated($po_id,$checkProducts);
            }else{
                $checkGRN = 1;
            }
            $checkOrderStatus = $this->_grnModel->checkPOType($po_id);
            $gds_order_id = "";
            $poInfo = $this->_grnModel->getPOInfo($po_id);
            $supply_le_wh_id = isset($poInfo->supply_le_wh_id)?$poInfo->supply_le_wh_id:"";
            $disc_before_tax = isset($poInfo->discount_before_tax)?$poInfo->discount_before_tax:0;
            $objPO = new PurchaseOrder();
            $whdata = $objPO->getLEWHById($warehouse);
            if(!empty($checkOrderStatus) && count($checkOrderStatus)>0){
                if($checkOrderStatus->po_so_order_code != '0' && $checkOrderStatus->po_so_order_code !="" ){
                        $gds_order_id = $objPO->getOrderIdByCode($checkOrderStatus->po_so_order_code);
                        $checkIncvoice = $this->_grnModel->checkPOSOInvoiceStatus($gds_order_id);
                        if(count($checkIncvoice) == 0){
                            return Response::json(array('status' => 400, 'message' => 'PO Order not Invoiced!'));
                        }
                }else{
                    return Response::json(array('status' => 400, 'message' => 'Order not placed for PO!'));
                }
            }
            if($checkGRN){
            $grn_type = ($po_id>0) ? 'PO' : 'Manual';
                        
            $baseTotal = isset($data['total_grn_basetotal']) ? $data['total_grn_basetotal'] : 0.00;
            $grandTotal = isset($data['total_grn_grand_total']) ? $data['total_grn_grand_total'] : 0.00;
            $poApprovalStatus = $this->_grnModel->getPoApprovalStatusByPoId($po_id);
            $grn_products = isset($data['grn_product_id']) ? $data['grn_product_id'] : array();
            $objPO = new PurchaseOrder();
            $poDetailArr = $objPO->getPoDetailById($po_id);
            $poDetailArr1 = json_decode(json_encode($poDetailArr),true);
            $stock_transfer = isset($poDetailArr1[0]['stock_transfer']) ? $poDetailArr1[0]['stock_transfer'] : 0;
            $stock_transfer_dc = isset($poDetailArr1[0]['stock_transfer_dc']) ? $poDetailArr1[0]['stock_transfer_dc'] : 0;
            $all_product_ids = array_column($poDetailArr1, "product_id");
            $missed_prd_ids = array_diff($all_product_ids, $grn_products);
            if($poApprovalStatus == 57107 && (count($grn_products) != count($poDetailArr) )){
                return Response::json(array('status' => 400, 'message' => 'Items missing!, Please refresh and try again!'));
            }
            if($supply_le_wh_id != "" && $supply_le_wh_id != 0){
                $checkLOC = $objPO->checkLOCByLeWhid($supply_le_wh_id);
                $warehouse_data = $this->_LegalEntity->getWarehouseById($supply_le_wh_id);
                $warehouse_name = isset($warehouse_data->display_name)?$warehouse_data->display_name:"";
                $availablebalance  = $checkLOC - $grandTotal;
                if($availablebalance < 0){
                    return Response::json(array('status' => 400, 'message' => 'Insufficient balance for '.$warehouse_name.' to place the order!'));
                }
                $margin = isset($warehouse_data->margin)?$warehouse_data->margin:"";
                if($margin === "" ){
                    return Response::json(array('status' => 400, 'message' => 'Invalid Margin Defined  for '.$warehouse_name));
                }

                $priceNotFoundData = '<tr class="subhead">
                                <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                <th width="17%" align="left" valign="middle">APOB to DC PO Price</th>
                                <th width="17%" align="left" valign="middle">APOB Selling Price</th>
                                <th width="17%" align="left" valign="middle">CP Enable</th>
                                <th width="17%" align="left" valign="middle">Is Sellable</th>
                            </tr>';
                $elpvsespData = '<tr class="subhead">
                                <th width="66%" align="left" valign="middle">Product Name (SKU) </th>
                                <th width="17%" align="left" valign="middle">APOB ELP</th>
                                <th width="17%" align="left" valign="middle">APOB ESP</th>
                            </tr>';
                $customer_type_id = $objPO->getStockistPriceGroup($warehouse_data->legal_entity_id,$supply_le_wh_id);
                if($customer_type_id == 0){
                    return Response::json(array('status' => 400, 'message' => "Pricing not found for DC/FC!"));
                }
                // $customer_type_id = 1016;
                $product_ids = array();
                $elpproduct_ids = array();
                $cartObj = new CartModel();
                foreach ($data['grn_product_id'] as $key=>$productId) {
                    $grn_received = (int)(isset($data['grn_received'][$key]) ? $data['grn_received'][$key] : 0);
                    $grn_free = (int)((isset($data['grn_free'][$productId]) && is_array($data['grn_free'][$productId])) ? array_sum($data['grn_free'][$productId]) : 0);
                    $grn_damaged = (int)((isset($data['grn_damaged'][$productId]) && is_array($data['grn_damaged'][$productId])) ? array_sum($data['grn_damaged'][$productId]) : 0);
                    $grn_missed=(int)(isset($data['grn_missed'][$key]) ? $data['grn_missed'][$key] : 0);
                    $grn_excess=(int)(isset($data['grn_excess'][$key]) ? $data['grn_excess'][$key] : 0);
                    $grn_quarantine = (int)(isset($data['grn_quarantine'][$key]) ? $data['grn_quarantine'][$key] : 0);
                    $totreceived = $grn_received-($grn_free+$grn_damaged+$grn_missed+$grn_excess+$grn_quarantine);
                    if($grn_received > 0){                        
                        $subTotal = 0.00;
                        $rowTotal = 0.00;
                        $rowDetails = isset($data[$productId]) ? json_decode($data[$productId]) : [];
                        if(!empty($rowDetails)) {
                            $subTotal = property_exists($rowDetails, 'subTotal') ? str_replace(',', '', $rowDetails->subTotal) : 0.00;
                            $rowTotal = property_exists($rowDetails, 'totalval') ? str_replace(',', '', $rowDetails->totalval) : 0.00;
                        }
                        $discountType = 0;
                        $discountTypeArray = (isset($data['grn_discount_type']) ? $data['grn_discount_type'] : []);
                        if(!empty($discountTypeArray)) {
                            if(in_array($productId, $discountTypeArray)) {
                                $discountType = 1;
                            }
                        }
                        $discountIncTax = 0;
                        $discountIncTaxArray = (isset($data['grn_discount_inc_tax']) ? $data['grn_discount_inc_tax'] : []);
                        if(!empty($discountIncTaxArray)) {
                            if(in_array($productId, $discountIncTaxArray)) {
                                $discountIncTax = 1;
                            }
                        }
                        $goodQty = ($grn_received - ($grn_damaged + $grn_missed + $grn_quarantine+$grn_free));
                        if($discount_on_bill > 0 && $disc_before_tax==0) {
                            $contribution = ($rowTotal/$grandTotal);
                            $finalRowDiscount = ($contribution * $discount_on_bill);
                            $finalRowTotal = ($rowTotal - $finalRowDiscount);
                        }else{
                            $finalRowTotal = $rowTotal;
                        }
                        $elp = ($finalRowTotal/$goodQty);  


                        $product_id = $productId;
                        $qty = $totreceived;
                        $appKeyData = env('DB_DATABASE');
                        $productSlabs = DB::selectFromWriteConnection(DB::raw("CALL ProdSlabFlatRefreshByProductId($product_id,$warehouse)"));
                        $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type_id.'_le_wh_id_'.$warehouse;
                        $response = Cache::get($keyString);
                        $unitPriceData = ($response != '') ? (json_decode($response, true)) : [];
                        $temp = trim($warehouse, "'");
                        $temp = str_replace(',', '_', $temp);
                        $contact_data = $objPO->getLEWHById($supply_le_wh_id);
                        $mobile_number = $contact_data->phone_no;
                        $customer_data = $objPO->getCustomerDataByNo($mobile_number);
                        $user_id = $customer_data->user_id;
                        if ($user_id == 0) {
                            $temp = 0;
                        }

                        $availQty = $objPO->checkInventory($product_id,$warehouse);
                        if (isset($unitPriceData[$temp]) && count($unitPriceData[$temp])) {
                            $CheckUnitPrice = $unitPriceData[$temp];
                            $tempDetails = [];
                            if (isset($availQty)) {
                                foreach ($CheckUnitPrice as $slabData) {
                                    if (isset($slabData['stock'])) {
                                        $slabData['stock'] = $availQty;
                                    }
                                    $tempDetails[] = $slabData;
                                }
                            }
                            if (!empty($tempDetails)) {
                                $CheckUnitPrice = $tempDetails;
                            }
                            $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                            Cache::put($keyString, json_encode($unitPriceData), 60);
                        } else {
                            $CheckUnitPrice = DB::selectFromWriteConnection(DB::raw("CALL getProductSlabsByCust($product_id,'" . $warehouse . "',$user_id,$customer_type_id)"));
                            $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                            if(count($CheckUnitPrice))
                                Cache::put($keyString, json_encode($unitPriceData), 60);
                        }
                        $packSizeArr = array();
                        $isFreebie = 0;
                        $isFreebie = $cartObj->isFreebie($product_id);
                        $productData = $this->_grnModel->getProductInfo($product_id);
                        if(!count($CheckUnitPrice) && !$isFreebie ){
                            
                            array_push($product_ids, $product_id);
                            $packSizeArr = array();
                            foreach ($CheckUnitPrice as $price) {
                                if (is_array($price)) {
                                    //Log::info('This is array');
                                    $packSizeArr[$price['pack_size']] = $price['unit_price'];
                                } elseif (is_object($price)) {
                                    //Log::info('This is object');
                                    $packSizeArr[$price->pack_size] = $price->unit_price;
                                }
                            }
                            $poProductQty = $totreceived;
                            $packSizePrice = $cartObj->getPackPrice($poProductQty, $packSizeArr);
                            if($packSizePrice=="" || !count($packSizePrice))
                                $CheckUnitPrice = "";
                            else{
                                $CheckUnitPrice = $packSizePrice;
                            }
                            $cp_enable_data = $objPO->getCPEnableData($product_id,$warehouse);
                            $cp_enable = (isset($cp_enable_data->cp_enabled) && $cp_enable_data->cp_enabled == 1)?"<span style='color:green'>Yes</span>":"<span style='color:red'>No</span>";
                            $is_sellable = (isset($cp_enable_data->is_sellable) && $cp_enable_data->is_sellable == 1)?"<span style='color:green'>Yes</span>":"<span style='color:red'>No</span>";
                            $priceNotFoundData .= '<tr class="subhead priceerrorname">
                                                <td align="left" valign="middle"><b>'.$productData->product_title.' <span style="color:blue"><b>('.$productData->sku.')</b></span></b></td>
                                                <td style="color:red" align="left" valign="middle">0</td>
                                                <td align="left" valign="middle">'.$CheckUnitPrice.'</td>
                                                <td align="left" valign="middle">'.$cp_enable.'</td>
                                                <td align="left" valign="middle">'.$is_sellable.'</td>
                                                    </tr>';
                            
                        }else{

                            if(!$isFreebie){
                                $packSizeArr = array();
                                foreach ($CheckUnitPrice as $price) {
                                    if (is_array($price)) {
                                        //Log::info('This is array');
                                        $packSizeArr[$price['pack_size']] = $price['unit_price'];
                                    } elseif (is_object($price)) {
                                        //Log::info('This is object');
                                        $packSizeArr[$price->pack_size] = $price->unit_price;
                                    }
                                }
                                $poProductQty = $totreceived;
                                $packSizePrice = $cartObj->getPackPrice($poProductQty, $packSizeArr);
                                if($packSizePrice=="" || !count($packSizePrice))
                                    $CheckUnitPrice = "";
                                else{
                                    $CheckUnitPrice = $packSizePrice;
                                }
                                if($CheckUnitPrice < round($elp,5)){
                                    //Dont check ELP vs ESP for some manufacturers
                                    $mstdata = $this->_masterLookup->getMasterLokup(78024);
                                    $ignore_mnf = isset($mstdata->description)?$mstdata->description:"";
                                    $mnflist = explode(',', $ignore_mnf);
                                    $prd_manf = $productData->manufacturer_id;
                                    if(!in_array($prd_manf,$mnflist)){
                                        array_push($elpproduct_ids, $product_id);
                                        $elpvsespData .= '<tr class="subhead priceerrorname">
                                                    <td align="left" valign="middle"><b>'.$productData->product_title.' <span style="color:blue"><b>('.$productData->sku.')</b></span></b></td>
                                                    <td style="color:red" align="left" valign="middle">'.$elp.'</td>
                                                    <td align="left" valign="middle">'.$CheckUnitPrice.'</td>
                                                        </tr>';
                                    }
                                }
                            }

                        }
                        
                    }
                }
                if(count($product_ids)){
                    return Response::json(array("status" => 401, "reason" => "Pricing Not Found!","message" => "pricing_mismatch_found","adjust_message"=>"Please Clear Cache or Upload Prices",'data'=>$priceNotFoundData));
                }
                if(count($elpproduct_ids)){
                    return Response::json(array("status" => 401, "reason" => "ELP vs ESP!","message" => "pricing_mismatch_found","adjust_message"=>"Please Check ELP & ESP",'data'=>$elpvsespData));
                }
            }
            if(round($grandTotal,2) <= 0 ){
                return Response::json(array('status' => 400, 'message' => 'Grand Total Cannot Be Zero!'));
            }
            $state_code = isset($whdata->state_code)?$whdata->state_code:"TS";
            $inward_code =  $this->getInwardCode($state_code);
            if($inward_code == ""){
                return Response::json(array('status' => 400, 'message' => Lang::get('inward.serialnoerror')));
            }
            $grnArr = array(
                'inward_type' => $grn_type,
                'po_no' => $po_id,
                'inward_code' => $inward_code,
                'inward_ref_no'=>$reference_id,
                'invoice_no'=>$invoice_id,
                'invoice_date'=>$invoice_date,
                'legal_entity_id' => $grn_supplier,
                'currency_id' => 4,
                'discount_on_bill_options' => $discount_on_bill_options,
                'on_bill_discount_type' => $on_bill_discount_type,
                'on_bill_discount_value' => $on_bill_discount_value,
                'discount_on_total' => $discount_on_bill,
                    'discount_before_tax' => $disc_before_tax,
                'shipping_fee' => $shippingcost,
                'base_total' => $baseTotal,
                'grand_total' => $grandTotal,
                'le_wh_id' => $warehouse,
                'inward_status' => 76001,
                'approval_status' => 57023,
                'remarks' => $data['grn_remarks1'],
                'created_by' => Session::get('userId'),
            );
            //Log::info($grnArr);
			DB::beginTransaction();
            $inward_id = $this->_grnModel->grnSave($grnArr);
            if ($inward_id) {
                $this->inwardDocUpdate($inward_id);
                $cartObj = new CartModel();
                $OrderModel = new \App\Modules\Orders\Models\OrderModel;
                foreach ($data['grn_product_id'] as $key=>$productId) {
                    $grn_received = (int)(isset($data['grn_received'][$key]) ? $data['grn_received'][$key] : 0);
                    $grn_free = (int)((isset($data['grn_free'][$productId]) && is_array($data['grn_free'][$productId])) ? array_sum($data['grn_free'][$productId]) : 0);
                    $grn_damaged = (int)((isset($data['grn_damaged'][$productId]) && is_array($data['grn_damaged'][$productId])) ? array_sum($data['grn_damaged'][$productId]) : 0);
                    $grn_missed=(int)(isset($data['grn_missed'][$key]) ? $data['grn_missed'][$key] : 0);
                    $grn_excess=(int)(isset($data['grn_excess'][$key]) ? $data['grn_excess'][$key] : 0);
                    $grn_quarantine = (int)(isset($data['grn_quarantine'][$key]) ? $data['grn_quarantine'][$key] : 0);
                    $totreceived = $grn_received-($grn_free+$grn_damaged+$grn_missed+$grn_excess+$grn_quarantine);
                    if($grn_received > 0){                        
                        $subTotal = 0.00;
                        $rowTotal = 0.00;
                        $rowDetails = isset($data[$productId]) ? json_decode($data[$productId]) : [];
                        if(!empty($rowDetails)) {
                            $subTotal = property_exists($rowDetails, 'subTotal') ? str_replace(',', '', $rowDetails->subTotal) : 0.00;
                            $rowTotal = property_exists($rowDetails, 'totalval') ? str_replace(',', '', $rowDetails->totalval) : 0.00;
                        }
                        $discountType = 0;
                        $discountTypeArray = (isset($data['grn_discount_type']) ? $data['grn_discount_type'] : []);
                        if(!empty($discountTypeArray)) {
                            if(in_array($productId, $discountTypeArray)) {
                                $discountType = 1;
                            }
                        }
                        $discountIncTax = 0;
                        $discountIncTaxArray = (isset($data['grn_discount_inc_tax']) ? $data['grn_discount_inc_tax'] : []);
                        if(!empty($discountIncTaxArray)) {
                            if(in_array($productId, $discountIncTaxArray)) {
                                $discountIncTax = 1;
                            }
                        }
                        $goodQty = ($grn_received - ($grn_damaged + $grn_missed + $grn_quarantine+$grn_free));
                        if($discount_on_bill > 0 && $disc_before_tax==0) {
                            $contribution = ($rowTotal/$grandTotal);
                            $finalRowDiscount = ($contribution * $discount_on_bill);
                            $finalRowTotal = ($rowTotal - $finalRowDiscount);
                        }else{
                            $finalRowTotal = $rowTotal;
                        }
                        $elp = ($finalRowTotal/$goodQty);                        
                    $grnProducts[] = array(
                        'inward_id' => $inward_id,
                        'product_id' => $productId,
                        'orderd_qty' => (isset($data['grn_po_qty'][$key]) ? $data['grn_po_qty'][$key] : 0),
                        'received_qty' => $grn_received,
                        'good_qty' => ($grn_received - ($grn_damaged + $grn_missed + $grn_quarantine)),
                        'free_qty' => $grn_free,
                        'damage_qty' => $grn_damaged,
                        'missing_qty' => $grn_missed,
                        'excess_qty' => $grn_excess,
                        'quarantine_stock' => $grn_quarantine,
                        'cur_elp' => $elp,
                        'price' => (isset($data['grn_base_price'][$key]) ? $data['grn_base_price'][$key] : 0),
                        'tax_per' => (isset($data['grn_taxper'][$key]) ? $data['grn_taxper'][$key] : 0),
                        'tax_amount' => (isset($data['grn_taxvalue'][$key]) ? $data['grn_taxvalue'][$key] : 0),
                        'discount_type' => $discountType,
                        'discount_inc_tax' => $discountIncTax,
                        'discount_percentage' => (isset($data['grn_discount_percent'][$key]) ? $data['grn_discount_percent'][$key] : 0),
                        'discount_total' => (isset($data['grn_discount_amount'][$key]) ? $data['grn_discount_amount'][$key] : 0),
                        'sub_total' => $subTotal,
                        'row_total' => $rowTotal,
                        'remarks' => (isset($data['grn_remarks'][$key]) ? $data['grn_remarks'][$key] : 0),
                        'created_by' => Session::get('userId'),
                    );
                        //Log::info($grnProducts);
                    date_default_timezone_set('Asia/Kolkata');
                    $inputTax[] = array(
                        'inward_id' => $inward_id,
                        'product_id' => $productId,
                        'transaction_no' => $reference_id,
                        'transaction_type' => 101001,
                        'transaction_date' => date('Y-m-d'),
                        'tax_type' => (isset($data['grn_taxtype'][$key]) ? $data['grn_taxtype'][$key] : 0),
                        'tax_percent' => (isset($data['grn_taxper'][$key]) ? $data['grn_taxper'][$key] : 0),
                        'tax_amount' => (isset($data['grn_taxvalue'][$key]) ? $data['grn_taxvalue'][$key] : 0),
                        'le_wh_id' => $warehouse,
                        'created_by' => Session::get('userId'),
                    );

                    if(isset($data['grn_packsize'][$productId])){
                        foreach ($data['grn_packsize'][$productId] as $detkey=>$packdata) {
                            $grnProductsDetails[] = array(
                                'inward_prd_id' => DB::raw("(SELECT inward_prd_id from inward_products where product_id=$productId and inward_id=$inward_id)"),
                                'product_id' => $productId,
                                'pack_level' => (isset($data['grn_packsize'][$productId][$detkey]) ? $data['grn_packsize'][$productId][$detkey] : 0),
                                'pack_qty' => (isset($data['grn_eachesqty'][$productId][$detkey]) ? $data['grn_eachesqty'][$productId][$detkey] : 0),
                                'received_qty' => (isset($data['grn_receivedqty'][$productId][$detkey]) ? $data['grn_receivedqty'][$productId][$detkey] : 0),
                                'tot_rec_qty' => (isset($data['grn_receivedtotal'][$productId][$detkey]) ? $data['grn_receivedtotal'][$productId][$detkey] : 0),
                                'mfg_date' => (isset($data['grn_pkmfg_date'][$productId][$detkey]) ? date('Y-m-d',strtotime($data['grn_pkmfg_date'][$productId][$detkey])) :''),
                                'exp_date' => (isset($data['grn_pkexp_date'][$productId][$detkey]) ? $data['grn_pkexp_date'][$productId][$detkey] : 0),
                                'freshness_per' => (isset($data['grn_freshness_percentage'][$productId][$detkey]) ? $data['grn_freshness_percentage'][$productId][$detkey] : 0),
                                'remarks' => (isset($data['grn_pack_remarks'][$productId][$detkey]) ? $data['grn_pack_remarks'][$productId][$detkey] : 0),
                                'status' => (isset($data['grn_pack_status'][$productId][$detkey]) ? $data['grn_pack_status'][$productId][$detkey] : 0),
                                'created_by' => Session::get('userId'),
                            );
                        }
                    }

                    $checkStockInward = $this->_grnModel->checkStockInward($inward_id,$productId);
                    if(empty($checkStockInward))
                    {
                        $stockInward[] = array(
                            'le_wh_id' => $warehouse,
                            'product_id' => $productId,
                            'good_qty' => ($grn_received - 
                                    ($grn_damaged + $grn_missed + $grn_quarantine)),
                            'free_qty' => $grn_free,
                            'dnd_qty' => $grn_missed,
                            'dit_qty' => $grn_damaged,  //damage in transit
                            'quarantine_qty' => $grn_quarantine,
                            'po_no' => $po_id,
                            'reference_no' => $inward_id,
                            'inward_date' => date('Y-m-d H:i:s'),
                            'status' => '',
                            'created_by' => \Session::get('userId'),
                        );
                        $productData = [];
                        $productData['product_id'] = $productId;
                        $productData['soh'] = ($grn_received - 
                                    ($grn_damaged + $grn_missed + $grn_quarantine));
                        $productData['qty'] = ($grn_received - 
                                    ($grn_damaged + $grn_missed + $grn_quarantine));
                        $productData['free_qty'] = $grn_free;
                        $productData['quarantine_qty'] = $grn_quarantine;
                        $productData['dit_qty'] = $grn_damaged;  //damage in transit
                        $productData['dnd_qty'] = $grn_missed;
                        $productData['elp'] = $elp;
                        $productData['manf_date'] = (isset($data['grn_pkmfg_date'][$productId][0]) ? date('Y-m-d',strtotime($data['grn_pkmfg_date'][$productId][0])) :'');
                        $productData['exp_date'] = (isset($data['grn_pkexp_date'][$productId][0]) ? $data['grn_pkexp_date'][$productId][0] : 0);
                        $actual_po_quantity = $data['po_numof_eaches'][$productId];
                        $pending_qty = $actual_po_quantity - $productData['soh'];
                        if($pending_qty > 0 && $gds_order_id > 0){
                            $has_parent = $cartObj->isFreebie($productId);

                            $return_id = 59006;// default return resaon "Less Quantity";
                            // we can use elp directly for return total
                            // $return_total = $pending_qty * $elp;
                            $product = $OrderModel->getProductByOrderId($gds_order_id, array($productId));
                            $product = $product[0];
                            $tax_per_object = $OrderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
                            $tax_per = $tax_per_object->tax_percentage;
                            $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
                            $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;

                            // for correct value we are getting singleUnitPriceWithtax from gds order table
                            $return_total = $pending_qty * $singleUnitPriceWithtax;
                            $productData['returns'] = array(
                                                        "product_id"=> $productId,
                                                        "return_qty"=> $pending_qty,
                                                        "delivered_qty"=> $productData['soh'],
                                                        "return_reason"=> $return_id,
                                                        "has_parent"=> $has_parent,
                                                        "return_total"=> $return_total
                                                        );
                        }
                        $productInfo[] = $productData;
                    }
                }else{

                    if($gds_order_id > 0){
                        $has_parent = $cartObj->isFreebie($productId);
                        $return_id = 59006;// default return resaon "Less Quantity";
                        $product = $OrderModel->getProductByOrderId($gds_order_id, array($productId));
                        $product = $product[0];
                        $tax_per_object = $OrderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
                        $tax_per = $tax_per_object->tax_percentage;
                        $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
                        $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;
                        $productData = array();
                        $actual_po_quantity = $data['po_numof_eaches'][$productId];
                        $return_total = $actual_po_quantity * $singleUnitPriceWithtax;
                        $productData['returns'] = array(
                                                            "product_id"=> $productId,
                                                            "return_qty"=> $actual_po_quantity,
                                                            "delivered_qty"=> 0,
                                                            "return_reason"=> $return_id,
                                                            "has_parent"=> $has_parent,
                                                            "return_total"=> $return_total
                                                            );
                        $productInfo[] = $productData;
                    }
                }
                }
                if($gds_order_id > 0 && count($missed_prd_ids)){
                    foreach ($missed_prd_ids as $key => $value) {
                        # code...
                        $productId = $value;
                        $has_parent = $cartObj->isFreebie($productId);
                        $return_id = 59006;// default return resaon "Less Quantity";
                        $product = $OrderModel->getProductByOrderId($gds_order_id, array($productId));
                        $product = $product[0];
                        $tax_per_object = $OrderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
                        $tax_per = $tax_per_object->tax_percentage;
                        $singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);
                        $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;
                        $productData = array();
                        $actual_po_quantity = $product->qty;
                        $return_total = $actual_po_quantity * $singleUnitPriceWithtax;
                        $productData['returns'] = array(
                                                            "product_id"=> $productId,
                                                            "return_qty"=> $actual_po_quantity,
                                                            "delivered_qty"=> 0,
                                                            "return_reason"=> $return_id,
                                                            "has_parent"=> $has_parent,
                                                            "return_total"=> $return_total
                                                            );
                        $productInfo[] = $productData;
                    }
                }
                $this->_grnModel->saveGrnProducts($grnProducts);
                $this->_grnModel->saveInputTax($inputTax);
                $this->_grnModel->saveGrnProductDetails($grnProductsDetails);
                $userId = \Session::get('userId');
                // if($supply_le_wh_id !="" && $supply_le_wh_id !=0){
                //     $posodata = $this->createPoByData($inward_id,$po_id,$warehouse,$supply_le_wh_id);
                // }
                // print_r($supply_le_wh_id);die();
                $response = $this->grnUpdateStatus($inward_id,$inward_code,$po_id, $userId,$stockInward,$productInfo,$warehouse,$stock_transfer,$stock_transfer_dc);

                $updateResponse = json_decode($response, true);
               // Log::info('after grn update');
                if(isset($updateResponse['status']) && $updateResponse['status'] == 400) {
                    $message = '';
                 //   Log::info('Rolling(1) back in storeGrnData function');
                              DB::rollback();
                    $message = isset($updateResponse['message']) ? $updateResponse['message'] : '';
                    return Response::json(array('status' => 400, 'message' => $message, 'inward_id' => 0));
                }else{
                   // Log::info('Commiting in storeGrnData function');
                    		DB::commit();
                            $args = array("ConsoleClass" => 'autocpenalble', 'arguments' => array("inward_id"=>$inward_id));
                            $queue = new Queue();
                            $queue->enqueue('default', 'ResqueJobRiver', $args);
                            if($supply_le_wh_id !="" && $supply_le_wh_id !=0){
                                $posodata = $this->createPoByData($inward_id,$po_id,$warehouse,$supply_le_wh_id);
                            }
                            return Response::json(array('status' => 200, 'message' => Lang::get('inward.successCreated'), 'inward_id' => $inward_id));
    				}
            } else {
                // Log::info('Rolling back (2) in storeGrnData function');
                DB::rollback();
                return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData')));
            }
            }else{
				        DB::rollback();
                return Response::json(array('status' => 400, 'message' => 'GRN already created'));
            }
        } catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage().' => '.$ex->getTraceAsString());
            return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function grnUpdateStatus($inward_id,$inward_code,$po_id, $userId,$stockInward=[],$productInfo=[],$warehouse=0,$stock_transfer=0,$stock_transfer_dc=0) {
        try {
            $this->_grnModel->updateTaxValues($inward_id);
            $objPO = new PurchaseOrder();
            $totalPoQty = $objPO->getPoQtyByPoId($po_id);
            $poApprovalStatus = $this->_grnModel->getPoApprovalStatusByPoId($po_id);
            $objPO->updatePO($po_id, array('po_status' => 87002, 'approval_status' => 57035));
            $this->_grnModel->updateElpData($inward_id,$userId);
            if(count($stockInward)>0 && count($productInfo)>0 ){
                $this->_grnModel->saveStockInwardNew($inward_code,$warehouse,$stockInward,$productInfo,$stock_transfer,$stock_transfer_dc,$po_id);
            }else{
                $this->_grnModel->saveStockInward($inward_id);
            }
            $this->_grnModel->assetProductDetails($inward_id);
            $checkOrderStatus = $this->_grnModel->checkPOType($po_id);
            $gds_order_id = "";
            $checkIncvoice = array();
            if(!empty($checkOrderStatus) && count($checkOrderStatus)>0){
                if($checkOrderStatus->po_so_order_code != '0' && $checkOrderStatus->po_so_order_code !="" ){
                        $objPO = new PurchaseOrder();
                        $gds_order_id = $objPO->getOrderIdByCode($checkOrderStatus->po_so_order_code);
                        $checkIncvoice = $this->_grnModel->checkPOSOInvoiceStatus($gds_order_id);
                        if(count($checkIncvoice) == 0){
                            return Response::json(array('status' => 400, 'message' => 'PO Order not Invoiced!'));
                        }
                }
            }
            
            /**
             * default approval status
             */
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('GRN', 'drafted', $userId);
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                $current_status_id = $res_approval_flow_func["currentStatusId"];
                $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                $table = 'inward';
                $unique_column = 'inward_id';
                $objPO->updateStatusAWF($table, $unique_column, $inward_id, $next_status_id . ",0");
                $approval_flow_func->storeWorkFlowHistory('GRN', $inward_id, $current_status_id, $next_status_id, 'System approval at the time of insertion', \Session::get('userId'));
            }
            
            $current_status_id = $poApprovalStatus;
            $next_status_id = 57035;
            $appr_comment = 'GRN created';
            $approval_flow_func->storeWorkFlowHistory('Purchase Order', $po_id, $current_status_id, $next_status_id, $appr_comment, $userId);
            $this->sendElpNotification($inward_id);
            /**
             * Update status of PO
             */
            $totalInwardQty = $this->_inwardModel->getTotalInwardQtyById($po_id);            
            //Log::info('poApprovalStatus');
            //Log::info($poApprovalStatus);
            if ($totalInwardQty < $totalPoQty) {
//                57119
                if($poApprovalStatus == 57119)
                {
                    $this->_grnModel->createSubPo($po_id, $userId);
                }elseif($poApprovalStatus == 57107){
					DB::rollback();
                    return json_encode(array('status' => 400, 'message' => 'Cannot create GRN with partial quantity'));
                }
            }

            $deliverPOSOOrder = "";
            // Log::info("Grn entering into Delivery Module");
            if(count($checkIncvoice) > 0){
                $returns = array_column($productInfo, "returns");             
                $deliverPOSOOrder = @$this->deliverPOSOOrder($gds_order_id,$po_id,$returns);
            }
            // Log::info("Grn entered success into Delivery Module");
            Notifications::addNotification(['note_code' => 'GRN001', 'note_message' => Lang::get('inward.notificationMsg'), 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['GRNID' => $inward_code], 'note_link' => '/grn/details/' . $inward_id]);
            Log::info('update function completed');
        } catch (\ErrorException $ex) {
			DB::rollback();
            Log::error($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData').' here'));
        }
       // Log::info('grn email start');
        @$this->emailWithAttachment($inward_id);
        //Log::info('grn email ends');
        $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PUTAWAY001');
        if($hasAccess){
            $this->_grnModel->initiatePutAway($inward_id);
        }
		return json_encode(array('status' => 200, 'message' => ''));
    }
    public function createSubPoWithMissingItems($po_id, $userId){
        if($po_id!="" && $userId!=""){
            $this->_grnModel->createSubPo($po_id, $userId);
            $message='created successfully';
        }else{
            $message='Po id should not be empty';
        }
        return json_encode(array('status' => 200, 'message' => ''));
    }
    public function updateAction()
    {
        try
        {
            $data = Input::all();
//            Log::info($data);
            $errorMessage = '';
            if(!empty($data))
            {
                $inwardId = isset($data['inward_id']) ? $data['inward_id'] : 0;
                if($inwardId > 0)
                {
                    $reference_id = isset($data['reference_id']) ? $data['reference_id'] : '';
                    $invoice_id = isset($data['invoice_id']) ? $data['invoice_id'] : '';
                    $invoiceDate = isset($data['invoice_date']) ? $data['invoice_date'] : '';
                    $invoice_date = date('Y-m-d');
                    if($invoiceDate != '')
                    {
                        $invoice_date = date('Y-m-d', strtotime($data['invoice_date']));
                    }
                    $discount_on_bill = $data['discount_on_bill'];
                    $on_bill_discount_type = (isset($data['on_bill_discount_type']) && $data['on_bill_discount_type'] == 'on') ? 1 : 0;
                    $on_bill_discount_value = $discount_on_bill;
                    $discount_on_bill_options = (isset($data['discount_on_bill_options']) && $data['discount_on_bill_options'] == 'on') ? 1 : 0;
                    if($discount_on_bill_options)
                    {
                        $discount_on_bill = 0;
                    }elseif($discount_on_bill > 0 && $on_bill_discount_type == 1){
                        $discount_on_bill = isset($data['discount_on_bill_value']) ? $data['discount_on_bill_value'] : 0;
                    }
                    $shippingcost = $data['shippingcost'];
                    $baseTotal = isset($data['total_grn_basetotal']) ? $data['total_grn_basetotal'] : 0.00;
                    $grandTotal = isset($data['total_grn_grand_total']) ? $data['total_grn_grand_total'] : 0.00;            
                    $grnArr = array(
                        'inward_ref_no'=>$reference_id,
                        'invoice_no'=>$invoice_id,
                        'invoice_date'=>$invoice_date,
                        'discount_on_bill_options' => $discount_on_bill_options,
                        'on_bill_discount_type' => $on_bill_discount_type,
                        'on_bill_discount_value' => $on_bill_discount_value,
                        'discount_on_total' => $discount_on_bill,
                        'shipping_fee' => $shippingcost,
                        'base_total' => $baseTotal,
                        'grand_total' => $grandTotal,
                        'remarks' => $data['grn_remarks1'],
                        'updated_by' => Session::get('userId')
                    );
//                    Log::info($grnArr);
//                    echo "<pre>";print_r($grnArr);
                    $grnResponse = $this->_grnModel->grnUpdate($grnArr, $inwardId);
                    if(!$grnResponse)
                    {
                        $errorMessage = 'Unable to save GRN details.';
                    }
                    foreach ($data['grn_product_id'] as $key=>$productId) {
                        $subTotal = 0.00;
                        $rowTotal = 0.00;
                        $rowDetails = isset($data[$productId]) ? json_decode($data[$productId]) : [];
                        if(!empty($rowDetails))
                        {
                            $subTotal = property_exists($rowDetails, 'subTotal') ? str_replace(',', '', $rowDetails->subTotal) : 0.00;
                            $rowTotal = property_exists($rowDetails, 'totalval') ? str_replace(',', '', $rowDetails->totalval) : 0.00;
                        }
                        $discountType = 0;
                        $discountTypeArray = (isset($data['grn_discount_type']) ? $data['grn_discount_type'] : []);
                        if(!empty($discountTypeArray))
                        {
                            if(in_array($productId, $discountTypeArray))
                            {
                                $discountType = 1;
                            }
                        }
                        $discountIncTax = 0;
                        $discountIncTaxArray = (isset($data['grn_discount_inc_tax']) ? $data['grn_discount_inc_tax'] : []);
                        if(!empty($discountIncTaxArray))
                        {
                            if(in_array($productId, $discountIncTaxArray))
                            {
                                $discountIncTax = 1;
                            }
                        }
                        $grn_received = (int)(isset($data['grn_received'][$key]) ? $data['grn_received'][$key] : 0);
                        $grn_free = (int)((isset($data['grn_free'][$productId]) && is_array($data['grn_free'][$productId])) ? array_sum($data['grn_free'][$productId]) : 0);
                        $grn_damaged = (int)((isset($data['grn_damaged'][$productId]) && is_array($data['grn_damaged'][$productId])) ? array_sum($data['grn_damaged'][$productId]) : 0);
                        $grnProducts = array(
                            'received_qty' => $grn_received,
                            'free_qty' => $grn_free,
                            'damage_qty' => $grn_damaged,
                            'price' => (isset($data['grn_base_price'][$key]) ? $data['grn_base_price'][$key] : 0),
                            'tax_per' => (isset($data['grn_taxper'][$key]) ? $data['grn_taxper'][$key] : 0),
                            'tax_amount' => (isset($data['grn_taxvalue'][$key]) ? $data['grn_taxvalue'][$key] : 0),
                            'discount_type' => $discountType,
                            'discount_inc_tax' => $discountIncTax,
                            'discount_percentage' => (isset($data['grn_discount_percent'][$key]) ? $data['grn_discount_percent'][$key] : 0),
                            'discount_total' => (isset($data['grn_discount_amount'][$key]) ? $data['grn_discount_amount'][$key] : 0),
                            'sub_total' => $subTotal,
                            'row_total' => $rowTotal,
                            'remarks' => (isset($data['grn_remarks'][$key]) ? $data['grn_remarks'][$key] : ''),
                            'updated_by' => Session::get('userId'),
                        );
//                        Log::info($grnProducts);
//                        print_r($grnProducts);
                        $grnProductResponse = $this->_grnModel->updateGrnProducts($grnProducts, $inwardId, $productId);
                        
                        $inputTax = array(
//                            'inward_id' => $inwardId,
//                            'product_id' => $productId,
//                            'transaction_no' => $reference_id,
//                            'transaction_type' => 101001,
//                            'tax_type' => (isset($data['grn_taxtype'][$key]) ? $data['grn_taxtype'][$key] : 0),
//                            'tax_percent' => (isset($data['grn_taxper'][$key]) ? $data['grn_taxper'][$key] : 0),
                            'tax_amount' => (isset($data['grn_taxvalue'][$key]) ? $data['grn_taxvalue'][$key] : 0),
//                            'le_wh_id' => $warehouse,
                            'updated_by' => Session::get('userId'),
                        );
                        $this->_grnModel->updateInputTax($inputTax, $inwardId, $productId);
                        
                        if(!$grnProductResponse)
                        {
                            $errorMessage = 'Unable to save GRN details.';
                        }
                    }
//                    die;
                }else{
                    return Redirect::back()->withErrors(['msg', 'No GRN ID']);
                }
            }else{
                return Redirect::back()->withErrors(['msg', 'Empty data send unable to update.']);
            }
//            $this->indexAction();
            return Response::json(array('status' => 200, 'message' => 'Sucessfully updated.'));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function emailWithAttachment($grnId) {
        
        try{
            //Log::info('grnId from '.__METHOD__);
            //Log::info($grnId);
            /*$hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN003');
            if($hasAccess == false) {
                return View::make('Indent::error');
            } */

            
            $productArr = $this->_inwardModel->getInwardDetailById($grnId);
            //Log::info('grn product count');
            //Log::info(count($productArr));
            if(count($productArr) == 0) {
                //Redirect::to('/grn/index')->send();
                //die();
//				Log::info($grnId);
//				Log::info((array)$productArr);
//				Log::info('Unable to send mail as there are no products');
				return;
            }
           
            $inward_code = isset($productArr[0]->inward_code) ? $productArr[0]->inward_code : $grnId;
            $instance = env('MAIL_ENV');
            $subject = $instance.'New GRN#'.$inward_code.' Created';
            $body['attachment'] = array('nameSpace' => '\App\Modules\Grn\Controllers\GrnController','functionName'=>'pdfAction','args'=>array($grnId,1));
            $body['file_name'] = 'GRN_'.$inward_code.'.pdf';
            $body['template'] = 'emails.grn';
            $body['name'] = 'Hello All';
            $body['comment'] = '';

            $objPO = new PurchaseOrder();
            $notificationObj= new NotificationsModel();
            $userIdData= $notificationObj->getUsersByCode('GRN001');
            $userIdData=json_decode(json_encode($userIdData));
            $userEmailArr = $objPO->getUserEmailByIds($userIdData);
            $toEmails = array();
            if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                foreach($userEmailArr as $userData){
                    $toEmails[] = $userData['email_id'];
                }
            }
            //Log::info('toEmails');
            //Log::info($toEmails);
            Utility::sendEmail($toEmails, $subject, $body);
            //Log::info('Done');
        }
        catch(Exception $e) {
            
        }
    }
    /**
    * [sendElpNotification used to send ELP Notification Email using Queue]
    * @param [id]
     * Author: [Raju.A]
     * Copyright: ebutor 2018
     * Created Date: 10th Jan 2018 
    */
    public function sendElpNotification($inward_id) {
        try{
            $options[] = array('column'=>'inward_id','val'=>$inward_id,'vw_name'=>'vw_TodaysGRN');
            $options = base64_encode(json_encode($options));
            $args = array("ConsoleClass" => 'AutoEmail', 'arguments' => array('ELPC0001', 'ELPChange', 0, $options));
            $queue = new Queue();
            $queue->enqueue('default', 'ResqueJobRiver', $args);
        }
        catch(Exception $e) {
    
        }
    }    
    /**
     * inwardDocUpdate() method is use to Update Inward Id in inward_docs table
     * @param  inward_id
     * @return 
     * 
     */
    
    public function inwardDocUpdate($inward_id) {
        try {
            $po_id = DB::selectFromWriteConnection(DB::raw("select po_no from inward where inward_id=$inward_id"));
            $po_id = isset($po_id[0]->po_no) ? $po_id[0]->po_no :'' ;
            $inwarddocid = DB::selectFromWriteConnection(DB::raw("select inward_doc_id from inward_docs where po_id = $po_id"));
           foreach($inwarddocid as $docid){
                $docid = $docid->inward_doc_id;
                $checkinwardid=DB::selectFromWriteConnection(DB::raw("select inward_id from inward_docs where inward_doc_id=$docid"));
                if(isset($checkinwardid[0]->inward_id) && $checkinwardid[0]->inward_id ==0){
                $this->_dispute->inwardDocUpdate($inward_id,$docid);
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    /*
     * supplierSupplierBrandOptions() method is used to get Suppliers by legalentity 
     * Warehouse Id Options
     * @param Null
     * @return JSON
     */

    public function supplierWarehouseOptions() {
        try {
            $sup_id = Input::get('supplier_id');
            $warehouses = $this->_grnModel->getWarehouseBySupplierId($sup_id);
            $warehouseOptions = '<option value="0">Select Warehouse</option>';
            foreach($warehouses as $warehouse) {
                $warehouseOptions .= '<option value="'.$warehouse->le_wh_id.'">'.$warehouse->lp_wh_name.'</option>';
            }
            return json_encode(array('warehouse_list'=>$warehouseOptions));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * getProductInfoBySku() method is use to get product information by sku
     * @param  Null
     * @return JSON
     * 
     */
    
    public function getProductInfoBySku() {
        try {
            $sku = Input::get('sku');
            $product_info = $this->_grnModel->getProductInfoBySku($sku);
            return json_encode(array('product_info'=>$product_info));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    /**
     * getProductPackInfo() method is use to get package information of product
     * @param  Null
     * @return JSON
     * 
     */
    
    public function getProductPackInfo() {
        try {
            $product_id = Input::get('product_id');
            $edit_data = Input::get();
            $pack_product_data = '';
            $packStatus = $this->_grnModel->getProductPackStatus();
            if(isset($edit_data['grn_packsize'][$product_id])){
            foreach($edit_data['grn_packsize'][$product_id] as $key=>$packs){
                $pkstatus = $edit_data['grn_pack_status'][$product_id][$key];
                $quarantined = ($pkstatus==91003)?'quarantined"':'';
                $pack_status='';
                foreach($packStatus as $status){
                    $selected = ($pkstatus==$status->value)?'selected="selected"':'';
                    $pack_status .='<option value="'.$status->value.'" '.$selected.'>'.$status->master_lookup_name.'</option>';
                }
                $pack_product_data .= '<tr>
                    <td align="center">'.$edit_data['grn_eachesqty'][$product_id][$key].'</td>
                    <td align="center">'.$edit_data['grn_receivedqty'][$product_id][$key].'</td>
                    <td align="center">'.$edit_data['grn_receivedtotal'][$product_id][$key].'</td>
                    <td align="center">'.$edit_data['grn_pkmfg_date'][$product_id][$key].'</td>
                    <td align="center">'.$edit_data['grn_freshness_percentage'][$product_id][$key].'%</td>
                    <td align="center"><select name="pack_status[]" class="'.$quarantined.' form-control">'.$pack_status.'</select></td>
                    <td align="center"><textarea rows="1" name="pack_remarks[]">'.$edit_data['grn_pack_remarks'][$product_id][$key].'</textarea></td>
                    <td align="center">                                    
                         <a class="fa fa-trash-o delete_product_pack" data-id=""></a>
                         <input type="hidden" name="packsize_id[]" value="'.$packs.'">
                         <input type="hidden" name="eachesqty[]" value="'.$edit_data['grn_eachesqty'][$product_id][$key].'">
                         <input type="hidden" name="receivedqty[]" value="'.$edit_data['grn_receivedqty'][$product_id][$key].'">
                         <input type="hidden" class="receivedtotal" name="receivedtotal[]" value="'.$edit_data['grn_receivedtotal'][$product_id][$key].'">
                         <input type="hidden" value="'.$edit_data['grn_freshness_percentage'][$product_id][$key].'" name="freshness_percentage[]">
                         <input type="hidden" value="'.$edit_data['grn_pkmfg_date'][$product_id][$key].'" name="pkmfg_date[]">
                         <input type="hidden" value="'.$edit_data['grn_pkexp_date'][$product_id][$key].'" name="pkexp_date[]">
                    </td>
                 </tr>';
            }
            }
            $product_info = $this->_grnModel->getProductPackInfo($product_id);
            $taxArr = $this->_grnModel->getProductTaxClass($product_id,4033,4033);
            $tax_per = isset($taxArr[0]['Tax Percentage'])?$taxArr[0]['Tax Percentage']:0;
            $tax_type = isset($taxArr[0]['Tax Type'])?$taxArr[0]['Tax Type']:'';
            
            $packUOM = '<option value="">Select Pack UOM</option>';
            foreach($product_info as $product){
                $packUOM .='<option value="'.$product->level.'" data-noofeach="'.$product->no_of_eaches.'">'.$product->packname.'</option>';
            }
            
            $grn_received = (int)$edit_data['grn_received'][0];
            $grn_free = (int)$edit_data['grn_free'][0];
            $grn_damaged = (int)$edit_data['grn_damaged'][0];
            $grn_missed=(int)$edit_data['grn_missed'][0];
            $grn_excess=(int)$edit_data['grn_excess'][0];
            $grn_quarantine = (int)$edit_data['grn_quarantine'][0];
            
            $product_info['packuom'] = $packUOM;
            $product_info['taxes']['taxtype'] = $tax_type;
            $product_info['taxes']['taxpercent'] = $tax_per;
            $product_info['packcofigtable'] = $pack_product_data;
            $product_info['grn_received'] = $grn_received;
            $product_info['grn_free'] = $grn_free;
            $product_info['grn_damaged'] = $grn_damaged;
            $product_info['grn_missed'] = $grn_missed;
            $product_info['grn_excess'] = $grn_excess;
            $product_info['grn_quarantine'] = $grn_quarantine;
            $product_info['grn_remarks'] = $edit_data['grn_remarks'];
            $product_info['discountval'] = $edit_data['discountval'];
            $product_info['actual_received'] = ($grn_received);
            return json_encode($product_info);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * addGrnSkuText() method is use to add product
     * @param  Null
     * @return JSON
     * 
     */
    
    public function addGrnSkuText() {
        try {
            $data = Input::get('addSkuArr');
            //print_r($data);die;
            $product_id = $data['product_id'];
            $sku_uom = $data['sku_uom'];
            $le_wh_id = $data['le_wh_id'];
            $sup_id = $data['supplier_id'];
            $orderQty = $data['order_qty'];
            $productInfo = $this->_grnModel->getProductInfoById($product_id,$le_wh_id,$sup_id);
            $productInfo = json_decode(json_encode($productInfo),true);
            $product_data = '';
            
            $total_qty = 0;
            $total_taxper = 0;
            $total_taxvalue = 0;
            $grand_total = 0;
            $base_total = 0;

            $taxArr = $this->_grnModel->getProductTaxClass($product_id);
            $totRecQty = 0;
            $base_price = $productInfo['base_price']*$totRecQty;
            $tax_per = isset($taxArr[0]['Tax Percentage'])?$taxArr[0]['Tax Percentage']:0;
            $tax_type = isset($taxArr[0]['Tax Type'])?$taxArr[0]['Tax Type']:'';
            $tax_value = ($base_price*$tax_per)/100;
            $total = $base_price+$tax_value;
            $packUOMInfo = $this->_grnModel->getProductPackUOMInfo($product_id,$sku_uom);
            $uomName=isset($packUOMInfo->uomName) ? $packUOMInfo->uomName:'';
            $no_of_eaches=isset($packUOMInfo->no_of_eaches) ? $packUOMInfo->no_of_eaches:0;
            $sr=$data['sno_increment'];
            $unitPrice = $productInfo['unit_price'];
            $isTaxInclude = $productInfo['is_tax_included'];
            if ($isTaxInclude == 1)
            {
                $unitPrice = ($unitPrice / (1 + ($tax_per / 100)));
            }
            $product_name = $productInfo['product_name'];
            $prodname = (strlen($product_name) > 40) ? substr($product_name, 0, 40) . '...' : $product_name;
            
            $product_packs = $this->_grnModel->getProductPackInfo($product_id);
            $packUOM = '';
            foreach($product_packs as $pack){
                if($pack->level != 16001)
                {
                    $packUOM .='<option value="'.$pack->level.'" data-noofeach="'.$pack->no_of_eaches.'">'.$pack->packname.' ('.$pack->no_of_eaches.' Eaches)'.'</option>';
                }else{
                    $packUOM .='<option value="'.$pack->level.'" data-noofeach="'.$pack->no_of_eaches.'">'.$pack->packname.'</option>';
                }
            }
            $default_no_eaches = isset($product_packs[0]->no_of_eaches) ? $product_packs[0]->no_of_eaches : 1;
            $poQty = ($productInfo['qty'] > 0) ? $productInfo['qty'] : $orderQty;
            $remaining_packpo_qty = ($orderQty * $no_of_eaches);
            $product_data .= '<tr>
                            <td align="center"><a class="grnItem" href="javascript:void(0);" id="'.$product_id.'"><i class="fa fa-caret-right"></i></a></td>
                                <td align="center">'.$sr.'</td>
                                <td align="center">'.$productInfo['sku'].'</td>
                            <td align="left" title="' . $product_name . '">' . $prodname . '</td>
                            <td align="center">' . $productInfo['mrp'] . '</td>
                            <td align="center">'.$poQty.' '.$uomName.' ('.$no_of_eaches.' Eaches) <br /> Remaining PO Qty : '.($remaining_packpo_qty).' 
                                <input id="remaining_packpo_qty_'.$product_id.'" readonly="" class="form-control" value="'.$remaining_packpo_qty.'" type="hidden">
                                <input name="packpo_qty" readonly="" class="form-control" value="'.$poQty.'" type="hidden">
                                <input name="packpo_numof_eaches" readonly="" class="form-control" value="'.$no_of_eaches.'" type="hidden">
                            </td>
                            <td align="left"><span id="received'.$product_id.'">0</span>
                                <input name="grn_received[]" id="grn_received_'.$product_id.'" type="hidden" value="0">
                                <input id="total_grn_free_'.$product_id.'" type="hidden" value="0">
                                <input id="product_kvi_'.$product_id.'" type="hidden" value="'.$productInfo['kvi'].'">
                                <input id="total_grn_damaged_'.$product_id.'" type="hidden" value="0">
                                <input name="grn_totreceived[]" class="packtotrec" type="hidden" value="0">
                            </td>
                            <td align="left">
                            <span><input name="rqty" class="form-control" style="width: 50px;float:left;" type="number" min="0" value="0">
                            <select name="pack_size" id="pack_uom_qty_'.$product_id.'" class="form-control inpusmwidth">'.$packUOM.'</select>
                                <input readonly="" disabled="" class="form-control uomqty" value="'.$default_no_eaches.'" type="hidden">
                                <input readonly="" disabled="" class="form-control qtytotal" value="0" type="hidden">
                                </span>
                                </td>
                            <td align="left">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input class="form-control mfg_date" aria-invalid="false" type="text">
                                </div></td>
                            <td align="center"><input class="form-control grn_free" style="width: 50px;" type="number" min="0" value="0"></td>
                            <td align="center"><input class="form-control grn_damaged" style="width: 50px;" type="number" min="0" value="0"></td>
                            <td align="center" class="hide"><input name="grn_excess[]" class="form-control grn_excess" style="width: 50px;" type="number" min="0" value="0"></td>
                            <td align="center" class="hide"><input name="grn_missed[]" class="form-control grn_missed" style="width: 50px;" type="number" min="0" value="0"></td>                                
                            <td align="center" class="hide"><input name="grn_quarantine[]" class="form-control grn_quarantine" style="width: 50px;" type="number" min="0" value="0"></td>
                            <td align="center"><textarea id="grn_remarks'.$product_id.'" name="grn_remarks[]" style="width:60px;" rows="1" id=""></textarea></td>
                            <td align="center"><input id="baseprice'.$product_id.'" name="grn_base_price[]" class="form-control" max="' . $productInfo['mrp'] . '" style="width: 79px;" type="number" min="0" value="' . $unitPrice . '"></td>
                                <td align="center"><span id="subTotal'.$product_id.'">0</span></td>
                                <td align="center">'.$tax_type.'@'.(float)$tax_per.'%</td>
                                <td align="center"><span id="taxtext'.$product_id.'">'.number_format($tax_value,2).'</span></td>
                                <td align="center">
                                <span  style="float:left; width:20px;"><input type="checkbox" name="grn_discount_type[]" id="discounttype'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'">%</span><br/>
                                <span style="float:left;"><input type="checkbox" name="grn_discount_inc_tax[]" id="discount_tax_type'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'" checked="true">INC TAX</span>
                                <input name="grn_discount_percent[]" class="form-control" id="discountpercent'.$product_id.'" data-product-id="'.$product_id.'" type="number" min="0" value="0" style="float:left;width:50px;">
                                <input name="grn_discount_amount[]" class="form-control" id="discountamount'.$product_id.'" data-product-id="'.$product_id.'" type="hidden" value="0" />
                            </td>
                            <td align="left">
                                <span id="discount'.$product_id.'">0</span>
                                <input name="grn_discount_amount[]" class="form-control" id="discountamount'.$product_id.'" data-product-id="'.$product_id.'" type="hidden" value="0" />
                            </td>
                            <td align="center" id="total_amt'.$product_id.'"><span id="totalval'.$product_id.'">'.number_format($total,2).'</span></td>
                        <td align="center" class="zui-sticky-col" style="background:#fbfcfd !important;">
                                <a class="fa fa-plus addpackbtn" data-id="'.$product_id.'" data-poqty="'.$productInfo['qty'].'" data-poqtyuom="'.$uomName.'" data-ponumeaches="'.$no_of_eaches.'"></a>
                                    <a class="fa fa-trash-o delete_product" data-id="'.$product_id.'"></a>                                    
                                    <span>
                                        <input name="grn_product_id[]" checked="checked" id="" type="hidden" value="'.$product_id.'">
                                        <input name="grn_sku[]" checked="checked" id="" type="hidden" value="'.$productInfo['sku'].'">
                                        <input name="grn_upc[]" type="hidden" value="'.$productInfo['upc'].'">
                                        <input name="grn_title[]" type="hidden" value="'.$productInfo['product_name'].'">
                                        <input name="grn_pack_size[]" type="hidden" value="'.$productInfo['pack_size'].'">
                                        <input name="grn_mrp[]" type="hidden" value="'.$productInfo['mrp'].'">
                                    <input name="grn_po_qty[]" type="hidden" value="'.$productInfo['qty'].'">
                                        <input name="grn_taxtype[]" type="hidden" value="'.$tax_type.'">
                                        <input id="taxper'.$product_id.'" name="grn_taxper[]" type="hidden" value="'.$tax_per.'">
                                        <input name="grn_taxvalue[]" id="taxval'.$product_id.'" type="hidden" value="'.$tax_value.'">
                                        <input name="grn_total[]" id="grntotalval'.$product_id.'" min="0" type="hidden" value="'.$total.'">
                                    </span>
                                    <span class="productPackdata'.$product_id.'">
                                    </span>
                                </td>
                                                    </tr>
                        <tr style="display:none;" id="packinfo-'.$product_id.'"><td colspan="13">
                        <table class="table table-striped" id="packconfiglist-'.$product_id.'">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="font-size:10px;"><strong>Pack Size</strong></th>
                                <th style="font-size:10px;"><strong>QTY (in Eaches)</strong></th>
                                <th style="font-size:10px;"><strong>Received</strong></th>
                                <th style="font-size:10px;"><strong>Tot.Rec.Qty</strong></th>
                                <th style="font-size:10px;"><strong>MFG Date</strong></th>
                                <th style="font-size:10px;"><strong>Freshness</strong></th>
                                <th style="font-size:10px;"><strong>Free</strong></th>
                                <th style="font-size:10px;"><strong>Damaged</strong></th>
                                <th style="font-size:10px;"><strong>Status</strong></th>
                                <th style="font-size:10px;"><strong>Remarks</strong></th>
                                <th></th>
                        </tr></thead>
                        <tbody></tbody>
                        </table></td></tr>';
            $sr++;
            $total_qty+=$data['order_qty'];
                $total_taxper+=$tax_per;
                $total_taxvalue+=$tax_value;
                $base_total=($base_total+$total-$tax_value);
                $grand_total+=$total;
             $response['sno'] = $sr;

             $calculation = array('totqty'=>$total_qty,'tottaxper'=>$total_taxper,'tottaxval'=>$total_taxvalue,'basetot'=>$base_total,'grandtot'=>$grand_total);
            

            //$final = Session::push('grnProductData.'.$data['upc'], $product_data);
            return json_encode(array('product_data'=>$product_data,'calculation'=>$calculation,'sno_increment'=>$sr));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getPackText() {
        try {
            $data = Input::get();
            $product_id = $data['packproduct_id'];
            $shelfLife = $this->_grnModel->getProductShelfLife($product_id);
            $shelf_life = $shelfLife->shelf_life;
            $shelfuom = $shelfLife->master_lookup_name;
            $mfg_date = '';
            $exp_date= '';
            $shelfLifePercentage=0;
            if($data['mfg_date']!=''){
                $mfg_date = date('d-m-Y',strtotime($data['mfg_date']));
                $suom = $shelfuom[0];
                $addstring = 'P';
                if($suom=='H'){
                    $addstring = 'P0Y0M0DT';
                }
                $date = new \DateTime($mfg_date);                
                if($shelf_life!=0){
                    $date->add(new \DateInterval($addstring.$shelf_life.$suom)); //new DateInterval('P7Y5M4DT4H3M2S')
                    $exp_date = $date->format('Y-m-d H:i:s');
                }else{
                    $exp_date = date('Y-m-d H:i:s');
                }
                $datetime1 = new \DateTime($mfg_date);
                $datetime2 = new \DateTime($exp_date);
                $interval = $datetime1->diff($datetime2);
                $totdays = $interval->format('%a');

                $currdate = new \DateTime(date('Y-m-d'));
                $interval1 = $currdate->diff($datetime2);
                $remaindays = $interval1->format('%a');
                
                $expdate=strtotime($exp_date);
                $curdate=strtotime(date('Y-m-d'));

                if($totdays>0 && $remaindays<=$totdays && $expdate>=$curdate){
                    $shelfLifePercentage = number_format(($remaindays*100)/$totdays,2);
                }else{
                    $shelfLifePercentage = number_format(0,2);
                }
            }
            $packStatus = $this->_grnModel->getProductPackStatus();
            $pack_status='';
            foreach($packStatus as $status){
                $pack_status .='<option value="'.$status->value.'">'.$status->master_lookup_name.'</option>';
            }
            $packUOMInfo = $this->_grnModel->getProductPackUOMInfo($product_id,$data['pack_size']);
                $uomName=isset($packUOMInfo->uomName) ? $packUOMInfo->uomName:'';
                $no_of_eaches=isset($packUOMInfo->no_of_eaches) ? $packUOMInfo->no_of_eaches:0;
            $product_data = '';
           $free = (isset($data['free']) && $data['free']!='') ? $data['free'] : 0;
           $damaged = (isset($data['damaged']) && $data['damaged']!='') ? $data['damaged'] : 0;
            $product_data .= '<tr>
                                    <th></th>
                                    <td align="center">'.$uomName.'</td>
                               <td align="center">'.$data['uomqty'].'</td>
                               <td align="center">'.$data['rqty'].'</td>
                               <td align="center" class="total_recieved_qty">' . $data['qtytotal'] . '</td>
                               <td align="center">'.$mfg_date.'</td>
                               <td align="center">'.$shelfLifePercentage.'%</td>
                                    <td style="font-size:10px;">'.$data['free'].'</td>
                                    <td style="font-size:10px;">'.$data['damaged'].'</td>
                                    <td style="font-size:10px;"><select class="form-control" name="grn_pack_status['.$product_id.'][]">'.$pack_status.'</select></td>
                                    <td style="font-size:10px;"><textarea rows="1" class="form-control" name="grn_pack_remarks['.$product_id.'][]" value=""></textarea></td>
                               <td align="center">                                    
                                    <a class="fa fa-trash-o delete_product_pack" data-id=""></a>
                                    <input type="hidden" id="product_id" value="'.$product_id.'" />
                                    <input name="grn_packsize['.$product_id.'][]" min="0" id="" type="hidden" value="'.$data['pack_size'].'">
                                    <input name="grn_eachesqty['.$product_id.'][]" type="hidden" value="'.$data['uomqty'].'">
                                    <input name="grn_receivedqty['.$product_id.'][]" type="hidden" value="'.$data['rqty'].'">
                                    <input name="grn_receivedtotal['.$product_id.'][]" type="hidden" value="'.$data['qtytotal'].'">
                                    <input name="grn_freshness_percentage['.$product_id.'][]" type="hidden" value="'.$shelfLifePercentage.'">
                                    <input name="grn_pkmfg_date['.$product_id.'][]" type="hidden" value="'.$mfg_date.'">
                                    <input name="grn_pkexp_date['.$product_id.'][]" type="hidden" value="'.$exp_date.'">
                                    <input name="grn_free['.$product_id.'][]" id="row_free_qty_'.$product_id.'" type="hidden" value="'.$free.'">
                                    <input name="grn_damaged['.$product_id.'][]" id="row_damaged_qty_'.$product_id.'" type="hidden" value="'.$damaged.'">
                               </td>
                            </tr>';
            return json_encode(array('product_data'=>$product_data,'received_qty'=>$data['qtytotal'],'freshness_per'=>$shelfLifePercentage));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * getGrnAction() method is used to get all grn
     * @param  Request $request Object
     * @return JSON
     */
 public function getGrnAction(Request $request, $status = null) {
        try {

            $statusArr = $this->_masterLookup->getAllOrderStatus('GRN');
            // $offset = (int)$request->input('$skip');
            //          $perpage = $request->input('$stop');

            $page = $request->input('page');   //Page number
            $page_size = $request->input('pageSize'); //Page size for ajax call
            // $perpage = isset($perpage) ? $perpage : 10;
            $postData = $request->all();

            $fromDate = '';
            $nextMonth = '';
            $orderby_array = "";
            $filters = $request->input('$filter');
            if ($request->input('$orderby')) {             //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc') {
                    $order_by_type = 'asc';
                }

                if (isset($this->produc_grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                    $order_by = $this->produc_grid_field_db_match[$order_query_field];
                }

                $orderby_array = $order_by . " " . $order_by_type;
            }

            $filter_by = $this->filterData($filters);
            $inward_code = isset($postData['inward_code']) ? $postData['inward_code'] : 0;
            $filters = array();
            if(isset($status) && $status!='') {
                $filters['status_id'] = $status;
            }
            $totalInwards = $this->_inwardModel->getTotalInward($filter_by, $filters);
            $inwardsArr = $this->_inwardModel->getAllInward($filter_by, $filters, 0, $page, $page_size, $orderby_array);
            $dataArr = array();
            $sno = 1;
                                                    
            $detailFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN003');
            $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN005');
            $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN006');
            $editFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN007');
            foreach ($inwardsArr as $inward) {
                $invoiceCode = $inward->invoice_code;
                $actions = '';
                if($editFeature && $invoiceCode == ''){
                    $actions.='<a href="/grn/edit/' . $inward->inward_id . '"><i class="fa fa-pencil"></i></a>&nbsp;';
                }                                        
                if($detailFeature){
                    $actions.='<a href="/grn/details/' . $inward->inward_id . '"><i class="fa fa-eye"></i></a>&nbsp;';
                }                                        
                if($printFeature){
                    $actions.= '<a href="/grn/print/' . $inward->inward_id . '"> <i class="fa fa-print"></i> </a>&nbsp;';
                }
                if($downloadFeature){
                    $actions.= '<a href="/grn/pdf/' . $inward->inward_id . '"> <i class="fa fa-download"></i> </a>&nbsp;';
                }
                $dataArr[] = array(
                    'grnId' => $inward->inward_code,
                    'poId' => $inward->po_code,
                    'grnDate' => $inward->created_at,
                    'legalsuplier' => $inward->business_legal_name,
                    'dcname'=>$inward->dcname,
                    'createdBy' => $inward->createdBy,
                    'ref_no' => $inward->inward_ref_no,
                    'invoice_no' => $inward->invoice_no,
                    'povalue' => $inward->povalue,
                    'grnvalue' => $inward->grnvalue,
                    'item_discount_value' => $inward->item_discount_value,
                    'Actions' => $actions
                );
                $sno++;
            }
            return Response::json(array('data' => $dataArr, 'totalGrn' => $totalInwards));
        } catch (Exception $e) {
            return Response::json(array('data' => array(), 'totalGrn' => 0));
        }
    }
    private function filterData($filters) {
        try {
            $filterDataArr = array();
            if (isset($filters)) {
                $stringArr = explode(' and ', $filters);
                if (is_array($stringArr)) {
                foreach ($stringArr as $data) {
                    $dataArr = explode(' ', $data);
                    if (substr_count($data, 'grnDate')) {
                        $filterDataArr['grnDate']['operator'] = $this->getCondOperator($dataArr[1]);
                        if (substr_count($dataArr[2], 'DateTime')) {
                            $dataArrr = explode("'", $dataArr[2]);
                            $time = strtotime($dataArrr[1]);
                            $filterDataArr['grnDate'][] = date("d", $time);
                            $filterDataArr['grnDate'][] = date("m", $time);
                            $filterDataArr['grnDate'][] = date("Y", $time);
                        } else {
                            $filterDataArr['grnDate'][] = $dataArr[2];
                        }
                    }                    
                    if (substr_count($data, 'grnvalue')) {
                        $filterDataArr[] = $this->produc_grid_field_db_match['grnvalue'].' '.$this->getCondOperator($dataArr[1]).' '. $dataArr[2];
                    }
                    if (substr_count($data, 'povalue')) {
                        $filterDataArr[] = $this->produc_grid_field_db_match['povalue'].' '.$this->getCondOperator($dataArr[1]).' '. $dataArr[2];
                    }
                    if (substr_count($data, 'item_discount_value')) {
                        $filterDataArr[] = $this->produc_grid_field_db_match['item_discount_value'].' '.$this->getCondOperator($dataArr[1]).' '. $dataArr[2];
                    }
                    if (substr_count($data, 'grnId') && !array_key_exists('grnId', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poIdValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'grnId','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poIdValArr,' ') : '%'.trim($poIdValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['grnId'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'poId') && !array_key_exists('poId', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $poIdValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'poId','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($poIdValArr,' ') : '%'.trim($poIdValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['poId'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'legalsuplier') && !array_key_exists('legalsuplier', $filterDataArr)) {
                        $sup = explode(' ge ', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'legalsuplier','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['legalsuplier'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'dcname') && !array_key_exists('dcname', $filterDataArr)) {
                        $sup = explode(' ge ', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'dcname','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['dcname'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'ref_no') && !array_key_exists('ref_no', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'ref_no','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['ref_no'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'invoice_no') && !array_key_exists('invoice_no', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'invoice_no','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['invoice_no'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'createdBy') && !array_key_exists('createdBy', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'createdBy','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['createdBy'].' '.$operator.' '.$value;
                    }
                    
                }
            }
            }
            return $filterDataArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
        /*
     * getCondOperator() method is used to get condition operator
     * @param $operator String
     * @return String
     */
    private function getCondOperator($operator) {
        try {
            switch ($operator) {
                case 'eq' :
                    $condOperator = '=';
                    break;

                case 'ne':
                    $condOperator = '!=';
                    break;

                case 'gt' :
                    $condOperator = '>';
                    break;

                case 'lt' :
                    $condOperator = '<';
                    break;

                case 'ge' :
                    $condOperator = '>=';
                    break;

                case 'le' :
                    $condOperator = '<=';
                    break;
            }
            return $condOperator;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * detailsAction() method is used to get inward detail
     * @param  Numeric $grnId
     * @return Array
     */
    
    public function detailsAction($grnId, $returnId = 0) {

        try {
            $isEditable = $this->_roleRepo->checkPermissionByFeatureCode('GRNREFEDIT001');
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN003');
            if ($hasAccess == false) {
                return View::make('Indent::error');
            }
            parent::Title('View GRN');
            $grnProductArr = $this->_inwardModel->getInwardDetailById($grnId);
            if (count($grnProductArr) == 0) {
                Redirect::to('/grn/index')->send();
                die();
            }
            Session::set('inward_id', $grnId);
            //$taxArr = array();//$this->_taxModel->getProductTaxByGrnId($grnId);
            $docTypes = $this->_dispute->getDocumentTypes();
            $docsArr = $this->_dispute->getDocuments($grnId);
			$deliveryGTIN = $this->_grnModel->getDeliveryGtin($grnId);

            $packArr = $uomInfoArr = array();
            $returnModel = new ReturnModel();
            $totalRecvedQty = 0;
            foreach ($grnProductArr as $product) {
                $packArr[$product->inward_prd_id] = $this->_inwardModel->getProductPackInfo($product->inward_prd_id);
                $uomInfoArr = $this->_grnModel->getProductPackUOMInfo($product->product_id, $product->uom);
                $returnArr = $returnModel->getProductReturnQty($grnId, $product->product_id);
                if (!$product->no_of_eaches) {
                    if (empty($uomInfoArr)) {
                        $eachs_count = $product->orderd_qty;
                    } else {
                        $eachs_count = $product->orderd_qty * $uomInfoArr->no_of_eaches;
                    }
                } else {
                    $eachs_count = $product->orderd_qty * $product->no_of_eaches;
                }
                if (!empty($uomInfoArr)) {
                    if ($product->uom != '16001') {
                        $product->orderd_qty = $product->orderd_qty . " " . $uomInfoArr->uomName . " <span style='font-size:10px;'>(" . $eachs_count . " Eaches)</span>";
                    } else {
                        $product->orderd_qty = $product->orderd_qty . " " . $uomInfoArr->uomName;
                    }
                }
                $totalRecvedQty += $product->received_qty;
                //$product->returnQty = (isset($returnArr->returnQty))?$returnArr->returnQty:0;                
                $product->ret_soh_qty = (isset($returnArr->ret_soh_qty))?$returnArr->ret_soh_qty:0;
                $product->ret_dit_qty = (isset($returnArr->ret_dit_qty))?$returnArr->ret_dit_qty:0;
                $product->ret_dnd_qty = (isset($returnArr->ret_dnd_qty))?$returnArr->ret_dnd_qty:0;
            }
            $inputTaxArr = $this->_taxModel->getInputTaxByInwardId($grnId);
            $po_id = $grnProductArr[0]->po_no;
            $objPO = new PurchaseOrder();
            $poArr = $objPO->getPoCodeById($po_id);
            $invoiceExist = $objPO->checkInvoiceByInwardId($grnId);
            $leWhId = isset($grnProductArr[0]->le_wh_id) ? $grnProductArr[0]->le_wh_id : 0;
            $whInfo = $this->_LegalEntity->getWarehouseById($leWhId);
			$billingAddress = $this->_grnModel->getBillingAddress($leWhId);
            // getting billing address according to legal entity id
            $grnDeatil = $this->_inwardModel->getInwardDetail($grnId);
            $leId = isset($grnDeatil->legal_entity_id)?$grnDeatil->legal_entity_id:0;

            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);

            if($leParentId)
                $billingAddress = $this->_LegalEntity->getLegalEntityById($leParentId);
            else
                $billingAddress = $this->_LegalEntity->getLegalEntityById($billingAddress->legal_entity_id);

            // checking supplier is global supplier le id = 24766
            $wh_le_id = isset($whInfo->legal_entity_id)?$whInfo->legal_entity_id:0;
            if($leId == 24766){
                $objPO = new PurchaseOrder();
                $apob_data = $objPO->getApobData($wh_le_id);
                if(count($apob_data)){
                    foreach($apob_data as $key => $value){
                        $grnProductArr[0]->$key = $value;
                        unset($apob_data->$key);
                    }
                }
            }

            // displaying blling  address as per company gst re gistered address in that state  
            $wh_state = isset($whInfo->state)?$whInfo->state:0;
            $check_apob = $this->_LegalEntity->checkisApob($leWhId);
            if($wh_state > 0 && $check_apob){
                $wh_state_data = $this->_LegalEntity->getStateBillingDC($wh_state);
                if(count($wh_state_data)){
                    $billingAddress = $this->_LegalEntity->getWarehouseById($wh_state_data->le_wh_id);
                }
            }
            /*             * * data required for Approval Workflow ** */
            $approval_flow_func = new CommonApprovalFlowFunctionModel();
            $status = (isset($grnProductArr[0]->approval_status) && $grnProductArr[0]->approval_status != 0) ? $grnProductArr[0]->approval_status : 'drafted';
            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('GRN', $status, \Session::get('userId'));
            $approvalOptions = array();
            $approvalVal = array();
            $isApprovalFinalStep = 0;
            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                foreach ($res_approval_flow_func["data"] as $options) {
                    if ($options['isFinalStep'] == 1) {
                        $isApprovalFinalStep = $options['isFinalStep'];
                    }
                    $approvalOptions[$options['nextStatusId'] . ',' . $options['isFinalStep']] = $options['condition'];
                }
            }
            $approvalVal = array('current_status' => $status,
                'approval_unique_id' => $grnId,
                'approval_module' => 'GRN',
                'table_name' => 'inward',
                'unique_column' => 'inward_id',
            );
            $approvalHistory = $objPO->getApprovalHistory('GRN', $grnId);
            /*** data required for Approval Workflow***/

            $approvalStatus = $this->_masterLookup->getAllOrderStatus('Approval Status');
            $approvedStatus = (isset($approvalStatus[$grnProductArr[0]->approval_status])) ? $approvalStatus[$grnProductArr[0]->approval_status] : '';
            if ($grnProductArr[0]->approval_status == 1) {
                $approvedStatus = 'Approved';
            }
            $printFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN005');
            $downloadFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN006');
            $poInvoiceFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN004');
            $poDetailFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO003');
            $grnEditFeature = $this->_roleRepo->checkPermissionByFeatureCode('GRN007');
            $returnCreateFeature = $this->_roleRepo->checkPermissionByFeatureCode('PR002');
            $createOrderFeature = $this->_roleRepo->checkPermissionByFeatureCode('POCRT001');

            $featureAccess = array('printFeature'=>$printFeature,
                                    'downloadFeature'=>$downloadFeature,
                                    'poDetailFeature'=>$poDetailFeature,
                                    'grnEditFeature'=>$grnEditFeature,
                                    'returnCreateFeature'=>$returnCreateFeature,
                                    'poInvoiceFeature'=>$poInvoiceFeature,
                                    'createOrderFeature'=>$createOrderFeature,
                                     'grnreferencenoedit'=>$isEditable);
          
            $totalReturns = (int) $returnModel->getAllPurchaseReturns($grnId, 1);            
            $statusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE RETURN');
            $reasonsArr = $this->_masterLookup->getAllOrderStatus('Purchase Return Reasons');
            $totalReturnQty = $returnModel->getReturnQtyByInwardId($grnId);
            $invoiceCount = $objPO->poInvoiceCountByPOId($po_id);
            $leId = (isset($poArr->legal_entity_id) ? $poArr->legal_entity_id : '');
            $totalPayments = (int) $objPO->getAllPayments($leId, 1);
            
            $poCode = explode('_',$poArr->po_code);
            $po_code = isset($poCode[0])?$poCode[0]:0;
            $parent = $objPO->getPODetailsByCode($po_code);
            $checkOrderStatus = $this->_grnModel->checkPOType($po_id);
            $orderDelivered = true;
            $gds_order_id = 0;
            if(!empty($checkOrderStatus) && count($checkOrderStatus)>0){
                if($checkOrderStatus->po_so_order_code != '0' && $checkOrderStatus->po_so_order_code !="" ){
                        $gds_order_id = $this->_grnModel->checkPODeliverStatus($checkOrderStatus->po_so_order_code);
                        Log::info('check orderstatus');
                        $poDetailArr = $objPO->getPoDetailById($po_id);
                        if($gds_order_id == "" ){
                            $orderDelivered = false;
                            $gds_order_id = $objPO->getOrderIdByCode($poDetailArr[0]->po_so_order_code);
                        }
                }else{
                    $orderDelivered = false;
                }
            }
            $parentPOId = isset($parent->po_id)?$parent->po_id:$id;
            
            return view('Grn::details')
                            ->with('grnProductArr', $grnProductArr)
                            ->with('inputTaxArr', $inputTaxArr)
                            ->with('packArr', $packArr)
							->with('billingAddress', $billingAddress)
                            ->with('po_code', (isset($poArr->po_code) ? $poArr->po_code : ''))
                            ->with('po_id', $po_id)
                            ->with('parentPOId', $parentPOId)
                            ->with('leId', $leId)
                            ->with('totalPayments', $totalPayments)
                            ->with('docTypes', $docTypes)
                            ->with('invoiceExist', $invoiceExist)
                            ->with('docsArr', $docsArr)
                            ->with('whInfo', $whInfo)
                            ->with('approvedStatus', $approvedStatus)
                            ->with('approvalOptions', $approvalOptions)
                            ->with('approvalVal', $approvalVal)
                            ->with('isApprovalFinalStep', $isApprovalFinalStep)
                            ->with('featureAccess', $featureAccess)                            
                            ->with('history', $approvalHistory)
                            ->with('totalReturns', $totalReturns)
                            ->with('totalRecvedQty', $totalRecvedQty)
                            ->with('totalReturnQty', $totalReturnQty)
                            ->with('returnId', $returnId)
                            ->with('statusArr', $statusArr)
                            ->with('invoiceCount', $invoiceCount)
							->with('deliveryGTIN', $deliveryGTIN)
                            ->with('reasonsArr', $reasonsArr)
                            ->with('orderDelivered', $orderDelivered)
                            ->with('gds_order_id', $gds_order_id);
        } catch (Exception $e) {
            
        }
    }

    /**
     * editAction() method is used to get inward detail
     * @param  Numeric $grnId
     * @return Array
     */
    
    public function editAction($grnId)
    {
        try
        {
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN007');
            if ($hasAccess == false)
            {
                return View::make('Indent::error');
            }
            $breadCrumbs = array('Home' => url('/'), 'GRN' => url('/grn/index'), 'Edit GRN' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $grnDetails = $this->_grnModel->getGrnDetails($grnId);
            $grnProductDetails = $this->_grnModel->getGrnProductDetails($grnId); 
//            DB::enableQueryLog();
            $grnDocDetails = $this->_grnModel->getGrnDocDetails($grnId);            
//            Log::info(DB::getQueryLog());
//            echo "<pre>";
//            print_R($grnDetails);
//            print_R($grnProductDetails);
//            print_R($grnDocDetails);
//            die;
            if(empty($grnDetails))
            {
                Redirect::to('/grn/index')->with(['error' => 'Invalid GRN number'])->send();
		}
            $inwardCode = $grnDetails->inward_code;
            parent::Title('Edit GRN '.$inwardCode);
            $docTypes = $this->_dispute->getDocumentTypes();
            $objPO = new PurchaseOrder();
            $po_no = $grnDetails->po_no;
            $approvalHistory = $objPO->getApprovalHistory('Purchase Order', $po_no);
            return view('Grn::edit')->with(['grnDetails' => $grnDetails,
                'docTypes' => $docTypes,
                'grnDocDetails' => $grnDocDetails,
                'product_list' => $grnProductDetails,
                'history' => $approvalHistory]);
			
        } catch (\ErrorException $ex)
        {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
		}
	}

    public function printGrn($grnId) {
        
        try{

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN003');
            if($hasAccess == false) {
                return View::make('Indent::error');
            } 
            $productArr = $this->_inwardModel->getInwardDetailById($grnId);
            if(count($productArr) == 0) {
                Redirect::to('/grn/index')->send();
                die();
            }
            $inwardCode = isset($productArr[0]->le_code) ? $productArr[0]->le_code : '';
            parent::Title('Print GRN '.$inwardCode);
            $inputTaxArr = $this->_taxModel->getInputTaxByInwardId($grnId);
            $taxSummArr = array();

            if(is_array($inputTaxArr) && count($inputTaxArr) > 0) {
                $totTaxAmt = 0;
                foreach ($inputTaxArr as $tax) {
                    if(isset($taxSummArr[(string)$tax->tax_percent]['amt'])) {
                        $totTaxAmt = $totTaxAmt + $taxSummArr[(string)$tax->tax_percent]['amt'];
                    }
                    else {
                        $totTaxAmt = $tax->tax_amount;
                    }
                    $taxSummArr[(string)$tax->tax_percent] = array('type'=>$tax->tax_type, 'amt'=>$totTaxAmt, 'per'=>$tax->tax_percent);
                }    
            }
           
            $objPO = new PurchaseOrder();
            $poArr = $objPO->getPoCodeById($productArr[0]->po_no);
            $po_date = (isset($poArr->po_date) ? date('d-m-Y', strtotime($poArr->po_date)) : '');
            $leId = isset($productArr[0]->legal_entity_id) ? $productArr[0]->legal_entity_id : 0;
            $leWhId = isset($productArr[0]->le_wh_id) ? $productArr[0]->le_wh_id : 0;
			$billingAddress = $this->_grnModel->getBillingAddress($leWhId);
            // getting billing address according to legal entity id
            $grnDeatil = $this->_inwardModel->getInwardDetail($grnId);
            $leId = isset($grnDeatil->legal_entity_id)?$grnDeatil->legal_entity_id:0;

            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            if($leParentId)
                $leInfo = $this->_LegalEntity->getLegalEntityById($leParentId);
            else
                $leInfo = $this->_LegalEntity->getLegalEntityById($billingAddress->legal_entity_id);

            if($leParentId)
                $billingAddress = $this->_LegalEntity->getLegalEntityById($leParentId);
            else
                $billingAddress =  $this->_LegalEntity->getLegalEntityById($billingAddress->legal_entity_id);
            $userInfo = $this->_LegalEntity->getUserByLegalEntityId($leId);
            $whInfo = $this->_LegalEntity->getWarehouseById($leWhId);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);
			$deliveryGTIN = $this->_grnModel->getDeliveryGtin($grnId);

            // checking supplier is global supplier le id = 24766
            $wh_le_id = isset($whInfo->legal_entity_id)?$whInfo->legal_entity_id:0;
            if($leId == 24766){
                $objPO = new PurchaseOrder();
                $apob_data = $objPO->getApobData($wh_le_id);
                if(count($apob_data)){
                    foreach($apob_data as $key => $value){
                        $productArr[0]->$key = $value;
                        unset($apob_data->$key);
                    }
                }
            }
            // displaying blling  address as per company gst registered address in that state
            $wh_state = isset($whInfo->state)?$whInfo->state:0;
            $check_apob = $this->_LegalEntity->checkisApob($leWhId);
            if($wh_state > 0 && $check_apob){
                $wh_state_data = $this->_LegalEntity->getStateBillingDC($wh_state);
                if(count($wh_state_data)){
                    $billingAddress = $this->_LegalEntity->getWarehouseById($wh_state_data->le_wh_id);
                }
            }
            return view('Grn::print')
                                ->with('grnProductArr', $productArr)
                                ->with('leInfo', $leInfo)
								->with('billingAddress', $billingAddress)
                                ->with('taxSummArr', $taxSummArr)
                                ->with('userInfo', $userInfo)
                                ->with('whInfo', $whInfo)
                                ->with('po_date', $po_date)
                                ->with('companyInfo', $companyInfo)
								->with('deliveryGTIN', $deliveryGTIN)
                                ->with('po_code', (isset($poArr->po_code) ? $poArr->po_code : ''));
        }
        catch(Exception $e) {
            
        }
    }

    public function pdfAction($grnId, $forEmail = 0) {
        
        try{

            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('GRN003');
            if($hasAccess == false && $forEmail == 0) {
                return View::make('Indent::error');
            } 

            
            $productArr = $this->_inwardModel->getInwardDetailById($grnId);
            //Log::info('PdfAction Product array');
            //Log::info($grnId);
            //Log::info($productArr);
            if(count($productArr) == 0) {
                Redirect::to('/grn/index')->send();
                die();
            }
            //Log::Info('padfaction in process');
            $inputTaxArr = $this->_taxModel->getInputTaxByInwardId($grnId);
            $taxSummArr = array();

            if(is_array($inputTaxArr) && count($inputTaxArr) > 0) {
                $totTaxAmt = 0;
                foreach ($inputTaxArr as $tax) {
                    if(isset($taxSummArr[(string)$tax->tax_percent]['amt'])) {
                        $totTaxAmt = $totTaxAmt + $taxSummArr[(string)$tax->tax_percent]['amt'];
                    }
                    else {
                        $totTaxAmt = $tax->tax_amount;
                    }
                    $taxSummArr[(string)$tax->tax_percent] = array('type'=>$tax->tax_type, 'amt'=>$totTaxAmt, 'per'=>$tax->tax_percent);
                }    
            }
           
            $objPO = new PurchaseOrder();
            $poArr = $objPO->getPoCodeById($productArr[0]->po_no);
            $inwardCode = isset($productArr[0]->le_code) ? $productArr[0]->le_code : '';
            parent::Title('PDF GRN '.$inwardCode);
           
            $po_date = (isset($poArr->po_date) ? date('d-m-Y', strtotime($poArr->po_date)) : '');
            $leId = isset($productArr[0]->legal_entity_id) ? $productArr[0]->legal_entity_id : 0;
            $leWhId = isset($productArr[0]->le_wh_id) ? $productArr[0]->le_wh_id : 0;
			$billingAddress = $this->_grnModel->getBillingAddress($leWhId);


            // getting billing address according to legal entity id
            $grnDeatil = $this->_inwardModel->getInwardDetail($grnId);
            $leId = isset($grnDeatil->legal_entity_id)?$grnDeatil->legal_entity_id:0;
            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);

            if($leParentId)
                $billingAddress = $this->_LegalEntity->getLegalEntityById($leParentId);
            else
                $billingAddress = $this->_LegalEntity->getLegalEntityById($billingAddress->legal_entity_id);

            $leParentId = $this->_LegalEntity->getLeParentIdByLeId($leId);
            $leInfo = $this->_LegalEntity->getLegalEntityById($leParentId);
            if($leParentId)
                $leInfo = $this->_LegalEntity->getLegalEntityById($leParentId);
            else
                $leInfo = $this->_LegalEntity->getLegalEntityById($billingAddress->legal_entity_id);
            $userInfo = $this->_LegalEntity->getUserByLegalEntityId($leId);
            $whInfo = $this->_LegalEntity->getWarehouseById($leWhId);
            $companyInfo = $this->_LegalEntity->getCompanyAccountByLeId($leParentId);
			$deliveryGTIN = $this->_grnModel->getDeliveryGtin($grnId);
            
            // checking supplier is global supplier le id = 24766
            $wh_le_id = isset($whInfo->legal_entity_id)?$whInfo->legal_entity_id:0;
            if($leId == 24766){
                $objPO = new PurchaseOrder();
                $apob_data = $objPO->getApobData($wh_le_id);
                if(count($apob_data)){
                    foreach($apob_data as $key => $value){
                        $productArr[0]->$key = $value;
                        unset($apob_data->$key);
                    }
                }
            }
            
            // displaying blling  address as per company gst registered address in that state
            $wh_state = isset($whInfo->state)?$whInfo->state:0;
            $check_apob = $this->_LegalEntity->checkisApob($leWhId);
            if($wh_state > 0 && $check_apob){
                $wh_state_data = $this->_LegalEntity->getStateBillingDC($wh_state);
                if(count($wh_state_data)){
                    $billingAddress = $this->_LegalEntity->getWarehouseById($wh_state_data->le_wh_id);
                }
            }
            
            $data = array('grnProductArr'=>$productArr, 'leInfo'=>$leInfo, 
                        'taxSummArr'=>$taxSummArr, 'userInfo'=>$userInfo, 'whInfo'=>$whInfo,
                        'po_date'=>$po_date,'deliveryGTIN' => $deliveryGTIN,
						'billingAddress' => $billingAddress,
                        'companyInfo'=>$companyInfo, 
                        'po_code'=>(isset($poArr->po_code) ? $poArr->po_code : ''));
            // return view('Grn::pdf', $data);
            $pdf = PDF::loadView('Grn::pdf', $data);
            //Log::info('complete pdf');
            return $pdf->download('grn_'.$grnId.'.pdf');
            
        }
        catch(Exception $e) {
            
        }
    }
    
    public function getSkus()
    {
        try{
            $data = \Input::all();
            $skus = $this->_grnModel->getSkus($data);
            return $skus;die;
        }
        catch(\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    
    public function getSuppliers()
    {
        try
        {
            $data = Input::get();
            //print_r($data);die;
            $isDocumentRequired = 1;
            $response['supplierList'] = [];
            $poId = $data['po_id'];
            $po_approval_status = 0;
            $legal_entity_id = \Session::get('legal_entity_id');
            $supOptions='';
            $warehouseOptions='';
            
            if (!($poId > 0)) {
                $supOptions = '<option value="0">Select Supplier</option>';
                $warehouseOptions = '<option value="0">Select Delivery Location</option>';
            }
            $supdata = $this->_grnModel->getPOSupplierProductList($poId);
            $poDiscountData = $this->_grnModel->getPODiscountDetails($poId);
            $isdiscount_before_tax = isset($poDiscountData['discount_before_tax'])?$poDiscountData['discount_before_tax']:0;
            $pobill_discount = isset($poDiscountData['discount'])?$poDiscountData['discount']:0;
            $pobill_discount_type = isset($poDiscountData['discount_type'])?$poDiscountData['discount_type']:0;
            $supplierList=$supdata['supplierList'];
            $warehouseList=$supdata['warehouseList'];
            $products=$supdata['products'];
            foreach($supplierList as $supplier){
                $supOptions .= '<option value='.$supplier->legal_entity_id.'>'.$supplier->business_legal_name.'</option>';
                $isDocumentRequired = $this->_grnModel->allowDocuments($supplier->legal_entity_id);
            }            
            foreach($warehouseList as $warehouse){
                $warehouseOptions .= '<option value='.$warehouse->le_wh_id.'>'.$warehouse->lp_wh_name.'</option>';
            }
            $productList='';
            $sr = 1;
            
            $products=json_decode(json_encode($products),true);
            //print_r($products);die;
            
            $total_qty = 0;
            $total_taxper = 0;
            $total_taxvalue = 0;
            $grand_total = 0;
            $base_total = 0;
            foreach($products as $productInfo){
                $getDate  = $this->_grnModel->getMfgDate($poId,$productInfo['product_id']);
                $getDate  = date('m/d/Y',strtotime($getDate));
                $taxArr = $this->_grnModel->getProductTaxClass($productInfo['product_id'],4033,4033);
                $packUOMInfo = $this->_grnModel->getProductPackUOMInfo($productInfo['product_id'],$productInfo['uom']);
                $uomName = isset($packUOMInfo->uomName) ? $packUOMInfo->uomName:'';
                $no_of_eaches=isset($productInfo['no_of_eaches']) ? $productInfo['no_of_eaches'] : 0;
                $po_quantity = isset($productInfo['po_quantity']) ? $productInfo['po_quantity'] : 0;
                $po_approval_status = isset($productInfo['approval_status']) ? $productInfo['approval_status'] : 0;
                $actual_po_quantity = isset($productInfo['actual_po_quantity']) ? $productInfo['actual_po_quantity'] : 0;
                $grn_received_qty = isset($productInfo['received_qty']) ? $productInfo['received_qty'] : 0;
                
                $unitPrice = $productInfo['unit_price'];
                $isTaxInclude = $productInfo['is_tax_included'];
                if ($isTaxInclude == 1)
                {
                    $unitPrice = ($unitPrice / (1 + ($productInfo['tax_per'] / 100)));
                }
                
//                $tax_per = isset($taxArr[0]['Tax Percentage']) ? $taxArr[0]['Tax Percentage'] : 0;
                $tax_per = isset($productInfo['tax_per']) ? $productInfo['tax_per'] : 0;
//                $tax_type = isset($taxArr[0]['Tax Type']) ? $taxArr[0]['Tax Type'] : '';
                $tax_type = isset($productInfo['tax_name']) ? $productInfo['tax_name'] : '';
//                $tax_value = ($unitPrice * $tax_per) / 100;
                $tax_value = 0.00;
//                $total = $unitPrice + $tax_value;
                $total = 0.00;
                $product_id = $productInfo['product_id'];
                $product_name = $productInfo['product_name'];
                $prodname = (strlen($product_name)>40)?substr($product_name,0,40).'...':$product_name;
                
                $product_apply_discount = isset($productInfo['apply_discount']) ? $productInfo['apply_discount'] : 0;
                $product_discount_type = isset($productInfo['discount_type']) ? $productInfo['discount_type'] : 0;
                $product_discount = isset($productInfo['discount']) ? $productInfo['discount'] : 0;
                
                $product_packs = $this->_grnModel->getProductPackInfo($product_id);
                $packUOM = '';
                $selected = "";
                foreach($product_packs as $pack){
                    if($pack->packname == $uomName){
                        $selected = "selected";
                    }else{
                        $selected = "";
                    }
                    $packUOM .='<option value="'.$pack->level.'" data-noofeach="'.$pack->no_of_eaches.'" '.$selected.'>'.$pack->packname.'('.$pack->no_of_eaches.')</option>';
                }
                $default_no_eaches = isset($product_packs[0]->no_of_eaches) ? $product_packs[0]->no_of_eaches : 1;
                $poRemainingQty = ($actual_po_quantity - $grn_received_qty);
                $unitDiscountPrice = round($product_discount/$poRemainingQty, 5);
                $productList .= '<tr style="white-space:nowrap !important;">
                                <td align="center"><a class="grnItem" href="javascript:void(0);" id="'.$product_id.'"><i class="fa fa-caret-right"></i></a></td>
                                <td align="center">'.$sr.'</td>
                                <td align="center">'.$productInfo['sku'].'</td>
                                <td align="left" title="' . $product_name . '">' . $prodname . '</td>
                                <td align="center">'.$productInfo['mrp'].'</td>
                                <td align="center">'.$productInfo['qty'].' '.$uomName.' ('.$no_of_eaches.' Eaches) <br /> Remaining PO Qty : '.$poRemainingQty.' 
                                    <input id="remaining_packpo_qty_'.$product_id.'" readonly="" class="form-control" value="'.($actual_po_quantity - $grn_received_qty).'" type="hidden">
                                    <input name="packpo_qty" readonly="" class="form-control" value="'.$productInfo['qty'].'" type="hidden">
                                    <input name="packpo_numof_eaches" readonly="" class="form-control" value="'.$no_of_eaches.'" type="hidden">
                                    <input name="po_numof_eaches['.$product_id.']" readonly="" class="form-control" value="'.$poRemainingQty.'" type="hidden">
                                </td> 
                                <td align="left"><span id="received'.$product_id.'">0</span>
                                    <input name="grn_received[]" id="grn_received_'.$product_id.'" type="hidden" value="0">
                                    <input id="total_grn_free_'.$product_id.'" type="hidden" value="0">
                                    <input id="product_kvi_'.$product_id.'" type="hidden" value="'.$productInfo['kvi'].'">
                                    <input id="total_grn_damaged_'.$product_id.'" type="hidden" value="0">
                                    <input name="grn_totreceived[]" class="packtotrec" type="hidden" value="0">
                                </td>
                                <td align="left">
                                <span><input name="rqty" class="form-control inpusmwidth" type="number" min="0" value="'.$productInfo['qty'].'">
                                <select name="pack_size" id="pack_uom_qty_'.$product_id.'" class="form-control inpusmwidth">'.$packUOM.'</select>
                                    <input readonly="" disabled="" class="form-control uomqty" value="'.$default_no_eaches.'" type="hidden">
                                    <input readonly="" disabled="" class="form-control qtytotal" value="0" type="hidden">
                                    </span>
                                </td>
                                <td align="left">
                                    <div class="input-icon right">
                                       <i class="fa fa-calendar"></i>

                                        <input class="form-control mfg_date" aria-invalid="false" type="text" value="'.$getDate.'">

                                    </div>
                                </td>
                                <td align="center"><input class="form-control grn_free inpusmwidth" type="number" min="0" value="0"></td>
                                <td align="center"><input class="form-control grn_damaged inpusmwidth" type="number" min="0" value="0"></td>
                                <td align="center" class="hide"><input name="grn_excess[]" class="form-control inpusmwidth grn_excess" type="number" min="0" value="0"></td>
                                <td align="center" class="hide"><input name="grn_missed[]" class="form-control inpusmwidth grn_missed" type="number" min="0" value="0"></td>                                
                                <td align="center" class="hide"><input name="grn_quarantine[]" class="form-control inpusmwidth grn_quarantine" type="number" min="0" value="0"></td>
                                <td align="center"><textarea id="grn_remarks'.$product_id.'" name="grn_remarks[]" style="width:60px;" rows="1" id=""></textarea></td>
                                <td align="center"><input id="baseprice'.$product_id.'" name="grn_base_price[]" class="form-control" max="' . $productInfo['mrp'] . '" style="width: 79px;" type="number" min="0" value="' . $unitPrice . '"></td>
                                <td align="center"><span id="subTotal'.$product_id.'">0</span></td>
                                <td align="center">'.$tax_type.'@'.(float)$tax_per.'%</td>
                                <td align="center"><span id="taxtext'.$product_id.'">'.number_format($tax_value,2).'</span></td>
                                <td align="center">';
                if($isdiscount_before_tax){
                    $incltaxcheck = '';
                    $taxtypecheck = ($pobill_discount_type)?'checked="true"':'';
                    $discapply = ($pobill_discount_type)?$pobill_discount:$product_discount;
                }else{
                    $incltaxcheck = 'checked="true"';
                    $taxtypecheck = ($product_discount_type)?'checked="true"':'';
                    $discapply = $product_discount;
                }
                if($product_apply_discount)
                {
                    if($product_discount_type)
                    {
                        $productList .= '<span style="float:left; width:20px;"><input type="checkbox" name="grn_discount_type[]" id="discounttype'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'" '.$taxtypecheck.'>%</span><br/>';
                    }else{
                        $productList .= '<span style="float:left; width:20px;"><input type="checkbox" name="grn_discount_type[]" id="discounttype'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'">%</span><br/>'
                                . '<input type="hidden" id="unit_discount_'.$product_id.'" value="'.$unitDiscountPrice.'" />
								<input type="hidden" id="po_discount_amount_'.$product_id.'" value="'.$discapply.'" />';
                    }
                    $productList .= '<span style="float:left;"><input type="checkbox" name="grn_discount_inc_tax[]" id="discount_tax_type'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'" '.$incltaxcheck.'>INC TAX</span>
                                    <input name="grn_discount_percent[]" class="form-control" id="discountpercent'.$product_id.'" data-product-id="'.$product_id.'" type="number" min="0" value="'.$product_discount.'" style="float:left;width:75px;">';
                }else{
                    $productList .= '<span  style="float:left; width:20px;"><input type="checkbox" name="grn_discount_type[]" id="discounttype'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'" '.$taxtypecheck.'>%</span><br/>
                                    <span style="float:left;"><input type="checkbox" name="grn_discount_inc_tax[]" id="discount_tax_type'.$product_id.'" data-product-id="'.$product_id.'" value="'.$product_id.'" '.$incltaxcheck.'>INC TAX</span>
                                    <input name="grn_discount_percent[]" class="form-control" id="discountpercent'.$product_id.'" data-product-id="'.$product_id.'" type="number" min="0" value="'.$discapply.'" style="float:left;width:75px;">';
                }
                $productList .= '<input name="grn_discount_amount[]" class="form-control" id="discountamount'.$product_id.'" data-product-id="'.$product_id.'" type="hidden" value="0" />
                                </td>
                                <td align="left">
                                    <span id="discount'.$product_id.'">0</span>
                                    <input name="grn_discount[]" id="discountval'.$product_id.'" min="0" type="hidden" value="0">
                                </td>
                                <td align="center" id="total_amt'.$product_id.'"><span id="totalval'.$product_id.'">'.number_format($total,2).'</span></td>
                            <td align="center" class="fixTable" style="background:#fbfcfd !important;">
                                    <a class="fa fa-plus addpackbtn" data-id="'.$product_id.'" data-poqty="'.$productInfo['qty'].'" data-poqtyuom="'.$uomName.'" data-ponumeaches="'.$no_of_eaches.'"></a>
                                    <a class="fa fa-trash-o delete_product" data-id="'.$product_id.'"></a>                                    
                                    <span>
                                        <input name="grn_product_id[]" checked="checked" id="" type="hidden" value="'.$product_id.'">
                                        <input name="grn_sku[]" checked="checked" id="" type="hidden" value="'.$productInfo['sku'].'">
                                        <input name="grn_upc[]" type="hidden" value="'.$productInfo['upc'].'">
                                        <input name="grn_title[]" type="hidden" value="'.$productInfo['product_name'].'">
                                        <input name="grn_pack_size[]" type="hidden" value="'.$productInfo['pack_size'].'">
                                        <input name="grn_mrp[]" type="hidden" value="'.$productInfo['mrp'].'">
                                        <input name="grn_po_qty[]" type="hidden" value="'.$productInfo['qty'].'">
                                        <input name="grn_taxtype[]" type="hidden" value="'.$tax_type.'">
                                        <input id="taxper'.$product_id.'" name="grn_taxper[]" type="hidden" value="'.$tax_per.'">
                                        <input name="grn_taxvalue[]" id="taxval'.$product_id.'" type="hidden" value="'.$tax_value.'">
                                        <input name="grn_total[]" id="grntotalval'.$product_id.'" min="0" type="hidden" value="'.$total.'">
                                    </span>
                                    <span class="productPackdata'.$product_id.'">
                                    </span>
                                </td>
							</tr>
                            <tr style="display:none;" id="packinfo-'.$product_id.'"><td colspan="13"  class="sub_table">
                            <table class="table table-striped" id="packconfiglist-'.$product_id.'">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="font-size:10px;"><strong>Pack Size</strong></th>
                                    <th style="font-size:10px;"><strong>QTY</strong></th>
                                    <th style="font-size:10px;"><strong>Received</strong></th>
                                    <th style="font-size:10px;"><strong>Tot.Rec.Qty</strong></th>
                                    <th style="font-size:10px;"><strong>MFG Date</strong></th>
                                    <th style="font-size:10px;"><strong>Freshness</strong></th>
                                    <th style="font-size:10px;"><strong>Free</strong></th>
                                    <th style="font-size:10px;"><strong>Damaged</strong></th>
                                    <th style="font-size:10px;"><strong>Status</strong></th>
                                    <th style="font-size:10px;"><strong>Remarks</strong></th>
                                    <th></th>
                            </tr></thead>
                            <tbody></tbody>
                            </table></td></tr>';
                $sr++;
                
                $total_qty+=$productInfo['qty'];
                $total_taxper+=$tax_per;
                $total_taxvalue+=$tax_value;
                $base_total=($base_total+$total-$tax_value);
                $grand_total+=$total;
            }
             $response['supplierList'] = $supOptions;
             $response['po_discount_data'] = $poDiscountData;
             $response['sno'] = $sr;
             $response['warehouseList'] = $warehouseOptions;
             $response['productList'] = $productList;
             $response['po_approval_status'] = $po_approval_status;
            $response['is_documnet_required'] = $isDocumentRequired;
             $response['calculation'] = array('totqty'=>$total_qty,'tottaxper'=>$total_taxper,'tottaxval'=>$total_taxvalue,'basetot'=>$base_total,'grandtot'=>$grand_total);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }

    public function createPackInputText()
    {
        try
        {
            $data = Input::get();
            $product_id = $data['packproduct_id'];
            $inputList = '';
            $grn_received = (int)$data['total_received'];
            $grn_free = (int)$data['total_free'];
            $grn_damaged = (int)$data['total_damage'];
            $grn_missed=(int)$data['total_missed'];
            $grn_excess=(int)$data['total_excess'];
            $grn_quarantine = (int)$data['total_quarantine'];
            $discount_type = (int)$data['discount_type'];
            $discount = $data['total_discount'];
            
            $packtotrec = $grn_received+$grn_free+$grn_damaged+$grn_missed+$grn_excess+$grn_quarantine;
            $inputList .= '<input name="grn_received[]" type="hidden" value="'.$grn_received.'">
                            <input name="grn_totreceived[]" class="packtotrec" type="hidden" value="'.$packtotrec.'">
                            <input name="grn_free[]" type="hidden" value="'.$grn_free.'">
                            <input name="grn_damaged[]" type="hidden" value="'.$grn_damaged.'">
                            <input name="grn_missed[]" type="hidden" value="'.$grn_missed.'">
                            <input name="grn_excess[]" type="hidden" value="'.$grn_excess.'">
                            <input name="grn_quarantine[]" type="hidden" value="'.$grn_quarantine.'">';
            foreach($data['packsize_id'] as $key=>$packsize){
                $inputList .= '<input name="grn_packsize['.$product_id.'][]" min="0" id="" type="hidden" value="'.$packsize.'">
                            <input name="grn_eachesqty['.$product_id.'][]" type="hidden" value="'.$data['eachesqty'][$key].'">
                            <input name="grn_receivedqty['.$product_id.'][]" type="hidden" value="'.$data['receivedqty'][$key].'">
                            <input name="grn_receivedtotal['.$product_id.'][]" type="hidden" value="'.$data['receivedtotal'][$key].'">
                            <input name="grn_freshness_percentage['.$product_id.'][]" type="hidden" value="'.$data['freshness_percentage'][$key].'">
                            <input name="grn_pkmfg_date['.$product_id.'][]" type="hidden" value="'.$data['pkmfg_date'][$key].'">
                            <input name="grn_pkexp_date['.$product_id.'][]" type="hidden" value="'.$data['pkexp_date'][$key].'">
                            <input name="grn_pack_status['.$product_id.'][]" type="hidden" value="'.$data['pack_status'][$key].'">
                            <input name="grn_pack_remarks['.$product_id.'][]" type="hidden" value="'.$data['pack_remarks'][$key].'">';
            }
             $response['inputList'] = $inputList;
             $response['received_data'] = array(
                                    'product_id'=>$product_id,
                                    'received'=>$grn_received,
                                    'free'=>$grn_free,
                                    'damage'=>$grn_damaged,
                                    'missed'=>$grn_missed,
                                    'excess'=>$grn_excess,
                                    'quarantine'=>$grn_quarantine,
                                    'total_discount'=>$discount,
                                    'discount_type'=>$discount_type,
                                    'pr_remarks'=>$data['pr_remarks'],
                                   );
           } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
        return $response;
    }
	
    public function creatPurchaseVoucher($inwardId) {
        try {
            $prProducts = $this->_inwardModel->getInwardDetailsById($inwardId);
            $prTotal = 0;
            $tax_total = 0;
            $baseAmt = 0;
            $voucher = array();
            $zeroTaxEntry = 0;
            $zeroTaxValue = 0;
            $is_asset = 0;
            $ZeroTaxElement = 0;
            $nonZeroTax = 0;
            $crs = 0;
            $drs = 0;
            if (is_array($prProducts) && count($prProducts) > 0) {
                if (isset($prProducts[0])) {
                    $grnProducts = $prProducts;
                    $grnTotal = $grnProducts[0]->grand_total;
                    $grnShipping = $grnProducts[0]->shipping_fee;
                    $discountAmount = $grnProducts[0]->discount_on_total;
                    $discount_before_tax = $grnProducts[0]->discount_before_tax;
                    $stock_transfer_dc = $grnProducts[0]->stock_transfer_dc;
                    $is_stock_transfer = $grnProducts[0]->is_stock_transfer;
                    $cost = $this->_gdsBus->getBusinesUnitLeWhId($grnProducts[0]->le_wh_id);
                    $parent_buId = isset($cost->parent_bu_id) ? $cost->parent_bu_id : 0;
                    $costcenter = isset($cost->cost_center) ? $cost->cost_center : 'Z1R1D1';
                    $cg = $this->_gdsBus->getBusinesUnitByParentId($parent_buId);
                    $costcenter_grp = isset($cg->cost_center) ? $cg->cost_center : 'Z1R1';
                    if($is_stock_transfer == 1 && $stock_transfer_dc !="" && $stock_transfer_dc !=NULL){
                        $transfer_dc_info = $this->_gdsBus->getBusinesUnitLeWhId($stock_transfer_dc);
                        $sup_state_id = isset($transfer_dc_info->state) ? $transfer_dc_info->state : 0;
                        $del_state_id = isset($cost->state) ? $cost->state : 0;
                        if($del_state_id == $sup_state_id){
                            // Vocuher entries not need for apob to apob within same state
                            return "Voucher entries not needed for stock trasnfer within same state.";
                        }
                    }
                    $grnTotal = (($grnTotal) - $grnShipping);
                    if ($grnTotal > 0) {
                        $voucher_code = $grnProducts[0]->inward_id;
                        $voucher_type = 'Purchase';
                        $reference = $grnProducts[0]->inward_code;
                        $reference_docs = ($grnProducts[0]->reference_docs != NULL || $grnProducts[0]->reference_docs != "")? $grnProducts[0]->reference_docs : $reference;
                        $reference_arr = explode(',', $reference_docs);

                        if(count($reference_arr)>0){
                            $ref_uniq = array_unique($reference_arr);
                            $reference_docs = trim(implode(',', $ref_uniq));
                        }
                        $le_code = $grnProducts[0]->le_code;
                        $supplier = $grnProducts[0]->business_legal_name;
                        $poCode = $grnProducts[0]->po_code;
                        $poDate = $grnProducts[0]->po_created_date;
                        $grnDate = $grnProducts[0]->created_at;
                        $invoice_date = $grnProducts[0]->invoice_date;
                        $grnCode = $grnProducts[0]->inward_code;
                        $poInvoiceCode = $grnProducts[0]->invoice_code;
                        $inwardCreatedDate = $grnDate;
                        $poInvoiceCreatedDate = $grnProducts[0]->po_invoice_created_at;
                        
                        $totCGST = 0;
                        $totSGST = 0;
                        $totIGST = 0;
                        $totUTGST = 0;
                        $baseAmounts=[];
                        $taxAmounts=[];
                        foreach ($prProducts as $product) {
                            $productTypeId = $product->product_type_id;
                            if ($productTypeId == 130001 && !$is_asset) {
                                $is_asset = 1;
                            }
                            $taxper = abs($product->tax_per);
                            if(isset($baseAmounts[$taxper])){
                                $baseAmounts[$taxper] = $baseAmounts[$taxper]+$product->sub_total;
                            }else{
                                $baseAmounts[$taxper] = $product->sub_total;
                            }
                            //$tax_total += $product->tax_amount;
                            //$baseAmt += $product->sub_total;

                            $discountAmount = $discountAmount + $product->discount_total;
                            //$tax_name = (isset($product->tax_type) && $tax_name!='')?$product->tax_type:$tax_name;
                            $tax_data = json_decode($product->tax_data, true);
                            $tax_name = isset($tax_data[0]['Tax Type']) ? $tax_data[0]['Tax Type'] : '';
                            $totCGST = (isset($tax_data[0]['CGST_VALUE']) ? $tax_data[0]['CGST_VALUE'] : 0);
                            $totSGST = (isset($tax_data[0]['SGST_VALUE']) ? $tax_data[0]['SGST_VALUE'] : 0);
                            $totIGST = (isset($tax_data[0]['IGST_VALUE']) ? $tax_data[0]['IGST_VALUE'] : 0);
                            $totUTGST = (isset($tax_data[0]['UTGST_VALUE']) ? $tax_data[0]['UTGST_VALUE'] : 0);

                            //CGST
                            if($totCGST>0 || ($taxper==0 && $tax_name=='GST')){
                                if(isset($taxAmounts['CGST'][$taxper])){
                                    $taxAmounts['CGST'][$taxper] = $taxAmounts['CGST'][$taxper]+$totCGST;
                                }else{
                                    $taxAmounts['CGST'][$taxper] = $totCGST;
                                }
                            }
                            //SGST
                            if($totSGST>0 || ($taxper==0 && $tax_name=='GST')){
                                if(isset($taxAmounts['SGST'][$taxper])){
                                    $taxAmounts['SGST'][$taxper] = $taxAmounts['SGST'][$taxper]+$totSGST;
                                }else{
                                    $taxAmounts['SGST'][$taxper] = $totSGST;
                                }
                            }
                            //IGST
                            if($totIGST>0 || ($taxper==0 && $tax_name=='IGST')){
                                if(isset($taxAmounts['IGST'][$taxper])){
                                    $taxAmounts['IGST'][$taxper] = $taxAmounts['IGST'][$taxper]+$totIGST;
                                }else{
                                    $taxAmounts['IGST'][$taxper] = $totIGST;
                                }
                            }
                            //UTGST
                            if($totUTGST>0 || ($taxper==0 && $tax_name=='UTGST')){
                                if(isset($taxAmounts['UTGST'][$taxper])){
                                    $taxAmounts['UTGST'][$taxper] = $taxAmounts['UTGST'][$taxper]+$totUTGST;
                                }else{
                                    $taxAmounts['UTGST'][$taxper] = $totUTGST;
                                }
                            }
                        }
                        $intrastate = ['GST', 'SGST', 'CGST','UTGST'];
                        $interstate = ['IGST'];
                        $objPO = new PurchaseOrder();
                        $tallyLedgers = $this->_grnModel->getMasterLookupByCatId('Tally Ledgers GST');
                        $purchaseAccountName = '';
                        $ledger_groupName = '';

                        if (in_array($tax_name, $intrastate)) {
                            $purchaseAccountName = $tallyLedgers['142006']->name;
                            $ledger_groupName = $tallyLedgers['142006']->description;
                        } else if (in_array($tax_name, $interstate)) {
                            $purchaseAccountName = $tallyLedgers['142007']->name;
                            $ledger_groupName = $tallyLedgers['142007']->description;
                        }
                        if ($is_asset) {
                            $ledger_groupName = $tallyLedgers['142014']->description;
                        }
                        $taxLookup = [];
                        if (in_array($tax_name, $intrastate)) {
                            $taxLookup = ['SGST' => 142008, 'CGST' => 142010 ,'UTGST'=>142017];
                            $taxbr=2;
                        } else if (in_array($tax_name, $interstate)) {
                            $taxLookup = ['IGST' => 142009];
                            $taxbr=1;
                        }


                        if($discount_before_tax==1){
                            $totAmnt = round($grnTotal,2);
                        }else{
                            $totAmnt = round($grnTotal + $discountAmount,2);
                        }
                        
                        /*$actAmount = round(($totAmnt,2);
                        $amount = round($totAmnt);
                        if($totAmnt==round($totAmnt)){
                            $roundOffAmnt=0;
                        }else{
                            $roundOffAmnt=round(($actAmount-$amount),2);
                        }*/
                        $crs = $crs + $totAmnt;
                        $voucher[] = array('voucher_code' => $reference_docs,
                            'voucher_type' => $voucher_type,
                            'voucher_date' => $inwardCreatedDate,
                            'ledger_group' => 'Sundry Creditors',
                            'ledger_account' => trim($supplier) . ' - ' . $le_code,
                            'tran_type' => 'Cr',
                            'amount' => $totAmnt,
                            'naration' => 'Being the purchase made from ' . $supplier
                            . ' PO No. ' . $poCode . ' dated ' . $poDate . ' with GRN no. '
                            . $grnCode . ' dated ' . $grnDate . ' and '
                            . 'purchase invoice no ' . $poInvoiceCode . ' dated ' . $poInvoiceCreatedDate,
                            'cost_centre' => $costcenter,
                            'cost_centre_group' => $costcenter_grp,
                            'reference_no' => $reference,
                            'is_posted' => 0,
                        );
                        if(count($baseAmounts)>0){
                            foreach($baseAmounts as $tax=>$baseVal){
                                $drs = $drs + round($baseVal,2);
                                $voucher[] = array('voucher_code' => $reference_docs,
                                  'voucher_type'=>$voucher_type,
                                  'voucher_date' => $inwardCreatedDate,
                                  'ledger_group'=>$ledger_groupName,
                                  'ledger_account' => $purchaseAccountName.'@'.$tax.'%',
                                  'tran_type'=>'Dr',
                                  'amount' => round($baseVal, 2),
                                  'naration'=>'',
                                  'cost_centre'=>$costcenter,
                                  'cost_centre_group'=>$costcenter_grp,
                                  'reference_no'=>$reference,
                                  'is_posted'=>0,
                                );
                            }
                        }
                        
                        if(count($taxAmounts)>0){
                            foreach($taxAmounts as $tax_type=>$taxArr){
                                $taxlookupval = isset($taxLookup[$tax_type])?$taxLookup[$tax_type]:"";
                                $inputAccountName = isset($tallyLedgers[$taxlookupval]->name)?$tallyLedgers[$taxlookupval]->name:"";
                                $taxgroup = isset($tallyLedgers[$taxlookupval]->description)?$tallyLedgers[$taxlookupval]->description:"";
                                foreach($taxArr as $tax=>$taxAmnt){
                                    if($taxAmnt>0){
                                        $drs = $drs + round($taxAmnt,2);
                                        $voucher[] = array('voucher_code' => $reference_docs,
                                            'voucher_type' => $voucher_type,
                                            'voucher_date' => $inwardCreatedDate,
                                            'ledger_group' => $taxgroup,
                                            'ledger_account' => $inputAccountName.'@'.($tax/$taxbr).'%',
                                            'tran_type' => 'Dr',
                                            'amount' => round($taxAmnt,2),
                                            'naration' => '',
                                            'cost_centre' => $costcenter,
                                            'cost_centre_group' => $costcenter_grp,
                                            'reference_no' => $reference,
                                            'is_posted' => 0,
                                        );
                                    }
                                }
                            }
                        } else {
                            $mesg = 'GRN Products do not have GST tax.';
                            Log::info($mesg);
                            return $mesg;
                        }
                        //echo '['.$crs.'==='.$drs;
                        $roundAmount = round($crs - $drs,2);
                        //echo '+++'.$roundAmount;die;
                        if($roundAmount>0){
                            $tracationType = 'Dr';   
                            $drs +=$roundAmount; 
                        }else{
                            $tracationType = 'Cr';
                            $crs +=$roundAmount;
                        }
                        Log::info('voucher crs='.$crs.'--Drs='.$drs);
                        Log::info('voucher round'.$roundAmount);
                        if (abs($roundAmount) > 0 && abs($roundAmount) < 1) {
                            $purchaseAccountName2 = $tallyLedgers['142011']->name;
                            $ledger_groupName2 = $tallyLedgers['142011']->description;
                            $voucher[] = array('voucher_code' => $reference_docs,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $inwardCreatedDate,
                                'ledger_group' => $ledger_groupName2,
                                'ledger_account' => $purchaseAccountName2,
                                'tran_type' => $tracationType,
                                'amount' => round(abs($roundAmount), 2),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                        } elseif (abs($roundAmount) > 1) {
                            $this->_inwardModel->sendErrorVoucherEntryMail($grnCode, $voucher);
                            $voucher = [];
                        }
                        if (!empty($voucher) && $discountAmount > 0 && $discount_before_tax==0) {
                            $ledgeGroupName = $tallyLedgers['142015']->name;
                            $ledgeName = $tallyLedgers['142015']->description;
                            $voucher[] = array('voucher_code' => $reference_docs,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $inwardCreatedDate,
                                'ledger_group' => 'Sundry Creditors',
                                'ledger_account' => trim($supplier) . ' - ' . $le_code,
                                'tran_type' => 'Dr',
                                'amount' => abs($discountAmount),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                            $voucher[] = array('voucher_code' => $reference_docs,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $inwardCreatedDate,
                                'ledger_group' => $ledgeGroupName,
                                'ledger_account' => $ledgeName,
                                'tran_type' => 'Cr',
                                'amount' => abs($discountAmount),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                        }
                        $diff = round($crs,2) - round($drs,2);
                        if ($diff != 0) {
                            $this->_inwardModel->sendErrorVoucherEntryMail($grnCode, $voucher);
                        }
                    }
                    // echo "<pre>";print_r($voucher);die;
                    //Log::info($voucher);
                    if (!empty($voucher)) {
                        $this->_inwardModel->saveVoucher($voucher);
                    }
                    $mesg = 'Voucher Created Successfully for GRN ' . $inwardId;
                }
            } else {
                $mesg = 'GRN Products details not found';
            }
            //Log::info($mesg);
            return $mesg;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    public function creatPurchaseVoucherOld($inwardId)
    {
        try
        {
            $grnProducts= $this->_inwardModel->getInwardDetailsById($inwardId);
            $grnTotal = 0;
            $taxArr = array();
            $baseAmtArr = array();
            $voucher=array();
            $zeroTaxEntry = 0;
            if(is_array($grnProducts) && count($grnProducts)>0){
               if(isset($grnProducts[0])){                   
                    $grnTotal = round($grnProducts[0]->grand_total);
                    $grnShipping = $grnProducts[0]->shipping_fee;
                    $discountAmount = $grnProducts[0]->discount_on_total;
                    
                    $cost = $this->_gdsBus->getBusinesUnitLeWhId($grnProducts[0]->le_wh_id);
                    $parent_buId = isset($cost->parent_bu_id)?$cost->parent_bu_id:0;
                    $costcenter = isset($cost->cost_center)?$cost->cost_center:'Z1R1D1';
                    $cg = $this->_gdsBus->getBusinesUnitByParentId($parent_buId);
                    $costcenter_grp = isset($cg->cost_center)?$cg->cost_center:'Z1R1';
                    foreach ($grnProducts as $product)
                    {
                        $taxper = $product->tax_per;
                        if($taxper == 0)
                        {
                            $zeroTaxEntry = 1;
                        }
                        $discountAmount = $discountAmount + $product->discount_total;
                        if ($product->tax_amount > 0)
                        {
                            $zeroTaxEntry = 0;
                            if (isset($taxArr[$taxper]))
                            {
                             $taxArr[$taxper] += $product->tax_amount;
                            } else
                            {
                            $taxArr[$taxper] = $product->tax_amount;
                        }                        
                            if (isset($baseAmtArr[$taxper]))
                            {
                             $baseAmtArr[$taxper] += $product->sub_total;
                            } else
                            {
                            $baseAmtArr[$taxper] = $product->sub_total;
                            }
                        }
                    }
                    $grnTotal = (($grnTotal) - $grnShipping);
                    if($grnTotal>0){
                        $voucher_code = $grnProducts[0]->inward_id;
                        $voucher_type = 'Purchase';
                        $reference = $grnProducts[0]->inward_code;
                        $le_code = $grnProducts[0]->le_code;
                        $supplier = $grnProducts[0]->business_legal_name;
                        $poCode = $grnProducts[0]->po_code;
                        $poDate = $grnProducts[0]->po_created_date;
                        $grnDate = $grnProducts[0]->created_at;
                        $grnCode = $grnProducts[0]->inward_code;
                        $poInvoiceCode = $grnProducts[0]->invoice_code;
                        $inwardCreatedDate = $grnProducts[0]->created_at;
                        $poInvoiceCreatedDate = $grnProducts[0]->po_invoice_created_at;
                        $voucher[] = array('voucher_code'=>$reference,
                                          'voucher_type'=>$voucher_type,
                                          'voucher_date'=>$inwardCreatedDate,
                                          'ledger_group'=>'Sundry Creditors',
                                          'ledger_account'=> trim($supplier).' - '.$le_code,
                                          'tran_type'=>'Cr',
                                          'amount'=>($grnTotal + $discountAmount),
                                          'naration'=>'Being the purchase made from '.$supplier
                            . ' PO No. '.$poCode.' dated '.$poDate.' with GRN no. '
                            . $grnCode.' dated '.$grnDate.' and '
                            . 'purchase invoice no '.$poInvoiceCode.' dated '.$poInvoiceCreatedDate,
                                          'cost_centre'=>$costcenter,
                                          'cost_centre_group'=>$costcenter_grp,
                                          'reference_no'=>$reference,
                                          'is_posted'=>0,
                                     );                        
                        $drTotals = 0;
                        foreach($baseAmtArr as $taxPer=>$base){
                            $taxPer1=(float) $taxPer;
                            $acc = '';
                            if($taxPer=='14.5'){
                                $acc = 501100;
                                $taxPer1=$taxPer1;
                            }else if($taxPer=='5'){
                                $acc = 501200;
                                $taxPer1='@'.$taxPer1;
                            }                            
                            if ($taxPer > 0)
                            {
                                $purchaseAccountName = $acc . ' : Purchase ' . $taxPer1;
                                $inputAccountName = 'Input TS VAT ' . $taxPer1.'%';
                                $purchaseAccountDetails = $this->_inwardModel->getLedgerName('Purchase', $taxPer1, $acc);
                                if(!empty($purchaseAccountDetails))
                                {
                                $purchaseAccountName = property_exists($purchaseAccountDetails, 'tlm_name') ? $purchaseAccountDetails->tlm_name : $acc . ' : Purchase @ ' . $taxPer1;
                                }
                                $inputAccountDetails = $this->_inwardModel->getLedgerName('Input', $taxPer1);
                                if(!empty($inputAccountDetails))
                                {
                                $inputAccountName = property_exists($inputAccountDetails, 'tlm_name') ? $inputAccountDetails->tlm_name : 'Input TS VAT ' . $taxPer1.'%';
                                }
                                $drTotals = $drTotals + (round($base, 2) + round($taxArr[$taxPer], 2));
                                $voucher[] = array('voucher_code' => $reference,
                                          'voucher_type'=>$voucher_type,
                                    'voucher_date' => $inwardCreatedDate,
                                          'ledger_group'=>'Purchase Accounts',
//                                    'ledger_account' => $acc . ' : Purchase @ ' . $taxPer1,
                                    'ledger_account' => $purchaseAccountName,
                                          'tran_type'=>'Dr',
                                    'amount' => round($base, 2),
                                          'naration'=>'',
                                          'cost_centre'=>$costcenter,
                                          'cost_centre_group'=>$costcenter_grp,
                                          'reference_no'=>$reference,
                                          'is_posted'=>0,
                                     );
                                $voucher[] = array('voucher_code' => $reference,
                                          'voucher_type'=>$voucher_type,
                                    'voucher_date' => $inwardCreatedDate,
                                          'ledger_group'=>'Duties & Taxes',
                                    'ledger_account' => $inputAccountName,
                                          'tran_type'=>'Dr',
                                    'amount' => round($taxArr[$taxPer], 2),
                                          'naration'=>'',
                                          'cost_centre'=>$costcenter,
                                          'cost_centre_group'=>$costcenter_grp,
                                          'reference_no'=>$reference,
                                          'is_posted'=>0,
                                     );
                        }
                        }                        
                                                

                        $drTotals = (($drTotals) - $discountAmount);
                        $drTotals = (str_replace(',', '', number_format($drTotals, 2, '.', '')));
                        $grnTotal = (str_replace(',', '', number_format($grnTotal, 2, '.', '')));
                        $roundAmount = 0;
                        if($drTotals > $grnTotal)
                        {
                            $roundAmount = ($drTotals - $grnTotal);
                        }else{
                            $roundAmount = ($grnTotal - $drTotals);
                        }
                        if(abs($roundAmount) > 0 && abs($roundAmount) < 1)
                        {
                            $roundAmount1 = ($drTotals - $grnTotal);
                            if($roundAmount1 > 0)
                            {
                                $tracationType = 'Cr';
                            }elseif($roundAmount1 < 0)
                            {
                                $tracationType = 'Dr';
                            }
                            $voucher[] = array('voucher_code' => $reference,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $inwardCreatedDate,
                                'ledger_group' => '710000 : General Admin Expenses',
                                'ledger_account' => '711900 : Round off',
                                'tran_type' => $tracationType,
                                'amount' => round(abs($roundAmount),2),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                        }elseif($roundAmount != 0){
                            $ledgeGroupName = 'Purchase Accounts';
                            $ledgeName = '501300 : Purchase @0%';
                            $ledgeGroupNameDetails = $this->_inwardModel->getLedgerGroupName($ledgeName);
                            if(!empty($ledgeGroupNameDetails))
                            {
                                $ledgeGroupName = property_exists($ledgeGroupNameDetails, 'tlm_group') ? $ledgeGroupNameDetails->tlm_group : 'Indirect Incomes';
                            }
                            if($zeroTaxEntry)
                            {
                                $voucher[] = array('voucher_code' => $reference,
                                    'voucher_type' => $voucher_type,
                                    'voucher_date' => $inwardCreatedDate,
                                    'ledger_group' => $ledgeGroupName,
                                    'ledger_account' => $ledgeName,
                                    'tran_type' => 'Dr',
                                    'amount' => ($grnTotal + $discountAmount),
                                    'naration' => '',
                                    'cost_centre' => $costcenter,
                                    'cost_centre_group' => $costcenter_grp,
                                    'reference_no' => $reference,
                                    'is_posted' => 0,
                                );
                            }else{
                                $this->_inwardModel->sendErrorVoucherEntryMail($grnCode, $voucher);
                                $voucher = [];
                            }
                        }
                        if(!empty($voucher) && $discountAmount > 0)
                        {
                            $ledgeGroupName = 'Indirect Incomes';
                            $ledgeName = 'Discount Receivables';
                            $ledgeGroupNameDetails = $this->_inwardModel->getLedgerGroupName($ledgeName);
                            if(!empty($ledgeGroupNameDetails))
                            {
                                $ledgeGroupName = property_exists($ledgeGroupNameDetails, 'tlm_group') ? $ledgeGroupNameDetails->tlm_group : 'Indirect Incomes';
                            }
                            $voucher[] = array('voucher_code' => $reference,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $inwardCreatedDate,
                                'ledger_group'=>'Sundry Creditors',
                                'ledger_account'=> trim($supplier).' - '.$le_code,
                                'tran_type' => 'Dr',
                                'amount' => abs($discountAmount),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                            $voucher[] = array('voucher_code' => $reference,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $inwardCreatedDate,
                                'ledger_group' => $ledgeGroupName,
                                'ledger_account' => $ledgeName,
                                'tran_type' => 'Cr',
                                'amount' => abs($discountAmount),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                        }
                        
                    }
                    //Log::info($voucher);
                    if(!empty($voucher))
                    {
                        $this->_inwardModel->saveVoucher($voucher);
                    }
                    $mesg = 'Voucher Created Successfully for GRN '.$inwardId;
               }
           }else{
               $mesg = 'GRN details not found';
           }
           return $mesg;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }
    /*
     * createPurchaseReturnVoucher() method is used to make purchase return voucher entry
     * @param int
     * @return String
     */
    public function createPurchaseReturnVoucher($prId)
    {

        try
        {
            $returnModel = new ReturnModel();
            $prProducts= $returnModel->getReturnDetailById($prId);
            $prTotal = 0;
            $tax_total = 0;
            $baseAmt = 0;
            $voucher=array();
            $crs = 0;
            $drs = 0;
            if(is_array($prProducts) && count($prProducts)>0){
               if(isset($prProducts[0])){
                    $prTotal = $prProducts[0]->pr_grand_total;
                    $cost = $this->_gdsBus->getBusinesUnitLeWhId($prProducts[0]->le_wh_id);
                    $parent_buId = isset($cost->parent_bu_id)?$cost->parent_bu_id:0;
                    $costcenter = isset($cost->cost_center)?$cost->cost_center:'Z1R1D1';
                    $cg = $this->_gdsBus->getBusinesUnitByParentId($parent_buId);
                    $costcenter_grp = isset($cg->cost_center)?$cg->cost_center:'Z1R1';
                    
                    $actAmount = round((floor(($prTotal)*1000)/1000),2);
                    $amount = round($prTotal,2);
                    $roundOffAmnt=0;
                    $drs = $drs + $amount;
                        
                    if($prTotal>0){
                        $voucher_type = 'Debit Note';
                        $reference = $prProducts[0]->pr_code;
                        $pr_code = $prProducts[0]->pr_code;
                        $prCreatedDate = $prProducts[0]->created_at;
                        $le_code = $prProducts[0]->le_code;
                        $supplier = $prProducts[0]->business_legal_name;
                        $grnDate = $prProducts[0]->inward_date;
                        $grnCode = $prProducts[0]->inward_code;                        
                        $voucher[] = array('voucher_code'=>$reference,
                                          'voucher_type'=>$voucher_type,
                                          'voucher_date'=>$prCreatedDate,
                                          'ledger_group'=>'Sundry Creditors',
                                          'ledger_account'=> trim($supplier).' - '.$le_code,
                                          'tran_type'=>'Dr',
                                          'amount'=>$amount,
                                          'naration'=>'Being the purchase return material send to '.$supplier
                            . ' Against GRN No. '.$grnCode.' dated '.$grnDate.' with PR No. '
                            . $pr_code.' dated '.$prCreatedDate,
                                          'cost_centre'=>$costcenter,
                                          'cost_centre_group'=>$costcenter_grp,
                                          'reference_no'=>$reference,
                                          'is_posted'=>0,
                                     );
                
                        $tax_name = '';
                        $totCGST = $totSGST = $totIGST = $totUTGST = 0;
                        $baseAmounts=[];
                        $taxAmounts=[];
                        
                        foreach ($prProducts as $product)
                        {
                            $taxper = abs($product->tax_per);
                            $tax_total = $product->tax_total;
                            if($product->is_tax_included==1){
                                $baseAmt = $product->sub_total/((100+$taxper)/100);
                            }else{
                                $baseAmt = $product->sub_total;
                            }
                            if(isset($baseAmounts[$taxper])){
                                $baseAmounts[$taxper] = $baseAmounts[$taxper]+$baseAmt;
                            }else{
                                $baseAmounts[$taxper] = $baseAmt;
                            }

                            $tax_name = (isset($product->tax_type) && $tax_name=='')?$product->tax_type:$tax_name;
                            $tax_data = json_decode($product->tax_data, true);
                            //foreach ($tax_data as $key => $val) {
                            $cgst_val = round(($product->tax_total * (isset($tax_data[0]['CGST']) ? $tax_data[0]['CGST'] : 0)) / 100,2);
                            $sgst_val = round(($product->tax_total * (isset($tax_data[0]['SGST']) ? $tax_data[0]['SGST'] : 0)) / 100,2);
                            $igst_val = round(($product->tax_total * (isset($tax_data[0]['IGST']) ? $tax_data[0]['IGST'] : 0)) / 100,2);
                            $utgst_val = round(($product->tax_total * (isset($tax_data[0]['UTGST']) ? $tax_data[0]['UTGST'] : 0)) / 100,2);
                            // }
                            //CGST
                            if($cgst_val>0){
                                if(isset($taxAmounts['CGST'][$taxper])){
                                    $taxAmounts['CGST'][$taxper] = $taxAmounts['CGST'][$taxper]+$cgst_val;
                                }else{
                                    $taxAmounts['CGST'][$taxper] = $cgst_val;
                                }
                            }
                            //SGST
                            if($sgst_val>0){
                                if(isset($taxAmounts['SGST'][$taxper])){
                                    $taxAmounts['SGST'][$taxper] = $taxAmounts['SGST'][$taxper]+$sgst_val;
                                }else{
                                    $taxAmounts['SGST'][$taxper] = $sgst_val;
                                }
                            }
                            //IGST
                            if($igst_val>0){
                                if(isset($taxAmounts['IGST'][$taxper])){
                                    $taxAmounts['IGST'][$taxper] = $taxAmounts['IGST'][$taxper]+$igst_val;
                                }else{
                                    $taxAmounts['IGST'][$taxper] = $igst_val;
                                }
                            }
                            //UTGST
                            if($utgst_val>0){
                                if(isset($taxAmounts['UTGST'][$taxper])){
                                    $taxAmounts['UTGST'][$taxper] = $taxAmounts['UTGST'][$taxper]+$utgst_val;
                                }else{
                                    $taxAmounts['UTGST'][$taxper] = $utgst_val;
                                }
                            }

                        }
                        
                        $intrastate = ['GST','SGST','CGST','UTGST'];
                        $interstate = ['IGST'];
                        $objPO = new PurchaseOrder();
                        $tallyLedgers = $objPO->getMasterLookupByCatId('Tally Ledgers GST');
                        $purchaseAccountName = '';
                        $ledger_groupName = '';
                        
                        if(in_array($tax_name, $intrastate)){
                            $purchaseAccountName = $tallyLedgers['142006']->name;
                            $ledger_groupName = $tallyLedgers['142006']->description;
                        }else if(in_array($tax_name, $interstate)){
                            $purchaseAccountName = $tallyLedgers['142007']->name;
                            $ledger_groupName = $tallyLedgers['142007']->description;
                        }
                        $taxLookup=[];
                        if(in_array($tax_name, $intrastate)){
                            $taxLookup = ['SGST'=>142008,'CGST'=>142010,'UTGST'=>142018];
                            $taxbr=2;
                        }else if(in_array($tax_name, $interstate)){
                            $taxLookup = ['IGST'=>142009];
                            $taxbr=1;
                        }
                        if(count($baseAmounts)>0){
                            foreach($baseAmounts as $tax=>$baseVal){
                                $crs += round($baseVal, 2);
                                $voucher[] = array('voucher_code' => $reference,
                                  'voucher_type'=>$voucher_type,
                                  'voucher_date' => $prCreatedDate,
                                  'ledger_group'=>$ledger_groupName,
                                  'ledger_account' => $purchaseAccountName.'@'.$tax.'%',
                                  'tran_type'=>'Cr',
                                  'amount' => round($baseVal, 2),
                                  'naration'=>'',
                                  'cost_centre'=>$costcenter,
                                  'cost_centre_group'=>$costcenter_grp,
                                  'reference_no'=>$reference,
                                  'is_posted'=>0,
                                );
                            }
                        }
                        if(count($taxAmounts)>0){
                            foreach($taxAmounts as $tax_type=>$taxArr){
                                $taxlookupval = isset($taxLookup[$tax_type])?$taxLookup[$tax_type]:"";
                                $inputAccountName = isset($tallyLedgers[$taxlookupval]->name)?$tallyLedgers[$taxlookupval]->name:"";
                                foreach($taxArr as $tax=>$taxAmnt){
                                    $crs += round($taxAmnt, 2);
                                    $voucher[] = array('voucher_code' => $reference,
                                        'voucher_type'=>$voucher_type,
                                        'voucher_date' => $prCreatedDate,
                                        'ledger_group'=>$ledger_groupName,
                                        'ledger_account' => $inputAccountName.'@'.($tax/$taxbr).'%',
                                        'tran_type'=>'Cr',
                                        'amount' => round($taxAmnt,2),
                                        'naration'=>'',
                                        'cost_centre'=>$costcenter,
                                        'cost_centre_group'=>$costcenter_grp,
                                        'reference_no'=>$reference,
                                        'is_posted'=>0,
                                );
                                }
                            }
                        }
                        
                        $roundOffAmnt = $crs - $drs;
                        if($roundOffAmnt>0){
                            $tracationType = 'Dr';   
                            $drs +=$roundOffAmnt; 
                        }else{
                            $tracationType = 'Cr';
                            $crs +=$roundOffAmnt;
                        }
                        if(abs($roundOffAmnt) > 0 && abs($roundOffAmnt) < 1)
                        {
                            $roundoffAccountName2 = $tallyLedgers['142011']->name;
                            $ledger_groupName2 = $tallyLedgers['142011']->description;
                            $voucher[] = array('voucher_code' => $reference,
                                'voucher_type' => $voucher_type,
                                'voucher_date' => $prCreatedDate,
                                'ledger_group' => $ledger_groupName2,
                                'ledger_account' => $roundoffAccountName2,
                                'tran_type' => $tracationType,
                                'amount' => round(abs($roundOffAmnt),2),
                                'naration' => '',
                                'cost_centre' => $costcenter,
                                'cost_centre_group' => $costcenter_grp,
                                'reference_no' => $reference,
                                'is_posted' => 0,
                            );
                            
                        }
                    }
                    // echo '<pre/>';print_r($voucher);die();
                    if(!empty($voucher))
                    {
                        $this->_inwardModel->saveVoucher($voucher);
                    }
                    $mesg = 'Voucher Created Successfully for Purchase Returns '.$prId;
               }
           }else{
               $mesg = 'PR details not found';
           }
           return $mesg;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    
    public function generateVouchers($date)
    {
        try
        {
            echo $this->_inwardModel->createVoucher($date);
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
    }
    
    public function getPoApprovalHistory($poId)
    {
        try
        {
            $objPO = new PurchaseOrder();
            $approvalHistory = $objPO->getApprovalHistory('Purchase Order', $poId);
//            echo "<pre>";print_R($approvalHistory);die;
            return view('PurchaseOrder::Form.poapprovalHistory', ['history' => $approvalHistory]);
//            $contents = $view->render();
//            echo "<pre>";print_R(htmlentities($contents));die;
//            return $view;
        } catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
    }
	
	public function creatPurchaseVoucherByDate($fromDate, $toDate)
	{
		try{
			if($fromDate != 0 && $toDate != 0)
			{
				$inwardList = DB::table('inward')
					->leftJoin('po_invoice_grid as poig', 'poig.inward_id', '=', 'inward.inward_id')
					->whereBetween('inward.created_at', [$fromDate, $toDate])
					->where('inward.approval_status', 1)
					->pluck('inward_id');
				if(!empty($inwardList))
				{
					foreach($inwardList as $inwardId)
					{
						$this->creatPurchaseVoucher($inwardId);
					}
				}
			}
		} catch (\ErrorException $ex) {
            \Log::info($ex->getMessage());
            \Log::info($ex->getTraceAsString());
        }
	}



    public function deliverPOSOOrder($orderId,$po_id,$returns=array()){
        try{

            $this->_InvoiceModel = new Invoice();
            $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($orderId), array('grid.gds_invoice_grid_id','grid.grand_total','grid.ecash_applied'));
            $objPO = new PurchaseOrder();
            $poDetailArr = $objPO->getPoDetailById($po_id);
            $legal_entity_id =$poDetailArr[0]->legal_entity_id;
            $deliveryData = $this->_grnModel->getUserByLegalEntityId($legal_entity_id);
            $userId = \Session::get('userId');
            if(count($deliveryData)>0){
                $userId = $deliveryData->user_id;
                $token = $deliveryData->password_token;
            }else{
                $token = $objPO->getTokenByUserId($userId);
            }
            if(!isset($invoiceInfo[0]->gds_invoice_grid_id)){
                $response = array('status' => 400, 'message' => 'Order not yet Invoiced!');
                return json_encode($response);
            }

            $OrderModel = new \App\Modules\Orders\Models\OrderModel;
            $order_data = $OrderModel->getOrderInfo(array($orderId),array('order_status_id'));
            $deliver_array = array('17007','17022','17023','17008');
            if(isset($order_data[0]->order_status_id) && in_array($order_data[0]->order_status_id,$deliver_array)){
                $response = array('status' => 200, 'message' => 'Order Already Delivered!');
                return json_encode($response);
            }

            $return_total = array_sum(array_column($returns, "return_total"));
            $invoiceId = $invoiceInfo[0]->gds_invoice_grid_id;
            $ecash_applied = $invoiceInfo[0]->ecash_applied;
            $invoiceAmt = $invoiceInfo[0]->grand_total;//$this->_InvoiceModel->getInvoicedPriceWithOrderID($orderId);
            $checkEcash = ($invoiceAmt - $return_total) - $ecash_applied;
            $le_wh_id = $poDetailArr[0]->le_wh_id;
            $contact_data = $objPO->getLEWHById($le_wh_id);
            $cust_legal_entity_id = $contact_data->legal_entity_id;
            $credit_limit_check = $contact_data->credit_limit_check;
            $checkLOC = $objPO->checkLOCByLeID($cust_legal_entity_id);
            $checkDeliveryLoc = $checkLOC - $invoiceAmt;
            // if(floor($checkEcash) > 0 || $checkDeliveryLoc < 0){
            if($credit_limit_check == 1){
                if(floor($checkEcash) > 0 ){
                    $response = array('status' => 400, 'message' => 'Insufficient wallet balance to deliver the order!');
                    return json_encode($response);
                }
            }
            $url = env('DELIVER_URL');
            $data = [
                'flag' => 2,
                'deliver_token' => $token,
                'module_id' => 1,
                'user_id' => $userId,
                'order_id' => $orderId,
                'invoice_id' => $invoiceId,
                'net_amount' => $invoiceAmt - $return_total,
                'amount' => $invoiceAmt - $return_total,
                'amount_collected' => 0,
                'amount_credit' => 0,
                'collectable_amt' => 0,
                'amount_return' => $return_total,
                'payment_mode' => 22010,
                'reference_no' => '--NA--',
                'round_of_value' => $invoiceAmt - round($invoiceAmt,2),
                'discount_applied' => 0,
                'discount_deducted' => 0,
                'ecash_applied' => $ecash_applied - $return_total,
                'returns' => $returns,
                'payments' => [],
            ];
            //$post_feild = ["data"=>json_encode($data)];
            //$headers = array("cache-control: no-cache","content-type: multipart/form-data");
            //$response = Utility::sendcUrlRequest($url, $post_feild, $headers,0);
            $pickObj = new pickController();
//            Log::info("Grn entering into getInvoiceByReturn Function");
            $response = $pickObj->getInvoiceByReturn($data);
  //          Log::info("Grn entered into getInvoiceByReturn Function");
    //        Log::info($response);
            $response = json_decode($response);
            if($response->status == "success"){
                $orderData = $this->_grnModel->getOrderByOrderId($orderId);
                $collections = $this->_grnModel->collectionDetailsById($orderId);
                $le_wh_id = $orderData->le_wh_id;
                $hub_id = $orderData->hub_id;
                $colremArray = array("collected_amt"=>$invoiceAmt,
                                "remittance_code"=>$collections->collection_code,
                                "acknowledged_by"=>$userId,
                                "hub_id"=>$hub_id,
                                "le_wh_id"=>$le_wh_id,
                                "by_ecash"=>$invoiceAmt,
                                "submitted_at"=>date("Y-m-d h:i:s"),
                                "submitted_by"=>$userId,
                                "acknowledged_at"=>date("Y-m-d h:i:s"),
                                "approval_status"=>57052);
                // entering collection remittance history
                $collectionRemittanceMappingId = $this->_grnModel->collectionRemittanceMapping($colremArray);
                $remArray = array("collection_id"=>$collections->collection_id,
                                "remittance_id"=>$collectionRemittanceMappingId);
                //entering remittance mapping
                $remittanceMappingId = $this->_grnModel->remittanceMapping($remArray);
                $response = array('status' => 200, 'message' => 'Your Order has been delivered successfully.'); //message - for mobile,Message-for api logs
            }
            return json_encode($response);
            //$MongoApiLogsModel->updateResponse($mongoInsertId, $response, $orderId);
        }catch(Exception $ex){
           // Log::info("Error on delivering po order");
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return 0;
        }
    }


    public function SaveReferenceNo(Request $request){

      $data=Input::all();

      $updatereferenceno=$this->_grnModel->SaveReferenceNo($data);

      if($updatereferenceno){
        echo 'Saved Successfully';
      }else{
        echo 'Failed to Save';
      }

    }

    public function createPoByData($inward_id,$po_id,$supplier_id,$le_wh_id){
        Log::info("child po create");
        $grnToDCPO = $this->_roleRepo->checkPermissionByFeatureCode('GRNTDCPO001');
        if($grnToDCPO == false){
            return false;
        }
        $objPO = new PurchaseOrder();

        $poArr = $objPO->getPoById($po_id);
        $poArr = json_decode(json_encode($poArr),true);
        $parent_po_code = $poArr['po_code'];
        Log::info('checkChildPoExist out');
        $childPOexist = $objPO->checkChildPoExist($po_id,$parent_po_code);
        if($childPOexist>0){
            return false;
        }
        Log::info('checkChildPoExist');
       // Log::info('inward_id in createPoByData'.$inward_id);
        $supplier_data = $this->_LegalEntity->getWarehouseById($supplier_id);
       // Log::info("supplier_id".$supplier_id);
        $legal_entity_id = 24766;//Ebutor MP LEID should be dynamic --isset($supplier_data->legal_entity_id)?$supplier_data->legal_entity_id:2; 
        $remove = ['po_id','po_code','created_at','updated_by','updated_at','approved_by','approved_at'];
        $poArr = array_diff_key($poArr, array_flip($remove));
        //echo '<pre/>';
        $CustomerRepo = new CustomerRepo();
        // $state_id = $CustomerRepo->getSateIdByDcId($le_wh_id);
        // $state_code = $CustomerRepo->getSateCodeById($state_id);
        $reciever_data = $this->_LegalEntity->getWarehouseById($le_wh_id);
        $state_code = isset($reciever_data->state_code)?$reciever_data->state_code:"TS";
        $serialNumber = Utility::getReferenceCode("PO",$state_code);
        $poArr['legal_entity_id']=$legal_entity_id;
        $poArr['parent_id']=$po_id;
        $poArr['le_wh_id']=$le_wh_id;
        $poArr['po_code']=$serialNumber;
        $poArr['supply_le_wh_id']="";
        $poArr['apply_discount_on_bill'] = 1;
        $poArr['discount_type'] = 1;
        $poArr['discount_before_tax'] = 1;
        $poArr['approval_status'] = 57107;
        $poArr['po_so_status'] = 0;
        $poArr['po_so_order_code'] = "";
        $poArr['discount'] = $reciever_data->margin;
        $poArr['po_status'] = 87001;
        $poArr['created_by']=\Session::get('userId');
        $newpoId = $objPO->savePo($poArr);

        $saveProducts=[];

        $products = $this->_grnModel->getGrnProductDetails($inward_id);
        $customer_type = $objPO->getStockistPriceGroup($reciever_data->legal_entity_id,$le_wh_id);
        // $customer_type = 1016;
        //$contact_data = $objPO->getLEWHById($le_wh_id);
        $mobile_number = isset($reciever_data->phone_no)?$reciever_data->phone_no:"";
        $customer_data = $objPO->getCustomerDataByNo($mobile_number);
        $user_id = $customer_data->user_id;
        $poData = array();
        $poData['product_ids'] = array();
        $objPOCt = new PurchaseOrderController();
        foreach($products as $product){
            $product = (array)$product;
            $total_qty =  $product['received_qty'];
            $ordered_qty =  $product['no_of_eaches'];
            $qty = $total_qty / $ordered_qty;
            $uom = $product['uom'];
            if(is_int($qty)){
                $qty = $qty;
                $no_of_eaches = $ordered_qty;
            }else{
                $qty = $product['received_qty'];
                $uom = 16001;
                $no_of_eaches = 1;
            }
            if($uom == 16001){
                $qty = $product['received_qty'];
                $no_of_eaches = 1;
            }

            $is_asset = 0;
            $product_id = $product['product_id'];
            $po_product = array();
            $po_product['product_id'] = $product_id;
            $checkFreebiee = $objPO->getFreebieParent($product_id);
            $parent_id = isset($checkFreebiee->main_prd_id)?$checkFreebiee->main_prd_id:$product_id;
            $po_product['parent_id'] = $parent_id;
            $po_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
            $po_product['qty'] = $qty;
            $po_product['uom'] = $uom;
            $po_product['no_of_eaches'] = $no_of_eaches;
            $po_product['free_qty'] = $product['free_qty'];
            $po_product['free_uom'] = $product['free_uom'];
            $po_product['free_eaches'] = $product['free_eaches'];
            $po_product['sku'] = $product['sku'];
            $po_product['is_tax_included'] = 1;
            // blocking discount for child po from parent po
            // $po_product['apply_discount'] = $product['apply_discount'];
            // $po_product['discount_type'] = $product['discount_type'];
            // $po_product['discount'] = $product['discount'];

            $packSizeArr = array();
            $appKeyData = env('DB_DATABASE');
            $keyString = $appKeyData . '_product_slab_' . $product_id . '_customer_type_' . $customer_type.'_le_wh_id_'.$supplier_id;
            $temp = trim($supplier_id, "'");
            $temp = str_replace(',', '_', $temp);
            if ($user_id == 0) {
                $temp = 0;
            }
            $availQty = $objPO->checkInventory($product_id,$supplier_id);
            $response = Cache::get($keyString);
           // Log::info('keyString ---'.$keyString);
            // Log::info('response ---'.$response);
            $unitPriceData = ($response != '') ? (json_decode($response, true)) : [];
            if (isset($unitPriceData[$temp]) && count($unitPriceData[$temp])) {
                $CheckUnitPrice = $unitPriceData[$temp];
                $tempDetails = [];
                if (isset($availQty)) {
                    foreach ($CheckUnitPrice as $slabData) {
                        if (isset($slabData['stock'])) {
                            $slabData['stock'] = $availQty;
                        }
                        $tempDetails[] = $slabData;
                    }
                }
                if (!empty($tempDetails)) {
                    $CheckUnitPrice = $tempDetails;
                }
                $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                Cache::put($keyString, json_encode($unitPriceData), 60);
            } else {
                $CheckUnitPrice = DB::selectFromWriteConnection(DB::raw("CALL getProductSlabsByCust($product_id,'" . $supplier_id . "',$user_id,$customer_type)"));
                // Log::info("CALL getProductSlabsByCust($product_id,'" . $supplier_id . "',$user_id,$customer_type)");
                // Log::info('CheckUnitPrice -----'.json_encode($CheckUnitPrice));
                $unitPriceData[$temp] = json_decode(json_encode($CheckUnitPrice), true);
                if(count($CheckUnitPrice))
                    Cache::put($keyString, json_encode($unitPriceData), 60);
            }
            foreach ($CheckUnitPrice as $price) {
                if (is_array($price)) {
                    //Log::info('This is array');
                    $packSizeArr[$price['pack_size']] = $price['unit_price'];
                } elseif (is_object($price)) {
                    //Log::info('This is object');
                    $packSizeArr[$price->pack_size] = $price->unit_price;
                }
            }
            $cartObj = new CartModel();
            $packSizePrice = $cartObj->getPackPrice($no_of_eaches, $packSizeArr);
            if($packSizePrice == ""){
                $packSizePrice = $product['cur_elp'];
            }
            $po_product['cur_elp'] = $packSizePrice - ($packSizePrice * $poArr['discount'] /100);
            $po_product['tax_name'] = $product['tax_name'];
            $po_product['tax_per'] = $product['tax_per'];
            $po_product['tax_data'] = $product['po_tax_data'];
            $po_product['hsn_code'] = $product['hsn'];
            $po_product['unit_price'] = $packSizePrice;
            $po_product['price'] = $po_product['no_of_eaches'] * $packSizePrice;
            $po_product['sub_total'] = $po_product['price'] * $po_product['qty'];
            $po_product['tax_amt'] = $po_product['sub_total'] - ($po_product['sub_total']/(100+$po_product['tax_per']) * 100);
            $po_product['tax_amt'] = number_format((float)$po_product['tax_amt'],5,'.','');
            $po_product['po_id'] = $newpoId;
            $saveProducts[] = $po_product;
            array_push($poData['product_ids'], array("product_id"=>$product_id,"qty"=>$po_product['qty']));

            // subscribing products
            $objPOCt->subscribeProducts($legal_entity_id,$le_wh_id,$product_id);
        }
        
        if(count($saveProducts)>0){
            $objPO->savePoProducts($saveProducts);
        }
        $poData['po_id'] = $newpoId;
        $poData['invoice_flag'] = 1;
        $poData['dc_id'] = $supplier_id;
        $poData['hub_id'] = $this->_grnModel->getHubIdByDcId($supplier_id);
        $poData['state_id'] = isset($reciever_data->state)?$reciever_data->state:4033;

        // po has been created and creationg po to so
        //Log::info("posorequest".json_encode($poData));
        $poso = $objPOCt->createSoByPoData($poData);
        Log::info("posoresponse".json_encode($poso));
        return $poso;

    }

    public function enableCp($inward_id){
        $products = $this->_grnModel->getGrnProductDetails($inward_id);
        $grnDeatil = $this->_inwardModel->getInwardDetail($inward_id);
        $le_wh_id = $grnDeatil->le_wh_id;
        $objPO = new PurchaseOrder();
        $prodObj = new ProductController();
        $date=date('Y-m-d H:i:s');
        foreach ($products as $key => $value) {
            $product_id = $value->product_id;
            $data = array("dcid"=>$le_wh_id,"productid"=>$product_id,"cpenable"=>1,"issellable"=>1);
            // checking both is_sellable and and cp_enabled in Products table
            $check_prop = $prodObj->checkProductProp($product_id,$le_wh_id);
            $checkFreebiee = $objPO->getFreebieParent($product_id);
            if(!isset($checkFreebiee->main_prd_id) && $check_prop){
                $is_sellable = $prodObj->isSellable($data);
                // if Successfully done
                if(stristr($is_sellable,"Successfully")){
                    $cp_enable = $prodObj->cpEnabled($data);
                    // if Successfully done                    
                    if(stristr($cp_enable,"Successfully") || stristr($cp_enable,"Already") || stristr($cp_enable,"Failed")){
                        $check_warehouseproductid=DB::table('product_cpenabled_dcfcwise')
                                             ->where(['product_id'=>$product_id,'le_wh_id'=>$le_wh_id])
                                               ->Where(function($query) 
                                                  {
                                                      $query->Where("esu",0)
                                                            ->orWhere("esu",NULL);
                                                  }
                                              )
                                             ->count();
                        if($check_warehouseproductid == 1){
                            $esu = $prodObj->getProductSU($product_id);
                            DB::table('product_cpenabled_dcfcwise')
                                ->where('le_wh_id',$le_wh_id)
                                ->where('product_id',$product_id)
                                ->update(['esu'=>$esu->esu,'updated_at'=>$date]);
                        }
                    }
                }
            }
        }
    }

    public function getInvoiceDate(Request $request){
        $supplierId = $request->input('supplierId');
        $SuppliertermsModel = new SuppliertermsModel();
        $credit_period = $SuppliertermsModel::where('legal_entity_id', $supplierId)->pluck('credit_period')->first();
        $invoice_date = Carbon::now()->format('m/d/Y');
        if($credit_period){
            $now = Carbon::now();
            $invoice_date = Carbon::now()->addDays($credit_period)->format('m/d/Y');
        }      
        return $invoice_date;
    }
}
