<?php

namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Ledger\Models\LedgerModel;
use App\Central\Repositories\CustomerRepo;
use DB;

class GrnModel extends Model {
    /*
     * Class Name: getPoList
     * Description: Function used to get po list based on picker_id
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 9 dec 2016
     * Modified Date & Reason: 
     */

    Public function getPoList($user_id) {
        $status = '87005,87001';
        $result = DB::table('po')
                ->select('po.po_id', 'po.po_code', db::raw('getMastLookupValue(po.po_status) as status'), 'po.po_date', 'leg.business_legal_name', DB::raw('sum(prd.sub_total) as total_amount'), db::raw('getLeWhName(po.le_wh_id) as warehouse_name'))
                ->join('legal_entities as leg', 'leg.legal_entity_id', '=', 'po.legal_entity_id')
                ->join('po_products as prd', 'po.po_id', '=', 'prd.po_id')
                ->where('logistic_associate_id', '=', $user_id)
                ->where('is_closed', 0)
                ->whereRaw('FIND_IN_SET(po_status,"' . $status . '")')
                ->groupBy('prd.po_id')
                ->get()->all();
        return $result;
    }
    Public function getOpenPoList() {
        $result = DB::table('po')
                ->select('po.po_id', 'po.po_code',
                        db::raw('getMastLookupValue(po.po_status) as status'), 'po.po_date', 'leg.business_legal_name',
                        DB::raw('sum(prd.sub_total) as total_amount'),
                        db::raw('getLeWhName(po.le_wh_id) as warehouse_name'),
                        DB::raw('count(prd.po_product_id) as line_item_count')
                    )
                ->join('legal_entities as leg', 'leg.legal_entity_id', '=', 'po.legal_entity_id')
                ->join('po_products as prd', 'po.po_id', '=', 'prd.po_id')                
                ->where('po_status', '87001')
                ->groupBy('prd.po_id')
                ->get()->all();
        return $result;
    }
    Public function getAssignedPoList($type) {
        $query = DB::table('po')
                ->select('po.po_id', 'po.po_code',
                        db::raw('getMastLookupValue(po.po_status) as status'), 'po.po_date', 'leg.business_legal_name',
                        DB::raw('sum(prd.sub_total) as total_amount'),
                        db::raw('getLeWhName(po.le_wh_id) as warehouse_name'),
                        DB::raw('count(prd.po_product_id) as line_item_count'),
                        DB::raw('GetUserName(po.logistic_associate_id,2) as picker_name')                        
                    )
                ->join('legal_entities as leg', 'leg.legal_entity_id', '=', 'po.legal_entity_id')
                ->join('po_products as prd', 'po.po_id', '=', 'prd.po_id')                
                ->whereIn('po_status', ['87001','87005']);
                $query->where('is_closed', 0);
                if($type=='assigned'){
                        $query->whereNotNull('logistic_associate_id');
                }else{
                    //$query->whereIn('approval_status', [57031,57032,57033,1]);
                    $query->whereIn('approval_status', [57107,57119,57120,1]);
                    $query->whereNull('logistic_associate_id');
                }
                $query->groupBy('prd.po_id');
                $result=$query->get()->all();
        return $result;
    }
    public function getUsersByRoleName($roleName) {
        $result = DB::table('users')
                ->select('users.user_id', 'users.firstname', 'users.lastname', 'users.email_id', 'users.mobile_no')
                ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->where(array('users.is_active' => 1))
                ->whereIn('roles.name', $roleName)
                ->get()->all();
        return $result;
    }
    public function getRoleIdByName($roleName) {
        $result = DB::table('roles')
                ->select('roles.role_id')
                ->where('roles.name', $roleName)
                ->first();
        return $result;
    }
    Public function getAssignedPosByPicker($picker_id, $status) {
        $query = DB::table('po')
                ->select(DB::raw('COUNT(DISTINCT po.po_id) as count'))
                ->where('logistic_associate_id', '=', $picker_id);
        if ($status == 'completed') {
            $query->join('inward', 'inward.po_no', '=', 'po.po_id');
            $query->whereBetween('inward.created_at', array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));
            $query->where('po_status', '87002');
        }
        if ($status == 'assigned') {
            $query->whereIn('po_status', array('87001','87005'));
        }
        $result = $query->first();
        return $result;
    }	
    Public function getAssignedOrdersByPicker($picker_id, $status) {
        $query = DB::table('gds_orders');
                $query->leftJoin('gds_order_track as track', 'track.gds_order_id', '=', 'gds_orders.gds_order_id');
        if ($status == 'completed') {
            $query->join('gds_ship_grid as ship', 'ship.gds_order_id', '=', 'gds_orders.gds_order_id');
            $query->whereBetween('ship.created_at', array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));
        }
        $query->select(DB::raw('COUNT(gds_orders.gds_order_id) as count'));
        if ($status == 'assigned') {
            $query->whereIn('gds_orders.order_status_id', array('17005','17020'));
        }        
        $query->where('track.picker_id', '=', $picker_id);
        $result = $query->first();
        return $result;
    }
    /*update po*/
    Public function updatePO($poId, $arr) {
        $update = DB::table('po')
                ->whereIn('po_id',$poId)->update($arr);
        return $update;
    }
    Public function getGRNList($type,$picker_id='',$offset,$perpage) {
        $query = DB::table('inward');
        $query->join('po', 'po.po_id', '=', 'inward.po_no');
        $query->join('putaway_list', 'putaway_list.source_id', '=', 'inward.inward_id');
        $query->select('po.po_id', 'po.po_code','inward.inward_id','inward.inward_code','putaway_list.putaway_id',
                DB::raw('GetUserName(inward.picker_id,2) as picker_name'),
                DB::raw('getMastLookupValue(inward.inward_status) as inward_status')
            );
        $query->where(['putaway_list.putaway_source' => 'GRN', 'putaway_list.putaway_status' => 12801]);
        if($type=='assigned'){
                $query->where('inward.picker_id','!=',0);
        }else if($type=='unassigned'){
            $query->where('inward.picker_id',0);
        }
        if($picker_id!=''){
            $query->where('inward.picker_id',$picker_id);
        }
        $query->where('inward.inward_status',76001);
        $query->groupBy('inward.inward_id');
        $query->orderBy('inward.created_at', 'DESC');
        $query->skip($offset * $perpage)->take($perpage);
        $result=$query->get()->all();
        return $result;
    }
    Public function updateGRN($inwardId, $arr) {
        $update = DB::table('inward')
                ->where('inward_id',$inwardId)->update($arr);
        $this->updatePutAwayData($inwardId, $arr);
        return $update;
    }
    
    public function updatePutAwayData($inwardId, $arr)
    {
        try
        {
            if($inwardId > 0)
            {
                $putawayId = DB::table('putaway_list')
                        ->where(['source_id' => $inwardId, 'putaway_source' => 'GRN'])
                        ->pluck('putaway_id');
                if($putawayId > 0)
                {
                    DB::table('putaway_allocation')
                            ->where('putaway_list_id', $putawayId)
                            ->update($arr);
                }else{
                    $comment = '<br/>Unable to assign picker in putaway allocation table as the putaway for the GRN '.$inwardId.' is not created<br/><br/>';
//                    $comment.= $fail_message;
                    $body = array('template' => 'emails.po', 'attachment' => '', 'name' => 'Hello All', 'comment' => $comment);
                    $inwardModel = new Inward();
//                    $userEmailArr = $inwardModel->getUserEmailByRoleName(['DC Manager']);
                    $toEmails = array();
                    $toEmails[] = 'sandeep.jeedula@ebutor.com';
                    $toEmails[] = 'saikumar.gopisetty@ebutor.com';
//                    if (is_array($userEmailArr) && count($userEmailArr) > 0) {
//                        foreach ($userEmailArr as $userData) {
//                            $toEmails[] = $userData->email_id;
//                        }
//                    }
                    $instance = env('MAIL_ENV');
                    $subject = $instance . 'Putaway picker assign reminder';
                    Utility::sendEmail($toEmails, $subject, $body);
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    public function getInwardDetailById($inwardId) {
        try {
            $fieldArr = array('inward.*', 'product.*');
            $query = DB::table('inward')->select($fieldArr);
            $query->join('inward_products as product', 'inward.inward_id', '=', 'product.inward_id');
            $query->where('inward.inward_id', $inwardId);
            #echo $query->toSql();
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
?>