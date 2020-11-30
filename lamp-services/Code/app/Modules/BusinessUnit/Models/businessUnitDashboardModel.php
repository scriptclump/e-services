<?php
//defining namespace
namespace App\Modules\BusinessUnit\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\BusinessUnit\Models\businessUnitDashboardModel;
use DB;
use Session;
use UserActivity;

class businessUnitDashboardModel extends Model
{
    protected $table = 'business_units';
    protected $primaryKey = "bu_id";


    public function allBusinessUnits($userId=null){

        if(empty($userId)){
            $userId = Session::get('userId');
        }else{
            $userId=$userId;
        }
        $rawQuery = " SELECT GROUP_CONCAT(object_id) as object_id FROM `user_permssion` WHERE `user_id`=$userId and permission_level_id=6";
        $access =  DB::select(DB::raw($rawQuery));
        $access = isset($access[0]->object_id)?$access[0]->object_id:0;
        $access = explode(',', $access);
        $data = [];
        if(in_array(0, $access)){
            $query1 = "SELECT `bu_name`, `bu_id`, `parent_bu_id`, `description`, `is_active`, `cost_center`,`tally_company_name`,`sales_ledger_name`,getBusinessUnitName(parent_bu_id) AS parent_name FROM `business_units`";
            $data = DB::select(DB::raw($query1));
            $bu_id_exist = array_column(json_decode(json_encode($data),1), 'bu_id');
        }else{
            $businesData = DB::table("business_units")
                        ->select(['bu_name', 'bu_id', 'parent_bu_id', 'description', 'is_active', 'cost_center','tally_company_name','sales_ledger_name',DB::raw("getBusinessUnitName(parent_bu_id) AS parent_name")])

                        ->whereIn('bu_id', $access)
                        //->orWhereIn('parent_bu_id', $access)
                        ->get()->all();
            $bu_id_exist = [];
            $bu_id_exist = array_column(json_decode(json_encode($businesData),1), 'bu_id');
            $data = $businesData;
            foreach ($businesData as $key => $bu) {
                $parent_buid = $bu->parent_bu_id;
                while($parent_buid>0){
                    if(!in_array($parent_buid, $bu_id_exist)){
                        $businesData1 = $this->getBUDetails($parent_buid);
                        if(count($businesData1)>0 && isset($businesData1->bu_id)){
                            $data[]=$businesData1;
                            $bu_id_exist[] = $businesData1->bu_id;
                            $parent_buid = $businesData1->parent_bu_id;
                        }
                    }else{
                        $parent_buid=0;
                    }
                }
            }
        }
        $data = json_decode(json_encode($data),1);
        array_multisort($bu_id_exist,SORT_ASC,SORT_REGULAR,$data);
        return json_encode($data);
    }

