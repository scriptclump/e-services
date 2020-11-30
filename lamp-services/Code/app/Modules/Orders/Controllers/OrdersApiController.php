<?php
/*
* Filename: OrdersApiController.php
* Description: This file is used for manage retailer & sales orders related api
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor@2016
* Version: v1.0
* Created date: 27th July 2017
* Modified date: 
*/

/*
* OrdersApiController is used to manage orders related api
* @author    Ebutor <info@ebutor.com>
* @copyright ebutor@2017
* @package   Orders
* @version:  v1.0
*/ 
namespace App\Modules\Orders\Controllers;
use Illuminate\Support\Facades\Input;
use Session;
use Response;
use URL;
use Config;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Modules\Orders\Controllers\ReturnController;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\Invoice;
use App\Modules\Orders\Models\OrderModel;

use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\models\Mongo\MongoApiLogsModel;

class OrdersApiController extends BaseController {   
    
    protected $_Dmapiv2Model;
    protected $_ShipmentModel;
    protected $_InvoiceModel;
     protected $_OrderModel;

    public function __construct() { 
       $this->_Dmapiv2Model = new Dmapiv2Model();
       $this->_ShipmentModel = new Shipment();
       $this->_InvoiceModel = new Invoice();
       $this->_OrderModel = new OrderModel();
    }

    public function updateReturnApiApproval()
    {
        $input_data = Input::all();

        /*
            Checking and validating input data.
         */
        if (isset($input_data['data'])) {   
            $returnData = json_decode($input_data['data'],true);
            if(!$returnData){
                return Response::json(['Status' => 'false', 'Message' => 'Json format sent is wrong']);
            }
          
            if(!isset($returnData['user_id'])){
            
                return Response::json(['Status' => 'false', 'Message' => 'User id Important feild missing !!!']);
            }
            /*
                Creating session for api handler
             */
            Session::put('userId', $returnData['user_id']);
        }
        /*
            returnController init Retrun Controller
         */
        $returnController = new ReturnController;
        /*
            passing data to updateReturnApiApproval with return data parameters
         */
        $message = $returnController->updateReturnApiApproval($returnData);

        return $message;
    }

    /**
     * setSession() method is used for set user id in session
     * @param  $userId Number
     * @return Null
     */
    
    private function setSession($userId) {
        if($userId) {
          Session::put('userId', $userId);
        }
    }

    public function authenticatToken($lpToken) {
        return json_decode(json_encode(DB::table("users")->where("lp_token", $lpToken)->orWhere("password_token", $lpToken)->pluck("user_id")->all()), true);
    }    

    public function verifyPickedQtyAction(Request $request) {
        
        try{
            $data = $request->all();
            #print_r($data);die;
            
            $MongoApiLogsModel = new MongoApiLogsModel();
            $mongoInsertId = $MongoApiLogsModel->insertApiLogsRequest('verifyPickedQty', $data);
            
            if(count($data) <=0 || empty($data['data'])) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
            }
            
            $postData = json_decode($data['data'], true);

            $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
            $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
            
            $token = isset($postData['token']) ? $postData['token'] : '';

            $userInfo = $this->authenticatToken($token);
            
            $userId = isset($userInfo[0]) ? (int)$userInfo[0] : 0;
            $orderId = isset($postData['order_id']) ? (int)$postData['order_id'] : 0;

            if(!$userId) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid User Token'));
            }

            if(!$orderId) {
                return Response::json(array('Status' => 404, 'Message' => 'Please input order id.'));
            }

