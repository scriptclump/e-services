<?php

namespace App\Modules\HubInventory\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
Class HubInventory extends Model
{        
    public function getHubInventory($bu)
    {
        $reports = DB::select("call getInvByHub('".$bu."')");
        if (count($reports) > 0)
        {
            return $reports;
        }
        else
        {
            return array();
        }
    }
    
    public function excelReports($bu) {
        $reports = $this->getHubInventory($bu);    
        $data = json_decode(json_encode($reports),true);        
        return $data; 
    }
    
    public function getHubs($hubsData)
    {
        $hublist = '<option value="">Please Select Hub ...</option> ';
        $hublistObj = DB::table('legalentity_warehouses')->where('dc_type',118002)->whereIn('le_wh_id',$hubsData)->select('le_wh_id','lp_wh_name')->get()->all();
        foreach($hublistObj as $hub)
        {
            $hublist .= '<option value="'.$hub->le_wh_id.'">'.$hub->lp_wh_name.'</option> ';
        }
        return $hublist;
    }
    public function getOrderItems($bu,$pid)
    {
        $reports = DB::select("call getOrderInvByHub('".$bu."',".$pid.")");
        if (count($reports) > 0)
        {
            return $reports;
        }
        else
        {
            return array();
        }
    }
}
