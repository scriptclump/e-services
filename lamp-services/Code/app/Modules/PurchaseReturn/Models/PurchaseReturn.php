<?php

namespace App\Modules\PurchaseReturn\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Indent\Models\IndentModel;
use App\Modules\SerialNumber\Models\SerialNumber;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use Log;
use DB;
use Response;
use Session;
use Notifications;
use App\Modules\Indent\Models\LegalEntity;
use App\Modules\Grn\Models\Grn;
use App\Modules\Grn\Models\Inward;
use Mail;
use Utility;
use Lang;
use App\Central\Repositories\RoleRepo;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use App\Modules\Orders\Models\Inventory;
use App\Modules\Roles\Models\Role;
use App\Modules\Orders\Models\ReturnModel;
use App\Modules\Orders\Models\OrderModel;
use DateTime;

class PurchaseReturn extends Model {

    protected $table = "purchase_returns";
    protected $primaryKey = 'pr_id';
    protected $fillable = array( 'pr_id','pr_code','inward_id','pr_status','le_wh_id',
        'legal_entity_id',
        'sr_invoice_code',
        //'discount_type','discount','discount_amt',
        'pr_grand_total','pr_total_qty',
        'pr_remarks', 'created_by','approved_by','approved_at','approval_status');
    protected $_SNumberModel;

    /*
     * getAllPurchasedOrders() is used to get all purchased orders based on filters
     * @param $filter Array
     *
     * $filter = array('channel_id'=>5, 'po_status'=>33, 'fdate'=>'2016-01-01', 'tdate'=>'2016-05-09');
     *
     * @param $offset Integer, default 0
     * @param $perpage Integer, default 10
     *
     * @return Array
     */

    public function getAllPurchasedReturns($filter = array(), $rowCount = 0,$orderbyarray=array(), $offset = 0, $perpage = 10) {

        try {
            $userId = Session::get('userId');
            $roleObj = new Role();
            $Json = json_decode($roleObj->getFilterData(6,$userId), 1);
            $filters = json_decode($Json['sbu'], 1);            
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $dc_acess_list=explode(',', $dc_acess_list);
            $_leModel = new LegalEntity();
            $suppliers = $_leModel->getSupplierId();

            $fieldArr = array(
                'pr.le_wh_id',
                'pr.legal_entity_id',
                'pr.inward_id',
                'inward.inward_code',
                'pr.pr_id',
                'pr.pr_code',
                'pr.pr_status',
                'pr.sr_invoice_code',
                'pr.approval_status',
                DB::raw('IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) AS approval_status_name'),
                DB::raw('GetUserName(pr.picker_id,2) AS picker_name'),
                'pr.created_at',
                'pr.pr_grand_total as prValue',
                DB::raw('GetUserName(pr.created_by,2) AS user_name'),
                'legal_entities.business_legal_name',
                'lwh.lp_wh_name',
                'lwh.city',
                'lwh.pincode',
                'lwh.address1'
            );

            // prepare sql

            $query = DB::table('purchase_returns as pr')->select($fieldArr);
            //if ($rowCount!=1) {
            //$query->join('purchase_return_products', 'pr.pr_id', '=', 'purchase_return_products.pr_id');
            //}
            $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'pr.legal_entity_id');
            $query->leftJoin('inward', 'inward.inward_id', '=', 'pr.inward_id');
            $query->leftJoin('legalentity_warehouses as lwh', 'lwh.le_wh_id', '=', 'pr.le_wh_id');
            $query->whereIn('pr.le_wh_id',$dc_acess_list);
            
//echo '<pre/>';print_r($filter);die;

           

            if (isset($filter['Status']) && $filter['Status'] > 0) {
                $query->where(DB::raw('IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status))'), $filter['Status']['operator'],$filter['Status']['value']);
            }
            if (isset($filter['pr_code']) && !empty($filter['pr_code'])) {
                $query->where('pr.pr_code', $filter['pr_code']['operator'],$filter['pr_code']['value']);
            }
            if (isset($filter['sr_invoice_code']) && !empty($filter['sr_invoice_code'])) {
                $query->where('pr.sr_invoice_code', $filter['sr_invoice_code']['operator'],$filter['sr_invoice_code']['value']);
            }
            if(isset($filter['inwardCode']) && !empty($filter['inwardCode'])) {
                $query->where('inward.inward_code',$filter['inwardCode']['operator'], $filter['inwardCode']['value']);
            }
            if (isset($filter['createdBy']) && !empty($filter['createdBy'])) {
                $query->where(DB::raw('GetUserName(pr.created_by,2)'), $filter['createdBy']['operator'],$filter['createdBy']['value']);
            }
            if (isset($filter['picker_name']) && !empty($filter['picker_name'])) {
                $query->where(DB::raw('GetUserName(pr.picker_id,2)'), $filter['picker_name']['operator'],$filter['picker_name']['value']);
            }
            if (isset($filter['Supplier']) && $filter['Supplier'] > 0) {
                $query->where('legal_entities.business_legal_name', $filter['Supplier']['operator'],trim($filter['Supplier']['value']));
            }
            if(isset($filter['shipTo']) && !empty($filter['shipTo'])) {
                $query->where('lwh.lp_wh_name', $filter['shipTo']['operator'], trim($filter['shipTo']['value']));
            }
            if(isset($filter['prValue']) && !empty($filter['prValue'])) {
                $query->where(DB::raw('ROUND(pr.pr_grand_total,2)'), $filter['prValue']['operator'],  $filter['prValue']['value']);
            }
            
            if (!empty($filter['createdOn'])) {
                $fdate = '';
                if (isset($filter['createdOn'][2]) && isset($filter['createdOn'][1]) && isset($filter['createdOn'][0])) {
                    $fdate = $filter['createdOn'][2] . '-' . $filter['createdOn'][1] . '-' . $filter['createdOn'][0];
                }
                if ($filter['createdOn']['operator'] == '=' && !empty($fdate)) {
                    $query->whereBetween('pr.created_at', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                } else if (!empty($fdate) && $filter['createdOn']['operator'] == '<' || $filter['createdOn']['operator'] == '<=') {
                    $query->where('pr.created_at', $filter['createdOn']['operator'], $fdate . ' 23:59:59');
                } else if (!empty($fdate)) {
                    $query->where('pr.created_at', $filter['createdOn']['operator'], $fdate . ' 00:00:00');
                }
            }
            if(isset($filter['pr_status_id']) && !is_array($filter['pr_status_id'])) {
                $query->where('pr.approval_status', $filter['pr_status_id']);
            }            
            if (!empty($orderbyarray)) {
                $orderClause = explode(" ", $orderbyarray);
                $query = $query->orderby($orderClause[0], $orderClause[1]);  //order by query 
            }else
            {
                $query->orderBy('pr.pr_id', 'desc');  //order by query 
            }
            
            //print_r($filter);
            if ($rowCount) {
                $pr = $query->count();
            } else {
                $page = $perpage * $offset;
                $query->groupBy('pr.pr_id');
                $query->skip($page)->take($perpage);
                $pr = $query->get()->all();
            }
            //echo $query->toSql();die;
            return $pr;
        } catch (Exception $e) {
            
        }
    }

