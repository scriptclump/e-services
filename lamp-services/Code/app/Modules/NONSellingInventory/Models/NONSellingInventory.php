<?php
namespace App\Modules\NONSellingInventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;
use App\Central\Repositories\RoleRepo;
use Notifications;
use UserActivity;
use Utility;

class NONSellingInventory extends Model {

    public function getFieldForceUsers()
    {
        $array  = array();
            $sql = DB::table("gds_orders")->where("created_by", "!=", 0)->distinct('created_by')->pluck("created_by")->all();
            for($i=0; $i < sizeof($sql); $i++)
            {
                $rawdata = DB::select("select  GetUserName(".$sql[$i].", 2) as username");
                $rawdata1 = json_decode(json_encode($rawdata), true);
                $array [$sql[$i]] = $rawdata1[0]['username'];
            }
        return $array;
    }

    public function getAllPlaces()
    {
        $array = array();
        $sql = DB::table("gds_orders_addresses")->distinct("area")->pluck("area")->all();
        
        for($i=0;$i < sizeof($sql); $i++)
        {
            
            $query = DB::table("cities_pincodes")->where("city_id", "=", $sql[$i])->pluck('officename')->all();
            if(!empty($query))
            {
                $array [$sql[$i]] = $query[0];    
            }
            
        }
        return $array;
        
    }

    public function getFilteredResults($filters)
    {
        $result = array();
        $users = 0;
        $areaa = 0;
        $startDate = date('Y-m-d', strtotime($filters['startdate']));
        $endDate = date('Y-m-d', strtotime($filters['enddate']));
        $fieldforceusers = $filters['fieldforceuser'];
        
        if($fieldforceusers!="")
        {
            $users  = implode(",", $fieldforceusers);    
        }
        
        $Placee = $filters['area'];
        if($Placee !="")
        {
            $areaa = implode(",", $Placee);    
        }
        $sql = DB::select("call getUnbilledSKUList('".$startDate."','".$endDate."','".$users."','".$areaa."')");
        $result['res'] = json_decode(json_encode($sql), true);
        $size = sizeof($result['res']);
        $result['count'] = $size;

        return $result;
    }


}
