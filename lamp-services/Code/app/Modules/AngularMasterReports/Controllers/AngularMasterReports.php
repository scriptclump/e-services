<?php 
namespace App\Modules\AngularMasterReports\Controllers;

use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Log;
use DB;
use View;
use Cache;
use Event;
use \Response;
use \Input;
use \Redirect;




class AngularMasterReports extends BaseController{

	
	function __construct()
	{

	}

	public function index()
	{
		return View::make('AngularMasterReports::index');
	}

}