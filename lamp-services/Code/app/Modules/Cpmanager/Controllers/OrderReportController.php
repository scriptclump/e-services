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
use App\Modules\Cpmanager\Models\OrderModel;
use App\Modules\Cpmanager\Models\OrderReportModel;
use App\Modules\Cpmanager\Models\Orderhistory;
use App\Modules\Cpmanager\Views\order;
use App\Http\Controllers\BaseController;
use App\Modules\Cpmanager\Models\CategoryModel;
use App\Modules\Cpmanager\Models\AdminOrderModel;

class OrderReportController extends BaseController {

    public function __construct() {

        $this->order = new OrderModel();
        $this->order_report = new OrderReportModel();
        $this->orderhistory = new Orderhistory();
        $this->_category = new CategoryModel();
        $this->_admin=new AdminOrderModel();
    }

    public function getOrdersList() {
        try {
            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {
                 
                $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);
                $userData = $this->_category->getUserId($arr->admin_token);                 
                      
                if ($checkAdminToken > 0) {
                    if (isset($arr->fdate) && $arr->fdate!="") {
                        $fdate = (isset($arr->fdate) && !empty($arr->fdate)) ? $arr->fdate : date('Y-m') . '-01';
                        $fdate = date('Y-m-d 00:00:00', strtotime($fdate));
                        $tdate = (isset($arr->tdate) && !empty($arr->tdate)) ? $arr->tdate : date('Y-m-d');
                        $tdate = date('Y-m-d 23:59:59', strtotime($tdate));
                    } else {
                        $fdate = "";
                        $tdate = "";
                    }

                    $status = (isset($arr->status) && $arr->status != '') ? explode(',',$arr->status) : [];
                    $filter_status = (isset($arr->filter_status) && $arr->filter_status != '') ? $arr->filter_status : '';
                    $beat_id = (isset($arr->beat_id) && $arr->beat_id != '') ? explode(',',$arr->beat_id) : [];
                    $hub_id = (isset($arr->hub_id) && $arr->hub_id != '') ? explode(',',$arr->hub_id) : [];
                    $offset = (isset($arr->offset) && $arr->offset != '') ? $arr->offset : 0;
                    $perpage = (isset($arr->perpage) && $arr->perpage != '') ? $arr->perpage : 10;
                    $columns=[];
                    if(isset($arr->docket_no) && $arr->docket_no != ''){
                        $columns['docket_no'] = $arr->docket_no;
                    }
                    if(isset($arr->flag) && $arr->flag != ''){
                        $columns['flag'] = $arr->flag;
                    }
                    $columns['beat_id'] = $beat_id;
                    $columns['hub_id'] = $hub_id;
                    $columns['user_token'] = $arr->admin_token;
                    $columns['filter_status'] = $filter_status;
                    $orders = $this->order_report->getOrdersByStatus($status, $fdate, $tdate,$offset,$perpage,$columns,$userData[0]->user_id);
                    if (!empty($orders)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "getOrderlist",
                            'data' => $orders
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
                print_r(json_encode(array('status' => "failed", 'message' => "Pass user token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
    public function getPRAOrdersList() {
        try {
            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {
                
                $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);
                $userData = $this->_category->getUserId($arr->admin_token);                
                      
                if ($checkAdminToken > 0) {
                    if (isset($arr->fdate)) {
                        $fdate = (isset($arr->fdate) && !empty($arr->fdate)) ? $arr->fdate : date('Y-m') . '-01';
                        $fdate = date('Y-m-d 00:00:00', strtotime($fdate));
                        $tdate = (isset($arr->tdate) && !empty($arr->tdate)) ? $arr->tdate : date('Y-m-d');
                        $tdate = date('Y-m-d 23:59:59', strtotime($tdate));
                    } else {
                        $fdate = "";
                        $tdate = "";
                    }
                    $status = ['17022','17023'];
                    $beat_id = (isset($arr->beat_id) && $arr->beat_id != '') ? explode(',',$arr->beat_id) : [];
                    $hub_id = (isset($arr->hub_id) && $arr->hub_id != '') ? explode(',',$arr->hub_id) : [];
                    $columns['beat_id'] = $beat_id;
                    $columns['hub_id'] = $hub_id;
                    $columns['user_token'] = $arr->admin_token;
                    $offset = (isset($arr->offset) && $arr->offset != '') ? $arr->offset : 0;
                    $perpage = (isset($arr->perpage) && $arr->perpage != '') ? $arr->perpage : 10;
                    $orders = $this->order_report->getPRAOrders($status, $fdate, $tdate,$offset,$perpage,$columns,$userData[0]->user_id);
                    if (!empty($orders)) {
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "getPRAOrderlist",
                            'data' => $orders
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
                print_r(json_encode(array('status' => "failed", 'message' => "Pass user token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }
    public function getOrdersDetails() {
        try {
            $data = Input::all();            
            $arr = isset($data['data'])?json_decode($data['data']):array();
            $orderInfo = [];
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {
                
                $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);                 
                     
                if ($checkAdminToken > 0) {
                        $orderId = $arr->order_id; 
                        $orderInfo = $this->order_report->getOrderInfoByOrderId($orderId);
                        if (!empty($orderInfo)) {
                        $orderProductInfo = $this->order_report->getOrderProdcutInfoByOrderId($orderId);                        
                        $orderInfo->productInfo = $orderProductInfo;
                        $module = [];
                        $invoiceInfo = $this->order_report->getInvoiceByOrderId($orderId);
                        if(count($invoiceInfo)>0){
                            $key1='';
                            foreach ($invoiceInfo as $key=>$invoice) {
                                $key1 = ($key>0)?$key:'';
                                $module['InvoiceId'.$key1]=$invoice->InvoiceId;
                                $module['InvoiceDate'.$key1]=$invoice->InvoiceDate;
                                $module['InvoicedBy'.$key1]=$invoice->InvoicedBy;
                            }
                        }
                        $cancelInfo = $this->order_report->getCancelByOrderId($orderId);
                        if(count($cancelInfo)>0){
                            $key1='';
                            foreach ($cancelInfo as $key=>$cancel) {
                                $key1 = ($key>0)?$key:'';
                                $module['CancelId'.$key1]=$cancel->CancelId;
                                $module['CancelDate'.$key1]=$cancel->CancelDate;
                                $module['CancelledBy'.$key1]=$cancel->CancelledBy;
                            }
                        }
                        $returnInfo = $this->order_report->getReturnsByOrderId($orderId);
                        if(count($returnInfo)>0){
                            $key1='';
                            foreach ($returnInfo as $key=>$return) {
                                $key1 = ($key>0)?$key:'';
                                $module['ReturnId'.$key1]=$return->ReturnId;
                                $module['ReturnDate'.$key1]=$return->ReturnDate;
                                $module['ReturnedBy'.$key1]=$return->ReturnedBy;
                            }
                        }
                        $orderInfo->invoiceInfo = $module;                    
                        return json_encode(Array(
                            'status' => "success",
                            'message' => "getInvoiceOderlist",
                            'data' => $orderInfo
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
                print_r(json_encode(array('status' => "failed", 'message' => "Pass user token", 'data' => [])));
                die;
            }
        } catch (Exception $e) {
            return Array('status' => "failed", 'message' => "Internal server error", 'data' => []);
        }
    }

    public function commentHistoryAction() {
        try {
            $data = Input::all();
            $arr = isset($data['data']) ? json_decode($data['data']) : array();
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {
               
                $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);

                if ($checkAdminToken > 0) {
                    $orderId = $arr->order_id;
                    $commentArr = $this->order_report->getOrderCommentById($orderId);
                    if(is_array($commentArr) && count($commentArr)){
                        $data = $commentArr;
                        $message = 'orderHistory';
                        $status = 200;
                    }else{
                        $data = [];
                        $message = 'No data';
                        $status = 200;
                    }
                    return json_encode([
                        'status' => $status,
                        'message' => $message,
                        'data' => $data
                    ]);
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass user token", 'data' => [])));
                die;
            }
        } catch (ErrorException $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getStatusList() {
        try {
            $data = Input::all();
            $arr = isset($data['data']) ? json_decode($data['data']) : array();
            if (isset($arr->admin_token) && !empty($arr->admin_token)) {
               $checkAdminToken = $this->_category->checkCustomerToken($arr->admin_token);

                if ($checkAdminToken > 0) {
                    $catName = isset($arr->cat_name) ? $arr->cat_name : 'Order Status';
                    $statusArr = $this->order_report->getOrderStatus($catName);
                    foreach($statusArr as $key=>$value){
                        $statusList[] =['key'=>$key,'value'=>$value]; 
                    }
                    if (is_array($statusArr) && count($statusArr) > 0) {
                        $data = $statusList;
                        $message = 'statusList';
                        $status = 200;
                    } else {
                        $data = [];
                        $message = 'No data';
                        $status = 200;
                    }
                    return json_encode([
                        'status' => $status,
                        'message' => $message,
                        'data' => $data
                    ]);
                } else {
                    return Array('status' => 'session', 'message' => 'Your Session Has Expired. Please Login Again.', 'data' => []);
                }
            } else {
                print_r(json_encode(array('status' => "failed", 'message' => "Pass user token", 'data' => [])));
                die;
            }
        } catch (ErrorException $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
