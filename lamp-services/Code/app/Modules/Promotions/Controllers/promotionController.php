<?php
/*
FileName :promotionController
Author   :eButor
Description :
CreatedDate :8/july/2016
*/
//defining namespace
namespace App\Modules\Promotions\Controllers;
//loading namespaces
use App\Http\Controllers\BaseController;
use App\Modules\Promotions\Controllers\commonIgridController;
use Illuminate\Support\Facades\Validator;
use App\Modules\Promotions\Models\PromotionModel;
use App\Central\Repositories\RoleRepo;
use Illuminate\Http\Request;
use Input;
use Session;
use DB;
use Redirect;
use Log;
use Notifications;

class promotionController extends BaseController{

    //calling model 
    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }

                parent::Title('Promotions');
                $this->_promotion_request = new PromotionModel();
                $this->_common_function = new commonIgridController();

                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
	
    /**
     * [promotionsIndex show the ignite UI index page ]
     * @return [view] [show the ignite UI index page]
     */
    public function promotionsIndex(){
        try{
            
            $this->_roleRepo = new RoleRepo();
            $breadCrumbs = array('Home' => url('/'),'Promotions' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $addAccess = $this->_roleRepo->checkPermissionByFeatureCode('PRMT002');
            return view('Promotions::index')->with(['addAccess' => $addAccess]);

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

    /**
     * [addPromotion show the view page for add promotion template]
     */
    public function addPromotion(){
        try {
            
            $breadCrumbs = array('Home' => url('/'),'Promotions' => '/promotions','Promotions Add' => '#');
            parent::Breadcrumbs($breadCrumbs);
            return view('Promotions::addPromotion', ['add_update_flag' => 'Add']);

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

    //
    /**
     * [updateData show the view page for update ]
     * @param  [int] $updateId [Promotion templare id]
     * @return [view]           [show the view page for update ]
     */
    public function updateData($updateId){
      try{
            $breadCrumbs = array('Home' => url('/'),'Promotions' => '/promotions/index','Promotions Update' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $update = $this->_promotion_request->getUpdateData($updateId); 
           // Notifications::addNotification(['note_code' =>'PRM003']);
            return view('Promotions::addPromotion',['add_update_flag' => 'Update', 'update' => $update]);
         }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        } 
    }

    /**
     * [savePromotion passing the savepromotion data to model]
     * @param  Request $request [promotion template information]
     * @return [view]           [promtion base view]
     */
    public function savePromotion(Request $request){
        try{    
            Notifications::addNotification(['note_code' =>'PRM002']);
            $promotionData = $request->input();
            $validator = Validator::make($request->all(),
                array(
                       'promotion_name'     => 'required',
                       'offertype'          => 'required',
                       'offeron'            => 'required',
                    )
                );

            if ($validator->fails()) {
                return redirect('/promotions/addpromotion')->withErrors($validator);
                }

            if( $this->_promotion_request->savePromotionData($promotionData) ){
                return redirect('/promotions');
            }else{
                return redirect('/promotions');
            }
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
  
    }

    /**
     * [promotionData filterization for required data]
     * @param  Request $request [ig grid i/p]
     * @return [Array]           [filtered grid data]
     */
    public function promotionData(Request $request){
        try{    
            $makeFinalSql = array();
            $filter = $request->input('%24filter');
            if( $filter=='' ){
                $filter = $request->input('$filter');
            }        

            // make sql for prmt_tmpl_name
            $fieldQuery = $this->_common_function->makeIGridToSQL("prmt_tmpl_name", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for offer_type
            $fieldQuery = $this->_common_function->makeIGridToSQL("offer_type", $filter);
            if($fieldQuery!=''){
                $makeFinalSql[] = $fieldQuery;
            }

            // make sql for offer_on
            $fieldQuery = $this->_common_function->makeIGridToSQL("offer_on", $filter);
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

            // Arrange data for pagination
            $page="";
            $pageSize="";
            if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
                $page = $request->input('page');
                $pageSize = $request->input('pageSize');
            }
            
            return $this->_promotion_request->viewPromotiondata($makeFinalSql, $statusFilter, $orderBy, $page, $pageSize);
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

    /**
     * [deleteData delete the template]
     * @param  Request $request [promotion template info]
     * @return [string]           [Promotion delete message]
     */
    public function deleteData(Request $request){
        try{
        
            Notifications::addNotification(['note_code' =>'PRM005']);
            $deleteData = $request->input('deleteData');
            return $this->_promotion_request->deleteData($deleteData);
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }

    /**
     * [updatewithId passing the promotionupdate data to model]
     * @param  Request $request [promotion template information]
     * @return [view]      
     */
    public function updatewithId(Request $request){
        try{    
            $data = $request->input();

            $validator = Validator::make($request->all(),
                array(
                       'promotion_name'   => 'required',
                       'offertype'        => 'required',
                       'offeron'          => 'required',
                    )
                );

            if ($validator->fails()) {
                return redirect('/promotions/addpromotion')->withErrors($validator);
            }

            if( $this->_promotion_request->updatePromotionData($data) ){
                return redirect('/promotions');
            }else{  
                return "something  error in this page";
            }

        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        }
    }
}