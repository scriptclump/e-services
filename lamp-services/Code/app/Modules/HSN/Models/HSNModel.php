<?php
namespace App\Modules\HSN\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;


class HSNModel extends Model {

    protected $roleAccess;
    
    public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
        $this->hsn_grid_fields = array(
            'HSNid' => 'HSNid',
            'Chapter' => 'Chapter',
            'ITC_HSCodes' => 'ITC_HSCodes',
            'HSC_Desc' => 'HSC_Desc',
            'tax_percent' => 'tax_percent',
            'is_active' => 'is_active'
            );
    }

    public function addNewHSNRecord($data)
    {
        // Insert Query to GET last Id, need to write in Laravel Querys :)
        $result = DB::TABLE('HSN_Master')
            ->insertGetId([
                "Chapter" => $data["Chapter"],
                "ITC_HSCodes" => $data["ITC_HSCodes"],
                "HSC_Desc" => $data["HSC_Desc"],
                "tax_percent" => $data["tax_percent"],
                "is_active" => $data["is_active"]
            ]);

        return $result;
    }

    public function getSingleRecord($id)
    {
        $query = '
            SELECT
                Chapter,
                ITC_HSCodes,
                HSC_Desc,
                tax_percent,
                is_active
            FROM 
                HSN_Master
            WHERE 
                HSNid = ?';
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function deleteSingleRecord($id)
    {
        $query = 'DELETE FROM HSN_Master WHERE HSNid = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }

    public function getHSNList($page,$pageSize,$orderByData,$filterData)
    {
        $query = 
            DB::table('HSN_Master')
                ->select(
                    'HSNid',
                    'Chapter',
                    'ITC_HSCodes',
                    'HSC_Desc',
                    'tax_percent',
                    DB::RAW('IF(is_active = 1, "Active", "In-Active") AS is_active'));
        
        // $page = !empty($request->input('page'))?$request->input('page'):1;   //Page number
        // $pageSize = !empty($request->input('pageSize'))?$request->input('pageSize'):10;
        
        // Sorting
        if ($orderByData) {
            $order = explode(' ', $orderByData);
            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = 'desc';
            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }
            if (isset($this->hsn_grid_fields[$order_query_field])) {
                $order_by = $this->hsn_grid_fields[$order_query_field];
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
                        foreach ($this->hsn_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->hsn_grid_fields[$key], 'like', $filter_value);
                            }
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1];
                        foreach ($this->hsn_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->hsn_grid_fields[$key], 'like', $filter_value);
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
                        foreach ($this->hsn_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->hsn_grid_fields[$key], $like, $filter_value);
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
                        foreach ($this->hsn_grid_fields as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->hsn_grid_fields[$key], $like, $filter_value);
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
                    if (isset($this->hsn_grid_fields[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->hsn_grid_fields[$filter_query_field];
                    }
                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
        }

        $result = array();
        $result['count'] = $query->count();

        // If the Filter Search is less than 10,
        // then the DB::Query is not working, due to undefined limit rows.
        // un-explanable bull shit
        if($filterData and $result['count'] < 10)
            $result['data'] = $query->get()->all();
        else
            $result['data'] = $query->skip($page * $pageSize)->take($pageSize)->get()->all();
        
        if(!empty($result))  return $result;
        return FALSE;
    }

    public function updateHSNRecord($data)
    {
        $query = '
            UPDATE 
                HSN_Master
            SET
                Chapter = ?,
                ITC_HSCodes = ?,
                HSC_Desc = ?,
                tax_percent = ?,
                is_active = ?
            WHERE
                HSNid = ?';

        $result = DB::UPDATE($query,[
            $data['Chapter'],
            $data['ITC_HSCodes'],
            $data['HSC_Desc'],
            $data['tax_percent'],
            $data['is_active'],
            $data['HSN_id']
        ]);

        return $result;
    }

    public function isHSNCodeUnique($hsnId,$hsnCode)
    {
        $query = '
            SELECT
                COUNT(HSNid) AS hsnid
            FROM
                HSN_Master
            WHERE
                ITC_HSCodes = ?';

        if($hsnId != 0)
            $query.=' AND HSNid <> '.$hsnId;
        
        $result = DB::SELECT($query,[$hsnCode]);
        $result = isset($result[0]->hsnid)?$result[0]->hsnid:$result;
        
        return ($result<1)?TRUE:FALSE;
    }
}