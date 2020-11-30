<?php

namespace App\Modules\Picklist\Models;

use Illuminate\Database\Eloquent\Model;
use Log;
use DB;
use Response;
use Session;
use Notifications;
use Mail;
use Utility;
use App\Modules\Orders\Models\OrderModel;

class Picklist extends Model
{

    protected $table = "picklist";
    protected $primaryKey = 'picklist_id';
    protected $fillable = array('picklist_date', 'lp_wh_id', 'picklist_status','created_by');



    public function savePicklist($data)
    {
		try
        {


          $orderIds = $data['ids'];

          $orderModel = new OrderModel();

          foreach($orderIds as $orderId) {

            $orderModel->updateOrderStatusById($orderId, 17020);
          }

        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return Response::json(array('status'=>400, 'message'=>'Failed', 'po_id'=>0));
        }
    }


}
