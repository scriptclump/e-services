<?php
namespace App\Modules\Reports\Controllers;
use App\Http\Controllers\BaseController;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Excel;
use Session;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Modules\Reports\Models\powerBIModel;

class powerBIController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return \Redirect::to('/');
                }
                return $next($request);
            });	
            parent::Title('Power BI Dashboard');
            $breadCrumbs = array('Home' => url('/'), 'Reports' => '', 'Power BI Dashboard' => url('powerbis'));
            parent::Breadcrumbs($breadCrumbs);
		  } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getPowerBIUrls($id){
        $get_id = isset($id)?$id:'';
        $get_data = powerBIModel::select('*')->where('feature_code',$get_id)->first();
        $description = $get_data['description'];
        $urls = $get_data['pbi_url'];
        $src = '" '.$urls.'" style="width:100%;height:100%"';
        return View::make("Reports::powerBI")->with(['src'=>$src,'description'=>$description]);
    }
}   
