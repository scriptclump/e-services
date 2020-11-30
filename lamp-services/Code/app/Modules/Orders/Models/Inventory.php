<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use App\Lib\Queue;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Controllers\OrdersController;
use DB;
use Lang;
use Log;

class Inventory extends Model
{
    
    /**
     * updateInventory method is used to update order qty in inventory table while did cancellation
     * @param  Array $products
     * @param  Number $le_wh_id
     * @param  string $symbol
     * @param  string $refNo, default is blank
     * @return Null
     */
    public function updateInventory($products, $orderId, $symbol='substract', $refNo = '') {
        //DB::beginTransaction();//commented by Nishanth
        try{
        	if(is_array($products) && count($products) > 0) {
                    $invLogs = array();
                    $orderModel = new OrderModel();
                    $orderInfo = $orderModel->getOrderInfoById($orderId, ['le_wh_id','hub_id']);
                    $le_wh_id = isset($orderInfo->le_wh_id) ? $orderInfo->le_wh_id : 0;
                    $hub_id = isset($orderInfo->hub_id) ? $orderInfo->hub_id : 0;
                $invKey = 0;
                foreach ($products as $product) {

                    $invInfo = $this->getInventory($product['product_id'], $le_wh_id);

                    $prevSOH = isset($invInfo->soh) ? $invInfo->soh : null;
                    $prevOrderQty = isset($invInfo->order_qty) ? $invInfo->order_qty : null;
                    $prevDitOrderQty = isset($invInfo->dit_order_qty) ? $invInfo->dit_order_qty : null;
                    $prevQuarantineQty = isset($invInfo->quarantine_qty) ? $invInfo->quarantine_qty : null;
                    $prevDndQty = isset($invInfo->dnd_qty) ? $invInfo->dnd_qty : null;
                    $prevDitQty = isset($invInfo->dit_qty) ? $invInfo->dit_qty : null;
                    
                    $symbolOpt = '';
                        if($symbol == 'add') {
                            $updateArr = ($hub_id == 10695)?array('dit_order_qty' => DB::raw('(dit_order_qty+'.$product['qty'].')')):array('order_qty' => DB::raw('(order_qty+'.$product['qty'].')'));
                        } else if($symbol == 'substract') {
                            $updateArr = ($hub_id == 10695)?array('dit_order_qty' => DB::raw('(dit_order_qty-'.$product['qty'].')')):array('order_qty' => DB::raw('(order_qty-'.$product['qty'].')'));
                            $symbolOpt = '-';
                        }
                        DB::table('inventory')->where(array('le_wh_id'=>$le_wh_id, 'product_id'=>$product['product_id']))->update($updateArr);
                        $invLogs[$invKey] = array(
                            'le_wh_id' => $le_wh_id,
                            'product_id' => $product['product_id'],
                            'soh' => 0,
                            'order_qty' => 0,
                            'ref' => $refNo,
                            'ref_type' => 5,
                            'old_soh' => $prevSOH,
                            'old_order_qty' => $prevOrderQty,
                            'old_dit_order_qty' => $prevDitOrderQty,
                            'old_quarantine_qty' => $prevQuarantineQty,
                            'old_dnd_qty' => $prevDndQty,
                            'old_dit_qty' => $prevDitQty,
                            'comments' => 'order qty ' . $symbol . 'ed'
                        );
                        if($hub_id == 10695){
                            $invLogs[$invKey]['dit_order_qty'] = $symbolOpt . $product['qty'];
                        } else {
                            $invLogs[$invKey]['order_qty'] = $symbolOpt . $product['qty'];
                        }
                        $invKey++;
                }

                /**
                 * Add inventory log
                 */

                if(count($invLogs)) {
                    $this->addInQueueWithBulk($invLogs);
                }

                //DB::commit();//commented by Nishanth
                return true;                
        	}            
        }
        catch(Exception $e) {
            //DB::rollback();//commented by Nishanth
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }
   
    /**
     * inventoryStockOutward() method is used to update SOH, ordered_qty at the time of invoice
     * @param Array $products
     * @param Number $le_wh_id
     * @param Boolean $outwardType, by default zero
     * @return Null
     *
     * 0 is for Sales Order Invoice
     * 1 is for Purchase Return
     * 3 Stock transfer PO
     */

    public function inventoryStockOutward($products, $le_wh_id, $outwardType=0, $refNo='', $refType='') {
        DB::beginTransaction();
        try{
            if(is_array($products) && count($products) > 0) {
                $invLogs = array();
                $stockOutward = array();
                $batch_history_array = array();
                $batchModel = new \App\Modules\Orders\Models\batchModel();
                $batch_inventory_update = "";
                $comments = "";
                if($refType == 3){
                    $comments = "SOH Subtracted (Stock Transfer PO ID:$refNo)";
                }
                foreach ($products as $product) {

                    $product_id = isset($product['product_id']) ? (int)$product['product_id'] : 0;
                    if($product_id <=0 || $le_wh_id <=0) {
                        continue;
                    }
                    
                    $invInfo = $this->getInventory($product_id, $le_wh_id);
                    $prevSOH = isset($invInfo->soh) ? $invInfo->soh : null;
                    $prevOrderQty = isset($invInfo->order_qty) ? $invInfo->order_qty : null;
                    $prevQuarantineQty = isset($invInfo->quarantine_qty) ? $invInfo->quarantine_qty : null;
                    $prevDndQty = isset($invInfo->dnd_qty) ? $invInfo->dnd_qty : null;
                    $prevDitQty = isset($invInfo->dit_qty) ? $invInfo->dit_qty : null;
                    
                    $dit_qty = isset($product['dit_qty']) ? (int)$product['dit_qty'] : 0;
                    $dnd_qty = isset($product['dnd_qty']) ? (int)$product['dnd_qty'] : 0;
                    if($outwardType == '0') {
                        $fields = array('soh' => DB::raw('(soh-'.$product['qty'].')'), 'order_qty' =>DB::raw('(order_qty-'.$product['qty'].')') );
                        DB::table('inventory')->where(array('le_wh_id'=>$le_wh_id, 'product_id'=>$product_id))->update($fields);
                    }
                    else if($outwardType == '1') {
                        $fields = array('soh' => DB::raw('(soh-'.$product['qty'].')'),
                            'dit_qty' => DB::raw('(dit_qty-'.$dit_qty.')'),
                            'dnd_qty' => DB::raw('(dnd_qty-'.$dnd_qty.')')
                            );
                        DB::table('inventory')->where(array('le_wh_id'=>$le_wh_id, 'product_id'=>$product_id))->update($fields);
                    }
                    
                    $invLogs[] = array(
                                        'le_wh_id'=>$le_wh_id,
                                        'product_id'=>$product_id,
                                        'soh'=>'-'.$product['qty'],
                                        'order_qty'=>'-'.($outwardType == '0' ? $product['qty'] : 0),
                                        'dit_qty'=>'-'.$dit_qty,
                                        'dnd_qty'=>'-'.$dnd_qty,
                                        'ref'=>$refNo,
                                        'ref_type'=>$refType,
                                        'old_soh'=>$prevSOH,
                                        'old_order_qty'=>$prevOrderQty,
                                        'old_quarantine_qty'=>$prevQuarantineQty,
                                        'old_dnd_qty'=>$prevDndQty,
                                        'old_dit_qty'=>$prevDitQty,
                                        'comments'=>$comments
                                );
//                    Log::info("invLogs");
  //                  Log::info(json_encode($invLogs));
                    //batches entries 
                    $orderObj = new  OrdersController();
                    $batch_inv_array = $orderObj->getBatchesByData($product_id,$le_wh_id,$product['qty'],0,10,[]);
                    foreach ($batch_inv_array as $ikey => $ivalue) {
                        //creating batch array
                        $batch_id = $ivalue->inward_id;
                        $invb_id = $ivalue->invb_id;
                        $elp = $ivalue->elp;
                        $req_qty = $product['qty'];
                        if($req_qty > $ivalue->qty){
                            $used_qty = $ivalue->qty;
                        }else if($ivalue->qty >= $req_qty){
                            $used_qty = $req_qty;
                        }
                        if(count($batch_inv_array) == 1){
                            $batch_ord_qty = $product['qty'];
                        }else{
                            $batch_ord_qty = $used_qty;
                        }
                       
                        $batch_history_array[] = array("inward_id"=>$batch_id,
                                        "le_wh_id"=>$le_wh_id,
                                        "product_id"=>$product_id,
                                        "qty"=>'-'.$used_qty,
                                        "old_qty"=>$ivalue->qty,
                                        'ref'=>$refNo,
                                        'ref_type'=>$refType,
                                        'dit_qty'=>'-'.$dit_qty,
                                        'old_dit_qty'=>$ivalue->dit_qty,
                                        'dnd_qty'=>'-'.$dnd_qty,
                                        'old_dnd_qty'=>$ivalue->dnd_qty,
                                        'comments'=>"Qty Substracted for Batch Id:$batch_id");
                        $product['qty'] = $req_qty - $used_qty;
                        $batch_inventory_update .= "UPDATE inventory_batch SET qty=qty-$used_qty where invb_id = $invb_id;";
                    }

                                                   
                }

                /**
                 * Add inventory log
                 */
                
                if(count($invLogs)) {
                    $this->addInQueueWithBulk($invLogs);
                }
                if(count($batch_history_array)){
                    if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                        DB::unprepared($batch_inventory_update);
                    }
                    //inserting batch history data
                    $batchModel->insertBatchHistory($batch_history_array);

                }


                DB::commit();
                return true;    
            }            
        }
        catch(Exception $e) {
            DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * inventoryStockInward() method is used to update SOH, ordered_qty at the time of stock inward
     * @param Array $products
     * @param Number $le_wh_id
     * @param String $refNo like GRN Code, Return Code
     * @param Number $refType 
     * 
     * 1- GRN, 2- Invoice, 3- Sales Return, 4- Purchase Return, 5. Cancel
     * 
     * @return Null
     */
    
    public function inventoryStockInward($products, $le_wh_id, $refNo, $refType) {
        //DB::beginTransaction();commented by Nishanth

        try{
            if(is_array($products) && count($products) > 0) {
                $invLogs = array();
                $batch_history_array = array();
                $batch_array = array();
                $batch_inventory_update = "";
                $gds_batch_update = "";
                $batchModel = new \App\Modules\Orders\Models\batchModel();
                $i=0;
                foreach ($products as $product) {
                    $product_id = isset($product['product_id']) ? (int)$product['product_id'] : 0;
                    if($product_id <=0 || $le_wh_id <=0) {
                        continue;
                    }

                    $invInfo = $this->getInventory($product_id, $le_wh_id);

                    $soh = isset($product['soh']) ? (int)$product['soh'] : 0;

                    $free_qty = isset($product['free_qty']) ? (int)$product['free_qty'] : 0;
                    $quarantine_qty = isset($product['quarantine_qty']) ? (int)$product['quarantine_qty'] : 0;
                    $dit_qty = isset($product['dit_qty']) ? (int)$product['dit_qty'] : 0;
                    $dnd_qty = isset($product['dnd_qty']) ? (int)$product['dnd_qty'] : 0;


                    $prevSOH = isset($invInfo->soh) ? $invInfo->soh : null;
                    $prevOrderQty = isset($invInfo->order_qty) ? $invInfo->order_qty : null;
                    $prevQuarantineQty = isset($invInfo->quarantine_qty) ? $invInfo->quarantine_qty : null;
                    $prevDndQty = isset($invInfo->dnd_qty) ? $invInfo->dnd_qty : null;
                    $prevDitQty = isset($invInfo->dit_qty) ? $invInfo->dit_qty : null;


                    $fields = array(
                            'le_wh_id'=>$le_wh_id, 
                            'product_id'=>$product_id, 
                            'soh'=>DB::raw('(soh+'.$soh.')'), 
                            'free_qty'=>DB::raw('(free_qty+'.$free_qty.')'), 
                            'quarantine_qty'=>DB::raw('(quarantine_qty+'.$quarantine_qty.')'), 
                            'dit_qty'=>DB::raw('(dit_qty+'.$dit_qty.')'), 
                            'dnd_qty'=>DB::raw('(dnd_qty+'.$dnd_qty.')')
                            );

                    $invLogs[] = array(
                                        'le_wh_id'=>$le_wh_id,
                                        'product_id'=>$product_id,
                                        'soh'=>$soh,
                                        'order_qty'=>0,
                                        'ref'=>$refNo,
                                        'ref_type'=>$refType,
                                        'quarantine_qty'=>$quarantine_qty,
                                        'dit_qty'=>$dit_qty,
                                        'dnd_qty'=>$dnd_qty,
                                        'old_soh'=>$prevSOH,
                                        'old_order_qty'=>$prevOrderQty,
                                        'old_quarantine_qty'=>$prevQuarantineQty,
                                        'old_dnd_qty'=>$prevDndQty,
                                        'old_dit_qty'=>$prevDitQty,
                                        'comments'=>''
                                );

                    
                    if(isset($invInfo->inv_id) && $invInfo->inv_id > 0) {
                        DB::table('inventory')->where(array('inv_id'=>$invInfo->inv_id))->update($fields);
                    }
                    else {                       
                        DB::table('inventory')->insert($fields);
                    }        
                      
                    // adding data back to batches while stock inward
                    if($refType == "Sales Returns" || $refType == 3){
                        $gds_order_id = DB::table('gds_return_grid')->select("gds_order_id")->where(array('return_order_code'=>$refNo))->first();
                        if(isset($gds_order_id->gds_order_id)){
                            $gds_order_id = $gds_order_id->gds_order_id;
                            $batchData = $batchModel::where("product_id",$product_id)
                                        ->where("gds_order_id",$gds_order_id)
                                        ->orderBy("gob_id","desc")
                                        ->get()->all();
                            if(count($batchData)){

                                foreach ($batchData as $bkey => $bvalue) {
                                    # code...
                                    if($soh > 0){
                                        $add_qty = ($soh >= $bvalue->inv_qty)?$bvalue->inv_qty:$soh;
                                        //query to update gds batch according to returns
                                        $main_batch_id =isset($bvalue->main_batch_id)?$bvalue->main_batch_id:'NULL';
                                        $gds_batch_update .= "UPDATE gds_orders_batch SET ret_qty=ret_qty+$add_qty where gob_id=$bvalue->gob_id;";
                                        $batch_inventory_update .= "UPDATE inventory_batch SET qty=qty+$add_qty where product_id = $product_id and le_wh_id=$le_wh_id and inward_id=$bvalue->inward_id";
                                        if($main_batch_id!=""){
                                            $batch_inventory_update .= " and main_batch_id = $main_batch_id;"; // for old data mainbatchid not exist
                                        }
                                        $old_data = DB::table("inventory_batch")
                                                    ->where("inward_id",$bvalue->inward_id)
                                                    ->where("product_id",$product_id)
                                                    ->where("le_wh_id",$le_wh_id)
                                                    ->first();
                                        $old_qty = 0;
                                        if(count($old_data)){
                                            $old_qty = $old_data->qty;
                                        }
                                        $batch_history_array[] = array("inward_id"=>$bvalue->inward_id,
                                                "le_wh_id"=>$le_wh_id,
                                                "product_id"=>$product_id,
                                                "qty"=>'+'.$add_qty,
                                                "old_qty"=>$old_qty,
                                                'ref'=>$refNo,
                                                'ref_type'=>$refType,
                                                'dit_qty'=>0,
                                                'old_dit_qty'=>0,
                                                'dnd_qty'=>0,
                                                'old_dnd_qty'=>0,
                                                'comments'=>'Qty Added by Sales Returns');
                                        $soh -=  $bvalue->inv_qty;
                                    }
                                }
                            }
                        }
                    }else{
                        $exp_date = isset($product['exp_date']) ? $product['exp_date'] : 0;
                        $mfg_date = isset($product['manf_date']) ? $product['manf_date'] : 0;
                        $elp = isset($product['elp']) ? $product['elp'] : 0;
                        //adding into Batches
                        $espQuery = "select getProductEsp_wh($product_id,$le_wh_id) as esp";
                        $espQuery = DB::select($espQuery);
                        if(count($espQuery) > 0){
                            $esp = $espQuery[0]->esp;
                        }else{

                            $esp = 0;
                        }
                        $batch_id = DB::table("inward")->select("inward_id")->where("inward_code",$refNo)->first();
                        $batch_id = isset($batch_id->inward_id)?$batch_id->inward_id:0;
                        /* to insert main_batch_id in inventory_batch_*/
                        $po_so_code = DB::table('inward')->select('inward_id','po.po_id','po.po_so_order_code')
                                        ->join('po','po.po_id','=','inward.po_no')
                                        ->where('inward_id',$batch_id)
                                        ->first();
                        $code = isset($po_so_code->po_so_order_code)?$po_so_code->po_so_order_code:'';
                        if (empty($code)) {
                            $mainBatch_id = [];
                            $mainBatch_id[0]['main_batch_id']=$batch_id;
                            $mainBatch_id[0]['ord_qty'] = $soh;
                        }else{
                            $getBatch_id = DB::table('gds_orders')->select('gds_order_id','le_wh_id')
                                            ->where('order_code',$code)
                                            ->get()->all();
                            $main_wh_id = isset($getBatch_id[0]->le_wh_id)? $getBatch_id[0]->le_wh_id:NULL;
                            $gds_orderID = isset($getBatch_id[0]->gds_order_id)? $getBatch_id[0]->gds_order_id:NULL;
                            // print_r("rajradclief  u have to change the query getting records from gds_ordebatch based on qty");
                            $batch_inv_array = $this->getQtyByData($product_id,$main_wh_id,$soh,0,10,[],$gds_orderID);
                            $getBatch_id = json_decode(json_encode($batch_inv_array),1);
                            $mainBatch_id = $getBatch_id;

                        }
                        //$main = '';
                        $actual_qty = $soh;
                        // mutltipule entries in inventory_batch same as in gds_orders_batch
                        foreach($mainBatch_id as $mainbatch){
                            $req_qty = $actual_qty;
                            $mainbatchid = (isset($mainbatch['main_batch_id']) && $mainbatch['main_batch_id']!="")?$mainbatch['main_batch_id']:$batch_id;
                            $bkey = $mainbatchid.'_'.$product_id;
                            if($req_qty>0){
                                if($req_qty > $mainbatch['ord_qty']){
                                    $used_qty = $mainbatch['ord_qty'];
                                }else if($mainbatch['ord_qty'] >= $req_qty){
                                    $used_qty = $req_qty;
                                }
                               // if(count($mainBatch_id) == 1){
                               //     $batch_ord_qty = $mainbatch['ord_qty'];
                               // }else{
                               //     $batch_ord_qty = $used_qty;
                               // }
                                if(isset($batch_array[$bkey])){
                                    $batch_array[$bkey]['qty'] = $batch_array[$bkey]['qty'] + $used_qty;
                                }else{
                                    $inwdPrdDetails =DB::table('inward_product_details as inwpd')
                                        ->join('inward_products as inwp','inwp.inward_prd_id','=','inwpd.inward_prd_id')
                                        ->where('inwp.inward_id',$batch_id)
                                        ->where('inwp.product_id',$product_id)
                                        ->select('inwpd.mfg_date','inwpd.exp_date')->first();
                                    $mfg_date=isset($inwdPrdDetails->mfg_date)?$inwdPrdDetails->mfg_date:$mfg_date;
                                    $exp_date=isset($inwdPrdDetails->exp_date)?$inwdPrdDetails->exp_date:$exp_date;
                                    $batch_array[$bkey] = array("inward_id"=>$batch_id,
                                                "le_wh_id"=>$le_wh_id,
                                                "product_id"=>$product_id,
                                                "qty"=>$used_qty,
                                                'dit_qty'=>$dit_qty,
                                                'dnd_qty'=>$dnd_qty,
                                                'elp'=>$elp,
                                                'esp'=>$esp,
                                                'mfg_date'=>$mfg_date,
                                                'exp_date'=>$exp_date,
                                                "created_by"=>\Session::get('userId'),
                                                "updated_by"=>\Session::get('userId'),
                                                "main_batch_id"=>$mainbatch['main_batch_id']);
                                        //$main=$batch_array[$i]['main_batch_id'];
                                        //$i++;
                                }
                                $actual_qty = $req_qty - $used_qty;
                            }
                        }
                        $batch_history_array[] = array(
                            'inward_id'=>$batch_id,
                            'le_wh_id'=>$le_wh_id,
                            'product_id'=>$product_id,
                            'qty'=>$soh,
                            'ref'=>$refNo,
                            'ref_type'=>$refType,
                            'dit_qty'=>$dit_qty,
                            'dnd_qty'=>$dnd_qty,
                            'old_dnd_qty'=>$prevDndQty,
                            'old_dit_qty'=>$prevDitQty,
                            'comments'=>'GRN Created');
                    }
                }
                /**
                 * Add inventory log
                 */
                if(count($invLogs)) {
                    $this->addInQueueWithBulk($invLogs);
                }

                if(count($batch_array)) {
                    $batchModel->insertBatch($batch_array);
                }

                if(count($batch_history_array)) {
                    $batchModel->insertBatchHistory($batch_history_array);
                }

                if(isset($gds_batch_update) && $gds_batch_update != ""){
                    DB::unprepared($gds_batch_update);
                }

                if(isset($batch_inventory_update) && $batch_inventory_update != ""){
                    DB::unprepared($batch_inventory_update);
                }
                //DB::commit();commneted by nishanth
                return true;   
            }            
        }
        catch(Exception $e) {
            DB::rollback();//commented by nishanth
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * getInventory() method is used to get inventory detail
     * @param Number $productId
     * @param Number $leWhId
     * @return Obbject
     */

    public function getInventory($productId, $leWhId) {
        try {
            
            $data = DB::selectFromWriteConnection(DB::raw('select * from inventory where inventory.product_id = '.$productId.' AND inventory.le_wh_id='.$leWhId.' limit 1'));
            return isset($data[0])?(object)$data[0]:[];
            /*$fields = array('inventory.*');
            $query = DB::table('inventory')->select($fields);
            $query->where('inventory.product_id', $productId);
            $query->where('inventory.le_wh_id', $leWhId);
            return $query->first();*/
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }        
    }


    public function updateInventoryByProductIdAndWhId($fields, $product_id, $le_wh_id) {
        //DB::beginTransaction();
        try{
            if((int)$product_id > 0 && (int)$le_wh_id > 0) {
                DB::table('inventory')->where(array('le_wh_id'=>$le_wh_id, 'product_id'=>$product_id))->update($fields);
                //DB::commit();
                return true;
            }            
        }
        catch(Exception $e) {
            //DB::rollback();
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
            return false;
        }
    }

     public function addInQueueWithBulk($data) {

       $queue = new Queue();
       $data = json_encode($data);
       $data = base64_encode($data);
       $args = array("ConsoleClass" => 'inventoryLog', 'arguments' => array('insertbulk', $data));
       $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
    }

    public function addInQueue($data) {

       $queue = new Queue();
       $data = json_encode($data);
       $data = base64_encode($data);
       $args = array("ConsoleClass" => 'inventoryLog', 'arguments' => array('insert', $data));
       $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
    }


    public function getQtyByData($product_id,$le_wh_id,$req_qty,$offset=0,$batch_limit,$batches=[],$gds_orderID){
        $batch_data = DB::table("gds_orders_batch")
                //->where("le_wh_id",$le_wh_id)
                ->where("product_id",$product_id)
                ->where("ord_qty",">",0)
                ->where("gds_order_id",$gds_orderID)
                //->skip($offset)
                //->limit($batch_limit)
                //->orderby("inward_id","ASC")
                ->get()->all();
        //$offset = $batch_limit;
        //$batch_limit = $batch_limit + 10;
        $batches = json_decode(json_encode($batch_data), 1);
        /*foreach ($batch_data as $key => $value) {
            # code...
            //if($req_qty > 0){
                $batches[] = $value;
            //}
            //else{
              //  break;
            //}
            //$req_qty -= $value->ord_qty;
            //if($req_qty <= 0 ){
              //  break;
            //}
        }*/
        //if($req_qty > 0 && count($batch_data)){
            //$this->getQtyByData($product_id,$le_wh_id,$req_qty,$batch_limit,$batch_limit,$batches);
        //}else{
           // $batches = $batches;
        //}
        return $batches;
    }

}
