<?php namespace App\Modules\Inbound\Controllers;

/*
 * @author Mohan Kumar Narukulla <mohan.narukulla@ebutor.com>
 */
use \Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Inbound\Models\ApiNodeJs;
use App\Modules\Inbound\Models\InboundWmsResponse;
use App\Modules\Inbound\Models\InboundRequest;
use App\Modules\Inbound\Models\LegalentityWarehouses;
use App\Modules\Inbound\Models\InboundProductList;  
use App\Modules\Inbound\Models\LegalEntities;
use App\Central\Repositories\RoleRepo;

class DashboardRequestController extends Controller {

    public function __construct() {
        $this->_api_node_js                         = new ApiNodeJs();
        $this->_inbound_wms_response                = new InboundWmsResponse();
        $this->_Inbound                             = new InboundRequest();
        $this->_warehouse                           = new LegalentityWarehouses();
        $this->_InboundProductList                  = new InboundProductList();
        $this->_RoleRepo                            = new RoleRepo();
        $this->_LegalEntities                       = new LegalEntities();


        $this->grid_field_db_match = array(
            'PrimaryKEY'        => 'inbound_request_id',
            'createdate'        => 'created_at',
            'updated_by'        => 'updated_by',
            'request_status'    => 'request_status',
            'updatedate'        => 'updated_at',
            'TotalQuantity'     => 'product_quantity'
        );
    }

    /*
     * @param $request this request is used to get the filter data and sorting data ,$status The status may or may not come.Based on this status we will sort the Dashboard Grid
     *
     *   This function will return the data for the grid in the inbound index page
     * 
     * @return \Inputs for Grid Data
     */
    function searchorderwise(Request $request, $status = '') {
       $LegalentityId  = Session::get('legal_entity_id');
        $page           = $request->input('page');   //Page number
        $pageSize       = $request->input('pageSize'); //Page size for ajax call
        $filter_array   = array();
        $orderby_array  = "";
        
        if ($request->input('$orderby')) {             //checking for sorting
            $order              = explode(' ', $request->input('$orderby'));
            $order_query_field  = $order[0]; //on which field sorting need to be done
            $order_query_type   = $order[1]; //sort type asc or desc
            $order_by_type      = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type      = 'asc';
            }
            
            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by           = $this->grid_field_db_match[$order_query_field];
            }

