<?php

namespace App\Modules\Tax\Models;

/*
  Filename : Counter.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 05-July-2016
  Desc : Model for tax classes mongo table, to store tax class names
 */

use Illuminate\Database\Eloquent\Model;
use App\Modules\Tax\Models\Masterlookup;
use App\Modules\Tax\Models\Zone;
use DB;
use Illuminate\Support\Facades\Session;
use UserActivity;
use Carbon\Carbon;
use App\Modules\UserActivityLogs\Models\UploadFilesLogs;
use Cache;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;

class TaxClass extends Model {

    protected $primaryKey = 'tax_class_id';

    public function getAllData($page, $pageSize, $orderby = '', $filterBy = '', $STATEID) {
        // print_r($orderby);die;
        $result = array();
        $sql = $this->join('zone', 'zone.zone_id', '=', 'tax_classes.state_id');

        if (!empty($orderby)) {
            $orderClause = explode(" ", $orderby);
            $sql = $sql->orderby($orderClause[0], $orderClause[1]);  //order by query
        }

        /* if($countryID != "")
          {
          $sql                    = $sql->where('zone.country_id','=',$countryID);
          } */

        $sql = $sql->where('state_id', '=', $STATEID);

        $sql = $sql->where('tax_classes.status', '=', 'Active');
        
        if (!empty($filterBy)) {
            foreach ($filterBy as $filterByEach) {
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
                    $sql = $sql->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                } else {
                    $sql = $sql->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }

        $count = $sql->count();
        $result['count'] = $count;
        $sql = $sql->skip($page * $pageSize)->take($pageSize);

        $result['result'] = $sql->get()->all();
        return $result;
    }

    function disableTaxRule($ruleid) {
        $sql = $this->find($ruleid);
        $sql->status = 'In-Active';
        if ($sql->save()) {
            $req['value'] = 1;
            $req['tax_class_code'] = $sql->tax_class_code;
        }

        return $req;
    }

     public function effectiveDates($stateId, $masterparentId, $effectiveDate)
    {
        $query = $this->join("master_lookup", "master_lookup.master_lookup_name", "=", "tax_classes.tax_class_type")
                ->where("parent_lookup_id", "=", $masterparentId)
                ->where("tax_classes.state_id", "=", $stateId)
                ->where("date_start", "=", $effectiveDate)
                ->where("mas_cat_id", "=", '9')
                ->count();
        return $query;

    }

    public function createTaxClass($classDetails) {
        $masterlookup = new Masterlookup();
        $zone         = new Zone();
        $userId = Session::get('userId');
        $taxpercentage = floatval($classDetails[0]['tax_percentage']);
//        $start_date = $classDetails[0]['start_date'];
//        if(empty($start_date))
//        {
//            $start_date = "1970-01-01";
//        }

        $explodeCode = explode("_", $classDetails[0]['taxclasscode']);
        $explodeCode[4] = (!empty($explodeCode[4]))? 'RATE-'.$taxpercentage: $explodeCode[4];

        $taxCode = implode("_", $explodeCode);
        $taxtype = $explodeCode[0];
        $state_code = $explodeCode[2];

//        $stateId = $zone->getSatetIdBasedonCode($state_code);
//
//        $parentLookupId = $masterlookup->getParentLookUpId($taxtype);
//
//        $check_effective_date = $this->effectiveDates($stateId, $parentLookupId, $start_date);

        // if($check_effective_date >= 1)
        // {
        //    $req['id_message'] = "effective-exists"; 
        //    return $req;
        // }
        

        $TaxClassCodeExists = $this->checkTaxClassActiveAndExists($taxCode);
        if ($TaxClassCodeExists > 0) {
            $req['id_message'] = "error";
            return $req;
            
        }

//        if ($classDetails[0]['start_date'] == "0000-00-00" || $classDetails[0]['start_date'] == "") {
//            $classDetails[0]['start_date'] = "1970-01-01";
//        }

        $explode = explode('_', $classDetails[0]['state']);
        //$class_name                 = $classDetails[0]['taxclass'] . '_' . str_replace(' ', '_', strtolower($explode[1])) . '_' . $classDetails[0]['tax_percentage'];
        $this->tax_class_type = $classDetails[0]['taxclass'];
        
        $this->tax_class_code = $taxCode;
        $this->state_id = $explode[0];
        $this->tax_percentage = $taxpercentage;
//        $this->date_start = $classDetails[0]['start_date'];
        $this->country_id = 99;
        $this->created_by = $userId;
        $this->SGST       = $classDetails[0]['sgst'];
        $this->CGST       = $classDetails[0]['cgst'];
        $this->IGST       = $classDetails[0]['igst'];
        $this->UTGST       = $classDetails[0]['utgst'];

        $this->save();
        /*  Logs were startted  */
        $oldvalues = "";
        $newvalues = array("StateId" => $explode[0], "TAXCLASSCODE" => $taxCode, "tax_percentage" => $taxpercentage, "Tax_Type" => $taxtype);
        $uniqueId  = array("Tax_Class_Code" => $taxCode);
        $mongodb = UserActivity::userActivityLog("TAXCREATION", $newvalues, "Tax Class Code is created by using DB table tax_classes", $oldvalues, $uniqueId);
        /*  Logs were Ended  */
        $req['id_message'] = $this->tax_class_id;
        $req['tax_class_code'] = $taxCode;
        return $req;
    }

    public function updateTaxClass($taxClassDetails) {
        $userId = Session::get('userId');
        $timestamp = date('Y-m-d H:i:s');
        $taxpercentage = floatval($taxClassDetails[0]['tax_percentage']);
        $explodeCode = explode("_", $taxClassDetails[0]['taxclasscode']);
        $explodeCode[4] = (!empty($explodeCode[4]))? 'RATE-'.$taxpercentage: $explodeCode[4];
        $taxCode = implode("_", $explodeCode);

//        if ($taxClassDetails[0]['start_date'] == "") {
//            $taxClassDetails[0]['start_date'] = "1970-01-01";
//        }
        
        $update_query = $this::where('tax_class_id', '=', $taxClassDetails[0]['tax_class_id'])->first();
        $explode = explode('_', $taxClassDetails[0]['state']);


        /*  Logs were startted  */
        $oldvalues = array("StateId" => $update_query->state_id, "TAXCLASSCODE" => $update_query->tax_class_code,"tax_percentage" =>$update_query->tax_percentage, "Tax_Type" => $update_query->tax_class_type);
        $newvalues = array("StateId" => $explode[0], "TAXCLASSCODE" => $taxCode, "tax_percentage" => $taxpercentage, "Tax_Type" => $explodeCode[0]);
        $uniqueId  = array("Tax_Class_Code" => $taxCode);
        $mongodb = UserActivity::userActivityLog("TAXCREATION", $newvalues, "Tax Class Code is UPDATED by using DB table tax_classes", $oldvalues, $uniqueId);
        /*  Logs were Ended  */
        $update_query->tax_class_type = $taxClassDetails[0]['taxclass'];
        $update_query->tax_class_code = $taxCode;
        $update_query->state_id = $explode[0];
        $update_query->tax_percentage = $taxpercentage;
//        $update_query->date_start = $taxClassDetails[0]['start_date'];
        $update_query->SGST = $taxClassDetails[0]['sgst'];
        $update_query->CGST = $taxClassDetails[0]['cgst'];
        $update_query->IGST = $taxClassDetails[0]['igst'];
        $update_query->UTGST = $taxClassDetails[0]['utgst'];
        $update_query->updated_by = $userId;
        $update_query->updated_at = $timestamp;
        
        if ($update_query->save()) {
            $req['tax_class_code'] = $taxCode;
            $req['message'] = "Tax class updated";
            return $req;
        } else {
            $req['tax_class_code'] = $taxCode;
            $req['message'] = "Tax class update fail!";
            return $req;
        }
    }

    public function getTaxClassDetails($taxClassCode)
    {
        $sql = $this->where("tax_class_code", "=", $taxClassCode)->first();
        return $sql;
    }

    public function storeTaxrules($taxdata, $URL) {
        
        $UserID = Session::get('userId');
        $Error_Array = array();
        $timestamp = date('Y-m-d H:i:s');
        $current_timeStamp = strtotime($timestamp);
        $zone = new Zone();
        $masterlookup = new Masterlookup();
        $all_tax_types = DB::table("master_lookup")->where('mas_cat_id', '=', '9')->pluck('master_lookup_name')->all();
        $insertedarray = array();
        $j = 3;
        $error = '';
        $wrong_effectivedate = "";
        $result = 0;
        $Count = 0;
        $insertCount = 0;
        $updateCounter = 0;
        $errordata = array();
        for ($i = 0; $i < sizeof($taxdata); $i++) {
            $taxdata[$i] = array_map('trim', $taxdata[$i]);
            if ($taxdata[$i]['tax_class_type'] == "" || !in_array($taxdata[$i]['tax_class_type'], $all_tax_types)) {
                $Error_Array[] = "A".$j." cell having invalid Data <br>";
                $j++;
                continue;
            }
          
            if ($taxdata[$i]['state'] == "") {
                $Error_Array[] = "B".$j." cell having invalid Data <br>";
                $j++;
                continue;
            }
           

            if ($taxdata[$i]['tax_percentage'] == "") {
                $Error_Array[] = "C".$j." cell having invalid Data <br>";
                $j++;
                continue;
            }

            if (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $taxdata[$i]['tax_percentage'])) {
                $Error_Array[] = "C".$j." cell having invalid Data <br>";
                $j++;
                continue;
            }

//            if ($taxdata[$i]['from_datedd_mm_yyyy'] != "") {
//                $startdate = strtotime($taxdata[$i]['from_datedd_mm_yyyy']);
//                $startdatee = ($taxdata[$i]['from_datedd_mm_yyyy'] != '') ? date('Y-m-d', strtotime($taxdata[$i]['from_datedd_mm_yyyy'])) : "";
//                
//            } else {
//                $startdatee = "1970-01-01";
//            }

            if(!is_numeric($taxdata[$i]['sgst']))
            {
               $Error_Array[] = "E".$j." cell having invalid Data <br>";
                $j++;
                continue; 
            }

            if(!is_numeric($taxdata[$i]['cgst']))
            {
               $Error_Array[] = "F".$j." cell having invalid Data <br>";
                $j++;
                continue; 
            }

            if(!is_numeric($taxdata[$i]['igst']))
            {
               $Error_Array[] = "G".$j." cell having invalid Data <br>";
                $j++;
                continue; 
            }

            if(!is_numeric($taxdata[$i]['utgst']))
            {
               $Error_Array[] = "H".$j." cell having invalid Data <br>";
                $j++;
                continue; 
            }

            $gst_sum = $taxdata[$i]['sgst'] + $taxdata[$i]['cgst'];

            $all_taxes = $gst_sum + $taxdata[$i]['igst'] + $taxdata[$i]['utgst'];

            if($all_taxes > 100)
            {
                $Error_Array[] = "Line #".$j." having invalid Data <br>";
                $j++;
                continue; 
            }else{
                    if($gst_sum != 100 && $gst_sum > 0)
                    {
                        $Error_Array[] = "Line #".$j." having invalid Data <br>";
                        $j++;
                        continue; 
                    }else if($taxdata[$i]['igst'] != 100 && $taxdata[$i]['igst'] > 0){
                        $Error_Array[] = "Line #".$j." having invalid Data <br>";
                        $j++;
                        continue;    
                    }else if($taxdata[$i]['utgst'] != 100 && $taxdata[$i]['utgst'] > 0){
                        $Error_Array[] = "Line #".$j." having invalid Data <br>";
                        $j++;
                        continue; 
                    }
            }

            $stateId = $zone->getSatetIdBasedonCode($zone->getStateCode($taxdata[$i]['state']));
            $parentLookupId = $masterlookup->getParentLookUpId($taxdata[$i]['tax_class_type']);
//            $check_effective_date = $this->effectiveDates($stateId, $parentLookupId, $startdatee);
            
            $all_tax_class_codes = $this->getAllTaxCodes();
            $all_tax_class_codes = json_decode($all_tax_class_codes, true);

            $tax_class_code = $taxdata[$i]['tax_class_type'] . "_" . "IN_" . $zone->getStateCode($taxdata[$i]['state']) . "_*_RATE-" . $taxdata[$i]['tax_percentage'];
            $taxclasscodecount = $this->checkTaxClassActiveAndExists($tax_class_code);

            if ($taxclasscodecount >= 1) {
                //checking effective date
//                if ($check_effective_date >= 1) {
//                    $Error_Array[] = "D".$j." cell having invalid Data <br>";
//                    $j++;
//                    continue;
//                }
//                else
//                {
                    /*  Logs were startted  */
                    $taxDetails = $this->getTaxClassDetails($tax_class_code);
                    $oldvalues = array("StateId" => $stateId, "TAXCLASSCODE" => $tax_class_code, "tax_percentage" => $taxdata[$i]['tax_percentage'], "Tax_Type" => $taxdata[$i]['tax_class_type']/*, "Effective_Date" => $taxDetails->date_start*/);
                    $newvalues = array("StateId" => $stateId, "TAXCLASSCODE" => $tax_class_code, "tax_percentage" => $taxdata[$i]['tax_percentage'], "Tax_Type" => $taxdata[$i]['tax_class_type']/*, "Effective_Date" => $startdatee*/);
                    $uniqueId  = array("Tax_Class_Code" => $tax_class_code);
                    $mongodb = UserActivity::userActivityLog("TAXCREATION", $newvalues, "Tax Class Code is Updated(Through Excel)", $oldvalues, $uniqueId);
                    /*  Logs were Ended  */

                    //updating effective dates
                    $query = $this->where('tax_class_code', $tax_class_code)->update([/*'date_start' => $startdatee,*/ 'updated_by' => $UserID, 'updated_at' => $timestamp]);
                    $updateCounter++;   
//                }
            
            } else {

                $insertCount = 0;
                $insertedarray1[] = array(
                    'tax_class_type' => $taxdata[$i]['tax_class_type'],
                    'tax_class_code' => $tax_class_code,
                    'state_id' => $zone->getStateId($taxdata[$i]['state']),
                    'country_id' => 99,
                    'tax_percentage' => $taxdata[$i]['tax_percentage'],
                    'SGST'  =>  $taxdata[$i]['sgst'],
                    'CGST'  =>  $taxdata[$i]['cgst'],
                    'IGST'  =>  $taxdata[$i]['igst'],
                    'UTGST'  =>  $taxdata[$i]['utgst'],
//                    'date_start' => @$startdatee,
                    'created_by' => $UserID
                );

                        /*  Logs were startted  */
                $oldvalues = "";
                $newvalues = array("StateId" => $stateId, "TAXCLASSCODE" => $tax_class_code, "tax_percentage" => $taxdata[$i]['tax_percentage'], "Tax_Type" => $taxdata[$i]['tax_class_type']/*, "start_date" => @$startdatee*/);
                $uniqueId  = array("Tax_Class_Code" => $tax_class_code);
                $mongodb = UserActivity::userActivityLog("TAXCREATION", $newvalues, "Tax Class Code is Created(Through Excel)", $oldvalues, $uniqueId);
                /*  Logs were Ended  */
                $insertedarray = array_unique($insertedarray1, SORT_REGULAR);
                $insertCount = $insertCount + count($insertedarray);
            }
            $Count = $i;
            $j++;
        }

        if (count($insertedarray) > 0) {
            $result = $this::insert($insertedarray);
        }
        
//        if($wrong_effectivedate != "")
//        {
//            $errordata['failed'] = rtrim($wrong_effectivedate, ",") . "  cell's effective date for this state already exists in system \r\n";    
//        }
        
        if ($Error_Array) {
            $errordata['failed'] = $Error_Array;
            $errordata['success'] = "$insertCount rows inserted \r\n";
            $errordata['update'] = "$updateCounter rows updated \r\n";
            $errordata['successcount'] = "$insertCount \r\n";
            $errordata['updatecount'] = "$updateCounter \r\n";
            $errordata['failedcount'] = count($Error_Array);
        } else {
            $errordata['failed'] ="";
            $errordata['success'] = "$insertCount rows inserted \r\n";
            $errordata['update'] = "$updateCounter rows updated \r\n";
            $errordata['successcount'] = "$insertCount \r\n";
            $errordata['updatecount'] = "$updateCounter\r\n";
            $errordata['failedcount'] = 0;
            
        }
        $errordata['reference'] = $current_timeStamp;
        $log_array = $errordata;

        unset($log_array['success']);
        unset($log_array['update']);
        unset($log_array['failedcount']);
        UserActivity::excelUploadFileLogs("TAXCREATION", $current_timeStamp, $URL, $log_array);
        return $errordata;
    }

