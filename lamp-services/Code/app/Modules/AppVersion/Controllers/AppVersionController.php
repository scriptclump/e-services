<?php

namespace App\Modules\AppVersion\Controllers;

use App\Central\Repositories\RoleRepo;
use App\Http\Controllers\BaseController;
use App\Modules\AppVersion\Models\AppVersionModel;
use Session;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Request;
use Redirect;
use DB;
use Response;
use Illuminate\Support\Facades\Cache;

class AppVersionController extends BaseController{

    protected $appVersionObj;
    protected $roleAccess;

    public function __construct(RoleRepo $roleAccess, AppVersionModel $appVersionObj){
        try{
            parent::__construct();
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    return Redirect::to('/');
                }
                return $next($request);
            });
            $this->appVersionObj = $appVersionObj;
            $this->roleAccess = $roleAccess;

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

    public function index()
    {
        try
        {
            parent::Breadcrumbs(array('Home' => '/', 'Administration' => '#', trans('app_version.heading.index_page_title') => '#'));
            $addPermission = false;
            $addPermission = $this->roleAccess->checkPermissionByFeatureCode('APP003');
            return View::make('AppVersion::index')->with(array("addPermission" => $addPermission));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function versionlist(Request $request)
    {
        try
        {
            $content = $this->appVersionObj->getAppVersionList();
            $editPermission = false;
            $editPermission = $this->roleAccess->checkPermissionByFeatureCode('APP002');
            
            foreach ($content as $con) {
                $con["released_date"] = date('d-m-Y H:i:s', strtotime($con["released_date"]));
            }
            
            return $content;
            // return ["Records" => $content,"TotalRows" => $count];

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function addVersion(Request $request)
    {
        try
        {
            $data = Input::all();
            if(is_numeric($data["version_number"]) && $data["version_number"] >= 0) // check the validity of Version Number
            {
                $released_date = date_parse($data["released_date"]);
                if ($released_date["error_count"] == 0 && checkdate($released_date["month"], $released_date["day"], $released_date["year"]))        // Checking the validity of the Released Date
                {
                    if((!empty($data["version_name"])) and (!empty($data["version_number"])) and (!empty($data["app_type"])) and (!empty($data["released_date"])))      // Checking wheather all fields are empty or not
                    {
                        $checkAppType = false;
                        $checkAppType = $this->appVersionObj->checkAppType($data["app_type"]);

                        if($checkAppType)
                            return Redirect::back()->withErrors(array("message" => trans('app_version.validation.duplicate').$data['app_type']));

                        $status = $this->appVersionObj->addAppVersion($data["version_name"],$data["version_number"],$data["app_type"],$data["released_date"]);

                        if($status)
                        {
                            return Redirect::back()->withErrors(["message" => trans('app_version.validation.success')]);
                        }
                        else
                            return Redirect::back()->withErrors(["message" => trans('app_version.validation.add_version_failed')]);
                    }
                    else
                        return Redirect::back()->withErrors(["message" => trans('app_version.validation.default')]);
                }
                else
                {
                    return Redirect::back()->withErrors(["released_date" => trans('app_version.validation.released_date_invalid')]);
                }
            }
            else
                return Redirect::back()->withErrors(["version_number" => trans('app_version.validation.version_number_invalid')]);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateVersion()
    {
        try{
            $data = Input::all();
            if(is_numeric($data["version_number"]) && $data["version_number"] >= 0 && $data["version_id"] > 0)
            {
                $status = $this->appVersionObj->updateAppVersion($data["version_number"],$data["version_id"],$data["version_name"],$data["released_date"],$data["app_type"]);
                if($status)
                    return Redirect::back()
                        ->withErrors([
                            "message" => trans('app_version.validation.updated')
                        ]);
            }
            else
                return Redirect::back()
                    ->withErrors([
                        "version_number" => trans('app_version.validation.version_number_invalid')
                    ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function editVersion($version_id = null)
    {
        try
        {
            $version_id = intval($version_id);
            if($version_id>0)
            {
                $result = $this->appVersionObj->getCurrentAppVersionInfo($version_id);
                // $result = json_decode(json_encode($result),true);
                // $result[0]["released_date"] = date('d-m-Y', strtotime($result[0]["released_date"]));

                $date  = strtotime($result->released_date);
                $day   = date('d',$date);
                $month = date('m',$date);
                $year  = date('Y',$date);
                $hour  = date('H',$date);
                $minute  = date('i',$date);
                $second  = date('s',$date);

                $released_date = $year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

                // <!-- 2014-01-02T12:42:13.509 -->
                // 2017-03-24 14:06:22

                return [
                    "version_id" => $version_id,
                    "app_type" => $result->app_type,
                    "version_name" => $result->version_name,
                    "version_number" => $result->version_number,
                    "released_date" => $released_date,
                ];
            }
            else
                return Redirect::back()
                    ->withErrors([
                        "version_number" => trans('app_version.validation.version_number_invalid')
                    ]);

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
}