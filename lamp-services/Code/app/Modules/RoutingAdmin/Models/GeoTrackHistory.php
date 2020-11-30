<?php 
namespace App\Modules\RoutingAdmin\Models;

use App\Modules\LegalEntities\Models\Legalentity;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;
use Log;
use  \Exception;


class GeoTrackHistory extends Model{

	protected $primaryKey = 'geo_track_history_id';
    public $timestamps = false;
    protected $table = 'geo_track_history';

	public function __construct(){

	}
	
	public function insertGeoTrackHistory($data)
	{

		$trackData = $this::firstOrNew(
			array(
				'de_id' => $data['de_id'], 
				'trip_date' => $data['date']
			)
		);
		$trackData->hub_id = $data['hub_id'];
		$trackData->hub_name = $data['hub_name'];
		$trackData->de_id = $data['de_id'];	
		$trackData->de_name = $data['de_name'];		
		$trackData->distance = $data['distance'];
		$trackData->trip_date = $data['date'];
		$trackData->save();
	}
}