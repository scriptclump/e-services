<?php
namespace App\Modules\RetailerSMS\Models;
use DB;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Session;

class SMSRetailer {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="sms_templets";
    public function getTableColumnHeadings($notify_code)
    {
      $response= DB::connection('mongo')->collection('sms_templets')->where('notify_code','=',$notify_code)->first();
      $response= json_decode(json_encode($response),1);
      return $response;
    }
    
    // public function getTableViewData()
    // {
    //   $response= DB::connection('mongo')->collection('sms_templets')->where('templateName','=','RetailerSMS')->pluck('view_name');
    //   $response= json_decode(json_encode($response),1);
    //   return $response;
    // }
 
}
