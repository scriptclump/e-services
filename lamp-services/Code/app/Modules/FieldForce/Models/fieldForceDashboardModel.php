<?php
//defining namespace

namespace App\Modules\FieldForce\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\FieldForce\Models\fieldForceDashboardModel;
use DB;
use Session;
use UserActivity;

class fieldForceDashboardModel extends Model
{
    protected $table = 'ff_target';
    protected $primaryKey = "ff_target_id";

    public function showfieldforceDetails($makeFinalSql, $statusFilter, $orderBy, $page, $pageSize){

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;

        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }

        if($statusFilter!=''){
            if($sqlWhrCls==''){
                $statusFilter = "WHERE " . $statusFilter;   
            }else{
                $statusFilter = " AND " . $statusFilter;
            }
        } 

         $sqlQuery ="select * FROM (select usr.user_id, concat(usr.firstname, ' ', usr.lastname) as 'FFFullName', usr.mobile_no, usr.reporting_manager_id,
                    (select concat(u.firstname, ' ', u.lastname) from users as u where u.user_id=usr.reporting_manager_id limit 1 ) as 'RMName',
                    CONCAT('<center><code>', '<a href=\"javascript:void(0)\" onclick=\"updateDetailsData(',usr.user_id,')\">
                        <i class=\"fa fa-cogs\"></i>
                        </a>
                        </code>
                        </center>') AS 'actions',
                    group_concat(pjp.pjp_name) as 'BeatName'
                    FROM
                      users AS usr 
                      INNER JOIN user_roles AS rl ON usr.user_id = rl.user_id
                      LEFT JOIN pjp_pincode_area as pjp on pjp.rm_id=usr.user_id
                    WHERE rl.role_id IN (52,53) 
                    group by usr.user_id 
                    ) as innertbl ".$sqlWhrCls . $statusFilter .$orderBy;
       $allData = DB::select(DB::raw($sqlQuery));
       return json_encode(array('results'=>$allData));
    }

    public function getLoadMascatData(){
        $getDetails = DB::table('master_lookup')
                            ->where('mas_cat_id', '=', '114')
                            ->where('is_active', '=', '1')
                            ->orderBy('sort_order')
                            ->get()->all();
        return $getDetails;
    }

    public function deleteFieldForceData($deleteData){

        // get OLD Data
        $fieldforceData = DB::table("ff_target")
                ->where('ff_target_id', '=', $deleteData)
                ->first();
        $fieldforceData = array(
                'OLDVALUES'         =>  json_decode(json_encode($fieldforceData)),
                'NEWVALUES'         => 'Deleted data',
            );

        $fieldforceData = DB::table("ff_target")
                ->where('ff_target_id', '=', $deleteData)
                ->delete();
        UserActivity::userActivityLog('FieldForce', $fieldforceData, 'Fieldforce Data Deleted by the User');
        return $fieldforceData;
    }

    public function loadFieldForceTarget($targetValue,$userid){

        $today=date('Y-m-d');
        $weeklydate = date("Y-m-d", strtotime("+1 week"));
        $monthlydate = date("Y-m-d", strtotime("+1 month"));

        // get Actual Param Name
        $getActualName = DB::table("master_lookup")
                        ->where("value","=",$targetValue)
                        ->first();
        $getActualName = $getActualName->description;


        // get Existing Table from for the user
        $existingData = DB::select(DB::raw("select * from ff_target where target_name_id='".$targetValue."' and ff_user_id='" . $userid . "' limit 1"));

        // Prepare an Array
        $ffTargetArray = array();
        //to get the daily value
        $sqlDaily = DB::select("call getOrdersDashboard('".$userid."',1,'".$today."','".$today."')");
        $sqlDaily = json_decode( json_encode($sqlDaily), true);
        $ffTargetArray['daily'][0] = isset($sqlDaily[0][$getActualName]) ? $sqlDaily[0][$getActualName] : 0;
        $ffTargetArray['daily'][1] = $existingData ? $existingData[0]->target_daily : 0;

        // to get weekly value
        $sqlweekly = DB::select("call getOrdersDashboard('".$userid."',1,'".$today."','".$weeklydate."')");
        $sqlweekly = json_decode( json_encode($sqlweekly), true);
        $ffTargetArray['weekly'][0] = isset($sqlweekly[0][$getActualName]) ? $sqlweekly[0][$getActualName] : 0;
        $ffTargetArray['weekly'][1] = $existingData ? $existingData[0]->target_weekly : 0;

        // to get monthly value
        $sqlmonthly = DB::select("call getOrdersDashboard('".$userid."',1,'".$today."','".$monthlydate."')");
        $sqlmonthly = json_decode( json_encode($sqlmonthly), true);
        $ffTargetArray['monthly'][0] = isset($sqlmonthly[0][$getActualName]) ? $sqlmonthly[0][$getActualName] : 0;
        $ffTargetArray['monthly'][1] = $existingData ? $existingData[0]->target_monthly : 0;

       return  $ffTargetArray;

    }

    public function getfieldforceDetails($ffid){
        $getfieldforcedetails = "select usr.firstname, mst.master_lookup_name, tg.*, '1' as 'dataFlag'
                                from ff_target as tg
                                inner join users as usr on usr.user_id=tg.ff_user_id
                                inner join master_lookup as mst on mst.value=tg.target_name_id
                                where tg.ff_user_id=".$ffid; 

        $allData = DB::select($getfieldforcedetails);

        if(count($allData)==0){
            $getfieldforcedetails = "select *, '0' as 'dataFlag' from users
                                where user_id=".$ffid; 
            $allData = DB::select($getfieldforcedetails);
        }

        $returnss= json_decode(json_encode($allData), true);

        return $returnss;
    }

    public function saveFieldForceData($fieldforcedata){

        $responseMSG="";

        $originalDate = isset($fieldforcedata['date']) && $fieldforcedata['date'] != '' ? $fieldforcedata['date'] : date('d/m/Y');
        $originaldate = str_replace('/', '-', $originalDate);
        $requesteddate = date("Y-m-d", strtotime($originaldate));

        $this->ff_user_id=$fieldforcedata['HiddenInputID'];
        $this->effective_date =  $requesteddate;
        $this->target_name_id = $fieldforcedata['mascat_target'];
        $this->target_daily = $fieldforcedata['daily'];
        $this->target_weekly = $fieldforcedata['weekly'];
        $this->target_monthly= $fieldforcedata['monthly'];
        $fielddata = DB::table("ff_target")
                    ->where('target_name_id','=',$fieldforcedata['mascat_target'])
                    ->where('ff_user_id','=',$fieldforcedata['HiddenInputID'])
                    ->count();
         
        if($fielddata>0){
        $responseMSG= 1;
        }else{
                    $this->save();
                    $oldData = array(
                    'NEWVALUES'         =>  json_decode(json_encode($fielddata))
                    );
                    UserActivity::userActivityLog('FieldForce', $oldData, 'Field Force Data Added by the User');
                    $responseMSG= 0;
        }
        return $responseMSG;
    }

    public function fieldForceDetails(){
        $getDetails = DB::table('ff_target')
                        ->join('master_lookup', 'ff_target.target_name_id', '=', 'master_lookup.value')->get()->all();
        return $getDetails;
    }

    public function getUserDetailsWithId($ffid){
        $getDetails = DB::table('ff_target')
                        ->join('master_lookup', 'ff_target.target_name_id', '=', 'master_lookup.value')
                        ->where('ff_user_id','=',$ffid)
                        ->get()->all();
        return $getDetails;
    }
}
