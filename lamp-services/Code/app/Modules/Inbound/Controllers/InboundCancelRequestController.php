<?php namespace App\Modules\Inbound\Controllers;

/*
 * @author Mohan Kumar Narukulla <mohan.narukulla@ebutor.com>
 */

use \Session;
use App\Http\Controllers\Controller;
use App\Modules\Inbound\Models\ApiNodeJs;
use Illuminate\Support\Facades\Input;
use App\Modules\Inbound\Models\InboundWmsResponse;
use App\Central\Repositories\RoleRepo;

class InboundCancelRequestController extends Controller {

    public function __construct() {
        $this->_api_node_js = new ApiNodeJs();
        $this->_inbound_wms_response = new InboundWmsResponse();
        $this->_RoleRepo = new RoleRepo();
    }
    
    /*
     * @param There is no external parameters
     * Here this function will connects to the Node API about cancelling the inward request
     * This function will cancell the particular inward request if it is cancellable stage, 
     * If the inward request status is not matched to the WMS status then this function will update the status in our DB(MySQL).
     * 
     * @return the message cancelled if it is in cancellable state or else it'll gives the appropriate message
     */

    public function cancelRequest() {
        try {
            $parameter = $this->_RoleRepo->decodeData(Input::get('id'));
            $params = array(
                "id" => $parameter
            );
            print_r($this->_api_node_js->nodeJsApi('http://10.175.8.12:3000/inbound/cancelinwardrequestP/', 'POST', $params));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
