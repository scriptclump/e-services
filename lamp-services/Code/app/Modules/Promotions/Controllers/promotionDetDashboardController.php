<?php
/*
FileName : promotionDetDashboardController
Author   : eButor
Description :
CreatedDate :07/Sept/2016
*/
//defining namespace
namespace App\Modules\Promotions\Controllers;
//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\Promotions\Controllers\commonIgridController;
use App\Modules\Promotions\Models\promotionDetailsDashboardModel;
use App\Modules\Promotions\Models\slabDetailsModel;
use App\Modules\Promotions\Models\promotionDayTimeModel;
use App\Modules\Promotions\Models\AddpromotionModel;
use App\Modules\Promotions\Models\AddPromotionBundleQuantity;
use  App\Modules\Promotions\Models\cashBackModel;
use App\Modules\Promotions\Models\freeQtyModel;
use  App\Modules\Promotions\Models\tradeDiscountModel;
use App\Central\Repositories\RoleRepo;
use Illuminate\Http\Request;
use Redirect;
use Input;
use Log;
use Session;
use Notifications;
use Carbon\Carbon;
use Excel;
use DB;
class promotionDetDashboardController extends BaseController{

	public function __construct() {

        $this->_roleRepo = new RoleRepo();
        $this->promotion_dashboard = new promotionDetailsDashboardModel();
        $this->objCommonGrid = new commonIgridController();
        $this->add_promotion_request = new AddpromotionModel();
        $this->objSlabDetails = new slabDetailsModel();
        $this->objPromotionDayTime = new promotionDayTimeModel();
        $this->objPromotionBundleForQuantity = new AddPromotionBundleQuantity();
        $this->objCashBack = new cashBackModel();
        $this->objFreeQtyModel = new freeQtyModel();
        $this->tradeDiscount = new tradeDiscountModel();

        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                parent::Title('Promotions');

                $access = $this->_roleRepo->checkPermissionByFeatureCode('PRDS001');

                if (!$access && Session::get('legal_entity_id')!=0) {
                    Redirect::to('/')->send();
                    die();
                }
                return $next($request);
            });

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [viewPromotionDetails View Promotion details]
     * @return [view] [redirects to promotion grid]
     */
	public function viewPromotionDetails(){
        try{
            $breadCrumbs = array('Home' => url('/'),'Promotions' => '#');

            $getStateDetails = $this->promotion_dashboard->getStateDetailsDropdown();
            $getCustomerGroup = $this->promotion_dashboard->getCustomerGroupDropdown();

            $getManufactureDetails = $this->promotion_dashboard->getManufactureDetailsDropdown();
            $getBrandDetails = $this->promotion_dashboard->getBrandDetailsDropdown();
            
            // Setting default 1 to the access level
            $addPromotionAcess=1;
            $uploadAccess=1;

            if(Session::get('legal_entity_id')!=0){
                $addPromotionAcess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS003');
                $uploadAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS002');

            }

			return view('Promotions::viewPromotionDashboard',['getStateDetails'=>$getStateDetails, 'getCustomerGroup'=>$getCustomerGroup, 'getManufactureDetails'=>$getManufactureDetails, 'getBrandDetails'=>$getBrandDetails,'addPromotionAcess' =>$addPromotionAcess,'uploadAccess' =>$uploadAccess]);

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
          }
    }
    /**
     * [deletepromotiondetails Delete promotion]
     * @param  Request $request [promotion information]
     * @return [string]           [success/failure string]
     */
    public function deletepromotiondetails(Request $request){

        try{
            DB::beginTransaction();

            $deleteAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS005');
            
            if($deleteAccess){
                $deleteData = $request->input('deleteData');
                $delete_table1 = $this->add_promotion_request->deletepromotiondetails($deleteData);
                $this->objPromotionDayTime->deleteDetails($deleteData);
                $this->objSlabDetails->deleteSlabDetails($deleteData);
                //deleting data from promotion_cashback_details
                $this->objCashBack->deleteCashBackDetails($deleteData);
                $this->objFreeQtyModel->deleteFreePromotionDetails($deleteData);
                $this->tradeDiscount->deleteTradeDetails($deleteData);
                $this->objPromotionBundleForQuantity->deleteBundleQuantity($deleteData);
                Notifications::addNotification(['note_code' =>'PRM006']);
                DB::commit();
                return "Successfully deleted!";
            }else{
                return 'Illeagal access!';
            }

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            DB::rollback();
            Redirect::to('/')->send();
        }
  
    }

    //filterization for required data
    public function showpromotionDetails(Request $request){
        try{
            $makeFinalSql = array();
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }        
            // make sql for promotion name
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("prmt_det_name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
             // make sql for product information
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("ProductInformation", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for offer type
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("prmt_offer_type", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for offer value
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("prmt_offer_value", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for state
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("state_names", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for offer on
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("offer_on", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for promotion created
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for promotions ends on
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("end_date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

             // make sql for promotions starts on
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("start_date", $filter,true);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }
            // make sql for Active/Inactive
            $fieldQuery = $this->objCommonGrid->makeIGridToSQL("PrmtStatus", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // Process data for Status Filter
            $statusFilter = '';
            if($request->input('filterStatusType')!='all'){
                $statusFilter = $request->input('filterStatusType')!='' ? "status='".$request->input('filterStatusType')."'" : '';
            }

            $orderBy = "";
            $orderBy = $request->input('%24orderby');
            if($orderBy==''){
                $orderBy = $request->input('$orderby');
            }
            //edit and delete access for RBAC
            $editAccess=1;
            $deleteAccess=1;
            if(Session::get('legal_entity_id')!=0){
                $editAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS004');
                $deleteAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRDS005');
            }
            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }
            return $this->promotion_dashboard->showpromotionsDetails($makeFinalSql, $statusFilter, $orderBy, $page, $pageSize,$editAccess,$deleteAccess);
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
    /**
     * [getAllPromotionDetailsData Downloads Promtion export]
     * @param  Request $request []
     * @return [excel]           [Downloads excel sheet]
     */
     public function getAllPromotionDetailsData(Request $request){
        // get all the active and inactive promotion details
        $getAllThedata = $this->promotion_dashboard->getAllPromotionsActiveInactive();
        $getAllThedata = json_decode(json_encode($getAllThedata),true);

        $headers = array('Promotion Name','Start Date','End Date','Promotion det Status','offer Type','offer Value','Status','Product Name','Offer On','State Name','Promotion Id','Promotion Template Id','Created At');
        $mytime = Carbon::now();
         Excel::create('All Promotions Report Sheet-'.$mytime->toDateTimeString(), function($excel) use($headers, $getAllThedata) 
            {
                $excel->sheet("Allpromotions", function($sheet) use($headers, $getAllThedata)
                {
                    $sheet->loadView('Promotions::downloadAllPromotionsReport', array('headers' => $headers, 'data' => $getAllThedata)); 
                });
            })->export('xlsx');
    }
}