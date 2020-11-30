<?php

/*
 * Filename: PicklistController.php
 * Description: This file is used for manage picklists
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 13 Sep 2016
 * Modified date: 13 Sep 2016
 */

/*
 * PicklistController is used to manage pick lists
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Picklist
 * @version: 	v1.0
 */

namespace App\Modules\Picklist\Controllers;

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
use App\Central\Repositories\RoleRepo;

use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Picklist\Models\Picklist;

use App\Central\Repositories\ProductRepo;
use Utility;

class PicklistController extends BaseController {

	protected $_picklistModel;
	protected $_orderModel;
	protected $_roleRepo;
	protected $_LegalEntity;
	protected $_masterLookup;


	/*
	 * __construct() method is used to call model
	 * @param Null
	 * @return Null
	 */

    public function __construct() {
		$this->middleware(function ($request, $next) {
			if (!Session::has('userId')) {
				Redirect::to('/login')->send();
			}
			return $next($request);
		});
		//$this->_plModel = new Picklist();
		$this->_orderModel = new OrderModel();
		$this->_roleRepo = new RoleRepo();
		$this->_LegalEntity = new LegalEntity();
		$this->_masterLookup = new MasterLookup();
		$this->_picklistModel = new PickList();
	}

    /*
     * indexAction() method is used to list of purchase returns
     * @param Null
     * @return String
     */

    public function printPicklist() {

		try{

			/*$hasAccess = $this->_roleRepo->checkPermissionByFeatureCode('PO001');
			if($hasAccess == false) {
				return View::make('Indent::error');
			}
*/
			$selectedOrders 	=	Session::get('printPicklist');

			if(empty($selectedOrders) || !isset($selectedOrders['ids'])) {
					Redirect::to('/salesorders/index')->send();
			}


			$Products_Info = $this->_orderModel->getProductByOrderIdArray($selectedOrders['ids']);

			$Order_Products = array();
			
			$Warehouse_Name = '';
			
			foreach($Products_Info as $Product) {
				if($Product->ordered_qty != $Product->canceled_qty) {
					$Order_Products[$Product->gds_order_id][]	=	$Product;
				}
				$Warehouse_Name = $Product->le_wh_name;
                $Picker_Name = $Product->picker_name;
			}

			$leDetail = $this->_LegalEntity->getLegalEntityById(2);
			//$companyInfo = $this->_LegalEntity->getCompanyAccountByLeId(2);
			return view::make('Picklist::printpicklist')
									->with('leDetail', $leDetail)
									//->with('companyInfo', $companyInfo)
									->with('orderProducts', $Order_Products)
                                    ->with('pickerName', $Picker_Name)
									->with('DC', $Warehouse_Name);
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
}
