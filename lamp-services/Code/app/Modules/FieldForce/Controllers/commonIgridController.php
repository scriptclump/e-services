<?php
/*
FileName : commonIgridController
Author   : eButor
Description : Function Written for IGrid Common Search
CreatedDate :15/jul/2016
*/
//defining namespace
namespace App\Modules\FieldForce\Controllers;

class commonIgridController {

	// Find the Symbol in the Grid Search Param
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
}