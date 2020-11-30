<?php
namespace App\Modules\TallyConnector\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;
use Redirect;

class tallyModel extends Model {

    protected $roleAccess;
	public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
        $this->tally_grid_fields = array(
            'sync_id' => 'sync_id',
            'cost_centre' => 'cost_centre',
            'bu_name' => 'business_units.bu_name',
            'cost_centre_group' => 'cost_centre_group',
            'sync_url' => 'sync_url',
            'is_active' => 'tally_le_sync.is_active'
        );
    }
    public function addNewTallyRecord($data)
	{
	        // Insert Query to GET last Id, need to write in Laravel Querys :)
            $date = date("Y-m-d H:i:s");
            $userId = Session::get('userId');
	        $result = DB::TABLE('tally_le_sync')
	            ->insertGetId([
	                "cost_centre" => $data["cost_centre"],
	                "cost_centre_group" => $data["cost_centre_group"],
	                "sync_url" => $data["sync_url"],
	                "is_active" => $data["is_active"],
                    "created_at" => $date,
                    "created_by" => $userId
	            ]);
            return true;
	}
	public function getSingleRecord($id)
    {
        $query = '
            SELECT
                cost_centre,
                cost_centre_group,
                sync_url,
                is_active
            FROM 
                tally_le_sync
            WHERE 
                sync_id = ?';
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }
    public function deleteSingleRecord($id)
    {
        $query = 'DELETE FROM tally_le_sync WHERE sync_id = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }
    public function updateTallyRecord($data)
    {
        $userId = Session::get('userId');
        $date = "'".date("Y-m-d H:i:s")."'";
        $query = '
            UPDATE 
                tally_le_sync
            SET
                cost_centre = ?,
                cost_centre_group = ?,
                sync_url = ?,
                is_active = ?,
               updated_at = '.$date.',
               updated_by = '.$userId.'
            WHERE
                sync_id = ?';

        $result = DB::UPDATE($query,[
            $data['cost_centre'],
            $data['cost_centre_group'],
            $data['sync_url'],
            $data['is_active'],
            $data['sync_id']
        ]);
        return $result;
    } 
    public function getTallyList($page,$pageSize,$orderByData,$filterData)
    {
  //      DB::enableQueryLog();
        $query = DB::table('tally_le_sync')
                ->leftJoin('business_units','business_units.cost_center','=','tally_le_sync.cost_centre')
                ->select(
                   'sync_id',
                   'cost_centre',
                   'business_units.bu_name',
                   'cost_centre_group',
                   'sync_url',
                   DB::raw('IF(tally_le_sync.is_active = 1, "Active", "In-Active") AS is_active'));
            
        // Sorting
        if ($orderByData) {
            $order = explode(' ', $orderByData);
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->tally_grid_fields[$order_query_field])) {
                $order_by = $this->tally_grid_fields[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }else{
            $query->orderBy('tally_le_sync.sync_id', 'DESC');
        }
        // Filtering
        if ($filterData) {
            $post_filter_query = explode(' and ', $filterData); //multiple filtering seperated by 'and'
            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter                    
                $filter = explode(' ', $post_filter_query_sub);
                $length = count($filter);
                $filter_query_field = '';
                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                        $filter_query_field .= $filter[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter[$length - 2];
                    $filter_query_value = $filter[$length - 1];
                } else {
                    $filter_query_field = $filter[0];
                    $filter_query_operator = $filter[1];
                    $filter_query_value = $filter[2];
                }
                $filter_query_substr = substr($filter_query_field, 0, 7);
                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    if ($filter_query_substr == 'startsw') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = $filter_value_array[1] . '%';
                        foreach ($this->tally_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                if($value=="tally_le_sync.is_active"){
                                    $query->where(DB::raw('getActiveInActive(tally_le_sync.is_active)'),'LIKE', $filter_value_array[1].'%');
                                }else{
                                    $query->where($this->tally_grid_fields[$key], 'like', $filter_value);
                                }
                            }
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1];
                        foreach ($this->tally_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                if($value=="tally_le_sync.is_active"){
                                    $query->where(DB::raw('getActiveInActive(tally_le_sync.is_active)'),'LIKE', '%'.$filter_value_array[1]);
                                }else{
                                    $query->where($this->tally_grid_fields[$key], 'like', $filter_value);
                                }
                            }
                        }
                    }
                    if ($filter_query_substr == 'tolower') {
                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = $filter_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }
                        foreach ($this->tally_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                if($value=="tally_le_sync.is_active"){
                                    $query->where(DB::raw('getActiveInActive(tally_le_sync.is_active)'),'LIKE', '%'.$filter_value_array[1].'%');
                                }else{
                                    $query->where($this->tally_grid_fields[$key], $like, $filter_value);
                                }
                            }
                        }
                    }
                    if ($filter_query_substr == 'indexof') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1] . '%';
                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }
                        foreach ($this->tally_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                if($value=="tally_le_sync.is_active"){
                                    $query->where(DB::raw('getActiveInActive(tally_le_sync.is_active)'),'LIKE', '%'.$filter_value_array[1].'%');
                                }else{
                                    $query->where($this->tally_grid_fields[$key], $like, $filter_value);
                                }
                            }
                        }
                    }
                } else {
                    switch ($filter_query_operator) {
                        case 'eq' :
                            $filter_operator = '=';
                            break;
                        case 'ne':
                            $filter_operator = '!=';
                            break;
                        case 'gt' :
                            $filter_operator = '>';
                            break;
                        case 'lt' :
                            $filter_operator = '<';
                            break;
                        case 'ge' :
                            $filter_operator = '>=';
                            break;
                        case 'le' :
                            $filter_operator = '<=';
                            break;
                    }
                    if (isset($this->tally_grid_fields[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->tally_grid_fields[$filter_query_field];
                    }
                   $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }
        $result = array();
        $result['count'] = $query->count();
        if($filterData and $result['count'] < $pageSize)
            $result['data'] = $query->get();
        else
            $result['data'] = $query->skip($page * $pageSize)->take($pageSize)->get();
        if(!empty($result))  return $result;
//        Log::info(DB::getQueryLog());
        return false;
    }
    public function isTallyCodeUnique($sync_id,$tallyCode)
    {
        $query = '
            SELECT
                COUNT(sync_id) AS sync_id
            FROM
                tally_le_sync
            WHERE
                cost_centre = ?';

        if($sync_id != 0)
            $query.=' AND sync_id <> '.$sync_id;
        $result = DB::SELECT($query,[$tallyCode]);
        $result = isset($result[0]->sync_id)?$result[0]->sync_id:$result;
        
        return ($result<1)?TRUE:FALSE;
    }
    public function isTallyCodeValid($tallyCode)
    {
        $query = "
            SELECT
                COUNT(*) AS count
            FROM
                business_units
            WHERE
                cost_center = '".$tallyCode."'";
        $result = DB::SELECT(DB::raw($query));
        if($result[0]->count > 0){
            return true;
        }return false;
    }
}         