    public function getBUDetails($buid){
        $businesData1 = DB::table("business_units")
                        ->select(['bu_name', 'bu_id', 'parent_bu_id', 'description', 'is_active', 'cost_center','tally_company_name','sales_ledger_name'])
                        ->where('bu_id', $buid)->first();
        return $businesData1;
    }
    public function getParentDetails(){
        $getParentData = DB::table('business_units')
                        ->get()->all();

        return $getParentData;
    }
	public function saveBusinessData($businessdata,$businessId){
		$responseMSG="";
		$entity_id = Session::get('legal_entity_id');
        $userId = Session::get('userId');
		$this->bu_name = $businessdata['business_name'];
		$this->description = $businessdata['description'];
		$this->legal_entity_id = $entity_id;
        $this->cost_center = $businessdata['cost_center'];
        $this->tally_company_name = $businessdata['tally_company_name'];
        $this->sales_ledger_name = $businessdata['sales_ledger_name'];
        $this->parent_bu_id = isset($businessdata['parent_id']) ? $businessdata['parent_id'] : 0; 

        $this->is_active = $businessdata['status'];

        if($businessId!=0){

            // Geting the OlbValue
            $businesOldData = DB::table("business_units")
                        ->where('bu_id',"=", $businessId)
                        ->first();
            // Check for duplicate Business Name, if it is changed
            $dataExistFlag = 0;
            if($businesOldData->bu_name != $businessdata['business_name']){

                $businesUnitName = DB::table("business_units")
                        ->where('bu_name',"=", $businessdata['business_name'])
                        ->count();

                if($businesUnitName>0){
                    $responseMSG=3;
                    $dataExistFlag=1;
                }
            }

            // if Updated entry not exist
            if($dataExistFlag==0){
                // Update the table
                DB::table('business_units')
                    ->where('bu_id', '=', $businessId )
                    ->update(['bu_name' => $businessdata['business_name'],'description' => $businessdata['description'],'parent_bu_id'=>$businessdata['parent_id'],'is_active'=>$businessdata['status'],'cost_center'=>$businessdata['cost_center'],'tally_company_name'=>$businessdata['tally_company_name'],'sales_ledger_name'=>$businessdata['sales_ledger_name'],'legal_entity_id'=>$businessdata['legal_entity_bu']]);
                
                // Prepare User Activity Data
                $oldData = array(
                    'OLDVALUES'         =>  json_decode(json_encode($businesOldData)),
                    'NEWVALUES'         =>  $businessdata
                );
                UserActivity::userActivityLog('BusinessUnit', $oldData, 'BusinessData Updated by the User');
                $responseMSG= 2;
            }
        }else{
            $businesUnitName = DB::table("business_units")
                    ->where('bu_name',"=", $businessdata['business_name'])
                    ->count();
            if($businesUnitName>0){
                $responseMSG= 3;
            }else{            
        		$budata = $this->save();
                $buid = $this->bu_id;
                DB::table('user_permssion')->insert(['user_id' => $userId,'permission_level_id' => 6,'object_id'=>$buid]);
                $oldData = array(
                    'NEWVALUES'         =>  json_decode(json_encode($businessdata))
                );
                UserActivity::userActivityLog('BusinessUnit', $oldData, 'BusinessData Added by the User');
        		$responseMSG= 1;
            }
        }
        return $responseMSG;
    }


	public function getBusinessID($updateBusinessID){
		$sqlQuery = "select bu.bu_id, bu.bu_name, bu.description,bu.is_active, bu.parent_bu_id,bu.cost_center,bu.tally_company_name,bu.sales_ledger_name,bu.legal_entity_id
			FROM business_units AS bu
			where bu.bu_id='".$updateBusinessID."'";
        $allData = DB::select(DB::raw($sqlQuery));
        return $allData;
	}

    public function getLoadBusinessData(){

        $getLoadBusiness = DB::table('business_units')->get()->all();
        return $getLoadBusiness;

    }
    public function deleteBusinessTreeData($deleteData){

    	// get OLD Data
        $businessData = DB::table("business_units")
                ->where('bu_id', '=', $deleteData)
                ->first();
        $businessData = array(
                'OLDVALUES'         =>  json_decode(json_encode($businessData)),
                'NEWVALUES'         => 'Deleted data',
            );

		$deleteBusinessData = DB::table('business_units')
							->where('bu_id', '=', $deleteData)
							->delete();
		UserActivity::userActivityLog('BusinessUnit', $businessData, 'BusinessData Deleted by the User');
        return $deleteBusinessData;
	}
    public function updateBusinessName($hub_id,$name,$flag){
        $updateQuery=false;
        $fieldArr = array('bu.sales_ledger_name','bu.tally_company_name','bu.bu_id');
        $query = DB::table('business_units as bu')->select($fieldArr);
        $query->join('legalentity_warehouses as lw', function($joina){
                $joina->on('bu.legal_entity_id','=','lw.legal_entity_id');
                $joina->on('lw.bu_id','=','bu.bu_id');
            });
        $query->where('lw.le_wh_id','=',$hub_id);
        $buName = $query->first();          
       if(isset($buName) && !empty($buName)){
            if($flag==1){
                $updateQuery = DB::table("business_units")
                        ->where('bu_id', $buName->bu_id)
                        ->update(['tally_company_name' => $name]);
            }elseif($flag==2){
                $updateQuery = DB::table("business_units")
                            ->where('bu_id', $buName->bu_id)
                            ->update(['sales_ledger_name' => $name]);
            }
        } 
               
        return $updateQuery;

    }
}