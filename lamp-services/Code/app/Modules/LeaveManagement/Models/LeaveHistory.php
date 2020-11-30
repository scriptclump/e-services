<?php
namespace App\Modules\LeaveManagement\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;


class LeaveHistory extends Model
{


    protected $roleAccess;
    public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
        
        $this->leavehistory_grid_fields = array(
            'leave_history_id' => 'scc_id',
            'emp_id' => 'leave_history.emp_id',
            'emp_ep_id' => 'employee.emp_code',
            'emp_name' => 'employee.firstname',
            'emp_type' => 'emp_groups.group_name',
            'leave_type' => 'm1.description',
            'from_date' => 'from_date',
            'to_date' => 'to_date',
            'no_of_days' => 'no_of_days',
            'reason' => 'm2.description',
            'contact_number' => 'contact_number',
            'emergency_mail' => 'emergency_mail',
            'status' => 'm3.master_lookup_name'
            );
    }

    public function getLeavehistoryList($makeFinalSql, $orderBy, $page, $pageSize)
    {
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        else{
            $orderBy = ' ORDER BY leave_history_id desc';
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
       $query = "SELECT * from (SELECT leave_history.leave_history_id,
                leave_history.emp_id,
                employee.emp_code as emp_ep_id,
                emp_groups.group_name as emp_type,
                leave_history.no_of_days,
                leave_history.contact_number,
                leave_history.emergency_mail,
                CONCAT(employee.firstname, ' ' ,employee.lastname) as 'emp_name',
                getMastLookupValue(leave_history.leave_type) as leave_type,
                (CASE WHEN leave_type = 148005  THEN getOptionalLeaveName(DATE_FORMAT(from_date, '%Y-%m-%d') )ELSE   getMastLookupValue (reason) END)AS reason,
                getMastLookupValue(leave_history.status) as status,
                DATE_FORMAT(leave_history.from_date, '%Y-%m-%d') as from_date,
                DATE_FORMAT(leave_history.to_date, '%Y-%m-%d') as to_date
            FROM `leave_history` 
            INNER JOIN `employee` ON `leave_history`.`emp_id` = `employee`.`emp_id`
            Left JOIN `emp_groups` ON `employee`.`emp_group_id` = `emp_groups`.`emp_group_id`
        ) as inntbl". $sqlWhrCls . $orderBy ;

        $allRecallData = DB::select(DB::raw($query));
        $TotalRecordsCount = count($allRecallData);
        if($page!='' && $pageSize!=''){
            $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
            $allRecallData = array_slice($allRecallData, $page, $pageSize);
        }
        $arr =  json_encode(array('results'=>$allRecallData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount))); 
        return $arr;        
    }
}








    