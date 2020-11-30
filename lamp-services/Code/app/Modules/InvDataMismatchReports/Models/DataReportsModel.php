<?php
namespace App\Modules\InvDataMismatchReports\Models;
use DB;
/*
  Filename : Product.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 31-May-2016
  Desc : Model for product mongo table
 */
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Session;

class DataReportsModel {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="emailTemplates";
    public function getTableColumnHeadings($mongo_template='InvDataMismatchReports')
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=',$mongo_template)->pluck('captions')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
    public function getTableViewData($mongo_template='InvDataMismatchReports')
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=',$mongo_template)->pluck('tableViews')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
 
   public function getTableViewSummaryData($mongo_template='InvDataMismatchReports')
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=',$mongo_template)->pluck('tableViewSummary')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }

    public function getTableProcedureData($mongo_template='InvDataMismatchReports')
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=',$mongo_template)->pluck('tableProcedure')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
 
   public function getTableProcedureSummaryData($mongo_template='InvDataMismatchReports')
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=',$mongo_template)->pluck('tableProcedureSummary')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
}
