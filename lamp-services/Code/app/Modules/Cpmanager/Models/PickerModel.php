<?php
  namespace App\Modules\Cpmanager\Models;
  use Illuminate\Database\Eloquent\Model;
  use App\Modules\Orders\Models\OrderModel;
  use App\Modules\Cpmanager\Models\OrderModel as cpOrderModel;
  use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
  use App\Modules\Ledger\Models\LedgerModel;
  use App\Modules\Roles\Models\Role;
  use App\Central\Repositories\CustomerRepo;
  use App\Modules\Cpmanager\Models\MasterLookupModel;
  use App\Modules\Cpmanager\Models\EcashModel;
  use App\Modules\Indent\Models\LegalEntity;
  use App\Modules\Cpmanager\Controllers\accountController;
  use App\Central\Repositories\RoleRepo;
  use DB;  
  use Log;
  use UserActivity;
  use Utility;
  date_default_timezone_set("Asia/Kolkata");
  
 
  class PickerModel extends Model {

    public function __construct() { 
   
      $this->_ecash = new EcashModel(); 
      $this->_roleRepo = new RoleRepo();
    }

    /*
      * Function Name: picklistdetails
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 5th Oct 2016
	   * Modified Date: 11th Jan 2017
      * Modified Date & Reason: 
    */
	
	
	 public function picklistdetails($order_id) {

        $data = array();   
        $order_id = explode(',', $order_id);
       $Orderdata=DB::table("gds_order_products as orderprod")
        ->select(db::raw("orderprod.gds_order_id,orderprod.product_id,orderprod.pname as product_name,
          orderprod.sku,round(orderprod.mrp,2) as mrp,sum(orderprod.qty) as order_qty,
          orderprod.parent_id,order.le_wh_id,
          IFNULL(getFreeBeePrdMpq(orderprod.product_id),0) AS freebee_mpq,
          IFNULL(getFreeBeeQty(orderprod.product_id),0) AS freebee_qty"))
        ->leftJoin('gds_orders as order','order.gds_order_id','=','orderprod.gds_order_id')
       // ->leftJoin('products as prod','prod.product_id','=','orderprod.product_id')
        /*->leftJoin('warehouse_config as bin', function($join)
            {
                $join->on('bin.pref_prod_id','=','prod.product_id');
                $join->on('bin.le_wh_id','=','order.le_wh_id');
            })*/
        ->whereIn('orderprod.gds_order_id',$order_id)            
        ->whereIn('order.order_status_id', array('17020', '17005'))
       // ->orderBy('bin.sort_order')
        ->GROUPBY("orderprod.product_id")                      
        ->get()->all();

    
        if(count($Orderdata) <=0 ) {
                return $data;
        } 
        /*** address*/
          $address=DB::table("legal_entities as le")
        ->select("order.order_code","order.gds_order_id as order_id","le.business_legal_name as shop_name",
          "le.address1 as addr1","le.address2 as addr2","bin.bin_location as dock","area.pjp_name as beat",
          "lewh.lp_wh_name as hub_name","order.le_wh_id")
          ->leftJoin('gds_orders as order','order.cust_le_id','=','le.legal_entity_id')
          ->leftJoin('pjp_pincode_area as area','area.pjp_pincode_area_id','=','order.beat')
          ->leftJoin('gds_order_track as ordertrack','ordertrack.gds_order_id','=','order.gds_order_id')
          //->leftJoin('legalentity_warehouses as lew','lew.le_wh_id','=','order.le_wh_id')
          ->leftJoin('legalentity_warehouses as lewh','lewh.le_wh_id','=','order.hub_id')
          ->leftJoin('cat_bin_mapping as bin','bin.bin_mapping_id','=','ordertrack.dock_area')
          //->where("address.address_type","=",'shipping')    
          ->whereIn("order.gds_order_id",$order_id)      
          ->groupBy("hub_name")      
          ->get()->all();
        $data['address']=$address[0]; 
    
        $orderdetails=json_decode(json_encode($Orderdata),true);    
        $_orderModel = new OrderModel();
        $cancelPrdArr = $_orderModel->getCancelledQtyByOrderId($order_id);  
        $shippedPrdArr = $_orderModel->getShippedQtyWithProductByOrderId($order_id);
        $binArr=array();
        
          for($i=0;$i<sizeof($orderdetails);$i++) {

        $product_id = $orderdetails[$i]['product_id'];
        $orderQty = (int)$orderdetails[$i]['order_qty'];
        $cancelQty = isset($cancelPrdArr[$product_id]) ? (int)$cancelPrdArr[$product_id] : 0;
        $pendingQty = ($orderQty - $cancelQty);   
        $shippedQty = isset($shippedPrdArr[$product_id]) ? (int)$shippedPrdArr[$product_id] : 0;
        $bin_data = DB::table('picking_reserve_bins as prb')
          ->select(['prb.*',DB::raw('GROUP_CONCAT(pack_config SEPARATOR "--") as pack_config'),DB::raw('sum(prb.reserved_qty) as reserved_qty')])
                ->whereIn("prb.order_id",$order_id)
                ->where("prb.product_id",$product_id)
                ->groupBy('prb.product_id')
                ->get()->all();
        $bin_data=json_decode(json_encode($bin_data),true);
        foreach ($bin_data as $key => $value_bin) {
        $pack_config=(isset($value_bin['pack_config']) && !empty($value_bin['pack_config']))?$value_bin['pack_config']:'';
    $pack_config = explode('--', $pack_config);
    $packconfig = array();
    $config=array();
    foreach($pack_config as $packs){
        $pack=json_decode($packs,true);
        if($pack!=""){
          foreach($pack as $key=>$packqty){
              $packconfig[$key]=isset($packconfig[$key])?($packconfig[$key]+$packqty):$packqty;
          }
        }
    }
        $j=0;
       foreach($packconfig as $key => $pack_value) 
       {
           db::enablequerylog();
          $prd_pack_data= DB::Table('product_pack_config as pro_pack')
            ->leftJoin('gds_order_product_pack as order_pack', function($join)
             {
                $join->on('order_pack.product_id','=','pro_pack.product_id');
                $join->on('order_pack.pack_id','=','pro_pack.level');
            })
            ->join('products as pro','pro_pack.product_id','=','pro.product_id')
            ->select(db::raw("pro_pack.level AS pack_level,
              pro_pack.pack_sku_code as ean_number,no_of_eaches,
               SUM((no_of_eaches*order_pack.esu_qty*order_pack.esu)) as pack_order_qty,
             order_pack.esu_qty,order_pack.esu,pro_pack.is_cratable,
              getMastLookupValue(pro_pack.level) as pack_name,
              IFNULL(getProductWeight(pro_pack.product_id,pro_pack.level),0) as weight"))
            ->whereIn('order_pack.gds_order_id',$order_id)
            ->where('order_pack.product_id','=',$product_id)
            ->where('pro_pack.level','=',$key)
            ->groupBy('order_pack.product_id')
            ->first();

           $config[$j] =array(
                 'pack_level'=> $prd_pack_data->pack_level,
                'ean_number'=>$prd_pack_data->ean_number,
                'no_of_eaches'=> $prd_pack_data->no_of_eaches,
                'pack_order_qty'=> $prd_pack_data->no_of_eaches*$pack_value,
                'esu_qty'=> $prd_pack_data->esu_qty,                                                                                                                                                                                                                                                          
                'esu'=> $prd_pack_data->esu,
                'pick_esu_qty'=>$pack_value,
                'is_cratable'=> $prd_pack_data->is_cratable,
                'pack_name'=>$prd_pack_data->pack_name,
                'weight'=> $prd_pack_data->weight
            );
        $j++;           
       }
       $orderId=$orderdetails[$i]['gds_order_id'];
       $orderwh=DB::select(DB::raw("select is_binusing from gds_orders g join legalentity_warehouses l  on l.le_Wh_id = g.le_wh_id where g.gds_order_id = ".$orderId));
        if($orderwh[0]->is_binusing == 0){
          $value_bin['reserved_qty']=$orderQty;
        }
        $data['products'][] = array(
                    'product_id'=>$orderdetails[$i]['product_id'],
                    'product_name'=>$orderdetails[$i]['product_name'], 
                    'sku'=>$orderdetails[$i]['sku'], 'mrp'=>$orderdetails[$i]['mrp'], 
                    'parent_id'=>$orderdetails[$i]['parent_id'], 
                    'freebee_mpq'=>$orderdetails[$i]['freebee_mpq'], 
                    'freebee_qty'=>$orderdetails[$i]['freebee_qty'], 
                    'order_qty'=>$orderQty, 
                    'ship_qty'=>$value_bin['reserved_qty'], 
                    'picked_qty'=>$shippedQty,
                    'sort_order'=>$value_bin['sort_order'],
                    'bin'=>$value_bin['bin_code'],
                    'bin_id'=>$value_bin['bin_id'],
                    'pack_config'=>$config

                    );
        
        $binArr[] = $value_bin['bin_code'];
         unset($config);
        }
        
       
       

       /* if($pendingQty)
        {
            // $orderdetails[$i]['product_id']=24;
            $bin =$this->getBinLocations($orderdetails[$i]['product_id']);

            $binCount = count($bin);

            $remaining = 0;
            foreach ($bin as $bin_data) {
            
               if($pendingQty<=$bin_data['qty'])
               {
               
                  $data['products'][] = array(
                    'product_id'=>$orderdetails[$i]['product_id'],
                    'product_name'=>$orderdetails[$i]['product_name'], 
                    'sku'=>$orderdetails[$i]['sku'], 'mrp'=>$orderdetails[$i]['mrp'], 
                    'parent_id'=>$orderdetails[$i]['parent_id'], 
                    'freebee_mpq'=>$orderdetails[$i]['freebee_mpq'], 
                    'freebee_qty'=>$orderdetails[$i]['freebee_qty'], 
                    'order_qty'=>$orderQty, 
                    'ship_qty'=>$pendingQty, 
                    'picked_qty'=>$shippedQty,
                    'sort_order'=>$bin_data['sort_order'],
                    'bin'=>$bin_data['bin'],
                    'bin_id'=>$bin_data['bin_id']
                  ); 
                  $binArr[] = $bin_data['bin'];
                  $pendingQty = $pendingQty-$bin_data['qty'];//30-20=10
                  break;
               } else {
                  $pendingQty = $pendingQty-$bin_data['qty'];//30-20=10
                  $data['products'][] = array(
                    'product_id'=>$orderdetails[$i]['product_id'],
                    'product_name'=>$orderdetails[$i]['product_name'], 
                    'sku'=>$orderdetails[$i]['sku'], 
                    'mrp'=>$orderdetails[$i]['mrp'], 
                    'parent_id'=>$orderdetails[$i]['parent_id'], 
                    'freebee_mpq'=>$orderdetails[$i]['freebee_mpq'], 
                    'freebee_qty'=>$orderdetails[$i]['freebee_qty'], 
                    'order_qty'=> $orderQty,
                    'ship_qty'=>$bin_data['qty'],
                    'picked_qty'=>$shippedQty,
                    'sort_order'=>$bin_data['sort_order'],
                    'bin'=>$bin_data['bin'],
                    'bin_id'=>$bin_data['bin_id']
                  );
                  $binArr[] = $bin_data['bin'];
                  
               }
            }

            if($pendingQty>0){

              $data['products'][] = array(
                    'product_id'=>$orderdetails[$i]['product_id'],
                    'product_name'=>$orderdetails[$i]['product_name'], 
                    'sku'=>$orderdetails[$i]['sku'], 
                    'mrp'=>$orderdetails[$i]['mrp'], 
                    'parent_id'=>$orderdetails[$i]['parent_id'], 
                    'freebee_mpq'=>$orderdetails[$i]['freebee_mpq'], 
                    'freebee_qty'=>$orderdetails[$i]['freebee_qty'], 
                    'order_qty'=> $orderQty,
                    'ship_qty'=>$pendingQty, 
                    'picked_qty'=>$shippedQty,
                    'sort_order'=>'',
                    'bin'=>'',
                    'bin_id'=>0
                  );
            }
         }*/
       $productsArr[] = $product_id;

        }
        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $key => $row) {
                $sort_data[$key] = $row['sort_order'];
            }
            array_multisort($sort_data, SORT_ASC, $data['products']);
        } else {
            $data['products'] = [];
        }

      /*  $freebie=DB::table("freebee_conf")
          ->select(db::raw("distinct free_prd_id"))
          ->whereIn("free_prd_id",$productsArr)      
          ->get()->all();
        $free_prod=json_decode(json_encode($freebie),true);
      $freebie_data= DB::Table('product_pack_config as pro_pack')
                    ->join('products as pro','pro_pack.product_id','=','pro.product_id')
                    ->select(db::raw("pro_pack.product_id,pro.mrp,pro_pack.level AS pack_level,
                      pro.product_title,pro_pack.pack_sku_code as ean_number,no_of_eaches,
                       (no_of_eaches)as pack_order_qty,
                      pro.sku,0 as esu_qty,pro_pack.esu,pro_pack.is_cratable,
                      (select group_concat(bin.wh_location) from 
                        warehouse_config as bin where bin.pref_prod_id=pro.product_id) 
                    as bin,getMastLookupValue(pro_pack.level) as pack_name,
                    IFNULL(getProductWeight(pro_pack.product_id,pro_pack.level),0) as weight"))
                   ->whereIn('pro_pack.product_id', $free_prod)
                   ->where('pro_pack.level',16001)
                    ->get()->all();

              $prd = DB::Table('product_pack_config as pro_pack')
            ->join('products as pro','pro_pack.product_id','=','pro.product_id')
            ->leftJoin('gds_order_product_pack as order_pack', function($join)
            {
                $join->on('order_pack.product_id','=','pro_pack.product_id');
                $join->on('order_pack.pack_id','=','pro_pack.level');
            })
            ->select(db::raw("pro_pack.product_id,pro.mrp,pro_pack.level AS pack_level,
              pro.product_title,pro_pack.pack_sku_code as ean_number,no_of_eaches,
               (no_of_eaches*order_pack.esu_qty*order_pack.esu)as pack_order_qty,
              pro.sku,order_pack.esu_qty,order_pack.esu,pro_pack.is_cratable,
              (select group_concat(bin.wh_location) from 
                warehouse_config as bin where bin.pref_prod_id=pro.product_id) as bin,getMastLookupValue(pro_pack.level) as pack_name,IFNULL(getProductWeight(pro_pack.product_id,pro_pack.level),0) as weight"))
            ->where('order_pack.gds_order_id','=',$order_id)
          
            ->get()->all();
        
        $data['pack_config'] =$prd; */
       
         $bin_config = DB::Table('warehouse_config as wc')
            ->join('bin_inventory as binv','wc.wh_loc_id','=','binv.bin_id')
            //->join('bin_type_dimensions as bin_dimension','wc.bin_type_dim_id','=','bin_dimension.bin_type_dim_id')
            ->select(db::raw("wc.wh_location as bin_location,
                      wc.wh_loc_id as bin_id,
                binv.qty as bin_qty,binv.product_id as prod_id,0 as pack_level"))
            ->whereIn('wc.wh_location', $binArr)
           // ->where('bin_dimension.bin_type', 109003)
           // ->where('binv.qty','>', 0)
            ->get()->all();
       
        $data['bin_config'] = $bin_config; 
          # data of count of crates ,CFC,bags 
        $orderTrack = DB::table("gds_order_track")
        ->select(DB::raw("SUM(cfc_cnt) as cfc_cnt,SUM(bags_cnt) as bags_cnt,SUM(crates_cnt) as crates_cnt"))
        ->whereIn('gds_order_id',$order_id)
        ->first();
        $data['CFC'] = isset($orderTrack->cfc_cnt) ? (int)$orderTrack->cfc_cnt : 0;
        $data['bags'] = isset($orderTrack->bags_cnt) ? (int)$orderTrack->bags_cnt : 0;
        $data['crates'] = isset($orderTrack->crates_cnt) ? (int)$orderTrack->crates_cnt : 0;  
        return $data;
        
    }  
    
    /*
  * Function Name: getinvoiceidcheck
  * Description: getinvoiceidcheck 
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 20 Oct 2016  
  * Modified Date & Reason:
  */
  public function getinvoiceidcheck($invoice_id)
    {

    $invoice_id=DB::table("gds_invoice_grid as grid")
             ->select("grid.gds_invoice_grid_id")
             ->where("grid.gds_invoice_grid_id","=",$invoice_id)  
             ->get()->all(); 

    return $invoice_id;

    }  
     /*
      * Class Name: getorderdetailbyinvoiceid
      * Description: Function used to get order details of picklist  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 5th Oct 2016
      * Modified Date & Reason: 
      * srinivas jakkula
    */
    
      public function getorderdetailbyinvoice($invoice_id) {
      
		   $address=DB::table("gds_orders as order")
        ->select(db::raw("order.order_code,invoice.gds_invoice_grid_id as invoice_id,
          invoice.invoice_code,invoice.ecash_applied,order.le_wh_id,invoice.created_at as date,
          order.gds_order_id as order_id,order.is_self as is_self,order.shop_name,
          u.mobile_no,IFNULL(le.address1,'')as address1,
          IFNULL(le.address2,'') as address2,IFNULL(ppa.pjp_name,'') as route,
          IFNULL(cfc_cnt,'') as cfc_cnt,IFNULL(bags_cnt,'') as bags_cnt,
          IFNULL(crates_cnt,'') as crates_cnt,order.order_status_id as status,
          IFNULL(order.discount,0) as discount,IFNULL(order.discount_amt,0)  as discount_amt,
          IFNULL(order.discount_type,'') as discount_type,
          order.order_date as order_date,order.cust_le_id,order.hub_id,getMastLookupValue(gop.payment_method_id) as payment_method_id,order.mfc_id,order.instant_wallet_cashback,order.discount_before_tax"))
          ->leftJoin('legal_entities as le','order.cust_le_id','=','le.legal_entity_id')
          ->leftJoin('gds_order_track as ordertrack','ordertrack.gds_order_id','=','order.gds_order_id')
         ->leftJoin('gds_orders_payment as gop','gop.gds_order_id','=','order.gds_order_id')
         ->leftJoin('gds_invoice_grid as invoice','invoice.gds_order_id','=','order.gds_order_id')
         ->leftJoin('pjp_pincode_area as ppa','ppa.pjp_pincode_area_id','=','order.beat')
         ->leftJoin('users as u','u.user_id','=','order.created_by')
          ->where("invoice.gds_invoice_grid_id","=",$invoice_id)    
          ->get()->all(); 
        $data['address']=$address[0]; 
        $data['address']->send_ff_otp = 0;;
        if($data['address']->is_self == 0){
          $this->_LegalEntity = new LegalEntity();
          $warehouse_data = $this->_LegalEntity->getWarehouseById($data['address']->le_wh_id);
          $data['address']->send_ff_otp = $warehouse_data->send_ff_otp;
        }
        $delivery_bydist = DB::table('master_lookup as ml')->select(DB::raw("description"))->where("ml.mas_cat_id", "=",78) ->where("ml.is_active", "=", 1)->where('ml.value',"=",78008)->get()->all();
        if(!empty($delivery_bydist))
        {  
        $data['address']->delivery_bydist=(int)$delivery_bydist[0]->description;
        }else{
        $data['address']->delivery_bydist=0;
        }

        $data['payment_method'] = $address[0]->payment_method_id;
        $customer_type=$this->_ecash->getUserCustomerType($address[0]->cust_le_id);

        if($customer_type==3013)
        {    
         $data['address']->is_premium=1;
        }else{
          $data['address']->is_premium=0;
        }
        
        $epc_minimumorder_value= DB::table('ecash_creditlimit')->select(DB::raw("minimum_order_value"))->where("customer_type", "=", 3013)->first();
        if(!empty($epc_minimumorder_value))
        {  
        $data['address']->premium_ordervalue=(int)$epc_minimumorder_value->minimum_order_value;
        }
          $user_id=$this->_ecash->getUserIdBasedLegalEntityId($address[0]->cust_le_id);
          $data['address']->available_cashabck= $this->_ecash->getExistingEcash($user_id);
          $data['address']->ecash_applied = (isset($address[0]->ecash_applied) && $address[0]->ecash_applied!='')?(float)$address[0]->ecash_applied:0;
          if($data['address']->instant_wallet_cashback == 0){
            $this->paymentmodel = new \App\Modules\Orders\Models\PaymentModel;
            $pendingCbk = $this->paymentmodel->getPendingCashback($user_id);
            $data['address']->ecash_applied += $pendingCbk;
          }

          $_orderModel = new cpOrderModel();
          $eCash = $_orderModel->getUserEcash($user_id);
          $user_credit_limit = (isset($eCash->creditlimit)) ? $eCash->creditlimit : 0;
          $wallet_cashback = (isset($eCash->cashback)) ? $eCash->cashback : 0; //if any order delivered this value will be -ve
          $undeliveredvalue = $_orderModel->custUnDeliveredOrderValue($address[0]->cust_le_id);
          $data['user_credit_limit'] = $user_credit_limit;
          $data['available_credit_limit'] = $user_credit_limit+$wallet_cashback-$undeliveredvalue;

        /*** order details*/
      $Invoicedata=DB::table("gds_orders as go")
         -> select(db::raw("gop.product_id AS product_id,IFNULL(gop.parent_id,'0') as parent_id,gop.pname as product_name,gop.sku AS sku,IFNULL(gop.star,'') AS star,
        gop.qty AS ordered_qty,getInvoicePrdQty (gop.gds_order_id,gop.product_id)  AS invoiced_qty,
        IFNULL(getFreeBeePrdMpq(gop.product_id),0) AS freebee_mpq,
        IFNULL(getFreeBeeQty(gop.product_id),0) AS freebee_qty,
        getReturnPrdQty (gop.gds_order_id,gop.product_id) as return_qty,gop.mrp,prod.esu,
        IFNULL(gop.discount,0) as discount,IFNULL(gop.discount_amt,0)  as discount_amt,
        IFNULL(gop.discount_type,'') as discount_type"))
         ->leftJoin('gds_order_products as gop', 'go.gds_order_id', '=', 'gop.gds_order_id')
         ->leftJoin('products as prod', 'prod.product_id', '=', 'gop.product_id')
        ->leftJoin('gds_invoice_grid as invgrid','invgrid.gds_order_id','=','gop.gds_order_id')
        ->leftJoin('gds_order_invoice as invinvoice','invinvoice.gds_invoice_grid_id','=','invgrid.gds_invoice_grid_id')
        ->leftJoin('gds_invoice_items as invoice','invoice.gds_order_invoice_id','=','invinvoice.gds_order_invoice_id')
        ->where('gop.gds_order_id','=',$address[0]->order_id)   
       ->where("invgrid.gds_invoice_grid_id","=",$invoice_id)   
        ->having("invoiced_qty" , ">", 0) 
        ->GROUPBY("go.gds_order_id","gop.product_id")
         ->get()->all();
        $orderModel_orderModule = new OrderModel();
        foreach($Invoicedata as $key => $product){
            //$unitprice = $orderModel_orderModule->getUnitPricesTaxAndWithoutTax($address[0]->order_id,$product->product_id);
            $unitprice =   $orderModel_orderModule->getUnitPricesTaxAndWithoutTaxForLp($address[0]->order_id,$product->product_id);
            $Invoicedata[$key]->singleUnitPrice = $unitprice['singleUnitPrice'];
            $Invoicedata[$key]->singleUnitPriceWithtax = $unitprice['singleUnitPriceWithtax'];
            $Invoicedata[$key]->singleUnitPriceBeforeTax = $unitprice['singleUnitPriceBeforeTax'];
            $Invoicedata[$key]->tax_percentage = $unitprice['tax_percentage'];
        }
        $data['products']=$Invoicedata;
        $discountsModel = new MasterLookupModel();
        $order_date = isset($address[0]->order_date)?($address[0]->order_date):date('Y-m-d H:i:s');
        $data['discounts']= $discountsModel->getDiscounts($order_date);
        DB::table('gds_order_track as got')->where('got.gds_order_id' ,$address[0]->order_id)->update(array('delivery_start_time' => date('Y-m-d H:i:s')));  

        return $data;
    }  


          /*
  * Function Name: getPickOrderList
  * Description: getPickupOrderList function is used to get all the orders based on picker_id and sheduled date
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 5 Oct 2016  
  * Modified Date & Reason:
  */
 public function getPickOrderList($user_id,$date,$status_id,$sort_id,$type)
  {
        $status_id=explode(',',$status_id);

    $pick_list = DB::table('gds_orders as go')
         ->select(DB::raw("group_concat(go.order_code) as order_code,got.pick_code,got.pick_type,group_concat(go.gds_order_id) as gds_order_id,
          go.shop_name,getMastLookupValue(go.order_status_id) as status,go.order_status_id
          ,go.firstname as fname,le.address1
          ,IFNULL(le.address2,'') address2,u.firstname as ff_name,u.mobile_no as ff_mobileno,
          IFNULL(ppa.pjp_name,'') AS beat,lewh.lp_wh_name as hub_name,go.le_wh_id,
          IFNULL(cp.officename,'') as area,
          IFNULL(cbm.bin_location,'') as dock_area"))
           ->leftJoin('legal_entities as le','le.legal_entity_id','=','go.cust_le_id')
           ->leftJoin('customers as cust','cust.le_id','=','le.legal_entity_id')
           ->leftJoin('cities_pincodes as cp','cust.area_id','=','cp.city_id')
           ->Join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
         // ->leftJoin('legalentity_warehouses as lew','lew.le_wh_id','=','go.le_wh_id')
           ->leftJoin('legalentity_warehouses as lewh','lewh.le_wh_id','=','go.hub_id')
           ->leftJoin('cat_bin_mapping as cbm','cbm.bin_mapping_id','=','got.dock_area')
           ->leftJoin('users as u','u.user_id','=','go.created_by')
           ->leftJoin('pjp_pincode_area as ppa','ppa.pjp_pincode_area_id','=','go.beat')
         // ->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->whereIn("go.order_status_id",$status_id)
          ->where("got.picker_id", "=",$user_id)
          ->where(db::raw("DATE(scheduled_piceker_date)"),$date)
          ->groupBy('go.gds_order_id');

        if($sort_id==1){
         if($type==0)
         {
          
        $sort_column="ppa.pjp_name";
        $sort_by = "asc";

         }else{
         
        $sort_column="ppa.pjp_name";
        $sort_by = "desc";

         } 
        
      }elseif ($sort_id==2) {
         if($type==0)
         {
          
        $sort_column="cp.officename";
        $sort_by = "asc";

         }else{
         
        $sort_column="cp.officename";
        $sort_by = "desc";

         } 
      }elseif ($sort_id==3) {
          if($type==0)
         {
          
        $sort_column="u.firstname";
        $sort_by = "asc";

         }else{
         
        $sort_column="u.firstname";
        $sort_by = "desc";

         } 
      }elseif ($sort_id==4) {
         if($type==0)
         {
          
        $sort_column="cbm.bin_location";
        $sort_by = "asc";

         }else{
         
        $sort_column="cbm.bin_location";
        $sort_by = "desc";

         } 
      }
    
     if(isset($sort_column)){
        
           $last_result  = $pick_list
                        ->orderBy($sort_column,$sort_by)
                        ->get()->all();    
      }else{
         $last_result  = $pick_list
                          ->get()->all();
      }

       
    return $last_result;

    }



 /*
  * Function Name: getPickerCount
  * Description: getTotalCount function is used to  count of total orders to be picked and already picked
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 7 Oct 2016  
  * Modified Date & Reason:
  */
  public function getPickerCount($user_id,$date)
    {
       
       $pending = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,go.order_status_id status_id,
         getMastLookupValue(go.order_status_id) status"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->leftJoin('master_lookup as ml','ml.value','=','go.order_status_id')
          ->where("got.picker_id",$user_id)
          ->where("go.order_status_id",17020)
          ->where(db::raw("DATE(scheduled_piceker_date)"),$date)
          ->get()->all();
    
     if($pending[0]->count==0)
     {
        $pending[0]->status_id=17020;
        $pending[0]->status='PICKLIST GENERATED';
        $data[]=$pending[0];

     }else{

       $data[]= $pending[0];
     }


     //$status='17020,17005';
    // $status=explode(',',$status);

    $total = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(go.gds_order_id)  AS count
          ,0  as status_id,
          'ALL' as status"))
          
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->where("got.picker_id",$user_id)
         // ->whereIn("go.order_status_id",$status)
          ->where(db::raw("DATE(scheduled_piceker_date)"),$date)
          ->get()->all();

     if($total[0]->count==0)
     {
        $total[0]->status_id=0;
        $total[0]->status='ALL';
        $data[]=$total[0];

     }else{

        $data[]= $total[0];
     }
       
   

     $picked = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,go.order_status_id as status_id,
          getMastLookupValue(go.order_status_id) as  status"))          
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->where("got.picker_id",$user_id)
          ->where("go.order_status_id",17005)
         ->where(db::raw("DATE(scheduled_piceker_date)"),$date)
          ->get()->all();
      
 //$picked[0]=array();
        if($total[0]->count==0)
     {
        $picked[0]->status_id=17005;
        $picked[0]->status='READY TO DISPATCH';
        $data[]=$picked[0];

     }else{

        $picked[0]->status_id=17005;
        $picked[0]->status='READY TO DISPATCH';
        $picked[0]->count=$total[0]->count-$pending[0]->count;
        $data[]=$picked[0];
     }
       
    

     return $data;

    }
      

  /*
  * Function Name: getDeliverOrderList
  * Description: getDeliverOrderList function is used to get all the orders based on delivery_id and delivered date
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 5 Oct 2016  
  * Modified Date & Reason:
  */
 public function getDeliverOrderList($user_id,$date,$status_id,$sort_id,$type,$dl_lat=0,$dl_long=0)
  {


    $status_id=explode(',',$status_id);
    $distinceQuery = "fn_lat_long(le.latitude,le.longitude,lewh.latitude,lewh.longitude) AS delivery_distance";
    if($sort_id == 3)
      $distinceQuery = "fn_lat_long(le.latitude,le.longitude,lewh.latitude,lewh.longitude) AS delivery_distance";
    else if($sort_id == 4)
      $distinceQuery = "fn_lat_long(le.latitude,le.longitude,$dl_lat,$dl_long) AS delivery_distance";

    $deliver_list = DB::table('gds_orders as go')
         ->select(DB::raw("distinct go.order_code,go.gds_order_id,
          gig.gds_invoice_grid_id as invoice_order_no ,
          le.business_legal_name as shop_name,getMastLookupValue(go.order_status_id) as status,
          go.order_status_id,
          go.firstname,le.address1,
          IFNULL(le.address2,'') address2,go.phone_no as mobile_no,
          IFNULL(u.mobile_no,'') ff_mobileno,IFNULL(u.firstname,'') ff_name,
          IFNULL(ppa.pjp_name,'') as beat,go.le_wh_id,lewh.lp_wh_name as hub_name,cfc_cnt,bags_cnt,crates_cnt,
          gig.grand_total as invoice_amount,le.longitude,le.latitude,le.pincode,le.city,
          $distinceQuery"))
          ->leftJoin('users as u','u.user_id','=','go.created_by')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->leftJoin('legal_entities as le','le.legal_entity_id','=','go.cust_le_id')
          ->Join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
         // ->leftJoin('legalentity_warehouses as lew','lew.le_wh_id','=','go.le_wh_id')
           ->leftJoin('legalentity_warehouses as lewh','lewh.le_wh_id','=','go.hub_id')
          ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->leftJoin('pjp_pincode_area as ppa','ppa.pjp_pincode_area_id','=','go.beat')          
          ->whereIn("go.order_status_id",$status_id)
          ->where("got.delivered_by", "=",$user_id)
          ->where(db::raw("DATE(got.delivery_date)"),$date);

        /* if($status_id[0]!='17021')
          {

           $deliver_list=$deliver_list->where(db::raw("DATE(got.delivery_date)"),$date);
          }*/


         if($sort_id==1){
         if($type==0)
         {
          
        $sort_column="ppa.pjp_name";
        $sort_by = "asc";

         }else{
         
        $sort_column="ppa.pjp_name";
        $sort_by = "desc";

         } 
        
      }elseif ($sort_id==2) {
         if($type==0)
         {
          
        $sort_column="le.business_legal_name";
        $sort_by = "asc";

         }else{
         
        $sort_column="le.business_legal_name";
        $sort_by = "desc";

         } 
      }elseif ($sort_id==3) {
         if($type==0)
         {
          
        $sort_column="delivery_distance";
        $sort_by = "asc";

         }else{
         
        $sort_column="delivery_distance";
        $sort_by = "desc";

         } 
      }elseif ($sort_id==4) {
         if($type==0)
         {
          
        $sort_column="delivery_distance";
        $sort_by = "asc";

         }else{
         
        $sort_column="delivery_distance";
        $sort_by = "desc";

         } 
      }

     if(isset($sort_column)){
        
           $last_result  = $deliver_list
                        ->orderBy($sort_column,$sort_by)
                        ->get()->all();    
      }else{
         $last_result  = $deliver_list
                          ->get()->all();
      }

    return $last_result;

    }


 /*
  * Function Name: getDeliveryCount
  * Description: getDeliveryCount function is used to  count of pending orders to be picked
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 7 Oct 2016  
  * Modified Date & Reason:
  */
  public function getDeliveryCount($user_id,$date)
    {

    $pending = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,17021 as
          status_id,
          'INVOICED' as  status,sum(grand_total) as amounttotal"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->rightJoin('master_lookup as ml','ml.value','=','go.order_status_id')
           ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->where("got.delivered_by",$user_id)
         // ->where("go.order_status_id",17021)
          ->whereRaw('FIND_IN_SET(go.order_status_id,"17021,17026,17025")')
          ->where(db::raw("DATE(got.delivery_date)"),$date)
          ->get()->all();

     if($pending[0]->count==0)
     {
        $pending[0]->status_id=17021;
        $pending[0]->status='INVOICED';
        $pending[0]->amounttotal=0;
        $data[]=$pending[0];

     }else{

         $data[]= $pending[0];
     }
       

     $status='17007,17021,17023,17014,17022,17025,17026';
     $status=explode(',',$status);

    $total = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,0
          as status_id,
         'ALL' as  status,sum(grand_total) as amounttotal"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
           ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->where("got.delivered_by",$user_id)
          ->whereIn("go.order_status_id",$status)
          ->where(db::raw("DATE(got.delivery_date)"),$date)
          ->get()->all();
       
      if($total[0]->count==0)
     {
        $total[0]->status_id=0;
        $total[0]->status='ALL';
        $total[0]->amounttotal=0;
        $data[]=$total[0];

     }else{

      $data[]= $total[0];
     }

  $delivered = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,go.order_status_id as  status_id,
          getMastLookupValue(go.order_status_id) as status,sum(grand_total) as amounttotal"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
           ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->where("got.delivered_by",$user_id)
          ->where("go.order_status_id",17007)
          ->where(db::raw("DATE(got.delivery_date)"),$date)
          ->get()->all();

   if($delivered[0]->count==0)
     {
        $delivered[0]->status_id=17007;
        $delivered[0]->status='DELIVERED';
        $delivered[0]->amounttotal=0;
        $data[]=$delivered[0];

     }else{

     $data[]= $delivered[0];
     }
       

       $partial_returned = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,go.order_status_id as status_id,
          getMastLookupValue(go.order_status_id) as status,sum(grand_total) as amounttotal"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
           ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->where("got.delivered_by",$user_id)
          ->where("go.order_status_id",17023)
          ->where(db::raw("DATE(got.delivery_date)"),$date)
          ->get()->all();

       
   if($partial_returned[0]->count==0)
     {
        $partial_returned[0]->status_id=17023;
        $partial_returned[0]->status='PARTIAL RETURNED';
        $partial_returned[0]->amounttotal=0;
        $data[]=$partial_returned[0];

     }else{

     
    $data[]= $partial_returned[0];
     }
       

    $hold = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,go.order_status_id as status_id,
          getMastLookupValue(go.order_status_id) as  status,sum(grand_total) as amounttotal"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->where("got.delivered_by",$user_id)
          ->where("go.order_status_id",17014)
          ->where(db::raw("DATE(got.delivery_date)"),$date)
          ->get()->all();

     if($hold[0]->count==0)
     {
        $hold[0]->status_id=17014;
        $hold[0]->status='HOLD';
        $hold[0]->amounttotal=0;
        $data[]=$hold[0];

     }else{

     
    $data[]= $hold[0];
     }
      
    

       $returned = DB::table('gds_order_track as got')
         ->select(DB::raw("COUNT(gds_order_code)  AS count,IFNULL(go.order_status_id,17022) status_id,
         getMastLookupValue(go.order_status_id) as status,sum(grand_total) as amounttotal"))
          ->Join('gds_orders as go','go.gds_order_id','=','got.gds_order_id')
          //->leftJoin('master_lookup as ml','ml.value','=','go.order_status_id')
           ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->where("got.delivered_by",$user_id)
          ->where("go.order_status_id",17022)
          ->where(db::raw("DATE(got.delivery_date)"),$date)
          ->get()->all();

      if($returned[0]->count==0)
     {
        $returned[0]->status_id=17022;
        $returned[0]->status='RETURNED';
        $returned[0]->amounttotal=0;
        $data[]=$returned[0];

     }else{

      $data[]= $returned[0];
     }


    return $data;

    }
    
    
    
    /*
  * Function Name: getProductbyBarcode
  * Description: getProductbyBarcode function is used to  count of pending orders to be picked
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 7 Oct 2016  
  * Modified Date & Reason:
  */
  public function getProductbyBarcode($user_id,$pack_sku_code,$offset_limit)
    {

    $address=DB::table("product_pack_config as ppc")
        ->select("gop.pname as product_name","gop.mrp","gop.qty","gop.product_id","ppc.pack_sku_code","gop.sku as sku_code")
          ->leftJoin('gds_order_products as gop','gop.product_id','=','ppc.product_id')
          ->leftJoin('gds_order_track as got','got.gds_order_id','=','gop.gds_order_id')
          ->where("ppc.pack_sku_code","=",$pack_sku_code) 
           ->Orwhere("gop.sku","=",$pack_sku_code)
           ->where("ppc.effective_date","<", date("Y-m-d H:i:s"))  
            ->where("got.picker_id",$user_id)
            ->orderBy("ppc.effective_date","desc")
            ->take($offset_limit)
          ->get()->all(); 


    return $address;

    }
    
    

      /*
  * Function Name: getGrandTotal
  * Description: getGrandTotal function  used to get complete invoice total of all the order
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 5 Oct 2016  
  * Modified Date & Reason:
  */
  public function getGrandTotal($user_id,$date)
  {

        $status_id='17007,17021,17023';
        $status_id=explode(',',$status_id);

    $total = DB::table('gds_orders as go')
         ->select(DB::raw("sum(grand_total) as total"))
          ->Join('users as u','u.user_id','=','go.gds_cust_id')
          ->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->Join('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
          ->Join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
          ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->whereIn("go.order_status_id",$status_id)
          ->where("got.delivered_by", "=",$user_id)
         // ->where(db::raw("DATE(got.delivery_date)"),$date)
         // ->grouBy('go.gds_order_id')
          ->get()->all();

    return $total[0]->total;

    }

 /*
  * Function Name: getProductbyBarcode
  * Description: getProductbyBarcode function is used to  count of pending orders to be picked
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 17 Oct 2016  
  * Modified Date & Reason:
  */
  public function getorderId($order_id)
    {

    $order_id=DB::table("gds_orders as go")
             ->select("gds_order_id")
             ->where("go.gds_order_id","=",$order_id)  
             ->get()->all(); 
 
  
    return $order_id;

    }

    /*
  * Function Name: getPaymentMethod
  * Description: getPaymentMethod function 
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 17 Oct 2016  
  * Modified Date & Reason:
  */
  public function getPaymentMethod()
  {

   return  $result = DB::table('master_lookup as ml')
                     ->select(DB::raw("ml.value,ml.master_lookup_name"))
                     ->where("ml.mas_cat_id", "=",22)
                     ->whereIn("ml.is_active",[1,3])
                     ->get()->all();


    }
    
   /*
  * Function Name: getCollectiondetails
  * Description: getCollectiondetails function is used to  count of pending orders to be picked
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 17 Oct 2016  
  * Modified Date & Reason:
  */
  public function getCollectiondetails($data)
    {
   
 
     // foreach($data['products'] as $val) {
          //get ship grid id
      if(!empty($data)){
          $gds_ship_grid_id=DB::table("gds_ship_grid as gsg")
             ->select("gds_ship_grid_id as gds_ship_grid_id")
             ->where("gsg.gds_order_id","=",$data['order_id'])  
             ->get()->all(); 
      
     foreach($gds_ship_grid_id as $val) {
           
        $val->gds_ship_grid_id;
                

    foreach($data['products'] as $val1) {
            //update delivered qty
            DB::table('gds_ship_products as gsp')  
                    ->where('gsp.product_id' ,"=",$val1['product_id'])
                    ->where('gsp.gds_ship_grid_id','=', $val->gds_ship_grid_id)
            ->update(array('qty' =>  $val1['delivered_qty']));
            
             DB::table('gds_returns as gs')  
                    ->where('gs.product_id' ,"=",$val1['product_id'])
                    ->where('gs.gds_order_id','=', $data['order_id'])
            ->update(array('qty' =>  $val1['return_qty']));

    }
               
     }
     
     
      }          

      $address=DB::table("gds_orders_addresses as address")
        ->select("order.order_code","ordertrack.created_at as date","address.gds_order_id as order_id","order.shop_name","order.order_status_id as status")
          ->leftJoin('gds_orders as order','order.gds_order_id','=','address.gds_order_id')
          ->leftJoin('gds_order_track as ordertrack','ordertrack.gds_order_id','=','order.gds_order_id')
          ->where("ordertrack.gds_order_id","=",$data['order_id']) 
          ->get()->all(); 

        
        
        $data['address']=$address[0]; 

         $total=DB::table("gds_orders as go")
         -> select(db::raw("go.total as ordervalue,sum(invoice.row_total_incl_tax) as invoicevalue"))
         ->leftJoin('gds_order_products as gop', 'go.gds_order_id', '=', 'gop.gds_order_id')  
        ->leftJoin('gds_invoice_items as invoice','invoice.gds_order_id','=','gop.gds_order_id')
         //->leftJoin('gds_ship_products as gsp','gsp.gds_ship_grid_id','=',$val->gds_ship_grid_id)
          //->leftJoin('gds_returns as gs','gs.gds_order_id','=','gop.gds_order_id')
          //->where('gsp.product_id' ,"=",$val1['product_id'])             
        ->where("invoice.gds_order_id","=",$address[0]->order_id) 
        ->where('gop.gds_order_id','=',$address[0]->order_id)       
        ->GROUPBY("go.gds_order_id","gop.product_id")
         ->get()->all();

      

        $data['products']=$total;
        return $data;

        
        
        

    }
     /*
  * Function Name: getBagsbybarcode
  * Description: getBagsbybarcode function is used to  count of pending orders to be picked
  * Author: Ebutor <info@ebutor.com
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 17 Oct 2016  
  * Modified Date & Reason:
  */
      public function getBagsbybarcode($container_barcode)
    {
     

    $address=DB::table("picker_container_mapping as pcm")
        ->select(db::raw("pcm.barcode as bag,sum(pcm.qty) as totalitems"))
          //->leftJoin('gds_orders as go','go.gds_order_id','=','pcm.order_id')
          //->leftJoin('master_lookup as ml','pcm.container_type','=','ml.master_lookup_name')
          ->where("pcm.container_barcode","=",$container_barcode)     
          ->get()->all(); 

    return $address;

    }

   /*
  * Function Name: saveContainerData
  * Description: saveContainerData function is used to  count of pending orders to be picked
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 17 Oct 2016  
  * Modified Date & Reason:
  */
    public function saveContainerData($array,$user_id)
    {
      $getorsettransactionflag=isset($array->needTransactions)?$array->needTransactions:true;
      if($getorsettransactionflag){
          DB::beginTransaction();
      }
        try
        {
            // Log::info('pickercontainer inside');
            //Log::info($array);
            if( empty($array->cancel_reason_id) || $array->cancel_reason_id=='' ){
                foreach ($array->container as $key => $value) 
                {
                    foreach ($value->products as $key => $value1) {

                        if($value1->qty >0) 
                        { 
                         
                            if(!empty($value1->bin_code))
                            {
                                foreach ($value1->bin_code as $key => $value2) 
                                {
                                    $pickedQty = $value1->qty;

                                    //calculate picked item weight according to pack types
                                    $sqlPackConfig = DB::select(DB::raw("select product_id, level, no_of_eaches, weight_uom, weight
                                            from product_pack_config
                                            where product_id = :prod and is_cratable=1 order by no_of_eaches desc"),array("prod"=>$value1->product_id));
                                    $sqlPackConfig = json_decode(json_encode($sqlPackConfig), true);
                                    $packArr = array();
                                    foreach($sqlPackConfig as $pack){
                                      if($pack['no_of_eaches']>0 && $pickedQty>0 && $pack['no_of_eaches']<=$pickedQty) {
                                        $packArr[] = array(
                                                  "packLevel"=>$pack['level'],
                                                  "packCount"=>floor($pickedQty / $pack['no_of_eaches'])
                                                );
                                        $pickedQty = ($pickedQty % $pack['no_of_eaches']);

                                      }
                                    }

                                    $prodWeight = 0.00;
                                    foreach($packArr as $eachPack){
                                      $sql = DB::select(DB::raw("select getProductWeight(:prod,:packLevel) as weight"), array("prod"=>$value1->product_id, "packLevel"=>$eachPack['packLevel']));
                                      $sql = json_decode(json_encode($sql), true);
                                      if(!empty($sql))
                                          $sql = $sql[0];
                                      
                                      if(isset($sql['weight']) && $sql['weight']!='')
                                          $prodWeight += $sql['weight']*$eachPack['packCount'];
                                      else
                                          $prodWeight += 0.00;
                                    }

                                    DB::table('picker_container_mapping')
                                        ->insert(['le_wh_id' => $array->le_wh_id,
                                        'order_id' =>   $array->order_id, 
                                        'productid' => $value1->product_id,
                                        'qty' => $value2->bin_qty,
                                        'product_barcode'=> $value1->prod_barcode,
                                        'picked_by'=> $user_id,
                                        'container_barcode'=> $value->cont_barcode,
                                        'container_type'=>$value->container_type,
                                        'bin_code'=>$value2->bin_location,
                                        'weight'=>$prodWeight,
                                        'weight_uom'=>86002]);
                                    $orderwh=DB::select(DB::raw("select is_binusing from gds_orders g join legalentity_warehouses l  on l.le_Wh_id = g.le_wh_id where g.gds_order_id = ".$array->order_id));
                                    if($orderwh[0]->is_binusing == 0){
                                      //$value_bin['reserved_qty']=$orderQty;
                                    }else{
                                      $quantity= $this->getBinQty($value2->bin_id,$value1->product_id);
                                      $oldVal = $quantity->qty;
                                      $newVal = $quantity->qty-$value2->bin_qty;
                                      $uniquevalues = array('product_id'=>$value1->product_id,
                                        'bin_id'=>$value2->bin_id,
                                        'order_id'=>$array->order_id);

                                      UserActivity::userActivityLog("BinInventoryUpdate", $newVal, "Update Bin Qty (deduction)" , $oldVal, $uniquevalues);
                                      DB::table('bin_inventory')
                                        ->where('bin_id',$value2->bin_id)
                                        ->where('product_id',$value1->product_id)
                                        ->decrement('qty',$value2->bin_qty);

                                    }
                                   // print_r($value2);exit;
                                   

                                      UserActivity::userActivityLog("PickerContainerMapping", $array->order_id, "Get Picker Container mapping Raw query" , $array->order_id, "insert into picker_container_mapping(le_wh_id,order_id,productid,product_barcode,qty,container_barcode,container_type,bin_code,weight,weight_uom,picked_by) value ('".$array->le_wh_id."','".$array->order_id."','".$value1->product_id."','". $value1->prod_barcode."','".$value->container_type."','".$value2->bin_location."','".$user_id."','".$prodWeight."',86002,'".$array->order_id."')");

                                }
                            }else{

                                $pickedQty = $value1->qty;

                                //calculate picked item weight according to pack types
                                $sqlPackConfig = DB::select(DB::raw("select product_id, level, no_of_eaches, weight_uom, weight
                                        from product_pack_config
                                        where product_id = :prod and is_cratable=1 order by no_of_eaches desc"),array("prod"=>$value1->product_id));
                                $sqlPackConfig = json_decode(json_encode($sqlPackConfig), true);

                                $packArr = array();
                                foreach($sqlPackConfig as $pack){
                                  if($pack['no_of_eaches']>0 && $pickedQty>0 && $pack['no_of_eaches']<=$pickedQty) {
                                    $packArr[] = array(
                                              "packLevel"=>$pack['level'],
                                              "packCount"=>floor($pickedQty / $pack['no_of_eaches'])
                                            );
                                    $pickedQty = ($pickedQty % $pack['no_of_eaches']);

                                  }
                                }

                                $prodWeight = 0.00;
                                foreach($packArr as $eachPack){
                                  $sql = DB::select(DB::raw("select getProductWeight(:prod,:packLevel) as weight"), array("prod"=>$value1->product_id, "packLevel"=>$eachPack['packLevel']));
                                  $sql = json_decode(json_encode($sql), true);
                                  if(!empty($sql))
                                      $sql = $sql[0];
                                  
                                  if(isset($sql['weight']) && $sql['weight']!='')
                                      $prodWeight += $sql['weight']*$eachPack['packCount'];
                                  else
                                      $prodWeight += 0.00;
                                }
                                
                                DB::table('picker_container_mapping')->insert(['le_wh_id' => $array->le_wh_id,
                                'order_id' =>   $array->order_id, 
                                'productid' => $value1->product_id,
                                'qty' => $value1->qty,
                                'product_barcode'=> $value1->prod_barcode,
                                'picked_by'=> $user_id,
                                'container_barcode'=> $value->cont_barcode,
                                'container_type'=>$value->container_type,
                                'bin_code'=>'',
                                'weight'=>$prodWeight,
                                'weight_uom'=>86002]);

                            }
                        }
                    }
                }
                $orderwh=DB::select(DB::raw("select is_binusing from gds_orders g join legalentity_warehouses l  on l.le_Wh_id = g.le_wh_id where g.gds_order_id = ".$array->order_id));
                if($orderwh[0]->is_binusing == 1){
                  $this->updateReserveQty($array->order_id);
                }
            }
            if($getorsettransactionflag){
              DB::commit();
            }      
            return 1;

        } catch (Exception $ex) {
          if($getorsettransactionflag){
              DB::rollback();
            }
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
   
   /*
  * Function Name: getOrdercode
  * Description: function is used to  retrive order code of particular order
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 21st Oct 2016  
  * Modified Date & Reason:
  */
    public function getOrdercode($order_id)
    {

      $result=DB::table('gds_orders')->select('order_code')
      ->where('gds_order_id','=',$order_id)
      ->get()->all();

      if($result== ''){

        $ordercode='';
      }else
      {
        $ordercode=$result[0]->order_code;
      }

      return $ordercode;



    }
    
 
 
 
 Public function ChangeOrderstatus($data,$status) {
      
        
      /*
       * Return Partially
       */

       if($status == 17023) {
          DB::table('gds_orders')  
                    ->where('gds_order_id' ,"=",$data['order_id'])                 
                    ->update(array('order_status_id' => $status));

          
          for($i=0;$i<sizeof($data['returns']);$i++) {       

           $produc_ID = $data['returns'][$i]['product_id']; 

           DB::table('gds_order_products')  
          ->where('gds_order_id',"=",$data['order_id'])   
          ->where('product_id',"=",$produc_ID)               
          ->update(array('order_status' => $status));

          }


       }
       /*
       * delivered & Hold status
       */ 

       else {
          DB::table('gds_orders')  
                    ->where('gds_order_id' ,"=",$data['order_id'])                 
                    ->update(array('order_status_id' => $status));
        $productdata = isset($data['products_info'])?$data['products_info']:[];
        foreach ($productdata as $key => $product) {
          // if($product['invoiced_qty']!=$product['ordered_qty']){
          //   $status = 17023;
          //   $product_id = $product['product_id'];
          //   DB::table('gds_order_products')  
          //           ->where('gds_order_id' ,"=",$data['order_id'])
          //           ->where('product_id',$product_id)                 
          //           ->update(array('order_status' => $status));
          // }else{
            $status = 17007;
            $product_id = $product['product_id'];
            DB::table('gds_order_products')  
                    ->where('gds_order_id' ,"=",$data['order_id'])
                    ->where('product_id',$product_id)                 
                    ->update(array('order_status' => $status));
          //}
        }
        //$productids = array_column($productdata, "product_id");
        // print_r($productids);die;
        // $gds_products_update='';
        // $productdata = $data[]
        //     for ($i=0; $i < count($productdata); $i++) { 
        //         $productidfromarray=$productdata[$i]['product_id'];
          
        //         // DB::table('gds_order_products')  
        //         //     ->where('gds_order_id' ,"=",$data['order_id'])
        //         //     ->where('product_id',"=",$productidfromarray)                 
        //         //     ->update(array('order_status' => $status));
        //         $gds_products_update .= "UPDATE gds_order_products SET order_status = ".$status." where gds_order_id = ".$data['order_id']." and product_id=".$productidfromarray.";";
        //     }
        //     if(isset($gds_products_update) && $gds_products_update != ""){
        //             DB::unprepared($gds_products_update);
        //     }  
        }       

    }

  public function CheckHoldcount($order_id) {
       
      $checkHoldTrack= DB::table('gds_order_track')
        ->select('hold_count')
        ->where('gds_order_id','=',"$order_id")
          
        ->get()->all();

    return $checkHoldTrack;
      
  }
    
  public function CheckHoldcountMaster() {
       
        $checkHoldTrack= "SELECT description FROM master_lookup m WHERE m.`value` = 17029";
        $query = DB::select(DB::raw($checkHoldTrack));
      
      return $query;
      
  }


      /*
      * Class Name: holdReasons
      * Description: Function used to return hold Reasons  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28th Oct 2016
      * Modified Date & Reason: 
    */

  Public function HoldReasons() {


         $result= DB::table('master_lookup as ml')
            ->select(DB::raw('ml.master_lookup_id as id,ml.master_lookup_name as name,ml.value'))
            ->leftJoin('master_lookup_categories as mlc','mlc.mas_cat_id','=','ml.mas_cat_id')
            ->where('mlc.is_active','=','1')
            ->where('ml.mas_cat_id','=',112)
            ->get()->all();
          return $result;              


   } 

   /*
      * Class Name: Ordercomments
      * Description: Function used to upadte  the order to Hold comments
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 28th Oct 2016
      * Modified Date & Reason: 
    */  


Public function Ordercomments($data,$status) {

  $token=$data['deliver_token'];
  $account = new accountController();
  $customerID=$account->getDataFromToken(2,$token,['user_id']);

    if(isset($data['hold_reason_id'])) {

      $comments= DB::table('master_lookup')
      ->select('master_lookup_name')
      ->where('value','=',$data['hold_reason_id'])
      ->get()->all();  

      $comment=$comments[0]->master_lookup_name;

          # insertion in gds_ordertrack with next delivery data

          $checkHoldTrack= DB::table('gds_order_track')
          ->select('hold_count')
          ->where('gds_order_id','=',$data['order_id'])
          //->orderby('created_at','desc')
          ->get()->all();

          if(!empty($checkHoldTrack)) {

          $hold_count=$checkHoldTrack[0]->hold_count;

          DB::Table('gds_order_track')  
          ->where('gds_order_id', $data['order_id'])
          ->update(array('delivery_date' => $data['next_delivery_date'],'hold_count' => $hold_count+1,
          'created_by'=> $customerID[0]->user_id,'created_at'=> date("Y-m-d H:i:s")));

          }else{

          DB::table('gds_order_track')->insert(['gds_order_code' =>$data['order_code'],
          'gds_order_id' =>  $data['order_id'], 
          'delivery_date' => $data['next_delivery_date'],
          'hold_count'=>  1,
          'created_by'=> $customerID[0]->user_id,
          'created_at'=> date("Y-m-d H:i:s")]);
          }

      }else{
      $comment='Delivered';
      }

$result= DB::table('gds_orders_comments')->insert(['comment_type' => 17,
'entity_id' =>  $data['order_id'], 
'order_status_id' => $status,
'comment' => $comment,
'comment_date'=> date("Y-m-d H:i:s"),
'commentby'=> $customerID[0]->user_id,
'created_at'=> date("Y-m-d H:i:s")]);

return $result;          
}

   /*
      * Class Name: getBeatName
      * Description: Function used to getbeat name  
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 2nd Nov 2016
      * Modified Date & Reason: 
    */

  Public function getBeatName() {

         $day=date("D");
        // $day=["'".$day."'"];
        
         $result= DB::table('pjp_pincode_area')
            ->select('pjp_name')
            ->whereRaw('FIND_IN_SET("'.$day.'",days)')
            ->get()->all();

          return $result;              


   } 


   /*
      * Class Name: getCollectiondetails
      * Description: Function used to  retrive collection amount based on delivery id
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 2nd Nov 2016
	  *modified 06 feb 2017
      * Modified Date & Reason: 
    */

  Public function getCollectiondata($date,$user_id) {

       
          # Invoice details

              $StartDate=date('Y-m-d 00:00:00');
              $EndDate=date('Y-m-d 23:59:59');

         $data= DB::table('collections as coll')
            ->select('coll.order_code','coll.collection_id','inv.gds_order_id','coll.customer_name','mlookup.master_lookup_name as payment_mode_name','collectionhistory.amount as collected_amount','collectionhistory.payment_mode','inv.invoice_code','inv.grand_total','coll.le_wh_id','coll.discount_amt','collectionhistory.ecash','orders.hub_id')
            ->join('gds_orders as orders','orders.gds_order_id','=','coll.gds_order_id')
            ->leftJoin('gds_invoice_grid as inv','inv.gds_invoice_grid_id','=','coll.invoice_id')
            ->leftJoin('collection_history as collectionhistory','collectionhistory.collection_id','=','coll.collection_id')
            ->leftJoin('master_lookup as mlookup','mlookup.value','=','collectionhistory.payment_mode')         
            ->where('collectionhistory.collected_by','=',$user_id) 
			  ->where(db::raw("DATE(coll.created_on)"),$date)
           // ->whereBetween('coll.created_on',array($StartDate,$EndDate)) 
            //->GROUPBY('inv.invoice_code') 
            ->get()->all();

       
            if(!empty($data)){

              $result=json_decode(json_encode($data),true);
              
              for ($i=0; $i < sizeof($result) ; $i++) { 
                
                $res[$i]['collection_id']=$result[$i]['collection_id'];
                $res[$i]['order_code']=$result[$i]['order_code'];
                $res[$i]['ledger_id']=$result[$i]['collection_id'];
                $res[$i]['shop_name']=$result[$i]['customer_name'];             
                $res[$i]['invoice_code']=$result[$i]['invoice_code'];
                $res[$i]['invoice_total']=$result[$i]['grand_total'];
                $res[$i]['collected_amount']=$result[$i]['collected_amount'];
                $res[$i]['ecash']=$result[$i]['ecash'];
                $res[$i]['le_wh_id']=$result[$i]['le_wh_id'];
                $res[$i]['hub_id']=$result[$i]['hub_id'];
                $res[$i]['discount_amt']=$result[$i]['discount_amt'];

                $status=$this->getStatus($result[$i]['collection_id']);
                $res[$i]['status']=$status;

              // $payment_mode=(isset($result[$i]['master_lookup_name']) && $result[$i]['master_lookup_name']!='')? $result[$i]['master_lookup_name']:'';
 
                $res[$i]['payment_mode']=$result[$i]['payment_mode_name'];
                $res[$i]['payment_mode_code']=$result[$i]['payment_mode'];

                # Sales return Amount

                $return_data= DB::table('gds_returns')
                ->select('reference_no')
                ->orderBy('created_at', 'desc')
                ->where('gds_order_id','=',$result[$i]['gds_order_id'])                
                ->get()->all();

                $total_Return_Amount= DB::table('gds_returns')
                ->select(DB::raw('sum(total) as total'))
                ->where('gds_order_id','=',$result[$i]['gds_order_id'])                
                ->get()->all();

                   if(!empty($return_data)) {
                    $return=json_decode(json_encode($return_data[0]),true);
                    $return_amount=json_decode(json_encode($total_Return_Amount[0]),true);

                    $res[$i]['sales_reference_no']=$return['reference_no'];
                    $res[$i]['sales_return_amount']=$return_amount['total'];
                  }
                
               }


            } else{

              $res='';
            }

                       
          return $res;              


   }


   /*
      * Class Name: getStatus
      * Description: Function used to  return status based remittanace entry
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 14th Nov 2016
      * Modified Date & Reason: 
    */


Public function getStatus($collection_id) {


      $data= DB::table('remittance_mapping')
          ->select('remittance_id')
          ->where('collection_id','=',$collection_id)        
          ->get()->all();

          if(!empty($data)) {
              $status= 1;
          }else{
             $status=0;
          }

     return $status;

}


   /*
      * Class Name: collectionRemittanceHistory
      * Description: Function used to  save collection amount data
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 8th Nov 2016
      * Modified Date & Reason: 
    */


   Public function collectionRemittanceHistory($user_id,$data) {  
 

       # entry to collection_remittance _history table
   
   


    foreach($data['order_summary'] as $params) {

    if(empty($params['total_upi']))
    {
     $params['total_upi']='';

    }  
    if(empty($params['denominations'])) 
    {
       $params['denominations']='';
    }else{
      $params['denominations']=json_encode($params['denominations']);
    }
		$Module_Prefix='RM';
    $whresult = $this->_roleRepo->getLEWHDetailsById($params['le_wh_id']);
    $state_code=isset($whresult->state_code)?$whresult->state_code:"TS";
    $remittance_code = Utility::getReferenceCode($Module_Prefix,$state_code);
		
    		DB::table('collection_remittance_history')
		->insert(['submitted_by' =>  $user_id,
			   'remittance_code'=> $remittance_code, 
			   'submitted_at' => date("Y-m-d H:i:s"),
			   'collected_amt'=>$params['amount_submitted'],
			   'approval_status' => 57055,
			   'le_wh_id'=>$params['le_wh_id'],
         'hub_id'=>$params['hub_id'],
			   'by_cash'=>$params['total_cash'],
			   'by_cheque'=>$params['total_cheque'],
			   'by_online'=>$params['total_online'], 
			    'by_upi'=>$params['total_upi'], 
          'by_ecash'=>isset($params['total_ecash'])?$params['total_ecash']:0, 
          'by_pos'=>isset($params['total_pos'])?$params['total_pos']:0, 
			   'denominations'=>$params['denominations'],                                
			   'created_at' => date("Y-m-d H:i:s")]);
		$last_remittance_id=  DB::getPdo()->lastInsertId(); 
		# Retrieve Warehouse id based on order id
		foreach ($params['collection_id'] as $collection_id) {
		# code...
		
    //Commented by pavan on 29 March, We are directly passing collection ids instead of getting the same via invoice id

    /* //commenting start here
    $ware_house_id=DB::table("gds_orders as order")
		->select("invoice.gds_invoice_grid_id")
		->leftJoin('gds_invoice_grid as invoice','invoice.gds_order_id','=','order.gds_order_id')
		->where("invoice.invoice_code","=",$invoice)      
		->get()->all(); 

                    if(!empty($ware_house_id)) {
                     
                      $invoice_id=$ware_house_id[0]->gds_invoice_grid_id;
                    }
                    else {
                      
                      $invoice_id=0;
                    }

                             # Retrieve collection id based on invoice id

                               $collectionId=DB::table("collections")
                              ->select("collection_id")
                              ->where("invoice_id","=",$invoice_id)      
                              ->get()->all();

                              if(!empty($collectionId)) {
                               $collection_id=json_decode(json_encode($collectionId),true);                    
                              }
                              else {
                                $collection_id=0;
                              }

                      
                          # entry to remittance_mapping table

                           for ($j=0; $j <sizeof($collectionId); $j++) { 

                            DB::table('remittance_mapping')
                             ->insert(['collection_id' =>  $collection_id[$j]['collection_id'], 
                                      'remittance_id' => $last_remittance_id]);   
                            } //ends here*/  

                    #added this code for direct insertion of collection
                    # entry to remittance_mapping table
                    DB::table('remittance_mapping')
                             ->insert(['collection_id' =>  $collection_id, 
                                      'remittance_id' => $last_remittance_id]);        

                } 

                #approval matrix

              $approval_flow_func= new CommonApprovalFlowFunctionModel();
              $ledgerModel= new LedgerModel();

              $table='collection_remittance_history';
              $unique_column='remittance_id';
              $approval_unique_id=$last_remittance_id;
              $approval_status= "57055,0";

              $ledgerModel->updateStatusAWF($table,$unique_column,$approval_unique_id, $approval_status);

               $approval_module='Payment';
               $approval_unique_id = $last_remittance_id;
               $current_status= 57050;
               $approval_status= 57055;
               $approval_comment='Payment Received by App';
               $userId= $user_id;

               $approval_flow_func->storeWorkFlowHistory($approval_module, $approval_unique_id, $current_status, $approval_status, $approval_comment,$userId);
     
    
    }


    return $last_remittance_id;

  }

/*
* Class Name: getOrderHistoryUpdate
* Description: Function used to  save Order histoty data
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 16th Nov 2016
* Modified Date & Reason: 
*/

public function getOrderHistoryUpdate($order_id,$user_id) {


        $result=DB::table('gds_orders as order')
        ->select('order.order_status_id','ml.master_lookup_name as comment')
        ->leftJoin('master_lookup as ml','ml.value','=','order.order_status_id')
        ->where('order.gds_order_id','=',$order_id)
        ->get()->all();

        $data=json_decode(json_encode($result[0]),true);
       
       # Inserting partially delivered & total Return in ordercomments
        $input['entity_id']=$order_id; 
        $input['comment']=$data['comment'];
        $input['order_status_id']=$data['order_status_id'];
        $input['comment_type']=17;
        $input['commentby']=$user_id;
        $input['comment_date']=date('Y-m-d H:i:s');
        $input['created_at']=date('Y-m-d H:i:s');

        $save= new OrderModel();
        $save->saveComment($input);

                   
}

 /*
      * Class Name: updateGeoLocation
      * Description: Function used to updateGeoLocation
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 2nd dec 2016
      * Modified Date & Reason: 
    */

  Public function updateGeoLocation($user_id,$order_id,$latitude,$longitude) 
  {  

                 
         $cust_le_id= DB::table('gds_orders')
                      ->select('cust_le_id')
                      ->where('gds_order_id','=',$order_id)
                      ->get()->all();
          
            $result= DB::Table('legal_entities')  
                ->where('legal_entity_id',$cust_le_id[0]->cust_le_id)
                ->update(array('latitude' => $latitude,
                  'longitude' => $longitude,
                  'updated_by'=> $user_id,
                  'updated_at' => date("Y-m-d H:i:s")));


          return $result;              


   }


/*
* Class Name: getInvoiceOderlist
* Description: Function used to  get invoice order list
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 16th Nov 2016
* Modified Date & Reason: 
*/

public function getInvoiceOderlist($date,$beat,$hub) {

$result = DB::table('gds_orders as go')
         ->select(DB::raw("distinct go.order_code,go.gds_order_id,
          gig.gds_invoice_grid_id as invoice_order_no ,
          le.business_legal_name as shop_name,ml.master_lookup_name as status,
          go.order_status_id,
          go.firstname,le.address1,
          IFNULL(le.address2,'') address2,
          IFNULL(ppa.pjp_name,'') as beat,go.beat as beat_id,
          IFNULL(go.hub_id,'')hub_id,cfc_cnt,bags_cnt,crates_cnt,
          getLeWhName(hub_id) hub_name,
          gig.grand_total as invoice_amount,le.pincode,le.city"))
         // ->leftJoin('users as u','u.user_id','=','go.created_by')
          ->Join('master_lookup as ml','ml.value','=','go.order_status_id')
          ->leftJoin('legal_entities as le','le.legal_entity_id','=','go.cust_le_id')
          ->Join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
          ->leftJoin('gds_invoice_grid as gig','go.gds_order_id','=','gig.gds_order_id')
          ->leftJoin('pjp_pincode_area as ppa','ppa.pjp_pincode_area_id','=','go.beat')          
          ->where("go.order_status_id",'=','17021')
          ->where(db::raw("DATE(gig.created_at)"),$date);
  
    if(!empty($hub))
    {
        $result->where("go.hub_id",$hub);

     }
     if(!empty($beat))
    {
        $result->where("go.beat",$beat);

     }

return $result->get()->all();          

                   
}


/*
* Class Name: getcontainerbyorder
* Description: Function used to  get invoice order list
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 04 Dec 2016
* Modified Date & Reason: 
*/

public function getcontainerbyorder($order_id) {
  try{
   
   $result = DB::table('gds_orders as go')
         ->select(DB::raw("distinct go.order_code,go.gds_order_id,
          pcm.container_num as container_number, pcm.container_barcode as container_id,
          SUM(IFNULL(weight,0)) as weight,SUM(IFNULL(weight_uom,0)) as weight_uom"))
          ->leftJoin('picker_container_mapping as pcm','pcm.order_id','=','go.gds_order_id')      
          ->whereRaw('FIND_IN_SET(go.gds_order_id,"'.$order_id.'")')
          ->groupBy('pcm.container_barcode')
          ->get()->all();
$cfc_cnt=$this->getBagsCratesCount($order_id,1);
$bags_cnt=$this->getBagsCratesCount($order_id,2);
if(is_array($cfc_cnt) && is_array($bags_cnt))
 { 
$result_cnt=array_merge($cfc_cnt,$bags_cnt);  
}elseif(is_array($bags_cnt)){
$result_cnt=$bags_cnt;
}elseif(is_array($bags_cnt)){
$result_cnt=$cfc_cnt;
}else{
  $result_cnt='';
}

if(empty($result_cnt))
{  
   return $result;   

}else{
  $result=array_merge($result,$result_cnt);
  return $result;
}
     

  }
    catch(Exception $e) {
      Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
    }   


                   
}

 /*
      * Class Name: updateGeo
      * Description: Function used to updateGeo
      * Author: Ebutor <info@ebutor.com>
      * Copyright: ebutor 2016
      * Version: v1.0
      * Created Date: 5th jan  2017
      * Modified Date & Reason: 
    */

  Public function updateGeo($user_id,$legal_entity_id,$latitude,$longitude) 
  {  
        
     try{  

            $result= DB::Table('legal_entities')  
                      ->where('legal_entity_id',$legal_entity_id)
                      ->update(array('latitude' => $latitude,
                        'longitude' => $longitude,
                        'updated_by'=> $user_id,
                        'updated_at' => date("Y-m-d H:i:s")));
      

          return $result;       

          }catch(Exception $e){

            return Array('status' => "failed", 'message' => $e->getMessage(), 'data' => []);
          }       


   }


      public function getUsersByRoleNameId($roleName,$users) {

        $result = DB::table('users')
                ->select('users.user_id', 'users.firstname', 'users.lastname', 'users.email_id', 'users.mobile_no')
                ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->where(array('users.is_active' => 1))
                ->whereIn('roles.name', $roleName)
                ->whereIn('user_roles.user_id', $users)
                ->get()->all();
        return $result;
    }
      public function getUsersByRoleCode($roleCodes,$users) {

        $result = DB::table('users')
                ->select('users.user_id', 'users.firstname', 'users.lastname', 'users.email_id', 'users.mobile_no')
                ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->join('roles', 'roles.role_id', '=', 'user_roles.role_id')
                ->where(array('users.is_active' => 1))
                ->whereIn('roles.short_code', $roleCodes)
                ->whereIn('user_roles.user_id', $users)
                ->groupBy('users.user_id')
                ->get()->all();
        return $result;
    }


      public function gethubWareName($hubs) {

        $result = DB::table('legalentity_warehouses as lew')
                ->select('lew.le_wh_id', 'lew.lp_wh_name')
                ->whereRaw('FIND_IN_SET(lew.le_wh_id,"'.$hubs.'")')
                ->get()->all();
        return $result;
    }
	
	
	
	 public function getInventoryByProductlist($offset_limit) {
          
          try {
           
            $inventory=DB::table("inventory as inv")
           ->select(db::raw("prod.product_id,prod.product_title as product_name,prod.sku,round(prod.mrp,2) as mrp,inv.soh as soh,inv.le_wh_id,bin.bin_mapping_id as binids"))
          ->leftJoin('products as prod','prod.product_id','=','inv.product_id')
          ->leftJoin('cat_bin_mapping as bin','bin.category_id','=','prod.category_id')
           ->where("prod.cp_enabled",'=',1)
          //->where(db::raw('GetCPInventoryByProductId(prod.product_id,'.$le_wh_id.')'),'>',0)
           ->take($offset_limit)
           ->get()->all();
  
        return $inventory;
            
                  
            }
            catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            } 

    }

    public function updateStatusAWF($table,$unique_column,$approval_unique_id, $next_status_id, $user_id){
        try{
            $status = explode(',',$next_status_id);
            $new_status = ($status[1]==0)?$status[0]:$status[1];
            $data = array(
                'approved_by'=>$user_id,
                'approved_at'=>date('Y-m-d H:i:s')
            );
            if($table == 'po' && ($new_status==57118 || $new_status==57032))
            {
                $data['payment_status'] = $new_status;
            }else{
                $data['approval_status'] = $new_status;
            }
            if($table == 'purchase_returns' && ($new_status==58069))
            {
                $data['picked_at'] = date('Y-m-d H:i:s');
            }
            DB::table($table)->where($unique_column, $approval_unique_id)->update($data);
        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function getApprovalHistory($module,$id) {
        $history=DB::table('appr_workflow_history as hs')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'hs.created_at','hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$id)
                        ->where('hs.awf_for_type',$module)
                        ->groupBy('hs.created_at')
                        ->get()->all();
        return json_decode(json_encode($history),true);
    }
	
        public function containerMaster($crate_code, $status, $transaction_status, $token,$le_wh_id){
            try {
//                $statusValue = DB::table("master_lookup")->where("mas_cat_id", 136)
//                               ->where("master_lookup_name", "LIKE", "%".strtolower($status)."%")->pluck("value")->all();
//                DB::table('container_master')  
//                ->whereIn('crate_code' , $crate_code)                 
//                ->update(array('status' => $statusValue[0]));
                $newArr = array();
                foreach($crate_code as $each){
                    $crateInfoArr["crate_code"] = $each;
                    $crateInfoArr["status"] = $status;
                    $crateInfoArr["transaction_status"] = $transaction_status;
                    $newArr[] = $crateInfoArr;
                }
                $queryData = array("lp_token" => $token, "le_wh_id" => $le_wh_id, "crate_info" => $newArr);
                $queryDataEncode["cratestatuslist_params"] = json_encode($queryData);
                
                $curlCall = curl_init();
                curl_setopt($curlCall, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlCall, CURLOPT_URL, $_SERVER['HTTP_HOST']."/cratemanagement/setcratestatus");
//                curl_setopt($curlCall, CURLOPT_URL, "http://dev.ebutor.com/cratemanagement/setcratestatus");
                curl_setopt($curlCall, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curlCall, CURLOPT_POSTFIELDS, $queryDataEncode);
                $output = curl_exec($curlCall);
                $info = curl_getinfo($curlCall);
                $error = curl_error($curlCall);
//                echo "<br /><br />Output: <pre>"; print_r(json_decode($output)); echo "<br />----<br />Info: "; var_dump($info); echo "<br />----<br />Error: "; var_dump($error);
                curl_close($curlCall);
                
                return $output;
            } catch (Exception $ex) {
                Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            }
        }
      
       public function checkContainer($crate_code){
       try{
           // $checkcontainer = DB::table('container_master')->where('crate_code',$crate_code)->first();
      
            $checkcontainer = DB::table('container_master as cm')
          ->select('cm.crate_code','cm.status','cm.transaction_status')
          ->where('cm.crate_code','=',$crate_code)
          ->get()->all();
          
            return $checkcontainer;
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }



   public function updateDeliveryEndtime($orderid){
       try{

 DB::table('gds_order_track as got')  
                    ->where('got.gds_order_id' ,$orderid)
                    ->update(array('delivery_end_time' => date("Y-m-d H:i:s")));
 
        
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }

public function updatePickStarttime($orderid){
try{

 DB::table('gds_order_track as got')  
                    ->where('got.gds_order_id' ,$orderid)
                    ->update(array('picking_start_time' => date("Y-m-d H:i:s")));
 
        
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }
public function updatePickEndtime($orderid){
try{

 DB::table('gds_order_track as got')  
                    ->where('got.gds_order_id' ,$orderid)
                    ->update(array('picking_end_time' => date("Y-m-d H:i:s")));
 
        
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }
/*
  * Function Name: getHubUsers()
  * Description: Used to get users based on hub
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2017
  * Version: v1.0
  * Created Date: 24th march 2017
  * Modified Date & Reason: 
  */
public function getHubUsers($user_id,$hub){
try{
 
         $result= DB::table('legalentity_warehouses as lew')
              ->select(db::raw("group_concat(DISTINCT userp.user_id separator ',') as hub_users"))
              ->join('user_permssion as userp','lew.bu_id','=','userp.object_id')
              ->whereRaw('FIND_IN_SET(lew.le_wh_id,"'.$hub.'")')
              ->where('lew.status',1)
              ->where('userp.permission_level_id',6)
              ->get()->all();
             
       return $result; 
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }

public function getBinQty($bin_id,$product_id){
try{
 
         $result= DB::table('bin_inventory')
              ->select('qty','reserved_qty')
              ->where('product_id',$product_id)
              ->where('bin_id',$bin_id)
              ->lockForUpdate()
              ->first();
             
       return $result; 
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }

 public function getBinLocations($product_id){
try{
        // DB::enablequerylog();
        $bin_config =  DB::Table('warehouse_config as wc')
            ->join('bin_inventory as binv','wc.wh_loc_id','=','binv.bin_id')
            ->join('bin_type_dimensions as bin_dimension','wc.bin_type_dim_id','=','bin_dimension.bin_type_dim_id')
            ->select(db::raw("distinct wc.wh_location as bin,binv.qty,binv.reserved_qty,wc.sort_order,binv.bin_id"))
            ->where('wc.pref_prod_id', $product_id)
            ->where('bin_dimension.bin_type', 109003)
            //->where('binv.qty','>', 0)
            //->lockForUpdate()
            ->get()->all();
        // $sql = DB::getQueryLog();
        // print_r(end($sql)); exit;

          if(!empty($bin_config))
           {
             $bin_config= json_decode(json_encode($bin_config),true);

        if(is_array($bin_config))   
       { 
       foreach ($bin_config as $key => $row) 
       {

            $sort_qtydata[$key] = $row['qty'];
        }
         array_multisort($sort_qtydata, SORT_DESC,$bin_config);
       }

       return $bin_config;
           } else{
            return array();
           }
       
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
           return array();
       }
   }
    public function getContainerOrder($container){
        try{
            $sql =  DB::table('picker_container_mapping')
            ->select('order_id')
            ->where('container_barcode', $container)
            ->orderBy('created_at', 'desc')
            ->first();
            $sql = json_decode(json_encode($sql), true);

            if(!empty($sql))
                return $sql['order_id'];
            else
                return '';
        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

    public function getOrderStatus($orderId){
        try{
            $sql = DB::table('gds_orders')
                ->select('order_status_id')
                ->where('gds_order_id', $orderId)
                ->first();
            $sql = json_decode(json_encode($sql), true);
            if(!empty($sql))
                return $sql['order_status_id'];
            else
                return '';

        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

  
     public function getBagsCratesCount($orderId,$flag){
        try{

         if($flag==1)
         {  
         $result_cnt=DB::table('gds_order_track as got')
         ->select(DB::raw("distinct got.gds_order_code as order_code,got.gds_order_id,
          '' as container_number,CONCAT('BAG(',got.bags_cnt,')') as container_id,
          0 as weight,0 as weight_uom")) 
         ->whereRaw('FIND_IN_SET(got.gds_order_id,"'.$orderId.'")')
         ->where('got.bags_cnt','>',0)
         ->get()->all();
      
           }else{
             $result_cnt=DB::table('gds_order_track as got')
             ->select(DB::raw("distinct got.gds_order_code as order_code,got.gds_order_id,
          '' as container_number,CONCAT('CFC(',got.cfc_cnt,')') as container_id,
          0 as weight,0 as weight_uom")) 
         ->whereRaw('FIND_IN_SET(got.gds_order_id,"'.$orderId.'")')
         ->where('got.cfc_cnt','>',0)->get()->all();
           }

               return $result_cnt ;


        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

public function getProductName($product_id){
        try{
            $sql = DB::select("select getProductName(".$product_id.") AS prodname ");
          
            if(!empty($sql))
                return $sql[0]->prodname;
            else
                return '';

        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return $ex->getMessage();
        }
    }

public function getReserveQty($bin_id,$product_id,$orderid){
try{
 
         $result= DB::table('picking_reserve_bins')
              ->select("reserved_qty as qty")
              ->where('product_id',$product_id)
              ->where('bin_id',$bin_id)
              ->where('order_id',$orderid)
              ->first();
             
       return $result; 
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }
public function updateReserveQty($order_id){
  try{
    $bin_data = DB::table('picking_reserve_bins as prb')->where("prb.order_id",$order_id)->get()->all();
    $bin_data=json_decode(json_encode($bin_data),true);
    foreach ($bin_data as $key => $value_bin) {

      $quantity= $this->getBinQty($value_bin['bin_id'],$value_bin['product_id']);
      $reserve_qty=$this->getReserveQty($value_bin['bin_id'],$value_bin['product_id'],$value_bin['order_id']);
      $reservedqty=isset($quantity->reserved_qty)?$quantity->reserved_qty:0;
      $qtyreserved=isset($reserve_qty->qty)?$reserve_qty->qty:0;
      $reserve=$reservedqty-$qtyreserved;

      $newVal = array('new_reserved_qty'=> $reserve);
      $oldVal = array('old_reserved_qty'=>$reservedqty);
      $uniquevalues = array('product_id'=>$value_bin['product_id'],
      'bin_id'=>$value_bin['bin_id'],
      'order_id'=>$order_id);

      UserActivity::userActivityLog("BinInventoryUpdate", $newVal, "Update Bin Reserved Qty (deduction)" , $oldVal, $uniquevalues);

      DB::table('bin_inventory')->where('bin_id',$value_bin['bin_id'])
      ->where('product_id',$value_bin['product_id'])
      ->decrement('reserved_qty', $qtyreserved);
    }    
    return true; 
  } catch (Exception $ex) {
    Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
    return $ex->getMessage();
  }
}

public function  getShipqty($product_id,$order_id){
try{
        $reserved_qty = DB::table('picking_reserve_bins as prb')
        ->select(db::raw("sum(reserved_qty) as qty"))
        ->where("prb.order_id",$order_id)
        ->where("prb.product_id",$product_id)
        ->groupBy('prb.product_id')
        ->first();
    $qty= isset($reserved_qty->qty) ? $reserved_qty->qty : 0;
       return $qty; 
       } catch (Exception $ex) {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
       }
   }
    /*
    * Function name: retailerCollectionPendingOrders
    * Description: the function is used to get all collection pending orders to be collected by collection agent
    * Author: Raju.A
    * Copyright: ebutor 2018
    * Version: v1.0
    * Created Date: 22th March 2018 :/
    * Modified Date & Reason:
    */
   public function retailerCollectionPendingOrders($retailer_le_id) {
        try {
            $pending_orders = DB::table('gds_orders as go')
                    ->leftJoin('gds_orders_payment as gop','gop.gds_order_id','=','go.gds_order_id')
                    ->leftJoin('gds_invoice_grid as inv_grid','inv_grid.gds_order_id','=','go.gds_order_id')
                    ->leftJoin('gds_return_grid as ret_grid','ret_grid.gds_order_id','=','go.gds_order_id')
                    ->leftJoin('collections as col','col.gds_order_id','=','go.gds_order_id')
                    ->select(['col.collection_id','go.gds_order_id','go.order_code','go.le_wh_id','go.hub_id','inv_grid.gds_invoice_grid_id as invoice_id','ret_grid.return_grid_id as return_id','inv_grid.ecash_applied',
                    DB::raw("inv_grid.grand_total as invoice_amt,IFNULL(ret_grid.total_return_value,0) as return_amt,IFNULL(sum(col.collected_amount),0) as collected_amt,IFNULL(sum(col.rounded_amount),0) as rounded_amt")])
                    ->where("go.cust_le_id", $retailer_le_id)
                    ->where("gop.payment_method_id", 22018)
                    ->whereIn("go.order_status_id", [17007,17010,17023])
                    ->having(DB::raw('collected_amt'),'<',DB::raw("round(invoice_amt-return_amt-ecash_applied)"))
                    ->orHavingRaw('col.collection_id is NULL')
                    ->groupBy('go.gds_order_id')
                    ->get()->all();
            return $pending_orders;
        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function getOrdersByPickCode($pickCode){
        try{
            $query = DB::table('gds_orders as orders')
                    ->leftJoin('gds_order_track as trck','trck.gds_order_id','=','orders.gds_order_id')
                    ->select(['trck.picker_id','orders.gds_order_id','orders.cust_le_id','orders.email','orders.phone_no','orders.order_code','orders.order_type','orders.le_wh_id','orders.legal_entity_id','orders.hub_id','orders.shop_name','orders.beat'])
                    ->where('trck.pick_code',$pickCode)
                    ->whereNotIn('orders.order_status_id',[17005,17009,17015])
                    ->get()->all();
            return $query;
        } catch (Exception $ex) {

        }
    }
    public function getOrderProductdetailsById($order_id) {
        try {
            $Orderdata = DB::table("gds_order_products as orderprod")
                    ->select(db::raw("orderprod.gds_order_id,orderprod.product_id,orderprod.pname as product_name,
                        orderprod.sku,round(orderprod.mrp,2) as mrp,sum(orderprod.qty) as order_qty,
                        orderprod.parent_id,IFNULL(getFreeBeePrdMpq(orderprod.product_id),0) AS freebee_mpq,
                        IFNULL(getFreeBeeQty(orderprod.product_id),0) AS freebee_qty"))
                    ->leftJoin('gds_orders as order', 'order.gds_order_id', '=', 'orderprod.gds_order_id')
                    ->where('orderprod.gds_order_id', $order_id)
                    ->GROUPBY("orderprod.product_id")
                    ->get()->all();
            return $Orderdata;
        } catch (Exception $ex) {
            
        }
    }


    public function insertMfcOrderTrack($data){
      $query = DB::table("mfc_order_tracking")->insertGetId($data);
      return $query;
    }

    public function checkMFCOrderStatusCount($data){
      $query = DB::table("mfc_order_tracking")->select('mfc_id')->where($data)->get()->all();
      return $query;
    }

    public function updateMFCOrderStatus($data,$mfc_id){
      $query = DB::table("mfc_order_tracking")->where(['mfc_id'=>$mfc_id])->update($data);
      return $query;
    }

    public function insertMFCDeliveryStatus($data){
      $query = DB::table("mfc_delivery_details")->insert($data);
      return $query;
    }

    public function checkMFCDeliveryData($data){
      $query = DB::table("mfc_delivery_details")->select('mfc_delivery_id')->where($data)->first();
      return $query;
    }

    public function updateMFCDeliverydata($data,$whereData){
      $query = DB::table("mfc_delivery_details")->where($whereData)->update($data);
      return $query;
    }

    public function getMFCDeliveryData($user_id){
      $query = DB::table("mfc_delivery_details")
                ->select('loan_amount','order_code','user_id')
                ->where(['user_id'=>$user_id])
                ->orderBy('updated_at','desc')->first();
      return $query;
    }

    public function updateDeliveryOtp($user_id,$otp){
      $query = DB::table('users')->where('user_id',$user_id)->update(['lp_otp'=>$otp]);
      return $query;
    }

    public function getDeliveryOtp($user_id){
      $query = DB::table('users')->where('user_id',$user_id)->select('lp_otp')->first();
      return isset($query->lp_otp)?$query->lp_otp:0;
    }

}
  
?>
