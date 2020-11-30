<?php

namespace App\Modules\CustomerFeedback\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
Class CustomerFeedback extends Model
{

    	protected $table = "customer_feedback";
	protected $primaryKey = 'fid';
        
    public function getCustomerFeedback()
    {
        $reports = DB::table('customer_feedback')->select('fid','comments','audio','picture',DB::raw('getBusinessLegalName(legal_entity_id)  AS legal_entity'),DB::raw('GetUserName(created_by,2)  AS created_by'),'created_at',DB::raw('getMastLookupValue(feedback_type)  AS feedback_type'), DB::raw('getMastLookupValue(feedback_group_type)  AS feedback_group_type'))->orderBy('created_at','desc')->get()->all();

        if (count($reports) > 0)
        {
            return $reports;
        }
        else
        {
            return array();
        }
    }
    
    public function excelReports() {
        $reports = $this->getCustomerFeedback();    
        $data = json_decode(json_encode($reports),true);        
        return $data; 
    }

}
