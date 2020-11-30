<?php

/*
 * Filename: SerialNumberController.php
 * Description: This file is used for manage serial number generation
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 23 June 2016
 * Modified date: 23 June 2016
 */

/*
 * SerialNumberController is used to manage serial number generation
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */ 

namespace App\Modules\SerialNumber\Controllers;


use App\Http\Controllers\BaseController;
use Session;
use Illuminate\Http\Request;
use Log;
use DB;
use Auth;
use Response;
use Illuminate\Support\Facades\Redirect;

use App\Modules\SerialNumber\Models\SerialNumber;
use Illuminate\Support\Facades\Route;
use Lang;

class SerialNumberController extends BaseController {
	
	protected $_serialNumber;
	protected $_config;

    public function __construct() {
		$this->middleware(function ($request, $next) {      
			if (!Session::has('userId')) {
				Redirect::to('/login')->send();
			}
			$this->_serialNumber = new SerialNumber();
		    return $next($request);
        });

    }
       
    public function indexAction() {
    	$currentSno = 'POTS16080098041';
    	$serialNumber = $_serialNumber->generateSerialNumber('TS', 'PO', $currentSno);
    	return $serialNumber;
	}
}
