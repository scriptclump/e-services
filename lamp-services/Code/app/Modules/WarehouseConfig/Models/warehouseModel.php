<?php
namespace App\Modules\WarehouseConfig\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;
use App\Modules\Roles\Models\Role;

class warehouseModel extends Model {
	public function __construct()
    {
        
        $this->warehouse_grid_fields = array(
            'pjp_pincode_area_id' => 'pjp_pincode_area_id',
            'pjp_name' => 'pjp_name',
            'days' => 'days',
            'total_outlets' => 'vw_getAllBeats.total_outlets',
            'default_pincode' => 'default_pincode',
            'rm_id' => 'users.firstname',
            'le_wh_id' =>'le_wh_id',
            'spoke_id' => 'vw_getAllBeats.spoke_name',
            'le_wh_id' => 'legalentity_warehouses.display_name',
        );
    }
    public function addNewWarehouseRecord($data)
    { 
            // Insert Query to GET last Id, need to write in Laravel Querys :)
            $date = date("Y-m-d H:i:s");
            $userId = Session::get('userId');
            $result = DB::TABLE('pjp_pincode_area')
                ->insertGetId([
                    "pjp_name" => $data["pjp_name"],
                    "days" => $data["days"],
                    "rm_id" => $data["rm_id"],
                    "default_pincode" => $data["pincode"],
                    "le_wh_id" => $data["le_wh_id"],
                    "spoke_id" => $data["spoke_id"],
                    "created_at" => $date,
                    "created_by" => $userId
                ]);
            return $result;
    }
    public function getSingleRecord($id)
    {
        $query = '
            SELECT
                pjp_name,
                days,
                rm_id,
                default_pincode,
                le_wh_id,
                spoke_id
            FROM 
                pjp_pincode_area
            WHERE 
                pjp_pincode_area_id = ?';
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }
    public function deleteSingleRecord($id)
    {
        if ($id != 0 && $id !='' ) {
            $query= "UPDATE customers SET beat_id= 0 WHERE beat_id=$id";  
            $query=DB::UPDATE($query); 
        }
        $query = 'DELETE FROM pjp_pincode_area WHERE pjp_pincode_area_id = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }
    public function updateWarehouseRecord($data)
    {
        $userId = Session::get('userId');
        $date = "'".date("Y-m-d H:i:s")."'";
        $query = '
            UPDATE 
                pjp_pincode_area
            SET
                pjp_name = ?,
                days = ?,
                rm_id = ?,
                default_pincode = ?,
                le_wh_id = ?,
                spoke_id = ?,
                updated_at = '.$date.',
               updated_by = '.$userId.'
            WHERE
                pjp_pincode_area_id = ?';

        $result = DB::UPDATE($query,[
            $data['pjp_name'],
            $data['days'],
            $data['rm_id'],
            $data['pincode'],
            $data['le_wh_id'],
            $data['spoke_id'],
            $data['pjp_pincode_area_id']
        ]);
        $data = DB::selectFromWriteConnection(DB::raw("call get_retailer_update(".$data['pjp_pincode_area_id'].",".$data['spoke_id'].",".$data['le_wh_id'].")"));
        return $result;
    } 
    public function getWarehouseList($page,$pageSize,$orderByData,$filterData)
    {
        $query = DB::table('vw_getAllBeats')
                ->join('spokes','spokes.spoke_id','=','vw_getAllBeats.spoke_id')
                ->join('legalentity_warehouses','vw_getAllBeats.le_wh_id','=','legalentity_warehouses.le_wh_id')
                ->join('users','vw_getAllBeats.rm_id','=','users.user_id')
                ->select(
                    'vw_getAllBeats.pjp_pincode_area_id',
                    'vw_getAllBeats.pjp_name',
                    'vw_getAllBeats.default_pincode',
                    'vw_getAllBeats.days',
                    'spokes.spoke_name as spoke_id',
                    'vw_getAllBeats.total_outlets',
                    'legalentity_warehouses.display_name as le_wh_id',
                    DB::raw("CONCAT(users.firstname,' ',users.lastname) as rm_id"));
        // Sorting
        if ($orderByData) {
            $order = explode(' ', $orderByData);
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->warehouse_grid_fields[$order_query_field])) {
                $order_by = $this->warehouse_grid_fields[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
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
                        foreach ($this->warehouse_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->warehouse_grid_fields[$key], 'like', $filter_value);
                            }
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1];
                        foreach ($this->warehouse_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->warehouse_grid_fields[$key], 'like', $filter_value);
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
                        foreach ($this->warehouse_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->warehouse_grid_fields[$key], $like, $filter_value);
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
                        foreach ($this->warehouse_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->warehouse_grid_fields[$key], $like, $filter_value);
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

                    if (isset($this->warehouse_grid_fields[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->warehouse_grid_fields[$filter_query_field];
                    }
                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

        $result = array();
        $result['count'] = $query->count();
        if($filterData and $result['count'] < $pageSize)
            $result['data'] = $query->get()->all();
        else
            $result['data'] = $query->skip($page * $pageSize)->take($pageSize)->get()->all();
        if(!empty($result))  return $result;
        return FALSE;
    }
    
    public function getusersInfo($id=null)
    {
        if($id==null){
            return DB::table('users')
                ->select (DB::raw("CONCAT(users.firstname,' ',users.lastname) AS firstname"),'users.user_id')
                ->join('user_roles as ur','ur.user_id','=','users.user_id')
                ->join ('roles as rol','rol.role_id','=','ur.role_id')
                ->join('user_permssion as up','up.user_id','=','users.user_id')
                ->where("rol.short_code","SSLO")
                ->where("up.permission_level_id",6)
                ->get()->all();
        }else{
            $bu_id = DB::table("legalentity_warehouses")
                ->select('bu_id')
                ->where('le_wh_id',$id)
                ->get()->all();
            $bu_id=json_decode(json_encode($bu_id),1);
            if(count($bu_id)>0)
            return DB::table('users')
                ->select (DB::raw("CONCAT(users.firstname,' ',users.lastname) AS firstname"),'users.user_id')
                ->join('user_roles as ur','ur.user_id','=','users.user_id')
                ->join ('roles as rol','rol.role_id','=','ur.role_id')
                ->join('user_permssion as up','up.user_id','=','users.user_id')
                ->where("rol.short_code","SSLO")
                ->where("up.permission_level_id",6)
                ->where("up.object_id",$bu_id[0]['bu_id'])
                ->groupBy ("user_id")
                ->get()->all();
            else return array();
        }
    }
    public function getwarehouseInfo($ids = null)
    {
        $this->_roleModel = new Role();
        $Json = json_decode($this->_roleModel->getFilterData(6), 1);
        $filters = json_decode($Json['sbu'], 1);
        $dc_acess_list = isset($filters['118002']) ? $filters['118002'] : 'NULL';

        if($ids == null)
        return DB::table('legalentity_warehouses')
                ->select('display_name','le_wh_id')
                ->where("dc_type",118002)
                ->whereIn("le_wh_id",explode(',',$dc_acess_list))
                ->get()->all();
    }

    public function display($id){
        return DB::table('legalentity_warehouses as lw')
                        ->join('spokes as sp','sp.le_wh_id','=','lw.le_wh_id')
                        ->select(['sp.spoke_name','sp.spoke_id'])
                        ->where("lw.le_wh_id",$id)
                        ->get()->all();
    }
    public function getspokeInfo($ids = null)
    {
        if($ids == null)
        return DB::table('spokes')
                ->select('spoke_name','spoke_id','le_wh_id')
                ->get()->all();
        else
        return DB::table('spokes')
                ->select('spoke_name','spoke_id','le_wh_id')
                ->where('le_wh_id',$ids)
                ->get()->all();

    }
}    