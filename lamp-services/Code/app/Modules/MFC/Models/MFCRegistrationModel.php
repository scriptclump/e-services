<?php
namespace App\Modules\MFC\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use URL;
use Cache;
use Illuminate\Http\Request;
use Log;

date_default_timezone_set('Asia/Kolkata');


class MFCRegistrationModel extends Model {

    public function __construct() {

    }	

    public function getCustomerByAadhar($aadhar_id) {
    
    	try{

            $data = DB::selectFromWriteConnection(DB::raw("select mobile_no,user_id,legal_entity_id,otp from users where aadhar_id = $aadhar_id"));

            if(isset($data[0]) && !empty($data[0])) {
                return $data[0];
            } else {
                return array();
            }    

    	} catch (Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    		return false;
    	}	

    }


    public function updateCustomerDetails($user_id, $updateArray) {
    
    	try{

    		$creditLimit 		= $updateArray['credit_limit'];
    		$latitude			= $updateArray['latitude'];
    		$longitude 			= $updateArray['longitude'];
    		$legal_entity_id 	= $updateArray['legal_entity_id'];
    		$address1 			= $updateArray['address1'];
            $address2           = $updateArray['address2'];
            $mfc                = $updateArray['mfc_id'];

    		DB::table('user_ecash_creditlimit')->where('user_id',$user_id)->update(['creditlimit' => $creditLimit]);

			DB::table('legal_entities')->where('legal_entity_id',$legal_entity_id)->update(['latitude'=>$latitude,'longitude'=>$longitude,'address1'=>$address1,'address2'=>$address2]);

            $MFC_Data = DB::table('mfc_customer_mapping')->where(array('cust_le_id'=>$legal_entity_id,'mfc_id'=>$mfc))->get()->all();    		


            DB::table('mfc_customer_mapping')->where('cust_le_id',$legal_entity_id)->update(['is_active'=>0]);

    		if(empty($MFC_Data)) {
                
                DB::table('mfc_customer_mapping')->insertGetId([
                                        'mfc_id' => $mfc,
                                        'cust_le_id'=>$legal_entity_id,
                                        'credit_limit'=>$creditLimit,
                                        'is_active'=>1
                                      ]);

            } else {

                DB::table('mfc_customer_mapping')->where(['cust_le_id'=>$legal_entity_id,'mfc_id' => $mfc])->update([
                                        'credit_limit'=>$creditLimit,
                                        'is_active'=>1
                                      ]);
            }  


            return true;

    	} catch (Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    		return false;
    	}	

    }

    public function getPjpAreaByPincode($pinCode) {

        try{

            // return DB::table('pjp_pincode_area')
            //     ->select('pjp_pincode_area_id')
            //     ->join('dc_hub_mapping','dc_hub_mapping.hub_id','=','pjp_pincode_area.le_wh_id')
            //     ->join('wh_serviceables','wh_serviceables.le_wh_id','=','dc_hub_mapping.dc_id')
            //     ->where('wh_serviceables.pincode',$pinCode)->first();
            return DB::select("SELECT getBeatbyPincode($pinCode) as pjp_pincode_area_id");


        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }   
    }

    public function getBeatIdByLeID($legal_entity_id) {

        try{

            $beat_id = DB::table('customers')
                ->select(DB::raw("beat_id"))
                ->where("le_id",'=', $legal_entity_id)
                ->get()->all();

            if(!empty($beat_id)) {
                return $beat_id[0]->beat_id;
            }
                return 0;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }   
    }

    public function getCityStateByPincode($pincode) {

        try{

            $data = DB::table('cities_pincodes as cp')
                ->select(array('cp.city','z.zone_id','z.name'))
                ->join('zone as z','z.name','=','cp.state')
                ->where('cp.pincode','=', $pincode)
                ->first();

            return $data;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }   
    }

    public function getLeIdByCode($le_code) {

        try{

            $data = DB::table('legal_entities')
                ->select('legal_entity_id')
                ->where('le_code','=', $le_code)
                ->first();
            return isset($data->legal_entity_id)?$data->legal_entity_id:0;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }   
    }


}    	
