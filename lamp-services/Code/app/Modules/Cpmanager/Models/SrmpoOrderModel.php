<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;
use App\Modules\Grn\Models\Grn;
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Roles\Models\Role;


class SrmpoOrderModel extends Model
{
     
/*
* Function name: getInventory
* Description: used to get inventory
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 28th Sept 2016
*
* Modified Date & Reason:
*/

 public function getPolist($user_ids,$supplier_id) {

      if($user_ids !='' && $supplier_id ==''){

        $str =implode(",",$user_ids);
        
        $query=  DB::select(DB::raw("SELECT p.po_id,p.po_code,p.po_date as date,
          getMastLookupValue(p.po_status) as po_status,
          CASE 
                    WHEN p.approval_status=1 THEN 'Approved'
                    ELSE getMastLookupValue(p.approval_status)
                    END AS po_approval_status,
          le.business_legal_name as supplier_name FROM po p
        LEFT JOIN legal_entities le ON le.legal_entity_id=p.legal_entity_id 
       -- LEFT JOIN master_lookup ml ON ml.value=p.po_status
        WHERE  p.created_by IN ($str) ORDER BY p.created_at DESC
        "));  
    }
    else{

        $query=  DB::select(DB::raw("SELECT p.po_id,p.po_code,p.po_date as date
          ,getMastLookupValue(p.po_status) as po_status,
          CASE 
                    WHEN p.approval_status=1 THEN 'Approved'
                    ELSE getMastLookupValue(p.approval_status)
                    END AS po_approval_status,le.business_legal_name as supplier_name FROM po p
        LEFT JOIN legal_entities le ON le.legal_entity_id=p.legal_entity_id 
      --  LEFT JOIN master_lookup ml ON ml.value=p.po_status
        WHERE  p.legal_entity_id=".$supplier_id." ORDER BY p.created_at DESC
        ")); 

    }      
                
         return $query;
    
  }
  public function getPolistByStatus($status,$offset,$perpage,$count=0,$user_id=NULL) {
        $filterStatus = array('open'=>'87001', 'partial'=>'87005','closed'=>'87002', 'expired'=>'87003', 'canceled'=>'87004');
        $approvalStatuslist = ['initiated'=>57106,'created'=>57029,'verified'=>57030,'approved'=>57031,'posit'=>57033,'checked'=>57107,'receivedatdc'=>57034,'grncreated'=>57035,'shelved'=>57108,'payments'=>57032];
        $fieldArr =array('po.po_id','po.po_code','po.po_date',
                'le.business_legal_name as supplier',
                'le.le_code as supplier_code',
                DB::raw('(select SUM(po_products.sub_total) from po_products where po_products.po_id=po.po_id) as po_total'),
                DB::raw('getLeWhName(po.le_wh_id) as le_wh_name'),
                'po_status as po_status_val',
                DB::raw('getMastLookupValue(po.po_status) as po_status'),
                DB::raw('po.approval_status,CASE 
                    WHEN po.approval_status=1 THEN "Shelved"
                    ELSE getMastLookupValue(po.approval_status)
                    END AS approvalStatus'),
               DB::raw('getMastLookupValue(po.payment_status) as payment_status'),
               );
        $query = DB::table('po')->select($fieldArr);
        $query->join('legal_entities as le', 'le.legal_entity_id', '=', 'po.legal_entity_id');
        $query->join('legalentity_warehouses as lwh', 'lwh.le_wh_id', '=', 'po.le_wh_id');
        if (isset($status) && in_array($status, $filterStatus)) {
            if ($status == 87005) {
                $query->where('po.po_status', $status);
                $query->where('po.is_closed', 0);
            } else if ($status == 87002) {
                $query->where(function ($query) use($status) {
                    $query->where('po.po_status', $status)
                        ->orWhere('po.is_closed', 1);
                });
                $query->whereNotIn('po.approval_status', [1, null, 0]);
            } else if ($status == 87001) {
                $query->where('po.po_status', $status);
                $query->where('po.approval_status', '!=', 57117);
            } else if ($status == 87004) {
                $query->where(function ($query) use($status) {
                    $query->where('po.po_status', $status)
                        ->orWhere('po.approval_status', 57117);
                });
            } else {
                $query->where('po.po_status', $status);
            }
        }
        if (isset($status) && in_array($status, $approvalStatuslist)) {            
            if($status==57032){
                $query->where(function ($query) {
                    $query->where('po.payment_mode', 2);
                    $query->orWhere('po.payment_due_date', '<=',date('Y-m-d').' 23:59:59');
                });
                $query->where(function ($query) {
                    $query->where('po.payment_status', 57118);
                    $query->orWhereNull('po.payment_status');
                });
                $query->whereNotIn('po.approval_status', [57117,57106,57029,57030]);
            }else if($status==57107){
                $query->whereIn('po.approval_status', [57119,57120,$status]);
                $query->whereNotIn('po.approval_status', [57117]);
            }else if($status==57034){
                $query->whereIn('po.approval_status', [57122,$status]);
                $query->whereNotIn('po.approval_status', [57117]);
            }else if($status==57108){
                $query->whereIn('po.po_status',[87002,87005]);
                $query->where('po.approval_status',1);
                $query->whereNotIn('po.approval_status', [57117]);
            }else{
                $query->where('po.approval_status', $status);
            }
            $query->whereNotIn('po.po_status', [87003,87004]);
        }
        $this->_roleModel = new Role();
        $Json = json_decode($this->_roleModel->getFilterData(6,$user_id), 1);
        $filters = json_decode($Json['sbu'], 1);
        $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
        $query->whereIn('po.le_wh_id', explode(',',$dc_acess_list));
        if($count==1){
            $result = $query->count();
        }else{
            $query->skip($offset * $perpage)->take($perpage);
            $query->orderBy('po.po_id','desc');
            $result = $query->get()->all();
        }
        return $result;
    }

    /*
     * Function name: getPodetails
     * Description: used to get PO order details
     * Author: Ebutor <info@ebutor.com>
     * Copyright: ebutor 2016
     * Version: v1.0
     * Created Date: 29th Sept 2016
     * Modified Date & Reason:
     */

    public function getPodetails($po_id) {
        /**
         *  Supplier address
         */
        $Supplierdata = DB::table('legal_entities as le')
                ->select("le.business_legal_name as supplier_name", "le.legal_entity_id as supplier_id", "le.address1", "le.address2", "le.city", "z.name AS state", "coun.name AS country", "le.pincode", "us.mobile_no as phone_no", "us.email_id")
                ->leftJoin('po as p', 'p.legal_entity_id', '=', 'le.legal_entity_id')
                ->leftJoin('users as us', 'us.legal_entity_id', '=', 'le.legal_entity_id')
                ->leftJoin('countries as coun', 'coun.country_id', '=', 'le.country')
                ->leftJoin('zone as z', 'z.zone_id', '=', 'le.state_id')
                ->where("p.po_id", "=", $po_id)
                ->get()->all();
        $data['Supplier_address'] = $Supplierdata[0];
        /**
         *  Delivery address
         */
        $Deliverydata = DB::table('legalentity_warehouses as lw')
                ->select("lw.le_wh_id", "lw.lp_wh_name AS warehouse_name", "lw.address1", "lw.address2", "lw.city", "z.name AS state", "lw.pincode", "coun.name AS country", "lw.phone_no", "lw.email", "lw.contact_name")
                ->leftJoin('po as p', 'p.le_wh_id', '=', 'lw.le_wh_id')
                ->leftJoin('countries as coun', 'coun.country_id', '=', 'lw.country')
                ->leftJoin('zone as z', 'z.zone_id', '=', 'lw.state')
                ->where("p.po_id", "=", $po_id)
                ->get()->all();

        $data['Delivery_address'] = $Deliverydata[0];
        /**
         *  Po docs
         */
        $poDocsdata = DB::table('po_docs')
                ->select("doc_id","po_id", "file_path")
                ->where("po_id", "=", $po_id)
                ->get()->all();
        $data['PO_Docs'] = (count($poDocsdata)>0)?$poDocsdata:[];
        /**
         *  P.O Details
         */
        $query = DB::table('po as p')
                ->select("p.po_code AS PO_Number", "p.po_id", "p.le_wh_id", 
                        db::raw("getMastLookupValue(p.po_status) as master_lookup_name"), "p.approval_status as approval_status_val", 
                        "p.payment_status as payment_status_val",
                        DB::raw('IF(p.payment_mode=2,"Pre Paid", "Post Paid") as payment_mode'),
                        "p.payment_due_date", 
                        db::raw("getMastLookupValue(p.payment_status) as payment_status"), db::raw("CASE 
                    WHEN p.approval_status=1 THEN 'Shelved'
                    ELSE getMastLookupValue(p.approval_status)
                    END as approval_status"), "p.po_date", "p.delivery_date", "p.po_type", "us.firstname", DB::raw('sum(sub_total) as total_amount'), db::raw("IFNULL(p.apply_discount_on_bill,'') AS apply_discount_on_bill"), db::raw("IFNULL(p.discount_type,'') as discount_type"), db::raw("IFNULL(p.discount,'') as discount"))
                ->leftJoin('users as us', 'us.user_id', '=', 'p.created_by')
                ->leftJoin('po_products as prod', 'prod.po_id', '=', 'p.po_id')
                // ->leftJoin('master_lookup as ml','ml.value','=','p.po_status')
                ->where("p.po_id", "=", $po_id)
                ->get()->all();
        if ($query[0]->po_type == 1) {
            $po_type = 'Qty_Based';
        } else {
            $po_type = 'Value_Based';
        }
        $leWhId = $query[0]->le_wh_id;
        $POdata = array(
            'PO_Number' => $query[0]->PO_Number,
            'po_id' => $query[0]->po_id,
            'payment_mode' => $query[0]->payment_mode,
            'payment_due_date' => $query[0]->payment_due_date,
            'po_date' => $query[0]->po_date,
            'warehouse_id' => $query[0]->le_wh_id,
            'delivery_date' => $query[0]->delivery_date,
            'po_status' => $query[0]->master_lookup_name,
            'po_approval_status' => $query[0]->approval_status,
            'approval_status_val' => $query[0]->approval_status_val,
            'payment_status_val' => $query[0]->payment_status_val,
            'payment_status' => $query[0]->payment_status,
            'po_type' => $po_type,
            'total_amount' => $query[0]->total_amount,
            'created_by' => $query[0]->firstname,
            'apply_discount_on_bill' => $query[0]->apply_discount_on_bill,
            'discount_type' => $query[0]->discount_type,
            'discount' => $query[0]->discount
        );
        $data['PO_Details'] = $POdata;

        /**
         *  PO product details
         */
        $curdate = date('Y-m-d');
        $lastdate = date('Y-m-d', strtotime('-30 days')); //date 30 days ago
        $Prod_query = DB::table('po_products as p')
                ->select("p.product_id", "p.parent_id", "prod.sku", "prod.product_title","p.hsn_code", "p.qty", "packconfig.pack_id", "freepackconfig.pack_id as freepack_id", 
                        db::raw("getMastLookupValue(p.uom) as uom"), "p.uom as packsize_value", "p.free_uom as freepacksize_value", 
                        db::raw("getMastLookupValue(p.free_uom) as free_uom"), 
                        db::raw("CASE 
                    WHEN prod.kvi=69010 THEN 1
                    ELSE 0
                    END AS freebee_flag"), "p.no_of_eaches", "p.free_qty", "p.free_eaches", "prod.mrp", "p.unit_price", "p.price", "p.cur_elp",
                        "p.is_tax_included", "p.tax_name", "p.tax_per", "p.tax_amt", "p.sub_total", 
                        db::raw("IFNULL(p.apply_discount,'') as apply_discount"), 
                        db::raw("IFNULL(p.discount_type,'') as discount_type"), 
                        db::raw("IFNULL(p.discount,'') as discount"), 
                        DB::raw('(select slp from slp_history as slph where slph.product_id=p.product_id and slph.le_wh_id=po.le_wh_id and slph.supplier_id=po.legal_entity_id order by effective_date limit 0,1) as slp'), 
                        DB::raw('(select dlp from product_tot as tot where tot.product_id=p.product_id and tot.le_wh_id=po.le_wh_id and tot.supplier_id=po.legal_entity_id limit 0,1) as dlp'), 
                        DB::raw('(select min(elp) from purchase_price_history as pph where pph.product_id=p.product_id) as std'), 
                        DB::raw('(select min(elp) from purchase_price_history as pph where pph.product_id=p.product_id and effective_date between "' . $lastdate . '" and "' . $curdate . '") as thirtyd'), 
                        DB::raw("(SELECT available_inventory FROM vw_inventory_report WHERE product_id = p.product_id and le_wh_id = po.le_wh_id) as 'available_inventory'"), 
                        DB::raw('(select elp from purchase_price_history as pph where pph.product_id=p.product_id and pph.created_at < po.created_at and pph.po_id!=po.po_id order by effective_date desc limit 0,1) as prev_elp')
                )
                ->join('po', 'po.po_id', '=', 'p.po_id')
                ->leftJoin('products as prod', 'prod.product_id', '=', 'p.product_id')
                ->leftJoin('product_pack_config as packconfig', function($join) {
                    $join->on('packconfig.product_id', '=', 'p.product_id');
                    $join->on('packconfig.level', '=', 'p.uom');
                    $join->on('packconfig.no_of_eaches', '=', 'p.no_of_eaches');
                })
                ->leftJoin('product_pack_config as freepackconfig', function($join) {
                    $join->on('freepackconfig.product_id', '=', 'p.product_id');
                    $join->on('freepackconfig.level', '=', 'p.free_uom');
                    $join->on('freepackconfig.no_of_eaches', '=', 'p.free_eaches');
                })
                ->where("p.po_id", "=", $po_id)
                ->GROUPBY('p.product_id')
                ->get()->all();



        $Prod_data = json_decode(json_encode($Prod_query), true);

        $i = 0;
        foreach ($Prod_data as $val) {
            $product_id = $val['product_id'];
            /* if($val['is_tax_included'] == 1) {
              $basePrice=($val['price']/(100+$val['tax_per']))*100;
              }
              else {
              $basePrice=$val['price'];
              } */


            //Sandeep code          
            $this->_grnModel = new Grn();
            $shelfLife = $this->_grnModel->getProductShelfLife($product_id);
            $shelf_life = isset($shelfLife->shelf_life)?$shelfLife->shelf_life:'';
            $shelfuom = isset($shelfLife->master_lookup_name)?$shelfLife->master_lookup_name:'';
            $date = new \DateTime(date('Y-m-d H:i:s'));
            if ($shelf_life != 0) {
                $date->add(new \DateInterval('P' . $shelf_life . $shelfuom[0])); //new DateInterval('P7Y5M4DT4H3M2S')
                $exp_date = $date->format('Y-m-d H:i:s');
            } else {
                $exp_date = date('Y-m-d');
            }
            $now = time(); // or your date as well
            $datediff = strtotime($exp_date) - $now;
            $shelf_life_days = floor($datediff / (60 * 60 * 24));
            // check the doc dtypes
            $doctypes = array();
            $grnCodumentsTypes = new \App\Modules\Grn\Models\Dispute;
            $doctypes = $grnCodumentsTypes->getDocumentTypes();

            $l = 0;
            foreach ($doctypes as $key => $value) {

                $sample[$l]['key'] = $key;
                $sample[$l]['value'] = $value;
                $l++;
            }



            $data['doctypes'] = $sample;
            # extra packsizes linked with product

            $extrapacksizes = DB::table('product_pack_config as packconfig')
                    ->select("packconfig.pack_id", "packconfig.level", "ml.master_lookup_name as level_name", "packconfig.no_of_eaches as pack_size")
                    ->leftJoin('master_lookup as ml', 'ml.value', '=', 'packconfig.level')
                    ->where("packconfig.product_id", "=", $product_id)
                    ->get()->all();

            if (!empty($extrapacksizes)) {
                $extra_packsizes = json_decode(json_encode($extrapacksizes), true);
            } else {
                $extra_packsizes = '';
            }


            if ($val['is_tax_included'] == 1) {
                $up_withouttax = ($val['unit_price'] / (1 + ($val['tax_per'] / 100)));
            } else {

                $up_withouttax = $val['unit_price'];
            }

            $qty_det = $this->_grnModel->getGrnQtyByPOProductId($query[0]->po_id, $product_id);

            $remaing_qty = $val['qty'] * $val['no_of_eaches'];
            if (!empty($qty_det->orderd_qty)) {

                $remaing_qty = ($remaing_qty) - ($qty_det->tot_received);
            }
            $remaing_freebee_qty = $val['free_qty'] * $val['free_eaches'];
            if (!empty($qty_det->orderd_qty)) {

                $remaing_freebee_qty = ($remaing_freebee_qty) - ($qty_det->tot_free_received);
            }

            $poModel = new PurchaseOrder();
            $pendingRetQty = $poModel->pendingReturns($product_id, $leWhId);
            $openPOQty = $poModel->openPOQty($product_id, $leWhId);
            $net_sold_qty = $poModel->netSoldQty([$product_id], $lastdate . ' 00:00:00', $curdate . ' 23:59:59', $leWhId);
            $diff = date_diff(date_create($lastdate), date_create($curdate));
            $daysDiff = $diff->format("%a") + 1;
            $dateRange = $poModel->dateFunct($lastdate, $curdate);
            $daysCount = $daysDiff - count($dateRange);

            if ($net_sold_qty !== 0) {
                $avg_day_sales_eaches = $net_sold_qty / $daysCount;
            } else {
                $avg_day_sales_eaches = 0.0000;
            }
            $available_inventory = $val['available_inventory'];
            if ($avg_day_sales_eaches > 0) {
                $val['available_inventory'] = ($available_inventory + $pendingRetQty + $openPOQty) / $avg_day_sales_eaches;
            } else {
                $val['available_inventory'] = 0;
            }

            $productsData[$i] = array(
                'product_id' => $product_id,
                'parent_id' => $val['parent_id'],
                'product_name' => $val['product_title'],
                'hsn_code' => $val['hsn_code'],
                'sku' => $val['sku'],
                'quantity' => $val['qty'],
                'pack_id' => $val['pack_id'],
                'uom' => $val['uom'],
                'packsize_value' => $val['packsize_value'],
                'no_of_eaches' => $val['no_of_eaches'],
                'free_qty' => $val['free_qty'],
                'freepack_id' => $val['freepack_id'],
                'free_uom' => $val['free_uom'],
                'freepacksize_value' => $val['freepacksize_value'],
                'free_eaches' => $val['free_eaches'],
                'packs' => $extra_packsizes,
                'mrp' => $val['mrp'],
                'unit_price' => $val['unit_price'],
                'price' => $val['price'],
                'is_tax_included' => $val['is_tax_included'],
                'tax_name' => $val['tax_name'],
                'tax_per' => $val['tax_per'],
                'tax_amt' => $val['tax_amt'],
                'sub_total' => $val['sub_total'],
                'self_life_days' => $shelf_life_days,
                'up_withouttax' => $up_withouttax,
                'remaing_qty' => $remaing_qty,
                'remaing_freebee_qty' => $remaing_freebee_qty,
                'freebee_flag' => $val['freebee_flag'],
                'apply_discount' => $val['apply_discount'],
                'discount_type' => $val['discount_type'],
                'discount' => $val['discount'],
                'slp' => $val['slp'],
                'std' => $val['std'],
                'thirtyd' => $val['thirtyd'],
                'dlp' => $val['dlp'],
                'prev_elp' => $val['prev_elp'],
                'cur_elp' => $val['cur_elp'],
                'available_inventory' => ceil($val['available_inventory']),
            );

            $i++;
        }

        $data['products']=$productsData; 
        
         return $data;
    
  }
public function getPoinfo($po_id) {
        try {
            $data = DB::table('po')
                    ->select("po.po_code", "po.po_id", "po.le_wh_id", "po.po_date", "po.delivery_date", "po.po_type", "po.approval_status","po.po_status","po.payment_status")
                    ->where("po.po_id", "=", $po_id)
                    ->first();
            return $data;
        } catch (Exception $e) {
            return Response::json(array('Status' => 404, 'Message' => 'Failed'));
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

  /*
* Function name: getStateId
* Description: used to get PO order details
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/
 public function getStateId($srm_token,$le_wh_id) {
      
      /**
      *  Seller state id
      */
                
          $Sellerdata = DB::table('users as u')
          ->select("le.state_id")
          ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
          ->where("u.password_token","=",$srm_token)          
          ->get()->all();

           $data['seller_state_id']=$Sellerdata[0]->state_id;  

      /**
      *  buyer_state_id
      */  

        
           $buyerdata = DB::table('legalentity_warehouses as lw')
            ->select("lw.state")
            ->where("lw.le_wh_id","=",$le_wh_id)          
            ->get()->all();   

           $data['buyer_state_id']=$buyerdata[0]->state;

           return $data;
}


/*
* Function name: packsizeStatus
* Description: used to get  packsizeStatus
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/
 public function packsizeStatus($value) {
      
      
      $status = DB::table('master_lookup')
            ->select("master_lookup_name")
            ->where("value","=",$value)          
            ->get()->all();   

           $data=$status[0]->master_lookup_name;

           return $data;
}

/*
* Function name: CalculateEaches
* Description: used to Calculate No of Eaches 
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 29th Sept 2016
* Modified Date & Reason:
*/
 public function CalculateEaches($product_id,$packsizestatus,$pack_id) {

    
      $eaches = DB::table('product_pack_config')
            ->select("no_of_eaches")
            ->where("product_id","=",$product_id)    
            ->where("level","=",$packsizestatus)  
            ->where("pack_id","=",$pack_id)     
            ->get()->all();
     
     
            $data=$eaches[0]->no_of_eaches;   

           return $data;
}


/*
* Function name: editPO
* Description: editPO function is used to fetch the slab rates of the product_id 
  passed with respective poid & product id.
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 18th Oct 2016
* Modified Date & Reason:
   */  


public function editPO($po_id,$product_id,$le_wh_id,$user_id) {
 

     $data = DB::select("CALL getPurchaseSlabs($product_id,$le_wh_id,$user_id)");

     $products_data=json_decode(json_encode($data[0]),true);    

     $poData = DB::table('po_products')
          ->select("qty","free_qty")
          ->where("po_id","=",$po_id)
          ->where("product_id","=",$product_id)          
          ->get()->all(); 

     $po_products=json_decode(json_encode($poData[0]),true); 

     $result = array_merge($products_data,$po_products);

    
     return $result;

}


/*
  * Function Name: getPOMasterlookupdata()
  * Description: Used to get PO related data
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 24th Nov 2016
  * Modified Date & Reason:
  */

  public function getPOMasterlookupdata(){


    $Payment_type_data = DB::table("master_lookup")
    ->select("master_lookup_name as name","value")
    ->where("mas_cat_id","=",22) 
    ->where("is_active","=",1)   
    ->get()->all();


    $data['payment_type']=$Payment_type_data; 


    $data['payment_mode'][0]=array(
    'value'=>1,
    'name'=>'Post Paid');
    $data['payment_mode'][1]=array(
    'value'=>2,
    'name'=>'Pre Paid');



    $Paid_through_data = DB::table("tally_ledger_master")
    ->select("tlm_id","tlm_name","tlm_group")  
    ->get()->all();


    $data['paid_through']=$Paid_through_data;

    return $data; 


  }


/*
  * Function Name: FindfreebieId()
  * Description: find  frebie prd based on input product id
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 28th Nov 2016
  * Modified Date & Reason:
  */


public function FindfreebieId($prod_array) {

    foreach ($prod_array as $key => $value) {

    $freeProdid = DB::table("freebee_conf")
    ->select("free_prd_id","main_prd_id")
    ->where("main_prd_id","=",$value['product_id']) 
    ->get()->all();

    if(!empty($freeProdid)) { 

    $data[]=$freeProdid[0];

    }
    else{

    $data[]=0;
    }

    } 

    return json_decode(json_encode($data),true);

}

/*
  * Function Name: checkfreebieproduct()
  * Description: check  frebie prd based on kvi=Q9 & is_sellable=0
  * Author: Ebutor <info@ebutor.com>
  * Copyright: ebutor 2016
  * Version: v1.0
  * Created Date: 30th Nov 2016
  * Modified Date & Reason:
  */


public function checkfreebieproduct($prod_id) {


  $freeProdid = DB::table("products")
    ->select("product_title")
    ->where("kvi","=",69010) 
    ->where("is_sellable","=",0)
    ->where("product_id","=",$prod_id)
    ->get()->all();

    $product_title= DB::table("products")
    ->select("product_title")
    ->where("product_id","=",$prod_id)
    ->get()->all();

    if(!empty($freeProdid)){
      
      $data['status']='true';
      $data['product_name']=$product_title[0]->product_title;

    }else{

      $data['status']='false';
      $data['product_name']=$product_title[0]->product_title;
    }

return $data;

}



}


   