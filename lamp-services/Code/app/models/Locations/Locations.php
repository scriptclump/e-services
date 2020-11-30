<?php

namespace App\models\Locations;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Locations extends Model {

    protected $table = 'locations'; // table name
    protected $primaryKey = 'location_id';
    public $timestamps = false;

    
    public function getMfgIdForLocationId($lid)
    {
        if(!empty($lid) && is_numeric($lid)){
            $mfgId = DB::table($this->table)
                        ->where('location_id', $lid)
                        ->pluck('manufacturer_id');
            return $mfgId;
        }else{
            return false;
        }
    }

    public function getParentIdForLocationId($lid)
    {
        if(!empty($lid) && is_numeric($lid)){
            $parentId = DB::table($this->table)
                        ->where('location_id', $lid)
                        ->pluck('parent_location_id');
            return $parentId;
        }else{
            return false;
        }
    }

    public function getAllChildIdForParentId($lid)
    {
        if(!empty($lid) && is_numeric($lid)){
            $childIds = DB::table($this->table)
                        ->where('parent_location_id', $lid)
                        ->pluck('location_id')->all();
            return $childIds;
        }else{
            return false;
        }
    }

    public function getDestinationLocationIdFromSAPCode($sapcode)
    {
        if(!empty($sapcode) && isset($sapcode)){
            $locationId = DB::table($this->table)
                        ->where('erp_code', $sapcode)
                        ->pluck('location_id');
            return $locationId;
        }else{
            return false;
        }
    }

    public function getSAPCodeFromLocationId($locationId)
    {
        if(!empty($locationId) && isset($locationId)){
            $erpCode = DB::table($this->table)
                        ->where('location_id', $locationId)
                        ->pluck('erp_code');
            return $erpCode;
        }else{
            return false;
        }
    }

    public function createOrReturnLocationId($destDetails, $mfgId)
    {
        $status = 0;
        $message = '';
        $id = 0;
        $locationTypeID = DB::table('location_types')
            ->where('manufacturer_id', $mfgId)
            ->where('location_type_name',$destDetails->Type)
            ->pluck('location_type_id');
        if($locationTypeID){
            try{
                $id = DB::table($this->table)->insertGetId(
                    Array(
                            'location_name' => $destDetails->name, 'manufacturer_id'=>$mfgId, 'location_type_id'=>$locationTypeID,
                            'location_email'=> $destDetails->email, 'location_address' => $destDetails->address, 'state'=>$destDetails->state, 'erp_code'=>$destDetails->sapcode,
                            'region' => $destDetails->region, 'longitude'=>$destDetails->longitude, 'latitude'=>$destDetails->latitude
                        )
                );
                $status = 1;
                $message = 'Location created succesfully';
            }catch(PDOException $e){
                $status = 0;
                $message = 'Error during location creation';
            }
        }else{
            $message = 'Invalid location type';
        }
        return Array('Status'=>$status, 'Message'=>$message, 'Id'=>$id);
    }


    public function getAllLocationsForMfgId($mfgId){
        if(!empty($mfgId) && is_numeric($mfgId)){
            $locations = DB::table($this->table)->select('location_id')
                            ->where('manufacturer_id', $mfgId)
                            ->get()->all();
            return $locations;
        }else{
            return FALSE;
        }
    }

    public function getStorageLocationIdForMissing($mfgId){
        $locationTypeID = DB::table('location_types')->where('manufacturer_id', $mfgId)->where('location_type_name', 'Storage Location ')->pluck('location_type_id');
        if($locationTypeID){
            $locationId = DB::table('locations')->where('manufacturer_id', $mfgId)->where('location_name', 'Missing Material')
                ->where('location_type_id', $locationTypeID)->pluck('location_id');
            if($locationId){
                return $locationId;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function getStorageLocationIdForDamage($mfgId, $locationId){
        $locationTypeID = DB::table('location_types')->where('manufacturer_id', $mfgId)->where('location_type_name', 'Storage Location ')->pluck('location_type_id');
        if($locationTypeID){
            $locationId = DB::table('locations')->where('manufacturer_id', $mfgId)->where('parent_location_id', $locationId)
                ->where('location_type_id', $locationTypeID)->where('location_name', 'Block Material')->pluck('location_id');
            if($locationId){
                return $locationId;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }    
}
