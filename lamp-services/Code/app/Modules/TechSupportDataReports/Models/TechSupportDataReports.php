<?php
namespace App\Modules\TechSupportDataReports\Models;
use DB;
/*
  Filename : Product.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 31-May-2016
  Desc : Model for product mongo table
 */
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Session;

class TechSupportDataReports {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="emailTemplates";
    public function getTableColumnHeadings()
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=','TechSupportDataTableHeadings')->pluck('captions');
      $response= json_decode(json_encode($response),1);
      return $response;
    }
    public function getTableViewData()
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=','TechSupportDataTableHeadings')->pluck('tableViews');
      $response= json_decode(json_encode($response),1);
      return $response;
    }
 
}
