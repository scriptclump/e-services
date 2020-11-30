<?php

namespace App\Modules\Orders\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use Response;
use Log;
use DB;
use Auth;
use Input;
use PDF;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\AutoAssignPickerCheckerModel;
use Utility;
use App\Modules\Orders\Controllers\OrdersController;
use App\Lib\Queue;

class AutoAssignPickerCheckerController extends BaseController {

    //protected $_autoModel;


    /*
     * __construct() method is used to call model
     * @param Null
     * @return Null
     */

    public function __construct() {
        $this->_autoModel = new AutoAssignPickerCheckerModel();
        $this->order_con = new OrdersController();
    }

    public function autoAssignPicker() {
        echo "i ma in consoleeee";
        //$autoassign = $this->_autoModel->getMasterLokup(78016);
        //$autoassignenable = isset($autoassign->description)?$autoassign->description:0;
        /*if($autoassignenable==1){            
        } else {
            echo 'Auto Assign Feature is disabled';
            return false;
        }*/
        $autoassignLe = $this->_autoModel->getEnableLegalEntity();
        if(count($autoassignLe)>0){
            foreach($autoassignLe as $leData){
                $le_id = isset($leData->legal_entity_id)?$leData->legal_entity_id:0;
                $roles_to_ignore = isset($leData->roles_to_ignore)?$leData->roles_to_ignore:0;
                if($le_id>0){
                    $openorders = $this->_autoModel->getOpenOrders($le_id);
                    foreach($openorders as $order){
                        $gds_order_id = $order->gds_order_id;
                        $order_status_id = $order->order_status_id;
                        $dc_id = $order->le_wh_id;
                        $hub_id = $order->hub_id;
                        $dchubmap = $this->_autoModel->checkDCHubMapping($dc_id,$hub_id);
                        if(count($dchubmap)>0){
                            $getPicker = $this->_autoModel->getUsersByFeatureCode('PICKR002',$le_id,$roles_to_ignore);
//                            Log::info('get picker list');
  //                          Log::info($getPicker);
                            $picker_id = isset($getPicker[0]->user_id)?$getPicker[0]->user_id:0;
    //                        Log::info("order_id to assign== ".$gds_order_id);
      //                      Log::info("picker_id to assign== ".$picker_id);
                            if($picker_id>0){
                                $data = ['ids'=>[$gds_order_id],'statusCodes'=>[$order_status_id],'pickedBy'=>$picker_id,'pickDate'=>date('Y-m-d')];
                                $result = $this->order_con->savePicklistAction($data);
                                /*$data = json_encode($data);
                                $data = base64_encode($data);
                                $queue = new Queue();
                                $args = array("ConsoleClass" => 'autoSavePicklist', 'arguments' => array($data));
                                $token = $queue->enqueue('default', 'ResqueJobRiver', $args);
                                */
                                $this->_autoModel->updateOrder($gds_order_id);
                            } else {
                                //break;
                            }
                        }
                    }
                }
            }
            return true;
        }
    }
}