    public function displayTaxRules($page = '', $pageSize = '') {
        $query = $this->where('status', 'Active');
        $final_result = array();
        $final_result['result'] = $query->get(array('tax_class_id', 'tax_class_type', 'tax_class_code', 'tax_percentage'))->all();
        return $final_result;
    }

    public function getAllTaxCodes() {
        $query = $this->pluck('status', 'tax_class_code')->all();
        return $query;
    }

    public function stateWise() {
        $fianl_result = array();

        $masterlookup = new MasterLookUp();

        $alltaxes = $masterlookup->getTaxTypes();

        $taxTypes = array();
        foreach ($alltaxes as $value) {
            $taxTypes[] = trim($value['master_lookup_name']);
        }
        ksort($taxTypes);


        $tax_res = $this->distinct('state_id')->where('status', '=', 'Active')->get(array('state_id', 'tax_class_code', 'tax_class_type'))->all();
        foreach ($tax_res as $tax_code) {
            if(in_array($tax_code['tax_class_type'], $taxTypes)){
                $new_res["text"] = $tax_code['tax_class_code'];
                $new_res["value"] = $tax_code['tax_class_code'];
                $fianl_result[$tax_code['state_id']][] = $new_res;
            }
            
        }
        $zone = new Zone();
        $state_ids = json_decode(json_encode($zone->where('country_id', '=', '99')->pluck('zone_id')->all()), true);
        foreach ($state_ids as $state) {
            if (empty($fianl_result[$state])) {
                $fianl_result[$state] = 'NULL';
            }
        }
        return $fianl_result;
    }

