<?php

/*
FileName :appVersionController
Author   :eButor
Description :
CreatedDate :7/jul/2016
*/
//defining namespace
namespace App\Modules\MobApp\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Modules\MobApp\Models\appVersionModel;
use Illuminate\Http\Request;
use Input;

class appVersionController extends BaseController{

    private $_appversion_request = '';

    public function __construct() {
        $this->_appversion_request = new appVersionModel();
    }
	
    public function appVersionIndex(){
        try{
            $breadCrumbs = array('Home' => url('/'),'Configuration' => '#','App' =>'#');
            parent::Breadcrumbs($breadCrumbs);

    	  return view('MobApp::index');
          }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
          }
    }

/*
    Function : findConditionSysbol
    Param : 
        @conditionString => Only the condition string for a field
        $fldname => Optional, needed only for Date Field
    Desc : Find he condition type from the query string and make the query filed as per the column 
    */
    public function findConditionSysbol($conditionString, $fldname=''){

        $finalString = "";

        // find for date search, (checking Month as it is common)
        $findSymbol = strpos($conditionString, "month");
        // if month is find in the string, assume the field is for Date Column
        if( $findSymbol>1 ){

            $filterArray = explode('('.$fldname.')', $conditionString);

            // Take Date from the string
            $day= '0'.trim(substr($filterArray[1], 4, 2));
            $day= substr($day, -2);

            // Take Month from the string
            $month= '0'.trim(substr($filterArray[2], 4, 2));
            $month= substr($month, -2);

            // Take Year from the string
            $year= trim(substr($filterArray[3], 4, 4));
            $filterDate = $year.'-'.$month.'-'.$day;
            $finalString = " = '" . $filterDate . "'";

        }else{
            // filter condition for all the field
            $checkConfitinFlag = 0;

            //find for contains, endswith, startswith and not contains
            // Checking Tolower as it is common to all the condition
            $findSymbol = strpos($conditionString, "tolower");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                
                // break the string by single quote, so that value can be taken
                $finalString = explode("'", $conditionString);

                // arranging the % symbol as per the condition
                $endSign = $startSign = "";
                if( strpos($conditionString, "endswith") !== false ){
                    $endSign = "";
                    $startSign = "%";
                }elseif (strpos($conditionString, "startswith") !== false) {
                    $endSign = "%";
                    $startSign = "";
                }elseif (strpos($conditionString, "ge") !== false) {
                    $endSign = "%";
                    $startSign = "%";
                }

                // if endswith, startswith and ge not there in the string then assume it is for not in
                if( $endSign . $startSign != '' ){
                    $finalString = " like '" . $startSign . trim($finalString[1]) . $endSign. "'";
                }else{
                    $finalString = " not like '%" . $startSign . trim($finalString[1]) . $endSign. "%'";
                }
                $checkConfitinFlag = 1;   
            }

            // find for equals
            $findSymbol = strpos($conditionString, "eq");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                $finalString = explode("eq", $conditionString);
                $finalString = " = " . $finalString[1];
                $checkConfitinFlag = 1;
            }

            //find fo not equals
            $findSymbol = strpos($conditionString, "ne");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                $finalString = explode("ne", $conditionString);
                $finalString = " != " . $finalString[1];
                $checkConfitinFlag = 1;
            }

            //find fo not gratter than
            $findSymbol = strpos($conditionString, "gt");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                $finalString = explode("gt", $conditionString);
                $finalString = " > " . $finalString[1];
                $checkConfitinFlag = 1;
            }

            //find fo not gratter than equal to
            $findSymbol = strpos($conditionString, "ge");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                $finalString = explode("ge", $conditionString);
                $finalString = " >= " . $finalString[1];
                $checkConfitinFlag = 1;
            }

            //find fo not less than
            $findSymbol = strpos($conditionString, "lt");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                $finalString = explode("lt", $conditionString);
                $finalString = " < " . $finalString[1];
                $checkConfitinFlag = 1;
            }

            //find fo not less than equal to
            $findSymbol = strpos($conditionString, "le");
            if($findSymbol>0 and $checkConfitinFlag == 0){
                $finalString = explode("le", $conditionString);
                $finalString = " <= " . $finalString[1];
                $checkConfitinFlag = 1;
            }
        }

        return $finalString;

    }

    /*
    Function : makeIGridToSQL
    Param : 
        $fldname => Needed only for Date Field
        @inputFilter => Row input filter
        $dateFilterFlag => Optional, needed only for Date fild, default is False
    Desc : Arrange the condition type as per the filed name 
    */
    public function makeIGridToSQL($fldname, $inputFilter, $dateFilterFlag=false){

        // break the input string by fildName, if fldnmae not found will return blank string
        $breakOnFildName = explode($fldname, $inputFilter);
        $finalSearchField = "";

        // check the for the fld name
        if( count( $breakOnFildName ) > 1 ){

            // check if the Date fld is true
            if($dateFilterFlag===false){

                // breaking the input string by "and" fld. because we can not send the entire input
                // to make the fild specific query, as there could be multiple symbol available for multiple fld
                $checkMultipleCondition = explode(" and", $inputFilter);
                if( count($checkMultipleCondition>1) ){
                    
                    // check in the confition string, if the fild name is available then send the querystring
                    foreach( $checkMultipleCondition as $val){
                        if( strpos($val, $fldname) !== false ){
                           $inputFilter = $val;
                        }
                    }

                    // generate the query
                    $finalSearchField = $this->findConditionSysbol($inputFilter, $fldname) == "" ? "" : $fldname . $this->findConditionSysbol($inputFilter, $fldname);
                }
            }else{

                // generate the query for Date fld
                $inputFilter = explode("day", $inputFilter); 

                if( count($inputFilter)>1 ){
                    $inputFilter = 'day'.$inputFilter[1];
                    $finalSearchField =  $this->findConditionSysbol($inputFilter, $fldname) == "" ? "" : "date_format(".$fldname.", '%Y-%m-%d')" . $this->findConditionSysbol($inputFilter, $fldname);
                }
            }
        }
        return $finalSearchField;
    }

     public function appVersionList(Request $request){

        $makeFinalSql = array();

        $filter = $request->input('%24filter');
        if( $filter=='' ){
            $filter = $request->input('$filter');
        }

         // make sql for version name
         $fieldQuery = $this->makeIGridToSQL("version_name", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        // make sql for outbound_order_id
       $fieldQuery = $this->makeIGridToSQL("version_number", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

          // make sql for version name
        $fieldQuery = $this->makeIGridToSQL("app_type", $filter);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $orderBy = "";
        $orderBy = $request->input('%24orderby');
        if($orderBy==''){
            $orderBy = $request->input('$orderby');
        }

        // Arrange data for pagination
        $page="";
        $pageSize="";
        if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
            $page = $request->input('page');
            $pageSize = $request->input('pageSize');
        }


        return $this->_appversion_request->viewAppVersiondata($makeFinalSql, $orderBy, $page, $pageSize);

    

    }

    public function addAppVersion(){
        try{
             $breadCrumbs = array('Home' => url('/'),'Configuration' => '#','App' => '/mobapp','Add App Version'=>'#');
             parent::Breadcrumbs($breadCrumbs);
             return view('MobApp::addAppVersion',['add_update_flag' => 'Add']);
         }
         catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
         }

     }

    public function saveAppVersion(Request $request){

        $AppversionData = $request->input();

       // echo "<pre/>";print_r($AppversionData);exit;

        $validator = Validator::make($request->all(),
            array(
                   'version_name'     => 'required',
                   'version_number'          => 'required',
                   'app_type'            => 'required',
                )

            );

        if ($validator->fails()) {
                return redirect('/mobapp/addappversion')->withErrors($validator);
            }

        if( $this->_appversion_request->saveVersionData($AppversionData) ){
            return redirect('/mobapp');
        }else{
            return redirect('/mobapp');
        }
    }
   public function updateData($updateId){
    try{
             $breadCrumbs = array('Home' => url('/'),'Configuration' => '#','App' => '/mobapp','Update App Version'=>'#');
             parent::Breadcrumbs($breadCrumbs);
            $update = $this->_appversion_request->getUpdateData($updateId);

         return view('MobApp::addAppVersion',['add_update_flag' => 'Update', 'update' => $update]);
    }
     catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
         }
     }
    public function updatewithId(Request $request){

        $data = $request->input();

        if( $this->_appversion_request->updateVersionData($data) ){
            return redirect('/mobapp');
            }else{  
            return "something  error in this page";
        }
    }

    public function deleteAppVersion(Request $request){
        $varsionID = $request->input('versionId');
        return $this->_appversion_request->deleteVersion($varsionID);
    }


}