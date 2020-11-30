<?php

/*
 * Filename: IndentController.php
 * Description: This file is used for manage sales orders
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 23 June 2016
 * Modified date: 23 June 2016
 */

/*
 * IndentController is used to manage orders
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\Indent\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Log;
use Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Indent\Models\IndentModel;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Indent\Models\Products;
use App\Modules\Indent\Models\ProductTot;
use App\Modules\Indent\Models\Po;
use App\Central\Repositories\RoleRepo;
use App\Modules\Notifications\Models\NotificationsModel;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Roles\Models\Role;
use Lang;
use Mail;
use DB;
use Notifications;
use PDF;
use Utility;

use Excel;

class IndentController extends BaseController {

    protected $_orderModel;
    protected $_masterLookup;
    protected $_legalEntityModel;
    protected $_Products;
    protected $_roleRepo;
    protected $_productTot;
    protected $_roleModel;
    
    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                Redirect::to('/login')->send();
            }
            return $next($request);
        });
        $this->_orderModel = new OrderModel();
        $this->_masterLookup = new MasterLookup();
        $this->_Indent = new IndentModel();
        $this->_legalEntityModel = new LegalEntity();
        $this->_Products = new Products();
        $this->_roleRepo = new RoleRepo();
        $this->__productTot = new ProductTot();
        $this->_po = new Po();
        $this->_roleModel = new Role();

        $this->produc_grid_field_db_match = array(
            'indentID' => 'indent_code',
            'indentType'   => 'indent_type',
            'supplier_id' => 'legal.business_legal_name',
            'indentDate' => 'indent.created_at',
            'createdBy' => 'user_name',
            'indentLocation' => 'warehouse.lp_wh_name',
            'qty' => 'qty',
            'Status' => 'status_name',
            'manufacturer'=>'getManfName(products.manufacturer_id)',
        );

        parent::Title('Indents - '.Lang::get('headings.Company'));
    }

    /*
     * createIndent() method is used to create indent
     * @param Null
     * @return
     */
    public function createIndent() {
        try{
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('IND002');
            if($hasAccess == false) {
                return View::make('Indent::error');
            } 
            
            $legalentityId = Session::get('legal_entity_id');            
            
            $warehouses = $this->_Indent->getWarehouses($legalentityId);
            $indentProducts = Session::get('indentProductData');
            
            return View::make('Indent::createIndent')
                            ->with('warehouses', $warehouses)
                            ->with('indentProducts', $indentProducts);
        } 
        catch (Exception $ex) {
            return Response::json(array('status' => 200, 'message' => Lang::get('salesorders.errorInputData')));
        }        
    }

    /*
     * downloadPDF() method is used to download indent in PDF and send email to logistic manager
     * @param $indent_id Integer
     * @return Null
     */
    
    public function downloadPDF($indent_id) {

		$indentArr = $this->_Indent->getIndentDetailById($indent_id);
        if(count($indentArr) > 0) {

            $leWhId = isset($indentArr[0]->le_wh_id) ? $indentArr[0]->le_wh_id : 0;
            $leId = isset($indentArr[0]->legal_entity_id) ? $indentArr[0]->legal_entity_id : 0;
            
            $warehouse = $this->_legalEntityModel->getWarehouseById($leWhId);
            $supplier = $this->_legalEntityModel->getLegalEntityById($leId);
            $supContact = $this->_legalEntityModel->getUserByLegalEntityId($leId);
             
            $leParentId = $this->_legalEntityModel->getLeParentIdByLeId($leId);
            $leParentId = empty($leParentId) ? 2 : $leParentId;

            $leInfo = $this->_legalEntityModel->getLegalEntityById($leParentId);
            $companyInfo = $this->_legalEntityModel->getCompanyAccountByLeId($leParentId);

            $data = array('warehouse'=>$warehouse,'leInfo'=>$leInfo,'supplier'=>$supplier,'supContact'=>$supContact, 'indentArr'=>$indentArr, 'companyInfo'=>$companyInfo);

            $indentCode = isset($indentArr[0]->indent_code) ? $indentArr[0]->indent_code : $indent_id;

            $pdf = PDF::loadView('Indent::indentPdf', $data);
            $subject = 'New Indent Created | ID#'.$indentCode;
            $body['attachment'] = $pdf->output();
            $body['file_name'] = 'Indent_'.$indentCode.'.pdf';
            $body['template'] = 'emails.indent_report';
            $body['name'] = 'Ebutor';
            $body['comment'] = '';

            $purchaseOrder = new PurchaseOrder();
            $notificationObj= new NotificationsModel();
            $userIdData= $notificationObj->getUsersByCode('IND0001');
            $userIdData=json_decode(json_encode($userIdData));
            $userEmailArr = $purchaseOrder->getUserEmailByIds($userIdData);
            $toEmails = array();
            if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                foreach($userEmailArr as $userData){
                    $toEmails[] = $userData['email_id'];
                }
            }
            Utility::sendEmail($toEmails, $subject, $body);
        }
    }

    /**
     * createIndentAction() method is used to store indent information
     * @param Null
     * @return
     */

    public function createIndentAction() {
       try{

           $postData = Input::all();
                      
           if(empty($postData['indent_warehouse']) || empty($postData['indent_supplier'])) {
                return Response::json(array('status' => 200, 'message' => Lang::get('indent.alertWH')));
           }

           if(!isset($postData['indent_products']) || !is_array($postData['indent_products']) || count($postData['indent_products']) <=0 ) {
                return Response::json(array('status' => 200, 'message' => Lang::get('indent.alertEmptyProd')));
           }
           
           $indent_date = date('Y-m-d H:i:s',strtotime($postData['indent_date']));

           $query = DB::table('legal_entities as legal')
                        ->leftjoin('zone as zn','zn.zone_id','=','legal.state_id')
                        ->where('legal_entity_id',$postData['indent_supplier'])
                        ->first();
           $stateCode = isset($query->code)?$query->code:"TS";          
           $indent_code = $this->getIndentCode($stateCode);

           $indentArr=array('indent_date'=>$indent_date,
                            'indent_type'=>1,
                            'indent_code'=>$indent_code,
                            'le_wh_id'=>$postData['indent_warehouse'],
                            'legal_entity_id'=>$postData['indent_supplier']
                            );
           $indent_id = $this->_Indent->saveIndent($indentArr);
           
           if($indent_id){
                foreach($postData['indent_products'] as $productId){
                    $indentQty = (isset($postData['indent_qty'][$productId]))?$postData['indent_qty'][$productId]:0;
                    $productData = $this->_Products->getProductsById($productId,$postData['indent_warehouse'],$postData['indent_supplier']);
                    $indentProducts[] = array(
                        'indent_id'=>$indent_id,
                        'product_id'=>$productData->product_id,
                        'pname'=>$productData->product_name,
                        'qty'=>$indentQty,
                        'mrp'=>$productData->mrp,
                        'price'=>$productData->base_price,
                        'cost'=>$productData->cbp,
                        'upc'=>$productData->upc,
                        'sku'=>$productData->sku,
                        );                     
                }
                #print_r($indentProducts);die;                
                $gds_op_id = $this->_Indent->saveIndentProducts($indentProducts);
                                
                $this->downloadPDF($indent_id);

                Notifications::addNotification(['note_code' => 'IND0001','note_message'=>'Indent #INDID Created Successfully', 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['INDID' => $indent_code], 'note_link' => '/indents/detail/'.$indent_id]);

               return Response::json(array('status' => 200, 'message' => Lang::get('indent.successIndent'),'indent_id'=>$indent_id));
           }else{
               return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData')));
           }
       } 
       catch (Exception $ex) {
            return Response::json(array('status' => 400, 'message' => Lang::get('salesorders.errorInputData')));
       }
    }

    /*
     * sendIndentEmail() method is used to send email
     * @param $mailTo String
     * @param $subject String
     * @param $body Array
     * @return Boolean
     */
    /*    
    private function sendIndentEmail($mailTo, $subject, $body = array()) {
        try {
            $mailFields = array('mailTo'=>$mailTo, 'subject'=>$subject, 'attachment'=>$body['attachment'],'file_name'=>$body['file_name']);
            $success = Mail::send($body['template'], array('name'=>$body['name']), function ($message) use ($mailFields) {
                $message->to($mailFields['mailTo']);
                $message->subject($mailFields['subject']);
                if ($mailFields['file_name']!='') {
                    $message->attachData($mailFields['attachment'], $mailFields['file_name']);
                    //$message->attach($mailFields['attachment']);
                }
            });
            return $success;
        }
        catch(Exception $e) {
            return false;
        }
    }
*/
    /*
     * indentList() method is used to show List of Indents
     * @param Null
     * @return
     */

    public function indentList(Request $request, $status = null) {
        try{
            Session::put('indentProductData', array());
            $legalEntityId = Session::get('legal_entity_id');
            
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('IND001');
            $createAccess = $this->_roleRepo->checkPermissionByFeatureCode('INDCR1');
            $exportInd = $this->_roleRepo->checkPermissionByFeatureCode('INDEXP');
            $stockistInd = $this->_roleRepo->checkPermissionByFeatureCode('INDSTK');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }            
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $allDc = $this->_orderModel->getDcHubDataByAcess($dc_acess_list);
            $filter_options['dc_data'] = $allDc;
            $supplierstype=1002;
            $suppliers = $this->_legalEntityModel->getLegalEntity($legalEntityId,$supplierstype);
            $statusArr = $this->_masterLookup->getAllOrderStatus('INDENT_STATUS');

            return View::make('Indent::index')->with(['suppliers'=>$suppliers,'allStatusArr'=>$statusArr,
                                                    'createAccess'=>$createAccess,'exportInd'=>$exportInd,'stockistInd'=>$stockistInd,'filter_options'=>$filter_options]);
        }
        catch(Exception $e) {

        }
    }

    /*
     * getOrderIndentAction() method is used to get List of Indents for grid
     * @param Null
     * @return
     */
    
    public function getOrderIndentAction(Request $request, $status = null) {

        try {

            $filter = array();
            $getData = $request->all();
            
            if(!empty($getData['supplier'])) {
                $filter['legal_entity_id'] = $getData['supplier'];
            }

            if(!empty($getData['indent_code'])) {
                $filter['indent_code'] = $getData['indent_code'];
            }

            if(!empty($getData['indent_status'])) {
                $filter['indent_status'] = $getData['indent_status'];
            }

            if(!empty($getData['fdate'])) {
                $filter['fdate'] = date('Y-m-d', strtotime($getData['fdate']));
            }

            if(!empty($getData['tdate'])) {
                $filter['tdate'] = date('Y-m-d', strtotime($getData['tdate']));
            }
            //print_r($filter);
            /*
             * for paging
             */
            
            $offset = (int) $request->input('$skip');
            $perpage = $request->input('$stop');
            $perpage=$request->input('$top');
            //$perpage = isset($perpage) ? $perpage : 10;

            /*
             * Prepare data for grid
             */
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

            $statusArr = $this->_masterLookup->getAllOrderStatus('INDENT_STATUS');
            // $totalIndents = $this->_Indent->getIndentCount($filter); //getting count
            $indentArr = $this->_Indent->getOrderIndents($filter,$filter_by, $offset, $perpage,$orderby_array = "");

            $IndentdeleteAccess = $this->_roleRepo->checkPermissionByFeatureCode('INDDLT001');
            
            $dataArr = array();
			
            if (count($indentArr['data'])) {
                foreach ($indentArr['data'] as $indent) {
                $actions='<a href="/indents/detail/' . $indent->indent_id . '"><i class="fa fa-eye"></i></a>&nbsp;';
                    if($indent->business_legal_name!=''){
                $actions .='<a href="/indents/print/' . $indent->indent_id . '" target="_blank"><i class="fa fa-print"></i></a>&nbsp;<a href="/indents/pdf/' . $indent->indent_id . '"><i class="fa fa-download"></i></a>';
}
            if($indent->status_name=='Pending' && $IndentdeleteAccess==true){
                   $actions .= '<a href="javascript:void(0)" onclick="deleteData('.$indent->indent_id.')">
              <i class="fa fa-trash-o"></i>
              </a>';
                }					
                    $dataArr[] = array(
                        'indentID' => $indent->indent_code,
                        'indentType' => ($indent->indent_type == 1 ? 'Manual' : 'Auto'),
                        'indentDate' => ($indent->created_at),
                        'supplier_id' => ($indent->business_legal_name),
                        'indentLocation' => $indent->lp_name,
                        'qty' => (int)$indent->qty,
                        'manufacturer'=>$indent->manufacturer_id,
                        'Status' => (isset($statusArr[$indent->indent_status]) ? $statusArr[$indent->indent_status] : 'Pending'),
                        'Actions' => $actions);
                }
            }
            return Response::json(array('totalIndents' => $indentArr['count'], 'data' => $dataArr));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
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
                    if (substr_count($data, 'indentDate')) {
                        $filterDataArr['indentDate']['operator'] = $this->getCondOperator($dataArr[1]);
                        if (substr_count($dataArr[2], 'DateTime')) {
                            $dataArrr = explode("'", $dataArr[2]);
                            $time = strtotime($dataArrr[1]);
                            $filterDataArr['indentDate'][] = date("d", $time);
                            $filterDataArr['indentDate'][] = date("m", $time);
                            $filterDataArr['indentDate'][] = date("Y", $time);
                        } else {
                            $filterDataArr['indentDate'][] = $dataArr[2];
                        }
                    } 
                    if (substr_count($data, 'indentID') && !array_key_exists('indentID', $filterDataArr)) {
                        $poIdValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'indentID'), '', $data));
                        $value = (isset($poIdValArr[1]) && $poIdValArr[1] == 'eq' && isset($poIdValArr[2])) ? $poIdValArr[2] : '%'.$poIdValArr[0].'%';
                        $operator = (isset($poIdValArr[1]) && $poIdValArr[1] == 'eq') ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['indentID'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'indentType') && !array_key_exists('indentType', $filterDataArr)) {
                        $indTypeValArr = explode(' ', str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'indentType'), '', $data));
                        $types = array(0 => 'auto', 1 => 'manual');
                        $value = (isset($indTypeValArr[1]) && $indTypeValArr[1] == 'eq' && isset($indTypeValArr[2])) ? $indTypeValArr[2] : $indTypeValArr[0];
                        $input = preg_quote($value, '~'); // don't forget to quote input string!
                        $result = preg_grep('~' . $input . '~', $types);
                        $type = '';
                        foreach ($result as $key => $val) {
                            $type.= $key.',';
                        }
                        $type = trim($type,',');
                        if($type!=''){
                            $filterDataArr[] = $this->produc_grid_field_db_match['indentType'].' '.'in '.$type;
                        }
                    }
                    if (substr_count($data, 'supplier_id') && !array_key_exists('supplier_id', $filterDataArr)) {
                        $sup = explode(' ge ', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'supplier_id','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['supplier_id'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'indentLocation') && !array_key_exists('indentLocation', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'indentLocation','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['indentLocation'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'qty') && !array_key_exists('qty', $filterDataArr)) {
                        $filterDataArr[] = $this->produc_grid_field_db_match['qty'].' '.$this->getCondOperator($dataArr[1]).' '. $dataArr[2];
                    }
                    if (substr_count($data, 'createdBy') && !array_key_exists('createdBy', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'createdBy','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['createdBy'].' '.$operator.' '.$value;
                    }
                    if (substr_count($data, 'Status') && !array_key_exists('Status', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'Status','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['Status'].' '.$operator.' '.$value;
                    } 

                    if (substr_count($data, 'manufacturer') && !array_key_exists('manufacturer', $filterDataArr)) {
                        $sup = explode('ge', $data);
                        $pos = strpos($data, 'eq');
                        $suppValArr = str_replace(array('(', ')', "'", ',', 'indexof', 'tolower', 'manufacturer','eq '), '', $sup[0]);
                        $value = ($pos>0) ? trim($suppValArr,' ') : '%'.trim($suppValArr,' ').'%';
                        $operator = ($pos>0) ? '=' : 'LIKE';
                        $filterDataArr[] = $this->produc_grid_field_db_match['manufacturer'].' '.$operator.' '.$value;
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
     * getIndentDetailAction() method is used to get indent detail by id
     * @param $indentId Numeric
     * @return HTML
     */

    public function getIndentDetailAction($indentId) {
		try {

            Session::put('indentProductData', array());
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('IND003');
            
            $createPO_IndentPage = $this->_roleRepo->checkPermissionByFeatureCode('CPOI001');

            $EditIndent_indentPage = $this->_roleRepo->checkPermissionByFeatureCode('EDI001');

            $updateSupplier_IndentPage = $this->_roleRepo->checkPermissionByFeatureCode('UPSI001');


            if($hasAccess == false) {
                return View::make('Indent::error');
            }  

			$indentArr = $this->_Indent->getIndentDetailById($indentId);
            
            parent::Title('Indent Details # '.$indentArr[0]->indent_code);

			
			if(count($indentArr) == 0) {
				Redirect::to('/indents')->send();
                die();
			}

            /*foreach ($indentArr as $key => $value) {
               $indentArr[$key]->packtype = $this->_Indent->getpacktype($value->product_id, $value->prod_eaches);
            }*/
            
			$leWhId = isset($indentArr[0]->le_wh_id) ? $indentArr[0]->le_wh_id : 0;
			$leId = isset($indentArr[0]->legal_entity_id) ? $indentArr[0]->legal_entity_id : 0;
			$warehouse = $this->_legalEntityModel->getWarehouseById($leWhId);
            //$supplier = $this->_legalEntityModel->getLegalEntityById($leId);
            //$suppliers = $this->__productTot->getSuppliersByIndent($indentId,$leWhId);
            $purchaseOrder = new PurchaseOrder();
            $data['indent_id']=$indentId;
            $suppliers = $purchaseOrder->getSuppliersforIndents($data);
            $indentCount_PO = $this->_po->getPoCountForAnIndent($indentId);
            $indentStatus = $this->_po->getIndentStatus($indentId);

            
            $suppliers = json_decode(json_encode($suppliers), true);
			
			return View::make('Indent::detail')
										->with('warehouse', $warehouse)
                                        ->with('selectedSupplier', $leId)
                                        // ->with('message', $message)
										->with('indentArr', $indentArr)
                                        ->with('suppliers', $suppliers)
										->with('createPO_IndentPage', $createPO_IndentPage)
                                        ->with('EditIndent_indentPage', $EditIndent_indentPage)
                                        ->with('updateSupplier_IndentPage', $updateSupplier_IndentPage)
                                        ->with('indent_count_po', $indentCount_PO)
                                        ->with('indent_Status', $indentStatus)
                                        ->with('encode_indent_id', $this->_roleRepo->encodeData($indentId))
                                        ->with('indentId', $indentId);
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

    public function updateIndentDetails($indentId)
    {
        try {
            parent::Title('Edit Indent - '.Lang::get('headings.Company'));
            Session::put('indentProductData', array());
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('IND003');
            $updateSupplier_IndentPage = $this->_roleRepo->checkPermissionByFeatureCode('UPSI001');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }  

            $indentArr = $this->_Indent->getIndentDetailById($indentId);
            
            if(count($indentArr) == 0) {
                Redirect::to('/indents')->send();
                die();
            }
            
            $leWhId = isset($indentArr[0]->le_wh_id) ? $indentArr[0]->le_wh_id : 0;
            $leId = isset($indentArr[0]->legal_entity_id) ? $indentArr[0]->legal_entity_id : 0;
            $warehouse = $this->_legalEntityModel->getWarehouseById($leWhId);
            //$supplier = $this->_legalEntityModel->getLegalEntityById($leId);
            $suppliers = $this->__productTot->getSuppliersByIndent($indentId,$leWhId);
            // $supplier = $this->_legalEntityModel->getLegalEntityById($leId);

            $suppliers = json_decode(json_encode($suppliers), true);
            
            return View::make('Indent::editDetail')
                                        ->with('warehouse', $warehouse)
                                        ->with('selectedSupplier', $leId)
                                        ->with('suppliers', $suppliers)
                                        ->with('indentArr', $indentArr)
                                        ->with('updateSupplier_IndentPage', $updateSupplier_IndentPage)
                                        ->with('indentId', $indentId);
        
            
        } catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


    public function saveDetails(Request $request)
    {
        try {
            $indent_data = $request->all();
            unset($indent_data['_token']);
            $update = $this->_Indent->getUpdateIndent($indent_data);
            if($update)
            {
                Session::put('message', 'success');
            }
            else
            {
                Session::put('message', '');
            }
            return $update;
        } catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getSelectedSupplierAddress($supplierId)
    {
        try {
            $getAddress = $this->__productTot->getSelectedSupplierAddress($supplierId);
            return $getAddress;
        } catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function updateIndentSupplier(Request $data){
        try{
            $supplierID = $data->input('supplierId');
            $indentId = $data->input('indentId');
            //echo $indentId;
            Log::info("Update Indent ID: ".$indentId." with Supplier ID: ".$supplierID);
            $response = $this->_Indent->updateIndent($indentId, array('legal_entity_id'=>$supplierID));
            echo $response;
        } 
        catch(Exception $e){
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            echo $e->getMessage();
        }
    }

    /**
     * printAction() method is used to print indent
     * @param $indentId Numeric
     * @return HTML
     */
    
    public function printAction($indentId) {
        try {
            //parent::Title('Print Indent - '.Lang::get('headings.Company'));
            Session::put('indentProductData', array());
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('IND003');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }  

            $indentArr = $this->_Indent->getIndentDetailById($indentId);
            parent::Title('Print Indent - '.Lang::get('headings.Company')." - ".trans($indentArr[0]->indent_code));
            if(count($indentArr) == 0) {
                Redirect::to('/indents')->send();
                die();
            }
                        
            $leWhId = isset($indentArr[0]->le_wh_id) ? $indentArr[0]->le_wh_id : 0;
            $warehouse = $this->_legalEntityModel->getWarehouseById($leWhId);
            
            $leId = isset($indentArr[0]->legal_entity_id) ? $indentArr[0]->legal_entity_id : 0;
            $supplier = $this->_legalEntityModel->getLegalEntityById($leId);
            $supContact = $this->_legalEntityModel->getUserByLegalEntityId($leId);
             
            $leParentId = $this->_legalEntityModel->getLeParentIdByLeId($leId);
            $leParentId = empty($leParentId) ? 2 : $leParentId;
            $leInfo = $this->_legalEntityModel->getLegalEntityById($leParentId);
            $companyInfo = $this->_legalEntityModel->getCompanyAccountByLeId($leParentId);
            
            return View::make('Indent::print')
                                        ->with('warehouse', $warehouse)
                                        ->with('leInfo', $leInfo)
                                        ->with('supplier', $supplier)
                                        ->with('supContact', $supContact)
                                        ->with('companyInfo', $companyInfo)
                                        ->with('indentArr', $indentArr);
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /**
     * getIndentPdfAction() method is used to download indent
     * @param $indentId Numeric
     * @return HTML
     */
    
    public function getIndentPdfAction($indentId) {
        try {
            Session::put('indentProductData', array());
            $hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('IND003');
            if($hasAccess == false) {
                return View::make('Indent::error');
            }  

            $indentArr = $this->_Indent->getIndentDetailById($indentId);
            if(count($indentArr) == 0) {
                Redirect::to('/indents')->send();
                die();
            }
                        
            $leWhId = isset($indentArr[0]->le_wh_id) ? $indentArr[0]->le_wh_id : 0;
            $warehouse = $this->_legalEntityModel->getWarehouseById($leWhId);
            
            $leId = isset($indentArr[0]->legal_entity_id) ? $indentArr[0]->legal_entity_id : 0;
            $supplier = $this->_legalEntityModel->getLegalEntityById($leId);
            $supContact = $this->_legalEntityModel->getUserByLegalEntityId($leId);
             
            $leParentId = $this->_legalEntityModel->getLeParentIdByLeId($leId);
            $leParentId = empty($leParentId) ? 2 : $leParentId;
            $leInfo = $this->_legalEntityModel->getLegalEntityById($leParentId);
            $companyInfo = $this->_legalEntityModel->getCompanyAccountByLeId($leParentId);

            $data = array('warehouse'=>$warehouse,'leInfo'=>$leInfo,'supplier'=>$supplier,'supContact'=>$supContact, 'indentArr'=>$indentArr, 'companyInfo'=>$companyInfo);
//echo '<pre/>';print_r($data);die;
            $pdf = PDF::loadView('Indent::indentPdf', $data);
            return $pdf->download('Indent'.$indentId.'.pdf');
     /*       return View::make('Indent::indentPdf')
                                        ->with($data);*/
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    /*
     * supplierSupplierBrandOptions() method is used to get Suppliers by legalentity 
     * Warehouse Id Options
     * @param Null
     * @return JSON
     */

    public function supplierSupplierOptions() {
        try {
            $le_wh_id = Input::get('le_wh_id');
            $suppliers = $this->_Indent->getSuppliersByWarehouseId($le_wh_id);
            $supplierOptions = '<select name="indent_supplier" id="indent_supplier" class="form-control select2me"><option value="">Select Supplier</option>';

            if(is_array($suppliers)) {
                foreach($suppliers as $supplier) {
                    $supplierInfo = $supplier->business_legal_name.', '.$supplier->address1.', '.$supplier->city.', '.$supplier->pincode;                
                    $supplierOptions .= '<option value="'.$supplier->supplier_id.'">'.$supplierInfo.'</option>';
                }
            }            
            $supplierOptions .= '</select>';            
            return json_encode(array('suppliers'=>$supplierOptions));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * productsBySupplier() method is used to get supplier Warehouse Options
     * @param Null
     * @return
     */

    public function productsBySupplier() {
        try {
            $supplier_id = Input::get('supplier_id');
            $warehouse_id = Input::get('warehouse_id');
            $products = $this->_Products->getproductsBySupplier($supplier_id,$warehouse_id);
            $productOptions = '<select class="form-control" name="sup_skus" id="sup_skus" required="required"><option value="">Select Product</option>';
            if(is_array($products)) {
                foreach($products as $product) {
                    $sku = ($product->sku!='')?' ('.$product->sku.')':'';
                    $productOptions .= '<option value="'.$product->product_id.'">'.$product->product_name.$sku.'</option>';
                }
            }
            
            $productOptions .= '</select>';
            
            return json_encode(array('products'=>$productOptions));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    /*
     * getProductInfo() method is used to get get Product Information
     * @param Null
     * @return
     */

    public function getProductInfo() {
        try {
            $product_id = Input::get('sup_skus');
            $identQty = Input::get('identQty');
            $le_wh_id = Input::get('le_wh_id');
            $supplier_id = Input::get('supplier_id');
            $product = $this->_Products->getProductsById($product_id,$le_wh_id,$supplier_id);
            #print_r($product);die;
            $product_data = '';
            $currency = ($product->symbol!='') ? $product->symbol:'Rs.';
            $product_data .= '<tr>
                                <td align="center">
                                <input name="indent_products[]" checked="checked" id="" type="hidden" value="'.$product->product_id.'">'.$product->sku.'
                                </td>
                                <td>'.$product->product_name.'</td>
                                <td align="center">'.(isset($product->upc) ? $product->upc : $product->seller_sku).'</td>
                                <td align="center">'.$product->mrp.'</td>
                                <td align="center">'.(int)$product->soh.'</td>
                                <td align="center">
                                <input type="number" min="1" value="'.(int)$identQty.'" size="3" class="form-control" name="indent_qty['.$product->product_id.']"/>
                                </td>
                                <td align="center">'.(int)$product->mbq.'</td>
                                <td align="center"><a class="fa fa-trash-o delete_product" data-id="'.$product->product_id.'"></a></td>
                            </tr>';
            $final = Session::push('indentProductData.'.$product->product_id, $product_data);
            return json_encode(array('product_data'=>$product_data));            
            
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
	
    /**
     * exportToCSV() method is use to export in csv
     * @param $postData Array
     * @return JSON
     */
    
    /**
     * Remember:
     * 
     * We are not using exportToCSV, that's why comment it.
     * 
	private function exportToCSV($postData) {
		try{
            $heading = array('SNo', 'Indent ID', 'Indent Date', 'DC', 'Supplier', 'SKU Code', 'Product Name', 'EAN Code','MRP', 'Available Inventory', 'Indent Qty', 'MPQ', 'Status');

            $file_path = public_path().'/download/indent'.time().'.csv';
            $fp = fopen($file_path, 'w');
            fputcsv($fp, $heading);         
            $statusArr = $this->_masterLookup->getAllOrderStatus('INDENT_STATUS');
            if(isset($postData['sku']) && is_array($postData['sku'])) {
                $sno = 1;
                foreach($postData['sku'] as $key=>$sku) {
                    $dateArr = array($sno,
                                    $postData['indent_id'], 
                                    $postData['indent_date'], 
                                    $postData['dc_name'], 
                                    $postData['supplier_name'], $sku, 
                                    $postData['pname'][$key], 
                                    $postData['upc'][$key], 
                                    $postData['mrp'][$key], 
                                    $postData['inventory'][$key], 
                                    $postData['indent_qty'][$key], 
                                    $postData['mpq'][$key],
                                    $statusArr[$postData['indent_status']]);
                    fputcsv($fp, $dateArr); 
                    #print_r($dateArr);
                    $sno = $sno +1;                   
                }
            }
            fclose($fp);

            // send email
            $body = array('template'=>'emails.indent_report', 'attachment'=>$file_path, 'name'=>'Hello All');

            $userEmailArr = $this->_Indent->getUserEmailByRoleName('Finance Manager');
            $toEmails = array();
            if(is_array($userEmailArr) && count($userEmailArr) > 0) {
                foreach($userEmailArr as $userData){
                    $toEmails[] = $userData->email_id;
                }
                Utility::sendEmail($toEmails, 'Indent Report', $body);
            }

            return true;
        }
        catch(Exception $e) {
            return false;           
        }						
	}
	*/

    /**
     * updateIndentAction() method is use to update indent
     * @param Null
     * @return JSON
     */
    /**
     * Remember:
     * 
     * We are not using update indent, that's why comment it
     * 
	public function updateIndentAction() {
		try {
			
			$postData = Input::all();
			#print_r($postData);die;
			if(isset($postData['approve']) && $postData['approve'] == 'approve') {				
				$this->exportToCSV($postData);
                $this->_Indent->updateIndent($postData['indent_id'], array('indent_status'=>'70002'));    
			}
			
			if(isset($postData['approve']) && $postData['approve'] == 'save') {
				if(is_array($postData['indent_qty']) && count($postData['indent_qty']) > 0) {
					foreach($postData['indent_qty'] as $indentId=>$indentQty) {
						$this->_Indent->updateIndentProduct($indentId, array('qty'=>$indentQty));
					}
				}
			}
			return Response::json(array('status'=>200, 'message'=>Lang::get('salesorders.success')));
		}
		catch (Exception $e){
			return Response::json(array('status'=>404, 'message'=>Lang::get('salesorders.404')));
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
    */
   
    /**
     * sendEmail() method is use to send email to user
     * @param $mailTo, $subject String, $body = array()
     * @return Boolean
     */
    /*
    private function sendEmail($mailTo, $subject, $body = array()) {
        try {
            $mailFields = array('mailTo'=>$mailTo, 'subject'=>$subject, 'attachment'=>$body['attachment']);
            $success = Mail::send($body['template'], array('name'=>$body['name']), function ($message) use ($mailFields) {
                
                $message->to($mailFields['mailTo']);
                $message->subject($mailFields['subject']);

                if ($mailFields['attachment'] != '') {
                    $message->attach($mailFields['attachment']);
                }

            });
            return $success;
        }
        catch(Exception $e) {
            return false;
        }
    }*/

    /**
     * removeProductAction() method is use to remove session products
     * @param Null
     * @return Boolean
     */

    public function removeProductAction() {
        try {
            $product_id = Input::get('product_id');
            Session::forget('indentProductData.'.$product_id);
        }
        catch(Exception $e) {
            return false;
        }
    }

    /**
     * getIndentCode() method is use to get indent code
     * @param Null
     * @return String
     */
    
    private function getIndentCode($stateCode="TS") {
        // $refNoArr = DB::select(DB::raw("CALL prc_reference_no('TS', 'ID')"));  //to support master slave DB architecture
        $refNoArr = Utility::getReferenceCode('ID',$stateCode);

        return $refNoArr;
    }

    /**
     * createIndentAutomatic() - Automatic indent creation
     * @param Null
     * @return JSON
     */
    
    public function createAutoIndentAction(Request $request) {

        try{
            $date = $request->get('date');
            if(isset($date)) {
                $fromDate = $date.' 00:00:00';
            }
            else {
                $fromDate = date('Y-m-d').' 00:00:00';
            }
            
            $toDate = date('Y-m-d').' 23:59:59';
            $ordersArr = $this->_orderModel->getOrders($fromDate, $toDate);
            //echo "<pre>";print_r($ordersArr);die;
           
            if(is_array($ordersArr) && count($ordersArr) <= 0) {
                return View::make('Indent::autoindent')->with('error', 'No Order Available');
                //return Response::json(array('status'=>404, 'message'=>'No Order Available'));
            }
           
            $finalIndentsArr = array();
            if(is_array($ordersArr) && count($ordersArr) > 0) {
               foreach ($ordersArr as $order) {
                    $suppliers = $this->_Indent->getSupplierWHId($order->product_id);
                    if(isset($suppliers->supplier_id) && $suppliers->supplier_id > 0) {
                        $finalIndentsArr[$suppliers->supplier_id]['le_wh_id'] = $suppliers->le_wh_id;
                        $finalIndentsArr[$suppliers->supplier_id]['items'][] = $order;
                    }                    
                } 
            }
            
            //echo "<pre>";print_r($finalIndentsArr);die;

            if(is_array($finalIndentsArr) && count($finalIndentsArr) > 0 ) {
                $sucessIndent = array();
                foreach ($finalIndentsArr as $supplierId => $prodsArr) {
                    $le_wh_id = $finalIndentsArr[$supplierId]['le_wh_id'];
            $query = DB::table('legal_entities as legal')
                        ->leftjoin('zone as zn','zn.zone_id','=','legal.state_id')
                        ->where('legal_entity_id',$supplierId)
                        ->first();
            $stateCode = isset($query->code)?$query->code:"TS"; 
                    $indent_code = $this->getIndentCode($stateCode);

                    $indentArr = array('indent_date'=>date('Y-m-d H:i:s'),
                                            'indent_type'=>0,
                                            'created_by'=> Session('userId'),
                                            'indent_code'=>$indent_code,
                                            'le_wh_id'=>$le_wh_id,
                                            'indent_status'=>'70001',
                                            'legal_entity_id'=>$supplierId
                                            );
                   $indentId = $this->_Indent->saveIndent($indentArr);
                   
                   if($indentId > 0 && is_array($prodsArr['items']) && count($prodsArr['items']) > 0 ) {
                        $products = array();
                        $orderIds = array();
                        $totQty = 0;
                        foreach ($prodsArr['items'] as $product) {
                            $products[$product->product_id] = array(
                                                'indent_id'=>$indentId,
                                                'gds_order_id'=>$product->gds_order_id,
                                                'product_id'=>$product->product_id,
                                                'pname'=>$product->pname,
                                               // 'qty'=>$product->qty,
                                                'mrp'=>$product->mrp,
                                                'price'=>$product->price,
                                                'cost'=>$product->cost,
                                                'upc'=>$product->upc,
                                                'sku'=>$product->sku
                                                );
                            $qty = (isset($products[$product->product_id]['qty']) ? $products[$product->product_id]['qty'] : 0);
                            $totQty = $totQty + $product->qty;
                            $products[$product->product_id]['qty'] = $totQty;
                            
                            $orderIds[] = $product->gds_order_id;                        
                        }
                        //print_r($products);
                        $this->_Indent->saveIndentProducts($products);
                       
                       /**
                        * Update order is_indent
                        */
                       
                        if(count($orderIds)) {
                            $this->_orderModel->updateOrder($orderIds, array('is_indent'=>1));
                        }                    
                        
                        /* 
                        Send notification message
                        */
                        
                        Notifications::addNotification(['note_code' => 'IND0001','note_message'=>Lang::get('indent.notificationMsg'), 'note_priority' => 1, 'note_type' => 1, 'note_params' => ['INDID' => $indent_code], 'note_link' => '/indents/detail/'.$indentId]);
                        $sucessIndent[] = $indent_code;
                        //echo '<br>Indent #'.$indent_code.' Created Successfully';
                   }
                }

                return View::make('Indent::autoindent')->with('sucessIndent', $sucessIndent);
            }
            else {
                return View::make('Indent::autoindent')->with('error', 'No Order Available');
                //return Response::json(array('status'=>404, 'message'=>'No Order Available'));
            }
        }
        catch(Exception $e) {
            return View::make('Indent::autoindent')->with('error', 'No Order Available');
            //return Response::json(array('status'=>404, 'message'=>'No Order Available'));
        }     
    }

    public function getIndentCountinPo($indentId)
    {
        try {
            $indentCountInPo = $this->_po->getPoCountForAnIndent($indentId);
            return $indentCountInPo;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function createExportIndents(){
         try{
            $flag ='';
            $filterData = Input::get();
            $fdate = (isset($filterData['fromdate']) && !empty($filterData['fromdate'])) ? $filterData['fromdate'] : date('Y-m').'-01';
            $fdate = str_replace('/', '-', $fdate);
            $fromDate=  date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['todate']) && !empty($filterData['todate'])) ? $filterData['todate'] : date('Y-m').'-01';
            $date = str_replace('/', '-', $tdate);
            $TDate=  date('Y-m-d', strtotime($date)); 
            $dcID =$filterData['loc_dc_id'];
            $dcNames = implode(',',$dcID);
            if ($dcNames==0){
                $dcNames = 'NULL';
            }else{
                $dcNames =  "'".$dcNames."'";
            }
            $details = json_decode(json_encode($this->_Indent->getIndentOrderData_ByDC($fromDate,$TDate,$dcNames,$flag)), true);
            $data1=$details[0]['query'];
            $data2 = $details[0]['query1'];
            $Derivedheaders = array();
            $Actualheaders = array();
           foreach ($data2 as $key => $value) {
                foreach ($value as $inkey => $invalue) {
                    # code...
                array_push($Derivedheaders, $inkey);
                };
                break;
           }
           foreach ($data1 as $key => $value) {
                foreach ($value as $inkey => $invalue) {
                    # code...
                array_push($Actualheaders, $inkey);
                };
                break;
           }
            Excel::create('Indent_Template_Sheet - '. date('Y-m-d'),function($excel) use($data1,$data2,$Derivedheaders,$Actualheaders) {

                $excel->sheet('Actual Invoice Indent', function($sheet) use($data1,$Actualheaders) {
                $sheet->loadView('Indent::indentExport' ,array("indentsExport" => $data1,'Derivedheaders'=>$Actualheaders));

                });
                $excel->sheet('Derived Sale Loss', function($sheet) use($data2,$Derivedheaders) { 

                $sheet->loadView('Indent::indentExport' ,array("indentsExport" => $data2,'Derivedheaders'=>$Derivedheaders));

                });      
            })->export('xls');

        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }
    public function createStockitsIndents(){
         try{
            $filterData = Input::get();
            $fdate = (isset($filterData['fsdate']) && !empty($filterData['fsdate'])) ? $filterData['fsdate'] : date('Y-m').'-01';
            $fdate = date('Y-m-d', strtotime($fdate));
            $tdate = (isset($filterData['tsdate']) && !empty($filterData['tsdate'])) ? $filterData['tsdate'] : date('Y-m-d');
            $tdate = date('Y-m-d', strtotime($tdate));

            $details = json_decode(json_encode($this->_Indent->getIndentOrderDataConsolidate($fdate,$tdate)), true);      



            // $details = json_decode(json_encode($this->_Indent->getIndentOrderDataConsolidate($fdate,$tdate)), true);
            // Excel::create('Consolidate Indent Order- '. date('Y-m-d'),function($excel) use($details) {
            //     $excel->sheet('Consolidate Indent Order', function($sheet) use($details) {          
            //     $sheet->fromArray($details);
            //     });      
            // })->export('csv');
        }catch(Exception $e) {
                Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 404, 'Message' => Lang::get('salesorders.errorInputData')));
        }
    }

    public function deleteIndentAction(){

        $data=Input::all();

        $deleteIndent=$this->_Indent->indentDelete($data['indent_id']);

        return $deleteIndent;
    }
}
