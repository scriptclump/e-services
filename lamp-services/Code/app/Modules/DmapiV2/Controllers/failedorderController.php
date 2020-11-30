<?php
namespace App\Modules\DmapiV2\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use Event;
use DB;
use File;
use View;
use Session;
use Response;
use Redirect;
use Illuminate\Http\Request;
use App\Events\DashboardEvent;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use App\Central\Repositories\ReportsRepo;
use App\Central\Repositories\RoleRepo;
use App\Modules\Cpmanager\Models\OrderapiLogsModel;
use App\Modules\Cpmanager\Models\OrderModel;
use Config;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;


class failedorderController extends BaseController{
    protected $roleAccess;

	public function __construct(RoleRepo $roleAccess) {
        try{
            parent::Title(trans('dashboard.dashboard_title.company_name').' - '.'Failed Order');
            parent::__construct();
            $this->roleAccess = $roleAccess;
              // Code to Check Access
            $this->middleware(function ($request, $next) {
                if(!$this->roleAccess->checkPermissionByFeatureCode('FO01')){
                   return Redirect::to('/');
                }

                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry Invalid Login. Please check log for More Details";
        }
        $this->_Dmapiv2Model = new Dmapiv2Model();
    }


    public function failedorderinterface(){
       try
        {
            // Code to Check Access
            if(!$this->roleAccess->checkPermissionByFeatureCode('FO01')){
               return Redirect::to('/');
            }
            parent::Breadcrumbs(array('Home' => '/','Administration' => '#','Failed Order' => '/dmapi/v2/fo/failedorder'));
            $statusInfo = $this->_Dmapiv2Model->getstatusInfo();
            $statusInfo=json_decode(json_encode($statusInfo),1);
            return view::make('DmapiV2::failedorder')
                          ->with("statusInfo",$statusInfo);
        } catch (\ErrorException $ex) {
            return "Sorry, something went wrong. Please check logs for more details";
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    

    public function edit($id){ 
        if($id < 0 or $id != null){
            $data = $this->_Dmapiv2Model->getSingleRecord($id);
            if(!empty($data)){
                $result['status'] = true;
                $result['order_data'] = $data[0]->order_data;
                $result['processed'] = $data[0]->processed;
                $result['order_date'] = $data[0]->order_date;
                $result['order_code'] = $data[0]->order_code;
                $result['updated_by'] = $data[0]->updated_by;
                $result['legal_entity_id'] = $data[0]->legal_entity_id;
                return $result;
            }
        }
        return ["status"=>false];
    }

    public function failedorderlist(Request $request)
    {  
        $check=$request;
        $makeFinalSql = array();
        $filter = $request->input('%24filter');
        if( $filter == ''){
            $filter = $request->input('$filter');
        }
        $this->objCommonGrid=new commonIgridController();

        //make sql for Legal Entity Id 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("legal_entity_id",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Order Data 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("order_data",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Order Status 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("processed",$filter,false);
        $fieldQuery =str_replace('processed', 'getMastLookupValue(is_processed)', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Updated By 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("updated_by",$filter,false);
        $fieldQuery =str_replace('updated_by', 'GetUserName (updated_by, 2)', $fieldQuery);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Order Code 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("order_code",$filter,false);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //make sql for Order Date 
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("order_date",$filter,true);
        if($fieldQuery != ''){
            $makeFinalSql[] = $fieldQuery;
        }
        //Arrange data for sorting
        $orderBy = "";
        $orderBy = $request->input('%24orderby');
        if($orderBy==''){
            $orderBy = $request->input('$orderby');
        }
        // Arrange data for pagination
        $page="";
        $pageSize="";
        if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
            $page = $request->input('page');
            $pageSize = $request->input('pageSize');
        }
        $result = $this->_Dmapiv2Model->getfailedorderList($makeFinalSql,$orderBy,$page,$pageSize);
        $EditorderPermission = $this->roleAccess->checkPermissionByFeatureCode('FO02');
        try {
            $i = 0;
            foreach ($result['results'] as $record) {
                $failedorderRecordId = $result['results'][$i]->failed_order_id;
                $actions = '';
                if($record->processed=='Not Processed'){
                    if($EditorderPermission)
                    $actions.= '<span class="actionsStyle" ><a onclick="editfailedorderRecord('.$failedorderRecordId.')"</a><i class="fa fa-pencil"></i></span> ';
                }
                $result['results'][$i++]->actions = $actions;
            }
            return $result;
        } catch (Exception $e) {
            Log::error($e->getMessage()." ".$e->getTraceAsString());
            return ["Records" => [], "TotalRecordsCount" => 0];
        }
    }

    public function processFailedOrder(Request $request){
        $orderjsondata = $request->input("edit_order_data");
        $edit_failed_order_id = $request->input("edit_failed_order_id");
        $decode_order_data=json_decode($orderjsondata);
        $encoded_data=json_decode(json_encode($decode_order_data),1);
        $cus_mobile_no=$encoded_data['customer_info']['mobile_no'];
        $order_id=$encoded_data['payment_info'][0]['order_id'];
        $order_status=$this->_Dmapiv2Model->checkOrderStatus($cus_mobile_no,$order_id);
        if(count($order_status)>0){
           return json_encode(array('status'=>"failed",'message'=>'This order has been already processed','data'=>[])); 
        }
        $message='';
        foreach ($encoded_data['product_info']as  $key=>$product_details) {
            $timestamp = md5(microtime(true));
            $txtFileName = 'product-inv-status-' . $timestamp . '.html';
            $file_path = 'download' . DIRECTORY_SEPARATOR . 'inventorysoh_log' . DIRECTORY_SEPARATOR . $txtFileName;
            $files_to_delete = File::files('download' . DIRECTORY_SEPARATOR . 'inventorysoh_log/');
            File::delete($files_to_delete);
            $product_id= $this->_Dmapiv2Model->getProductInfo($product_details['sku']);
            $avail_qty= $this->_Dmapiv2Model->getProductInventory($product_details['le_wh_id'],$product_id);
            if($avail_qty<$product_details['quantity']){
               $message .="Inventory not found for product -  {$product_details['sku']} ";
               $message .='<br/>';
            }
            
        }
        $url = "";
        if($message!=''){
            if(isset($file_path)){
                $file = fopen($file_path, "w");
                fwrite($file, $message);
                fclose($file);
                $url = $file_path;
                $message = "Click <a href=".'/'.$file_path." target='_blank'> here </a> to view details.";
            }
            Session::flash('test', $message);
            return json_encode(array('status'=>"failed",'message'=>$message,'data'=>[]));
        }
        $order_data_req = $orderjsondata; 
        $logs_aray['order_req']=$order_data_req;
        $data['parameters'] = $logs_aray;
        $data['apiUrl'] = 'orderlogs';
        $this->order = new OrderModel(); 
        $this->_orderapi = new OrderapiLogsModel();
        $this->_orderapi->OrderApiRequests($data); 
        $HostUrl=$this->order->getHostURL();                
        $url= 'http://'.$HostUrl.'/dmapi/v2/placeorder'; 

        $det= array();
        $det['api_key'] = Config::get('dmapi.GDSAPIKey');
        $det['secret_key'] = Config::get('dmapi.GDSAPISECRETKey');
        $det['orderdata']=$order_data_req;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST, sizeof($det));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $det);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        if(isset($httpcode) && !empty($httpcode)) 
        {
            if($httpcode != 200 ) {                 
                return json_encode(array('status'=>"failed",'message'=> "Internal Server Error",'data'=>[]));
            }else{
                // Is Processed Update
                if($edit_failed_order_id > 0 or $edit_failed_order_id != null){
                    $status = $this->_Dmapiv2Model->updateorder($edit_failed_order_id,$orderjsondata);
                    return json_encode(array('status'=>"success",'message'=> "Order processed",'data'=>[]));
                }
            }
        }else{
            return json_encode(array('status'=>"failed",'message'=> "No Response From Server!",'data'=>[]));
        }
    }

    public function updateOrderStatus(){
        $data = Input::all();
        $result['data'] = $data;
        $result['status'] = false;
        $result['status'] = $this->_Dmapiv2Model->updateOrderStatus($data);
        return $result;

    }

}

