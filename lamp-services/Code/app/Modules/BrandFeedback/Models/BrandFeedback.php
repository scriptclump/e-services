<?php
namespace App\Modules\BrandFeedback\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;
use Session;


class BrandFeedback extends Model
{
    protected $roleAccess;
    public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
    }

    public function updateBrandFeedback($data, $userId){
        $query = 'UPDATE brand_feedback
                  SET
                    status = ?,
                    updated_by = ?,
                    updated_at = ?,
                    comments = ?,
                    assignee = ?
                WHERE
                    brand_feedback_id = ?';

        $result = DB::UPDATE($query,[
            $data['status'],            
            $userId,
            date('Y-m-d H:i:s'),
            $data['comments'],
            $data['assignee'],
            $data['brand_feedback_id']
        ]);
        return true;
    }

    public function getSingleRecord($id){
        $query = 'SELECT 
					'.DB::raw('GetUserName(bfb.ff_id,2) AS sales_rep').',
					rf.`business_legal_name` AS shop_name, 
					rf.`city`, 
					rf.`state`,
					rf.`state_id`,
					rf.`latitude`,
					rf.`longitude`,
					'.DB::raw("ROUND(bfb.buying_price,2) as buying_price").', 
					'.DB::raw("ROUND(bfb.selling_price,2) as selling_price").', 
					'.DB::raw("ROUND(bfb.weekly_sales_value,2) as weekly_sales_value").',
					bfb.feedback_picture AS image, 
					bfb.status,
					bfb.comments,
					'.DB::raw('GetUserName(bfb.created_by,2) AS created_by').',
					bfb.created_at,
					'.DB::raw('GetUserName(bfb.updated_by,2) AS updated_by').',
					bfb.`updated_at`,
                    bfb.`assignee`,
					rf.beat,
					fc.dc_name
					FROM  brand_feedback AS bfb
					LEFT JOIN retailer_flat AS rf ON (rf.legal_entity_id = bfb.`retailer_le_id`)
					LEFT JOIN dc_hub_mapping AS fc ON (fc.`hub_id` = rf.`hub_id`)
					WHERE bfb.brand_feedback_id = ?';        
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function deleteBrandFeedback($id){
        $query = 'DELETE FROM brand_feedback WHERE brand_feedback_id = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }

    public function getBrandFeedbackList($makeFinalSql, $orderBy, $page, $pageSize){
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        else{
            $orderBy = ' ORDER BY bfb.brand_feedback_id desc';
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }

        //////////////////////////////////////
        $rolesObj   = new Role();
        $userid     = Session::get('userId');
        $Json       = json_decode($rolesObj->getFilterData(6,$userid), 1);
        $filters    = json_decode($Json['sbu'], 1);            
        $DcId       = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $DcId       = explode(',',$DcId);
        array_push($DcId,0);

        // Get the legal entity ids w.r.t dc ids
        $legalEntityIdArray = array();
        $child_legal_entity_id = DB::table('legalentity_warehouses')->distinct()->select('legal_entity_id')->whereIn('le_wh_id' , $DcId)->get()->all();
        foreach ($child_legal_entity_id as $val) {
            $legalEntityIdArray[] = $val->legal_entity_id;
        }
        $sqlWhrCls .= " AND lw.`legal_entity_id` IN (".implode(",", $legalEntityIdArray).")";

        if($sqlWhrCls!="")
        {
            $sqlWhrCls .= " AND lw.`legal_entity_id` IN (".implode(",", $legalEntityIdArray).")";
        }
        else
        {
            $sqlWhrCls .= " WHERE lw.`legal_entity_id` IN (".implode(",", $legalEntityIdArray).")";
        }
        //////////////////////////////////////

        $query = "SELECT 
                    bfb.brand_feedback_id,
                    GetUserName(bfb.ff_id,2) AS sales_rep,
                    rf.`business_legal_name` AS shop_name, 
                    rf.`city`, 
                    rf.`state`,
                    rf.`state_id`,
                    rf.`latitude`,
                    rf.`longitude`,
                    ".DB::raw('ROUND(bfb.buying_price,2) as buying_price').", 
                    ".DB::raw('ROUND(bfb.selling_price,2) as selling_price').", 
                    ".DB::raw('ROUND(bfb.weekly_sales_value,2) as weekly_sales_value').", 
                    bfb.feedback_picture AS image, 
                    ".DB::raw("IFNULL(getMastLookupValue(bfb.status),'Open') AS `status`").",
                    bfb.comments,
                    GetUserName(bfb.created_by,2) AS created_by ,
                    DATE_FORMAT(bfb.created_at, '%d-%m-%Y %H:%i:%s') AS created_at,
                    GetUserName(bfb.updated_by,2) AS updated_by ,
                    DATE_FORMAT(bfb.`updated_at`, '%d-%m-%Y %H:%i:%s') AS updated_at,
                    rf.beat,
                    lw.`display_name` AS dc_name,
                    GetUserName(bfb.assignee,2) AS assignee
                FROM  brand_feedback AS bfb
                JOIN retailer_flat AS rf ON (rf.legal_entity_id = bfb.`retailer_le_id`)
                JOIN legal_entities lw ON (rf.parent_le_id=lw.legal_entity_id)". $sqlWhrCls . $orderBy ;
        // echo $query;exit;
        $allRecallData = DB::select(DB::raw($query));
        $TotalRecordsCount = count($allRecallData);
        if($page!='' && $pageSize!=''){
            $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
            $allRecallData = array_slice($allRecallData, $page, $pageSize);
        }
        $arr = array('results'=>$allRecallData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount)); 
        return $arr;        
    }

    public function getFeedbackExportDetails($from_date, $to_date)
    {

        $orderBy = " ORDER BY bfb.brand_feedback_id desc";
        $sqlWhrCls = " WHERE DATE(bfb.created_at) BETWEEN '$from_date' AND '$to_date'";
        
        //////////////////////////////////////
        $rolesObj   = new Role();
        $userid     = Session::get('userId');
        $Json       = json_decode($rolesObj->getFilterData(6,$userid), 1);
        $filters    = json_decode($Json['sbu'], 1);            
        $DcId       = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $DcId       = explode(',',$DcId);
        array_push($DcId,0);

        if($sqlWhrCls!="")
        {

            $legalEntityIdArray = array();
            $child_legal_entity_id = DB::table('legalentity_warehouses')->distinct()->select('legal_entity_id')->whereIn('le_wh_id' , $DcId)->get()->all();
            foreach ($child_legal_entity_id as $val) {
                $legalEntityIdArray[] = $val->legal_entity_id;
            }
            $sqlWhrCls .= " AND lw.`legal_entity_id` IN (".implode(",", $legalEntityIdArray).")";

        }

        $query = "SELECT 
                    GetUserName(bfb.ff_id,2) AS sales_rep,
                    rf.`business_legal_name` AS shop_name, 
                    rf.beat,
                    lw.`display_name` AS dc_name,
                    rf.`city`, 
                    rf.`state`,
                    ".DB::raw('ROUND(bfb.buying_price,2) as buying_price').", 
                    ".DB::raw('ROUND(bfb.selling_price,2) as selling_price').", 
                    ".DB::raw('ROUND(bfb.weekly_sales_value,2) as weekly_sales_value').", 
                    bfb.feedback_picture AS image, 
                    ".DB::raw("IFNULL(getMastLookupValue(bfb.status),'Open') AS `status`").",
                    bfb.comments,
                    GetUserName(bfb.assignee,2) AS assignee,
                    GetUserName(bfb.created_by,2) AS created_by ,
                    DATE_FORMAT(bfb.created_at, '%d-%m-%Y %H:%i:%s') AS created_at,
                    GetUserName(bfb.updated_by,2) AS updated_by ,
                    DATE_FORMAT(bfb.`updated_at`, '%d-%m-%Y %H:%i:%s') AS updated_at
                    FROM  brand_feedback AS bfb
                    JOIN retailer_flat AS rf ON (rf.legal_entity_id = bfb.`retailer_le_id`)
                    JOIN legal_entities lw ON (rf.parent_le_id=lw.legal_entity_id)"
                    . $sqlWhrCls . $orderBy ;   
        $allRecallData = DB::select(DB::raw($query));    
        return $allRecallData;
    }
}








