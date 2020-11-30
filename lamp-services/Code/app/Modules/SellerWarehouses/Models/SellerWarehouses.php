<?php

namespace App\Modules\SellerWarehouses\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Log;
use Response;
use \App\Central\Repositories\RoleRepo;
use \App\Central\Repositories\ProductRepo;
use App\Modules\Roles\Models\Role;
use App\Modules\LegalEntity\Models\LegalEntityModel;
class SellerWarehouses extends Model {
/**
     * 
     * function for saving a warehouse from existing list of warehouses to a legalentity
     * input : warehousedetails, legal_entity_id
     * output : status, message
**/
    public $roleAccess;
    public $productrepo;
    public $bussinessUnitList;
    public function __construct() {
        parent::__construct();
        $this->bussinessUnitList= '<option value="">Please Select Bussiness Unit ....</option> ';
        $this->roleAccess = new RoleRepo();
        $this->productrepo = new ProductRepo();
    }
    /**
     * [saveWarehouse To save warehouse]
     * @param  [array] $warehouses [warehouse info]
     * @param  [int] $legal_id   [legal entity id]
     * @return [array]             [With status, message and warehouse id]
     */
	public function saveWarehouse($warehouses,$legal_id){
		try {
			$le_wh_id = 0;
            $status = false; 
            $message = 'Unable to save warehouse'; 
			foreach ($warehouses as $key => $warehouse) {
				$warehouse['details'] = DB::table('lp_warehouses')->leftjoin('logistics_partners','lp_warehouses.lp_id','=','logistics_partners.lp_id')->where('lp_wh_id',$warehouse['wh_id'])->select('lp_warehouses.*','logistics_partners.lp_name')->first();
				if(!empty($warehouse['tin_number']) && isset($warehouse['tinProof']) && isset($warehouse['apobProof'])){
				$le_wh_id = DB::table('legalentity_warehouses')->insertGetId([
					'lp_id' => $warehouse['details']->lp_id,
                    'lp_name' => $warehouse['details']->lp_name,
                    'lp_wh_id' => $warehouse['details']->lp_wh_id,
					'legal_entity_id' => $legal_id,
					'tin_number' => $warehouse['tin_number'],
					'lp_wh_name' => $warehouse['details']->lp_wh_name,
					'contact_name' => $warehouse['details']->contact_name,
					'phone_no' => $warehouse['details']->phone_no,
					'email' => $warehouse['details']->email,
					'country' => $warehouse['details']->country,
					'address1' => $warehouse['details']->address1,
					'address2' => $warehouse['details']->address2,
					'state' => $warehouse['details']->state,
					'pincode' => $warehouse['details']->pincode,
					'city' => $warehouse['details']->city,
					'longitude' => $warehouse['details']->longitude,
					'latitude' => $warehouse['details']->latitude,
					'landmark' => $warehouse['details']->landmark,
                    'status' => 1
					]);
				if($le_wh_id){
					if(!empty($warehouse['tinProof'])){
	                    $type = 'wh_tin';
	                    $id = $this->saveFile($legal_id,$warehouse['tinProof'],$le_wh_id,$type);
	                }
	                if(!empty($warehouse['apobProof'])){
	                     $type = 'wh_apob';
	                     $id = $this->saveFile($legal_id,$warehouse['apobProof'],$le_wh_id,$type);
	                }
					$status = true; 
            		
				}
			}
			else{
				$status = false; 
            	$message = 'Unable to add Warehouse'; 	
			}
		}
        $message = 'Warehouse added successfully!!'; 
		} catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return Response::json([
                        'id' => $le_wh_id,
                        'status' => $status,
                        'message' => $message]);
	}
    /**
     * [saveCustomWarehouse To save custom warehouse]
     * @param  [array] $data [Warehouse info]
     * @param  [int] $id   [legal entity id]
     * @return [array]       [With status,message and wh id]
     */
    public function saveCustomWarehouse($data,$id){
        try {
            $status = false;
            $message = "Unable to save Warehouse";
            $cost_center=$this->findCostCenter($data['businessUnit1']); 
            $is_apob_add = (isset($data['is_apob_id']) && $data['is_apob_id'] == 'on') ? 1 : 0;
            $limitCheck = (isset($data['limit_check']) && $data['limit_check'] == 'on') ? 1 : 0;
            $billingCheck = (isset($data['billing']) && $data['billing'] == 'on') ? 1 : 0;
            $this->legalEntity = new LegalEntityModel();
            
            $cityname=$this->legalEntity->getCityName($data['wh_city']);
            $ctyname=json_decode(json_encode($cityname),true);
            $data['wh_city']=$ctyname[0]['city_name'];
            $le_wh_id = DB::table('legalentity_warehouses')->insertGetId([
                'lp_id' => '',
                'dc_type'=>$data['wh_type'],
                'bu_id' => $data["businessUnit1"],
                'display_name' => $data['displayname'],
                //'parent_display_name' =>$data['parent_display_name'],
                'cost_centre'=>$cost_center,
                'fssai'=>$data['fssai'],
                'lp_name' => "Custom",
                'le_wh_code'=>$data['wh_code'],
                'lp_wh_name' => $data['wh_name'],
                'address1' => $data['wh_address1'],
                'address2' => $data['wh_address2'],
                'pincode' => $data['wh_pincode'],
                'city' => $data['wh_city'],
                'margin' => $data['margin'],
                'state' => $data['wh_state'],
                'country' => $data['wh_country'],
                'longitude' => $data['wh_log'],
                'latitude' => $data['wh_lat'],
                'contact_name' => $data['contact_name'],
                'phone_no' => $data['phone_no'],
                'email' => $data['email'],
                'legal_entity_id' => $id,
                'status' => 1,
                'is_apob'=>$is_apob_add,
                'credit_limit_check'=>$limitCheck,
                'is_billing'=>$billingCheck,
                'jurisdiction'=>$data['Jurisdiction_id'],
                ]);
            $find = DB::table('warehouse_config')->where('le_wh_id',$le_wh_id)->pluck('le_wh_id');
            if(empty($find))
            {
            DB::table('warehouse_config')->insert(['le_wh_id'=>$le_wh_id,'wh_location'=>$data['wh_name'],'wh_location_types'=>120001]);
            }
            $ecashTableData = ['creditlimit'=>'0',
                                   'minimum_order_value'=>'1000',
                                   'self_order_mov'=>'2000',
                                   'mov_ordercount'=>'0',
                                ];
            $customers = DB::table("master_lookup")->select('value')->where('mas_cat_id','=',3)->get()->all();
            $customers = json_decode(json_encode($customers), true);

            foreach ($customers as  $customer_values) {      

            $query = DB::table("ecash_creditlimit")->insert(['dc_id'=>$le_wh_id,'customer_type'=>$customer_values['value'],
                                   'state_id'=>$data['wh_state'],
                                   'creditlimit'=>'0',
                                   'minimum_order_value'=>'1000',
                                   'self_order_mov'=>'2000',
                                   'mov_ordercount'=>'0']);    
            }

            if(!empty($le_wh_id)){
                $status = true;
                $message = "Warehouse saved successfully";
                $hubs = isset($data['hubs']) ? $data['hubs'] : [];
                if(!empty($hubs))
                {
                    foreach ($hubs as $hubId)
                    {
                        DB::table('dc_hub_mapping')
                            ->insert(['dc_id' => $le_wh_id, 'hub_id' => $hubId]);
                    }
                }


                $price_group_id = isset($data['price_group_id'])?$data['price_group_id']:0;
                DB::table('stockist_price_mapping')->insert(['le_wh_id'=> $le_wh_id,'legal_entity_id'=>$id,'stockist_price_group_id'=> $price_group_id]);

            }
            return json_encode([
                'status' => $status,
                'message' => $message,
                'le_wh_id' => $le_wh_id
                ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateCustomWarehouse To update custom warehouse]
     * @param  [array] $data [wh info]
     * @param  [int] $id   [warehouse id]
     * @return [array]       [With status and message]
     */
    public function updateCustomWarehouse($data,$id){
        try {
            $status = false;
            $message = "Unable to update Warehouse data";
            $cost_center=$this->findCostCenter($data['businessUnit1']); 
            $isActive = (isset($data['is_active']) && $data['is_active'] == 'on') ? 1 : 0;
            $is_apob = (isset($data['is_apob_id']) && $data['is_apob_id'] == 'on') ? 1 : 0;
            $creditLimit_id = (isset($data['CreditLimit_Check']) && $data['CreditLimit_Check'] == 'on') ? 1 : 0;
            $billing_id = (isset($data['billing']) && $data['billing'] == 'on') ? 1 : 0;
            $is_disabled = (isset($data['is_disabled']) && $data['is_disabled'] == 'on') ? 1 : 0;
            $ff_otp = (isset($data['send_ff_otp']) && $data['send_ff_otp'] == 'on') ? 1 : 0;
            $is_bin_using = (isset($data['is_bin_using']) && $data['is_bin_using'] == 'on') ? 1 : 0;
            $days = isset($data['is_days'])?$data['is_days']:'';
            $time = isset($data['appt_time'])?$data['appt_time']:'';
             DB::table('legalentity_warehouses')
                ->where('le_wh_id',$id)
                ->where('legal_entity_id',$data['legal_entity_id'])
                ->update([
                    'lp_id' => '',
//                    'dc_type' => $data["wh_type"],
                    'bu_id' => $data["businessUnit1"],
                    'cost_centre'=>$cost_center,
                    'lp_name' => "Custom",
                    'display_name' => $data['displayname'],
                    //'parent_display_name' =>$data['parent_display_name'],
                    'le_wh_code'=>$data['wh_code'],
                    'lp_wh_name' => $data['wh_name'],
                    'address1' => $data['wh_address1'],
                    'address2' => $data['wh_address2'],
                    'pincode' => $data['wh_pincode'],
                    'fssai'=>$data['fssai'],
                    'city' => $data['wh_city'],
                    'margin' => $data['margin'],
                    'state' => $data['wh_state'],
                    'country' => $data['wh_country'],
                    'longitude' => $data['wh_log'],
                    'latitude' => $data['wh_lat'],
                    'contact_name' => $data['contact_name'],
                    'phone_no' => $data['phone_no'],
                    'email' => $data['email'],
                    'status' => $isActive,
                    'is_apob'=>$is_apob,
                    'credit_limit_check'=>$creditLimit_id,
                    'is_billing'=>$billing_id,
                    'jurisdiction'=>$data['Jurisdiction_edit'],
                    'is_disabled' =>$is_disabled,
                    'send_ff_otp'=>$ff_otp,
                    'is_binusing'=>$is_bin_using,
                    'wh_pdp'=>$days,
                    'wh_pdp_slot'=>$time,
                    ]);
            $hubs = isset($data['hubs']) ? $data['hubs'] : [];
            if($data['isfc'] == 0){
                if($data['is_virtual'] == 0){
                    $fcs = isset($data['fcs'])?$data['fcs']:[];
                }else{
                    $fcs=isset($data['dcundervirtualdc'])?$data['dcundervirtualdc']:[];
                }
            }else{
                $fcs = isset($data['dcs'])?$data['dcs']:[];
            }
            DB::table('dc_hub_mapping')
                        ->where('dc_id', $id)
                        ->delete();
            
            if(!empty($hubs))
            {
                foreach ($hubs as $hubId)
                {
                    DB::table('dc_hub_mapping')
                        ->insert(['dc_id' => $id, 'hub_id' => $hubId]);
                }
            }
            $dc_le_id=DB::table('legalentity_warehouses')
                    ->select('legal_entity_id')
                    ->where('le_wh_id','=',$id)
                    ->get()->all();
            $update_id = Session::get('userId');
            if($data['isfc']== 1){
                // $mapping_Table=DB::table('dc_fc_mapping')->select('fc_le_wh_id')->where('fc_le_wh_id','=',$id)->first();
                $getchildbuid = DB::table('legalentity_warehouses')->select('bu_id')->where('le_wh_id','=',$id)->first();//this is used to get fc_le_wh_id's buid
                $business_unit = DB::table('business_units')->where('bu_id',$getchildbuid->bu_id)->update(['parent_bu_id'=>NULL]);
                DB::table('dc_fc_mapping')
                ->where('fc_le_wh_id',$id)
                ->delete();
            }else{
                $mapping_Table=DB::table('dc_fc_mapping')->select('fc_le_wh_id')->where('dc_le_wh_id','=',$id)->get();
                foreach ($mapping_Table as  $value) {
                if(isset($value->fc_le_wh_id) && $value->fc_le_wh_id!=''){
                    $getchildbuid = DB::table('legalentity_warehouses')->select('bu_id')->where('le_wh_id','=',$value->fc_le_wh_id)->first();//this is used to get fc_le_wh_id's buid
                    $business_unit = DB::table('business_units')->where('bu_id',$getchildbuid->bu_id)->update(['parent_bu_id'=>NULL]);
                    DB::table('dc_fc_mapping')
                        ->where('dc_le_wh_id',$id)
                        ->delete();
                    }
                }
            }
            
            if(!empty($fcs))
            {
                if($data['isfc'] == 0 || $data['is_virtual']==1){ 
                   
                    foreach ($fcs as $fcId) {
                        $fc_le_id=[];
                        $fc_le_id=DB::table('legalentity_warehouses')
                        ->select('legal_entity_id')
                        ->where('le_wh_id','=',$fcId)
                        ->get()->all();
                        if( count($fc_le_id)>0){
                            DB::table("dc_fc_mapping")
                            ->insert(['dc_le_wh_id'=>$id,'dc_le_id'=>$dc_le_id[0]->legal_entity_id,'fc_le_wh_id'=>$fcId,'fc_le_id'=>$fc_le_id[0]->legal_entity_id,'status'=>$isActive,'updated_by'=>$update_id,'created_by'=>$update_id]);

                            $getchildbuid = DB::table('legalentity_warehouses')->select('bu_id')->where('le_wh_id','=',$id)->first();//this is used to get fc_le_wh_id's buid
                            $getparentbuid = DB::table('legalentity_warehouses')->select('bu_id')->where('le_wh_id','=',$fcId)->first();// this is used to get dc_le_wh_id's buid
                            $business_unit = DB::table('business_units')->where('bu_id',$getparentbuid->bu_id)->update(['parent_bu_id'=>$getchildbuid->bu_id]);
                        }
                    }
                }else{
                  
                     foreach ($fcs as $fcId) {
                        $dc_id=[];
                        $dc_id=DB::table('legalentity_warehouses')
                        ->select('legal_entity_id')
                        ->where('le_wh_id','=',$fcId)
                        ->get()->all();
                        if(count($dc_id)>0){
                           /* DB::table("dc_fc_mapping")
                            ->insert(['dc_le_wh_id'=>$fcId,'dc_le_id'=>$dc_id[0]->legal_entity_id,'fc_le_wh_id'=>$id,'fc_le_id'=>$dc_le_id[0]->legal_entity_id,'status'=>$isActive,'updated_by'=>$update_id,'created_by'=>$update_id]);*/
                             DB::table("dc_fc_mapping")
                            ->insert(['fc_le_wh_id'=>$id,
                                'fc_le_id'=>$dc_le_id[0]->legal_entity_id,
                                'dc_le_wh_id'=>$fcId,
                                'dc_le_id'=>$dc_id[0]->legal_entity_id,
                                'status'=>$isActive,
                                'updated_by'=>$update_id,
                                'created_by'=>$update_id]);

                            $getchildbuid = DB::table('legalentity_warehouses')->select('bu_id')->where('le_wh_id','=',$id)->first();//this is used to get fc_le_wh_id's buid
                            $getparentbuid = DB::table('legalentity_warehouses')->select('bu_id')->where('le_wh_id','=',$fcId)->first();// this is used to get dc_le_wh_id's buid
                            $business_unit = DB::table('business_units')->where('bu_id',$getchildbuid->bu_id)->update(['parent_bu_id'=> $getparentbuid->bu_id]);                           
                            // update fc's parent bu id with dc's buid
                            // $legalEntity =DB::table('legal_entities')->where('legal_entity_id',$dc_le_id[0]->legal_entity_id)->update(['parent_le_id'=>$dc_id[0]->legal_entity_id]);

                        }
                        
                    }

                }

            }
            DB::table('warehouse_config')
                     ->where('le_wh_id',$id)
					 ->where('wh_location_types',120001)
                     ->update(['wh_location'=>$data['wh_name']]);

             $price_group_id = isset($data['price_group_id'])?$data['price_group_id']:0;

             $stpTable =$this->getPriceGroupEdit($id);

             if (count($stpTable)==0) {
                 DB::table('stockist_price_mapping')->insert(['legal_entity_id'=>$data['legal_entity_id'],
                                                                    'le_wh_id'=>$id,
                                                                    'stockist_price_group_id'=>$price_group_id]);
            }else{


                DB::table('stockist_price_mapping')->where('le_wh_id',$id)->update(['stockist_price_group_id'=> $price_group_id]);
            }


            $status = true;
            $message = "Warehouse updated successfully";
            return json_encode([
                'status' => $status,
                'message' => $message
                ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [saveFile To save warehouse related documents]
     * @param  [int] $legal_id [legal entity id]
     * @param  [file] $file     [file]
     * @param  [int] $id       [reference no]
     * @param  [string] $type     [document type]
     * @return [int]           [inserted id]
     */
	public function saveFile($legal_id,$file,$id,$type){
      try {
	      if(!empty($file)){
	         $fName = $type.'_'.$id.'_'.$file->getClientOriginalName();
			 $amazon_directory_name = 'le-warehouses';
			 $url = $this->productrepo->uploadToS3($file,$amazon_directory_name,1);
	         //$destinationPath = public_path().'/uploads/LegalWarehouses/'.$fName;
	         //$extension = $file->getClientOriginalExtension();
	         //$path = '/uploads/LegalWarehouses/'.$fName;
	         //copy($file,$destinationPath);
	         $doc_id = DB::table('legal_entity_docs')
	                        ->insertGetId([
	                          'doc_name' => $fName,
	                          'legal_entity_id' => $legal_id,
	                          'reference_no' => $id,
	                          'doc_url' => $url,
	                          'doc_type' => $type
	                    ]);
	          if($doc_id){
	            return $doc_id;
	          }
	          else{
	            return 0;
	          }
	      }
    }
  	 catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
   
    /**
     * [saveDocs To save warehouse related documents]
     * @param  [array] $data [warehouse info]
     * @param  [int] $id   [warehouse id]
     * @return [array]       [With status and message]
     */
    public function saveDocs($data,$id){
        try {
            $message = "Unable to save docs";
            $status = false;
            $doc_id = 0;
            if(!empty($data['le_wh_id'])){
                if(!empty($data['tin_files'])){
                        $type = 'wh_tin';
                        $doc_id = $this->saveFile($id,$data['tin_files'],$data['le_wh_id'],$type);
                    }
                if(!empty($data['apob_files'])){
                        $type = 'wh_apob';
                        $doc_id = $this->saveFile($id,$data['apob_files'],$data['le_wh_id'],$type);
                }
                if($doc_id != 0){
                $status = true;
                $message = "Documents saved Successfully";
                }
            }
            return json_encode([
                'status' => $status,
                'message' => $message
                ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateDocs To update warehouse related documents]
     * @param  [array] $data [warehouse information]
     * @param  [int] $id   [warehouse id]
     * @return [array]       [Returns with status and message]
     */
    public function updateDocs($data,$id){
        try {
            $message = "Docs not updated";
            $status = false;
	        $url = null;
            $amazon_directory_name = 'le-warehouses';
            if(!empty($data['tin_doc_id'])){
                if(isset($data['tin_files']) && !empty($data['tin_files'])){
                    //echo "HERE"; die();
                        $type = 'wh_tin';
				//$doc_id = $this->updatePinDocs($id,$data['tin_files'],$data['le_wh_id'],$data['tin_doc_id'],	$type);		                        
				//$doc_id = $this->updatePinDocs($id,$data['tin_files'],$data['le_wh_id'],$data['tin_doc_id'],$type);
                        //$legal_id,$file,$le_wh_id,$id,$type){
                        $url = $this->productrepo->uploadToS3($data['tin_files'],$amazon_directory_name,1);
                        
                        $fName = $type.'_'.$data['tin_doc_id'].'_'.$data['tin_files']->getClientOriginalName();
                        DB::table('legal_entity_docs')
                            ->where('doc_id',$data['tin_doc_id'])
                            ->update([
                              'doc_name' => $fName,
                              'legal_entity_id' => $id,
                              'reference_no' => $data['le_wh_id'],
                              'doc_url' => $url,
                              'doc_type' => $type
                        ]);
             
                        //echo "<pre>"; print_r($doc_id); die();
                }
            }
            else{
                if(isset($data['tin_files']) && !empty($data['tin_files'])){
                    $type = 'wh_tin';
                    $url = $this->saveFile($id,$data['tin_files'],$data['le_wh_id'],$type);
                }
            }
            if (!empty($data['apob_doc_id'])) {
                 if(isset($data['apob_files']) && !empty($data['apob_files'])){
                        $type = 'wh_apob';
			$url = $this->productrepo->uploadToS3($data['apob_files'],$amazon_directory_name,1);
                        //$doc_id = $this->updatePinDocs($id,$data['apob_files'],$data['le_wh_id'],$data['apob_doc_id'],$type);
                        $fName = $type.'_'.$data['apob_doc_id'].'_'.$data['apob_files']->getClientOriginalName();
                        DB::table('legal_entity_docs')
                            ->where('doc_id',$data['apob_doc_id'])
                            ->update([
                              'doc_name' => $fName,
                              'legal_entity_id' => $id,
                              'reference_no' => $data['le_wh_id'],
                              'doc_url' => $url,
                              'doc_type' => $type
                        ]);
             

                }
            }
            else{   
                 if(isset($data['apob_files']) && !empty($data['apob_files'])){
                        $type = 'wh_apob';
                        $url = $this->saveFile($id,$data['apob_files'],$data['le_wh_id'],$type);
                }
            }
	        $message = $message. " - ".$url;
            if($url != null){
            $status = true;
            $message = "Updated Successfully";
            }
            return json_encode([
                'status' => $status,
                'message' => $message
                ]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updatePinDocs To update warehouse documents]
     * @param  [int] $legal_id [legal entity id]
     * @param  [file] $file     [document]
     * @param  [int] $le_wh_id [warehouse id]
     * @param  [int] $id       [document id]
     * @param  [string] $type     [type of document]
     * @return [int]           [1]
     */
    public function updatePinDocs($legal_id,$file,$le_wh_id,$id,$type){
      try {
          if(!empty($file)){
            //echo "<pre>"; print_r("Herereer"); die();
            $stat = 0;
             $fName = $type.'_'.$id.'_'.$file->getClientOriginalName();
             $destinationPath = public_path().'/uploads/LegalWarehouses/'.$fName;
             $extension = $file->getClientOriginalExtension();
             $path = '/uploads/LegalWarehouses/'.$fName;
             copy($file,$destinationPath);
             DB::table('legal_entity_docs')
                            ->where('doc_id',$id)
                            ->update([
                              'doc_name' => $fName,
                              'legal_entity_id' => $legal_id,
                              'reference_no' => $le_wh_id,
                              'doc_url' => $path,
                              'doc_type' => $type
                        ]);
                $stat = 1;
                //echo "<prE>"; print_r($doc_id); die();

                return $stat;
          }
        }
      catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateWarehouse To update tin number in warehouse]
     * @param  [array] $data [documents info]
     * @param  [int] $id   [warehouse id]
     * @return [array]       [With status and message]
     */
    public function updateWarehouse($data,$id){
        try {
            $status = false;
            $message = "Unable to update Warehouse data";
 
            if(!empty($data['tin_number'])){
                DB::table('legalentity_warehouses')
                        ->where('le_wh_id',$id)
                        ->update([
                            'tin_number' => $data['tin_number']
                            ]);
            if(!empty($data['tinProof'])){
                $type = "tinDoc";
               $this->updateFile($data['legal_entity_id'],$data['tinProof'],$id,$type);
            }
             if(!empty($data['apobProof'])){
                $type = "apobDoc";
               $this->updateFile($data['legal_entity_id'],$data['apobProof'],$id,$type);
            }
            }
            $status = true;
            $message = "Warehouse updated successfully";
            return json_encode([
                'status' => $status,
                'message' => $message
                ]);
        }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [updateFile To update file]
     * @param  [int] $legal_id [legal entity id]
     * @param  [file] $file     [file]
     * @param  [int] $id       [document id]
     * @param  [string] $type     [document type]
     * @return [int]           [document id]
     */
    public function updateFile($legal_id,$file,$id,$type){
      try {
          if(!empty($file)){
             $fName = $type.'_'.$id.'_'.$file->getClientOriginalName();
             $destinationPath = public_path().'/uploads/LegalWarehouses/'.$fName;
             $extension = $file->getClientOriginalExtension();
             $path = '/uploads/LegalWarehouses/'.$fName;
             copy($file,$destinationPath);
             $doc_id = DB::table('legal_entity_docs')
                            ->where('doc_id',$id)
                            ->update([
                              'doc_name' => $fName,
                              'legal_entity_id' => $legal_id,
                              'reference_no' => $id,
                              'doc_url' => $path,
                              'doc_type' => $type
                        ]);
              if($doc_id){
                return $doc_id;
              }
              else{
                return 0;
              }
          }
        }
      catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getLogisticsPartners To get Logistics Partners]
     * @return [array ] [List of warehouses]
     */
    public function getLogisticsPartners() {
        $user_id = Session::get('userId');
        $legal_entity_id = DB::table('users')
                ->where('user_id', '=', $user_id)
                ->select('legal_entity_id')
                ->first();
        //echo "<prE>"; print_r($user_id); echo "<prE>"; print_r($legal_entity_id); die();
        $roleRepo = new RoleRepo();
        $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
        $userId = Session::get('userId');
        $role=new Role();
        $dcList = $role->getWarehouseData($userId, 6,0);
        $dc=json_decode($dcList,true);
        $dc=implode($dc,',');
        $dc=explode(',',$dc);

        if ($legal_entity_id->legal_entity_id != 0) {

            // DB::enableQueryLog();
            $query = DB::table('legalentity_warehouses')
                        ->leftJoin('legal_entities','legal_entities.legal_entity_id','=','legalentity_warehouses.legal_entity_id')
                        ->leftJoin('dc_fc_mapping','dc_fc_mapping.fc_le_wh_id', '=', 'legalentity_warehouses.le_wh_id')

                        ->select('legalentity_warehouses.lp_id','legalentity_warehouses.dc_type','legalentity_warehouses.le_wh_id', 'legalentity_warehouses.lp_name', 'legalentity_warehouses.lp_wh_name','legalentity_warehouses.le_wh_code','legalentity_warehouses.fssai','legalentity_warehouses.tin_number','legalentity_warehouses.margin','legalentity_warehouses.display_name', 
                            DB::raw('getStateNameById(legalentity_warehouses.state) AS state'),
                            DB::raw('CONCAT(legalentity_warehouses.address1, " ", '
                                        . 'IFNULL(legalentity_warehouses.address2,"")) AS address'),
                            DB::raw('getLeWhName(dc_fc_mapping.dc_le_wh_id) AS parent'),
                            'legalentity_warehouses.city','legalentity_warehouses.status','legalentity_warehouses.credit_limit_check',
                            DB::raw('getMastLookupDescByValue(legal_entities.legal_entity_type_id) AS description')
                        );                        
            if($globalaccess != 1){
                $query->whereIn('legalentity_warehouses.le_wh_id',$dc);
            }
            $lpWarehouses = $query->groupBy('legalentity_warehouses.le_wh_id')->orderBy('legalentity_warehouses.le_wh_id', 'DESC')
                        ->get()->all();
            $editDCPermission = $this->roleAccess->checkPermissionByFeatureCode('DC002');
            $deleteDCPermission = $this->roleAccess->checkPermissionByFeatureCode('DC003');
            if($globalaccess==1){
                $editDCPermission=1;
                $deleteDCPermission=1;
            }
			$DcTypes = $this->getMasterLookUpData('118','DC Type');
			foreach($DcTypes as $type)
			{
				$finalDc[$type->value] = $type->name;
			}
            foreach ($lpWarehouses as $lpWarehouse) {                
                $actions='';
                $lpWarehouse->status = ($lpWarehouse->status == 1) ? 'Active': 'In-Active';
                $lpWarehouse->credit_limit_check = ($lpWarehouse->credit_limit_check == 1) ? 'Yes': 'No';
				if($lpWarehouse->dc_type){
				$lpWarehouse->dc_type = $finalDc[$lpWarehouse->dc_type];
				}
                if(empty($lpWarehouse->lp_id)){
                    if($editDCPermission)
                    {
                        $actions = '<span style="padding-left:20px;" >'
                        . '<a href="/warehouse/editCustom/'. $lpWarehouse->le_wh_id.'"><i class="fa fa-pencil"></i></span>';
                    }
                 }
                 else{
                     if($editDCPermission)
                     {
                         $actions = '<span style="padding-left:20px;" >'
                        . '<a href="javascript:void(0);" onclick="editWarehouse(' . $lpWarehouse->le_wh_id . ')" data-target="#basicvalCodeModal1"><i class="fa fa-pencil"></i></span>';
                     }                    
                 }
                if($deleteDCPermission)
                {
                    $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0)" onclick="deleteEntityType(' . $lpWarehouse->le_wh_id . ')"><i class="fa fa-trash-o"></i></a></span>';
                }                
//                $lpWarehouse->actions = $actions;                
                $lpWarehouse->actions = $actions;
            }
            //exit;
            //print_r(DB::getQueryLog());exit;
            return $lpWarehouses;
          // echo '<pre>'; print_r($lpWarehouses);die;
        }
    }

    /**
     * [deleteLpWharehouses To delete warehouse]
     * @param  [int] $le_wh_id [warehouse id]
     * @return [array]           
     */
    public function deleteLpWharehouses($le_wh_id)
    {
        try{
            
            $deleteLpwh = DB::table("legalentity_warehouses")
                            ->where('le_wh_id','=',$le_wh_id)
                            ->delete();
            return $deleteLpwh;
            
        }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [deletePin To delete a pincode from wh_Serviceables]
     * @param  [int] $id [wh_Serviceables id]
     * @return [array]
     */
    public function deletePin($id)
    {
        try{
            
            $deletePinId = DB::delete("DELETE w.* FROM wh_serviceables w WHERE w.wh_serviceables_id=".$id);
            return $deletePinId;
            
        }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    /**
     * [getMasterLookUpData get master lookup values by it's category id and name]
     * @param  [int] $id   [master category id ]
     * @param  [string] $name [master lookup cat name]
     * @return [array]       [master lookup values ]
     */
    public function getMasterLookUpData($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
            ->orderBy('master_lookup.sort_order', 'asc')
            ->get()->all();
      return $returnData;
    }
    /**
     * [getBussinessUnitData To get business unit list]
     * @return [array] [business unit list]
     */
    public function getBussinessUnitData()
    {
        return  $this->getChildBussinessUnits(0,1);
    }
    /**
     * [getChildBussinessUnits To get child business unit list of parent bu]
     * @param  [int] $bid   [bu id]
     * @param  [int] $level [level]
     * @return [array]        [Bu list]
     */
    public function getChildBussinessUnits($bid,$level)
    {

        $roleRepo = new RoleRepo();
        $globalaccess=$roleRepo->checkPermissionByFeatureCode('GLB0001');
        $legal_entity_id = Session::get('legal_entity_id');

        $bussinesUnits = DB::table('business_units')
                ->select('bu_id','bu_name','parent_bu_id')
                ->where('is_active','1') ;
        if($globalaccess==1){

        }else{
            $userId=Session::get('userId');
            $business_unit=DB::table('user_permssion')
            ->where('user_id',$userId)
            ->where('permission_level_id',6)
            ->pluck('object_id')->all();
            //echo $legal_entity_id;exit;
            //print_r($business_unit);exit;
            if(in_array(0,$business_unit)){

            }else{
                $bussinesUnits=$bussinesUnits->whereIn('bu_id',$business_unit);
            }
        }
        $bussinesUnits=$bussinesUnits->get()->all();
       // print_r($bussinesUnits);exit;

        if (!empty($bussinesUnits)) 
        {
            foreach($bussinesUnits as  $units)
            { 
               /* $css_class='';
                switch ($level) {
                    case 1:
                        $css_class='parent_cat';
                        break;
                    case 2:
                        $css_class='sub_cat';
                        break;
                    case 3:
                        $css_class='prod_class';
                        break;
                    default:
                        $css_class='prod_class_'.$level;
                        break;
                }*/
                $this->bussinessUnitList.= '<option value="'.$units->bu_id.'"> '.$units->bu_name.'</option>';
                //$this->getChildBussinessUnits($units->bu_id,$level+1);
            }
        }
        return $this->bussinessUnitList;
    }
    /**
     * [getBeatsInfo To get beats info]
     * @param  [int] $hubId [hub id]
     * @return [array]        [Beats list]
     */
    public function getBeatsInfo($hubId)
    {
        try
        {
            $collection = [];
            if($hubId > 0)
            {
                DB::enableQueryLog();
                $collection = DB::table('spokes')
                        ->leftJoin('pjp_pincode_area', 'pjp_pincode_area.spoke_id', '=', 'spokes.spoke_id')
//                        ->leftJoin('pincode_area', 'pincode_area.pjp_pincode_area_id', '=', 'pjp_pincode_area.pjp_pincode_area_id')
                        ->leftJoin('legalentity_warehouses', 'legalentity_warehouses.le_wh_id', '=', 'spokes.le_wh_id')
                        ->where('spokes.le_wh_id', $hubId)
                        ->select(DB::raw('legalentity_warehouses.lp_wh_name as Hub'),
                                DB::raw('spokes.spoke_name as Spoke'), DB::raw('pjp_pincode_area.pjp_name as Beat'),
                                DB::raw('pjp_pincode_area.days as Days'), DB::raw('GetUserName(pjp_pincode_area.rm_id, 2) as RM')
//                                DB::raw('pincode_area.pincode as Pincode'), 
//                                DB::raw('getAreaName(pincode_area.area_id) as Area')
                                )
                        ->get()->all();
//                Log::info(DB::getQueryLog());
            }
            return $collection;
        } catch (\ErrorException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    /**
     * [findCostCenter To get cost center of bu]
     * @param  [int] $id [bu id]
     * @return [string]     [cost center]
     */
    public function findCostCenter($id){
        $data=DB::table('business_units')
                ->select('cost_center')
                ->where('bu_id','=',$id)
                ->get()->all(); 
        $data=json_decode(json_encode($data),true);
        return $data[0]['cost_center'];

    }
    /**
     * [getselectedhubValidates To validate selected hub ]
     * @param  [INT] $id   [DC ID]
     * @param  [array] $data [hub info]
     * @return [array]       [status and message]
     */
    public function getselectedhubValidates($id,$data){
        $status=true;
        $message='';
        for($i=0;$i<count($data);$i++){
            $hubData=DB::table('dc_hub_mapping')
                    ->select('dc_id')
                    ->where('hub_id',$data[$i])
                    ->get()->all();
            /*if(count($hubData)==0){
                echo ($data[$i]);

            }*/
            $hubData=json_decode(json_encode($hubData),true);
            if(count($hubData)>0){
                if($hubData[0]['dc_id'] != $id){
                    $status=false;
                    $dcName=DB::table('legalentity_warehouses')
                            ->select('lp_wh_name')
                            ->where('le_wh_id',$hubData[0]['dc_id'])
                            ->get()->all();
                    $hubName=DB::table('legalentity_warehouses')
                            ->select('lp_wh_name')
                            ->where('le_wh_id',$data[$i])
                            ->get()->all();
                    $dcName=json_decode(json_encode($dcName),true);
                    $hubName=json_decode(json_encode($hubName),true);
                    $message=$message.' '.$hubName[0]['lp_wh_name'].' has already been mapped to '.$dcName[0]['lp_wh_name']."\n";
                }
            }
        }
        return json_encode([
                'status' => $status,
                'message' => $message
                ]);

    }
    /**
     * [getFcValidate To check whether the fc is already mapped or not]
     * @param  [int] $id   [fc id]
     * @param  [int] $fcs  [fc's list to map]
     * @param  [int] $dcId [dc id]
     * @return [array]       [With status and message]
     */
    public function getFcValidate($id,$fcs,$dcId){
        $status=true;
        $message='';
        for($i=0;$i<count($fcs);$i++){
            $fcName=DB::select(DB::raw("select display_name from legalentity_warehouses where le_wh_id=".$fcs[$i]));
            $fcMapped=DB::select(DB::raw("select DISTINCT(l.`display_name`) FROM  legalentity_warehouses l LEFT JOIN dc_fc_mapping d  ON d.dc_le_wh_id=l.`le_wh_id` WHERE fc_le_wh_id=".$fcs[$i]." AND dc_le_wh_id not in (".$dcId.")"));
            if(count($fcMapped)>0){
                for($j=0;$j<count($fcMapped);$j++){
                    $message.=$fcName[0]->display_name ." already mapped to ".$fcMapped[$j]->display_name."\n";
                    $status=false;
                }
            }
        }
        return json_encode([
            'status' => $status,
            'message' => $message
            ]);
    }
    /**
     * [getPriceGroupEdit To get price group list
     * @return  [To get price group list]
     **/
    public function getPriceGroup(){
       $query = DB::table('master_lookup')->select(['value','master_lookup_name'])
                                ->where('mas_cat_id','=',3)->groupBy('value')
                                ->orderBy('master_lookup_name','ASC')->get()->all();
       return $query;           
    }
    /**
     * [getMasterLookupForTimeSlot To get Time slots list]
     * @return [array] [Time slots list]
     */
    public function getMasterLookupForTimeSlot(){

        $query = DB::table('master_lookup')
                ->select(['value','master_lookup_name'])
                ->where('mas_cat_id',171)
                ->get()->all();
        return $query;

    }
    /**
     * [getPriceGroupEdit To get price group of a warehouse]
     * @param  [int] $id [warehouse id]
     * @return [int]     [price group]
     */
    public function getPriceGroupEdit($id){
       $query = DB::table('stockist_price_mapping')->select('stockist_price_group_id')->where('le_wh_id','=',$id)->first();
       return $query;           
    } 
    /**
     * [getDays To get pjp days list]
     * @return [array] [pjp days list]
     */
    public function getDays(){
        $query = DB::table('master_lookup')->select('description')->where('mas_cat_id','=',186)->first();
        $result = explode (",", $query->description); 
        return $result;
    }
    public function state(){
        
        $results = DB::table('zone')->select('zone.zone_id as state_id','zone.name as state')->where('country_id',99)->orderByRaw("FIELD(name,'Telangana') DESC")->get();
        return $results;
    }

        /**
     * [saveGStAddress To save GST Address]
     * @param  [array] $data [GST Address info]
     * @param  [int] $id   [legal entity id]
     */
    public function saveGStAddress($data){
        try {
            $status = false;
            $cost_center=$this->findCostCenter($data['businessUnit1']); 
            $gst_state_code = $this->findGstState($data['gst_state']);
            $this->legalEntity = new LegalEntityModel();
            $cityname=$this->legalEntity->getCityName($data['gst_city']);
            $ctyname=json_decode(json_encode($cityname),true);
            $data['gst_city']=$ctyname[0]['city_name'];
            $le_gst_id = DB::table('legal_entity_gst_addresses')->insertGetId([
                'bu_id' => $data['businessUnit1'],
                'display_name' => $data['display_name'],
                'cost_centre'=>$cost_center,
                'gst_state_code'=>$gst_state_code,
                'gstin'=>strtoupper($data['tin_number']),
                'fssai'=>$data['fssai'],
                'state' => $data['gst_state'],
                'city' => $data['gst_city'],
                'address1' => $data['gst_address1'],
                'address2' => $data['gst_address2'],
                'pincode' => $data['gst_pincode'],
                'country' => $data['gst_country'],
                'jurisdiction' => $data['Jurisdiction_id'],
                'longitude' => $data['gst_log'],
                'latitude' => $data['gst_lat'],
                'contact_name' => $data['contact_name'],
                'phone_no' => $data['phone_no'],
                'email' => $data['email'],
                'landmark' =>$data['gst_landmark'],
                 'status' => true,
                ]);
  
            if(!empty($le_gst_id)){
                $status = true;
                $message = "New GSTIN address saved successfully";
            }
            return json_encode([
                'status' => $status,
                'message' => $message,
                'billing_id' => $le_gst_id
                ]);
        } catch (\ErrorException $ex) {
            return json_encode(['status'=>false,'message'=>'Unable to save GSTIN Address']);
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function gstAddressList(){
         $this->roleAccess = new RoleRepo();
        $globalaccess=$this->roleAccess->checkPermissionByFeatureCode('GSTAD001');
        $editGstPermission = $this->roleAccess->checkPermissionByFeatureCode('GSTAD003');
        $deleteGstPermission = $this->roleAccess->checkPermissionByFeatureCode('GSTAD004');

        if($globalaccess == 1){
                $editGstPermission=1;
                $deleteGstPermission=1;
        }
        $actions='';
        $list_gsts = DB::table('legal_entity_gst_addresses')
                    ->select('display_name','cost_centre','gstin','state','billing_id','address1','phone_no','email','country','city','status','fssai',
                        DB::raw('getStateNameById(legal_entity_gst_addresses.state) AS state')
                    )
                    ->get();
        foreach ($list_gsts as $list_gst) {   
        $list_gst->status = ($list_gst->status == 1) ? 'Active': 'In-Active';
        if($editGstPermission){
                        $actions = '<span style="padding-left:20px;" >'
                        . '<a href="/warehouse/editGstAddress/'. $list_gst->billing_id.'"><i class="fa fa-pencil"></i></span>';
        }                    
        if($deleteGstPermission){
                    $actions .= '<span style="padding-left:20px;" ><a href="javascript:void(0)" onclick="deleteEntityType(' . $list_gst->billing_id . ')"><i class="fa fa-trash-o"></i></a></span>';
        }                
               $list_gst->actions = $actions;
    }
    return $list_gsts;
        
}
  
    public function editGstList($id){
        try{
        $result = DB::table('legal_entity_gst_addresses')->where('billing_id',$id)->first();
        return $result;
        }catch (\ErrorException $ex) {
            \Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }

    }
        /**
     * [updateGstAddress To update GST ADDRESS]
     * @param  [array] $data [gst info]
     * @param  [int] $id   [billing id]
     * @return [array]       [With status and message]
     */
    public function updateGstAddress($data,$id){
       try {
            $status = false;
            $message = "Unable to update GSTIN Address data";
            $cost_center=$this->findCostCenter($data['businessUnit2']); 
            $gst_state_code = $this->findGstState($data['gst_state']);
            $isActive = (isset($data['status'])) ? 1 : 0;
            DB::table('legal_entity_gst_addresses')
                ->where('billing_id',$id)
                ->update([
                    'bu_id' => $data["businessUnit2"],
                    'display_name' => $data['display_name'],
                    'cost_centre'=>$cost_center,
                    'gst_state_code'=>$gst_state_code,
                    'gstin'=>$data['tin_number'],
                    'state' => $data['gst_state'],
                    'fssai' =>$data['fssai'],
                    'city' => $data['gst_city'],
                    'address1' => $data['gst_address1'],
                    'address2' => $data['gst_address2'],
                    'pincode' => $data['gst_pincode'],
                    'country' => $data['gst_country'],
                    'jurisdiction' => $data['Jurisdiction_id'],
                    'longitude' => $data['gst_log'],
                    'latitude' => $data['gst_lat'],
                    'contact_name' => $data['contact_name'],
                    'phone_no' => $data['phone_no'],
                    'email' => $data['email'],
                    'landmark' =>$data['gst_landmark'],
                    'status' => $isActive,
                    ]);
          
            $status = true;
            $message = "GSTIN address updated successfully";
            return json_encode([
                'status' => $status,
                'message' => $message
                ]); 
        } catch (\ErrorException $ex) {
            return json_encode([
                'status' => false,
                'message' => 'Failed to Update Record'
                ]);
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
      /**
     * [findGST State Code To get GST State of Code]
     * @param  [int] $id [state id]
     * @return [string]     [GST State Code ]
     */
    public function findGstState($id){
        $data=DB::table('zone')
                ->select('gst_state_code')
                ->where('zone_id','=',$id)
                ->get(); 
        $data=json_decode(json_encode($data),true);
        return $data[0]['gst_state_code'];

    }
         /**
     * [findGST State Code To get GST State of Code]
     * @param  [int] $id [state id]
     * @return [string]     [GST State Code ]
     */
    public function getGstins($id){
        $data=DB::table('legal_entity_gst_addresses')
                ->select('gstin')
                ->where('id',$id)
                ->get(); 
        return $data;

    }
}
?>
