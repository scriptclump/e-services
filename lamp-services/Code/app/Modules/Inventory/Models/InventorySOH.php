<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use DB;

class InventorySOH extends Model {

    protected $table = "stock_transfer_grid";

     // checkTheSkuWithIssellableAndCpenabled
    public function checkTheSkuWithIssellableAndCpenabled($oldsku){

         $count = DB::table('products as p')
                ->where('p.sku', '=', $oldsku)
                ->first();
        return $count;
    }


       // save into stock transfer grid table
        public function saveIntoGridTable($savegriddata){
            $save=DB::table('stock_transfer_grid')->insert($savegriddata);
            $lastid = DB::getPdo()->lastInsertId($save);
            return $lastid;
        }

        // save into details table
        public function saveIntodetailsTable($saveintotransferdetails){
           $saveintodetails = DB::table('stock_transfer_details')->insert($saveintotransferdetails);
            return $saveintodetails;
        }

        // view all the sku details tickets
        public function getAllTheStockDetailsForView($id){
            $viewdetails = DB::table('stock_transfer_details as std')
            ->where('std.st_id','=',$id)
            ->get()->all();
            return $viewdetails;
        }
        public function checkTheOldSkuExistOrNot($sku,$le_wh_id){

            //$le_wh_id = env('LE_WH_ID');
            $sqlData = DB::table("inventory as inv")
                        ->leftjoin("products as p","p.product_id", "=", "inv.product_id")
                        ->where("p.sku","=",$sku)
                        ->where("inv.le_wh_id","=",$le_wh_id)
                        ->first();
         return $sqlData;
        }
        public function updateStatusInTableInventory($id,$status){
            $updatestatus = DB::table('stock_transfer_details')->where('st_id', '=', $id)->update(['approval_status' => $status]);

        }

        // get the old sku values form db
        public function getTheoldSkuValue($stid){

            $viewdetails = DB::table('stock_transfer_details as std')
            ->select('old_sku','old_sku_id','new_sku_id','new_sku')
            ->where('std.st_id','=',$stid)
            ->get()->all();
            return $viewdetails;
        }

        public function getThedataProductId($skuname){
             $getdetails = DB::table('products as p')
            ->select('product_id')
            ->where('p.sku','=',$skuname)
            ->first();
            return $getdetails;
        }

        // gete the old sku values
        public function getThevaluesFromInventory($productid){
            $le_wh_id = env('LE_WH_ID');
             $getdetails = DB::table('inventory as in')
            ->select('dit_qty','dnd_qty','quarantine_qty','soh','reserved_qty')
            ->where('in.product_id','=',$productid)
            ->where('in.le_wh_id','=',$le_wh_id)
            ->get()->all();
            return $getdetails;
        }

        // update new sku colum dit,dng,soh,quqrantine
        public function updateToNewSkuInventory($new_sku_product_id,$old_sku_product_id,$oldskudata){
            foreach($oldskudata as $data){
            $le_wh_id = env('LE_WH_ID');
            DB::beginTransaction();
            $updateIntoNewSku = DB::table('inventory')->where('product_id', '=', $new_sku_product_id)->where('le_wh_id','=',$le_wh_id)->update(['dit_qty' => DB::raw('(dit_qty+'.$data['dit_qty'].')') ,'dnd_qty' =>DB::raw('(dnd_qty+'.$data['dnd_qty'].')'),'quarantine_qty'=>DB::raw('(quarantine_qty+'.$data['quarantine_qty'].')'),'soh'=>DB::raw('(soh+'.$data['soh'].')') ] );

            // update the product table with new coloumn
                $updateIntoProducts = DB::table('products')->where('product_id', '=', $old_sku_product_id)->update(['new_sku_id' => $new_sku_product_id]);
                // dit,dnd,soh should be zero in old sku
            $updateIntoOldSku = DB::table('inventory')->where('product_id', '=', $old_sku_product_id)->update(['dit_qty' => 0,'dnd_qty' =>0,'quarantine_qty'=>0,'soh'=>0]);
            DB::commit();

            }
        }

