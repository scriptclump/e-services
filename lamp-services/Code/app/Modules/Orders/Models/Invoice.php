<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Orders\Models\PaymentModel;
use App\Modules\Orders\Models\Shipment;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\GdsBusinessUnit;
use App\Modules\Orders\Controllers\OrdersController;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Cpmanager\Controllers\MasterLookupController;
use App\Central\Repositories\CustomerRepo;
use DB;
use Log;
use App\Lib\Queue;
use Lang;
class Invoice extends Model
{
	public function getInvoicedQtyByOrderId($orderId) {
		try{
			$fieldArr = array(DB::raw('SUM(products.qty) as invoicedQty'));
			$query = DB::table('gds_invoice_items as products')->select($fieldArr);
			$query->where('products.gds_order_id', $orderId);
			$row = $query->first();
			return isset($row->invoicedQty) ? (int)$row->invoicedQty : 0;
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}		
	}

	public function updateInvoiceItem($orderId, $productId, $fields) {
		try{
	       DB::table('gds_invoice_items')->where(array('gds_order_id'=>$orderId, 'product_id'=>$productId))->update($fields);
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}

	public function updateInvoiceGridById($gridId, $fields) {
		try{
		   DB::table('gds_invoice_grid')->where(array('gds_invoice_grid_id'=>$gridId))->update($fields);
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}

	public function updateSalesVoucher($voucherCode, $fields) {
		try{
	       DB::table('vouchers')->where(array('voucher_code'=>$voucherCode))->update($fields);
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}


	public function getInvoicedQtyByOrderIdAndProductId($orderId, $productId) {
		try{
			$fieldArr = array('products.product_id', 'products.qty as invoicedQty');
			$query = DB::table('gds_invoice_items as products')->select($fieldArr);
			$query->where('products.gds_order_id', $orderId);
			$query->where('products.product_id', $productId);
			return $query->first();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}

	public function getInvoicedQtyWithProductByOrderId($orderId) {
		try{
			$fieldArr = array('products.product_id', DB::raw('SUM(products.qty) as invoicedQty'));
			$query = DB::table('gds_invoice_items as products')->select($fieldArr);
			$query->where('products.gds_order_id', $orderId);
			$query->groupBy('products.product_id');
			$rows = $query->get()->all();
			$dataArr = array();
			if(is_array($rows)) {
				foreach ($rows as $data) {
					$dataArr[$data->product_id] = (int)$data->invoicedQty;
				}
			}
			return $dataArr;
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}

	public function getInvoicedQtyWithStatusByOrderId($orderId) {
		try{
			$fieldArr = array('products.invoice_status', DB::raw('SUM(products.qty) as invoicedQty'));
			$query = DB::table('gds_invoice_items as products')->select($fieldArr);
			$query->where('products.gds_order_id', $orderId);
			$query->groupBy('products.invoice_status');
			return $query->get()->all();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}

	public function getInvoicedPriceWithOrderID($orderId){
		try{
			$fieldArr = array(DB::raw('SUM(invoice.grand_total) as invoicedAmt'));
			$query = DB::table('gds_invoice_grid as invoice')->select($fieldArr)
					->where('invoice.gds_order_id', $orderId);
			$invoiceAmt = json_decode(json_encode($query->get()->all()), true);
			//dd($query->toSql());
			return $invoiceAmt[0]['invoicedAmt'];
		}
		catch(Exception $e){
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
	public function getInvoicedDetailById($invoiceId,$orderId) {

        $fieldArr = array('grid.*', 'gop.sku', 'gop.seller_sku', 'gop.price as single_price',
            'gop.mrp', 'gop.pname',
            'item.product_id',
            'item.qty as invoicedQty',
            'item.comments',
            'gop.qty as orderedQty',
            'gop.total as itemTotal',
            'gop.discount as orderdiscount',
            'gop.hsn_code',
            'item.price as single_unit_price',
            'item.tax_amount as item_tax_amount',
            'item.row_total as row_total_exc_tax',
            'item.row_total_incl_tax',
            'item.discount',
            'item.discount_amt',
            'item.discount_type',
            'invoice.discount as bill_discount',
            'invoice.discount_amt as bill_discount_amt',
            'invoice.discount_type as bill_discount_type',
            'item.CGST',
            'item.SGST',
            'item.IGST',
            'item.UTGST',
            DB::raw('(
				    CASE
				      WHEN ISNULL(
				        `gop`.`parent_id`
				      ) 
				      THEN `gop`.`product_id` 
				      ELSE `gop`.`parent_id` 
				    END
				  	) AS `sort_parent_id`'),
            DB::raw('(item.qty/item.eaches_in_cfc) as invCfc')
            );
        $query = DB::table('gds_invoice_grid as grid')->select($fieldArr);
        $query->join('gds_order_invoice as invoice', 'invoice.gds_invoice_grid_id', '=', 'grid.gds_invoice_grid_id');
        $query->join('gds_invoice_items as item', 'invoice.gds_order_invoice_id', '=', 'item.gds_order_invoice_id');
        $query->join('gds_order_products as gop', 'gop.product_id', '=', 'item.product_id');
        $query->where('grid.gds_invoice_grid_id', $invoiceId);
        $query->where('gop.gds_order_id', $orderId);
        $query->orderBy('gop.pname', 'asc');
        $query->orderBy('invCfc', 'asc');
        //echo $query->toSql();
        return $query->get()->all();
    }
    
    public function getAllInvoiceCodesByOrderid($orderId) {
		try{
			$fieldArr = array('gds_invoice_grid.invoice_code','gds_invoice_grid_id');
			$query = DB::table('gds_invoice_grid')->select($fieldArr)->where('gds_order_id',$orderId);
			return $query->get()->all();
		}
		catch(Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}	
	}


	public function getInvoiceDueAmount($invoiceId) {

			try{
	
				$this->_paymentModel = new PaymentModel();

				$this->_paymentModel->checkInitialCollectionEntry($invoiceId);

				$fieldArr = array('invoice_amount','collected_amount','return_total');

				$query = DB::table('collections')->select($fieldArr)
							->where('collections.invoice_id',$invoiceId)->first();

				$Due_Amt = $query->invoice_amount - $query->collected_amount - $query->return_total;

				echo json_encode(array('Due_Amt'=>$Due_Amt));


			}
			catch(Exception $e) {
				Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			}	
	}

	public function getInvoiceDueAmountValue($invoiceId) {

			try{
	
				$fieldArr = array(DB::raw('SUM(ledger.dr_amt) as dr_sum_amt'),DB::raw('SUM(ledger.cr_amt) as cr_sum_amt'));

				$query = DB::table('ledger')->select($fieldArr)
							->where('ledger.invoice_id',$invoiceId)->first();
				
				$Due_Amt = $query->dr_sum_amt - $query->cr_sum_amt;

				return $Due_Amt;


			}
			catch(Exception $e) {
				Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			}	
	}

    /**
     * [getInvoicedPriceWithOrderIDInvoiceID description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     * @added by prasenjit for return api For cp team
     * 19 th October
     */
    public function getInvoicedPriceWithOrderIDInvoiceID($orderId,$invoiceId){
		try{
			$fieldArr = array(DB::raw('SUM(invoice.grand_total) as invoicedAmt'));
			$query = DB::table('gds_invoice_grid as invoice')->select($fieldArr)
					->where('invoice.gds_order_id', $orderId)
					->where('invoice.gds_invoice_grid_id',$invoiceId);
			$invoiceAmt = json_decode(json_encode($query->get()->all()), true);
			//dd($query->toSql());
			return $invoiceAmt[0]['invoicedAmt'];
		}
		catch(Exception $e){
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}

    /**
     * method	:	saveSalesVoucher
     * input	:	$invoiceId
     * output	:	success/failed
     */
	

    public function saveSalesVoucher($invoiceId, $remarks='Sales Entry'){
    	//DB::beginTransaction();//commented by Nishanth
		try{

                $_BusinessUnit = new GdsBusinessUnit();


                $TallyRef		= array();
                $TallyEntriesML = DB::table('master_lookup')
                					->select(array('master_lookup_name','description','value'))
                					->where('mas_cat_id',142)
                					->get()->all();

                foreach($TallyEntriesML as $TallyEntry) {

                	$TallyRef[$TallyEntry->value] = $TallyEntry;

                }

                $fields = array('orders.order_code',
                				'orders.order_date',
                				'orders.hub_id',
                				'orders.le_wh_id',
                				'orders.discount_before_tax',
                				'grid.created_at as invoice_date',
                				'le.business_legal_name',
                				'le.le_code',
                				//'tax.tax',
                				//'tax_class.tally_reference',
                				DB::raw('SUM(products.row_total) as saleTotal'),
                				DB::raw('SUM((gdsprod.cost/gdsprod.qty)*products.qty) as costbeforeTax'),
                				DB::raw('SUM(((gdsprod.cost/gdsprod.qty)*products.qty*tax.tax)/100) as taxValueWithoutDisc'),                				
                				'grid.cgst_total',
                				'grid.sgst_total',
                				'grid.igst_total',
                				'grid.utgst_total',
                				'grid.invoice_code',
                				'grid.grand_total',
                        'invoice.discount_amt as bill_disc',
                        DB::raw('SUM(((gdsprod.cost/gdsprod.qty)*products.qty*gdsprod.discount)/100) as prdDisTotal'),
                        DB::raw('SUM(((((gdsprod.cost/gdsprod.qty)*products.qty*gdsprod.discount)/100)*tax.tax)/100) as taxOnDiscount'),
                        DB::raw('ROUND(tax.tax) as tax'),
                        DB::raw('SUM(products.CGST) as cgst'),
                        DB::raw('SUM(products.SGST) as sgst'),
  						DB::raw('SUM(products.IGST) as igst'),
  						DB::raw('SUM(products.UTGST) as utgst')
                    );

                $query = DB::table('gds_invoice_items as products')->select($fields);
                $query->join('gds_order_invoice as invoice','invoice.gds_order_invoice_id','=','products.gds_order_invoice_id');
                $query->join('gds_invoice_grid as grid','grid.gds_invoice_grid_id','=','invoice.gds_invoice_grid_id');
                $query->join('gds_orders as orders','orders.gds_order_id','=','grid.gds_order_id');
                $query->leftjoin('legal_entities as le','le.legal_entity_id','=','orders.cust_le_id');
                $query->join('gds_order_products as gdsprod', function($join)
                {
                    $join->on('gdsprod.product_id','=','products.product_id');
                    $join->on('gdsprod.gds_order_id','=','orders.gds_order_id');
                });
                $query->join('gds_orders_tax as tax', function($join)
                {
                    $join->on('tax.gds_order_prod_id','=','gdsprod.gds_order_prod_id');
                    $join->on('tax.gds_order_id','=','orders.gds_order_id');
                });
                //$query->leftJoin('gds_orders_tax as tax','tax.gds_order_prod_id','=','gdsprod.gds_order_prod_id');
                //$query->leftJoin('tax_classes as tax_class','tax_class.tax_class_id','=','tax.tax_class');
                $query->groupBy('tax.tax','invoice.gds_order_invoice_id');
                $query->where('grid.gds_invoice_grid_id', $invoiceId);
                $resArr = $query->get()->all();

                $hubId = isset($resArr[0]->hub_id) ? $resArr[0]->hub_id : 0;
                $leWhId = isset($resArr[0]->le_wh_id) ? $resArr[0]->le_wh_id : 0;

                $costCenterData = $_BusinessUnit->getBusinesUnitLeWhId($hubId, array('bu.cost_center','bu.bu_name'));
                $costCenterGroupData = $_BusinessUnit->getBusinesUnitLeWhId($leWhId, array('bu.cost_center'));
                
                $costCenterName = isset($costCenterData->cost_center) ? $costCenterData->cost_center : 'Z1R1D1';
                $bu_name = isset($costCenterData->bu_name) ? $costCenterData->bu_name : '';
                
                $costCenter = $costCenterName.' - '.$bu_name;

                $costCenterGroup = isset($costCenterGroupData->cost_center) ? $costCenterGroupData->cost_center : 'Z1R1';

                $withoutTaxArray = array();
                $withTaxArr = array();
                $insertArray =array();
                $taxAmountSum = 0;                
                
                if(count($resArr)) {
                	$grand_total = round($resArr[0]->grand_total);
                	if(isset($resArr[0]->discount_before_tax) && $resArr[0]->discount_before_tax == 1){
                		$grand_total = 0;
                	}
                	foreach ($resArr as $key => $resr) {
                        $res = $resr;

                		$invoice_code = (isset($res->invoice_code) && !empty($res->invoice_code)) ? $res->invoice_code : $invoiceId;
                                $invoice_date = isset($res->invoice_date)?$res->invoice_date:date('Y-m-d H:i:s');
	                    $salesAcct = isset($TallyRef['142001']) ? $TallyRef['142001']->master_lookup_name : '';
	                    $taxAcct = isset($TallyRef['142003']) ? $TallyRef['142003']->master_lookup_name : '';
                    //this is for discount applied on before tax ,so there is only product level disount and that is discount will be entered in vouchers table         
                            
                         if(isset($res->discount_before_tax) && $res->discount_before_tax == 1){
                            $res->saleTotal = $res->costbeforeTax;
                            $grand_total += ($res->costbeforeTax+$res->taxValueWithoutDisc);
                            $res->igst= ($res->igst_total!=0)?$res->taxValueWithoutDisc:0;
                            //$res->utgst= ($res->utgst_total!=0)?$res->taxValueWithoutDisc:0;
                            if($res->sgst!=0){
                                $res->sgst = $res->taxValueWithoutDisc/2;
                                $res->cgst = $res->taxValueWithoutDisc/2;
                            }
                            if($res->utgst!=0){
                            	$res->cgst = $res->taxValueWithoutDisc/2;
                            	$res->utgst = $res->taxValueWithoutDisc/2;
                            }

                        }
                        
                        $taxstr = '@'.$res->tax.'%';
                		if($res->igst_total!=0) {
		                    $salesAcct = isset($TallyRef['142002']) ? $TallyRef['142002']->master_lookup_name : '';
		                    $taxAcct = isset($TallyRef['142004']) ? $TallyRef['142004']->master_lookup_name : '';
		                    $taxAcct .=$taxstr;
		                	$withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142004']->description,'ledger_account'=>$taxAcct,'tran_type'=>'Cr','amount'=>$res->igst,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

                        } 
                		if($res->utgst_total!=0) {
		                    $salesAcct = isset($TallyRef['142002']) ? $TallyRef['142002']->master_lookup_name : '';
		                    $taxAcct = isset($TallyRef['142018']) ? $TallyRef['142018']->master_lookup_name : '';
		                    //$taxAcct .=$taxstr;
		                    $taxbr = '@'.($res->tax/2).'%';
		                    $taxAcct .=$taxbr;

		                	$withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142018']->description,'ledger_account'=>$taxAcct,'tran_type'=>'Cr','amount'=>$res->utgst,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

                		}
                		 
                		if($res->sgst_total!=0) {
                            
		                    $salesAcct = isset($TallyRef['142001']) ? $TallyRef['142001']->master_lookup_name : '';
		                    $taxAcct = isset($TallyRef['142003']) ? $TallyRef['142003']->master_lookup_name : '';
		                    $taxbr = '@'.($res->tax/2).'%';
		                    $taxAcct .=$taxbr;
                            $withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142003']->description,'ledger_account'=>$taxAcct,'tran_type'=>'Cr','amount'=>$res->sgst,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
		                } 

                		if($res->cgst_total!=0) {

		                    $salesAcct = isset($TallyRef['142001']) ? $TallyRef['142001']->master_lookup_name : '';
		                    $taxAcct = isset($TallyRef['142005']) ? $TallyRef['142005']->master_lookup_name : '';
		                    $taxbr = '@'.($res->tax/2).'%';
		                    $taxAcct .=$taxbr;
		                	$withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142005']->description,'ledger_account'=>$taxAcct,'tran_type'=>'Cr','amount'=>$res->cgst,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

		                }
                        
                        $salesAcct .= $taxstr;

	                	$withoutTaxArray[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>'Sales Accounts','ledger_account'=>$salesAcct,'tran_type'=>'Cr','amount'=>$res->saleTotal,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
	                
	                if(isset($res->bill_disc) && $res->bill_disc>0){
                            $disc_grp = isset($TallyRef['142013']->description)?$TallyRef['142013']->description:'';
                            $disc_act = isset($TallyRef['142013']->master_lookup_name)?$TallyRef['142013']->master_lookup_name:'';
                            $insertArray[] 	= array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$invoice_date,'ledger_group'=>$disc_grp,'ledger_account'=>$disc_act,'tran_type'=>'Dr','amount'=>round($res->bill_disc,2),'naration'=>'','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                        }
                    } 
                    $insertArray[] 	= array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$invoice_date,'ledger_group'=>'Sundry Debtors','ledger_account'=>$res->business_legal_name.' - '.$res->le_code,'tran_type'=>'Dr','amount'=>$grand_total,'naration'=>'Being the sales made to '.$res->business_legal_name.' Order No. '.$res->order_code.' dated '.$res->order_date.' with invoice no '.$res->invoice_code.' dated '.$res->invoice_date,'cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);   


                	$insertArray	= array_merge($insertArray,$withoutTaxArray);
                	$insertArray	= array_merge($insertArray,$withTaxArr);
                	
//echo '<pre/>';print_r($insertArray);die;
                	


                	DB::table('vouchers')->insert($insertArray);
                	$this->saveVoucherRoundoff($invoice_code, $remarks, $costCenter, $costCenterGroup);
                        if(isset($res->discount_before_tax) && $res->discount_before_tax == 1){
                            $this->saveTradeDiscountVoucher($resArr);
                            //$this->saveTradeDiscountVoucherTax($resArr);
                        }


                    //DB::commit(); //commented by Nishanth

                	return true;
                }
            

		}
		catch(Exception $e){
			//DB::rollback();//commented by Nishanth
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
			return false;
		}
	}
	
	public function saveVoucherRoundoff($invoiceCode, $remarks='Rounded', $costCentre='Z1R1D1', $costCenterGroup='Z1R1') {
		$query = "SELECT *,
                        IF( DiffAmt<=1 && DiffAmt>=-0.01, DrAmt-DiffAmt, DrAmt) AS 'FinalAmt'
                        FROM
                        (
                        SELECT is_posted,voucher_code, voucher_date, 
                        SUM(CASE tran_type WHEN 'Dr' THEN amount ELSE 0 END) AS 'DrAmt',
                        SUM(CASE tran_type WHEN 'Cr' THEN amount ELSE 0 END) AS 'CrAmt',
                        (SUM(CASE tran_type WHEN 'Dr' THEN amount ELSE 0 END) - SUM(CASE tran_type WHEN 'Cr' THEN amount ELSE 0 END)) AS 'DiffAmt'
                        FROM vouchers WHERE voucher_type='Sales' AND voucher_code = '$invoiceCode' and is_posted='0'
                         GROUP BY voucher_code
                        ) AS inntbl2
                        WHERE  DiffAmt!=0 and DiffAmt>=-1 AND DiffAmt<=1";
                

        $mismatchData = DB::select( DB::raw($query) );
        if(count($mismatchData)) {
        	foreach ($mismatchData as $value) {
	            $voucherNew  = array('voucher_code'=>$value->voucher_code,'voucher_type'=>'Sales','voucher_date'=>$value->voucher_date,'ledger_group'=>'710000 : General Admin Expenses','ledger_account'=>'711900 : Round Off','tran_type'=>($value->DiffAmt > 0 ) ? 'Cr' : 'Dr','amount'=> abs($value->DiffAmt),'naration'=>'','cost_centre'=>$costCentre,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$value->voucher_code,'is_posted'=>0,'Remarks'=>$remarks);
	            DB::table('vouchers')->insert($voucherNew);
	        }
        }        
	}
    public function saveTradeDiscountVoucher($resArr) {
        try {
        	$TallyRef = array();
            $TallyEntriesML = DB::table('master_lookup')
                    ->select(array('master_lookup_name', 'description', 'value'))
                    ->where('mas_cat_id', 142)
                    ->get()->all();
            foreach ($TallyEntriesML as $TallyEntry) {
                $TallyRef[$TallyEntry->value] = $TallyEntry;
            }
            $hubId = isset($resArr[0]->hub_id) ? $resArr[0]->hub_id : 0;
            $leWhId = isset($resArr[0]->le_wh_id) ? $resArr[0]->le_wh_id : 0;
            $_BusinessUnit = new GdsBusinessUnit();
            $costCenterData = $_BusinessUnit->getBusinesUnitLeWhId($hubId, array('bu.cost_center', 'bu.bu_name'));
            $costCenterGroupData = $_BusinessUnit->getBusinesUnitLeWhId($leWhId, array('bu.cost_center'));

            $costCenterName = isset($costCenterData->cost_center) ? $costCenterData->cost_center : 'Z1R1D1';
            $bu_name = isset($costCenterData->bu_name) ? $costCenterData->bu_name : '';
            $costCenter = $costCenterName . ' - ' . $bu_name;
            $costCenterGroup = isset($costCenterGroupData->cost_center) ? $costCenterGroupData->cost_center : 'Z1R1';
            $withoutTaxArray = array();
            $withTaxArr = array();
            $insertArray = array();
            $taxAmountSum = 0;
            $invoice_date = date('Y-m-d H:i:s');
            $roundoff = 0;
            $crs = 0;
            $drs = 0;
            if (count($resArr)) {
            	$grand_total = 0;
            	if (isset($resArr[0]->discount_before_tax) && $resArr[0]->discount_before_tax == 1) {
	            	foreach ($resArr as $key => $resr) {
	//print_r($resr);die;
	            	
	                $res = $resr;
	                
	                $invoice_code = (isset($res->invoice_code) && !empty($res->invoice_code)) ? 'TD'.$res->invoice_code : 'TD'.$invoiceId;
	                // this is for discount applied on before tax ,so there is only product level disount and that is discount will be entered in vouchers table
                    $res->saleTotal = round($res->prdDisTotal,2);
                    $grand_total += round(($res->prdDisTotal + $res->taxOnDiscount),2);
                    $res->igst = ($res->igst_total != 0) ? $res->taxOnDiscount : 0;
                    $res->utgst = ($res->utgst_total != 0) ? $res->taxOnDiscount : 0;
                    if ($res->sgst != 0) {
                        $res->sgst = round($res->taxOnDiscount / 2,2);
                        $res->cgst = round($res->taxOnDiscount / 2,2);
                    }
                    $drs +=round($res->saleTotal,2)+round($res->igst,2)+round($res->utgst,2)+round($res->sgst,2)+round($res->cgst,2);
                    $tax_per = $res->tax;
	                //$roundoff = $res->grand_total-($res->saleTotal+$res->sgst_total+$res->cgst_total);
	                
	                $remarks = 'Trade Discount Entry';
	                $taxstr = '@'.$tax_per.'%';
	                if ($res->igst != 0) {
	                	$taxAcct = isset($TallyRef['142004']) ? $TallyRef['142004']->master_lookup_name : '';
	                    $taxAcct .=$taxstr;
	                    $withTaxArr[] = array('voucher_code' => $invoice_code, 'voucher_type' => 'Credit Note', 'voucher_date' => $res->invoice_date, 'ledger_group' => $TallyRef['142004']->description, 'ledger_account' => $taxAcct, 'tran_type' => 'Dr', 'amount' => round($res->igst,2), 'naration' => '0', 'cost_centre' => $costCenter, 'cost_centre_group' => $costCenterGroup, 'reference_no' => $res->invoice_code, 'is_posted' => 0, 'Remarks' => $remarks);
	                }
	                if ($res->utgst != 0) {
	                	$taxAcct = isset($TallyRef['142018']) ? $TallyRef['142018']->master_lookup_name : '';
	                    $taxAcct .=$taxstr;
	                    $withTaxArr[] = array('voucher_code' => $invoice_code, 'voucher_type' => 'Credit Note', 'voucher_date' => $res->invoice_date, 'ledger_group' => $TallyRef['142018']->description, 'ledger_account' => $taxAcct, 'tran_type' => 'Dr', 'amount' => round($res->utgst,2), 'naration' => '0', 'cost_centre' => $costCenter, 'cost_centre_group' => $costCenterGroup, 'reference_no' => $res->invoice_code, 'is_posted' => 0, 'Remarks' => $remarks);
	                }
	                if ($res->sgst != 0) {
	                	$taxAcct = isset($TallyRef['142003']) ? $TallyRef['142003']->master_lookup_name : '';
	                	$taxbr = '@'.($tax_per/2).'%';
	                    $taxAcct .=$taxbr;
	                    $withTaxArr[] = array('voucher_code' => $invoice_code, 'voucher_type' => 'Credit Note', 'voucher_date' => $res->invoice_date, 'ledger_group' => $TallyRef['142003']->description, 'ledger_account' => $taxAcct, 'tran_type' => 'Dr', 'amount' => round($res->sgst,2), 'naration' => '0', 'cost_centre' => $costCenter, 'cost_centre_group' => $costCenterGroup, 'reference_no' => $res->invoice_code, 'is_posted' => 0, 'Remarks' => $remarks);
	                }
	                if ($res->cgst != 0) {
	                	$taxAcct = isset($TallyRef['142005']) ? $TallyRef['142005']->master_lookup_name : '';
	                	$taxbr = '@'.($tax_per/2).'%';
	                    $taxAcct .=$taxbr;
	                    $withTaxArr[] = array('voucher_code' => $invoice_code, 'voucher_type' => 'Credit Note', 'voucher_date' => $res->invoice_date, 'ledger_group' => $TallyRef['142005']->description, 'ledger_account' => $taxAcct, 'tran_type' => 'Dr', 'amount' => round($res->cgst,2), 'naration' => '0', 'cost_centre' => $costCenter, 'cost_centre_group' => $costCenterGroup, 'reference_no' => $res->invoice_code, 'is_posted' => 0, 'Remarks' => $remarks);
	                }
	                $salesAcct = isset($TallyRef['142016']) ? $TallyRef['142016']->master_lookup_name : '';
	                $salesAcct .=$taxstr;
	                $withoutTaxArray[] = array('voucher_code' => $invoice_code, 'voucher_type' => 'Credit Note', 'voucher_date' => $res->invoice_date, 'ledger_group' => 'Sales Accounts', 'ledger_account' => $salesAcct, 'tran_type' => 'Dr', 'amount' => round($res->saleTotal,2), 'naration' => '0', 'cost_centre' => $costCenter, 'cost_centre_group' => $costCenterGroup, 'reference_no' => $res->invoice_code, 'is_posted' => 0, 'Remarks' => $remarks);
	                	                
	            }
	            
	            $insertArray[] = array('voucher_code' => $invoice_code, 'voucher_type' => 'Credit Note', 'voucher_date' => $invoice_date, 'ledger_group' => 'Sundry Debtors', 'ledger_account' => $res->business_legal_name . ' - ' . $res->le_code, 'tran_type' => 'Cr', 'amount' => round($grand_total,2), 'naration' => 'Being the Trade discount given to ' . $res->business_legal_name . ' Order No. ' . $res->order_code . ' dated ' . $res->order_date . ' with invoice no ' . $res->invoice_code . ' dated ' . $res->invoice_date, 'cost_centre' => $costCenter, 'cost_centre_group' => $costCenterGroup, 'reference_no' => $res->invoice_code, 'is_posted' => 0, 'Remarks' => $remarks);
	            $crs = round($grand_total,2);
	            $roundoff = round($crs - $drs,2);
            	if(abs($roundoff)>0 && abs($roundoff)<1){
	            	$roundAccountName2 = $TallyRef['142011']->master_lookup_name;
	                $ledger_groupName2 = $TallyRef['142011']->description;
                    $withTaxArr[]  = array('voucher_code'=>$invoice_code,'voucher_type'=>'Credit Note','voucher_date'=>$res->invoice_date,'ledger_group'=>$ledger_groupName2,'ledger_account'=>$roundAccountName2,'tran_type'=>($roundoff > 0 ) ? 'Dr' : 'Cr','amount'=> abs($roundoff),'naration'=>'','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                }
                	if($grand_total > 0){
		                $insertArray = array_merge($insertArray, $withoutTaxArray);
		                $insertArray = array_merge($insertArray, $withTaxArr);
		                DB::table('vouchers')->insert($insertArray);
		            }
	            }
                return true;
            }
        } catch (Exception $ex) {
            
        }
    }

    public function getInvoiceGridOrderId($orderIds, $fields) {
    	try{
    		$query = DB::table('gds_invoice_grid as grid')->select($fields);
			$query->whereIn('grid.gds_order_id', $orderIds);
			return $query->get()->all();
    	}
    	catch(Exception $e) {
    		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    	}		
	}

  	public function getInvoiceProductByOrderId($orderId) {
        try {
			
			$fields = array(
	                            'product.*',
	                            'invoice.gds_order_invoice_id',
	                            'invoice.created_at as invoice_date',
	                            'product.price as single_price',
	                            'invproducts.qty as invoicedQty',
	                            'product.total as sub_total',
	                            'grid.gds_invoice_grid_id', 
	                            'grid.invoice_code',
                                    'remarks',
                                    'invoice.discount_type as bill_disc_type',
                                    'invoice.discount as bill_disc',
                                    'invoice.discount_amt as bill_disc_amt',
	                            'grid.ecash_applied',
                                    'product.qty as orderedQty',
	                            DB::raw('(
								    CASE
								      WHEN ISNULL(
								        `product`.`parent_id`
								      ) 
								      THEN `product`.`product_id` 
								      ELSE `product`.`parent_id` 
								    END
								  	) AS `sort_parent_id`'),
	                            'invproducts.price as single_unit_price',
	                            'invproducts.tax_amount as item_tax_amount',
	                            'invproducts.row_total as row_total_exc_tax',
	                            'invproducts.row_total_incl_tax',
	                            'invproducts.CGST',
	                            'invproducts.SGST',
	                            'invproducts.IGST',
	                            'invproducts.UTGST',
	                            DB::raw('(invproducts.qty/invproducts.eaches_in_cfc) as invCfc'),
                              DB::raw('getCFCType(invproducts.product_id,invproducts.eaches_in_cfc) as cfcName')
                              ,'orders.gds_order_id','orders.order_code','orders.order_date','orders.cust_le_id',
                                'orders.created_by as order_created_by','orders.is_self','orders.order_status_id',
                            DB::raw('(select user_id from users where users.legal_entity_id=orders.cust_le_id and users.is_parent=1 limit 1) as cust_user_id')
	                          );

			$query = DB::table('gds_invoice_items as invproducts')->select($fields);
	        $query->join('gds_order_products as product', 'product.product_id', '=', 'invproducts.product_id');
	        $query->join('gds_order_invoice as invoice', 'invproducts.gds_order_invoice_id', '=', 'invoice.gds_order_invoice_id');
	    	$query->join('gds_invoice_grid as grid', 'grid.gds_invoice_grid_id', '=', 'invoice.gds_invoice_grid_id');
                $query->join('gds_orders as orders', 'grid.gds_order_id', '=', 'orders.gds_order_id');
			$query->where('invproducts.gds_order_id', $orderId);
			$query->where('product.gds_order_id', $orderId);
			$query->orderBy('product.pname', 'asc');
			//$query->orderBy('sort_parent_id', 'asc');
			//$query->orderBy('invCfc', 'asc');
	        return $query->get()->all();
			//echo $query->toSql();die;
        } 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }

    public function getCFCPackConfig($productId,$uom='') {
    	
		try{
			$query = DB::table('product_pack_config as ppc')->select(array('ppc.no_of_eaches'));
			$query->where('ppc.product_id', $productId);

			if(!empty($uom)){
				$query->where('ppc.level', $uom);
			}
			$query->where('ppc.is_sellable', 1);
			$query->orderBy('ppc.level', 'DESC');
			return $query->first();
		} 
        catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}				
    }

    public function getTaxPercentage($orderId, $productId){
	    $query = DB::table('gds_orders_tax as gdstax')->select(array('gdstax.tax as tax_percentage', 'gdstax.SGST', 'gdstax.CGST', 'gdstax.IGST', 'gdstax.UTGST'));
	    $query->where('gdstax.gds_order_id', $orderId);
	    $query->where('gdstax.product_id', $productId);	    
		return $query->first();
	}

    public function generateInvoiceByOrderId($orderId, $shipGridId=0, $isChangeStatus=0, $comment='') {
  		//DB::beginTransaction();//commented by Nishanth
  		try {
  			$_orderController = new OrdersController();
  			$objShipmentModel = new Shipment();
  			$_orderModel = new OrderModel();

	    	if(!$shipGridId) {
	    		$shipInfo = $objShipmentModel->verifyShipmentByOrderId($orderId, array('grid.gds_ship_grid_id'));
	            $shipGridId = isset($shipInfo->gds_ship_grid_id) ? $shipInfo->gds_ship_grid_id : 0;
	    	}

	    	$shippedArr = $objShipmentModel->getShipmentQtyWithProductById($shipGridId);
			if(is_array($shippedArr) && count($shippedArr) > 0) {
				
				$grandTotal = $baseGrandTotal = $shippingTaxAmount = $totalQty = $taxAmount = $shippingAmount = $subTotal = $discountAmount = $totSGSTVal = $totCGSTVal = $totIGSTVal = $totUTGSTVal = 0;
					
				$itemsArr = array();
				$invoice_status = '54002';
				$billing_name = '';
				$inventory_err_flag = 0;
				$orderDetail = $_orderModel->getOrderDetailById($orderId);
  				$le_wh_id = isset($orderDetail->le_wh_id) ? (int)$orderDetail->le_wh_id : 0;
  				$productInvoiceArray = [];
  				$statusArr=array();
  				$cancelgriditems="SELECT  `goc`.`product_id`,SUM(goc.qty) AS cancelQty FROM `gds_cancel_grid` AS `grid` LEFT JOIN `gds_order_cancel` AS `goc` ON `grid`.`cancel_grid_id` = `goc`.`cancel_grid_id` WHERE `grid`.`gds_order_id` = $orderId  GROUP BY `goc`.`product_id`";
                $cancelgridqty = DB::selectFromWriteConnection($cancelgriditems);
  				$orderprodctsbyid=$objShipmentModel->getOrderProductById($orderId);
  				foreach ($cancelgridqty as $key => $value) {
  					if($cancelgridqty[$key]->cancelQty==$orderprodctsbyid[$cancelgridqty[$key]->product_id]){
  						$_orderModel->updateProductStatus($orderId, $cancelgridqty[$key]->product_id, 17015);
  					}
  				}
				foreach($shippedArr as $productId=>$qty) {
					$line_item_comment = '';
					$cancelqty="SELECT  `goc`.`product_id`,SUM(goc.qty) AS cancelQty FROM `gds_cancel_grid` AS `grid` LEFT JOIN `gds_order_cancel` AS `goc` ON `grid`.`cancel_grid_id` = `goc`.`cancel_grid_id` WHERE `grid`.`gds_order_id` = $orderId AND `goc`.`product_id` = $productId GROUP BY `goc`.`product_id`";
                    $cancelqty = DB::selectFromWriteConnection($cancelqty);
                    $cancelqty = isset($cancelqty[0]->cancelQty)?$cancelqty[0]->cancelQty:0;
					$product = $_orderModel->getProductByOrderIdProductId($orderId, $productId);

					$pname = isset($product->pname) ? $product->pname : '';
					$pname = str_replace(array('"', "'"), '', $pname);
                    $invArr = $_orderModel->getInventory($productId, $le_wh_id);
					$soh = isset($invArr->soh) ? (int)$invArr->soh : 0;
					if(($soh > 0 && ($qty > $soh)) || $soh <= 0) {
						$inventory_err_flag = 1;
						$errorInvArray[] = $product->sku;
					}
					$billing_name = isset($product->shop_name) ? $product->shop_name : '';

					$tax_per_object = $this->getTaxPercentage($orderId, $productId);
					//$tax_per_object = $_orderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
					$tax_per = isset($tax_per_object->tax_percentage) ? $tax_per_object->tax_percentage : 0;

					$sgstPer = isset($tax_per_object->SGST) ? $tax_per_object->SGST : 0;
					$cgstPer = isset($tax_per_object->CGST) ? $tax_per_object->CGST : 0;
					$igstPer = isset($tax_per_object->IGST) ? $tax_per_object->IGST : 0;
					$utgstPer = isset($tax_per_object->UTGST) ? $tax_per_object->UTGST : 0;

					//get tax percentage
					$singleUnitPrice = (($product->total / (100+$tax_per)*100) / $product->qty);

					if($cancelqty>0){
                		$data = DB::selectFromWriteConnection(DB::raw("select p.esu,pc.no_of_eaches,p.price FROM products_slab_rates p JOIN product_pack_config pc  ON p.`product_id` = pc.product_id AND p.`pack_type`=pc.`level`  AND CURDATE() BETWEEN start_date AND end_date WHERE wh_id IN (".$le_wh_id. ") and p.product_id=".$productId." order by pc.no_of_eaches desc"));
//                		log::info($data);
                		if(count($data)>0){
                			$slabapplied=0;
                			for($i=0;$i<count($data);$i++){
                				if($slabapplied==0){
			                		if(($data[$i]->esu*$data[$i]->no_of_eaches)<=$qty){
			                			//log::info('if');
				              			$slabapplied=1;
			                			$singleUnitPrice=($data[$i]->price*100)/(100+$tax_per);
			                		    //log::info('singleUnitPrice   '.$singleUnitPrice);
			                		}else{
			                			if($i==0){
			                				$actual_esp = isset($product->actual_esp) ? $product->actual_esp : 0;
				                		//	log::info('else');
				                		//	log::info($actual_esp);
				                		//	log::info($tax_per);
				                			$singleUnitPrice=($actual_esp*100)/(100+$tax_per);
		                    			//	log::info('singleUnitPrice   '.$singleUnitPrice);
					                	}
				                	}
				                }else{
				                	break;
				                }
			                }
		                }                    	
                    }

					$net_value = ($singleUnitPrice * $qty);

					$singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;

					$tax_amount = (($singleUnitPrice * $tax_per) / 100 ) * $qty;

					$SGSTVal = ( $tax_amount * $sgstPer ) / 100;
					$CGSTVal = ( $tax_amount * $cgstPer ) / 100;
					$IGSTVal = ( $tax_amount * $igstPer ) / 100;
					$UTGSTVal = ( $tax_amount * $utgstPer ) / 100;

					$totSGSTVal = $totSGSTVal + $SGSTVal;
					$totCGSTVal = $totCGSTVal + $CGSTVal;
					$totIGSTVal = $totIGSTVal + $IGSTVal;
					$totUTGSTVal = $totUTGSTVal + $UTGSTVal;

					$rowTotal = $net_value;
					
					$rowTotalInclTax = ($tax_amount + $net_value);

					$taxAmount = $taxAmount + $tax_amount;

					$grandTotal = $grandTotal + $rowTotalInclTax; 
					$totalQty = $totalQty + $qty;

					$subTotal = $subTotal + $net_value;

					$baseGrandTotal = $baseGrandTotal + $net_value;
					
					$key = $orderId.'-'.$productId;
					$discount_amount = 0;
					$potosoorderproductuom= $this->getPackLevelFromPOByOrderIdProductId($orderDetail->order_code,$productId);
					$packConfig = $this->getCFCPackConfig($productId,$potosoorderproductuom);
					$no_of_eaches = isset($packConfig->no_of_eaches) ? $packConfig->no_of_eaches : 0;

					$itemsArr[$key] = array('gds_order_invoice_id'=>'',
											'gds_order_id'=>$orderId,
											'product_id'=>$productId,
											'qty'=>$qty,
											'price'=>$singleUnitPrice,
											'price_incl_tax'=>$singleUnitPriceWithtax,
											'base_cost'=>$singleUnitPrice,
											'tax_amount'=>$tax_amount,
											'discount_amount'=>$discount_amount,
											'row_total'=>$rowTotal,
											'row_total_incl_tax'=>$rowTotalInclTax,
											'SGST'=>$SGSTVal,
											'CGST'=>$CGSTVal,
											'IGST'=>$IGSTVal,
											'UTGST'=>$UTGSTVal,
											'invoice_status'=>$invoice_status,
											'comments'=>$line_item_comment,
											'created_by'=>Session('userId'),
											'eaches_in_cfc'=>$no_of_eaches,
											'created_at'=>(string)Date('Y-m-d H:i:s'));

					/**
					 * Update order product status
					 */
					//$item_status = ($product->qty == $qty) ? '17021' : '17013';
					if($product->qty==$qty){
						$item_status=17021;
						//log::info('equal qty');
						$statusArr[$orderId]['17201']='17021';
					}elseif($product->qty==$cancelqty){
						$item_status=17015;
						$statusArr[$orderId]['17015']='17015';
						//log::info('cancel qty');
					}else{
						$item_status=17013;
						$statusArr[$orderId]['17021']='17013';
						//log::info('processing qty');
					}
					$productInvoiceArray[][$productId] = $singleUnitPriceWithtax * $qty;
					$_orderModel->updateProductStatus($orderId, $productId, $item_status);
				}
				//print_r($itemsArr);die;
				/**
				 * save data in invoice grid, order invoice and invoice item
				 */

				if($inventory_err_flag == 1){
					$msg = Lang::get('salesorders.alertInventory');
					$msg = str_replace('{SKU}', implode(', ', $errorInvArray), $msg);
					return array('Status'=>403, 'order_code'=>$orderDetail->order_code, 'Message'=>$msg);
				}
				$wh_state_id = isset($orderDetail->state)?$orderDetail->state:4033;
				$_cusRepo = new CustomerRepo();
				$le_id = $orderDetail->legal_entity_id;
				//$invoiceCode = $_cusRepo->getRefCode('IV',$wh_state_id);
				$inv_code_genr = $_cusRepo->getInvCode($wh_state_id,$le_id,'IV');
				$invoiceCode = $inv_code_genr[0]->invoice_code;
                $cust_user_id = $orderDetail->cust_user_id;
		
                $is_self = isset($orderDetail->is_self) ? $orderDetail->is_self : 0;
                $order_date = isset($orderDetail->order_date) ? $orderDetail->order_date : date('Y-m-d H:i:s');                
                $ord_discount_amt = isset($orderDetail->discount_amt) ? $orderDetail->discount_amt : 0;
                $custdic = $this->getCustomerDiscounts($order_date, $grandTotal,'order');
                $bill_discount = 0;
                $bill_discount_type = '';
                $bill_discount_amt = 0;
                if($is_self=='1' && $ord_discount_amt>0){
                    $bill_discount = isset($custdic->discount) ? $custdic->discount : 0;
                    $bill_discount_type = isset($custdic->discount_type) ? $custdic->discount_type : '';
                    $bill_discount_amt = 0;
                    if($bill_discount_type=='percentage'){
                        $bill_discount_amt = ($grandTotal * $bill_discount)/100;
                    }else{
                        $bill_discount_amt = $bill_discount;
                    }
                    if($grandTotal>$bill_discount_amt){
                        $grandTotal = ($grandTotal - $bill_discount_amt);
                    }else{
                        $bill_discount_amt = 0;
                    }
                }
                $paymentModel = new PaymentModel();
                $userEcash = $paymentModel->getUserEcash($cust_user_id);
                $cashback =isset($userEcash->cashback)?$userEcash->cashback:0;
                $applied_cashback =isset($userEcash->applied_cashback)?$userEcash->applied_cashback:0;
                
                $creditlimit =isset($userEcash->creditlimit)?$userEcash->creditlimit:0;
                // checking order is from stockist to ebutor
                $this->_poModel = new PurchaseOrder();
                $is_Stockist = $this->_poModel->checkStockist($orderDetail->cust_le_id);

                $avalbleEcash = ($is_Stockist > 0 )?$creditlimit:0;
                $avalbleEcash += $cashback - $applied_cashback;
                
                if($grandTotal>=$avalbleEcash){
                    $appliedEcash = ($avalbleEcash>=1)?$avalbleEcash:0;
                }else{
                    $appliedEcash = $grandTotal;
                }
                if($is_Stockist == 0){
	                //get order cashback after cancellations
	                $cashback_amount = $this->recalculateEcashAtInvoice($orderDetail,$productInvoiceArray);

	                if($avalbleEcash>=$cashback_amount){
	        			$appliedEcash = $cashback_amount;
	        		}
	            }
               
                //Log::info("invoice ecash applied cust_user_id===".$cust_user_id." avlbl cash==". $avalbleEcash);
                $appliedEcash = ($is_Stockist > 0 )?$appliedEcash:$appliedEcash;
                if($avalbleEcash>0){
                    $eCash = ['applied_cashback'=> DB::raw('(applied_cashback+' . $appliedEcash . ')')];
                    $paymentModel->updateEcash($cust_user_id, $eCash);
                }
				$gridDataArr = array('invoice_code'=>(string)$invoiceCode,
									'grand_total'=>$grandTotal,
									'ecash_applied'=>$appliedEcash,
									'sgst_total'=>$totSGSTVal,
									'cgst_total'=>$totCGSTVal,
									'igst_total'=>$totIGSTVal,
									'utgst_total'=>$totUTGSTVal, 
									'gds_order_id'=>$orderId,
								'billing_name'=>$billing_name, 'invoice_status'=>$invoice_status,
								'created_by'=>Session('userId'),'created_at'=>Date('Y-m-d H:i:s'), 'invoice_qty'=>$totalQty);
				$invoiceGridId = $_orderModel->invoiceGrid($gridDataArr);
				
				if($invoiceGridId) {
					$invoiceDataArr = array('gds_invoice_grid_id'=>$invoiceGridId,
											'base_grand_total'=>$baseGrandTotal,
											'shipping_tax_amount'=>$shippingTaxAmount,
											'tax_amount'=>$taxAmount,
											'grand_total'=>$grandTotal,
											'shipping_amount'=>$shippingAmount,
											'total_qty'=>$totalQty,
											'subtotal'=>$subTotal,
					                        //'discount_amount'=>$discountAmount,
					                        'discount' => $bill_discount,
					                        'discount_amt' => $bill_discount_amt,
					                        'discount_type' => $bill_discount_type,
											'status'=>$invoice_status,
											'created_by'=>Session('userId'),
											'created_at'=>Date('Y-m-d H:i:s'));
					$invoiceId = $_orderModel->gdsOrderInvoice($invoiceDataArr);

					if($invoiceId) {
						foreach($shippedArr as $productId=>$qty) {
							$key = $orderId.'-'.$productId;
							$itemsArr[$key]['gds_order_invoice_id'] = $invoiceId;						
						}
						//print_r($itemsArr);die;
						$_orderModel->insertBulkInvoiceGridItems($itemsArr);
					}

					$_orderController->saveOutputTax($invoiceGridId);
					$_orderController->saveStockOutward($invoiceGridId);
					$this->saveSalesVoucher($invoiceGridId);
				}
				//log::info('sts==============='.$isChangeStatus);
				if($isChangeStatus) {
					//echo '<pre/>';print_r($statusArr[$orderId]);exit;
						if(isset($statusArr[$orderId]) && count($statusArr[$orderId]) == 1) {
							$statusCodeArr = array_values($statusArr[$orderId]);
							$statuscode = isset($statusCodeArr[0]) ? $statusCodeArr[0] : '';
						}elseif((isset($statusArr[$orderId]) && array_key_exists('17013', $statusArr[$orderId])) || (isset($statusArr[$orderId]) && (array_key_exists('17021', $statusArr[$orderId]) && array_key_exists('17015', $statusArr[$orderId])))){
							$statuscode = '17021';
						}else{
							$statuscode = '17013';
						}
						if($statuscode=='17013'){
							$statuscode='17021';
						}
						//log::info('sts==============='.$statuscode);
					$_orderModel->updateOrderStatusById($orderId, $statuscode);
					$_orderController->saveComment($orderId, 'Order Status', array('comment'=>$comment, 'order_status_id'=>$statuscode));	
				}
				//Log::info("invoice completed==".$orderId);
                //$_orderModel->updateTrackBulkshipment($orderId,$postData['representative_name']);	
                
                //DB::commit();//commented by Nishanth
  				return $invoiceGridId;
			}
  		}catch (Exception $e) {
    		//DB::rollback();//commented by Nishanth
    		Log::info("invoice orderid==".$orderId);
        	Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        	return false;
		}
  	}
    /*
     * getCustomerDiscounts is a method to get the customer discount info
     */
    public function getCustomerDiscounts($date, $value,$disc_on='order') {
        /*$query = DB::table('customer_discounts as cd');
                $query->select('cd.discount_type', 'cd.discount');
                $query->where('cd.discount_on_values','<=', $value);
                $query->where('cd.discount_start_date','<=', $date);
                $query->where('cd.discount_end_date','>=', $date);
                $query->where('cd.discount_on', $disc_on);
                $query->orderby('cd.discount_on_values','desc');
        $result = $query->first();*/
        $query = "SELECT `discount_type`, `discount_on`, `discount` FROM `customer_discounts` WHERE `discount_start_date` <= '".$date."' AND `discount_end_date` >= '".$date."' AND `discount_on_values` <= $value AND `discount_on` = '".$disc_on."' ORDER BY `discount_on_values` DESC LIMIT 1";
        $data = DB::select($query);
        $result = isset($data[0])?$data[0]:$data;
       return $result;
    }


    /*
    * Function name: generateOpenOrderInvoice
    * Description: used to Create Invoice for Open Order
    * Author: Raju.A
    * Copyright: ebutor 2018
    * Version: v1.0
    * Created Date: 20th FEB 2018 :/
    * Modified Date & Reason:
    */
    public function generateOpenOrderInvoice($orderId, $isChangeStatus = 0, $comment = '') {
        $_orderModel = new OrderModel();
    	$orderDetail = $_orderModel->getOrderDetailById($orderId);
        $wh_state_id = isset($orderDetail->state)?$orderDetail->state:4033;
        $le_id = $orderDetail->legal_entity_id;
		$_cusRepo = new CustomerRepo();
		//$invoiceCode = $_cusRepo->getRefCode('IV',$wh_state_id);
		$inv_code_genr = $_cusRepo->getInvCode($wh_state_id,$le_id,'IV');
		$invoiceCode = $inv_code_genr[0]->invoice_code;
        DB::beginTransaction();
        try {
            $_orderController = new OrdersController();
            $objShipmentModel = new Shipment();

            /* if (!$shipGridId) {
                $shipInfo = $objShipmentModel->verifyShipmentByOrderId($orderId, array('grid.gds_ship_grid_id'));
                $shipGridId = isset($shipInfo->gds_ship_grid_id) ? $shipInfo->gds_ship_grid_id : 0;
            } */

            //$shippedArr = $objShipmentModel->getShipmentQtyWithProductById($shipGridId);
            $shippedArr = $objShipmentModel->getOrderProductById($orderId);
            $le_wh_id = $orderDetail->le_wh_id;
            if (is_array($shippedArr) && count($shippedArr) > 0) {

                $grandTotal = $baseGrandTotal = $shippingTaxAmount = $totalQty = $taxAmount = $shippingAmount = $subTotal = $discountAmount = $totSGSTVal = $totCGSTVal = $totIGSTVal = $totUTGSTVal = 0;

                $itemsArr = array();
                $invoice_status = '54002';
                $billing_name = '';
                $errorInvArray['order_code'] = $orderDetail->order_code;
                $errorInvArray['status_type'] = 'inventory_error';
                $errorInvArray['inv_html'] = "";
                $inventoryData = "";
                $productInvoiceArray = [];
                foreach ($shippedArr as $productId => $qty) {
					$line_item_comment = '';
                    //checking cancel qty before invoice
                    $cancelqty="SELECT  `goc`.`product_id`,SUM(goc.qty) AS cancelQty FROM `gds_cancel_grid` AS `grid` LEFT JOIN `gds_order_cancel` AS `goc` ON `grid`.`cancel_grid_id` = `goc`.`cancel_grid_id` WHERE `grid`.`gds_order_id` = $orderId AND `goc`.`product_id` = $productId GROUP BY `goc`.`product_id`";
                    $cancelqty = DB::select($cancelqty);
                    $cancelqty = isset($cancelqty[0]->cancelQty)?$cancelqty[0]->cancelQty:0;
                    //cancel qty removed before order invoice
                    $orderqty=$qty;
                    $qty = $qty - $cancelqty;
                    $product = $_orderModel->getProductByOrderIdProductId($orderId, $productId);
                    $pname = isset($product->pname) ? $product->pname : '';
					$pname = str_replace(array('"', "'"), '', $pname);
                    $invArr = $_orderModel->getInventory($productId, $le_wh_id);
					$soh = isset($invArr->soh) ? (int)$invArr->soh : 0;
					if($soh > 0 && ($qty > $soh) && $qty > 0) {
						$errorInvArray['inv_html'] .= '<tr class="subhead priceerrorname">
                                                <td align="left" valign="middle"><b>'.$pname.' <span style="color:blue"><b>('.$product->sku.')</b></span></b></td>
                                                <td style="color:red" align="left" valign="middle">'.$qty.'</td>
                                                <td align="left" valign="middle">Less than available inventory.</td>
                                                    </tr>';
					}else if($soh <= 0 && $qty > 0){
						$errorInvArray['inv_html'] .= '<tr class="subhead priceerrorname">
                                                <td align="left" valign="middle"><b>'.$pname.' <span style="color:blue"><b>('.$product->sku.')</b></span></b></td>
                                                <td style="color:red" align="left" valign="middle">'.$qty.'</td>
                                                <td align="left" valign="middle">Zero inventory.</td>
                                                    </tr>';
					}
                    $billing_name = isset($product->shop_name) ? $product->shop_name : '';

                    $tax_per_object = $this->getTaxPercentage($orderId, $productId);
                    //$tax_per_object = $_orderModel->getTaxPercentageOnGdsProductId($product->gds_order_prod_id);
                    $tax_per = isset($tax_per_object->tax_percentage) ? $tax_per_object->tax_percentage : 0;

                    $sgstPer = isset($tax_per_object->SGST) ? $tax_per_object->SGST : 0;
                    $cgstPer = isset($tax_per_object->CGST) ? $tax_per_object->CGST : 0;
                    $igstPer = isset($tax_per_object->IGST) ? $tax_per_object->IGST : 0;
                    $utgstPer = isset($tax_per_object->UTGST) ? $tax_per_object->UTGST : 0;
                    $singleUnitPrice = (($product->total / (100 + $tax_per) * 100) / $product->qty);
                    if($orderqty>$qty){
                		$data = DB::select(DB::raw("select p.esu,pc.no_of_eaches,p.price FROM products_slab_rates p JOIN product_pack_config pc  ON p.`product_id` = pc.product_id AND p.`pack_type`=pc.`level`  AND CURDATE() BETWEEN start_date AND end_date WHERE wh_id IN (".$le_wh_id. ") and p.product_id=".$productId." order by pc.no_of_eaches desc"));
                		//log::info($data);
                		if(count($data)>0){
                			$slabapplied=0;
                			for($i=0;$i<count($data);$i++){
                				if($slabapplied==0){
			                		if(($data[$i]->esu*$data[$i]->no_of_eaches)<=$qty){
			                			//log::info('if');
				              			$slabapplied=1;
			                			$singleUnitPrice=($data[$i]->price*100)/(100+$tax_per);
			                		    //log::info('singleUnitPrice   '.$singleUnitPrice);
			                		}else{
			                			if($i==0){
			                				$actual_esp = isset($product->actual_esp) ? $product->actual_esp : 0;
				                		//	log::info('else');
				                		//	log::info($actual_esp);
				                		//	log::info($tax_per);
				                			$singleUnitPrice=($actual_esp*100)/(100+$tax_per);
		                    			//	log::info('singleUnitPrice   '.$singleUnitPrice);
					                	}
				                	}
				                }else{
				                	break;
				                }
			                }
		                }                    	
                    }

                    //get tax percentage
                    //$singleUnitPrice = (($product->total / (100 + $tax_per) * 100) / $product->qty);
                    $net_value = ($singleUnitPrice * $qty);

                    $singleUnitPriceWithtax = (($tax_per / 100) * $singleUnitPrice) + $singleUnitPrice;

                    $tax_amount = (($singleUnitPrice * $tax_per) / 100 ) * $qty;

                    $SGSTVal = ( $tax_amount * $sgstPer ) / 100;
                    $CGSTVal = ( $tax_amount * $cgstPer ) / 100;
                    $IGSTVal = ( $tax_amount * $igstPer ) / 100;
                    $UTGSTVal = ( $tax_amount * $utgstPer ) / 100;

                    $totSGSTVal = $totSGSTVal + $SGSTVal;
                    $totCGSTVal = $totCGSTVal + $CGSTVal;
                    $totIGSTVal = $totIGSTVal + $IGSTVal;
                    $totUTGSTVal = $totUTGSTVal + $UTGSTVal;

					$rowTotal = $net_value;

                    $rowTotalInclTax = ($tax_amount + $net_value);

                    $taxAmount = $taxAmount + $tax_amount;

                    $grandTotal = $grandTotal + $rowTotalInclTax;
                    $totalQty = $totalQty + $qty;

                    $subTotal = $subTotal + $net_value;

                    $baseGrandTotal = $baseGrandTotal + $net_value;

                    $key = $orderId . '-' . $productId;
					$discount_amount = 0;
					$potosoorderproductuom= $this->getPackLevelFromPOByOrderIdProductId($orderDetail->order_code,$productId);
                    $packConfig = $this->getCFCPackConfig($productId,$potosoorderproductuom);
                    $no_of_eaches = isset($packConfig->no_of_eaches) ? $packConfig->no_of_eaches : 0;

                    $itemsArr[$key] = array('gds_order_invoice_id' => '',
                        'gds_order_id' => $orderId,
                        'product_id' => $productId,
                        'qty' => $qty,
                        'price' => $singleUnitPrice,
                        'price_incl_tax' => $singleUnitPriceWithtax,
                        'base_cost' => $singleUnitPrice,
                        'tax_amount' => $tax_amount,
											'discount_amount'=>$discount_amount,
                        'row_total' => $rowTotal,
                        'row_total_incl_tax' => $rowTotalInclTax,
                        'SGST' => $SGSTVal,
                        'CGST' => $CGSTVal,
                        'IGST' => $IGSTVal,
                        'UTGST' => $UTGSTVal,
                        'invoice_status' => $invoice_status,
                        'comments' => $line_item_comment,
                        'created_by' => Session('userId'),
                        'eaches_in_cfc' => $no_of_eaches,
                        'created_at' => (string) Date('Y-m-d H:i:s'));

                    /**
                     * Update order product status
                     */
                   

                    $item_status = ($product->qty == $qty) ? '17021' : '17013' ;
                    if($qty == 0){
                    	$item_status = $product->order_status;
					}
                    $productInvoiceArray[][$productId] = $singleUnitPriceWithtax * $qty;
					$_orderModel->updateProductStatus($orderId, $productId, $item_status);
                }
               	
               	// check inventory before invoice 
               	if(is_array($errorInvArray) && count($errorInvArray) > 0 && $errorInvArray['inv_html'] != "") {

	  				return array('status' => 404, 'message' => $errorInvArray); 
	  			}
                //print_r($itemsArr);die;
                /**
                 * save data in invoice grid, order invoice and invoice item
                 */
                
                $cust_user_id = $orderDetail->cust_user_id;
                
                $is_self = isset($orderDetail->is_self) ? $orderDetail->is_self : 0;
                $order_date = isset($orderDetail->order_date) ? $orderDetail->order_date : date('Y-m-d H:i:s');                
                $ord_discount_amt = isset($orderDetail->discount_amt) ? $orderDetail->discount_amt : 0;
                $custdic = $this->getCustomerDiscounts($order_date, $grandTotal,'order');
                $bill_discount = 0;
                $bill_discount_type = '';
                $bill_discount_amt = 0;
                if($is_self=='1' && $ord_discount_amt>0){
                    $bill_discount = isset($custdic->discount) ? $custdic->discount : 0;
                    $bill_discount_type = isset($custdic->discount_type) ? $custdic->discount_type : '';
                    $bill_discount_amt = 0;
                    if($bill_discount_type=='percentage'){
                        $bill_discount_amt = ($grandTotal * $bill_discount)/100;
                    }else{
                        $bill_discount_amt = $bill_discount;
                    }
                    if($grandTotal>$bill_discount_amt){
                        $grandTotal = ($grandTotal - $bill_discount_amt);
                    }else{
                        $bill_discount_amt = 0;
                    }
                }
                
                        $paymentModel = new PaymentModel();
                        $userEcash = $paymentModel->getUserEcash($cust_user_id);
                        $cashback =isset($userEcash->cashback)?$userEcash->cashback:0;
                        $applied_cashback =isset($userEcash->applied_cashback)?$userEcash->applied_cashback:0;
                        $creditlimit =isset($userEcash->creditlimit)?$userEcash->creditlimit:0;
                        // checking order is from stockist to ebutor
                        $this->_poModel = new PurchaseOrder();
                		$is_Stockist = $this->_poModel->checkStockist($orderDetail->cust_le_id);
                        $avalbleEcash = ($is_Stockist > 0 )?$creditlimit:0;
                        $avalbleEcash += $cashback - $applied_cashback;
                        if($grandTotal>=$avalbleEcash){
                            // checking cashback and customer is stockist or not
                            if ($is_Stockist > 0) {
                            	$credit_limit_check = $this->_poModel->getLePayments($orderDetail->cust_le_id);
                				$credit_limit_check = isset($credit_limit_check->credit_limit_check)?$credit_limit_check->credit_limit_check:0;
                                //Log::info('order_code'.$orderDetail->order_code.'Insufficient wallet balance to invoice stockist order');
                                if($credit_limit_check == 1  && $grandTotal > $avalbleEcash){
                                	return array('status'=>400,'order_code' => $orderDetail->order_code, 'message' => 'Insufficient wallet balance to invoice stockist order '.$orderDetail->order_code);
                                }else{
                                	$appliedEcash = $grandTotal;
                                }
                            }else{
                        		$appliedEcash = ($avalbleEcash>=1)?$avalbleEcash:0;
                            }
                        }else{
                            $appliedEcash = $grandTotal;
                        }
                        if($is_Stockist == 0){
	                        //get order cashback after cancellations
			                $cashback_amount = $this->recalculateEcashAtInvoice($orderDetail,$productInvoiceArray);

			                if($avalbleEcash>=$cashback_amount){
			        			$appliedEcash = $cashback_amount;
			        		}
			            }						

                        //Log::info("open invoice ecash applied cust_user_id===".$cust_user_id." avlbl cash==". $avalbleEcash);
                        $appliedEcash = ($is_Stockist > 0 )?$appliedEcash:$appliedEcash;
                        if($avalbleEcash>0){
                            $eCash = ['applied_cashback'=> DB::raw('(applied_cashback+' . $appliedEcash . ')')];
                            $paymentModel->updateEcash($cust_user_id, $eCash);
                        }
				$gridDataArr = array('invoice_code'=>(string)$invoiceCode,
									'grand_total'=>$grandTotal,
                  					'ecash_applied'=>$appliedEcash,
									'sgst_total'=>$totSGSTVal,
									'cgst_total'=>$totCGSTVal,
									'igst_total'=>$totIGSTVal,
									'utgst_total'=>$totUTGSTVal, 
									'gds_order_id'=>$orderId,
								'billing_name'=>$billing_name, 'invoice_status'=>$invoice_status,
								'created_by'=>Session('userId'),'created_at'=>Date('Y-m-d H:i:s'), 'invoice_qty'=>$totalQty);
				$invoiceGridId = $_orderModel->invoiceGrid($gridDataArr);
				
				if($invoiceGridId) {
					$invoiceDataArr = array('gds_invoice_grid_id'=>$invoiceGridId,
											'base_grand_total'=>$baseGrandTotal,
											'shipping_tax_amount'=>$shippingTaxAmount,
											'tax_amount'=>$taxAmount,
											'grand_total'=>$grandTotal,
											'shipping_amount'=>$shippingAmount,
											'total_qty'=>$totalQty,
											'subtotal'=>$subTotal,
                        //'discount_amount'=>$discountAmount,
                        'discount' => $bill_discount,
                        'discount_amt' => $bill_discount_amt,
                        'discount_type' => $bill_discount_type,
											'status'=>$invoice_status,
											'created_by'=>Session('userId'),
											'created_at'=>Date('Y-m-d H:i:s'));
					$invoiceId = $_orderModel->gdsOrderInvoice($invoiceDataArr);

					if($invoiceId) {
						foreach($shippedArr as $productId=>$qty) {
							$key = $orderId.'-'.$productId;
							$itemsArr[$key]['gds_order_invoice_id'] = $invoiceId;						
						}
						//print_r($itemsArr);die;
						$_orderModel->insertBulkInvoiceGridItems($itemsArr);
						//sending notifaication/sms/email for so
				        // $po_id = $this->_poModel->getPoIdBySOCode($orderDetail->order_code);
				        // if($po_id > 0 && $po_id !="" ){
				        // 	$this->sendNotifyOnInvoice($orderId,'AUTOSMSNOTIFY01',$po_id);
				        // }

					}

					$trackDetails = $_orderModel->getGdsTrackDetail($orderId);

					if(empty($trackDetails)) {

						$track_details = array( 'gds_order_id'=>$orderId,
												'gds_order_code'=>$orderDetail->order_code,
												'invoice_order_no'=>$invoiceGridId,
												'invoice_order_code'=>$invoiceCode,
												'created_by' => Session('userId'),
												'created_at' => Date('Y-m-d H:i:s')
												);

						DB::table('gds_order_track')->insert($track_details);
					} else {

						$track_details = array( 'invoice_order_no'=>$invoiceGridId,
												'invoice_order_code'=>$invoiceCode
												);

						DB::table('gds_order_track')->where('gds_order_id',$orderId)->update($track_details);
					}

					$_orderController->saveOutputTax($invoiceGridId);
					$_orderController->saveStockOutward($invoiceGridId);
					$this->saveSalesVoucher($invoiceGridId);
				}

				if($isChangeStatus) {
					$_orderModel->updateOrderStatusById($orderId, '17021');
					$_orderController->saveComment($orderId, 'Order Status', array('comment'=>$comment, 'order_status_id'=>'17021'));	
				}
				
                //$_orderModel->updateTrackBulkshipment($orderId,$postData['representative_name']);	
                
                DB::commit();
  				return $invoiceGridId;
            }
        } catch (Exception $e) {
       		Log::info("failed in generateOpenOrderInvoice auto_invoice mode");
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }

    public function sendNotifyOnInvoice($order_id,$notification_code,$params){
        $this->queue = new Queue();
        $args = array("ConsoleClass" => 'autosmsnotify', 'arguments' => array('notification_code'=>$notification_code,'params'=>$params));
        Log::info(json_encode($args));
        $job = $this->queue->enqueue('default', 'ResqueJobRiver', $args);
        return true;
    }
    public function recalculateEcashAtInvoice($orderDetail,$productInvoiceArray){

    	$instant_cashback = isset($orderDetail->instant_wallet_cashback) ? $orderDetail->instant_wallet_cashback : 0;
        $cashback_amount = isset($orderDetail->cashback_amount) ? $orderDetail->cashback_amount : 0;
        $cust_user_id = $orderDetail->cust_user_id;

        $paymentModel = new PaymentModel();

        $is_self = isset($orderDetail->is_self) ? $orderDetail->is_self : 0;
        $order_date = isset($orderDetail->order_date) ? $orderDetail->order_date : date('Y-m-d H:i:s');
        $le_wh_id = $orderDetail->le_wh_id;
        $cust_le_id = $orderDetail->cust_le_id;
        
        $customer_type_id = $paymentModel->getCustomerType($cust_le_id);
        $orderId =$orderDetail->gds_order_id;

        if($instant_cashback==1){
        	if($cashback_amount==0){
        		$ecash_history = DB::table('ecash_transaction_history')->select('cash_back_amount')
        		->where('order_id',$orderId)
        		->where('transaction_type','143002')->first();
        		$cashback_amount = isset($ecash_history->cash_back_amount)?$ecash_history->cash_back_amount:0;
        	}
        	
        	
    		$master_lookup = new MasterLookupController();
            $ecashOrder = json_decode($master_lookup->getOrderEcashValue($productInvoiceArray,$order_date,$le_wh_id,$customer_type_id,$is_self,$cust_le_id));
            $new_cashback = 0;
	        if(isset($ecashOrder->data) && count($ecashOrder->data)){
	            if(isset($ecashOrder->data[0]->applyCashback) && $ecashOrder->data[0]->applyCashback){
	                $new_cashback = $ecashOrder->data[0]->cashback_applied;
	            }
	        }
	        if($cashback_amount!=$new_cashback){

	        	//cb - 10,new cb = 7
	        	$ecash_diff = ($new_cashback>$cashback_amount)?0:($cashback_amount - $new_cashback);


	        	if($ecash_diff>0){
	                $eCash = ['cashback'=> DB::raw('(cashback-' . $ecash_diff . ')')];
	                $paymentModel->updateEcash($cust_user_id, $eCash);
	                
	                $userEcash = $paymentModel->getUserEcash($cust_user_id);
        			$cashback =isset($userEcash->cashback)?$userEcash->cashback:0;

	                $comment = 'Cashback reduced due to full/partial cancel!';
	                $ecashHistory = ['user_id'=>$cust_user_id,
	                    'legal_entity_id'=>$cust_le_id,
	                    'order_id'=>$orderId,
	                    'delivered_amount'=>0,
	                    'cash_back_amount'=>$ecash_diff,
	                    'balance_amount'=>$cashback,
	                    'transaction_type'=>143001,
	                    'transaction_date'=>date('Y-m-d H:i:s'),
	                    'order_status_id'=>17018,
	                    'comment'=>$comment
	                    ];
	                $paymentModel->saveEcashHistory($ecashHistory);
	            }
	            if($cashback_amount>=$new_cashback){
	            	$cashback_amount = $new_cashback;
	            }
	        }
    	}
    	return $cashback_amount;
    }

    public function getPackLevelFromPOByOrderIdProductId($ordercode,$productid){

    	$getProductuom=DB::table('po')
    					->join('po_products as pp','po.po_id','=','pp.po_id')
    					->select('pp.uom as uom')
    					->where('po.po_so_order_code',$ordercode)
    					->where('pp.product_id',$productid)
    					->first();
		$getProductuom=isset($getProductuom->uom)?$getProductuom->uom:0;

		return $getProductuom;	
    }

}