    public function getTaxClassId($taxClassCode) {
        return $this->where('tax_class_code', $taxClassCode)->pluck('tax_class_id')->all();
    }

    public function fetchTaxClassId($taxclassCode) {
        $query = $this->where('tax_class_code', '=', $taxclassCode)->get(array('tax_class_id'))->all();
        $data = $query;
        return $data[0]['tax_class_id'];
    }

    public function storeMAppingWithTaxClassesAndProduct($data, $URL) {
        try{
            $products = new Product();
            $taxmap = new ClassTaxMap();
            $zone = new Zone();
            $work_flow_class = new CommonApprovalFlowFunctionModel();
            $res_approval_flow_func   = $work_flow_class->getApprovalFlowDetails('TAX', 'drafted', \Session::get('userId'));
            $nextStatusId = $res_approval_flow_func["data"][0]["nextStatusId"];
            
            $created_by = Session::get('userId');

            $timestamp = date('Y-m-d H:i:s');
            $current_timeStamp = strtotime($timestamp);

            $insertedarray = array();
            $insertedall = array();

            $errorMessages = "";
            $insertcount = 1;
            $failedcount = 1;
            $insertARRAY = array();
            $errorarray = array();
            $errorData = array();
            $insertARRAY['lines'] = array();
            $insertARRAY['duplicatedata'] = array();
            $allTaxcodes = $this->getTaxClassCodesByState();
            $allHsnCodes = $this->getAllHsnCodes();
            $parentId=$this->getApprovalIdForTax();
            for ($i = 0; $i < sizeof($data); $i++) {

                $productID = $products->getProductId($data[$i]['sku']);
                if ($productID == 0) {
                    $errordata = ($i + 3);
                    $insertARRAY['skudata'][] = "In line #" . $errordata . " sku having invalid data";
                    continue;
                }

                foreach ($data[$i] as $key => $value) {

                    if (strlen($key) == 3 && $key == 'all') {
                        if (in_array($value, $allTaxcodes['*'])) {
                            if($data[$i]['hsn_code'] != "" && is_numeric($data[$i]['hsn_code'])){
                                $hsncodemaster=DB::table('HSN_Master')->where('ITC_HSCodes',$data[$i]['hsn_code'])->first();
                                $data[$i]['hsn_code']=isset($hsncodemaster->ITC_HSCodes)?$hsncodemaster->ITC_HSCodes:'';
                                $explodeTaxCode = explode('_', $value);
                                $TaxType = $explodeTaxCode[0];
                                $TAXCLASSID = $this->fetchTaxClassId($value);
                                $stateId = $this->getStateIdBasedonTaxcode($value);

                                $count = $taxmap->checkDuplicate($stateId, $productID, $value);

                                if ($count >= 1) {

                                    $errordata = ($i + 3);
                                    //duplicate array is creating
                                    $insertARRAY['duplicatedata'][] = "Line #" . $errordata . " : \"*(ALL)\" Column data already exist <br>";
                                    // $errorarray['cells'][] = "all";
                                } else {
                                    $insertTaxmap = new ClassTaxMap();
                                    $check_HSN = json_decode(json_encode($insertTaxmap->checkHSNCodeForProduct($productID)), true);
                                    
                                    if(!empty($check_HSN) &&  $data[$i]['hsn_code'] != $check_HSN[0])
                                    {
                                        $val = ($i + 3);
                                        $insertARRAY['lines'][] = 'Line #' . $val . ': Please check HSN Code <br>';
                                        $i++;
                                        continue;
                                    }
                                    $insertcount = $i + 1;

                                    $insertedarray[] = array(
                                        'product_id' => $productID,
                                        'tax_class_id' => $TAXCLASSID,
                                        'status' => $nextStatusId,
                                        'created_by' => $created_by
                                    );
                                    
                                    $insertTaxmap->insertTaxClassMap($productID, $TAXCLASSID,'', $data[$i]['hsn_code'],$parentId);
                                    /*   Logs startted  */
                                    $oldvalues = "";
                                    $newvalues = array("SKU" => $data[$i]['sku'], "TAXCLASSCODE" => $value, "PRODUCT_ID" => $productID);
                                    $uniqueId  = array("Product_id" => $productID);
                                    $mongodb = UserActivity::userActivityLog("TAXMAPPING", $newvalues, "TAXMAPPING was Creating to the Table tax_class_product_map through bulk upload", $oldvalues, $uniqueId);
                                    /* Logs ended */
                                }
                            } else if($data[$i]['hsn_code'] == ""){
                                $val = ($i + 3);
                                $insertARRAY['lines'][] = 'Line #' . $val . ': HSN Code Column is empty <br>';
                            } else if(!is_numeric($data[$i]['hsn_code'])){
                                $val = ($i + 3);
                                $insertARRAY['lines'][] = 'Line #' . $val . ': HSN Code Column have invalid HSN Code <br>';
                            }
                        }else if(!in_array($value, $allTaxcodes['*']) && $value != "")
                        {
                            $val = ($i + 3);
                            $insertARRAY['lines'][] = 'Line #' . $val . ': "' . strtoupper($key) . '" Column have invalid TaxClassCode <br>';
                        }
                    }
                    
                    if (strlen($key) == 2) {
                        if (in_array($value, $allTaxcodes[strtoupper($key)])) {
                            if($data[$i]['hsn_code'] != "" && is_numeric($data[$i]['hsn_code'])){
                                $hsncodemaster=DB::table('HSN_Master')->where('ITC_HSCodes',$data[$i]['hsn_code'])->first();
                                $data[$i]['hsn_code']=isset($hsncodemaster->ITC_HSCodes)?$hsncodemaster->ITC_HSCodes:'';
                                $explodeTaxCode = explode('_', $value);
                                $TaxType = $explodeTaxCode[0];
                                $stateCODE = $explodeTaxCode[2];
                                $STATEIDbasedonstateCODE = $zone->getSatetIdBasedonCode($stateCODE);
                                $TAXCLASSID = $this->fetchTaxClassId($value);
                                $stateId = $this->getStateIdBasedonTaxcode($value);
                                $countclass = $taxmap->checkDuplicate($stateId, $productID, $value);

                                if ($countclass >= 1) {
                                    //UPdate concept comes
                                    $val = ($i + 3);
                                    // echo "duplli";
                                    $insertARRAY['duplicatedata'][] = 'Line #' . $val . ': "' . strtoupper($key) . '" Column data already exist <br>';
                                } else {
                                    $insertcount = $i + 1;
                                    $insertedall22[] = array(
                                        'product_id' => $productID,
                                        'tax_class_id' => $TAXCLASSID,
                                        'status' => $nextStatusId,
                                        'created_by' => $created_by

                                    );
                                    $insertTaxmap = new ClassTaxMap();
                                      // $insertTaxmap = new ClassTaxMap();
                                    $check_HSN = json_decode(json_encode($insertTaxmap->checkHSNCodeForProduct($productID)), true);
                                    
                                    if(!empty($check_HSN) &&  $data[$i]['hsn_code'] != $check_HSN[0])
                                    {
                                        $val = ($i + 3);
                                        $insertARRAY['lines'][] = 'Line #' . $val . ': Please check HSN Code <br>';
                                        $i++;
                                        continue;
                                    }
                                    $insertTaxmap->insertTaxClassMap($productID, $TAXCLASSID,'', $data[$i]['hsn_code'],$parentId);
                                     /*   Logs startted  */
                                    $oldvalues = "";
                                    $newvalues = array("SKU" => $data[$i]['sku'], "TAXCLASSCODE" => $value, "PRODUCT_ID" => $productID);
                                    $uniqueId  = array("Product_id" => $productID);
                                    $mongodb = UserActivity::userActivityLog("TAXMAPPING", $newvalues, "TAXMAPPING was Creating to the Table tax_class_product_map through bulk upload", $oldvalues, $uniqueId);
                                    /* Logs ended */

                                    $insertedall = array_unique($insertedall22, SORT_REGULAR);
                                }
                            } else if($data[$i]['hsn_code'] == ""){
                                $val = ($i + 3);
                                $insertARRAY['lines'][] = 'Line #' . $val . ': HSN Code Column is empty <br>';
                            } else if(!is_numeric($data[$i]['hsn_code'])){
                                $val = ($i + 3);
                                $insertARRAY['lines'][] = 'Line #' . $val . ': HSN Code Column have invalid HSN Code <br>';
                            }
                        }
                        else if(!in_array($value, $allTaxcodes[strtoupper($key)]) && $value != "")
                        {
                            $val = ($i + 3);
                            $insertARRAY['lines'][] = 'Line #' . $val . ': "' . strtoupper($key) . '" Column have invalid TaxClassCode <br>';
                        }
                        if (!empty($value) && !in_array($value, $allTaxcodes[strtoupper($key)])) {
                            $explodeTaxCode = explode('_', $value);
                            $TaxType = $explodeTaxCode[0];
                            $stateCODE = $explodeTaxCode[2];
                            $STATEIDbasedonstateCODE = $zone->getSatetIdBasedonCode($stateCODE);
                            $checkCodeExistsorNot = $this->checkTaxClassCode($STATEIDbasedonstateCODE, $value);

                            if ($checkCodeExistsorNot != 1) {
                                $val = ($i + 3);
                                $insertARRAY['lines'][] = 'Line #' . $val . ': "' . strtoupper($key) . '" Column have invalid TaxClassCode <br>';
                                // continue;
                            }
                        }
                    }
                }
            }

            if (count($insertedall) > 0) {
                $insertARRAY['success'] = 1;
            }
            if (empty($insertARRAY)) {
                $insertARRAY = "";
            }
            $errorData['uniqueRef'] = $current_timeStamp;
            $errorData['success'] = $insertARRAY;
            $errorData['duplicatecount'] = isset($insertARRAY['duplicatedata'])?count($insertARRAY['duplicatedata']):0;
            $errorData['linescount'] = isset($insertARRAY['lines']) ? count($insertARRAY['lines']) : 0;
            $errorData['insertcount'] = (isset($insertedall) ? count($insertedall) : 0)+ (isset($insertedarray) ? count($insertedarray) : 0);
            $loG_Data = $errorData;
            unset($loG_Data['duplicatecount']);
            unset($loG_Data['linescount']);
            unset($loG_Data['uniqueRef']);

            UserActivity::excelUploadFileLogs("TAXMAPPING", $current_timeStamp, $URL, $loG_Data);
            return $errorData;
        }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getAllHsnCodes() {
        return json_decode(json_encode(DB::table('HSN_Master')->pluck('ITC_HSCodes')->all()), true);
    }
    
    public function getTaxClassCodesByState() {
        $zone = new Zone();
        $ClassArray = array();
        $allStates = $zone->allStates();
        for ($i = 0; $i < sizeof($allStates); $i++) {
            $zoneID = $allStates[$i]['zone_id'];
            $ClassArray[$allStates[$i]['code']] = $this->where('state_id', '=', $zoneID)->where('status', '=', 'Active')->pluck('tax_class_code')->all();
        }
        return $ClassArray;
    }

    public function getStateIdBasedonTaxcode($taxclassCode) {
        $query = $this->where('tax_class_code', '=', $taxclassCode)->get(array('state_id'))->all();
        $data = $query;
        return $data[0]['state_id'];
    }

    public function checkTaxClassActiveAndExists($taxClasscode) {
        $sql = $this->where('status', '=', 'Active')->where('tax_class_code', '=', $taxClasscode)->count();
        return $sql;
    }

    public function checkTaxClassCode($stateId, $taxcode) {
        $query = $this->where('state_id', '=', $stateId)->where('tax_class_code', '=', $taxcode)->count();
        return $query;
    }

    public function getTaxClassCodeByTaxId($taxId)
    {
        $sql = $this->where("tax_class_id", "=", $taxId)->pluck("tax_class_code")->all();
        $result = $sql;
        return $result[0];
    }

    /**
     * [getTaxInfoByTaxClassId description] added by prasenjit @28th Nov
     * @return [type] [description] for tax class tally entry
     */
    public function getTaxInfoByTaxClassId($taxClassId){

        $taxinfo = Cache::get('TaxClass_'.$taxClassId,false);
        if(!$taxinfo){
          $query = $this->where('tax_class_id', '=', $taxClassId)->get()->all();
          $query = json_decode(json_encode($query),true);
          $taxinfo = $query[0];
          Cache::put('TaxClass_'.$taxClassId,$taxinfo,3600);

        }
        return $taxinfo;

    }

    public function getApprovalIdForTax(){
        $tax_id=DB::statement(DB::raw("update master_lookup SET description=description+1 WHERE value=18701"));
        $result = "select description from master_lookup where value=18701";
        $result=DB::selectFromWriteConnection(DB::raw($result));
        $result=json_decode(json_encode($result[0]),true);
        return $result['description'];
        
    }


    

}