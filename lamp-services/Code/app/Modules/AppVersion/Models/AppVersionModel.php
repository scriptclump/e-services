<?php
namespace App\Modules\AppVersion\Models;
use App\Central\Repositories\RoleRepo;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class AppVersionModel extends Model {

    protected $roleAccess;
    
    public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
    }

    public function getCurrentAppVersionInfo($version_id = null)
    {
        if(!empty($version_id))         // If the request is to edit the version data
        {
            $result=DB::table("app_version_info")
                        ->select('version_id','version_name','version_number','app_type','released_date')
                        ->where('version_id','=',$version_id)
                        ->first();
            
            return $result;
        }
        else
            return 0;
    }

    public function checkAppType($app_type = null)
    {
        if($app_type)
        {
            // DB::enableQueryLog();
            $result = DB::table('app_version_info')
                        ->where('app_type',$app_type)
                        ->count();
            // Log::info(DB::getQueryLog());
            return $result;   
        }
        return false;
    }

    public function getAppVersionList()
    {
        $result = DB::table("app_version_info")
                        ->select('version_id','version_name','version_number','app_type','released_date')
                        ->get()->all();

        $finalResultArr = [];
        $count = 0;
        foreach ($result as $record) {
            
            $count++;
            $currentId = $record->version_id;
            $status = null;
            $status = DB::table("app_version_history")
                        ->select('version_pre_name','version_pre_number','pre_released_date')
                        ->where([['version_id','=',$currentId],['app_type','=',$record->app_type],])
                        ->orderBy('version_history_id','desc')
                        ->get()->all();
            
            $record->version_id = intval($record->version_id);
            $editPermission = false;
            $editPermission = $this->roleAccess->checkPermissionByFeatureCode('APP002');
            if($editPermission)
                $record->actions = '<span style="padding-left:20px;" ><a href="javascript:void(0);" onclick="editAppVersion('.$record->version_id.')"><i class="fa fa-pencil"></i></span>';
            $record->childs = -1;
            
            $finalResultArr[] = $record;
            if($status != null)             // If there is child
            {
                $subArr = [];
                $subCount = 100;
                foreach ($status as $subRecord) {
                    $count++;
                    $subArr[] = [
                        "version_id" => $subCount++,        
                        "version_name" => $subRecord->version_pre_name,
                        "version_number" => $subRecord->version_pre_number,
                        "app_type" => null,
                        "released_date" => $subRecord->pre_released_date,
                        "actions" => null,
                        "childs" => intval($currentId),
                    ];
                }
                // $record->childs = $subArr;
                if(isset($subArr))
                    $finalResultArr = array_merge($finalResultArr,$subArr);
            }
            
        }
        return json_decode(json_encode($finalResultArr),true);

        // $countResult = DB::table("app_version_info")->count();
        // return ["Records" => $finalResultArr,"TotalRows" => $count];
        // return ["Records" => ,"TotalRows" => $countResult];
    }

    public function addAppVersion($version_name,$version_number,$app_type,$released_date)
    {
        $status = 
            DB::table('app_version_info')
                ->insert([
                    'version_name' => $version_name,
                    'version_number' => $version_number,
                    'app_type' => $app_type,
                    'released_date' => $released_date,
                ]);
        return $status;
    }

    public function updateAppVersion($version_number = -1,$version_id = 0,$version_name = null,$released_date = null, $app_type)
    {
        if($version_number >= 0 && $version_id > 0 && $released_date != null && $version_name != null)
        {
            // Collecting the old values below,,,
            $result = DB::table('app_version_info')
                            ->select('version_id','version_number','version_name','released_date')
                            ->where([['version_id', $version_id],['app_type', $app_type],])
                            ->first();

            // Inserting them into the History Table
            $history_status =   DB::table('app_version_history')
                                ->insertGetId([
                                    'version_id' => $result->version_id,
                                    'app_type' => $app_type,
                                    'version_pre_number' => $result->version_number,
                                    'version_pre_name' => $result->version_name,
                                    'pre_released_date' => $result->released_date
                                    ]);

            // Updating the new data in to the main table
            $info_status = DB::table('app_version_info')
                            ->where([['version_id', $version_id],['app_type', $app_type],])
                            ->update([
                                'version_number' => $version_number,
                                'version_name' => $version_name,
                                'released_date' => $released_date
                                ]);

            return ($info_status && $history_status);
        }
        else
            return 0;
    }
}