            $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'verifyPickedQty');

            if(!$response) {
                return Response::json(array('Status' => 404, 'Message' => 'verifyPickedQty API Authentication failed.'));
            }

            if($orderId) {
                $pickedQty = $this->_ShipmentModel->getPickedQtyByOrderId($orderId);
                $orderedQty = $this->_OrderModel->getOrderedQtyByOrderId($orderId);
                if($orderedQty == $pickedQty) {
                    $response = array('Status' => 200, 'Message' => 'FullPicked');
                }
                else {
                    $response = array('Status' => 200, 'Message' => 'PartialPicked');
                }

                $MongoApiLogsModel->updateResponse($mongoInsertId, $response, $orderId);
                return Response::json($response);
            }
        }
        catch(Exception $e) {
            return Response::json(array('Status' => 404, 'Message' => 'Something went wrong.'));
        }
    }

    public function generateInvoiceAction(Request $request) {
        DB::beginTransaction();
        try{
            $data = $request->all();
            //print_r($data);die;
            $MongoApiLogsModel = new MongoApiLogsModel();
            $mongoInsertId = $MongoApiLogsModel->insertApiLogsRequest('GenerateInvoice', $data);
            
            /**
             * Validate data
             */
            
            if(count($data) <=0 || empty($data['data'])) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid input data'));
            }
            
            $postData = json_decode($data['data'], true);

            $apiKey = isset($data['api_key']) ? $data['api_key'] : Config::get('dmapi.GDSAPIKey');
            $secretKey = isset($data['secret_key']) ? $data['secret_key'] : Config::get('dmapi.GDSAPISECRETKey');
            
            $token = isset($postData['token']) ? $postData['token'] : '';

            $userInfo = $this->authenticatToken($token);
            
            $userId = isset($userInfo[0]) ? (int)$userInfo[0] : 0;
            $orderId = isset($postData['order_id']) ? (int)$postData['order_id'] : 0;

            if(!$userId) {
                return Response::json(array('Status' => 404, 'Message' => 'Invalid User Token'));
            }

            if(!$orderId) {
                return Response::json(array('Status' => 404, 'Message' => 'Please input order id.'));
            }

            $this->setSession($userId);

            $response = $this->_Dmapiv2Model->checkUserAccess($apiKey, $secretKey, 'GenerateInvoice');

            if(!$response) {
                return Response::json(array('Status' => 404, 'Message' => 'GenerateInvoice API Authentication failed.'));
            }

            if($orderId) {
                $shipInfo = $this->_ShipmentModel->verifyShipmentByOrderId($orderId, array('grid.gds_ship_grid_id'));
                $shipGridId = isset($shipInfo->gds_ship_grid_id) ? $shipInfo->gds_ship_grid_id : 0;
                #var_dump($shipInfo);die;
            
                if($shipGridId) {
                    
                    $invoiceInfo = $this->_InvoiceModel->getInvoiceGridOrderId(array($orderId), array('grid.gds_invoice_grid_id'));

                    $invoiceGridId = isset($invoiceInfo[0]->gds_invoice_grid_id) ? $invoiceInfo[0]->gds_invoice_grid_id : 0;
                    if($invoiceGridId) {
                        return Response::json(array('Status' => 404, 'Message' => 'Invoice already generated.'));
                    }
                    else {
                        $this->_InvoiceModel->generateInvoiceByOrderId($orderId, $shipGridId, true, 'Invoice generated by app.');
                        
                        $response = array('Status' => 200, 'Message' => 'Invoice Created Successfully.');

                        $MongoApiLogsModel->updateResponse($mongoInsertId, $response, $orderId);

                        DB::commit();
                        return Response::json($response);
                    }
                }
                else {
                    return Response::json(array('Status' => 404, 'Message' => 'Make sure order should be RTD.'));
                }
            }
        }
        catch(Exception $e) {
            DB::rollback();
            return Response::json(array('Status' => 404, 'Message' => 'Something went wrong.'));
        }

    }

    public function getApiLogsAction() {
        $MongoApiLogsModel = new MongoApiLogsModel();
        $logs = $MongoApiLogsModel->getApiLogsListDate('2017-05-01','2017-05-22', 10,0);
        print_r($logs);
        die();
        //$MongoApiLogsModel->updateResponse($mongoInsertId, $response);
    }
}