            $orderby_array          = $order_by . " " . $order_by_type;
        }

        if ($request->input('$filter')) {                                          //checking for filtering
            $post_filter_query      = explode(' and ', $request->input('$filter'));   //multiple filtering seperated by 'and'
            $date                   = array();
            foreach ($post_filter_query as $post_filter_query_sub) {               //looping through each filter
                $filter                 = explode(' ', $post_filter_query_sub);
                $filter_query_field     = $filter[0];
                $filter_query_operator  = $filter[1];
                $filter_query_value     = $filter[2];

                if (strpos($post_filter_query_sub, ' or ') !== false) {
                    $query_field_arr    = explode(' or ', $post_filter_query_sub);
                    foreach ($query_field_arr as $query_field_data) {               //looping through each filter

                        $filter                     = explode(' ', $query_field_data);
                        $filter_query_field         = $filter[0];
                        $filter_query_operator      = $filter[1];
                        $filter_query_value         = $filter[2];

                        if (strpos($filter_query_field, 'day(') !== false) {
                            $start                                      = strpos($filter_query_field, '(');
                            $end                                        = strpos($filter_query_field, ')');
                            $filter_query_field                         = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['day']  = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                            continue;
                        } elseif (strpos($filter_query_field, 'month(') !== false) {
                            $start                                      = strpos($filter_query_field, '(');
                            $end                                        = strpos($filter_query_field, ')');
                            $filter_query_field                         = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['month']= ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                            continue;
                        } elseif (strpos($filter_query_field, 'year(') !== false) {
                            $start                                      = strpos($filter_query_field, '(');
                            $end                                        = strpos($filter_query_field, ')');
                            $filter_query_field                         = substr($filter_query_field, $start + 1, $end - $start - 1);
                            $date[$filter_query_field]["value"]['year'] = $filter_query_value;
                            $date[$filter_query_field]["operator"]      = $filter_query_operator;
                            $filter_query_operator                      = $date[$filter_query_field]['operator'];
                            $filter_query_value                         = "DateTime'" . implode('-', array_reverse($date[$filter_query_field]['value'])) . "'";
                        }
                    }
                } else {
                    if (strpos($filter_query_field, 'day(') !== false) {
                        $start                                          = strpos($filter_query_field, '(');
                        $end                                            = strpos($filter_query_field, ')');
                        $filter_query_field                             = substr($filter_query_field, $start + 1, $end - $start - 1);
                        $date[$filter_query_field]["value"]['day']      = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                        continue;
                    } elseif (strpos($filter_query_field, 'month(') !== false) {
                        $start                                          = strpos($filter_query_field, '(');
                        $end                                            = strpos($filter_query_field, ')');
                        $filter_query_field                             = substr($filter_query_field, $start + 1, $end - $start - 1);
                        $date[$filter_query_field]["value"]['month']    = ($filter_query_value < 10) ? '0' . $filter_query_value : $filter_query_value;
                        continue;
                    } elseif (strpos($filter_query_field, 'year(') !== false) {
                        $start                                          = strpos($filter_query_field, '(');
                        $end                                            = strpos($filter_query_field, ')');
                        $filter_query_field                             = substr($filter_query_field, $start + 1, $end - $start - 1);
                        $date[$filter_query_field]["value"]['year']     = $filter_query_value;
                        $date[$filter_query_field]["operator"]          = $filter_query_operator;
                        $filter_query_operator                          = $date[$filter_query_field]['operator'];
                        $filter_query_value                             = "DateTime'" . implode('-', array_reverse($date[$filter_query_field]['value'])) . "'";
                    }
                    reset($date);
                }

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw') {
                        $filter_value_array                             = explode("'", $filter_query_field);     //extracting the input filter value between single quotes ex 'value'
                        $filter_value                                   = $filter_value_array[1] . '%';

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {       //getting the filter field name
                                $cm1                                    = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_array[]                         = $cm1;
                            }
                        }
                    }

                    if ($filter_query_substr == 'endswit') {
                        $filter_value_array                             = explode("'", $filter_query_field);     //extracting the input filter value between single quotes ex 'value'
                        $filter_value                                   = '%' . $filter_value_array[1];
                        
                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {       //getting the filter field name
                                $appendvals                                    = $this->grid_field_db_match[$key] . ' like ' . $filter_value;
                                $filter_array[]                                = $appendvals;
                            } 
                        }
                    }

                    if ($filter_query_substr == 'tolower') {
                        $filter_value_array = explode("'", $filter_query_value);     //extracting the input filter value between single quotes ex 'value'
                        $filter_value = $filter_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = ' = ';
                        } else {
                            $like = ' != ';
                        }
                        
                        foreach ($this->grid_field_db_match as $key => $value) {
                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {       //getting the filter field name
                                $cm3 = $this->grid_field_db_match[$key] . $like . $filter_value;
                                $filter_array[] = $cm3;
                                //echo $cm3;die;
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof') {
                        $filter_value_array = explode("'", $filter_query_field);     //extracting the input filter value between single quotes ex 'value'
                        $filter_value = '%' . $filter_value_array[1] . '%';
                        
                        if ($filter_query_operator == 'ge') {
                            $like = ' like ';
                        } else {
                            $like = ' not like ';
                        }

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {       //getting the filter field name
                                $cm4 = $this->grid_field_db_match[$key] . $like . $filter_value;
                                $filter_array[] = $cm4;
                                // echo $cm4;die;
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

                    if (isset($this->grid_field_db_match[$filter_query_field])) {  //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }
                    $filter_array[]   = @$filter_field . $filter_operator . $filter_query_value; //die;
                }
            }
        }
        $result = $this->_Inbound->getStatuses($filter_array, $orderby_array, $page, $pageSize, $status, $LegalentityId);
        $decodedvalues = $result['result'];

        foreach ($decodedvalues as $key => $value) {
            //Consignment ID in GRID
            $decodedvalues[$key]['PrimaryKEY']              = '<a href="#" class="inwarddetails" data-id="' . $this->_RoleRepo->encodeData($value['inbound_request_id']) . '" data-toggle="modal" data-target="#myModal" >' . $value['inbound_request_id'] . '</a>';
            $decodedvalues[$key]['createdate']              = date('Y-m-d',strtotime($value['created_at']));
            $decodedvalues[$key]['updatedate']              = date('Y-m-d',strtotime($value['updated_at']));
            $cancelled                                      = $decodedvalues[$key]['is_cancelled'];
            $status_ourdb                                   = $decodedvalues[$key]['request_status'];
            $TotalQuantity                                  = $this->_Inbound->getTotalQuantity($value['inbound_request_id']);
            $decodedvalues[$key]['TotalQuantity']           = $TotalQuantity;
            //Action Column in GRID
            if ($cancelled == 0)
            {
                $decodedvalues[$key]['action']              = '<code style="cursor: pointer;"><i  class="fa fa-times" onclick="Cancel_inward_request(\'' . $this->_RoleRepo->encodeData($value['lp_request_id']) . '\');"></i></code>';
            }
            else
            {
                $decodedvalues[$key]['action']              = '<code>Cancelled</code>';
            }
            
            if ($status_ourdb == 'CMP') {
                $decodedvalues[$key]['action']              = '<code>Completed</code>';
            } else if ($status_ourdb == 'CAN') {
                $decodedvalues[$key]['action']              = '<code>cancelled </code>';
            } else if ($status_ourdb == 'EXR') {
                $decodedvalues[$key]['action']              = '<code>Expired </code>';
            } else if ($status_ourdb == 'GIN') {
                $decodedvalues[$key]['action']              = '<code>Recieved </code>';
            }
        }
        echo json_encode(array('results' => $decodedvalues, 'TotalRecordsCount' => $result['count']));
    }
    
    /*
     * @param $request This request will give the InwardId
     *
     * This function will return the data for the particular Inward request in Grid.
     * 
     * @return the table for the particular inward request .
     */

    public function getInwardDetails(Request $request) {
            $inwardid                       = $this->_RoleRepo->decodeData($request->input('inwardId'));
            $result                         = $this->_Inbound->inwardRequestDetails($inwardid);
            $result_array                   = json_decode(json_encode($result), true);
            $result_array[0]['ware_name']   = $this->warehousename($result_array[0]['wh_id']);
            $result_array[0]['pickup_address']   = $this->pickUpAddress($result_array[0]['client_id']);
             $i                             =0;
        foreach ($result_array[0]['inbound_product_details'] as $inbound_product_details) {
            $result_array[0]['inbound_product_details'][$i]['product_name']  =  $this->productName($inbound_product_details['product_id']);
            $result_array[0]['inbound_product_details'][$i]['product_image'] =  $this->productimage($inbound_product_details['product_id']);
            $i++;

        }
        print_r(json_encode($result_array));
    }
    
    /*
     * @param $warehouseId is the warehouse Id
     *
     * This function will gives warehouse name
     * 
     * @return the gives warehouse name .
     */

    public function warehousename($warehouseId) {
        $warehousename              = $this->_warehouse->where('le_wh_id', $warehouseId)->get(['address1','address2','city'])->all();
        return $warehousename;
    }

    public function pickUpAddress($legalEntityId) {
        $warehousename              = $this->_LegalEntities->where('legal_entity_id', $legalEntityId)->get(['address1','address2','city'])->all();
        return $warehousename;
    }
    
     /*
     * @param $productId is the Product Id
     *
     * This function will gives Product name
     * 
     * @return  gives Product name .
     */

    public function productName($productId) {
        $productName                = $this->_InboundProductList->productName($productId);
        return $productName;
    }
    
    /*
     * @param $productId is the Product Id
     *
     * This function will gives Product Image path for a particular Product
     * 
     * @return  gives Image path for a particular Product .
    */

    public function productimage($productId) {
        $productImage               = $this->_InboundProductList->productimage($productId);
        return $productImage;
    }


    /*
     * @param There is no parameters
     *
     * This function will gives All Records count,All Pending Records count,All Completed Records count,All Cancel Records count
     * 
     * @return  gives gives All Records count,All Pending Records count,All Completed Records count,All Cancel Records count
    */

    public function getAllCountHere()
    {
       $LegalentityId                                          = Session::get('legal_entity_id');
       $allTypesofRecords                                      = array();
       $allCountOfStatuses                                     = $this->_Inbound->getAllCountHere($LegalentityId);
       
                
            if(isset($allCountOfStatuses['CMP']))
            {
                $allTypesofRecords['allCompletedRecordscount']  = $allCountOfStatuses['CMP'];
            } 
            else {
                $allTypesofRecords['allCompletedRecordscount'] = '0';
            }
            

             if(isset($allCountOfStatuses['CAN']))
            {
                $allTypesofRecords['allCancelRecordscount']     = $allCountOfStatuses['CAN'];
            }
            else
            {
                $allTypesofRecords['allCancelRecordscount']     = 0;
            }

            if($allCountOfStatuses)
            {
               unset($allCountOfStatuses['CMP']);
               unset($allCountOfStatuses['CAN']);
               $allTypesofRecords['allPendingRecordscount']     = array_sum($allCountOfStatuses);

            }
       $allTypesofRecords['allRecordscount']        = array_sum($allTypesofRecords);
       print_r(json_encode($allTypesofRecords));       
       
    }






    }
