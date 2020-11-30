<?php
/*
FileName : CustomertallyGetDataFromDB.php
Author   : eButor
Description : Pricing dashboard model.
CreatedDate : 18/02/2019
*/
//defining namespace
namespace App\Modules\TallyConnector\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Log;

class CustomertallyModel extends Model{

   //Getting the Voucher data ...
    public function getVoucherData($cost_centre='',$voucher_type='',$voucher_flag='',$voucher_date_from='',$voucher_date_to=''){

    	   if($voucher_type!=""||$voucher_flag!=''||$voucher_date_from='' || $voucher_date_to=''){
			//need to implement date...
			$voucher_date_start=$voucher_date_from.' 00:00:00';
			$voucher_date_end=$voucher_date_to.' 23:59:59';
            //Getting the cost_center value ...
            $voucherData=[];
			if($cost_centre!=""){
	          	$query = DB::table('vouchers')
	          			//->where('reference_no','TSIV19020170619');
				        ->whereBetween('voucher_date', [$voucher_date_start, $voucher_date_end])
						->where('is_posted', '=',$voucher_flag);
						$query->where("cost_centre",$cost_centre);
						if($voucher_type!=""){
							$query->where("voucher_type",$voucher_type);
						}
				$voucherData = $query->groupBy("voucher_code","reference_no")
							->orderBy("voucher_code")
							->offset(0)
							->limit(500)
							->get()->all();
			}
			return $voucherData;
				
		}else{
			return [];
		}		
	}

	// Check for Duplicate Voucher
	public function checkDuplicateVoucher($VCNumber,$reffNumber, $VCType){
		$voucherCount = DB::table('vouchers');
		if($VCType=='Purchase'){
			//Voucher code can be duplicate for Purchases,so checking with refno
			$voucherCount->where("reference_no", "=", $reffNumber) 
						->where("voucher_type", "=", $VCType);
		} else {
			$voucherCount->where('voucher_code','=',$VCNumber);
		}		
		$voucherCount = $voucherCount->where('is_posted', '=', '1')
			->count();
		return $voucherCount;
	}
   //Getting the Vocherdata
   public function getVoucherLineData($voucherCode, $voucherType,$reffNumber){
		$voucherLineData = DB::table('vouchers')
					->where('voucher_code', '=', $voucherCode)
					->where('reference_no', '=', $reffNumber)
					->where('voucher_type', '=', $voucherType)
					->get()->all();
		return $voucherLineData;
		//print_r($voucherLineData);die();			
	}


	public function updateVoucher($voucher_code, $updateVal, $tally_resp){
		// update table by 1
		$updateVouchers = DB::table('vouchers')
		             	->where('voucher_code', '=', $voucher_code)
		             	->update(['is_posted'=> $updateVal, 'tally_resp' => $tally_resp, 'sync_date' => date('Y-m-d H:i:s') ]);

		return true;
	}
	public function updateLedger($voucher_code, $updateVal, $tally_resp){
		// update table by 1
		$updateVouchers = DB::table('legal_entities')
		             	->where('le_code', '=', $voucher_code)
		             	->update(['is_posted'=> $updateVal, 'tally_resp' => $tally_resp ]);

		return true;
	}
	public function getCostCentreData($HubId){
		$data = DB::table('legalentity_warehouses as lw')
							->leftJoin('business_units as bu','bu.bu_id','=','lw.bu_id')
							->leftJoin('legal_entities as le','le.legal_entity_id','=','lw.legal_entity_id')
							->select(['bu.cost_center','bu.bu_name','bu.tally_company_name','bu.sales_ledger_name','lw.legal_entity_id','le.legal_entity_type_id'])
							->where("lw.le_wh_id",$HubId)
							->first();
		return $data;
	}
	public function getWarehouseCostCentreByHub($HubId){
		$data = DB::table('legalentity_warehouses as lw')
							->leftJoin('dc_hub_mapping as dhm','dhm.dc_id','=','lw.le_wh_id')
							->leftJoin('business_units as bu','bu.bu_id','=','lw.bu_id')
							->select(['lw.le_wh_id','bu.cost_center','bu.bu_name','bu.tally_company_name','bu.sales_ledger_name','lw.legal_entity_id'])
							->where("dhm.hub_id",$HubId)
							->first();
		return $data;
	}
	// get Customer Data if they have any Order
	public function getRetailerData($parent_le_id){
		$customerDataSQL = "select * FROM
						(
							SELECT l.legal_entity_id,l.business_legal_name,l.parent_le_id,l.le_code,l.address1,l.address2,l.city,l.pincode,l.gstin,getStateNameById(l.state_id) as state_name,
							(SELECT COUNT(gds_order_id) FROM gds_orders AS gord WHERE gord.cust_le_id=l.legal_entity_id) AS 'TotalOrder'
							FROM legal_entities AS l
							WHERE legal_entity_type_id LIKE '30%' AND is_posted=0 and parent_le_id=".$parent_le_id."
						) AS innertbl WHERE TotalOrder>0";
		$customerData = DB::select(DB::raw($customerDataSQL));
		return $customerData;

	}
	// get Customer Data if they have any Order
	public function getFCData($parent_le_id){
		$data = DB::table('dc_fc_mapping as dfm')
							->leftJoin('legal_entities as le','le.legal_entity_id','=','dfm.fc_le_id')
							->select(['le.legal_entity_id','le.business_legal_name','le.parent_le_id','le.le_code','le.address1','le.address2','le.city','le.pincode','le.gstin',DB::raw('getStateNameById(le.state_id) as state_name')])
							->where("dfm.dc_le_id",$parent_le_id)
							->where("le.is_posted",0)
							->get()->all();
		return $data;
	}
	// get failed vouchers ledger Data
	public function getFailedLedgerData($cost_centre){
		$data = DB::table('vouchers as v')
							->join('gds_invoice_grid as gig','gig.invoice_code','=','v.voucher_code')
							->join('gds_orders as go','go.gds_order_id','=','gig.gds_order_id')
							->join('legal_entities as le','le.legal_entity_id','=','go.cust_le_id')
							->select(['le.legal_entity_id','le.business_legal_name','le.parent_le_id','le.le_code','le.address1','le.address2','le.city','le.pincode','le.gstin',DB::raw('getStateNameById(le.state_id) as state_name')])
							->where('v.tally_resp','LIKE','Ledger %')
							->where("v.voucher_type",'Sales')
							->where("v.ledger_group",'Sundry Debtors')
							->where("v.is_posted",0)
							->where("v.cost_centre",$cost_centre)
							->groupBy("v.ledger_account")
							->get()->all();
		return $data;
	}


 }