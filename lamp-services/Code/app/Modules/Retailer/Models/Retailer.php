<?php
namespace App\Modules\Retailer\Models;

use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\ProductRepo;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Retailer\Models\countries;
use App\Modules\PurchaseOrder\Controllers\PaymentController;
use DB;
use Log;
use Caching;
use Session;
use Utility;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\Users;
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Cpmanager\Controllers\MasterLookupController;
use URL;


class Retailer extends Model
{
    public $timestamps = false;
    protected $table = "legal_entities";
    protected $primaryKey = 'legal_entity_id';    
    protected $roleAccessObj;
    protected $roleModel;
    protected $users;
    protected $_paymentObj;

    public function __construct(RoleRepo $roleAccessObj) {
        $this->roleAccessObj = $roleAccessObj;
        $this->users = new Users();
    }
    
    public function filterRetailersData($request, $produc_grid_field_db_match, $minimumFields = null)
    {
        try
        {
            $editRetailerFeature = $this->roleAccessObj->checkPermissionByFeatureCode('RET002');
            $deleteRetailerFeature = $this->roleAccessObj->checkPermissionByFeatureCode('RET003');
            $approveRetailerFeature = $this->roleAccessObj->checkPermissionByFeatureCode('RET004');
            $blockRetailerFeature = $this->roleAccessObj->checkPermissionByFeatureCode('RET005');
            $retailerListDetails = $this->filterData($request, $produc_grid_field_db_match, $minimumFields);
            $results = isset($retailerListDetails['Records']) ? $retailerListDetails['Records'] : [];
            $i = 0;
            foreach ($results as $result) {
                $actions = '';
                $checkbox = '';
                $checkbox = '<input id="select_row" type="checkbox" class="centerAlignment" value="'.$result->legal_entity_id.'" />'; 
                $results[$i]->select_option = $checkbox;
                if($approveRetailerFeature)
                {
                    $actions = '<span style="padding-left:15px;" ><a href="/retailers/approve/' . $this->roleAccessObj->encodeData($result->legal_entity_id) . '" >'
                        . '<i class="fa fa-thumbs-o-up"></i></a></span>';
                }
                $results[$i]->is_approved = ($result->is_approved == 1) ? '<span style="display:block" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>' : '<span style="display:block" class="ui-igcheckbox-small-off ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';
                $results[$i]->profile_completed = ($result->profile_completed == 1) ? '<span style="display:block" class="ui-icon ui-icon-check ui-igcheckbox-small-on"></span>' : '<span style="display:block" class="ui-igcheckbox-small-off ui-icon ui-icon-check ui-igcheckbox-small-on"></span>';

                if($editRetailerFeature)
                {
                    $actions .= '<span style="padding-left:15px;" ><a href="/retailers/edit/' . $this->roleAccessObj->encodeData($result->legal_entity_id) . '" >'
                        . '<i class="fa fa-pencil"></i></a></span>';
                }
                if($deleteRetailerFeature)
                {
                    $actions .= '<span style="padding-left:15px;" >'
                        . '<a href="javascript:void(0)" '
                        . 'onclick="deleteLegalEntity(' . $result->legal_entity_id . ')">'
                        . '<i class="fa fa-trash-o"></i></a></span>';
                }
                if($blockRetailerFeature)
                {
                    $inactiveUsers = $this->roleAccessObj->getActiveUsers($result->legal_entity_id);
                    if ($inactiveUsers) {
                        $actions = $actions . '<label class="switch" style="float:right;"><input class="switch-input block_users"'
                                . ' type="checkbox" checked="true"  name="' . $result->legal_entity_id . '"  id="' . $result->legal_entity_id . '" value="' . $result->legal_entity_id . '" >'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                    } else {
                        $actions = $actions . '<label class="switch" style="float:right;"><input class="switch-input block_users" '
                                . 'data_attr_productid="' . $result->legal_entity_id . '" type="checkbox" check="false"  name="' . $result->legal_entity_id . '"  id="cp_chaild' . $result->legal_entity_id . '" value="' . $result->legal_entity_id . '" >'
                                . '<span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label>';
                    }
                }
                $results[$i]->actions = $actions;
                $results[$i]->created_at = date('d/m/Y', strtotime($results[$i]->created_at));
                $results[$i]->updated_at = date('d/m/Y', strtotime($results[$i]->updated_at));
                $results[$i]->last_order_date = isset($results[$i]->last_order_date)?date('d/m/Y', strtotime($results[$i]->last_order_date)):'';
                $i++;
            }
            $retailerListDetails['Records'] = $results;
            return $retailerListDetails;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function filterData($request, $produc_grid_field_db_match, $minimumFields = null)
    {
        try
        { 
            $globalAccess = $this->roleAccessObj->checkPermissionByFeatureCode("GLB0001");
            $dcfc_access = $this->roleAccessObj->checkPermissionByFeatureCode("DCFC01");
            $legalEntityId=Session::get('legal_entity_id');
            $userId=Session::get('userId');
            $rolemodel=new Role();  
            $dcList = $rolemodel->getWarehouseData($userId, 6);
            $dcList = json_decode($dcList,true);
            //$hubList=DB::select(DB::raw("select hub_id from dc_hub_mapping where dc_id in (".$dcList['118001'].")"));
            $inputData=$dcList['118002'];
            $hubdata=explode(',',$inputData);
            $request_input = $request->input();
            $filter_by = '';
            $filterBy = '';
            $orderby_array = [];
            $order_by = '';
            $date = array();
            if (isset($request_input['$filter'])) {
                $filterBy = $request_input['$filter'];
                $removespace=(explode("'",$filterBy));
                for($i=1; $i<count($removespace); $i++){
                    $i=$i++;
                    $removespace[$i]=trim($removespace[$i]," ");
                }
                $newstring=implode("'",$removespace);
                $filterBy=$newstring;


            } elseif (isset($request_input['%24filter'])) {
                $filterBy = urldecode($request_input['%24filter']);
            }
            
            $page = $request->input('page');   //Page number
            $pageSize = $request->input('pageSize'); //Page size for ajax
            $results = DB::table('retailer_flat')
                    ->leftJoin('gds_orders', 'gds_orders.cust_le_id', '=', 'retailer_flat.legal_entity_id');
            if(!$minimumFields)
            {
                $results = $results->select('retailer_flat.*', DB::raw('COUNT(gds_orders.`gds_order_id`) AS orders'), DB::raw('getDcName(retailer_flat.hub_id) AS DC' ),
                        DB::raw('DATE(MAX(gds_orders.`created_at`)) AS last_order_date'));
            }else{
                $results = $results->select('retailer_flat.legal_entity_id', 'retailer_flat.le_code', 'retailer_flat.name', 'retailer_flat.mobile_no', DB::raw('getDcName(retailer_flat.hub_id) AS DC' ));
            }
            if ($request->input('$orderby'))
            {             //checking for sorting
                $order = explode(' ', $request->input('$orderby'));
                $order_query_field = $order[0]; //on which field sorting need to be done
                $order_query_type = $order[1]; //sort type asc or desc
                $order_by_type = 'desc';

                if ($order_query_type == 'asc')
                {
                    $order_by_type = 'asc';
                }

                if (isset($produc_grid_field_db_match[$order_query_field]))
                { //getting appropriate table field based on grid field
                    $order_by = $produc_grid_field_db_match[$order_query_field];
                }
                $orderby_array = $order_by . " " . $order_by_type;
            }

            if (isset($filterBy) && $filterBy != '') {
                $filter_explode = explode(' and ', $filterBy);

                foreach ($filter_explode as $filter_each) {
                    $filter_each_explode = explode(' ', $filter_each);
                    $length = count($filter_each_explode);
                    $filter_query_field = '';
                    if ($length > 3) {
                        for ($i = 0; $i < $length - 2; $i++)
                            $filter_query_field .= $filter_each_explode[$i] . " ";
                        $filter_query_field = trim($filter_query_field);
                        $filter_query_operator = $filter_each_explode[$length - 2];
                        $filter_query_value = $filter_each_explode[$length - 1];
                    } else {
                        $filter_query_field = $filter_each_explode[0];
                        $filter_query_operator = $filter_each_explode[1];
                        $filter_query_value = $filter_each_explode[2];
                    }                    
                    if (strpos($filter_each, ' or ') !== false)
                    {
                        $query_field_arr = explode(' or ', $filter_each);
                        foreach ($query_field_arr as $query_field_data)
                        {
                            $filter = explode(' ', $query_field_data);
                            $filter_query_field = $filter[0];
                            $filter_query_operator = $filter[1];
                            $filter_query_value = $filter[2];

                            if (strpos($filter_query_field, 'day(') !== false)
                            {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                                continue;
                            } elseif (strpos($filter_query_field, 'month(') !== false)
                            {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                                continue;
                            } elseif (strpos($filter_query_field, 'year(') !== false)
                            {
                                $start = strpos($filter_query_field, '(');
                                $end = strpos($filter_query_field, ')');
                                $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                                $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                                $date[$filter_query_field]["operator"] = $filter_query_operator;
                                $filter_query_operator = $date[$filter_query_field]['operator'];
                                $filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
                            }
                        }
                    } else
                    {
                        if (strpos($filter_query_field, 'day(') !== false)
                        {
                            $start = strpos($filter_query_field, '(');
                            $end = strpos($filter_query_field, ')');
                            $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['day'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                            continue;
                        } elseif (strpos($filter_query_field, 'month(') !== false)
                        {
                            $start = strpos($filter_query_field, '(');
                            $end = strpos($filter_query_field, ')');
                            $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['month'] = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                            continue;
                        } elseif (strpos($filter_query_field, 'year(') !== false)
                        {
                            $start = strpos($filter_query_field, '(');
                            $end = strpos($filter_query_field, ')');
                            $filter_query_field = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                            $date[$filter_query_field]["operator"] = $filter_query_operator;
                            $filter_query_operator = $date[$filter_query_field]['operator'];
                            $filter_query_value = implode('-', array_reverse($date[$filter_query_field]['value']));
                        }
                        reset($date);
                    }

                    $filter_query_field_substr = substr($filter_query_field, 0, 7);

                    if ($filter_query_field_substr == 'startsw' || $filter_query_field_substr == 'endswit' || $filter_query_field_substr == 'indexof' || $filter_query_field_substr == 'tolower') {
                        //Here we are checking the filter is of which type startwith, endswith, contains, doesn't contain, equals, doesn't equal

                        if ($filter_query_field_substr == 'startsw') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_field_value_array[1] . '%';

                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $starts_with_value = $produc_grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $starts_with_value;
                                } else {
                                    $starts_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'endswit') {
                            $filter_query_field_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = '%' . $filter_query_field_value_array[1];

                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    $ends_with_value = $produc_grid_field_db_match[$key] . ' like ' . $filter_value;
                                    $filter_by[] = $ends_with_value;
                                } else {
                                    $ends_with_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'tolower') {
                            $filter_query_value_array = explode("'", $filter_query_value);
                            //extracting the input filter value between single quotes, example: 'value'

                            $filter_value = $filter_query_value_array[1];
                            if ($filter_query_operator == 'eq') {
                                $like = ' = ';
                            } else {
                                $like = ' != ';
                            }
                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    if($key=='DC'){
                                        $hub_id_list= DB::select(DB::raw("CALL gethubId('$filter_query_value_array[1]')"));
                                        //$hub_id_list= DB::select(DB::raw("CALL gethubId('dc')"));
                                        $hub_id_list = json_decode(json_encode($hub_id_list), True);
                                       for($i=0;$i<count($hub_id_list);$i++){
                                            if($hub_id_list[$i]['hub']!=0){
                                                $array[]=$hub_id_list[$i]['hub'];
                                            }
                                       }
                                        $to_lower_value="retailer_flat.hub_id IN (".implode(',',$array).")";
                                    }
                                    else{
                                        //getting the filter field name
                                        $to_lower_value = $produc_grid_field_db_match[$key] . $like . $filter_value;
                                    }
                                    $filter_by[] = $to_lower_value;

                                } else {
                                    $to_lower_value = "";
                                }
                            }
                        }

                        if ($filter_query_field_substr == 'indexof') {
                            $filter_query_value_array = explode("'", $filter_query_field);
                            //extracting the input filter value between single quotes ex 'value'

                            $filter_value = '%' . $filter_query_value_array[1] . '%';

                            if ($filter_query_operator == 'ge') {
                                $like = ' like ';
                            } else {
                                $like = ' not like ';
                            }
                            $hub_id_list='';
                            foreach ($produc_grid_field_db_match as $key => $value) {
                                if (strpos($filter_query_field, '(' . $key . ')') != 0) {
                                    //getting the filter field name
                                    if($key=='DC'){
                                        $hub_id_list= DB::select(DB::raw("CALL gethubId('$filter_query_value_array[1]')"));
                                        //$hub_id_list= DB::select(DB::raw("CALL gethubId('dc')"));
                                        $hub_id_list = json_decode(json_encode($hub_id_list), True);
                                       for($i=0;$i<count($hub_id_list);$i++){
                                            if($hub_id_list[$i]['hub']!=0){
                                                $array[]=$hub_id_list[$i]['hub'];
                                            }
                                       }
                                        $indexof_value="retailer_flat.hub_id IN (".implode(',',$array).")";
                                    }
                                    else{
                                        $indexof_value = $produc_grid_field_db_match[$key] . $like . $filter_value; 
                                    }
                                    $filter_by[] = $indexof_value;
                                } else {
                                    $indexof_value = "";
                                }
                            }
                        }
                    } else {

                        switch ($filter_query_operator) {
                            case 'eq' :
                                $filter_operator = ' = ';
                                break;

                            case 'ne':
                                $filter_operator = ' != ';
                                break;

                            case 'gt' :
                                $filter_operator = ' > ';
                                break;

                            case 'lt' :
                                $filter_operator = ' < ';
                                break;

                            case 'ge' :
                                $filter_operator = ' >= ';
                                break;

                            case 'le' :
                                $filter_operator = ' <= ';
                                break;
                        }

                        if (isset($produc_grid_field_db_match[$filter_query_field])) {
                            //getting appropriate table field based on grid field
                            $filter_field = $produc_grid_field_db_match[$filter_query_field];
                        }
                        if (strpos($filter_query_value, 'DateTime') !== false && $filter_field == 'last_order_date')
                        {
                            $temp = str_replace("DateTime'", '', $filter_query_value);
                            $tempArray = explode('T', $temp);
                            $filter_query_value = isset($tempArray[0]) ? $tempArray[0] : $filter_query_value;
                        }
                        $filter_by[] = $filter_field . $filter_operator . '"'.$filter_query_value.'"';
                    }
                }
            }
            if(!empty($filter_by))
            {       
                if (!empty($filter_by)) {
                    foreach ($filter_by as $filterByEach) {
                        $filterByEachExplode = explode(' ', $filterByEach);

                        $length = count($filterByEachExplode);
                        $filter_query_value = '';
                        if ($length > 3) {
                            $filter_query_field = $filterByEachExplode[0];
                            $filter_query_operator = $filterByEachExplode[1];
                            for ($i = 2; $i < $length; $i++)
                                $filter_query_value .= $filterByEachExplode[$i] . " ";
                        } else {
                            $filter_query_field = $filterByEachExplode[0];
                            $filter_query_operator = $filterByEachExplode[1];
                            $filter_query_value = $filterByEachExplode[2];
                        }

                        $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                        if (in_array(trim($filter_query_operator), $operator_array)) {

                            if($filter_query_field == 'orders')
                            {
                                $filter_query_value = str_replace('"', '', $filter_query_value);
                                $results = $results->having($filter_query_field, $filter_query_operator, (int)$filter_query_value);
                            }elseif($filter_query_field == 'last_order_date'){
                                $filter_query_value = str_replace('"', '', $filter_query_value);
                                $results = $results->where(DB::raw('date(gds_orders.created_at)'), $filter_query_operator, $filter_query_value);
                            }else{
                                $results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                            }                            
                        } else {
                           
                            if($filter_query_field == 'retailer_flat.hub_id'){

                                $filter_query_value=trim($filter_query_value,'(');
                                $filter_query_value=trim($filter_query_value,')');
                                $filter_query_value=explode(',',$filter_query_value);
                                $results=$results->whereIn('retailer_flat.hub_id',$filter_query_value);

                            }
                            else{
                                $results = $results->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                            }
                        }
                    }
                }
                if($dcfc_access){
                    $results = $results->where(function ($query) {
                        $query->where('retailer_flat.legal_entity_type_id', 'like','3%')
                                ->orWhere('retailer_flat.legal_entity_type_id', 'like','1%');
                    });
                }
                else{
                    $results = $results->where('legal_entity_type_id', 'like', '3%');
                }
                if (!empty($orderby_array)) {
                    $orderClause = explode(" ", $orderby_array);             
                    if($orderClause[0] == 'orders')
                    {
                        $results = $results->orderby('orders', $orderClause[1]);  //order by query 
                    }else{
                        $results = $results->orderby($orderClause[0], $orderClause[1]);  //order by query 
                    }                    
                }else{
//                    $results = $results->orderBy('legal_entities.legal_entity_id', 'DESC');
                    $results = $results->orderBy('retailer_flat.legal_entity_id', 'DESC');
                }
                // As we deal with multiple business, we had added parent_le_id
                if(!$globalAccess){
                    $results = $results->whereIn('retailer_flat.hub_id',$hubdata);
                }
               
                $results = $results->groupBy('retailer_flat.legal_entity_id');
                // Below line is to get totalCount of Customers
                $tempCountCollection = clone $results;
                $results = $results
                    ->skip($page * $pageSize)->take($pageSize)
                    ->get()->all();
                if($filter_query_field == 'orders')
                {
                    $totalCount = $tempCountCollection
                            ->get()->all();                    
                }else{
                    $totalCount = $tempCountCollection
                            ->get()->all();
                }
                $totalCount = count($totalCount);
            }else{
                if($dcfc_access){
                    $results = $results->where(function ($query) {
                        $query->where('retailer_flat.legal_entity_type_id', 'like','3%')
                                ->orWhere('retailer_flat.legal_entity_type_id', 'like','1%');
                    });
                }
                else{
                    $results = $results->where('legal_entity_type_id', 'like', '3%');
                }
                $results = $results->groupBy('retailer_flat.legal_entity_id');
                $tempCountCollection = clone $results;
                if(!$globalAccess){
                    $results = $results->whereIn('retailer_flat.hub_id',$hubdata);
                }
                if (!empty($orderby_array)) {
                    $orderClause = explode(" ", $orderby_array);
                    // Log::info('orderClause');
                    // Log::info($orderClause);
                    $results = $results->orderby($orderClause[0], $orderClause[1]);  //order by query 
                }else{
                    $results = $results->orderBy('retailer_flat.legal_entity_id', 'DESC');
                }
                // As we deal with multiple business, we had added parent_le_id
                
                $results = $results->skip($page * $pageSize)->take($pageSize)
                    ->get()->all();
                // Here we are counting the list of records[customers] that are fetched in the count
                $tempCountCollection = $tempCountCollection->select(DB::raw('count(retailer_flat.legal_entity_id) as orders'));

                if(!$globalAccess){
                    $totalCount = $tempCountCollection->whereIn('retailer_flat.hub_id',$hubdata)->get()->all();
                    
                }else{
                    $totalCount = $tempCountCollection->get()->all();
                }

                $totalCount = count($totalCount);
            }
            return ['Records' => $results, 'totalCustomerCount' => $totalCount];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getDashboardRetailers($data)
    {
        try
        {
            $fromDate = date('Y-m-d');
            $datetime = new \DateTime('tomorrow');
            $toDate = $datetime->format('Y-m-d');
            // if(!empty($data))
            // {
                $filterDate = isset($data['filter_date']) ? $data['filter_date'] : '';
                // var_dump($filterDate);
                if($filterDate != '')
                {
                    switch($filterDate)
                    {
                        case 'wtd':
                            $currentWeekSunday = strtotime("last sunday");
                            $sunday = date('w', $currentWeekSunday)==date('w') ? $currentWeekSunday + 7*86400 : $currentWeekSunday;
                            $lastSunday = date("Y-m-d",$sunday);
                            $fromDate = $lastSunday;
                            break;
                        case 'mtd':
                            $fromDate = date('Y-m-01');
                            break;
                        case 'ytd':
                            $fromDate = date('Y-01-01');
                            break;
                        default:
                            break;
                    }
                }
            // }
            $results = DB::select('CALL getLegalEntitiesExportData("'.$fromDate.'","'.$toDate.'")');
            // var_dump($results);
            return json_encode($results);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
        
    public function getRetailerData($retId,$parentLeId)
    {
        try
        {
            $results = DB::table('retailer_flat')
                    ->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'retailer_flat.legal_entity_id')
                    ->leftJoin('gds_orders', 'gds_orders.cust_le_id', '=', 'retailer_flat.legal_entity_id')
//                    ->leftjoin('users as us', 'us.legal_entity_id', '=', 'retailer_flat.legal_entity_id')
                    ->where('retailer_flat.legal_entity_id', $retId)
                    ->select('retailer_flat.*',DB::raw('COUNT(gds_orders.`gds_order_id`) AS orders'), 'legal_entities.gstin','legal_entities.fssai')
                    ->first();
            if(property_exists($results, 'business_start_time'))
            {
                $results->business_start_time = date('h:i a', strtotime($results->business_start_time));
            }
            if(property_exists($results, 'business_end_time'))
            {
                $results->business_end_time = date('h:i a', strtotime($results->business_end_time));
            }
            $results->updated_at = date('d-m-Y', strtotime($results->updated_at));
            $results->updated_at = $results->updated_at.' '.$results->updated_time;
            $results->created_at = date("d-m-Y", strtotime($results->created_at));
            $productRepo = new ProductRepo();
            $history = $productRepo->getApprovalHistory('legal_entities', 'legal_entity_id', $retId);
            $states_data = $this->getStates();
            DB::enableQueryLog();
            /*$hubCollection = DB::table('wh_serviceables')
                    ->join('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'wh_serviceables.le_wh_id')
                    ->join('dc_hub_mapping', 'dc_hub_mapping.dc_id', '=', 'wh_serviceables.le_wh_id')
                    ->where('wh_serviceables.pincode', $results->pincode)
                    ->where('legalentity_warehouses.dc_type', 118001)
                    ->lists('dc_hub_mapping.hub_id');*/
            $hubsList = DB::table('legalentity_warehouses')
                    ->where(['legalentity_warehouses.dc_type' => 118002, 
                        'legalentity_warehouses.status' => 1])
                    //->whereIn('legalentity_warehouses.le_wh_id', $hubCollection)
                    ->select('legalentity_warehouses.le_wh_id', 'legalentity_warehouses.lp_wh_name')
                    ->get()->all();
            $hubId = $results->hub_id;
            $spokeId = $results->spoke_id;
            $spokesList = DB::table('spokes')
                        //->where('spokes.le_wh_id', $hubId)
                        ->select('spoke_id', 'spoke_name')
                        ->get()->all();
            $hubsSpokesList = DB::table('pjp_pincode_area')
                        ->select('pjp_pincode_area.pjp_pincode_area_id', 'pjp_pincode_area.pjp_name', 
                        'spokes.spoke_id', 'spokes.spoke_name', 'spokes.le_wh_id', 'legalentity_warehouses.lp_wh_name')
                        ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                        ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'spokes.le_wh_id')
                        ->where('pjp_pincode_area.pjp_pincode_area_id', '>', 0)
                        ->where('legalentity_warehouses.status', 1)
                        ->orderBy('pjp_pincode_area.pjp_pincode_area_id');
            // Checking the Global Access to View & Assign all the Beats
            $globalAccess = $this->roleAccessObj->checkPermissionByFeatureCode("GLB0001");
            if(!$globalAccess){
                // If the logged in User doesnot have access then we
                // restrict him with specific legal entity beats
                $legalEntityId = Session::get('legal_entity_id');
                $hubsSpokesList = $hubsSpokesList->where('legalentity_warehouses.legal_entity_id',$legalEntityId);
            } // Finally GET is down here
            $hubsSpokesList = $hubsSpokesList->get()->all();
            $hubsSpokesCollection = [];         
            if(!empty($hubsSpokesList))         
            {
                foreach($hubsSpokesList as $hubsSpokesData)
                {
                    $hubsSpokesCollection[$hubsSpokesData->pjp_pincode_area_id] = ['spoke_id' => $hubsSpokesData->spoke_id, 'le_wh_id' => $hubsSpokesData->le_wh_id];
                    $beats[] = (object)['pjp_pincode_area_id' => $hubsSpokesData->pjp_pincode_area_id, 'pjp_name' => $hubsSpokesData->pjp_name.' - '.$hubsSpokesData->spoke_name. ' - '.$hubsSpokesData->lp_wh_name];
                }
            }
            $gstStateCodesList = DB::table('zone')
                                ->where('gst_state_code', '>', 0)
                                ->orderBy('gst_state_code', 'ASC')
                                ->pluck(DB::raw('group_concat(gst_state_code) as gst_state_code'))->all();
            $gstStateCodes = [];
            if(!empty($gstStateCodesList))
            {
                //$gstStateCodes = property_exists($gstStateCodesList, 'gst_state_code') ? $gstStateCodesList->gst_state_code : [];
                $gstStateCodes = isset($gstStateCodesList[0]) ? $gstStateCodesList[0] : [];
            }               
//            echo "<pre>";print_R($hubsList);die;
            $businessTypes = $this->roleAccessObj->getMasterLookupData('Customer Types');
            $segmentTypes = $this->roleAccessObj->getMasterLookupData('Business Segments');
            if($results->business_type_id == 47001){
                $segmentTypes1 = $this->roleAccessObj->getMasterLookupData('Company Types');
                $segmentTypes = array_merge($segmentTypes,$segmentTypes1);
                $beats = DB::table('pjp_pincode_area')
                    ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                    ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'spokes.le_wh_id')
                    ->select(DB::raw("concat(pjp_pincode_area.`pjp_name`,'-',spokes.`spoke_name`,'-',legalentity_warehouses.`display_name`) as pjp_name"),'pjp_pincode_area_id')->get();
            }
            else{
                $beats=$this->getBeatDataForLeId($parentLeId);
            }
            $masterManufacturers = $this->roleAccessObj->getMasterLookupData('Master Manf');
            $areas = $this->roleAccessObj->getAreaList($results->pincode);
            if($areas == 0)
                $areas = [];
            //$beats = $this->roleAccessObj->getBeatData($spokeId);
            $docArray = json_decode($this->getDocumentList($retId));
            $documents = [];
            if(!empty($docArray))
            {
                $documents = property_exists($docArray, 'Records') ? $docArray->Records : [];
            }
            $volumes = $this->getVolumes();
            $countriesList = Caching::getGlobalElement('countries_list');
            $countries = [];
            if($countriesList != '')
            {
                $countries = json_decode(json_encode($countriesList), true);
            }else{
                $countries = countries::where('status', 1)->select('country_id', 'name')->get()->all();
            $countries = json_decode(json_encode($countries), true);
                Caching::setGlobalElement('countries_list', $countries);
            }

            $cash = DB::TABLE('user_ecash_creditlimit as u')
                ->leftJoin('users', 'users.user_id', '=', 'u.user_id')
                ->select(DB::RAW('IFNULL(u.cashback,0)-IFNULL(u.applied_cashback,0) as ecash'),'u.creditlimit','u.user_id')
                ->where('users.legal_entity_id',$retId)
                ->where('users.is_parent',1)
                ->first();
            $ecash = isset($cash->ecash)?round($cash->ecash,2):0;
            $creditlimit = isset($cash->creditlimit)?round($cash->creditlimit,2):0;
            $user_id = isset($cash->user_id)?round($cash->user_id,2):0;
            $creditlimitEditAccess = $this->roleAccessObj->checkPermissionByFeatureCode("LOCE001");
            $ecashavailEditAccess = $this->roleAccessObj->checkPermissionByFeatureCode("EAE001");
            $paymentobj = new PaymentController();
            $creditApprovalHistory = $paymentobj->getApprovalHistory('Credit Limit', $user_id);
            //$beats=$this->getBeatDataForPincode($results->pincode);

            $businessUnitsData=$this->users->getBusinesUnitData();
            $userData = DB::table('users as u')
                        ->join('user_preferences as up','u.user_id','=','up.user_id')
                        ->where('u.legal_entity_id',$retId)
                        ->select('up.sms_subscription as sms_notification')
                        ->get()->all();
            $sms_notification=0;
            if(count($userData)>0){
                if(isset($userData[0]->sms_notification))
                $sms_notification=isset($userData[0]->sms_notification)?$userData[0]->sms_notification:0;   
            }

        $currUrl = URL::current(); 
        $urlArray = explode('/',$currUrl);
        if(isset($urlArray[0]) && $urlArray[0]=='https'){
        $mapurl = "https://maps.googleapis.com/maps/api/js?key=".env('GOOGLE_MAP_URL_KEY')."&libraries=places";
        }
        else{
        $mapurl = "http://maps.googleapis.com/maps/api/js?key=".env('GOOGLE_MAP_URL_KEY')."&libraries=places";    
        }
            $results->sms_notification=$sms_notification;
            return [
                'mapurl' => $mapurl,
                'areas' => $areas,
                'beats' => $beats,
                'ecash' => $ecash,
                'volumes' => $volumes,
                'history' => $history,
                'retailers' => $results,
                'hubsList' => $hubsList,
                'docArray' => $documents, 
                'countries' => $countries,
                'documents' => $documents,
                'spokesList' => $spokesList,
                'creditlimit' => $creditlimit,
                'states_data' => $states_data,
                'segmentTypes' => $segmentTypes,
                'businessTypes' => $businessTypes,
                'gst_state_codes' => $gstStateCodes,
                'masterManufacturers' => $masterManufacturers,
                'creditlimitEditAccess' => $creditlimitEditAccess,
                'ecashavailEditAccess' => $ecashavailEditAccess,
                'hubsSpokesCollection' => json_encode($hubsSpokesCollection),
                'creditApprovalHistory'=>$creditApprovalHistory,
                'businessunit' => $businessUnitsData
            ];
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            return "Sorry something went wrong. Please check the logs and try again later";
        }
    }
    
    public function getDocumentList($legalEntityId)
    {
        try
        {
            $results['Records'] = [];
            if($legalEntityId > 0)
            {
                $collection = [];
                $docCollection = $this->roleAccessObj->getDocumentList($legalEntityId);
                if(!empty($docCollection))
                {
                    $i = 0;
                    foreach($docCollection as $docData)
                    {
                        $docCollection[$i]->doc_preview = $docData->doc_type;
//                        $docCollection[$i]->download_link = '<a href="'.url('/').$docData->doc_url.'" target="_blank">Download</a>';
//                        $docCollection[$i]->created_by = $this->roleAccessObj->getUserNameById($docData->created_by);
                        $i++;
                    }
                    $collection = $docCollection;
                }
                $results['Records'] = $collection;
            }
            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode($results);
    } 
    
    public function getStates() {
        try {
            $states_data = DB::table('zone')
                    ->where('country_id', 99)
                    ->select('zone_id', 'country_id', 'name', 'code')
                    ->get()->all();
            return $states_data;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getVolumes() {
        try {
            $volumes = DB::table('master_lookup')
                    ->leftJoin('master_lookup_categories', 'master_lookup_categories.mas_cat_id', '=', 'master_lookup.mas_cat_id')
                    ->where('master_lookup_categories.mas_cat_name', 'Volume Classes')
                    ->select('master_lookup.value', 'master_lookup.master_lookup_name')
                    ->get()->all();
            return $volumes;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getLegalEntityIdByCode($retailerCode)
    {
        try
        {
            $legalEntityId = 0;
            if($retailerCode != '')
            {
                $legalEntityDetails = DB::table('legal_entities')
                        ->where('le_code', $retailerCode)
                        ->pluck('legal_entity_id')->all();
                if(!empty($legalEntityDetails))
                {
                    $legalEntityId = isset($legalEntityDetails[0]) ? $legalEntityDetails[0] : 0;
                }
            }
            return $legalEntityId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getBeatByName($beatName)
    {
        try
        {
            $beatId = 0;
            if($beatName != '')
            {
                $beatDetails = DB::table('pjp_pincode_area')
                        ->where('pjp_name', $beatName)
                        ->pluck('pjp_pincode_area_id')->all();
                if(empty($beatDetails))
                {
                    $beatId = $this->createBeatByName($beatName);
                }else{
                    $beatId = isset($beatDetails[0]) ? $beatDetails[0] : 0;
                }
            }
            return $beatId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function createBeatByName($beatName)
    {
        try
        {
            $beatId = 0;
            if($beatName != '')
            {
                $insertData['pjp_name'] = $beatName;
                $beatId = DB::table('pjp_pincode_area')
                        ->insertGetId($insertData);
            }
            return $beatId;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateBeat($retailerCode, $beatName)
    {
        try
        {
            if($beatName !== '')
            {
                $beatId = $this->getBeatByName($beatName);
                $legalEntityId = $this->getLegalEntityIdByCode($retailerCode);
                if($beatId > 0 && $legalEntityId > 0)
                {
                    $customerId = DB::table('customers')->where('le_id', $legalEntityId)->pluck('id')->all();
                    if(!empty($customerId))
                    {
                        DB::table('customers')
                            ->where('le_id', $legalEntityId)
                            ->update(['beat_id' => $beatId]);
                    }else{
                        DB::table('customers')->insert(['le_id' => $legalEntityId, 'beat_id' => $beatId]);
                    }
                }
            }else{
                $beatId = 0;
                $legalEntityId = $this->getLegalEntityIdByCode($retailerCode);
                if($legalEntityId > 0)
                {
                    DB::table('customers')
                            ->where('le_id', $legalEntityId)
                            ->update(['beat_id' => $beatId]);
                }
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateRetailerDetails($retailerCode, $retailer)
    {
        try
        {
            if($retailerCode != 0 && !empty($retailer))
            {
                $businessLegalName = isset($retailer['shop_name']) ? $retailer['shop_name'] : '';
                $retailerName = isset($retailer['name']) ? $retailer['name'] : '';
                $retailerName = isset($retailer['name']) ? $retailer['name'] : '';

            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getButtonPermissions()
    {
        try
        {
            $buttonFeatureCodes = ['RET006', 'RET007', 'RET008'];
            $buttonFeaturePermissions = [];
            foreach($buttonFeatureCodes as $features)
            {
                $buttonFeaturePermissions[$features] = $this->roleAccessObj->checkPermissionByFeatureCode($features);
            }
            return $buttonFeaturePermissions;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function updateFlatTable($legalEntityId,$parent_le_id = null)
    {
        try
        {
            // Log::info(__METHOD__);
   //          Log::info('legal_entity_id of retailer'.$legalEntityId);
            if($legalEntityId > 0)
            {
                // DB::enableQueryLog();
                DB::beginTransaction(); 
                $legalEntityDetails = DB::table('retailer_flat')
                        ->where('legal_entity_id', $legalEntityId)
                        ->first(['le_code']);
                $aadhar_id=DB::table('users')
                        ->where('legal_entity_id',$legalEntityId)
                        ->first(['aadhar_id','mobile_no']);
                $aadhar_id=json_decode(json_encode($aadhar_id),1);
                $results = DB::select('CALL getLegalEntitiesDataById(0,0,'.$legalEntityId.')');
                $response = [];
                if($parent_le_id!=null){
                    $response['parent_le_id']=$parent_le_id;
                }
                // Log::info('results'); 
                // Log::info((array)$results);  
                if(!empty($results))
                {
                    $response = json_decode(json_encode($results), true);
                    $response = isset($response[0]) ? $response[0] : $response;
                    if($parent_le_id== null){
                        //echo $response['pincode'];
                        $parent_le_id=$this->getParentIdFromLegalEntity($response['legal_entity_id']);
                        $response['parent_le_id']=$parent_le_id;
                    }
                }
                $response['aadhar_id']=$aadhar_id['aadhar_id'];
                $response['mobile_no']=$aadhar_id['mobile_no'];
                if(!empty($legalEntityDetails))
                {
                    $legalEntityId = $response['legal_entity_id'];
                    if(isset($response['legal_entity_id']))
                    {
                        unset($response['legal_entity_id']);
                    }
                    if(isset($response['le_code']))
                    {
                        unset($response['le_code']);
                    }
                    // Log::info('hub_id'.$response['hub_id']); 

                    if(isset($response['hub_id']) && $response['hub_id'] == 0)
                    {
                        $beatId = isset($response['beat_id']) ? $response['beat_id'] : 0;
                        // Log::info('beat_id'.$beatId); 
                        if($beatId > 0)
                        {   
                            $hubDetails = DB::table('pjp_pincode_area')
                            ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                            ->where('pjp_pincode_area_id', $beatId)
                            ->select('pjp_pincode_area.spoke_id', 'pjp_pincode_area.le_wh_id')
                            ->first();
                             // Log::info('hubDetails'); 
                             //  Log::info($hubDetails); 
                            if(!empty($hubDetails))
                            {
                                $hubId = property_exists($hubDetails, 'le_wh_id') ? $hubDetails->le_wh_id : 0;
                                $spokeId = property_exists($hubDetails, 'spoke_id') ? $hubDetails->spoke_id : 0;
                                if($hubId > 0 && $spokeId > 0)
                                {
                                    $response['hub_id'] = $hubId;
                                    $response['spoke_id'] = $spokeId;                                   
                                    $updateDetails['hub_id'] = $hubId;
                                    $updateDetails['spoke_id'] = $spokeId;
                                    DB::table('customers')
                                    ->where('le_id', $legalEntityId)
                                    ->update($updateDetails);
                                }
                            }
                        }
                    }
                    // Log::info('response'); 
                    //  Log::info($response);
                    unset($response['sms_notification']);
 
                    DB::table('retailer_flat')
                            ->where('legal_entity_id', $legalEntityId)
                            ->update($response);
                }else{
                    // Log::info('hub_id_else'.$response['hub_id']); 
                   
                    if(isset($response['hub_id']) && $response['hub_id'] == 0)
                    {
                        $beatId = isset($response['beat_id']) ? $response['beat_id'] : 0;
                        // Log::info('beatId_else'.$beatId); 
                        if($beatId > 0)
                        {   
                            $hubDetails = DB::table('pjp_pincode_area')
                            ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                            ->where('pjp_pincode_area_id', $beatId)
                            ->select('pjp_pincode_area.spoke_id', 'pjp_pincode_area.le_wh_id')
                            ->first();
                            // Log::info('hubDetails_else'); 
                            //Log::info($hubDetails); 
                            if(!empty($hubDetails))
                            {
                                $hubId = property_exists($hubDetails, 'le_wh_id') ? $hubDetails->le_wh_id : 0;
                                $spokeId = property_exists($hubDetails, 'spoke_id') ? $hubDetails->spoke_id : 0;
                                if($hubId > 0 && $spokeId > 0)
                                {
                                    $response['hub_id'] = $hubId;
                                    $response['spoke_id'] = $spokeId;               
                                    $updateDetails['hub_id'] = $hubId;
                                    $updateDetails['spoke_id'] = $spokeId;
                                       // Log::info('updateDetails'); 
                                       //  Log::info($updateDetails); 

									DB::table('customers')
									->where('le_id', $legalEntityId)
									->update($updateDetails);
								}
							}
						}
					}

                     // Log::info('last_response'); 
                     // Log::info($response);
                    unset($response['sms_notification']);
                    DB::table('retailer_flat')->insert($response);                  
                }
                // Log::info(DB::getQueryLog());
                DB::commit();
            }
        } catch (\ErrorException $ex) {
            DB::rollback();
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getUserCreditDetails($user_ecash_id) {
        try {
            // here userid parameter refers to user_ecash_id in user_ecash_creditlimit table
            $userdata = DB::table('user_ecash_creditlimit as e')
                        ->join('users as u','u.user_id','=','e.user_id')
                        ->join('legal_entities as l','l.legal_entity_id','=','u.legal_entity_id')
                        //->where('e.user_id',$user_id)
                        ->where('e.user_ecash_id',$user_ecash_id)
                        ->select('e.user_id','e.user_ecash_id','e.pre_approve_limit','e.creditlimit','e.approval_status','e.le_id'
                                ,DB::raw('IF(e.approval_status=1,getMastLookupValue(57199),getMastLookupValue(e.approval_status)) as approval_status_name'),DB::raw('GetUserName(e.user_id,2) as user_name'),'l.business_legal_name')->first();
            return $userdata;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function saveDetailsIntoMappingTable($data,$rt_Id){
      $checkbox_active = isset($data['is_mfc_active']) ? 1 : 0;

      if($checkbox_active)
      {
        $update_Customer_Mapping_inactive = DB::table('mfc_customer_mapping')->where('cust_le_id', $rt_Id)->update(['is_active' => 0]);
      }

      $mfc_Customer_Mapping = DB::table('mfc_customer_mapping')->insert(['mfc_id'=>$data['b_name'],'is_active' => $checkbox_active,'credit_limit'=>$data['c_limit'],'cust_le_id'=>$rt_Id]);


      return $mfc_Customer_Mapping;
    }

    public function businessNames(){

       $query = DB::table('legal_entities')->select('legal_entity_id', 'business_legal_name')->where('legal_entity_type_id','=',1015)->get()->all();

       return $query;
    }
    public function gridData($getId){

        $act = "'<center><code>','<a data-toggle=\'modal\' onclick =\'editDetails(/',mfc.cust_mfc_id,'/)\'/ > <i class=\'fa fa-pencil\'></i> </a>&nbsp;&nbsp;&nbsp;</code></center>'";


        $details = "SELECT  mfc.`cust_mfc_id`, mfc.`cust_le_id`, mfc.`credit_limit`,mfc.`mfc_id`,IF(mfc.`is_active` =1,'Yes','No') AS is_active,le.`business_legal_name` as name ,CONCAT(".$act.") as actions FROM  `mfc_customer_mapping` AS `mfc` JOIN `legal_entities` AS `le` ON `le`.`legal_entity_id` = `mfc`.`mfc_id` and mfc.cust_le_id = $getId";

        $query = DB::select(DB::raw($details));

        return $query;
    }

    public function getGridEditData($id){

        $query = DB::table('mfc_customer_mapping')->select('*')->where('cust_mfc_id','=',$id)->get()->all();
     
         return $query;

    }
    public function updateLenderData($request){
        try{

        $data = $request->all();

        $id = $data['edit_mfc_id'];

        $checkbox_active = isset($data['mfc_is_active']) ? 1 : 0;

        $mfcDetails = DB::table('mfc_customer_mapping')->select('*')->where('cust_mfc_id','=',$id)->first();

        $userDetails = DB::table('users')->select('user_id')->where('legal_entity_id','=',$mfcDetails->cust_le_id)->where('is_parent',1)->first();

        $userEcash= DB::table('user_ecash_creditlimit')->select(['user_ecash_id','creditlimit'])->where('user_id','=',$userDetails->user_id)->first();

        $update = DB::table('mfc_customer_mapping')
                    ->where('cust_mfc_id', '=',$id)
                    ->update([                     
                            'mfc_id'           =>  $data['mfc_mapping_dropdown'], 
                            'credit_limit'     =>  $data['edit_c_limit'],
                            'is_active'        =>  $checkbox_active
                            ]);

            if($checkbox_active){ 

             $usersTable = DB::table('user_ecash_creditlimit')
                     ->where('user_ecash_id','=',$userEcash->user_ecash_id)
                     ->update([                     
                             'creditlimit' => $data['edit_c_limit'],
                            ]); 

                    }        

          return  True;

        }catch(\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return Response::json(array('status' => 500));
        }           

    }

    public function checkLeId($result){
    //try{
        $aadharNumber='';
        $message='';
        $mfcCode = $result['mfc_code'];
        $retailerCode=$result['retailer_code'];
        $creditLimit = $result['credit_limit'];
        $aadharNumber= $result['aadhar_number'];
        $mfcId='';
        $retailerId='';
        $retailerIdOnAadhar='';
        
        if(empty($creditLimit)){
            $message='credit limit is empty';
        }else{
            if($mfcCode!=''){
                $mfcId = DB::table('legal_entities')->select('legal_entity_id')->where('le_code', $mfcCode)->first();
                /*print_r($mfcId);
                echo 'mfc check';*/
                if($retailerCode!=''){
                    $retailerId = DB::table('legal_entities')->select('legal_entity_id')->where('le_code', $retailerCode)->first(); 
                }
                if($aadharNumber!=''){
                    $retailerIdOnAadhar= DB::table('users')->select('legal_entity_id')->where(['aadhar_id'=> $aadharNumber,'is_active' => 1])->first();

                }
                if(isset($mfcId->legal_entity_id)){
                    if($retailerCode!=''||$aadharNumber!=''){ 
                        $checkingIds='';
                        if(isset($retailerId->legal_entity_id)&&isset($retailerIdOnAadhar->legal_entity_id)){
                            if($retailerId->legal_entity_id == $retailerIdOnAadhar->legal_entity_id){
                                $message='correct';
                            }else{
                                $message='Customer Code and Aadhar Number are not matching';
                            }

                        }          
                        else if(isset($retailerId->legal_entity_id)||isset($retailerIdOnAadhar->legal_entity_id)){
                            if(isset($retailerId->legal_entity_id)){                        
                                $message='correct';
                            }
                            else{
                                $message='correct';
                            }
                        }else{
                            if($retailerCode!=''){
                                if(isset($retailerId->legal_entity_id)){

                                }else{
                                    $message='Customer code is not correct';
                                }
                            }
                            if($aadharNumber!=''){
                                if(isset($retailerIdOnAadhar->legal_entity_id)){

                                }else{
                                    $message='Aadhar number is not correct';
                                }
                            }
                        }


                    }else{
                        $message='Customer code and aadhar number are empty';
                    }
                }else{
                    $message='mfc code is empty';
                }
            }
        }
        return $message;          

     /*}catch(\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return False;
        } */
 }
    public function insertLeId($result){        
        $mfcCode = $result['mfc_code'];
        $retailerCode=$result['retailer_code'];
        $creditLimit = $result['credit_limit'];
        $aadharNumber= $result['aadhar_number'];        
        $mfcId='';
        $retailerId='';
        $retailerIdOnAadhar='';
        $mfcId = DB::table('legal_entities')->select('legal_entity_id')->where('le_code', $mfcCode)->first();

        if($retailerCode!=''){
            $retailerId = DB::table('legal_entities')->select('legal_entity_id')->where('le_code', $retailerCode)->first(); 
        }
        if($aadharNumber!=''){
            $retailerIdOnAadhar= DB::table('users')->select('legal_entity_id')->where(['aadhar_id'=> $aadharNumber,'is_active' => 1])->first();
        }
        $query='';
        if(isset($retailerId->legal_entity_id)){
            $checkingIds = DB::table('mfc_customer_mapping')->select(['mfc_id','cust_le_id'])->where(['mfc_id'=> $mfcId->legal_entity_id,'cust_le_id'=>$retailerId->legal_entity_id])->get()->all();
            if(empty($checkingIds)){
               
                $query = DB::table('mfc_customer_mapping')->insert(['mfc_id'=>$mfcId->legal_entity_id,'cust_le_id'=> $retailerId->legal_entity_id,'credit_limit'=>$creditLimit]);
            }
            else{
                $query = DB::table('mfc_customer_mapping')->where(['mfc_id'=> $mfcId->legal_entity_id,'cust_le_id'=>$retailerId->legal_entity_id])->update(['credit_limit'=>$creditLimit]);
            }
        }
        else if(isset($retailerIdOnAadhar->legal_entity_id)){
            $checkingIds = DB::table('mfc_customer_mapping')->select(['mfc_id','cust_le_id'])->where(['mfc_id'=> $mfcId->legal_entity_id,'cust_le_id'=>$retailerIdOnAadhar->legal_entity_id])->get()->all();
            if(empty($checkingIds)){
               
                $query = DB::table('mfc_customer_mapping')->insert(['mfc_id'=>$mfcId->legal_entity_id,'cust_le_id'=> $retailerIdOnAadhar->legal_entity_id,'credit_limit'=>$creditLimit]);

            }
            else{
                $query = DB::table('mfc_customer_mapping')->where(['mfc_id'=> $mfcId->legal_entity_id,'cust_le_id'=>$retailerIdOnAadhar->legal_entity_id])->update(['credit_limit'=>$creditLimit]);
            }
        }
        return $query;
    }


 public function getDcList(){
    $globalAccess = $this->roleAccessObj->checkPermissionByFeatureCode("GLB0001");
    if(!$globalAccess){
        $legal_entity=Session::get('legal_entity_id');
        $query = DB::table('legalentity_warehouses')
                ->select('le_wh_id','lp_wh_name')
                ->where('legal_entity_id',$legal_entity)
                ->where('dc_type',118001)
                ->get()->all();

    }else{
        $query = DB::table('legalentity_warehouses')
                ->select('le_wh_id','lp_wh_name')
                ->where('dc_type',118001)
                ->get()->all();
    }
    return $query;                
 }
    

    public function checkPincodeLegalentity($pincode){
        $getLegalEntity=DB::table('wh_serviceables')
                        ->select('legal_entity_id')
                        ->where('pincode',$pincode)
                        ->get()->all();
        $getLegalEntity=json_decode(json_encode($getLegalEntity),1);
        if(count($getLegalEntity)>0){
            return $getLegalEntity[0]['legal_entity_id'];
        }else{
            return false;
        }
    } 
    public function getParentLeId($pincode){
        $getParentLeId=DB::table('wh_serviceables')
                        ->select('legal_entity_id')
                        ->where('pincode',$pincode)
                        ->get()->all();

        if(count($getParentLeId)>0){
            $getParentLeId=json_decode(json_encode($getParentLeId),1);
            return $getParentLeId[0]['legal_entity_id'];
        }else{
            return false;
        }
    }
    public function getBeatDataForPincode($pincode){

        $beats="select CONCAT(IFNULL(pjp.`pjp_name`,''),' - ',IFNULL(s.`spoke_name`,''),' - ',IFNULL(lew.`display_name`,''),'') AS 'pjp_name',pjp.`pjp_pincode_area_id` FROM wh_serviceables ws, dc_hub_mapping lw, pjp_pincode_area pjp, spokes s, legalentity_warehouses lew WHERE ws.`le_wh_id` = lw.`dc_id`AND pjp.`le_wh_id` = lw.`hub_id`AND s.`spoke_id` = pjp.`spoke_id`AND lew.`le_wh_id` = pjp.`le_wh_id`AND ws.`pincode` = ".$pincode ;

        $beats=DB::select(DB::raw($beats));
        //print_r($beats);exit;

        return $beats;
    }
    public function getBeatDataForLeId($parentId){
        $le_wh_ids = DB::select(DB::raw("select group_concat(le_wh_id) as le_list from legalentity_warehouses where legal_entity_id = $parentId"));
        $le_wh_ids = $le_wh_ids[0]->le_list;
        $le_wh_ids = explode(',',$le_wh_ids);
        $beats = DB::table('pjp_pincode_area')
                    ->leftJoin('spokes', 'spokes.spoke_id', '=', 'pjp_pincode_area.spoke_id')
                    ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'spokes.le_wh_id')
                    ->whereIn('pjp_pincode_area.le_wh_id',$le_wh_ids)
                    ->select(DB::raw("concat(pjp_pincode_area.`pjp_name`,'-',spokes.`spoke_name`,'-',legalentity_warehouses.`display_name`) as pjp_name"),'pjp_pincode_area_id')->get()->all();
        return $beats;
    }
    public function getVerifyData($recordData,$columnName){
        $result="select * from retailer_flat where ".$columnName."=".$recordData;
        $result=DB::select(DB::raw($result));
        print_r($result);exit;
    }
    public function recordResult($data){
        $message='';
        $retailerCode = $data['retailer_code']==''?NULL:trim($data['retailer_code']);
        $aadharNumber = $data['aadhar_number']==''?NULL:trim($data['aadhar_number']);
        $customerType = $data['customer_type']==''?NULL:trim($data['customer_type']);
        $segmentType  = $data['segment_type']==''?NULL:trim($data['segment_type']);
        $beat         = $data['beat']==''?NULL:trim($data['beat']);
        $pincode      = $data['pin_code']==''?NULL:trim($data['pin_code']);
        $state        = $data['state']==''?NULL:trim($data['state']);
        $city         = $data['city']==''?NULL:trim($data['city']);
        $area         = $data['area']==''?NULL:trim($data['area']);
        $latitude     = $data['latitude']==''?NULL:trim($data['latitude']);
        $longitude    = $data['longitude']==''?NULL:trim($data['longitude']);
        $volume       = $data['volume_class']==''?NULL:trim($data['volume_class']);
        $mobile       = $data['mobile']==''?NULL:trim($data['mobile']);
        $shopName     = $data['shop_name']==''?NULL:trim($data['shop_name']);
        $retailername = $data['name']==''?NULL:trim($data['name']);
        $gstin        = $data['gstin']==''?NULL:trim($data['gstin']);
        $noOfShutters = $data['no_of_shutters']==''?NULL:trim($data['no_of_shutters']);
        $createCustomer=0;
        $validRecord=true;
        $retailer_legal_entity=0;
/*        if($retailerCode==NULL){
*/          if($aadharNumber == NULL && $retailerCode==NULL){
                $createCustomer=1;
                $validRecord=true;
                $aadhar_legal_entity_id=0;
            }
            
            if($aadharNumber!=NULL){
               /* if(strlen($aadharNumber)!=12){
                    $validRecord=false;
                    $message.="Aadhar no should be 12 digits";
                }*/
                if($validRecord){
                    /*if(!is_numeric($aadharNumber)){
                        $validRecord=false;
                        $message.="There should not be any special characters in aadhar number";
                    }*/
                    if($validRecord){
                        $aadhar_legal_entity_id=$this->getLegalEntity('aadhar_id',$aadharNumber);
                        if($aadhar_legal_entity_id==0){
                            $createCustomer=1;
                            $validRecord=true;
                        }else{
                            $validRecord=true;
                            $retailer_legal_entity=$aadhar_legal_entity_id;
                        }
                        $data['aadhar_id']=$aadharNumber;
                    }
                }
            }
        //}else{
            if($retailerCode!=NULL){
            if(!ctype_alnum($retailerCode)){
                $validRecord=false;
                $message.="There should not be any special characters in retailercode";
            }
            if($validRecord){
                $retailer_legal_entity=$this->getLegalEntity('le_code',$retailerCode);
                if($aadharNumber != NULL){
                    $aadhar_legal_entity_id=$this->getLegalEntity('aadhar_id',$aadharNumber);
                    if($retailer_legal_entity == $aadhar_legal_entity_id){
                        if($retailer_legal_entity  == 0){
                            $message.="aadhar number and retailer code are wrong";
                            $validRecord=false;

                        }
                    }else{
                        if($retailer_legal_entity>0 && ($aadhar_legal_entity_id == 0 && $data['aadhar_number']=='')){
                            $validRecord=true;
                        }else if($retailer_legal_entity>0 && ($aadhar_legal_entity_id == 0 && $data['aadhar_number']!='')){

                            $createCustomer=0;
                            $validRecord=true;
                        }else{
                            $message.="aadhar number and retailer code are not matching"; 
                            $validRecord=false;
                        }
                       
                    }
                }else if($retailer_legal_entity == 0){
                    $message.="retailer code is wrong";
                    $validRecord=false;

                }else{
                    $validRecord=true;
                }
            }
        }
        if($validRecord){
            if($customerType!=NULL){
                $customer_type_id=$this->getId(3,$customerType);
                if($customer_type_id == '' ){
                    $validRecord=false;
                    $message.="Customer Type is wrong";
                }else{
                    $data['customer_id']=$customer_type_id;
                }
            }else{
                $validRecord=false;
                $message.="Customer type is required";
            }
            if($validRecord){
                if($segmentType!=NULL){
                    $segment_type_id=$this->getId(48,$segmentType);
                    if($segment_type_id == '' ){
                        $validRecord=false;
                        $message.="Segment Type is wrong";
                    }else{
                        $data['segment_id']=$segment_type_id;
                    }
                }else{
                    $validRecord=false;
                    $message.="Segment type is required";
                }
                if($validRecord){                    
                    if($pincode!=NULL){
                        $pincodequery =DB::table('wh_serviceables as whs')
                                ->select(DB::raw("count(le_wh_id) as count"))
                                ->where("whs.pincode",'=', $pincode)
                                ->get()->all();
                        $pincodequery=json_decode(json_encode($pincodequery),1);
                        $pincodequery=$pincodequery[0]['count'];
                        if($pincodequery == 0){
                            $validRecord=false;
                            $message.="The given pincode is not serviceable";
                        }
                    }else{
                        $validRecord=false;
                        $message.="pincode is required";
                    }
                    if($validRecord){
                        if($city != NULL){
                            if (ctype_alpha(str_replace(' ', '', $city)) === false) {
                                $validRecord=false;
                                $message.="There should not be any special characters in city";
                            }
                            if($validRecord){
                                $city_id = DB::table('cities_pincodes as cp')
                                           ->select("cp.city_id")
                                           ->where("cp.pincode", "=", $pincode)
                                           ->where("cp.city","=",$city)
                                           ->get()->all();
                                if(count($city_id)>0){                  
                                    $city_id=json_decode(json_encode($city_id),1);
                                    $city_id=$city_id[0]['city_id'];
                                    $data['area_id']=$city_id;
                                }else{
                                    $validRecord=false;
                                    $message.="The given city and pincode configuration is wrong";
                                }
                            }
                            if($validRecord){
                                if($area!= NULL){
                                    $city_id = DB::table('cities_pincodes as cp')
                                           ->select("cp.city_id")
                                           ->where("cp.pincode", "=", $pincode)
                                           ->where("cp.officename","=",$area)
                                           ->where("cp.city",'=',$city)
                                           ->get()->all();
                                    if(count($city_id)>0){                  
                                        $city_id=json_decode(json_encode($city_id),1);
                                        $city_id=$city_id[0]['city_id'];
                                        $data['area_id']=$city_id;
                                    }else{
                                        $validRecord=false;
                                        $message.="The given pincode,city and area configuration is wrong";
                                    }
                                }else{
                                    $data['area_id']="";
                                }
                            }
                        }else{
                            $validRecord=false;
                            $message.="City is required";
                        }
                        if($validRecord){
                            if($state!=NULL){
                                $state_id=DB::table('zone')
                                        ->select("zone_id")
                                        ->where(["country_id"=>99,"name"=>$state])
                                        ->get()->all();
                                if(empty($state_id)){
                                    $validRecord=false;
                                    $message.="State name is wrong";
                                }else{
                                    $state_id=$state_id[0]->zone_id;
                                    $data['state_id']=$state_id;
                                }

                            }else{
                                $validRecord=false;
                                $message.="State is required"; 
                            }
                            if($validRecord){
                                if($beat!=NULL){
                                    $beat_id=DB::select(DB::raw("select pjp.pjp_pincode_area_id from 
                                            wh_serviceables ws,dc_hub_mapping lw ,pjp_pincode_area pjp ,spokes s,  legalentity_warehouses lew where ws.le_wh_id = lw.dc_id and pjp.le_wh_id = lw.hub_id 
                                              and s.spoke_id = pjp.spoke_id 
                                              and lew.le_wh_id = pjp.le_wh_id 
                                              and ws.pincode = '$pincode'
                                              and pjp.pjp_name = '$beat'"));
                                    if(empty($beat_id)){
                                        $validRecord=false;
                                        $message.="Beat pincode and given pincode are not matching";
                                    }else{
                                        $data['beat_id']=$beat_id[0]->pjp_pincode_area_id;
                                    }
                                }else{
                                    $validRecord=false;
                                    $message.="Beat is required";
                                }
                                if($validRecord){
                                    if($latitude == NULL){
                                       
                                    }else{
                                       if(!is_numeric($latitude)){
                                            $validRecord=false;
                                            $message.="You should only enter the numbers in latitude field";
                                       } 
                                    }
                                    if($validRecord){
                                        if($longitude==NULL){
                                            
                                        }
                                        else{
                                           if(!is_numeric($longitude)){
                                                $validRecord=false;
                                                $message.="You should only enter the numbers in longitude field";
                                           } 
                                        }
                                    }
                                    if($validRecord){
                                        if($mobile!=NULL){
                                            if(strlen($mobile)!=10){
                                                $validRecord=false;
                                                $message.="mobile no is not valid";
                                            }else{
                                                if(!(is_numeric($mobile))){
                                                    $validRecord=false;
                                                    $message.="There should  be only numbers in mobile no";
                                                }else{
                                                    $result=$this->validateMobileNo($mobile,$retailer_legal_entity);
                                                    if($result == 0){

                                                    }
                                                    else if($result > 0){
                                                        $validRecord=false;
                                                        $message.="User already exist with this mobile no";
                                                    }
                                                }                                                
                                            }
                                        }else{
                                            $validRecord=false;
                                            $message.="Mobile no is required";
                                        }
                                        if($validRecord){
                                            if($gstin!=NULL){
                                                if(!ctype_alnum($gstin)){
                                                    $validRecord=false;
                                                    $message.="There should not be any special characters in GSTIN";
                                                }
                                                if($validRecord){
                                                    if(strlen($gstin)!=15){
                                                        $validRecord=false;
                                                        $message.=" gstin no is not valid";
                                                    }
                                                }
                                            }else{
                                                $data['gstin']='';
                                            }
                                            if($validRecord){
                                                if($noOfShutters!=NULL){
                                                    if(!(is_numeric($noOfShutters))){
                                                        $validRecord=false;
                                                        $message.="There should  be only numbers in no of shutters";
                                                    }
                                                }else{
                                                    $data['no_of_shutters']='';
                                                }
                                                if($validRecord){
                                                    if($data['address'] == ''){
                                                        $validRecord=false;
                                                        $message.="address is required";
                                                    }
                                                    else{
                                                       /* if(!preg_match('/^[a-z0-9 .]+$/i',$data['address'])){
                                                            $validRecord=false;
                                                            $message.="There should not be any special characters in address";
                                                        }*/
                                                    }
                                                    
                                                } 
                                                if($validRecord){
                                                    if($data['smart_phone']!=''){
                                                        $data['smart_phone']=strtolower($data['smart_phone']);
                                                        if($data['smart_phone']!='yes'&&$data['smart_phone']!='no'){
                                                            $validRecord=false;
                                                            $message.="you should enter either yes/no in smartphone field";
                                                        }
                                                    }
                                                    if($validRecord){
                                                        if($data['internet_availability']!=''){
                                                            $data['internet_availability']=strtolower($data['internet_availability']);
                                                            if($data['internet_availability']!='yes'&&$data['internet_availability']!='no'){
                                                                $validRecord=false;
                                                                $message.="you should enter either yes/no in internet availability field";
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            if($validRecord){
                                                if($shopName==NULL){
                                                    $validRecord=false;
                                                    $message.="shop name is required";
                                                }
                                                else{
                                                    /*if (ctype_alpha(str_replace(' ', '', $shopName)) === false) {
                                                        $validRecord=false;
                                                        $message.="There should not be any special characters in shop name";
                                                    }*/
                                                }
                                                if($validRecord){
                                                    if($retailername == NULL){
                                                        $validRecord=false;
                                                        $message.="name is required";
                                                    }
                                                    else{
                                                        if (ctype_alpha(str_replace(' ', '', $retailername)) === false) {
                                                        //if(!ctype_alnum($retailername)){
                                                           // echo $retailerName;exit;
                                                            $validRecord=false;
                                                            $message.="Enter only characters";
                                                        }
                                                    }
                                                    if($validRecord){
                                                        if($data['business_start_time']!='' && $data['business_start_time']!=''){
                                                        
                                                            $bstart=substr($data['business_start_time'],11);
                                                            $bend=substr($data['business_end_time'],11);
                                                            $bstartdup=substr($data['business_start_time'],11,2);
                                                            $benddup=substr($data['business_end_time'],11,2);
                                                            if($bstartdup > $benddup || $bstartdup == $benddup){
                                                                $validRecord=false;
                                                                $message.=" Give proper business start time and end time";
                                                            }
                                                             if($bstartdup == '00' && $benddup='00'){
                                                                $validRecord=false;
                                                                $message.=" Please enter start time and end time in hh:mm::ss format";
                                                            }

                                                            if($validRecord){
                                                                if(preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $bstart) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $bend)){

                                                                    $data['new_bstart']=$bstart;
                                                                    $data['new_bend']=$bend;
                                                                }
                                                                else{
                                                                    $validRecord=false;
                                                                    $message.="Please enter start time and end time in hh:mm::ss format";
                                                                }
                                                            }
                                                        }else{
                                                            
                                                            if($data['business_start_time'] == '' && $data['business_end_time'] == ''){
                                                                $data['new_bstart']='';
                                                                $data['new_bend']='';
                                                            }else{
                                                                $validRecord=false;
                                                                $message.="Please enter business start and end time both ";
                                                            }

                                                        }
                                                       /* if($data['locality'] == ''){
                                                            $validRecord=false;
                                                            $message.="locality is required";
                                                        }
                                                        if($validRecord){
                                                            if($data['landmark'] == ''){
                                                                $validRecord=false;
                                                                $message.="landmark is required";
                                                            }
                                                            
                                                        } */  
                                                    }   
                                                }

                                            }
                                        }
                                        
                                    }
                                }

                            }
                        
                        }
                       
                    }
                }
            }
        }
        $data['createCustomer']=$createCustomer;
        $data['legal_entity_id']=$retailer_legal_entity;
        if($validRecord){
            if($volume!=NULL){
                $volume=$this->getId(96,$volume);
                $data['volume_class']=$volume;
                if($volume==''){
                    $validRecord=false;
                    $message.='given volume class is wrong';
                }
            }
            else{
                $data['volume_class']='';
            }
        }
        if($message!=''){
            $data['error']=$message;
        }
        return $data;       
    }
    public function getLegalEntity($columnname,$value){
        $count="select count(*) as count from retailer_flat where ".$columnname."="."'".$value."'";
        $count=DB::Select(DB::raw($count));
        $count=json_decode(json_encode($count),1);
        $count=$count[0]['count'];
        if($count>0){
            $legal_entity_id=DB::Select(DB::raw("select legal_entity_id from retailer_flat where ".$columnname."="."'".$value."'"));
            $legal_entity_id=json_decode(json_encode($legal_entity_id),1);
            $legal_entity_id = $legal_entity_id[0]['legal_entity_id'];
            return $legal_entity_id;
        }else{
            return  0;     
        }
    }
    public function getId($catValue,$value){
        $getValue=DB::table('master_lookup')
                ->select('value')
                ->where(['mas_cat_id'=>$catValue,'master_lookup_name'=>$value])
                ->get()->all();
        if(count($getValue)>0){
            $getValue=json_decode(json_encode($getValue),1);
            $getValue=$getValue[0]['value'];
            return $getValue;
        }else{
            return '';
        }
    }
    public function getSpoke($beat){
        $getValue=DB::table('pjp_pincode_area')
                ->select('spoke_id')
                ->where(['pjp_pincode_area_id'=>$beat])
                ->get()->all();
        if(count($getValue)>0){
            $getValue=json_decode(json_encode($getValue),1);
            $getValue=$getValue[0]['spoke_id'];
            return $getValue;
        }else{
            return '';
        }
    }
    public function validateMobileNo($mobile,$legalEntity){
        $mobileStatus = DB::table('users')
                        ->where([['mobile_no',$mobile],['is_active',1],['legal_entity_id','<>',$legalEntity]])                        
                        ->count();
        return $mobileStatus;
    }
    public function checkHubLegalentity($hub_id){
        $getLegalEntity=DB::table('legalentity_warehouses')
                        ->select('legal_entity_id')
                        ->where('le_wh_id',$hub_id)
                        ->get()->all();
        $getLegalEntity=json_decode(json_encode($getLegalEntity),1);
        if(count($getLegalEntity)>0){
            return $getLegalEntity[0]['legal_entity_id'];
        }else{
            return false;
        }
    }
    public function getExcelData(){
        $getCityPincode=DB::table('cities_pincodes')
                        ->select(DB::raw("concat(`pincode`,'-',`officename`,'-',`city`,'-',`state`) as data"))
                        ->get()->all();
        return $getCityPincode;
    }
    public function getNameFromMaster($Value){
        $getValue=DB::table('master_lookup')
                ->select('master_lookup_name')
                ->whereIn('value',$Value)
                ->get()->all();
        $result=Array();
        if(count($getValue)>0){
            $getValue=json_decode(json_encode($getValue),1);
            foreach ($getValue as $value) {
                $result[]=$value['master_lookup_name'];
            }
            return $result;
        }else{
            return '';
        }
    }
    public function deleteRetailer($ret_id){
        $result=DB::selectFromWriteConnection(DB::raw("CALL get_DeleteRetailerByLeID($ret_id)"));
        return $result[0]->Is_Deleted;
    }
    public function getParentIdFromLegalEntity($legal_entity_id){
        $parent= DB::table('legal_entities')
                ->select('parent_le_id')
                ->where('legal_entity_id',$legal_entity_id)
                ->first();

      if(isset($parent->parent_le_id) and !empty($parent->parent_le_id))
        return $parent->parent_le_id;
      return '';
    }
    public function generateCreditLimitReport(){
        $query = DB::selectFromWriteConnection(DB::raw("CALL getCustomerCreditLimit()"));
        return $query;
    }

    public function getfeedback($makeFinalSql,$orderBy,$page,$pageSize,$legalEntityId)
    {
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }else{
            $orderBy = ' ORDER BY fid desc';
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' AND ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        $query="SELECT 
                  `fid`,
                  `comments`,
                  `created_at`,
                  `picture`,
                  `audio`,
                  GetUserName (created_by, 2) AS created_by,
                  getBusinessLegalName(legal_entity_id)  AS legal_entity_id,
                  getMastLookupValue(feedback_type)  AS feedback_type,
                  getMastLookupValue(feedback_group_type)  AS feedback_group_type
                FROM
                  customer_feedback 
                  where `legal_entity_id` in (" . $legalEntityId .")
                ". $sqlWhrCls . $orderBy ;
        $allData = DB::select(DB::raw($query));
        $TotalRecordsCount = count($allData);
        if($page!='' && $pageSize!=''){
            $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
            $allData = array_slice($allData, $page, $pageSize);
        }
        $arr = array('results'=>$allData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount)); 
        return $arr;      
    }

    public function addnewfeedback($data,$feedback_pic,$feedback_audio){
        $date = date("Y-m-d H:i:s");
        $userId = Session::get('userId');
        $result = DB::TABLE('customer_feedback')
            ->insertGetId([
                "legal_entity_id" => $data["legal_entity_id"],
                "feedback_group_type" => $data["add_feedback_group"],
                "feedback_type" => $data["add_feedback_type"],
                "comments" => $data["retailer_comments"],
                "picture" =>$feedback_pic,
                "audio" => $feedback_audio,
                "created_at" => $date,   
                "created_by" => $userId
            ]);
        return $result;
    }

    public function getSingleRecord($id)
    {
        $result = DB::table('customer_feedback')
                    ->select('fid','comments','audio','picture',DB::raw('GetUserName(created_by,2)  AS created_by'),'created_at',
                        DB::raw('getBusinessLegalName(legal_entity_id)  AS legal_entity_id'),
                        DB::raw('getMastLookupValue(feedback_type)  AS feedback_type'), 
                        DB::raw('getMastLookupValue(feedback_group_type)  AS feedback_group_type'))->orderBy('fid','desc')
                    ->where('fid',$id)
                    ->get()->all();
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function deleteSingleRecord($id)
    {
        $query = 'DELETE FROM customer_feedback WHERE fid = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }

    public function feedbacktype($id){
        return  DB::table('master_lookup as ml')
              ->select(DB::raw("ml.master_lookup_name as name ,ml.value"))
              ->where('ml.parent_lookup_id','=',$id)
              ->get()->all();
    }

    public function getOrderInfo($orderId){
        $orderdetails = DB::table('gds_orders')
                        ->leftJoin('users', 'users.legal_entity_id', '=', 'gds_orders.cust_le_id')
                        ->leftJoin('master_lookup', 'master_lookup.value', '=', 'gds_orders.order_status_id')
                        ->select('gds_order_id','order_code','shop_name','total','email','phone_no','order_status_id','cust_le_id','master_lookup.master_lookup_name','users.user_id')
                        ->where('gds_order_id',$orderId)->get()->all();
        return $orderdetails;
    }
    public function getcashback($data){
        $legal_entity = DB::table('legal_entities')->where('legal_entity_id',$data['cust_le_id'])->select('parent_le_id','legal_entity_type_id')->first();
        $le_wh_id = DB::table('legalentity_warehouses')->where('legal_entity_id',$legal_entity->parent_le_id)->where('dc_type',118001)->select('le_wh_id')->first();
        $order = DB::table('gds_orders')->where('gds_order_id',$data['order_id'])->select('order_date','is_self')->get()->all();
        $products =DB::table('gds_order_products')->select('total','product_id')->where('gds_order_id',$data['order_id'])->get()->all();
        $products = json_decode(json_encode($products),1);

        foreach($products as $key => $value){
            $tempArray = array();
            $cancelprodtot = DB::table('gds_order_cancel')
                        ->leftJoin('gds_cancel_grid','gds_cancel_grid.cancel_grid_id','=','gds_order_cancel.cancel_grid_id')
                        ->where('gds_cancel_grid.gds_order_id',$data['order_id'])
                        ->where('gds_order_cancel.product_id',$value['product_id'])->select(DB::raw("sum(gds_order_cancel.total_price) as total_price"))->get()->all();
            if(!empty($cancelprodtot))
                $tempArray[$value['product_id']]=$value['total'] - $cancelprodtot[0]->total_price;
            else
                $tempArray[$value['product_id']]=$value['total'];
            $productArray[] =$tempArray;
        }

        $this->_masterlookup = new MasterLookupController();
        $cashback = $this->_masterlookup->getOrderEcashValue($productArray,$order[0]->order_date,$le_wh_id->le_wh_id,$legal_entity->legal_entity_type_id,$order[0]->is_self,$data['cust_le_id']);
        $cash = json_decode(($cashback),1);
        if(!empty($cash['data']))
            $cash = $cash['data'][0]['cashback_applied'];
        else
            $cash = '';
        return $cash;
    }
    public function updateCashback($data){
        $this->_paymentObj = new PaymentModel();
        $userId=Session::get('userId');
        $cash_back_amount = DB::table('ecash_transaction_history')
                                ->where('order_id',$data['order_id'])
                                ->select('cash_back_amount')->get()->all();

        if(in_array($data['order_status_id'],['17001','17005','17020'])){
            if(count($cash_back_amount)==0 || empty($cash_back_amount[0]->cash_back_amount)){
                $gds_orders = DB::table('gds_orders')
                        ->where('gds_order_id',$data['order_id']);
                if($data['is_active']=='true'){
                    $gds_orders->update(array('instant_wallet_cashback' => 1,'updated_by' => $userId,'cashback_amount' => $data['cashback']));
                }else{
                    $gds_orders->update(array('instant_wallet_cashback' => 0,'updated_by' => $userId));
                }
                $this->_paymentObj->updateUserEcash($data['user_id'],$data['cashback'],0,$data['order_id'],143002,"Cashback added from web end!",$data['order_status_id']);
                return 1;
            }else{
                return 2;
            }
        }
    }
    public function getUsersByLegalEntityIdAndMobileno($legalEntityId,$mobile_no)
    {
        try
        {
            $response = 0;
            if($legalEntityId > 0)
            {
                $response = DB::table('users')
                        ->leftjoin('user_roles','user_roles.user_id','=','users.user_id')
                        ->leftjoin('roles','roles.role_id','=','user_roles.role_id')
                        ->where('users.legal_entity_id', $legalEntityId)
                        ->where('users.mobile_no',$mobile_no)
                        ->select('users.user_id', 'users.firstname', 'users.lastname', 
                                DB::raw('GetUserName(users.created_by, 2)'), DB::raw('GetUserName(users.user_id, 2) as name'),
                                'users.mobile_no', 'users.email_id','users.aadhar_id','users.profile_picture',DB::raw("GROUP_CONCAT(roles.name)  AS rolename"),'users.otp')
                        ->get()->all();
            }
            if(!empty($response))
            {
                $i = 0;
                foreach($response as $retailerUserDetails)
                {
                    $userId = $retailerUserDetails->user_id;
                   
                        
                    $response[$i]->profile_picture = '<img src="" alt="Image not found" class="img-circle" style="height: 50px; width: 50px;" />';
                    
                    $response[$i]->action = '<span style="padding-left:15px;" ><a href="#" onclick="editUser('.$userId.')" >'
                        . '<i class="fa fa-pencil"></i></a></span>'; 
                    $i++;
                }
            }
        } catch (\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return $response;
    }

    public function getDocumentTypes() {
        try {
            $fields = array('lookup.value','lookup.master_lookup_name');
                        $query = DB::table('master_lookup as lookup')->select($fields);
                        $query->where('lookup.mas_cat_id',188);
                        return $query->pluck('lookup.master_lookup_name','lookup.value')->all();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    public function legalEntityDoc($id){
        try {
            $fieldArr = array('legal_entity_docs.*');
            
            $query = DB::table('legal_entity_docs')->select('legal_entity_docs.*',DB::raw("getMastLookupValue(legal_entity_docs.doc_type) as doc_type"),DB::raw("GetUserName(legal_entity_docs.created_by,2) as fullname"));
            $query->where('legal_entity_docs.legal_entity_id', $id);
            return $query->get()->all();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }

    public function getleDocDetails($id) {
        try
        {
            $result = [];
            if($id > 0)
            {
                $result = DB::table('legal_entity_docs')
                        ->where('legal_entity_id', $id)
                        ->select('doc_name','legal_entity_docs.created_at as created_at','doc_type',
                                'doc_url', DB::raw('GetUserName(legal_entity_docs.created_by, 2) as created_by'), 
                                'doc_id')
                        ->get()->all();
            }
            return $result;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function upLoadPathInToDB($docsArr){
        try {
            $id = DB::table('legal_entity_docs')->insertGetId($docsArr);
            return $id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    
    }
    public function getLoginUserInfo() {
        try{
            $userId = Session::get('userId');//Session('userId'),
            $fieldArr = array('users.*');
            $query = DB::table('users')->select($fieldArr);
            $query->where('users.user_id', $userId);
            $userdata = $query->first();
            return $userdata;
        }
        catch(Exception $e) {

        }
    }
    public function getDocumentById($id) {
        try {
            $fieldArr = array('legal_entity_docs.*');
            
            $query = DB::table('legal_entity_docs')->select($fieldArr);           
            $query->where('legal_entity_docs.doc_id', $id);
            return $query->first();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }

    public function deleteRecord($id){
        try {
            DB::table('legal_entity_docs')->where('doc_id', '=', $id)->delete();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }

    public function getFeedbackGroup(){
        $getFeedbackGroup=DB::table('master_lookup')
                ->select('value','master_lookup_name')
                ->where('mas_cat_id',115)
                ->get()->all();
        if(count($getFeedbackGroup)>0){
            $getGroup=json_decode(json_encode($getFeedbackGroup),1);
            return $getGroup;
        }else{
            return '';
        }
    }

    public function getFeedbackComments(){
        $getFeedbackComments=DB::table('master_lookup')
                ->select('value','master_lookup_name')
                ->where('mas_cat_id',107)
                ->get()->all();
        if(count($getFeedbackComments)>0){
            $getComments=json_decode(json_encode($getFeedbackComments),1);
            return $getComments;
        }else{
            return '';
        }
    }


    
}
