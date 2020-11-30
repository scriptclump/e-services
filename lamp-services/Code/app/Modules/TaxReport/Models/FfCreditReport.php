<?php
/*

 */
namespace App\Modules\TaxReport\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
/*

 */
class FfCreditReport extends Model {

    public function getCreditReportsData_Ffs($fdate,$tdate,$warehouse,$flag=1){

      
      $query = DB::selectFromWriteConnection(DB::raw("CALL getFCCreditReport(".$warehouse.",'".$fdate."','".$tdate."','".$flag."')")); 
      
      return $query;
    }

    public function getBatchProcessReportsData($fdate,$tdate,$warehouse,$flag=1){

      
      $query = DB::selectFromWriteConnection(DB::raw("CALL getInventoryBatchReport(".$warehouse.",'".$fdate."','".$tdate."')")); 
      
      return $query;
    }
}
