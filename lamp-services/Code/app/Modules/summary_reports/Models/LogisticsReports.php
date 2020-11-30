<?php
namespace App\Modules\summary_reports\Models;
use DB;
/*
  Filename : Product.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 31-May-2016
  Desc : Model for product mongo table
 */
use Session;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class LogisticsReports extends Eloquent {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="logistics_summary_reports";
    public function getLogisticData()
    {
      $response= $this->pluck('name')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
     public function getEmailData()
    {
      $response= DB::connection('mongo')->collection('emailTemplates')->where('templateName','=','SummaryReports')->pluck('email_id')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
     public function getCRMDataHeaders()
    {
      $response=  DB::connection('mongo')->collection('crm_summary_repport')->pluck('name')->all();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
    public function getSalesData()
    {
      $response= DB::connection('mongo')->collection('sales_summary_reports')->pluck('name')->all(); 
      $response= json_decode(json_encode($response),1);
      return $response;
    }
    public function getPurchageData()
    {
      $response= DB::connection('mongo')->collection('purchase_summary_reports')->pluck('name')->all(); 
      $response= json_decode(json_encode($response),1);
      return $response;
    }
	public function getCRMData()
	{
	  DB::enablequerylog();
	  $response =  DB::table('ff_report')->select('name','order_cnt','calls_cnt','tbv','uob','abv','tlc','ulc','alc','contrib', DB::raw('(CASE WHEN order_cnt="0" THEN "0" WHEN order_cnt=NULL THEN "0" ELSE  (order_cnt*100)/calls_cnt END) AS  call_success_rate'))->where('order_date',date('Y-m-d'))->get()->all();
    $response= json_decode(json_encode($response),1);
	  return $response;
	}
}