        public function checkTheOldSkuInTable($old_sku){

             $getdetails = DB::table('stock_transfer_details as st')
            ->where('st.old_sku','=',$old_sku)
            ->where('st.approval_status','!=',1)
            ->get()->all();
            return $getdetails;

        }

        public function checkEspElpIn_APOB_DC($newsku,$fromdcid,$todcid){
//check if product has els,esp in fc,dc,apob
            $product_id=$this->checkTheSkuWithIssellableAndCpenabled($newsku);
            $product_id=$product_id->product_id;
            $getfromdclegalentitytype=$this->apobToApobTransfer($fromdcid);
            $gettodclegalentitytype=$this->apobToApobTransfer($todcid);
            $checktransferingdcelpesp=$this->checkElpEspInDCAPOB($product_id,$fromdcid);
            $checktransfereddcelpesp=$this->checkElpEspInDCAPOB($product_id,$todcid);

            if($getfromdclegalentitytype==1002 && $gettodclegalentitytype==1002 && !empty($checktransferingdcelpesp->dlp) && !empty($checktransfereddcelpesp->dlp) && $checktransfereddcelpesp->esp==$checktransferingdcelpesp->esp){
               
                   return true;
                
            }elseif(!empty($checktransferingdcelpesp->dlp) && !empty($checktransfereddcelpesp->dlp) && $checktransfereddcelpesp->esp==$checktransferingdcelpesp->esp){

                   return $checkespelp=$this->checkEspElpIn_Parent_DC_APOB($product_id,$todcid);
            }else{
                return false;
            }


        }

        public function checkEspElpIn_Parent_DC_APOB($product_id,$dcid){
            
            $parent_dc_apob_id=$this->getDCFCMappingLeWhID($dcid);
            $getelpespinchild=$this->checkElpEspInDCAPOB($product_id,$dcid);

            if(is_numeric($parent_dc_apob_id) && !empty($getelpespinchild[0]->dlp) && !empty($getelpespinchild[0]->esp)){
                $getelpespparent=$this->checkElpEspInDCAPOB($product_id,$parent_dc_apob_id);
                
                if(!empty($getelpespparent[0]->dlp)  && $getelpespparent[0]->esp==$getelpespinchild[0]->esp){
                    return $this->checkEspElpIn_Parent_DC_APOB($product_id,$parent_dc_apob_id);
                }elseif($parent_dc_apob_id==0){
                    return true;
                }else{
                    return false;
                }   
            }elseif(empty($getelpespinchild[0]->dlp) || empty($getelpespinchild[0]->esp)){
                return false;
            }else{
                return true;
            }
            
        }

        public function getDCFCMappingLeWhID($dcid){
            $getlewhid=DB::table('dc_fc_mapping')->select('dc_le_wh_id')->where('fc_le_wh_id',$dcid)->first();
            $dc_le_wh_id=isset($getlewhid->dc_le_wh_id)?$getlewhid->dc_le_wh_id:0;
            return $dc_le_wh_id;
        }

        public function apobToApobTransfer($dcid){

            $checkifbothdcsorapobs=DB::table('legalentity_warehouses as lw')->join('legal_entities as le','lw.legal_entity_id','=','le.legal_entity_id')->select('le.legal_entity_type_id')->where('lw.le_wh_id',$dcid)->first();
            return $checkifbothdcsorapobs->legal_entity_type_id;

        }

        public function checkElpEspInDCAPOB($product_id,$dcid){
            $checkespelpinstockstransferdc="select dlp,getProductEsp_wh(".$product_id.",".$dcid.") as esp from product_tot where le_wh_id in (".$dcid.") and product_id in (".$product_id.") order by prod_price_id desc limit 1";
            $checkespelpinstockstransferdc=DB::selectFromWriteConnection(DB::raw($checkespelpinstockstransferdc));

            return $checkespelpinstockstransferdc;

        }

}