    public function getActivePOS() {
        try {
            $fields = array('po.po_id', 'po.po_code');
            $query = DB::table('po')->select($fields);
            $query->whereIn('po.po_status', array('87001', '87002'));
            $query->orderBy('po.created_at', 'po_id');
            return $query->get()->all();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    public function getRemarkReasons($parentId) {
        try {
            $fields = array('reason.reason_id', 'reason.name', 'reason.description');
            $query = DB::table('reason_master as reason')->select($fields);
            $query->where('reason.parent_id', $parentId);
            return $query->get()->all();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
    }

    public function getSuppliers($inwardId=0) {
        try {
            $response['supplierList'] = [];
            $legal_entity_id = \Session::get('legal_entity_id');
            $purchaseOrder = new PurchaseOrder();
            $legal_entity_type_id = $purchaseOrder->getLegalEntityTypeId($legal_entity_id);
            
            $supOptions='';
            $warehouseOptions='';
            $this->_roleModel = new Role();
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            if($inwardId > 0)
            {
                $supplierList = DB::table('legal_entities')
                        ->join('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->join('indent', 'indent.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['indent.indent_id' => $inwardId, 'legal_entities.legal_entity_type_id' => 1002, 'suppliers.is_active' => 1, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name','legal_entities.le_code'])->all();

                $warehouseList = DB::table('legalentity_warehouses')
                        ->join('indent', 'indent.le_wh_id', '=', 'legalentity_warehouses.le_wh_id')
                        ->where(['indent.indent_id' => $inwardId])
                        ->whereIn('legalentity_warehouses.le_wh_id', explode(',',$dc_acess_list))
                        ->get(['legalentity_warehouses.lp_wh_name', 'legalentity_warehouses.le_wh_id'])->all();


                $products = DB::table('inward_products as inwardprod')
                        ->where(['inwardprod.inward_id' => $inwardId])
                        ->leftJoin('products','products.product_id','=','inwardprod.product_id')
                        ->leftJoin('inward','inward.inward_id','=','inwardprod.inward_id')
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->leftJoin('product_tot as tot', function($join)
                        {
                            $join->on('products.product_id','=','tot.product_id');
                            $join->on('tot.supplier_id','=','inward.legal_entity_id');
                            $join->on('tot.le_wh_id','=','inward.le_wh_id');
                        })
                        ->leftJoin('currency','tot.currency_id','=','currency.currency_id')
                        ->select('products.product_title', 'inwardprod.*', 'quarantine_stock','products.sku', 'products.seller_sku', 'products.upc', 'products.mrp','brands.brand_id', 'products.pack_size','tot.dlp','tot.base_price','currency.symbol_right as symbol')
                        ->get()->all();
                foreach($products as $k=>$product) {
                    $tot_rec_qty = $this->getTotalRecQtyByInwardId($product->inward_id, $product->product_id);
                    $rem_qty = $product->received_qty - $tot_rec_qty;
                    if($rem_qty>0)
                    {
                        $products[$k]->rem_qty = $rem_qty;
                    } else {
                        unset($products[$k]);
                    }
                }
            }else{
                $supOptions='<option value="">Select Supplier</option>';
                $warehouseOptions='<option value="">Select Delivery Location</option>';

                $supplierList = DB::table('legal_entities')
                        ->join('suppliers', 'suppliers.legal_entity_id', '=', 'legal_entities.legal_entity_id')
                        ->where(['legal_entities.legal_entity_type_id' => 1002, 'suppliers.is_active' => 1, 'legal_entities.is_approved' => 1, 'parent_id'=>$legal_entity_id])
                        ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name','legal_entities.le_code'])->all();
                $warehouseList=[];
                $products = [];
            }
            $universalSupplier = DB::table('master_lookup')->select('description')->where('value','=',78023)->first();
            $legal_id = DB::table('legal_entities')->select(['legal_entity_id','business_legal_name'])->where('legal_entity_id','=',$universalSupplier->description)->first();
            $supOptions .= '<option value='.$legal_id->legal_entity_id.'>'.$legal_id->business_legal_name.'</option>';

            $dc_fc_mapping_data = $purchaseOrder->getDCFCData($legal_entity_id);

            foreach($dc_fc_mapping_data as $dc_fc){
                $supOptions .= '<option value='.$dc_fc->legal_entity_id.'>'.$dc_fc->business_legal_name.'</option>';
            }
            if($legal_entity_type_id == 1001){
                $legal_entity_type_id = [1014,1016];
            
                $fc_dc_legal_entities = DB::table('dc_fc_mapping')->select(DB::raw("GROUP_CONCAT(DISTINCT CONCAT(dc_le_id,',',fc_le_id) ) AS dc_le_id"))
                            ->whereIn('dc_fc_mapping.dc_le_wh_id', explode(',',$dc_acess_list))
                            ->orWhereIn('dc_fc_mapping.fc_le_wh_id', explode(',',$dc_acess_list))
                            ->first();
                $fc_dc_legal_entities = isset($fc_dc_legal_entities->dc_le_id) ? $fc_dc_legal_entities->dc_le_id : "";
                $dcfcList = array();
                if($fc_dc_legal_entities != ""){
                    $dcfcList = DB::table('legal_entities')
                            ->whereIn( 'legal_entities.legal_entity_id',explode(',',$fc_dc_legal_entities))
                            ->whereIn( 'legal_entities.legal_entity_type_id',$legal_entity_type_id)
                            ->get(['legal_entities.legal_entity_id', 'legal_entities.business_legal_name','legal_entities.le_code'])->all();
                }
                foreach($dcfcList as $dc_fc){
                   
                     $supOptions .= '<option value='.$dc_fc->legal_entity_id.'>'.$dc_fc->business_legal_name.' -- '.$dc_fc->le_code.'</option>';
                    
                }
            }
            foreach($supplierList as $supplier){
                if($supplier->legal_entity_id != $legal_id->legal_entity_id)
                $supOptions .= '<option value='.$supplier->legal_entity_id.'>'.$supplier->business_legal_name.' -- '.$supplier->le_code.'</option>';
            }
            foreach($warehouseList as $warehouse){
                $warehouseOptions .= '<option value='.$warehouse->le_wh_id.'>'.$warehouse->lp_wh_name.'</option>';
            }
            $response['supplierList'] = $supOptions;
            $response['warehouseList'] = $warehouseOptions;
            $response['products'] = $products;
            $response['productList'] = '';
            return $response;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage() . ' ' . $ex->getTraceAsString());
        }
        return $response;
    }

    public function savePurchaseReturnData($data)
    {
        try
        {
            $prId = 0;
            $totPoQty = 0;
                        
            #echo '<pre>';print_r($data);die;
            
            $legalEntityId = \Session::get('legal_entity_id');
            $supplier_id = isset($data['supplier_list']) ? $data['supplier_list'] : 0;
            $warehouse_id = isset($data['warehouse_list']) ? $data['warehouse_list'] : 0;
            $sale_return_inv_no = isset($data['sale_return_inv_no']) ? $data['sale_return_inv_no'] : "";
            $purchaseOrder = new purchaseOrder();
            $whdetails = $purchaseOrder->getLEWHById($warehouse_id);
            $state_code = isset($whdetails->state_code)?$whdetails->state_code:"TS";

            $serialNumber = Utility::getReferenceCode("PR",$state_code);
             // Log::info("serialNumber CODE GENENENENENNE ---------".$serialNumber);
            DB::beginTransaction();
            #var_dump($totPoQty);die;
            
            if($sale_return_inv_no !=""){
                $sr_data = array("supplier_id"=>$supplier_id,
                        "sr_inv_no"=>$sale_return_inv_no,
                        "pr_totprice"=>$data['pr_totprice'],
                        "pr_le_wh_id"=>$warehouse_id);
                $sr_data = $this->checkSrInvoice($sr_data);
                if(isset($sr_data['status']) && $sr_data['status'] == 400){
                    $sr_data['serialNumber'] = "";
                    $sr_data['pr_id'] = "";
                    return $sr_data;
                }
            }
            $prDetails['legal_entity_id'] = $supplier_id;
            $prDetails['sr_invoice_code'] = $sale_return_inv_no;
            $prDetails['le_wh_id'] = $warehouse_id;
            $prDetails['created_by'] = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
            //$prDetails['platform'] = (isset($data['platform_id']) && $data['platform_id']!='')?$data['platform_id']:5001;
            $prDetails['approval_status'] = 57036;
            //$bill_discount = (isset($data['bill_discount']))?$data['bill_discount']:0;
            //$bill_discount_type = (isset($data['bill_discount_type']))?$data['bill_discount_type']:0;
            $billDiscAmt = 0;
            $grand_total = array_sum($data['pr_totprice']);
            /*if($bill_discount_type==1){
                $billDiscAmt = ($grand_total*$bill_discount)/100;
            }else{
                $billDiscAmt = $bill_discount;
            }
            if($billDiscAmt>$grand_total){
                return array('status'=>400, 'message'=>'Bill discount amount can not be more than total', 'pr_id'=>'','serialNumber'=>'');
            }else{
                $grand_total = ($grand_total - $billDiscAmt);
            } */
            //$prDetails['discount_amt'] = $billDiscAmt;
            //$prDetails['discount'] = (isset($data['bill_discount']))?$data['bill_discount']:0;
            //$prDetails['discount_type'] = (isset($data['bill_discount_type']))?$data['bill_discount_type']:0;
            $prDetails['pr_grand_total'] = $grand_total;
            
            $prDetails['pr_remarks'] = isset($data['pr_remarks']) ? $data['pr_remarks'] : '';
            $prDetails['pr_code'] = $serialNumber;
            $prId = $this->create($prDetails);
            $prId = $prId->pr_id;
            
            if($prId)
            {
                $productInfo = isset($data['pr_product_id']) ? $data['pr_product_id'] : [];
                //$packsize = $data['packsize'];
                
                if(!empty($productInfo))
                {
                    $totalQty = 0;
                    foreach($productInfo as $key=>$product_id)
                    { 
                        $product = $this->getProductInfoByID($product_id,$supplier_id,$warehouse_id);                        
                        $product = json_decode(json_encode($product),true);
                        $pr_product = array();
                        $pr_product['product_id'] = $product_id;
                        //$pr_product['parent_id'] = (isset($data['parent_id']) && isset($data['parent_id'][$key]))?$data['parent_id'][$key]:'';
                        $pr_product['mrp'] = (isset($product['mrp']) && $product['mrp']!='')?$product['mrp']:0;
                       
                        $qty = (isset($data['soh_qty'][$key]))?$data['soh_qty'][$key]:0;
                        $dit_qty = (isset($data['dit_qty'][$key]))?$data['dit_qty'][$key]:0;
                        $dnd_qty = (isset($data['dnd_qty'][$key]))?$data['dnd_qty'][$key]:0;
                        if(isset($product['soh']) && isset($product['dit_qty']) && isset($product['dnd_qty'])){
                            $avilInv = $product['soh']-$product['order_qty'];
                            if($qty>$avilInv){
                                DB::rollback();
                                return array('status'=>400, 'message'=>'SOH Qty should not be more than Current SOH for <strong>'.$product['sku'].'</strong>', 'pr_id'=>'','serialNumber'=>'');
                            }
                            if($dit_qty>$product['dit_qty']){
                                DB::rollback();
                                return array('status'=>400, 'message'=>'DIT Qty should not be more than Current DIT for <strong>'.$product['sku'].'</strong>', 'pr_id'=>'','serialNumber'=>'');
                            }
                            if($dnd_qty>$product['dnd_qty']){
                                DB::rollback();
                                return array('status'=>400, 'message'=>'DND Qty should not be more than Current DND for <strong>'.$product['sku'].'</strong>', 'pr_id'=>'','serialNumber'=>'');
                            }
                        }else{
                            DB::rollback();
                            return array('status'=>400, 'message'=>'Could not find inventory details', 'pr_id'=>'','serialNumber'=>'');
                        }
                        //$pack_id = (isset($packsize[$key]) && $packsize[$key]!='')?$packsize[$key]:'';
                        //$uomPackinfo = $purchaseOrder->getProductPackUOMInfoById($pack_id);                            
                        //$no_of_eaches = 1;//(isset($uomPackinfo->no_of_eaches))?$uomPackinfo->no_of_eaches:0;
                        
                       /* $free_qty = (isset($data['freeqty'][$key]))?$data['freeqty'][$key]:0;
                        $free_pack_id=(isset($data['freepacksize'][$key]) && $data['freeqty'][$key]!=0)?$data['freepacksize'][$key]:'';
                        $freeUOMPackinfo = $purchaseOrder->getProductPackUOMInfoById($free_pack_id);
                        $free_no_of_eaches = (isset($freeUOMPackinfo->no_of_eaches))?$freeUOMPackinfo->no_of_eaches:0;
                        */
                        $pr_product['qty'] = $qty;
                        $pr_product['dit_qty'] = $dit_qty; //damage qty
                        $pr_product['dnd_qty'] = $dnd_qty; //missing qty
                        $pr_product['no_of_eaches'] = 1;//$no_of_eaches;
                        //$pr_product['uom'] = (isset($uomPackinfo->value))?$uomPackinfo->value:0;
                       // $pr_product['free_qty'] = $free_qty;
                        //$pr_product['free_eaches'] = $free_no_of_eaches;
                        //$pr_product['free_uom'] = (isset($freeUOMPackinfo->value) && $data['freeqty'][$key]!=0)?$freeUOMPackinfo->value:0;
                        
                        $pr_product['is_tax_included'] = (isset($data['pretax'][$product_id]))?$data['pretax'][$product_id]:0;
                        //$pr_product['discount_inc_tax'] = (isset($data['item_disc_tax_type'][$key]))?$data['item_disc_tax_type'][$key]:0;
                        //$pr_product['discount_type'] = (isset($data['item_discount_type'][$key]))?$data['item_discount_type'][$key]:0;
                        //$pr_product['discount'] = (isset($data['item_discount'][$key]))?$data['item_discount'][$key]:0;
                        //$pr_product['discount_amt'] = (isset($data['item_discount_amt'][$product_id]))?$data['item_discount_amt'][$product_id]:0;
                        $unit_price = (isset($data['unit_price'][$product_id]))?$data['unit_price'][$product_id]:0;
                        $pr_product['unit_price'] = $unit_price;
                        $pr_product['price'] = (isset($data['pr_baseprice'][$key]))?$data['pr_baseprice'][$key]:0;
                        $totQty = $qty+$dit_qty+$dnd_qty;//(($qty * $no_of_eaches) - ($free_qty * $free_no_of_eaches));
                        $pr_product['sub_total'] = $unit_price * $totQty;
                        $pr_product['total'] = (isset($data['pr_totprice'][$key]))?$data['pr_totprice'][$key]:0;
                        
                        $tax_total = (isset($data['pr_taxvalue'][$product_id]))?$data['pr_taxvalue'][$product_id]:0;
                        $tax_amt = $tax_total / $totQty;
                        
                        $pr_product['tax_type'] = (isset($data['pr_taxname'][$product_id]))?$data['pr_taxname'][$product_id]:'';
                        $pr_product['tax_per'] = (isset($data['pr_taxper'][$product_id]))?$data['pr_taxper'][$product_id]:0;
                        $pr_product['tax_amt'] = $tax_amt;
                        $pr_product['tax_total'] = $tax_total;
                        $pr_product['tax_data'] = (isset($data['pr_taxdata'][$product_id]))?base64_decode($data['pr_taxdata'][$product_id],true):'';
                        $pr_product['hsn_code'] = (isset($data['hsn_code'][$product_id]))?$data['hsn_code'][$product_id]:'';
                        $pr_product['pr_id'] = $prId;
                       #print_r($pr_product);die;
                        DB::table('purchase_return_products')->insert($pr_product);
                        $pr_product['created_by'] = (isset($data['created_by']) && $data['created_by']!='')?$data['created_by']:\Session::get('userId');
                        //$totQty = $totQty + ($qty*$no_of_eaches);
                        $totalQty = $totalQty + $totQty;
                    }
                    $arr = ['pr_total_qty'=>$totalQty];
                    $this->updatePR($prId,$arr);
                    DB::commit();
                }
                return array('status'=>200, 'message'=>'PR Created Successfully', 'pr_id'=>$prId,'serialNumber'=>$serialNumber);
            }
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return json_encode(array('status'=>400, 'message'=>$ex->getMessage(), 'pr_id'=>0));
        }
    }

    public function getPRQtyByPOId($poId) {
        try {
            $fieldArr = array(DB::raw('SUM(qty) AS tot_received'));
            $query = DB::table('purchase_return_products')->select($fieldArr);
            $query->join('purchase_returns', 'purchase_returns.pr_id', '=', 'purchase_return_products.pr_id');
            $query->where('purchase_returns.po_id', $poId);
            $prQty = $query->first();
            return isset($prQty->tot_received) ? (int) $prQty->tot_received : 0;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    private function getDeliveryDate($days) {
        $curDate = date('Y-m-d');
        $deliveryDate = array();
        if (is_array($days) && count($days) > 0) {
            foreach ($days as $day) {
                $date = date('Y-m-d', strtotime($day . ' this week'));

                if ($date >= $curDate) {
                    $date = date('Y-m-d', strtotime($day . ' next week'));
                }

                $deliveryDate[] = $date;
            }
        }
        return $deliveryDate;
    }

    private function sendEmail($mailTo, $subject, $body = array()) {
        try {
            $mailFields = array('mailTo' => $mailTo, 'subject' => $subject, 'attachment' => $body['attachment']);
            $success = Mail::send($body['template'], array('name' => $body['name'], 'comment' => $body['comment']), function ($message) use ($mailFields) {

                        $message->to($mailFields['mailTo']);
                        $message->subject($mailFields['subject']);

                        if ($mailFields['attachment'] != '') {
                            $message->attach($mailFields['attachment']);
                        }
                    });
            return $success;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * getPrCountByStatus method is used to get total record based on status by legal entity
     * @param  Integer $legalEntityId
     * @return Array
     */
    public function getPrCountByStatus($legalEntityId) {
        try {
            $userId = Session::get('userId');
            $roleObj = new Role();
            $Json = json_decode($roleObj->getFilterData(6,$userId), 1);
            $filters = json_decode($Json['sbu'], 1);            
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            $prDetails = "SELECT 
                            SUM(IF(pr.`approval_status` IS NULL, 1 , IF(pr.`approval_status`=0, 1, 
                            IF(pr.`approval_status`=57036, 1, 0)))) AS 'Initiated',
                            SUM(IF(pr.`approval_status` = 57037, 1, 0)) AS 'Created',
                            SUM(IF(pr.`approval_status` = 57139, 1, 0)) AS 'Picklist',
                            SUM(IF(pr.`approval_status` = 57140, 1, 0)) AS 'RTD',
                            SUM(IF(pr.`approval_status` = 57136, 1, 0)) AS 'Verified',
                            SUM(IF(pr.`approval_status` = 57137, 1, 0)) AS 'Dispatched',
                            SUM(IF(pr.`approval_status` = 57138, 1, 0)) AS 'Cancelled',
                            SUM(IF(pr.`approval_status` = 1, 1, 0)) AS 'Completed'
                        FROM `purchase_returns` AS pr WHERE pr.le_wh_id IN (".$dc_acess_list.")";
                        //JOIN legal_entities on legal_entities.legal_entity_id=pr.legal_entity_id 
                        //where legal_entities.parent_id=$legalEntityId
            $prData = DB::select(DB::raw($prDetails));
            return $prData;
        } catch (Exception $e) {
            
        }
    }

    public function getPrDetailById($prId) {
        try {

            $fieldArr = array(
                'pr.le_wh_id',
                'pr.legal_entity_id',
                'pr.pr_id',
                'pr.pr_code',
                'pr.sr_invoice_code',
                'pr.pr_address',
                //'pr.discount_type as bill_discount_type',
                //'pr.discount as bill_discount',
                //'pr.discount_amt as bill_discount_amt',
                'pr.pr_grand_total',
                'pr.pr_status',
                'pr.approval_status',
                DB::raw('IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) AS approval_status_name'),
                'pr.created_at',
                'pr.pr_remarks',
                 DB::raw('GetUserName(pr.created_by,2) AS user_name'),                
                'prp.product_id',
                'prp.parent_id',
                'prp.mrp',
                'prp.qty',
                'prp.dit_qty',
                'prp.dnd_qty',
                'prp.uom',
                'prp.no_of_eaches',
                'prp.free_qty',
                'prp.free_uom',
                'prp.free_eaches',
                'prp.tax_type',
                'prp.tax_per',
                'prp.tax_amt',
                'prp.tax_total',
                'prp.hsn_code',
                'prp.tax_data',
                //'prp.discount_inc_tax',
                //'prp.discount_type',
                //'prp.discount',
                //'prp.discount_amt',
                'prp.is_tax_included',
                'prp.unit_price',
                'prp.price',
                'prp.sub_total',
                'prp.total',
                'inventory.soh',
                'inventory.dit_qty as inv_dit_qty',
                'inventory.dnd_qty as inv_dnd_qty',
                'gdsp.sku',
                'gdsp.product_title as product_name',
            );

            // prepare sql

            $query = DB::table('purchase_returns as pr')->select($fieldArr);
            $query->join('purchase_return_products as prp', 'pr.pr_id', '=', 'prp.pr_id');
            $query->join('products as gdsp', 'gdsp.product_id', '=', 'prp.product_id');
            $query->join('inventory', function($join)
                {
                   $join->on('prp.product_id','=','inventory.product_id');
                   $join->on('pr.le_wh_id','=','inventory.le_wh_id');
                });
            $query->where('pr.pr_id', $prId);
            $pr = $query->get()->all();

            //echo $query->toSql();die;
            return $pr;
        } catch (Exception $e) {
            
        }
    }
    public function getPRDetails($prId) {
        try {
            $fieldArr = array(
                'pr.le_wh_id',
                'pr.legal_entity_id',
                'pr.pr_id',
                'pr.pr_code',
                //'pr.discount_type as bill_discount_type',
                //'pr.discount as bill_discount',
                //'pr.discount_amt as bill_discount_amt',
                'pr.pr_grand_total',
                'pr.pr_status',
                'pr.approval_status',
                 DB::raw('IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) AS approval_status_name'),
                'pr.created_at',
                'pr.pr_remarks',
                DB::raw('GetUserName(pr.created_by,2) AS user_name'),                
            );
            // prepare sql
            $query = DB::table('purchase_returns as pr')->select($fieldArr);            
            $query->where('pr.pr_id', $prId);
            $pr = $query->first();
            return $pr;
        } catch (Exception $e) {
            
        }
    }
    public function getProductInfoByID($product_id,$supplier_id,$warehouse_id) {
        try {
            $product = DB::table('products')
                        ->where('products.product_id',$product_id)
                        ->where('tot.supplier_id',$supplier_id)
                        ->where('tot.le_wh_id',$warehouse_id)
                        ->leftJoin('brands','products.brand_id','=','brands.brand_id')
                        ->leftJoin('product_content as content','products.product_id','=','content.product_id')
                        ->leftJoin('product_tot as tot','products.product_id','=','tot.product_id')
                        ->leftJoin('inventory', function($join)
                         {
                            $join->on('products.product_id','=','inventory.product_id');
                            $join->on('tot.le_wh_id','=','inventory.le_wh_id');
                         })
                        ->leftJoin('currency','tot.currency_id','=','currency.currency_id')
                        ->select('products.product_id','products.upc','products.product_title as pname'
                                ,'products.sku'
                                ,'products.pack_size','products.seller_sku','products.mrp','tot.base_price as price'
                                ,'tot.dlp','tot.supplier_id','tot.le_wh_id'
                                ,'brands.brand_id','brands.brand_name'
                                ,'inventory.mbq'
                                ,'inventory.soh'
                                ,'inventory.dit_qty'
                                ,'inventory.dnd_qty'
                                ,'inventory.atp'
                                ,'inventory.order_qty'
                                ,'currency.symbol_left as symbol'
                                ,'products.is_sellable',
                                DB::raw('getPackType(products.product_id) AS packType'),
                                DB::raw('(select elp from purchase_price_history as pph where pph.product_id=products.product_id order by pur_price_id desc limit 0,1) as prev_elp'),
                                DB::raw('getMastLookupValue(products.kvi) AS KVI'))
                        ->first();
            return $product;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function savePrProducts($pr_product){
        try{
            DB::table('purchase_return_products')->insert($pr_product);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updatePRProducts($pr_product,$product_id,$prId,$flagdata){
    try
    {
        $update = DB::table('purchase_return_products')->where('product_id',$product_id)
                                        ->where('pr_id',$prId)->update($pr_product);
        
    }
    catch (\ErrorException $ex) {
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        return Response::json(array('status'=>400, 'message'=>'Failed', 'po_id'=>0));
    }
}
public function checkPRProductExist($prId,$product_id){
        try{
            $fieldArr = array('purchase_return_products.pr_id','purchase_return_products.product_id');
            $query = DB::table('purchase_return_products')->select($fieldArr);
            $query->where('purchase_return_products.pr_id', $prId);
            $query->where('purchase_return_products.product_id', $product_id);
            $productInfo = $query->first();
            if($productInfo && count($productInfo)>0){
                return 1;
            }else{
                return 0;
            }
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPreUpdatePRProducts($prId,$product_id){
        try{
            $fieldArr = array('prp.qty','prp.dit_qty','prp.dnd_qty','prp.no_of_eaches'
                ,'prp.is_tax_included','prp.unit_price','prp.price','prp.sub_total','prp.total','prp.tax_type','prp.tax_per','prp.tax_amt','prp.tax_total');
            $query = DB::table('purchase_return_products as prp')->select($fieldArr);
            $query->where('prp.pr_id', $prId);
            $query->where('prp.product_id', $product_id);
            $productInfo = $query->first();
            return json_decode(json_encode($productInfo),true);
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function updatePR($prId, $dataArr) {
        try {
            DB::table('purchase_returns')->where('pr_id', $prId)->update($dataArr);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getPrCodeById($prId) {
        try {

            $fieldArr = array('pr.pr_code', 'legal_entity_id', 'pr.approval_status');
            $query = DB::table('purchase_returns as pr')->select($fieldArr);
            $query->where('pr.pr_id', $prId);
            $pr = $query->first();
            return $pr;
        } catch (Exception $e) {
            
        }
    }
    public function deletePRProducts($prId,$productId){
        try{
            $delete = DB::table('purchase_return_products')->where('product_id', $productId)
                            ->where('pr_id',$prId)->delete();
            return $delete;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function assignPickerToPR($pr_id, $user_id, $picker_id) {
        try {
            if (isset($pr_id) && is_array($pr_id)) {
                $this->_roleRepo = new RoleRepo();
                $picker_data = $this->_roleRepo->getUserInfoById($picker_id);
                $picker_name = (isset($picker_data->firstname) && isset($picker_data->lastname)) ? $picker_data->firstname . ' ' . $picker_data->lastname : '';
                foreach ($pr_id as $prId) {
                    /**
                     * approval status
                     */
                    $approval_flow_func = new CommonApprovalFlowFunctionModel();
                    $res_approval_flow_func = $approval_flow_func->getApprovalFlowDetails('Purchase Return', '57037', $user_id);
                    if (isset($res_approval_flow_func["currentStatusId"]) && isset($res_approval_flow_func["data"])) {
                        $current_status_id = $res_approval_flow_func["currentStatusId"];
                        $next_status_id = $res_approval_flow_func["data"][0]["nextStatusId"];
                        $appr_comment = 'Picklist Generated(Assigned to ' . $picker_name . ' #'.$picker_id.')';
                        $approval_flow_func->storeWorkFlowHistory('Purchase Return', $prId, $current_status_id, $next_status_id, $appr_comment, $user_id);
                    }
                    $dataArr = ['picker_id' => $picker_id, 'approval_status' => 57139, 'picker_assigned_at' => date('Y-m-d')];
                    $this->updatePR($prId, $dataArr);
                }
                return json_encode(Array('status'=>200, 'message' => 'picker assigned successfully', 'data' => []));
            } else {
                return json_encode(Array('status' => 'session', 'message' => 'Pr Id should not be empty', 'data' => []));
            }
        } catch (Exception $e) {
            
        }
    }
    public function getPicklistPRDetails($prId) {
        try {
            $fieldArr = array(
                'pr.le_wh_id',
                'pr.legal_entity_id',
                'pr.pr_id',
                'pr.pr_code',
                'pr.pr_grand_total',
                'pr.pr_status',
                'pr.approval_status',
                 DB::raw('IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) AS approval_status_name'),
                DB::raw('GetUserName(pr.picker_id,2) AS picker_name'),
                'pr.picked_at',
                'pr.created_at',
                'pr.pr_remarks',
                DB::raw('GetUserName(pr.created_by,2) AS user_name'),
                'lw.lp_wh_name',
                'le.business_legal_name',
                'users.mobile_no'
            );
            // prepare sql
            $query = DB::table('purchase_returns as pr')->select($fieldArr);
            $query->join('legalentity_warehouses as lw', 'lw.le_wh_id', '=', 'pr.le_wh_id');
            $query->join('legal_entities as le', 'le.legal_entity_id', '=', 'pr.legal_entity_id');
            $query->join('users', 'users.legal_entity_id', '=', 'le.legal_entity_id');
            if (is_array($prId)) {
                $query->whereIn('pr.pr_id', $prId);
            } else {
                $query->where('pr.pr_id', $prId);
            }
            $pr = $query->get()->all();
            return $pr;
        } catch (Exception $e) {
            
        }
    }
    public function getPrProducts($pr_id) {
        try {            
            $fieldArr = array(
                'pr.le_wh_id','pr.legal_entity_id','pr.pr_id','pr.pr_code','pr.pr_grand_total',
                'pr.pr_status','pr.approval_status',
                DB::raw('IF(pr.approval_status=1,"Finance Approved",getMastLookupValue(pr.approval_status)) AS approval_status_name'),
                'pr.created_at','pr.pr_remarks',
                 DB::raw('GetUserName(pr.created_by,2) AS user_name'),                
                 DB::raw('GetUserName(pr.picker_id,2) AS picker_name'),
                'prp.product_id','prp.parent_id','prp.mrp','prp.qty','prp.dit_qty','prp.dnd_qty',
                'prp.uom','prp.no_of_eaches','prp.free_qty','prp.free_uom','prp.free_eaches','prp.tax_type',
                'prp.tax_per','prp.tax_amt','prp.tax_total','prp.hsn_code','prp.tax_data',
                'prp.is_tax_included','prp.unit_price','prp.price','prp.sub_total','prp.total',
                'gdsp.sku','gdsp.product_title as product_name',
                'lw.lp_wh_name',
                'prp.unit_price'
            );
            // prepare sql
            $query = DB::table('purchase_returns as pr')->select($fieldArr);
            $query->join('purchase_return_products as prp', 'pr.pr_id', '=', 'prp.pr_id');
            $query->join('legalentity_warehouses as lw', 'lw.le_wh_id', '=', 'pr.le_wh_id');
            $query->join('legal_entities as le', 'le.legal_entity_id', '=', 'pr.legal_entity_id');
            $query->join('products as gdsp', 'gdsp.product_id', '=', 'prp.product_id');
            $query->where('pr.pr_id', $pr_id);
            return $query->get()->all();
        } catch (Exception $e) {
            
        }
    }

    public function updateStatusAWF($table,$unique_column,$approval_unique_id, $next_status_id){
        try{
            $status = explode(',',$next_status_id);
            $new_status = ($status[1]==0)?$status[0]:$status[1];
            $invoice = array(
                'approval_status'=>$new_status,
                'approved_by'=>\Session::get('userId'),
                'approved_at'=>date('Y-m-d H:i:s')
            );
            if(in_array($new_status, [1,57137]) && $table == 'purchase_returns')
            {
                $invoice['pr_status']=103002;
            }
            DB::table($table)->where($unique_column, $approval_unique_id)->update($invoice);
            $response = array('status'=>200,'message'=>'Success');
            if($new_status == 1 && $table == 'purchase_returns')
            {
                $this->saveOutputTax($approval_unique_id);
                app('App\Modules\Grn\Controllers\GrnController')->createPurchaseReturnVoucher($approval_unique_id);
            }else if($new_status == 57137 && $table == 'purchase_returns'){ //57137- verified & dispatched
                $response = $this->saveStockOutward($approval_unique_id);
                $sale_return_inv_no = DB::table("purchase_returns")->select("sr_invoice_code")->where("pr_id",$approval_unique_id)->first();
                $sale_return_inv_no = isset($sale_return_inv_no->sr_invoice_code)?$sale_return_inv_no->sr_invoice_code:"";
                if($sale_return_inv_no != ""){
                    $this->_orderModel = new OrderModel();
                    $gds_order_data  = $this->_orderModel->getInvoiceDataFromInvoiceCode($sale_return_inv_no);
                    $gds_order_id = $gds_order_data->gds_order_id;
                    $returnStock = $this->getPrDetailById($approval_unique_id);
                    $returnStock = json_decode(json_encode($returnStock),true);
                    $supplier_id = isset($returnStock[0]['legal_entity_id'])?$returnStock[0]['legal_entity_id']:'';
                    $supp_le_wh_id = $this->getleWhIdByLeId($supplier_id);
                    $this->salesReturnByPrId($approval_unique_id,$gds_order_id,$supp_le_wh_id);
                }
            }
            return $response;
        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function saveStockOutward($pr_id) {
        try {
            $returnStock = $this->getPrDetailById($pr_id);
            if (count($returnStock) > 0) {
                $stockOutward = array();
                $returnStock = json_decode(json_encode($returnStock),true);
                foreach ($returnStock as $key=>$stock) {
                    $product = $this->getProductInfoByID($stock['product_id'],$stock['legal_entity_id'],$stock['le_wh_id']);
                    $product = json_decode(json_encode($product),true);
                    if (isset($product['soh']) && isset($product['dit_qty']) && isset($product['dnd_qty'])) {
                        $avilInv = $product['soh']-$product['order_qty'];
                        if ($stock['qty'] > $avilInv) {
                            return array('status' => 400, 'message' => 'SOH Qty should not be more than Current SOH for <strong>' . $product['sku'] . '</strong>');
                        }
                        if ($stock['dit_qty'] > $product['dit_qty']) {
                            return array('status' => 400, 'message' => 'DIT Qty should not be more than Current DIT for <strong>' . $product['sku'] . '</strong>');
                        }
                        if ($stock['dnd_qty'] > $product['dnd_qty']) {
                            return array('status' => 400, 'message' => 'DND Qty should not be more than Current DND for <strong>' . $product['sku'] . '</strong>');
                        }
                    } else {
                        return array('status' => 400, 'message' => 'Could not find inventory details');
                    }
                    $stockOutward[] = array(
                        'reference_no' => $stock['pr_id'],
                        'reference_type' => $stock['pr_code'],
                        'product_id' => $stock['product_id'],
                        'ordered_qty' => $stock['qty'],
                        'dit_qty' => $stock['dit_qty'],
                        'dnd_qty' => $stock['dnd_qty'],
                        'outward_date' => date('Y-m-d'),
                        'outward_type' => 1,
                        'le_wh_id' => $stock['le_wh_id'],
                        'created_by' => Session('userId')
                    );
                    $returnStock[$key]['qty'] = $stock['qty'];
                }
                $le_wh_id = isset($returnStock[0]['le_wh_id'])?$returnStock[0]['le_wh_id']:'';
                $refNo = isset($returnStock[0]['pr_code'])?$returnStock[0]['pr_code']:'';
                DB::table('stock_outward')->insert($stockOutward);
                $invModel = new Inventory();
                $invModel->inventoryStockOutward($returnStock, $le_wh_id, 1, $refNo, 4);  
                return array('status' => 200, 'message' => 'success');
            } else {
                return array('status' => 400, 'message' => 'No data found');
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function saveOutputTax($pr_id) {
        try {
            $productTax = $this->getPrDetailById($pr_id);
            if (count($productTax) > 0) {
                $outputTax = array();
                foreach ($productTax as $tax) {
                    $tax_amount = $tax->tax_total;
                    $outputTax[] = array('outward_id' => $tax->pr_id,
                        'product_id' => $tax->product_id,
                        'transaction_no' => $tax->pr_code,
                        'transaction_date' => date('Y-m-d H:i:s'),
                        'transaction_type' => 101004,
                        'tax_type' => $tax->tax_type,
                        'tax_percent' => $tax->tax_per,
                        'tax_amount' => $tax_amount,
                        'le_wh_id' => $tax->le_wh_id,
                        'created_by' => Session('userId')
                    );
                }
                DB::table('output_tax')->insert($outputTax);
                return 'success';
            } else {
                return 'No Tax data found';
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }


    public function salesReturnByPrId($pr_id,$gds_order_id,$le_wh_id){
        // Creating Sales Return
        $this->returnMdl = new ReturnModel();
        $user_id = Session::get('user_id');

        $returnStock = $this->getPrDetailById($pr_id);
        if (count($returnStock) > 0) {
            $stockOutward = array();
            $returnStock = json_decode(json_encode($returnStock),true);
            $status_id = 67002;
            $returndata = array();
            $key = 0;
            $returnValue = 0;
            $total_return_items = 0;
            $total_return_item_qty = 0;
            $_OrderModel = new OrderModel();
            foreach ($returnStock as $key=>$stock) {
                $product = $stock;
                $product_id = $product['product_id'];
                $return_qty = ($product['qty'] * $product['no_of_eaches']) + $product['dit_qty'] + $product['dnd_qty'];

                $checkProduct = $_OrderModel->getProductByOrderId($gds_order_id,[$product_id]);
                $good_qty = $return_qty;
                $excess_qty = $return_qty;
                if(count($checkProduct)){
                    $good_qty = 0;
                }else{
                    $excess_qty = 0;
                }
                $price = $product['unit_price'];                   
                $returnValue += $product['unit_price'] * $return_qty;
                $returndata[$key]['product_id'] = $product_id;
                $returndata[$key]['qty'] = $return_qty;
                $returndata[$key]['good_qty'] = $good_qty;
                $returndata[$key]['bad_qty'] = 0;
                $returndata[$key]['dnd_qty'] = 0;
                $returndata[$key]['dit_qty'] = 0;
                $returndata[$key]['excess_qty'] = $excess_qty;
                $returndata[$key]['return_reason_id'] = 59016;
                $returndata[$key]['return_status_id'] = $status_id;
                $returndata[$key]['approval_status'] = $status_id;
                $returndata[$key]['approved_by_user'] = $user_id;
                $returndata[$key]['gds_order_id'] = $gds_order_id;
                $returndata[$key]['le_wh_id'] = $le_wh_id;
                $returndata[$key]['tax_details'] = $price;
                $key++;
                //adding return qty
                $total_return_items += 1;
                $total_return_item_qty += $return_qty;
            }
            if(count($returndata) > 0){
                $orderData['gds_order_id'] = $gds_order_id;
                $orderData['total_return_value'] = $returnValue;
                $orderData['return_status_id'] = $status_id;
                $orderData['approval_status'] = $status_id;
                $orderData['total_return_items'] = $total_return_items;
                $orderData['total_return_item_qty'] = $total_return_item_qty;
                
                $refcode = $_OrderModel->getRefCode('SR');
                $orderData['reference_no'] = $refcode;
                $returnGridId = $this->returnMdl->saveReturnGrid($orderData);

                foreach ($returndata as $key => $orderData) {   

                    if($returnGridId){

                        $orderData['return_grid_id'] = $returnGridId;
                        $orderData['reference_no'] = $refcode;
                        $returnData[$key]['return_grid_id'] = $returnGridId;
                        $returnData[$key]['reference_no'] = $refcode;

                        //get tax percentage

                        $product_tax_details = $this->getProductTaxInfoById($pr_id,$orderData['product_id']);
                        $tax_per = $product_tax_details->tax_per;
                        $singleUnitPrice = (($product_tax_details->sub_total / (100+$tax_per)*100) / $orderData['qty']);
                        $singleUnitPriceWithtax = (($tax_per/100) * $singleUnitPrice) + $singleUnitPrice;

                        $tax_details = json_decode($product_tax_details->tax_data,true);
                        $tax_details = $tax_details[0];
                        $tax_per = $tax_details['Tax Percentage'];
                        $tax_class = $tax_details['Tax Class ID'];
                        $SGST = $tax_details['SGST'];
                        $CGST = $tax_details['CGST'];
                        $IGST = $tax_details['IGST'];
                        $UTGST = $tax_details['UTGST'];

                        $pricedata = array(   'singleUnitPrice' => $singleUnitPrice,
                                        'singleUnitPriceWithtax' => $singleUnitPriceWithtax,
                                        'tax_percentage' => $tax_per,
                                        'tax_class' => $tax_class,
                                        'SGST' => $SGST,
                                        'CGST' => $CGST,
                                        'IGST' => $IGST,
                                        'UTGST' => $UTGST,                                      
                                    );
                        $return = $this->returnMdl->saveReturns($orderData,$user_id,$pricedata);
                            if($return){
                                $return = true;
                                $status = 200;
                                $message = $refcode;
                            }else{
                                $return = false;
                                $status = 200;
                                $message = "failed";
                            }
                    }else{
                        $return = false;
                        $status = 400;
                        $message = "failed";
                    }
                }

                 /**
                 * Update return grid GST value
                 */
                $return_gst = $this->returnMdl->updateGstOnReturnGrid($orderData['gds_order_id']);

                $this->returnMdl->updateReturnOrderStatusonOrderId($orderData['gds_order_id'],$refcode);
                $args = array("ConsoleClass" => 'mail', 'arguments' => array('DmapiReturnOrderTemplate', $returnGridId));

                $invoiceData = $_OrderModel->getInvoiceIdFromOrderId($gds_order_id);
                $invoiceData = $invoiceData[0];
                $InvoiceReference = $_OrderModel->getInvoiceCodefromInvoiceID($invoiceData->gds_order_invoice_id);
                $collectionData['order_id'] = $gds_order_id;
                $collectionData['return_id'] = $returnGridId;
                $return_data['status'] = $status;
                $return_data['message'] = $message;
                $collectionData['invoice'] = $invoiceData->gds_order_invoice_id;
                $collectionData['invoice_reference'] = $invoiceData->invoice_code;
                $collectionData['collected_on'] = date('Y-m-d');
                $collectionData['collected_by'] = $user_id;
                $collectionData['mode_of_payment'] = '';
                $collectionData['reference_num'] = $refcode;
                $collectionData['collection_amount'] = $returnValue;
                $collectionData['gst'] = $return_gst;
                
                $returnVouchers = $this->returnMdl->saveReturnsVoucherGST($returndata,$collectionData);

                return $return_data;
            }else{

                return "No Returns";
            }




        }

    }

    public function getProductTaxInfoById($pr_id,$product_id){
        $tax_details = DB::table("purchase_return_products")
                    ->where('pr_id',$pr_id)
                    ->where('product_id',$product_id)
                    ->first();
        return $tax_details;
    }

    public function getleWhIdByLeId($legal_entity_id){
        $le_wh_id = DB::table("legalentity_warehouses")
                    ->select('le_wh_id')
                    ->where('legal_entity_id',$legal_entity_id)
                    ->where('dc_type',118001)
                    ->first();
        return isset($le_wh_id->le_wh_id) ? $le_wh_id->le_wh_id : 0;
    }

    public function checkSrInvoice($data){
        $sr_inv_no = $data['sr_inv_no'];
        $supplier_id = $data['supplier_id'];
        $pr_le_wh_id = $data['pr_le_wh_id'];
      
        if (!preg_match('/^[A-Za-z]{2}[IV]{2}[0-9]{11}$/',$sr_inv_no) && !preg_match('/^[a-zA-Z0-9]{6}[0-9]{4}[a-zA-Z0-9]{6}$/',$sr_inv_no) ){
            return array("status"=>400,"message"=>"Invalid Invoice No Format!");
        }
        $this->_orderModel = new OrderModel();
        $gds_order_data  = $this->_orderModel->getInvoiceDataFromInvoiceCode($sr_inv_no);
        if(isset($gds_order_data->gds_order_id)){
            $date = DateTime::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s'));
            $invoice_date = DateTime::createFromFormat('Y-m-d H:i:s',$gds_order_data->created_at);
            $interval = $date->diff($invoice_date);
            $diff = $interval->format('%m');
            if($diff > 6){
                return array("status"=>400,"message"=>"Invoice No should not be old than 6 months!");
            }
            $orderData = $this->_orderModel->getOrderDetailById($gds_order_data->gds_order_id);
            $this->returnMdl = new ReturnModel();
            $returnTotal =  $this->returnMdl->getAllReturns($gds_order_data->gds_order_id);
            $returnTotal = json_decode(json_encode($returnTotal),1);
            $returnTotal = array_sum(array_column($returnTotal, "total"));
            $order_grand_total = $gds_order_data->grand_total;
            $order_grand_total -= $returnTotal;
            $order_status_id = $orderData->order_status_id;
            if(!in_array($order_status_id, [17023,17007,17008])){
                return array("status"=>400,"message"=>"Order Should be in Delivered/Completed Status!");
            }
            if($supplier_id == 24766){
                $this->_LegalEntity = new LegalEntity();
                $whDetail = $this->_LegalEntity->getWarehouseById($pr_le_wh_id);
                $wh_le_id = isset($whDetail->legal_entity_id)?$whDetail->legal_entity_id:0;
                if($wh_le_id > 0){
                    $this->_poModel = new PurchaseOrder();
                    $le_type_id = $this->_poModel->getLegalEntityTypeId($wh_le_id);
                    // print_r($supplierInfo);die;                    
                    $apob_data = $this->_poModel->getApobData($wh_le_id);
                    if(count($apob_data)){
                        $supp_le_wh_id = $apob_data->le_wh_id;
                        $supp_le_id = $apob_data->legal_entity_id;
                    }
                    $dc_le_type_id = $this->_poModel->getLegalEntityTypeId($supp_le_id);
                    if($dc_le_type_id == 1016){
                        $apob_data = $this->_poModel->getApobData($supp_le_id);
                        if(count($apob_data)){
                            $supp_le_wh_id = $apob_data->le_wh_id;
                        }
                    }
                }
            }else{
                $supp_le_wh_id = $this->getleWhIdByLeId($supplier_id);
            }
            if($orderData->le_wh_id != $supp_le_wh_id){
                return array("status"=>400,"message"=>"Supplier warehouse should be equal to invoice warehouse");
            }
            if(isset($data['pr_totprice']) && is_array($data['pr_totprice'])){
                $grand_total = array_sum($data['pr_totprice']);
                if($grand_total > $order_grand_total || $order_grand_total <= 0){
                    return array("status"=>400,"message"=>"PR total should be less than invoice value!");
                }
            }

            return array("status"=>200,"message"=>"Success");
        }else{
            return array("status"=>400,"message"=>"Invalid Invoice No!");
        }
    }

    public function saveDocument($docsArr) {
        try {
            $id = DB::table('pr_docs')->insertGetId($docsArr);
            return $id;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function deleteDoc($doc_id) {
        try {
            $query = DB::table('pr_docs');
            $query->where('pr_docs.doc_id', $doc_id)->delete();
            Session::put('prdocs', array_diff(Session::get('prdocs'), [$doc_id]));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function prDocUpdate($pr_id,$docid) {
        try {
            $query = DB::table('pr_docs');
            $query->where('pr_docs.doc_id', $docid);
            $query->update(array('pr_id' => $pr_id));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getprDocs($pr_id) {
        try {
            $query = DB::table('pr_docs');
            $query->where('pr_docs.pr_id', $pr_id);
            return $docs = $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

}
