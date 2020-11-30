<?php

namespace App\Modules\Vouchers\Controllers;


use App\Http\Controllers\BaseController;
use Session;
use View;
use Illuminate\Http\Request;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\Redirect;
use App\Modules\Vouchers\Models\Voucher;

class VouchersController extends BaseController {

	protected $_voucher;

    public function __construct() {
		$this->_voucher = new Voucher();
    }

    public function saveVoucherAction() {
    	$this->_voucher->saveSalesVoucher();
    	die('Successfully inserted');
    }


}
