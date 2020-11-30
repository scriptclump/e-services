<?php
namespace App\Modules\LegalEntities\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;


class StateModel extends Model{

    protected $roleAccess;
    public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
        $this->state_grid_fields = array(
            'scc_id' => 'scc_id',
            'state_name' => 'State_name',
            'state_code' => 'State_code',
            'city_name' => 'City_name',
            'city_code' => 'City_code',
            'dc_inc_id' => 'dc_inc_id',
            'fc_inc_id' => 'fc_inc_id',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'is_active' => 'state_city_codes.is_active'
            );
    }
    public function addNewStateRecord($data,$statecode)
    {
        // Insert Query to GET last Id, need to write in Laravel Querys :)
       
        $result = DB::TABLE('state_city_codes')
                ->insertGetId([
                    "state_name" => $data["state_name"],
                    "state_code" => $statecode,
                    "city_name" => $data["city_name"],
                    "city_code" => $data["city_code"],
                    "dc_inc_id" => $data["dc_inc_id"],
                    "fc_inc_id" => $data['fc_inc_id'],
                    "latitude" => $data['latitude'],
                    "longitude" => $data['longitude'],
                    "is_active" => $data["is_active"]
                ]);
        return $result;
    }
    public function updateStateRecord($data,$statecode)
    {
        $query = 'UPDATE state_city_codes
                  SET
                    state_name = ?,
                    state_code = ?,
                    city_name = ?,
                    city_code = ?,
                    dc_inc_id = ?,
                    fc_inc_id = ?,
                    latitude = ?,
                    longitude = ?,
                    is_active = ?
                WHERE
                    scc_id = ?';
        $result = DB::UPDATE($query,[
            $data['state_name'],
            $statecode,
            $data['city_name'],
            $data['city_code'],
            $data['dc_inc_id'],
            $data['fc_inc_id'],
            $data['latitude'],
            $data['longitude'],
            $data['is_active'],
            $data['scc_id']
        ]);
        return true;
    }

    public function getSingleRecord($id)
    {
        $query = '
            SELECT *FROM 
                state_city_codes
            WHERE 
                scc_id = ?';
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function deleteSingleRecord($id)
    {
        $query = 'DELETE FROM state_city_codes WHERE scc_id = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }
    public function getStateList($page,$pageSize,$orderByData,$filterData)
    {
        $query = DB::table('state_city_codes')
                //->join('zone','state_city_codes.state_name','=','zone.name')
                ->select(
                    'scc_id',
                    //'zone.name as state_name',
                    'state_name',
                    'state_code',
                    'city_name',
                    'city_code',
                    'dc_inc_id',
                    'fc_inc_id',
                    'latitude',
                    'longitude',
                    DB::raw('IF(state_city_codes.is_active = 1, "Active","In-Active") AS is_active'));
        
        // Sorting
        if ($orderByData) {
            $order = explode(' ', $orderByData);
            $order_query_field = $order[0];
            $order_query_type = $order[1]; 
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->state_grid_fields[$order_query_field])) {
                $order_by = $this->state_grid_fields[$order_query_field];
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
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the i
                        $filter_value = $filter_value_array[1] . '%';
                        foreach ($this->state_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                             
                                if($value=="state_city_codes.is_active")
                                {
                                    $query->where(DB::raw('getActiveInActive(state_city_codes.is_active)'),'LIKE',$filter_value_array[1].'%');
                                }else
                                {  //getting the filter field name
                                    $query->where($this->state_grid_fields[$key], 'like', $filter_value);
                                }
                            }
                            
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1];
                        foreach ($this->state_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->state_grid_fields[$key], 'like', $filter_value);
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
                        foreach ($this->state_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0)
                            {
                                if($value=="state_city_codes.is_active")
                                {
                                    $query->where(DB::raw('getActiveInActive(state_city_codes.is_active)'),'LIKE',$filter_value_array[1].'%');
                                }else
                                {  
                                //getting the filter field name
                                    $query->where($this->state_grid_fields[$key], $like, $filter_value);
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
                        foreach ($this->state_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                if($value=="state_city_codes.is_active"){
                                    $query->where(DB::raw('getActiveInActive(state_city_codes.is_active)'),'LIKE','%'.$filter_value_array[1].'%');
                                }else{
                                    $query->where($this->state_grid_fields[$key], $like, $filter_value);
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
                    if (isset($this->state_grid_fields[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->state_grid_fields[$filter_query_field];
                    }
                     $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

        $result = array();
        $result['count'] = $query->count();
        if($result['count'] < $pageSize)
            $result['data'] = $query->get()->all();
        else
            $result['data'] = $query->skip($page * $pageSize)->take($pageSize)->get()->all();
        if(!empty($result))  return $result;
        return FALSE;
    }
    

    public function isStateCodeUnique($scc_id,$stateCode)
    {
         $query = '
            SELECT
                COUNT(scc_id) AS scc_id
            FROM
                state_city_codes
            WHERE
                state_name = ?';

        if($scc_id != 0)
            $query.=' AND scc_id <> '.$scc_id;
        
        $result = DB::SELECT($query,[$stateCode]);
        $result = isset($result[0]->scc_id)?$result[0]->scc_id:$result;
        
        return ($result<1)?TRUE:FALSE;
    }
    public function isCityCodeUnique($scc_id,$cityCode)
    {
         $query = '
            SELECT
                COUNT(scc_id) AS scc_id
            FROM
                state_city_codes
            WHERE
                city_name = ?';

        if($scc_id != 0)
            $query.=' AND scc_id <> '.$scc_id;
        
        $result = DB::SELECT($query,[$cityCode]);
        $result = isset($result[0]->scc_id)?$result[0]->scc_id:$result;
        
        return ($result<1)?TRUE:FALSE;
    }


    public function getstateInfo($ids = null)
    {
        if($ids == null)
        return DB::table('zone')
                ->select('name','gst_state_code')
                ->where('country_id','=','99')
                ->get()->all();
    }     


    public function getstatecode($data)
    {
       //print_r($data);exit;
        $statecode = DB::table('zone')
                  ->select('gst_state_code')
                  ->where('name','=',$data['state_name'])
                  ->get()->all();

               

     //  print_r($statecode);exit;
       return $statecode;
    }     
    

}

