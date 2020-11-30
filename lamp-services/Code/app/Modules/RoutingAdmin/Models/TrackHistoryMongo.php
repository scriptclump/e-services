<?php

namespace App\Modules\RoutingAdmin\Models;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use DB;

class TrackHistoryMongo extends Eloquent
{
    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="geo_track_log";
    public $timestamps = false;

    public function saveTrackHistoryMongo($data)
    {
        $dt2 = new \MongoDate(strtotime($data['date']));
        $saveData = [];
        $saveData['de_id'] = (int)$data['de_id'];
        $saveData['de_name'] = $data['de_name'];
        $saveData['distance'] = $data['distance'];
        $saveData['coordinate_data'] = $data['coordinate_data'];
        $saveData['order_data'] = $data['order_data'];
        $saveData['hub_id'] = $data['hub_id'];
        $saveData['hub_name'] = $data['hub_name'];
        $saveData['date'] = $dt2;
        $this::where('de_id', (int)$data['de_id'])
        ->where('date', $dt2)
        ->update($saveData, ['upsert' => true]);
        return true;
    }

    public function getTrackHistoryMongo($de_id, $date)
    {
        $newDate = new \MongoDate(strtotime($date));
        $data = $this::where('de_id',(int)$de_id)
                        ->where('date', $newDate)
                        ->get()->all();
        if (count($data) > 0) {
            return $data[0];
        }else{
            return false;
        }
        
    }

    public function getTrackHistoryByHubMongo($hub_id, $from_date, $to_date)
    {
        $startDate = new \MongoDate(strtotime($from_date));
        $endDate = new \MongoDate(strtotime($to_date));
        $data = $this::select(['de_id','de_name', 'distance','hub_id','hub_name','order_data','date'])
                        ->where('hub_id',(int)$hub_id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->get()->all();
        if (count($data) > 0) {
            return $data;
        }else{
            return false;
        }
        
    }

    public function getTrackHistoryByDEMongo($de_id, $from_date, $to_date)
    {
        $startDate = new \MongoDate(strtotime($from_date));
        $endDate = new \MongoDate(strtotime($to_date));
        $data = $this::select(['de_id','de_name', 'distance','hub_id','hub_name','order_data','date'])
                        ->where('de_id',(int)$de_id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->get()->all();

        if (count($data) > 0) {
            return $data;
        }else{
            return false;
        }
        
    }
}
