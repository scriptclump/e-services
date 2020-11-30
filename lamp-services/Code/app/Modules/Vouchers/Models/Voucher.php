<?php

namespace App\Modules\Vouchers\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Orders\Models\GdsBusinessUnit;
use DB;

class Voucher extends Model
{
	public function saveSalesVoucher($invoiceId, $remarks='Sales Entry'){
        DB::beginTransaction();
        try{

                $_BusinessUnit = new GdsBusinessUnit();

                $fields = array('orders.order_code',
                                'orders.order_date',
                                'orders.hub_id',
                                'orders.le_wh_id',
                                'grid.created_at as invoice_date',
                                'le.business_legal_name',
                                'le.le_code',
                                'tax.tax',
                                'tax_class.tally_reference',
                                DB::raw('SUM(products.tax_amount) as taxSum'),
                                DB::raw('SUM(products.row_total) as saleTotal'),
                                'grid.invoice_code',
                                'grid.grand_total');


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
                $query->leftJoin('gds_orders_tax as tax','tax.gds_order_prod_id','=','gdsprod.gds_order_prod_id');
                $query->leftJoin('tax_classes as tax_class','tax_class.tax_class_id','=','tax.tax_class');
                $query->groupBy('tax.tax');
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
                $invoice_date = date('Y-m-d H:i:s');

                if(count($resArr)) {
                    foreach($resArr as $res) {
                        $invoice_code = (isset($res->invoice_code) && !empty($res->invoice_code)) ? $res->invoice_code : $invoiceId;
                
                        $tax = (float)$res->tax;
                        if($tax) {
                            
                            $tally_reference = json_decode($res->tally_reference);


                            $salesAcct = isset($tally_reference->SALES_CODE) ? $tally_reference->SALES_CODE : '';

                            $taxAcct = isset($tally_reference->IO_CODE) ? 'Output '.$tally_reference->IO_CODE : '';
                        
                            $invoice_date = $res->invoice_date;
                            
                            $withoutTaxArray[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>'Sales Accounts','ledger_account'=>$salesAcct,'tran_type'=>'Cr','amount'=>$res->saleTotal,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

                            $withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>'Duties & Taxes','ledger_account'=>$taxAcct,'tran_type'=>'Cr','amount'=>$res->taxSum,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                            $saleTotal = round($res->saleTotal,2);                          
                            $taxSum = round($res->taxSum,2);                            

                            $taxAmountSum = $taxAmountSum + $saleTotal + $taxSum;
                        }
                    }

                    $insertArray[]  = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$invoice_date,'ledger_group'=>'Sundry Debtors','ledger_account'=>$res->business_legal_name.' - '.$res->le_code,'tran_type'=>'Dr','amount'=>round($res->grand_total),'naration'=>'Being the sales made to '.$res->business_legal_name.' Order No. '.$res->order_code.' dated '.$res->order_date.' with invoice no '.$res->invoice_code.' dated '.$res->invoice_date,'cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                    $insertArray    = array_merge($insertArray,$withoutTaxArray);
                    $insertArray    = array_merge($insertArray,$withTaxArr);
                    DB::table('vouchers')->insert($insertArray);
                    $this->saveVoucherRoundoff($invoice_code, $remarks, $costCenter, $costCenterGroup);
                    DB::commit();
                    return true;
                }

        }
        catch(Exception $e){
            DB::rollback();
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


    public function saveGSTSalesVoucher($invoiceId, $remarks='Sales Entry'){
        DB::beginTransaction();
        try{

            
                $_BusinessUnit = new GdsBusinessUnit();


                $TallyRef       = array();
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
                                'grid.created_at as invoice_date',
                                'le.business_legal_name',
                                'le.le_code',
                                //'tax.tax',
                                //'tax_class.tally_reference',
                                DB::raw('SUM(products.row_total) as saleTotal'),
                                'grid.cgst_total',
                                'grid.sgst_total',
                                'grid.igst_total',
                                'grid.utgst_total',
                                'grid.invoice_code',
                                'grid.grand_total',
                        'invoice.discount_amt as bill_disc');

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
                //$query->leftJoin('gds_orders_tax as tax','tax.gds_order_prod_id','=','gdsprod.gds_order_prod_id');
                //$query->leftJoin('tax_classes as tax_class','tax_class.tax_class_id','=','tax.tax_class');
                $query->groupBy('invoice.gds_order_invoice_id');
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
                $invoice_date = date('Y-m-d H:i:s');

                if(count($resArr)) {
                
                        
                        $res = $resArr[0];

                        $invoice_code = (isset($res->invoice_code) && !empty($res->invoice_code)) ? $res->invoice_code : $invoiceId;


                        $salesAcct = isset($TallyRef['142001']) ? $TallyRef['142001']->master_lookup_name : '';
                        $taxAcct = isset($TallyRef['142003']) ? $TallyRef['142003']->master_lookup_name : '';


                        if($res->igst_total!=0) {

                            $salesAcct = isset($TallyRef['142002']) ? $TallyRef['142002']->master_lookup_name : '';

                            $taxAcct = isset($TallyRef['142004']) ? $TallyRef['142004']->master_lookup_name : '';

                            $withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142004']->description,'ledger_account'=>$TallyRef['142004']->master_lookup_name,'tran_type'=>'Cr','amount'=>$res->igst_total,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);


                        } 

                        if($res->utgst_total!=0) {

                            $salesAcct = isset($TallyRef['142002']) ? $TallyRef['142002']->master_lookup_name : '';
                            $taxAcct = isset($TallyRef['142004']) ? $TallyRef['142004']->master_lookup_name : '';

                            $withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142004']->description,'ledger_account'=>$TallyRef['142004']->master_lookup_name,'tran_type'=>'Cr','amount'=>$res->igst_total,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

                        } 

                        if($res->sgst_total!=0) {

                            $salesAcct = isset($TallyRef['142001']) ? $TallyRef['142001']->master_lookup_name : '';
                            $taxAcct = isset($TallyRef['142003']) ? $TallyRef['142003']->master_lookup_name : '';


                            $withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142003']->description,'ledger_account'=>$TallyRef['142003']->master_lookup_name,'tran_type'=>'Cr','amount'=>$res->sgst_total,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

                        } 

                         if($res->cgst_total!=0) {

                            $salesAcct = isset($TallyRef['142001']) ? $TallyRef['142001']->master_lookup_name : '';
                            $taxAcct = isset($TallyRef['142005']) ? $TallyRef['142005']->master_lookup_name : '';

                            $withTaxArr[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>$TallyRef['142005']->description,'ledger_account'=>$TallyRef['142005']->master_lookup_name,'tran_type'=>'Cr','amount'=>$res->cgst_total,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                        }



                        $withoutTaxArray[] = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$res->invoice_date,'ledger_group'=>'Sales Accounts','ledger_account'=>$salesAcct,'tran_type'=>'Cr','amount'=>$res->saleTotal,'naration'=>'0','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);

                    $insertArray[]  = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$invoice_date,'ledger_group'=>'Sundry Debtors','ledger_account'=>$res->business_legal_name.' - '.$res->le_code,'tran_type'=>'Dr','amount'=>round($res->grand_total),'naration'=>'Being the sales made to '.$res->business_legal_name.' Order No. '.$res->order_code.' dated '.$res->order_date.' with invoice no '.$res->invoice_code.' dated '.$res->invoice_date,'cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                    if(isset($res->bill_disc) && $res->bill_disc>0){
                            $disc_grp = isset($TallyRef['142013']->description)?$TallyRef['142013']->description:'';
                            $disc_act = isset($TallyRef['142013']->master_lookup_name)?$TallyRef['142013']->master_lookup_name:'';
                            $insertArray[]  = array('voucher_code'=>$invoice_code,'voucher_type'=>'Sales','voucher_date'=>$invoice_date,'ledger_group'=>$disc_grp,'ledger_account'=>$disc_act,'tran_type'=>'Dr','amount'=>round($res->bill_disc,2),'naration'=>'','cost_centre'=>$costCenter,'cost_centre_group'=>$costCenterGroup,'reference_no'=>$res->invoice_code,'is_posted'=>0,'Remarks'=>$remarks);
                        }
                    $insertArray    = array_merge($insertArray,$withoutTaxArray);
                    $insertArray    = array_merge($insertArray,$withTaxArr);
                    
                    DB::table('vouchers')->insert($insertArray);
                    $this->saveVoucherRoundoff($invoice_code, $remarks, $costCenter, $costCenterGroup);
                    DB::commit();
                    return true;
                }



        }
        catch(Exception $e){
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }
    


}
