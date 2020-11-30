<?php

namespace App\Modules\SerialNumber\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class SerialNumber extends Model
{
	/**
	 * getSerialNumber() method is used to get config
	 * @param  String $state_code
	 * @param  String $prefix
	 * @return Array
	 */
	
	public function getSerialNumber($state_code, $prefix) {
		try{
			$query = DB::table('serial_numbers as sno')->select(array('sno.*'));
			$query->where('sno.state_code', $state_code);
			$query->where('sno.prefix', $prefix);
			return $query->first();
		}
		catch(Exception $e) {
			Log::error($e->getMessage().' '.$e->getTraceAsString());
		}		
	}
	
	/**
	 * generateSerialNumber() method is used to get next serial number
	 * @param  String $state_code
	 * @param  String $prefix
	 * @param  String $currentSno
	 * @return String
	 */
	
	public function generateSerialNumber($state_code, $prefix, $currentSno='') {
		try{
			$config = $this->getSerialNumber($state_code, $prefix);

			$stateCode = $config->state_code;
	    	$prefix = $config->prefix;
	    	$length = $config->length;

	    	$year = ($config->yearlength == 4) ? date('Y') : date('y');
	    	$month = ($config->monthlength == 1) ? date('n') : date('m');

	    	$currentNumber = (isset($currentSno) && !empty($currentSno)) ? $currentSno : '';

	    	$currentId = (int)str_replace(array($prefix, $stateCode, $month, $year), '', $currentNumber);
	    	$nextId = $currentId + 1;
	    	$lengthOfZero = ($length - strlen($nextId));

	    	$appendZero = '';
	    	if($lengthOfZero) {
	    		for($i=1; $i <= $lengthOfZero; $i++) {
					$appendZero .= '0';
				}
	    	}

	    	return $prefix.$stateCode.$year.$month.$appendZero.$nextId;	
		}
		catch(Exception $e) {
			Log::error($e->getMessage().' '.$e->getTraceAsString());
		}
	}
}
