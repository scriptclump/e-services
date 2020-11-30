<?php
/*

 */
namespace App\Modules\TaxReport\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
/*

 */
class PaymentReceivedReport extends Model {

    public function getPaymentReportsData_Ffs($fdate,$tdate,$warehouse){

    	$query = DB::selectFromWriteConnection(DB::raw("CALL getPaymentDetails('".$warehouse."','".$fdate."','".$tdate."')")); 
    	return $query;
    }


    public function stockistPaymentHistory($makeFinalSql, $orderBy, $page, $pageSize){
    	
    	ini_set('memory_limit', '-1');

    	    if($orderBy!=''){
				$orderBy = ' ORDER BY ' . $orderBy;
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
    	        $sqlQuery="select * from vw_stockist_payment_history";

		    	    if($sqlWhrCls!=''){
				        $sqlQuery.=$sqlWhrCls;
				    }

				    /*if(!empty($finalSearchField)){
				    	$sqlQuery.=' where '.$finalSearchField;
				    }*/
				    $pageLimit = '';
						if($page!='' && $pageSize!=''){
							$pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
						}

					$sqlQuery.=' order by Created_At desc';	
						//print_r($sqlQuery);exit;
                //echo $sqlQuery;exit;
    	        $result = DB::selectFromWriteConnection(DB::raw($sqlQuery));
       return json_decode(json_encode($result),true);

    	        //return $query;
    }
}