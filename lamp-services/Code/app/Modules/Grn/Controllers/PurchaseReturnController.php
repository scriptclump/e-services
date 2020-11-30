<?php

/*
 * Filename: PurchaseReturnController.php
 * Description: This file is used for manage purchase returns
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 22 Dec 2016
 * Modified date: 22 Dec 2016
 */

namespace App\Modules\Grn\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Response;
use Log;
use DB;
use Auth;
use Input;
use PDF;
use Illuminate\Support\Facades\Redirect;

use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Grn\Models\Inward;
use App\Modules\Indent\Models\LegalEntity;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\RoleRepo;
use Illuminate\Support\Facades\Route;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Grn\Models\ReturnModel;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\PurchaseReturn\Models\PurchaseReturn;
use Utility;
use Notifications;

class PurchaseReturnController extends BaseController {

    protected $_masterLookup;
    protected $_roleRepo;
    protected $_inwardModel;
    protected $_returnModel;
    protected $_LegalEntity;
    protected $_customerRepo;

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
            }
            return $next($request);
        });
        $this->_masterLookup = new MasterLookup();
        $this->_returnModel = new ReturnModel();
        $this->_inwardModel = new Inward();
        $this->_LegalEntity = new LegalEntity();
        $this->_customerRepo = new CustomerRepo();
        $this->_roleRepo = new RoleRepo();
    }

    public function createReturn($inward_id) {
        try {
            $grnProductArr = $this->_inwardModel->getInwardDetailById($inward_id);
            $leWhId = isset($grnProductArr[0]->le_wh_id) ? $grnProductArr[0]->le_wh_id : 0;
            $whInfo = $this->_LegalEntity->getWarehouseById($leWhId);
            $statusArr = $this->_masterLookup->getAllOrderStatus('PURCHASE RETURN');
            $reasonsArr = $this->_masterLookup->getAllOrderStatus('Purchase Return Reasons');
            $totalRecvedQty = 0;
            foreach ($grnProductArr as $product) {                
                $totalRecvedQty += $product->received_qty;            
            }
            $totalReturnQty = $this->_returnModel->getReturnQtyByInwardId($inward_id);
            return view('Grn::createReturn')
                            ->with('grnProductArr', $grnProductArr)
                            ->with('whInfo', $whInfo)
                            ->with('totalRecvedQty', $totalRecvedQty)
                            ->with('totalReturnQty', $totalReturnQty)
                            ->with('statusArr', $statusArr)
                            ->with('reasonsArr', $reasonsArr);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function saveReturn() {
        
        try {
            $data = Input::all();
            //print_r($data);die;
            DB::beginTransaction();
            $inward_id = (isset($data['inward_id'])) ? $data['inward_id'] : 0;
            $inwDetails = $this->_inwardModel->getInwardDetailById($inward_id);
            $warehouse = $inwDetails[0]->le_wh_id;
            $state_code = isset($inwDetails[0]->state_code)?$inwDetails[0]->state_code:"TS";
            $prCode = Utility::getReferenceCode("PR",$state_code);

            if ($inward_id != 0) {
                $purchase_returnArr = [
                    'pr_code' => $prCode,
                    'inward_id' => $inward_id,
                    'pr_status' => 103001,
                    'approval_status' => 57036,
                    'pr_grand_total' => 0,
                    'pr_total_qty' => 0,
                    'pr_remarks' => $data['return_comment'],
                    'created_by' => Session('userId'),
                    'le_wh_id' => $inwDetails[0]->le_wh_id,
                    'legal_entity_id' => $inwDetails[0]->legal_entity_id,
                ];
                $pr_id = $this->_returnModel->saveReturns($purchase_returnArr);
                
                $grand_total = 0;
                $totQty = 0;
                if ($pr_id > 0) {
                    if (isset($data['selected']) && is_array($data['selected'])) {
                        $return_productArr=[];
                        foreach ($data['product_id'] as $productId) {
                            $productInfo = $this->_inwardModel->getInwardProductById($inward_id, $productId);
                            $soh_qty = (isset($data['soh_qty']) && is_array($data['soh_qty'])) ? $data['soh_qty'][$productId] : 0;
                            $dit_qty = (isset($data['dit_qty']) && is_array($data['dit_qty'])) ? $data['dit_qty'][$productId] : 0;
                            $dnd_qty = (isset($data['dnd_qty']) && is_array($data['dnd_qty'])) ? $data['dnd_qty'][$productId] : 0;
                            $qty = ($soh_qty+$dit_qty+$dnd_qty);
                            $selected = (isset($data['selected']) && is_array($data['selected']) && isset($data['selected'][$productId])) ? $data['selected'][$productId] : 0;
                            if ($qty > 0 && $selected != 0) {
                                $unit_price = (isset($productInfo->price)) ? $productInfo->price : 0;
                                $tax_per = (isset($productInfo->tax_per)) ? $productInfo->tax_per : 0;
                                $sub_total = $unit_price * $qty;
                                $tax_amt = ($unit_price * $tax_per) / 100;
                                $tax_total = $tax_amt * $qty;
                                $total = $sub_total + $tax_total;
                                $grand_total += $total;
                                $totQty += $qty;
                                $return_productArr[] = [
                                    'pr_id' => $pr_id,
                                    'product_id' => $productId,
                                    'qty' => $soh_qty,
                                    'dit_qty' => $dit_qty,
                                    'dnd_qty' => $dnd_qty,
                                    'unit_price' => $unit_price,
                                    'mrp' => (isset($productInfo->mrp)) ? $productInfo->mrp : 0,
                                    'tax_type' => (isset($productInfo->tax_type)) ? $productInfo->tax_type : '',
                                    'tax_per' => $tax_per,
                                    'tax_amt' => $tax_amt,
                                    'uom' => '16001',
                                    'no_of_eaches' => '1',
                                    'price' => $unit_price,
                                    'sub_total' => $sub_total,
                                    'tax_total' => $tax_total,
                                    'tax_data' => (isset($productInfo->tax_data))?$productInfo->tax_data:'',
                                    'hsn_code' => (isset($productInfo->hsn_code))?$productInfo->hsn_code:'',
                                    'total' => $total,
                                    'reason' => (isset($data['return_reason']) && is_array($data['return_reason'])) ? $data['return_reason'][$productId] : '',
                                    'created_by' => Session('userId'),
                                ];
                            }else{
                                
                            }
                        }
                        if(is_array($return_productArr) && count($return_productArr)>0 && $grand_total>0 && $totQty>0){
                            $this->_returnModel->saveReturnProducts($return_productArr);
                            $arr = ['pr_grand_total'=>$grand_total,'pr_total_qty'=>$totQty];
                            $this->_returnModel->updateReturn($pr_id,$arr);
                            DB::commit();
                            /**
                            * default approval status
                            */
                            $approval_flow_func = new CommonApprovalFlowFunctionModel();
                            $created_by = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
                            $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Return', '57036', $created_by);
                            if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                                $current_status_id = $res_approval_flow_func["currentStatusId"];
                                $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                                $table = 'purchase_returns';
                                $unique_column = 'pr_id';
                                $prModel = new PurchaseReturn();
                                $prModel->updateStatusAWF($table, $unique_column, $pr_id, $next_status_id . ",0");
                                $appr_comment = 'System approval at the time of insertion';
                                $approval_flow_func->storeWorkFlowHistory('Purchase Return', $pr_id, $current_status_id, $next_status_id, $appr_comment, $created_by);
                            }
                            Notifications::addNotification(['note_code' => 'PR001','note_message'=>'PR #PRID Created Successfully', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['PRID' => $prCode], 'note_link' => '/pr/details/'.$pr_id]);
                            $mailData['subject'] = 'New PR#'.$prCode.' Created';
                            $mailData['message'] = '<p>New PR created!</p>';
                            $subject = $mailData['subject'];
                            app('App\Modules\PurchaseReturn\Controllers\PurchaseReturnController')->emailWithAttachment($pr_id,$prCode,$mailData);
                            return json_encode(['status' => 200, 'message' => 'Return Created Successfully', 'inward_id' => $inward_id,'pr_id' => $pr_id]);
                        }else{
                            DB::rollback();
                            return json_encode(['status' => 400, 'message' => 'Return Qty/Amount should not be zero.']);
                        }
                    }  else {
                        DB::rollback();
                        return json_encode(['status' => 400, 'message' => 'Please select at least one product']);
                    }
                }else{
                    DB::rollback();
                    return json_encode(['status' => 400, 'message' => 'Return Id should not be zero']);
                }
            } else {
                DB::rollback();
                return json_encode(['status' => 400, 'message' => 'Inward Id should not be empty']);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            DB::rollback();
            return json_encode(['status' => 400, 'message' => $e->getMessage() . ' => ' . $e->getTraceAsString()]);
        }
    }



}
