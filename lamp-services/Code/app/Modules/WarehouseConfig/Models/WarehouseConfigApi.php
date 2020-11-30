<?php

namespace App\Modules\WarehouseConfig\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\PurchaseOrder\Controllers\PurchaseOrderController;
use DB;
use Log;
use Session;
Class WarehouseConfigApi extends Model
{

    protected $table = "warehouse_config";
    protected $primaryKey = 'wh_loc_id';
    public $binLocationLoop=array();
    public $binArray=array("0"); 
    public $locationArray=array();
    public $nonLocationArray=array();
    public $productBinConfigArray=array();
    public $listArray='';
    public function getBinLocationLoop($parent_id,$wh_id)
    {
        $wh =DB::table('warehouse_config')
            ->where('wh_loc_id', $parent_id)
            ->where('le_wh_id', $wh_id)
            ->first();
        $wh1= json_decode(json_encode($wh),1);
        if(!empty($wh))
        {
            if($wh1['parent_loc_id']!=0)
            {
                $level_name=$this->getMasterLookupNameByValue($wh1['wh_location_types']);
                $this->binLocationLoop[]=array('level_type'=>$level_name,'location_name'=>$wh1['wh_location']);
                $this->getBinLocationLoop($wh1['parent_loc_id'],$wh_id);
            }
        }
    }
    public function getBinLocationbyProIdData($data)
    {

        if(!empty($data) && !empty($data['wh_id']) && !empty($data['capacity']) && !empty($data['product_id']))
        {
            $queryRs=DB::table('warehouse_config')->where('res_prod_grp_id',$data['product_id'])->where('le_wh_id',$data['wh_id'])->where('capacity','>=',$data['capacity'])->first();
            if(!empty($queryRs))
            {
                $this->getBinLocationLoop($queryRs->wh_loc_id,$queryRs->le_wh_id);   
                return  json_encode(array('status'=>"success",'message'=>'Get bin location by product id and warehoue id','data'=>json_encode($this->binLocationLoop)));  
            }
            else
            {                
                $queryRs=DB::table('warehouse_config')->where('le_wh_id',$data['wh_id'])->where('capacity','>=',$data['capacity'])->first();
                if(!empty($queryRs))
                {
                    $this->getBinLocationLoop($queryRs->wh_loc_id,$queryRs->le_wh_id);   
                    return  json_encode(array('status'=>"success",'message'=>'Get bin location by product id and warehoue id','data'=>json_encode($this->binLocationLoop)));  
                }
                else
                {
                    return  json_encode(array('status'=>"failed",'message'=>'Please create new bin for this capacity','data'=>""));  
                }
            }
        }
        else
        {
             return json_encode(array('status'=>"failed",'message'=> 'Invaid data','data'=>""));  
        }
    }
    public function showInvByBinIdData($data)
    {
        if(!empty($data) && !empty($data['bin_id']))
        {
            $queryRs=DB::table('product_bin_mapping')->where('bin_id',$data['bin_id'])->select('product_id','qty as inv')->first();
            if(!empty($queryRs))
            {
                return  json_encode(array('status'=>"success",'message'=>'check how many inventory it have for respective bin','data'=>json_encode($queryRs)));
            }else
            {
                return json_encode(array('status'=>"failed",'message'=> 'There is no product  configuration for this bin','data'=>""));  
            }   
        }else
        {
            return json_encode(array('status'=>"failed",'message'=> 'Invaid data','data'=>""));  
        }
    }
    //save product wise capacity and bin id into bin_maping  table
    public function deriveBinCapacityByProductIdData($data)
    {
        DB::enablequerylog();
        if(!empty($data) && !empty($data['product_id']) && !empty($data['capacity']) && !empty($data['wh_id']) && !empty($data['bin_type']))
        {
             $rs = DB::Table('product_bin_config')
                    ->where('product_id','=',$data['product_id'])
                    ->where('wh_id','=',$data['wh_id'])
                    ->select('product_id')
                    ->get()->all();
            if(!empty($rs))
            {
                $checkCapacityRs= DB::Table('warehouse_config as wh_conf')
                                    ->join('product_bin_mapping as pro_bin_map','pro_bin_map.bin_id','=','wh_conf.wh_loc_id')
                                    ->where('wh_loc_id','=',$data['wh_id'])
                                    ->where('res_prod_grp_id','=',$data['product_id'])
                                    ->select('wh_loc_id')
                                    ->get()->all();
                             $rrr=Db::getquerylog();
                             print_r(end($rrr));
                             die();
                                    print_r($checkCapacityRs);
                return  json_encode(array('status'=>"success",'message'=>'Derived bin level capacity by product id','data'=>json_encode($rs)));
            }else
            {
                $rs1 = DB::Table('product_bin_config')->insert([ 
                    'product_id'=>$data['product_id'],                    
                    'wh_id'=>$data['wh_id'],
                    'bin_type'=>$data['bin_type'],
                    'qty'=>$data['capacity']]); 
                return json_encode(array('status'=>"success",'message'=> "Successfully saved derived bin capacity by product id",'data'=>$rs1));  
            }   
        }
        else
        {
            return json_encode(array('status'=>"failed",'message'=> 'Invaid data','data'=>""));  
        } 
    }
     public function checkBinInventoryByBinCodeData($data)
    {
        DB::enablequerylog();
        if(!empty($data) && !empty($data['bin_code']) && isset($data['wh_id']))
        {  
            $rs = DB::Table('bin_inventory as bin_inv')
                    ->join('warehouse_config as wh_config','wh_config.wh_loc_id','=','bin_inv.bin_id')
                    ->leftjoin('products','products.product_id','=','wh_config.pref_prod_id')
                    ->leftjoin('inventory as inv',function($join)
                             {
                                 $join->on('inv.product_id','=','wh_config.pref_prod_id');
                                 $join->on('inv.le_wh_id','=','wh_config.le_wh_id');
                             })
                    ->leftjoin('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_config.bin_type_dim_id')
                    ->leftjoin('product_bin_config as bin_config',function($join)
                             {
                                 $join->on('bin_config.bin_type_dim_id','=','bin_dim.bin_type_dim_id');
                                 $join->on('bin_config.prod_group_id','=','wh_config.res_prod_grp_id');
                             })
                    ->leftjoin('master_lookup','master_lookup.value','=','bin_config.pack_conf_id')
                    ->select('wh_location as bin_code','wh_loc_id as bin_id','product_title as product_name','products.product_id','mrp','master_lookup.master_lookup_name as pack_type','bin_inv.qty as bin_item_qty',DB::raw('if(inv.soh is not null,inv.soh,0 ) as SOH_eaches'))
                    ->where('wh_location','=',$data['bin_code'])
                    ->where('wh_config.le_wh_id','=',$data['wh_id'])
                    ->groupby('bin_inv.bin_id')
                    ->get()->all();
            if(!empty($rs))
            {
                $packInfo= $this->getPackInfo($rs[0]->product_id);
                $rs[0]->config=$packInfo;
                if(!empty($rs[0]->config=$packInfo))
                {
                    //  $pack_info=$this->getPackInfo($);
                    return  json_encode(array('status'=>"success",'message'=>'Bin Information','data'=>(object)$rs[0]));
                }
                else
                {
                     return json_encode(array('status'=>"failed",'message'=> 'There is no product configuration for this bin','data'=>""));  
                }
            }else
            {
                return json_encode(array('status'=>"failed",'message'=> 'There is no product configuration for this bin','data'=>""));  
            }   
        }else
        {
             return json_encode(array('status'=>"failed",'message'=> 'Invaid data','data'=>""));
        }
    }
    public function getPackInfo($pid)
    {
        $getProPackInfo = DB::Table('product_pack_config')
                        ->join('master_lookup','master_lookup.value','=','product_pack_config.level')
                        ->where('product_id',$pid)
                        ->select('product_pack_config.level as pack_type_id','master_lookup.master_lookup_name as pack_type','no_of_eaches','pack_sku_code as EAN','is_cratable')
                        ->get()->all();
        return $getProPackInfo;
    }
    public function poGrnProductsWithBinLocationData($data)
    {
        DB::enablequerylog();
        $dataArray=array();
        $packLevelQty=array();
        if(isset($data['putaway_list_id']) && isset($data['bin_type_id']))
        {
            $getPutawayListId = DB::table('putaway_list')
                            ->select('putaway_source as type','source_id as id')
                            ->where('putaway_id',$data['putaway_list_id'])
                            ->first();
            $getPutawayListId = json_decode(json_encode($getPutawayListId),true);
            if(!empty($getPutawayListId))
            {
               $id = $getPutawayListId['id'];
               $type = $getPutawayListId['type'];
                $GRNData = DB::Table('products as pro')
                            ->join('putaway_allocation as putaway','putaway.prod_id','=','pro.product_id')                            
                            ->join('warehouse_config as wh_config','wh_config.wh_loc_id','=','putaway.bin_id')
                            ->select('putaway.bin_id as bin_id','bin_type_dim_id','wh_location','wh_id','pro.product_id AS product_id','product_group_id','product_title','sku','mrp','putaway.qty as grn_qty','putaway_list_id','putaway.total_qty as tot_grn_qty','putaway.pending_qty','putaway.placed_qty')
                            ->where('putaway.putaway_list_id','=',$data['putaway_list_id'])
                            ->where('putaway.bin_type','=',$data['bin_type_id'])
                            ->where('putaway.pending_qty','!=',0)
                            ->get()->all();
                if(!empty($GRNData))
                {
                   $GRNData= json_decode(json_encode($GRNData),true);
                    foreach ($GRNData as $GRNvalue)
                    {
                        $prd = DB::Table('product_pack_config as pro_pack')
                                ->join('products as pro','pro_pack.product_id','=','pro.product_id')
                                ->select(db::raw("pro_pack.product_id,pro_pack.level AS pack_level,pro.product_title,pro_pack.pack_sku_code as ean_number,no_of_eaches AS   qty,pro.sku"))
                                ->where('pro_pack.product_id', $GRNvalue['product_id'])
                                ->get()->all(); 
                        $binTypeName= $this->getBinTypeName($GRNvalue['bin_type_dim_id']);        
                        $res[] = array('bin_code'=>$GRNvalue['wh_location'],'bin_id'=>$GRNvalue['bin_id'],'bin_type_name'=>$binTypeName);

                        $tot_grn_qty=(empty($GRNvalue['tot_grn_qty']))?$GRNvalue['grn_qty']:$GRNvalue['tot_grn_qty'];
                        //get pack type name and pack qty by inwr
                        if($type == "GRN")
                        {
                            $packLevelQty = $this->getGRNPackTypeQtyByPutawayId($id);
                        }
                        $cfcCount =$this->getProductPackEaches($GRNvalue['product_id'],"16004");
                        $cfcEaches = "";
                        if(!empty($cfcCount))
                        {
                            $cfcEaches = number_format($GRNvalue['pending_qty']/$cfcCount['no_of_eaches'],2);
                        }
                        $subinnerCount =$this->getProductPackEaches($GRNvalue['product_id'],"16003");
                        $subinnerEaches = "";
                        if(!empty($subinnerCount))
                        {
                            $subinnerEaches = number_format($GRNvalue['pending_qty']/$subinnerCount['no_of_eaches'],2);
                        }
                        $subinnerCount =$this->getProductPackEaches($GRNvalue['product_id'],"16002");
                        $subinnerEaches = "";
                        if(!empty($subinnerCount))
                        {
                            $subinnerEaches = number_format($GRNvalue['pending_qty']/$subinnerCount['no_of_eaches'],2);
                        }
                        $innerCount =$this->getProductPackEaches($GRNvalue['product_id'],"16003");
                        $innerEaches = "";
                        if(!empty($innerCount))
                        {
                            $innerEaches = number_format($GRNvalue['pending_qty']/$innerCount['no_of_eaches'],2);
                        }
                        //$totGrnQty = $this->getGrnQty($id,$GRNvalue['product_id']);
                        $dataArray[]=['wh_id'=>$GRNvalue['wh_id'],'product_id'=>$GRNvalue['product_id'],'putaway_list_id'=>$data['putaway_list_id'],'product_title'=>$GRNvalue['product_title'],'sku_code'=>$GRNvalue['sku'],'mrp'=> number_format($GRNvalue['mrp'],2,'.',''),'GRN_qty'=>$tot_grn_qty,'qty'=>$GRNvalue['grn_qty'],'cfc_qty'=>$cfcEaches,"subinner_qty"=>$subinnerEaches,"inner_qty"=>$innerEaches,'bin_grn_qty'=>$GRNvalue['grn_qty'],'placed_qty'=>$GRNvalue['placed_qty'],'pending_qty'=>$GRNvalue['pending_qty'],'bin_config'=>$res,'bin_status'=>"Assigned",'EAN_number'=>"",'pack_config'=>$prd,"putaway_pack_level"=>$packLevelQty];        
                         $res =array();  
                    }
                }
                 return json_encode(array('status'=>"success",'message'=> 'Products List.','data'=>$dataArray)); 
            }else
            {
                return json_encode(array('status'=>"failed",'message'=> 'Putaway list wrong entry.','data'=>"")); 
            }           
        }
        else
        {
            return json_encode(array('status'=>"failed",'message'=> 'Invaid parameters.','data'=>""));
        }
    }
    public function getGrnQty($grn_id,$pid)
    {
        $productIdList = DB::table('inward as inw')
                                ->join('inward_products AS inw_pro','inw_pro.inward_id','=','inw.inward_id')
                                ->join('inward_product_details AS inw_det','inw_det.inward_prd_id','=','inw_pro.inward_prd_id')
                                ->select(DB::raw('sum(inw_det.tot_rec_qty) as tot_rec_qty'))
                                ->where('inw.inward_id',$grn_id)
                                ->where('inw_det.product_id','=',$pid)
                                ->first(); 
        $productIdList = json_decode(json_encode($productIdList),true); 
        return $productIdList['tot_rec_qty'];
    }
    public function getPlacedGrnQty($pid,$putaway_list_id,$bin_id){
        $rs= DB::table('putaway_allocation')
            ->select(DB::raw('sum(placed_qty) as qty'))
            ->where('putaway_list_id',$putaway_list_id)
            ->where('prod_id',$pid)
            ->where('bin_id',$bin_id)
            ->first();

        $rs= json_decode(json_encode($rs),true);
        if(empty($rs['qty']))
        {
            $rs =0;
        }else
        {
            $rs = $rs['qty'];
        }
        return $rs;
    }
    public function getProductPackLevelEachesVolumn($pid){
         $eachLevel=DB::table('product_pack_config')
                    ->select(DB::raw('length*breadth*height as volumn'))
                    ->where('level','=',16001)
                    ->where('product_id',$pid)
                    ->first();
        return json_decode(json_encode($eachLevel),true);
    } 
    public function getBinQty($bin_id){
        $checkAlloQty = DB::table('putaway_allocation')
                            ->select(DB::raw('sum(qty) as qty'))
                            ->where('is_active','=',1)
                            ->where('bin_id',$bin_id)
                            ->first();
        $checkAlloQty = json_decode(json_encode($checkAlloQty),true);
        $binInvQty = DB::table('bin_inventory')
                    ->where('bin_id',$bin_id)
                    ->select('qty')
                    ->first();
        $binInvQty = json_decode(json_encode($binInvQty),true);
        $placedQty = $checkAlloQty['qty'];
        if(!empty($binInvQty))
        {
            $placedQty = $placedQty+$binInvQty['qty'];
        }
        $placedQty = array('placed_qty'=>$placedQty);
    return json_decode(json_encode($placedQty),true);
    }
     public function getPlacedQty($inward_id,$pid)
    {
         $placedQty=DB::table('putaway_allocation')
                    ->select(DB::raw('sum(placed_qty) as placed_qty'))
                    ->where('prod_id',$pid)
                    ->where('putaway_list_id',$inward_id)
                    ->first();
        return json_decode(json_encode($placedQty),true);
    }
    public function getBinDimByBinId($binDimId)
    {
        $rs = DB::Table('bin_type_dimensions')                
            ->select('length','breadth','heigth')
            ->where('bin_type_dim_id',$binDimId)
            ->first();
         $rs= json_decode(json_encode($rs),true);
         $binVolumn= $rs['length']*$rs['breadth']*$rs['heigth'];
         return $binVolumn;
    }
    public function getBinAvailableQty($bin_id,$wh_id)
    {
        $rs = DB::Table('putaway_allocation as putaway')
                ->leftjoin('product_bin_mapping as pro_map','pro_map.put_away_id','=','putaway.allocation_id')
            ->select(DB::raw("SUM(CASE WHEN (is_active = '0') THEN placed_qty ELSE 0 END) as placed_qty"))  
            ->where('bin_id',$bin_id)
            ->where('wh_id',$wh_id)
            ->first();
            return json_decode(json_encode($rs),true);
    }
     public function reservedBinByBinId($bin_id,$wh_id,$pro_id,$inward_id,$tot_qty,$picker_id,$pack_type,$proGrpId)
    {
        $checkReseredRs = DB::table('putaway_allocation')
                        ->where('wh_id',$wh_id)
                        ->where('bin_id',$bin_id)
                        ->where('inbound_id',$inward_id)
                        ->where('is_active',1)
                        ->where('pack_level',$pack_type)
                        ->first();
        $checkReseredRs = json_decode(json_encode($checkReseredRs),true);
        if(!empty($checkReseredRs))
        {
            DB::table('putaway_allocation')
            ->where('wh_id',$wh_id)
            ->where('bin_id',$bin_id)
            ->where('inbound_id',$inward_id)
            ->where('is_active',1)
            ->where('pack_level',$pack_type) 
            ->delete();
        }
        //adding product to bin level in warehouse config proGrpId   
            $rs=  DB::table('warehouse_config')
                ->where('le_wh_id',$wh_id)
                ->where('wh_loc_id',$bin_id)
                ->update(array('res_prod_grp_id'=>$proGrpId,'pref_prod_id'=>$pro_id));            
            $rs1 = DB::Table('putaway_allocation')->insert([ 
                'wh_id'=>$wh_id,                    
                'bin_id'=>$bin_id,
                'prod_id'=>$pro_id,
                'pack_level'=>$pack_type,
                'inbound_id'=>$inward_id,
                'qty'=>$tot_qty,
                'is_active'=>1,
                'created_by'=>$picker_id]); 
            return $rs1;
    }
    public function getEmptyBinBySortOrder($wh_id,$productVolumn,$actual_qty){
        $alloc_bin=DB::table('putaway_allocation')
                    ->where('is_active',1)
                    ->pluck('bin_id')->all();
        $alloc_bin=json_decode(json_encode($alloc_bin),true);      
        $rs = DB::Table('warehouse_config as wh_conf')
            ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
            ->select('le_wh_id','wh_location','wh_conf.bin_type_dim_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height','sort_order','wh_conf.wh_loc_id as wh_loc_id')
            ->where('le_wh_id',$wh_id)
            ->where('wh_location_types','=','120006')
            ->whereNotIN('wh_conf.wh_loc_id',$alloc_bin)
            ->groupby('wh_conf.wh_loc_id')
            ->orderBy('sort_order','desc')
            ->get()->all();
        $rs=json_decode(json_encode($rs),true);
        $data='';
        $getTotBinQty='';
        $checkCap='';
        if(!empty($rs))
        {
            foreach ($rs as $rsValue) 
            {
                $binPlaceQty=$this->getBinQty($rsValue['wh_loc_id']);
                $binPlaceQty=(empty($binPlaceQty['placed_qty']))?0:$binPlaceQty['placed_qty'];
                $binCap=$rsValue['length']*$rsValue['breadth']*$rsValue['height'];
                $placedQtyVolumn=$binPlaceQty*$productVolumn;
                $readyToPlaceQtyVolumn=$actual_qty*$productVolumn;
                $totVolumn=$placedQtyVolumn;
                $binCapPlacedCap = $binCap-$totVolumn;
                $bin_qty = $binCapPlacedCap/$productVolumn;
                if(($binCap >0 && $binCap >= $totVolumn) && (floor($bin_qty)>0))
                {
                    $checkCap= $actual_qty-floor($bin_qty);
                    if($checkCap > 0)
                    {
                         $data[]=array('wh_location'=>$rsValue['wh_location'],'wh_loc_id'=>$rsValue['wh_loc_id'],'le_wh_id'=>$rsValue['le_wh_id'],'bin_qty'=>floor($bin_qty),'bin_type_dim_id'=>$rsValue['bin_type_dim_id']);                       
                    }else
                    {
                        $data='';
                    }
                }
            }
        }    
        return $data;
    }
     public function getBinConfigDataByProductId($pro_grp_id,$product_id,$wh_id,$productVolumn,$actual_qty,$pack_level_type,$putaway_type)
    {
        $getTotBinQty='';
        $checkBinCap=0;
        $dataArray=array();
        $pro_grp='';    
        $actualQty_cnt_val1 = -$actual_qty;
        $productVolumn= floor($productVolumn);
        if($putaway_type == 'SR')
        {
            $rs = DB::Table('warehouse_config as wh_conf')
                ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                ->select('wh_loc_id','le_wh_id','wh_location','res_prod_grp_id as pro_grp','pref_prod_id as product_id','sort_order','wh_conf.bin_type_dim_id as bin_type_dim_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                ->where('le_wh_id',$wh_id)
                ->where('wh_location_types','120006')
                ->where('res_prod_grp_id','=',$pro_grp_id)
                ->Where('pref_prod_id','=',0)
                ->orWhere('pref_prod_id','=',$product_id)
                ->get()->all();
            $rs=json_decode(json_encode($rs),true);       
            $dataArray= $this->binCheckLoop($rs,$dataArray,$productVolumn,$actualQty_cnt_val1);
        }
        else 
        {
            $rs = $this->getReservedAvailability(109004,$wh_id,$pro_grp_id,$product_id);
            $rs=json_decode(json_encode($rs),true);   
            $dataArray= $this->binCheckLoop($rs,$dataArray,$productVolumn,$actualQty_cnt_val1);        
            if(empty($dataArray))
            {
                $rs = $this->getReservedAvailability(109005,$wh_id,$pro_grp_id,$product_id);
                $rs=json_decode(json_encode($rs),true); 
                $dataArray= $this->binCheckLoop($rs,$dataArray,$productVolumn,$actualQty_cnt_val1);       
            }
        }
        return $dataArray;
    }
    public function binCheckLoop($rs,$dataArray,$productVolumn,$actualQty_cnt_val1)
    {
        if(!empty($rs))
        {
             foreach ($rs as $rsValue) 
            {
                $binLBH = floor($rsValue['length']*$rsValue['breadth']*$rsValue['height']);
                $totBinCapacity = floor($binLBH/$productVolumn);
                $binWiseQty = $this->getBinQty($rsValue['wh_loc_id']);
                $totBinCapacity = (int)$totBinCapacity - (int)$binWiseQty['placed_qty']; 
               if($actualQty_cnt_val1 < 0 && $totBinCapacity >0)
               {
                    $dataArray[] = array('bin_code'=>$rsValue['wh_location'],'bin_id'=>$rsValue['wh_loc_id'],'bin_qty'=>$totBinCapacity);
               }
                $actualQty_cnt_val1= $actualQty_cnt_val1+$totBinCapacity;
               
            }
            if($actualQty_cnt_val1<0)
            {
                $dataArray=array();
            }
            return $dataArray;        
        }else
        {
            return $dataArray;
        }
    }
    public function getReservedAvailability($bin_type,$wh_id,$pro_grp_id,$product_id)
    {
         $rs = DB::Table('warehouse_config as wh_conf')
                ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                ->select('wh_loc_id','le_wh_id','wh_location','res_prod_grp_id as pro_grp','pref_prod_id as product_id','sort_order','wh_conf.bin_type_dim_id as bin_type_dim_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                ->where('le_wh_id',$wh_id)
                ->where('wh_location_types','120006')
                ->where('bin_dim.bin_type','=',$bin_type)
                ->where('res_prod_grp_id','=',$pro_grp_id)
                ->Where('pref_prod_id','=',$product_id)
                ->get()->all();
        return $rs;
    }
    //this is from while scanning product in wh, it returns EAN no respective pack type
    public function getProductInfoByEanData($ean_no)
    {
        $rs = DB::Table('product_pack_config as pro_pack')
            ->join('products as pro','pro_pack.product_id','=','pro.product_id')
            ->select('pro_pack.product_id','pro_pack.level AS pack_level','pro.product_title','no_of_eaches AS qty','pro.sku','is_cratable')
            ->where('pro_pack.pack_sku_code',$ean_no)
            ->get()->all();
       return json_decode(json_encode($rs),true);
    }
     public function putawayModel($data)
    {
        DB::enablequerylog();
        $grn_id= $data['grn_id'];
        $bin_id= $data['bin_id'];
        $wh_id= $data['wh_id'];
        $pro_id= $data['product_id'];
        $picker_id= $data['picker_id'];
        $qty=$data['qty'];
        $grn_qty = $data['grn_qty'];
        $grn_status=$data['grn_status'];
        $token = $data['token'];
        if((isset($data['grn_qty']) && $data['grn_qty']!='') && (isset($data['grn_id']) && $data['grn_id']!='') && (isset($data['bin_id']) && $data['bin_id']!='') &&(isset($data['wh_id']) && $data['wh_id']!='') && (isset($data['product_id']) && $data['product_id']!='')&&(isset($data['picker_id']) && $data['picker_id']!='')&&(isset($data['qty']) && $data['qty']!='') &&(isset($data['grn_status']) && $data['grn_status']!='') )
        {
//            log::info("Putaway Process is started.");
            $getGrnQty =  DB::table('putaway_allocation')
                        ->select(DB::raw('sum(placed_qty) as placed_qty'))
                        ->where('wh_id',$wh_id)
                        ->where('bin_id',$bin_id)
                        ->where('prod_id',$pro_id)
                        ->where('putaway_list_id',$grn_id)
                        ->where('pending_qty','!=',0)
                        ->first();
            $getGrnQty = json_decode(json_encode($getGrnQty),true);
            $PlacedGrnQty = (empty($getGrnQty['placed_qty']))?0:$getGrnQty['placed_qty'];
            if($grn_qty >= $PlacedGrnQty && $grn_qty >=$qty)
            {
                $rs1 =  DB::table('putaway_allocation')
                        ->where('wh_id',$wh_id)
                        ->where('bin_id',$bin_id)
                        ->where('prod_id',$pro_id)
                        ->where('putaway_list_id',$grn_id)
                        ->where('pending_qty','!=',0)
                        ->select('placed_qty','qty','pending_qty')
                        ->orderBy('created_at', 'desc')
                        ->first();
                $rs1 = json_decode(json_encode($rs1),true);
                if(!empty($rs1))
                {
                    $qty1= $qty+$rs1['placed_qty'];
                    $pending_qty =$rs1['pending_qty']-$qty;
                    $rs=  DB::table('putaway_allocation')
                            ->where('wh_id',$wh_id)
                            ->where('bin_id',$bin_id)
                            ->where('prod_id',$pro_id)
                            ->where('putaway_list_id',$grn_id)
                            ->where('pending_qty','!=',0)
                            ->update(array('placed_qty'=>$qty1,'pending_qty'=>$pending_qty,'is_active'=>0));
                    // log::info("updated putaway_allocation table with qty");
                    if($rs==1)
                    {
                        $this->updateBinInvTbl($wh_id,$bin_id,$pro_id,$qty,$grn_status,$grn_id,$picker_id,$token);
                        return json_encode(array('status'=>"Success",'message'=> 'Successfully Completed.','data'=>"")); 
                    }
                    else
                    {
                       // log::info("Failed data Mismatched in putaway Process.");
                        return json_encode(array('status'=>"failed",'message'=> 'Data Mismatched.','data'=>"")); 
                    }                    
                }else
                {
                    return json_encode(array('status'=>"failed",'message'=> 'Data Mismatched.','data'=>"")); 
                }
            }
            else
            {
               return json_encode(array('status'=>"failed",'message'=> "Placed Qty is more then GRN Qty.",'data'=>"")); 
            }
        }
        else
        {
            return json_encode(array('status'=>"failed",'message'=> 'Sorry some parameters are missed.','data'=>"")); 
        }
    }
    public function updateBinInvTbl($wh_id,$bin_id,$pro_id,$qty,$grn_status,$grn_id,$picker_id,$token)
    {
        DB::enablequerylog();
        $checkBin_inv = DB::table('bin_inventory')
                                ->where('wh_id',$wh_id)
                                ->where('bin_id',$bin_id)
                                ->where('product_id',$pro_id)
                                ->select('qty')
                                ->first();
        $checkBin_inv =  json_decode(json_encode($checkBin_inv),true);
        if(!empty($checkBin_inv))
        {
            $sum = $qty+$checkBin_inv['qty'];
            $rs1 = DB::Table('bin_inventory')
                        ->where('wh_id',$wh_id)
                        ->where('bin_id',$bin_id)
                        ->where('product_id',$pro_id)
                        ->update(array('qty'=>$sum));
            $getPutawayListStatus = DB::table('putaway_allocation')
                                    ->where('pending_qty','!=',0)
                                    ->where('putaway_list_id',$grn_id)
                                    ->select(DB::raw('sum(pending_qty) as qty'))
                                    ->first();
             $getPutawayListStatus =  json_decode(json_encode($getPutawayListStatus),true);
             //Log::info("updating putaway_list table -------------------------------");
            // echo $getPutawayListStatus['qty'].'_______';
            if(empty($getPutawayListStatus['qty']))
            {
                //log::info("Updating putaway list status update and ----------putaway all dones");
                $getGRNid =DB::table('putaway_list')
                            ->where('putaway_id',$grn_id)
                            ->select('putaway_source','source_id')
                            ->first();
                $getGRNid =  json_decode(json_encode($getGRNid),true);
                if(!empty($getGRNid))
                {
                    if($getGRNid['putaway_source']=="GRN")
                    {
                       // log::info("Calling poPutawayCompleted method. parameters are id =".$getGRNid['putaway_source']."----- Piceker Id-----".$picker_id);
                        $PurchaseOrderController = new PurchaseOrderController(1);
                        $PurchaseOrderController->poPutawayCompleted($getGRNid['source_id'],$picker_id);
                       // log::info("after calling poPutawayCompleted method.");
                    }else if($getGRNid['putaway_source']=="SR")
                    {
                       // log::info("Putaway Crates CURL process started.......");
                        $crateUpdateRs = $this->cratesUpdate($getGRNid['source_id'],$wh_id,$token);
                       // log::info("Putaway Crates CURL update responce...".$crateUpdateRs);
                    }
                }                
                $rs =  DB::table('putaway_list')
                        ->where('putaway_id',$grn_id)
                        ->update(array('putaway_status'=>'12804'));
               // log::info("Putaway status Updated Successfully with Completed status(12804)...");
            }
        }
    }
    public function getCratesList($data)
    {
        $container_type=$data['container_type'];
        $wh_id=$data['wh_id'];
        if((isset($container_type) && $container_type!='') && (isset($wh_id) && $wh_id!=''))
        {
            $rs = DB::Table('container_master')
                ->select('status',DB::raw('count(status) as status_count'))
                ->where('container_type','=',$container_type)
                ->whereIn('le_wh_id',array($wh_id))
                ->groupby('status')
                ->get()->all();
            $rs=json_decode(json_encode($rs),true);
            $arrayData='';
            foreach ($rs as $crateValues)
            {
                $arrayData[]=array("status"=>$crateValues['status'],"status_count"=>$crateValues['status_count']);
            }
            return json_encode(array('status'=>"success",'message'=> 'Crates List with statu','data'=>$arrayData)); 

        }else{
            return json_encode(array('status'=>"failed",'message'=> 'Wrong Data. Pass container type and warehouse id','data'=>"")); 
        }        
       
    }
    public function binCapacityByProdId($wh_id,$pid,$qty)
    {
        $binVolumn='';
        $status=0;
        $arrayData='';
        $actualQty_cnt_val1= -$qty;
        $productEachLevel = DB::table('product_pack_config')
                            ->join('products','products.product_id','=','product_pack_config.product_id')
                            ->select('product_title','product_group_id','length','breadth','height')
                            ->where('level','=',16001)
                            ->where('product_pack_config.product_id',$pid)
                            ->first();
        $productEachLevel = json_decode(json_encode($productEachLevel),true);
        $arrayData['config']=["product_title"=>$productEachLevel['product_title']];
        $product_volumn = $productEachLevel['length']*$productEachLevel['breadth']*$productEachLevel['height'];
        $product_grp_id = $productEachLevel['product_group_id'];
        $rs = DB::Table('warehouse_config as wh_conf')
                ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                ->select('wh_conf.wh_loc_id as bin_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                ->where('le_wh_id',$wh_id)
                ->where('wh_location_types','120006')
                ->where('res_prod_grp_id','=',$product_grp_id)
                ->Where('pref_prod_id','=','0')
                ->orWhere('pref_prod_id','=',$pid)
                ->get()->all();
        $rs = json_decode(json_encode($rs),true);
        if(!empty($rs))
        {
            foreach ($rs as $rsValue) 
            {
                $binLBH = floor($rsValue['length']*$rsValue['breadth']*$rsValue['height']);
                $totBinCapacity = floor($binLBH/$product_volumn);
                $binWiseQty = $this->getBinQty($rsValue['bin_id']);
                $totBinCapacity = (int)$totBinCapacity - (int)$binWiseQty['placed_qty']; 
               if($actualQty_cnt_val1 < 0)
               {
                    
               }
                $actualQty_cnt_val1= $actualQty_cnt_val1+$totBinCapacity;               
            }
        }
        if($actualQty_cnt_val1 <0)
        {
            return json_encode(array('status'=>"failed",'message'=> 'Configure bin.','data'=>$arrayData));
           
        }else
        {
             return json_encode(array('status'=>"success",'message'=> 'Check Product wise bin capacity','data'=>$arrayData));
        }
    }
    public function binCapacityByPutAwayListId($putListId,$wh_id,$pid,$qty,$packLevel,$putaway_type)
    {
        DB::enablequerylog();
        $binVolumn='';
        $status=0;
        $arrayData='';
        $actualQty_cnt_val1= -$qty;
        $dataArray = array();
        $productEachLevel = DB::table('product_pack_config')
                            ->join('products','products.product_id','=','product_pack_config.product_id')
                            ->select('product_title','product_group_id','length','breadth','height')
                            ->where('level','=',16001)
                            ->where('product_pack_config.product_id',$pid)
                            ->first();
        $productEachLevel = json_decode(json_encode($productEachLevel),true);
        $arrayData['config']=["product_title"=>$productEachLevel['product_title']];
        $product_volumn = $productEachLevel['length']*$productEachLevel['breadth']*$productEachLevel['height'];
        $product_grp_id = $productEachLevel['product_group_id'];
        if($putaway_type == 'GRN')
        {
            $reservedRs = DB::Table('warehouse_config as wh_conf')
                        ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                        ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_loc_id as bin_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                        ->where('le_wh_id',$wh_id)
                        ->where('wh_location_types','120006')
                        ->where('res_prod_grp_id','=',$product_grp_id)
                        ->where('bin_dim.bin_type','=','109004')
                        ->Where('pref_prod_id','=',$pid)
                        ->get()->all();
            $reservedRs = json_decode(json_encode($reservedRs),true);
            if(!empty($reservedRs))
            {

                $QtyCnt="";
                $totMainGRNQty = $qty;
                $reservedBinQty =$qty;
                $preRemQty='';
                $placedQtyFlag=0;
                $dataBinarray=array(); 
                foreach ($reservedRs as $rsValue) 
                {                    
                    $binLBH = floor($rsValue['length']*$rsValue['breadth']*$rsValue['height']);
                    $remGrnQty=$reservedBinQty;
                    $totBinCapacity = floor($binLBH/$product_volumn);  
                    $binWiseQty= $this->binReservedQty($rsValue['bin_id'],$pid);

                    $pleQty=$totBinCapacity-$binWiseQty; 
                   /* echo $reservedBinQty.'________'.$totBinCapacity.'__________'.$pleQty.'-------------<br>';*/
                    //chck bin is safficient to GRN qty or not
                    if($reservedBinQty <= $pleQty && $pleQty>0)
                    {
                      $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty,'grn_qty'=>$reservedBinQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                      $QtyCnt ="";
                      break;
                    }else if($pleQty >0 )
                    {

                       if($pleQty <= $reservedBinQty)
                       {
                            $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty,'grn_qty'=>$pleQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                            $reservedBinQty= $reservedBinQty-$pleQty;
                            $QtyCnt+= $pleQty;
                            $remGrnQty = $reservedBinQty;
                       }else
                       if($pleQty >=$reservedBinQty && $QtyCnt <= $mainQty)
                       {
                            $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty,'grn_qty'=>$reservedBinQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                            $reservedBinQty= $reservedBinQty;
                            $QtyCnt+= $reservedBinQty;
                            $remGrnQty = $reservedBinQty;
                       } 
                    }
                    $actualQty_cnt_val1= $actualQty_cnt_val1+$totBinCapacity; 
                }
                //if we are founding any reserved bin move to storage
                if($QtyCnt!=$reservedBinQty && $QtyCnt!='')
                {
                   $dataBinarray =array();
                   $storageRs = DB::Table('warehouse_config as wh_conf')
                        ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                        ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_loc_id as bin_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                        ->where('le_wh_id',$wh_id)
                        ->where('wh_location_types','120006')
                        ->where('res_prod_grp_id','=',$product_grp_id)
                        ->where('bin_dim.bin_type','=','109005')
                        ->Where('pref_prod_id','=',$pid)
                        ->get()->all();
                    $storageRs = json_decode(json_encode($storageRs),true);
                     if(!empty($storageRs))
                    {
                        $QtyCnt="";
                        $mainQty =$qty;
                        $preRemQty='';
                        $dataBinarray=array(); 
                        foreach ($storageRs as $rsValue) 
                        {                    
                            $binLBH = floor($rsValue['length']*$rsValue['breadth']*$rsValue['height']);
                            $remGrnQty=$qty;
                            $totBinCapacity = floor($binLBH/$product_volumn);  
                            $binWiseQty= $this->binReservedQty($rsValue['bin_id'],$pid);                          
                            $pleQty=$totBinCapacity-$binWiseQty; 
                            //chck bin is safficient to GRN qty or not
                            if($qty <= $pleQty && $pleQty>0)
                            {
                              $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty,'grn_qty'=>$qty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                              $QtyCnt ="";
                              break;
                            }else if($pleQty >0 )
                            {
                               if($pleQty <= $qty)
                               {
                                    $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty,'grn_qty'=>$pleQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                                    $qty= $qty-$pleQty;
                                    $QtyCnt+= $pleQty;
                                    $remGrnQty = $qty;
                               }else
                               if($pleQty >=$qty && $QtyCnt <= $mainQty)
                               {
                                    $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty,'grn_qty'=>$qty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                                    $qty= $qty;
                                    $QtyCnt+= $qty;
                                    $remGrnQty = $qty;
                               } 
                            }
                            $actualQty_cnt_val1= $actualQty_cnt_val1+$totBinCapacity; 
                        }
                        if($QtyCnt!=$qty && $QtyCnt!='')
                        {
                            $dataBinarray= array();
                        }
                    }
                }
            }
            if(empty($dataBinarray))
            {
                 $QtyCnt="";
                $reservedBinQty =$qty;
                $totMainGRNQty1 = $qty;
                $preRemQty='';
                $placedQtyFlag=0;
                $dataBinarray=array();
                $dataBinarray =array();
                   $storageRs = DB::Table('warehouse_config as wh_conf')
                        ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                        ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_loc_id as bin_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                        ->where('le_wh_id',$wh_id)
                        ->where('wh_location_types','120006')
                        ->where('res_prod_grp_id','=',$product_grp_id)
                        ->where('bin_dim.bin_type','=','109005')
                        ->Where('pref_prod_id','=',$pid)
                        ->get()->all();
                    $storageRs = json_decode(json_encode($storageRs),true);
                     if(!empty($storageRs))
                    {
                        $QtyCnt="";
                        $mainQty =$qty;
                        $preRemQty='';
                        $dataBinarray=array(); 
                        foreach ($storageRs as $rsValue) 
                        {                    
                            $binLBH = floor($rsValue['length']*$rsValue['breadth']*$rsValue['height']);
                            $remGrnQty=$qty;
                            $totBinCapacity = floor($binLBH/$product_volumn);  
                            $binWiseQty= $this->binReservedQty($rsValue['bin_id'],$pid);

                            $pleQty=$totBinCapacity-$binWiseQty; 
                            //chck bin is safficient to GRN qty or not
                            if($qty <= $pleQty && $pleQty>0)
                            {
                              $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty1,'grn_qty'=>$qty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                              $QtyCnt ="";
                              break;
                            }else if($pleQty >0 )
                            {
                               if($pleQty <= $qty)
                               {
                                    $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty1,'grn_qty'=>$pleQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                                    $qty= $qty-$pleQty;
                                    $QtyCnt+= $pleQty;
                                    $remGrnQty = $qty;
                               }else
                               if($pleQty >=$qty && $QtyCnt <= $mainQty)
                               {
                                    $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty1,'grn_qty'=>$qty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                                    $qty= $qty;
                                    $QtyCnt+= $qty;
                                    $remGrnQty = $qty;
                               } 
                            }
                            $actualQty_cnt_val1= $actualQty_cnt_val1+$totBinCapacity; 
                        }
                        if($QtyCnt!=$qty && $QtyCnt!='')
                        {
                            $dataBinarray= array();
                        }
                    }
            }            
        }else if($putaway_type == 'SR')
        {
            $reservedRs = DB::Table('warehouse_config as wh_conf')
                        ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                        ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_loc_id as bin_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                        ->where('le_wh_id',$wh_id)
                        ->where('wh_location_types','120006')
                        ->where('res_prod_grp_id','=',$product_grp_id)
                        ->where('bin_dim.bin_type','=','109003')
                        ->Where('pref_prod_id','=',$pid)
                        ->get()->all();
            $reservedRs = json_decode(json_encode($reservedRs),true);
            if(!empty($reservedRs))
            {

                $QtyCnt="";
                $reservedBinQty =$qty;
                $preRemQty='';
                $placedQtyFlag=0;
                $dataBinarray=array(); 
                foreach ($reservedRs as $rsValue) 
                {                    
                    $binLBH = floor($rsValue['length']*$rsValue['breadth']*$rsValue['height']);
                    $remGrnQty=$reservedBinQty;
                    $totBinCapacity = floor($binLBH/$product_volumn);  
                    $binWiseQty= $this->binReservedQty($rsValue['bin_id'],$pid);

                    $pleQty=$totBinCapacity-$binWiseQty; 
                   /* echo $reservedBinQty.'________'.$totBinCapacity.'__________'.$pleQty.'-------------<br>';*/
                    //chck bin is safficient to GRN qty or not
                    if($reservedBinQty <= $pleQty && $pleQty>0)
                    {
                      $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty1,'grn_qty'=>$reservedBinQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                      $QtyCnt ="";
                      break;
                    }else if($pleQty >0 )
                    {

                       if($pleQty <= $reservedBinQty)
                       {
                            $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty1,'grn_qty'=>$pleQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                            $reservedBinQty= $reservedBinQty-$pleQty;
                            $QtyCnt+= $pleQty;
                            $remGrnQty = $reservedBinQty;
                       }else
                       if($pleQty >=$reservedBinQty && $QtyCnt <= $mainQty)
                       {
                            $dataBinarray[] = array('bin_id'=>$rsValue['bin_id'],'wh_id'=>$rsValue['wh_id'],'bin_qty'=>$totBinCapacity,'product_id'=>$pid,'pack_type'=>$packLevel,'tot_grn_qty'=>$totMainGRNQty1,'grn_qty'=>$reservedBinQty,'placed_qty'=>$binWiseQty,'remaining_qty'=>$pleQty);
                            $reservedBinQty= $reservedBinQty;
                            $QtyCnt+= $reservedBinQty;
                            $remGrnQty = $reservedBinQty;
                       } 
                    }
                    $actualQty_cnt_val1= $actualQty_cnt_val1+$totBinCapacity; 
                }
                //if we are founding any reserved bin move to storage
                if($QtyCnt!=$reservedBinQty && $QtyCnt!='')
                {
                   $dataBinarray =array();
                }
            }
        }
        if($actualQty_cnt_val1 <0)
        {
            return json_encode(array('status'=>"failed",'message'=> 'Configure bin.','data'=>$dataBinarray));
           
        }else
        {
             return json_encode(array('status'=>"success",'message'=> 'Check Product wise bin capacity','data'=>$dataBinarray));
        }
    }

    public function binReservedQty($bin_id,$pid)
    {
       $rs=DB::select('select getBinProdQty("'.$bin_id.'","'.$pid.'") as qty');
       $binWiseQty = json_decode(json_encode($rs),true);
       return $binWiseQty[0]['qty'];
    }
    public function getCrateByCrateCode($data)
    {
        $crate_code=$data['crate_code'];
        if((isset($crate_code) && $crate_code!=''))
        {
            $rs = DB::Table('picker_container_mapping as picker')
                ->join('gds_orders as gds','picker.order_id','=','gds.gds_order_id')
                ->select('gds_order_id','order_code')
                ->where('container_barcode','=',$crate_code)
                ->first();
            $rs=json_decode(json_encode($rs),true);
            $arrayData='';           
            $arrayData[]=array("order_id"=>$rs['gds_order_id'],"order_code"=>$rs['order_code']);           
            return json_encode(array('status'=>"success",'message'=> 'Crate code Information','data'=>$arrayData));
        }else{
            return json_encode(array('status'=>"failed",'message'=> 'Enter Crate code.','data'=>"")); 
        }       
    }
    public function binReservation($wh_id,$bin_id,$putawayListId,$pack_type,$pro_id,$tot_qty,$tot_grn_qty,$bin_type)
    {
        if('109005' <= $bin_type)
        {
            $bin_type ='109005';
        }
        $checkReseredRs = DB::table('putaway_allocation')
                            ->where('wh_id',$wh_id)
                            ->where('bin_id',$bin_id)
                            ->where('putaway_list_id',$putawayListId)
                            ->where('is_active',1)
                            ->where('pack_level',$pack_type)
                            ->where('prod_id',$pro_id)
                            ->where('bin_type',$bin_type)
                            ->whereNull('picker_id')
                            ->select('qty')
                            ->first();
        $checkReseredRs = json_decode(json_encode($checkReseredRs),true);
        if(!empty($checkReseredRs))
        {  
           // Log::info("Putaway allocation updated one record. bin_id =".$bin_id);
            $rs1 = DB::table('putaway_allocation')
            ->where('wh_id',$wh_id)
            ->where('bin_id',$bin_id)
            ->where('putaway_list_id',$putawayListId)
            ->where('is_active',1)
            ->where('bin_type',$bin_type)
            ->where('pack_level',$pack_type) 
            ->update(array('qty'=>$tot_qty)); 
            return $rs1;
        }else{
               /* $rsss = DB::Table('putaway_allocation')
                ->where('wh_id',$wh_id)
                ->where('bin_id',$bin_id)
                ->where('putaway_list_id',$putawayListId)
                ->where('is_active',1)
                ->where('bin_type',$bin_type)
                ->where('pack_level',$pack_type)
                ->where('prod_id',$pro_id)
                ->delete();*/
            //    Log::info("Putaway allocation inserted one record. bin_id =".$bin_id);
                $rs1 = DB::Table('putaway_allocation')->insert([ 
                    'wh_id'=>$wh_id,                    
                    'bin_id'=>$bin_id,
                    'prod_id'=>$pro_id,
                    'pack_level'=>$pack_type,
                    'putaway_list_id'=>$putawayListId,
                    'qty'=>$tot_qty,
                    'pending_qty'=>$tot_qty,
                    'total_qty'=>$tot_grn_qty,
                    'bin_type'=>$bin_type,
                    'is_active'=>1]); 
                return $rs1;
            } 
        
        return 0;
    }
    public function binAllocation($putListId)
    {
        $productIdList = '';
        $reservedArray= array();
        $productTitleArray = array();
        $status_cnt=0;
        $getPutawayListId = DB::table('putaway_list')
                            ->select('putaway_source as type','source_id as id')
                            ->where('putaway_id',$putListId)
                            ->where('putaway_status','!=','12804')  
                            ->where('putaway_status','!=','12801')                            
                            ->first();
        $getPutawayListId = json_decode(json_encode($getPutawayListId),true);
        if(!empty($getPutawayListId))
        {
           $id = $getPutawayListId['id'];
           $type = $getPutawayListId['type'];
           if($type == 'GRN')
            {
                $productIdList = DB::table('inward as inw')
                                ->join('inward_products AS inw_pro','inw_pro.inward_id','=','inw.inward_id')
                                ->join('inward_product_details AS inw_det','inw_det.inward_prd_id','=','inw_pro.inward_prd_id')
                                ->select('inw.le_wh_id as wh_id','inw.inward_id','inw_det.product_id','inw_det.pack_level','inw_det.tot_rec_qty')
                                ->where('inw.inward_id',$id)
                                ->get()->all(); 
                $productIdList = json_decode(json_encode($productIdList),true);         
            }
            else if($type == 'SR')
            {
                $productIdList = DB::table('gds_return_grid as gds_grid')
                                ->join('gds_returns AS gds_ret','gds_ret.gds_order_id','=','gds_grid.gds_order_id')
                                ->join('gds_orders AS gds_ord','gds_ord.gds_order_id','=','gds_grid.gds_order_id')
                                ->select('gds_ret.product_id','gds_ret.qty as tot_rec_qty','gds_ord.le_wh_id as wh_id')
                                ->where('gds_grid.gds_order_id',$id)
                                ->get()->all(); 
                $productIdList = json_decode(json_encode($productIdList),true);       
            }
            if(!empty($productIdList))
            {
                foreach($productIdList as $productIdValue)
                {
                    $productTitleArray[]= $productIdValue['product_id'];
                    $pack_type = (isset($productIdValue['pack_level']))?$productIdValue['pack_level']:'16001';
                    $checkCap = $this->binCapacityByPutAwayListId($putListId,$productIdValue['wh_id'],$productIdValue['product_id'],$productIdValue['tot_rec_qty'],$pack_type,$type);
                    $checkCap = json_decode($checkCap,true);
                   
                    $reservedArray[] = $checkCap['data'];
                }
            }  
            if(!empty($reservedArray[0]))
            { 
                for($i=0; $i < sizeof($reservedArray); $i++)
                { 
                    foreach ($reservedArray[$i] as $resKey )
                    {
                        if(isset($resKey['wh_id']))
                        {
                            $binRs = $this->binReservation($resKey['wh_id'],$resKey['bin_id'],$putListId,$resKey['pack_type'],$resKey['product_id'],$resKey['grn_qty'],$resKey['tot_grn_qty'],$resKey['bin_type']);
                            if($binRs == 1)
                            {
                                $status_cnt = 1;
                            }
                        }
                    }
                }
                if($status_cnt == 1)
                {
                    $rs =  DB::table('putaway_list')
                            ->where('putaway_id',$putListId)
                            ->update(array('putaway_status'=>'12801'));        
                     return json_encode(array('status'=>"Success",'message'=> 'Bin Allocation Success.','data'=>"")); 
                }else if($status_cnt == 0)
                {
                     $getArray= DB::table('products')
                                ->whereIn('product_id',$productTitleArray)
                                ->select('product_title')
                                ->get()->all();
                     return json_encode(array('status'=>"failed",'message'=> 'Bin Allocation Failed.','data'=>$getArray)); 
                }
            }else
            {
                $getArray= DB::table('products')
                    ->whereIn('product_id',$productTitleArray)
                    ->select('product_title')
                    ->get()->all();
                return json_encode(array('status'=>"failed",'message'=> 'Bin Allocation Failed.','data'=>$getArray)); 
            }
        } else{
            return json_encode(array('status'=>"failed",'message'=> 'Bin Allocation Failed.','data'=>"")); 
        }       
    } 
    //while scanmnig product we will show which bin it have allocated
    public function productBinLocation($ean_no,$wh_id)
    {
        $binArray=array();
        if($ean_no!='' && $wh_id!='')
        {
            $rs = DB::table('warehouse_config as wh_config')
                ->join('product_pack_config as pro_pack','pro_pack.product_id','=','wh_config.pref_prod_id')
                ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_config.bin_type_dim_id')
                ->join('master_lookup as mst','mst.value','=','bin_dim.bin_type')
                ->join('products','products.product_id','=','wh_config.pref_prod_id')
                ->leftjoin('inventory as inv',function($join)
                 {
                     $join->on('inv.product_id','=','wh_config.pref_prod_id');
                     $join->on('inv.le_wh_id','=','wh_config.le_wh_id');
                 })
                ->leftjoin('bin_inventory as put_away','put_away.bin_id','=','wh_config.wh_loc_id')
                ->select('sku','wh_loc_id AS bin_id','wh_config.parent_loc_id','wh_location AS bin_code','pref_prod_id AS product_id','products.product_title','mrp','mst.master_lookup_name AS bin_type',DB::raw('SUM(qty) AS bin_qty'),DB::raw('if(inv.soh is not null,inv.soh,0 ) as soh'))
                ->where('pro_pack.pack_sku_code',$ean_no)
                ->where('wh_config.le_wh_id',$wh_id)
                ->groupby('wh_loc_id')
                ->get()->all();         
            $rs = json_decode(json_encode($rs),true);
            if(!empty($rs))
            {
                foreach ($rs as $rsValue)
                {
                    $this->listArray='';
                    $getRootName = $this->getAisleName($rsValue['parent_loc_id'],$wh_id);
                    $getRootName = rtrim($getRootName,'-->');
                    $getRootName = $getRootName.'-->'.$rsValue['bin_code'];
                    $binArray[] = array("product_name"=>$rsValue['product_title'],"sku"=>$rsValue["sku"],"mrp"=>$rsValue['mrp'],"bin_id"=>$rsValue['bin_id'],"bin_location_path"=>$getRootName,"bin_code"=>$rsValue['bin_code'],"bin_qty"=>$rsValue['bin_qty'],"SOH_eaches"=>$rsValue['soh'],"bin_type"=>$rsValue['bin_type']);
                }
                if(!empty($binArray))
                {
                    return json_encode(Array('status' => 'success', 'message' =>'Bin Details.', 'data' => $binArray));
                }else
                {
                     return json_encode(Array('status' => 'failed', 'message' =>'Failed to find bin datails.', 'data' => $binArray));
                }               
            }else
            {
                return json_encode(Array('status' => 'failed', 'message' =>'Failed to find bin datails.', 'data' => $binArray));
            }             
        }else
        {
           return json_encode(Array('status' => 'failed', 'message' =>'Bin Details.', 'data' => $binArray)); 
        }
    }
    public function getAisleName($aisle_id,$wh_id)
    {
        $rs = DB::table('warehouse_config')
            ->where('le_wh_id',$wh_id)
            ->where('wh_loc_id',$aisle_id)
            ->select('wh_location','parent_loc_id')
            ->first();
        $rs= json_decode(json_encode($rs),true);
        if(!empty($rs))
        {
            $this->listArray= $rs['wh_location'].'-->'.$this->listArray;           
            if($rs['parent_loc_id'] > 0)
            $this->getAisleName($rs['parent_loc_id'],$wh_id);
        }
        return $this->listArray;
    }
    public function binAllocationTrigger()
    {
        $rsDataArray= array();
        $getPutawayListIds = DB::table('putaway_list')
                            ->where('putaway_status','=','12803')  
                            ->select('putaway_id','putaway_source as type')
                            ->get()->all();
        $getPutawayListIds = json_decode(json_encode($getPutawayListIds),true);
        if(!empty($getPutawayListIds))
        {
            foreach ($getPutawayListIds as $listValue)
            {
                 $this->putawayBinAllocation($listValue['putaway_id']);
            }
        }
        return "triggering hold status";
    }
    public function putawayBinAllocation($putListId)
    {
        //parent function from returnmodel putawaylist
        DB::enablequerylog();
      // Log::info("Calling putaway bin allocation method");
        $putawayListArray ="";
        $productIdList = '';
        $binPutawayList=array();
        $productTitleArray = array();
        $status_cnt=0;
        $getPutawayListId = DB::table('putaway_list')
                            ->select('putaway_source as type','source_id as id')
                            ->where('putaway_id',$putListId)
                            ->where('putaway_status','!=','12804')  
                            ->where('putaway_status','!=','12801')
                            ->first();
        $getPutawayListId = json_decode(json_encode($getPutawayListId),true);
        if(!empty($getPutawayListId))
        {
           $id = $getPutawayListId['id'];
           $type = $getPutawayListId['type'];
           if($type == 'GRN')
            {
                $productIdList = DB::table('inward as inw')
                                ->join('inward_products AS inw_pro','inw_pro.inward_id','=','inw.inward_id')
                                ->join('inward_product_details AS inw_det','inw_det.inward_prd_id','=','inw_pro.inward_prd_id')
                                ->select('inw.le_wh_id as wh_id','inw.inward_id','inw_det.product_id','inw_det.pack_level','inw_det.tot_rec_qty','inw_det.pack_qty as pack_qty','inw_det.received_qty as tot_pack_qty')
                                ->where('inw.inward_id',$id)
                                ->get()->all(); 
                $productIdList = json_decode(json_encode($productIdList),true);         
            }
            else if($type == 'SR')
            {
                $productIdList = DB::table('gds_return_grid as gds_grid')
                                ->join('gds_returns AS gds_ret','gds_ret.gds_order_id','=','gds_grid.gds_order_id')
                                ->join('gds_orders AS gds_ord','gds_ord.gds_order_id','=','gds_grid.gds_order_id')
                                ->select('gds_ret.product_id','gds_ret.qty as tot_rec_qty','gds_ord.le_wh_id as wh_id')
                                ->where('gds_grid.return_grid_id',$id)
                                ->get()->all(); 
                $productIdList = json_decode(json_encode($productIdList),true);   
            }
            if(!empty($productIdList))
            {
                
                foreach ($productIdList as $putwayListValue) 
                {
                    $product_id= $putwayListValue['product_id'];
                    $grn_eaches_qty= $putwayListValue['tot_rec_qty'];
                    $wh_id= $putwayListValue['wh_id'];
                    $pack_level= (isset($putwayListValue['pack_level']))?$putwayListValue['pack_level']:'16001';
                    $pack_wise_qty =(isset($putwayListValue['pack_qty']))?$putwayListValue['pack_qty']:'0';
                    $pack_wise_tot_qty =(isset($putwayListValue['tot_pack_qty']))?$putwayListValue['tot_pack_qty']:'0';
                    if($type == 'SR')
                    {
                        $binPutawayList =$this->salesReturnBinLocation($id,$product_id,$wh_id,$grn_eaches_qty,109003);

                    }else
                    {
                        $remaining_grn_qty=$this->getProductWiseBinLocation($id,$type,$product_id,$grn_eaches_qty,$wh_id,$pack_level,$pack_wise_qty,$pack_wise_tot_qty,109003,0);       
                    }
                }
                if(empty($this->productBinConfigArray))
                {
                    if(empty($this->nonLocationArray))
                    {
                        try
                        {
                            DB::beginTransaction();
      //                      Log::info("DB transaction begin for putaway allocation");
                            foreach($this->locationArray as $locationValue)
                            {
                                $binRs= $this->binReservation($locationValue['wh_id'],$locationValue['bin_id'],$putListId,$locationValue['pack_type'],$locationValue['product_id'],$locationValue['qty'],$locationValue['tot_grn_qty'],$locationValue['bin_type']);
                                    if($binRs == 1)
                                    {
                                        $status_cnt = 1;
                                    }
                            }
                            if($status_cnt == 1)
                            {
                                $rs =  DB::table('putaway_list')
                                        ->where('putaway_id',$putListId)
                                        ->update(array('putaway_status'=>'12801'));    
                                DB::commit();       
                                 return json_encode(array('status'=>"Success",'message'=> 'Bin Allocation Success.','data'=>"")); 
                            }else
                            {
                                DB::rollback();
      //                          Log::info("putaway allocation method with db roll back");
                            }
                        }
                        catch (\ErrorException $ex) 
                        {
         //                   Log::info("Putaway allocation process error with ");
                            Log::error($ex->getMessage());
                            Log::error($ex->getTraceAsString());
                        }
                    }else 
                    {
                        $getArray= DB::table('products')
                                    ->whereIn('product_id',$this->nonLocationArray)
                                    ->select('product_title')
                                    ->get()->all();
                        return json_encode(array('status'=>"failed",'message'=> 'This product dont have bins, grn status hold.','data'=>$getArray));
                    } 
                }else{
                     $getArray= DB::table('products')
                                    ->whereIn('product_id',$this->productBinConfigArray)
                                    ->select('product_title')
                                    ->get()->all();
                        if($type == 'SR')
                        {
                            return json_encode(array('status'=>"failed",'message'=> 'This product dont have min and max capacity (or) dont have sufficient max capacity (or) bins are not Configure. ','data'=>$getArray));
                        }else
                        {
                         return json_encode(array('status'=>"failed",'message'=> 'This product dont have min and max capacity (or) Storage bins are not Configure (or) Replenishment happens. ','data'=>$getArray));
                        }
                }               
            }
            else
            {
                return json_encode(array('status'=>"failed",'message'=> 'No products available. ','data'=>""));
            }
        }else
        {
            return json_encode(array('status'=>"failed",'message'=> 'This is putaway list already created. ','data'=>""));
        }
    }
    public function getProductWiseBinLocation($id,$type,$product_id,$grn_eaches_qty,$wh_id,$pack_level,$pack_wise_qty,$pack_wise_tot_qty,$bin_dim_type,$emptyStorageBin)
    {

        $tot_grn_qty=$grn_eaches_qty;
        //$this->locationArray='';
        //$locationArray=array();
        $bin_type_cnt =0;
        $remaining_grn_qty=$grn_eaches_qty;
        $getBins = DB::Table('warehouse_config as wh_conf')
                    ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                    ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_location as bin_code','wh_conf.wh_loc_id as bin_id','wh_conf.res_prod_grp_id','wh_conf.pref_prod_id','wh_conf.bin_type_dim_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height','bin_category')
                    ->where('le_wh_id',$wh_id)
                    ->where('wh_location_types','120006')
                    ->where('bin_dim.bin_type','=',$bin_dim_type)
                    ->where('bin_category','!=','')
                    ->whereIN('wh_conf.pref_prod_id',array($product_id))
                    ->whereNotIn('wh_conf.wh_loc_id',array_unique($this->binArray))
                    ->get()->all();
       if($bin_dim_type!=109003 && empty($getBins))
       {
            $getBins = DB::Table('warehouse_config as wh_conf')
                        ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                        ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_location as bin_code','wh_conf.wh_loc_id as bin_id','wh_conf.res_prod_grp_id','wh_conf.pref_prod_id','wh_conf.bin_type_dim_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height','bin_category')
                        ->where('le_wh_id',$wh_id)
                        ->where('wh_location_types','120006')
                        ->where('bin_dim.bin_type','=',$bin_dim_type)
                        ->where('wh_conf.pref_prod_id','=',0)
                        ->where('bin_category','!=','')
                        ->whereNotIn('wh_conf.wh_loc_id',array_unique($this->binArray))
                        ->get()->all();
            $bin_type_cnt =1;
       }
        $loop_cnt=0;
        $chcekBinArray = array_filter($getBins);
        $getBins = json_decode(json_encode($getBins),true); 
        if(!empty($chcekBinArray) && $grn_eaches_qty >0 && $remaining_grn_qty >0)
        {  
           
            $cnt = 0;
            foreach ($getBins as $key=>$bintypeValue)
            {
                $cnt++;
                $productBinCatType = $this->productBinCategoryType($product_id);
                if(!in_array($bintypeValue['bin_id'],array_unique($this->binArray)) && $productBinCatType == $bintypeValue['bin_category'] && $remaining_grn_qty >0)
                {

                    $checkMinMax = $this->checkReservedProductMinMax($product_id,$wh_id,$bintypeValue['bin_type_dim_id']);
                    if(empty($checkMinMax) && $bin_dim_type <= 109006 && $bin_dim_type!=109003)
                    {  

                        $sss= $this->dynamicBins($bintypeValue['bin_id'],$bintypeValue['bin_type_dim_id'],$wh_id,$product_id);
                    }else if(empty($bintypeValue['pref_prod_id']) && $remaining_grn_qty >0)
                    {

                        $productGrpid = $this->getProductGroupID($product_id);
                        $this->addProductToBin($bintypeValue['bin_id'],$productGrpid,$product_id);
                        $remaining_grn_qty= $this->getProductWiseBinLocation($id,$type,$product_id,$remaining_grn_qty,$wh_id,$pack_level,$pack_wise_qty,$pack_wise_tot_qty,$bin_dim_type,1);
                    }
                    $productMinMaxInfo = $this->productMinMaxValues($wh_id,$bintypeValue['res_prod_grp_id'],$bintypeValue['bin_type_dim_id']);
                    $binCurrentQty =$this->binReservedQty($bintypeValue['bin_id'],$product_id);
                    $replenishmentStatus = $this->binReplenishmentStatus($bintypeValue['bin_id']);
                    if(!empty($productMinMaxInfo) && empty($replenishmentStatus))
                    {
                        $eachesCount = $this->getProductPackEaches($product_id,$productMinMaxInfo['pack_conf_id']);
                      //min qty * eaches , min qty may be cfc or eaches
                        $eachesQty = $eachesCount['no_of_eaches']*$productMinMaxInfo['min_qty'];
                        if($binCurrentQty != 0)
                        {
                            $productCapacityRange = $productMinMaxInfo['max_qty']-$productMinMaxInfo['min_qty']; 
                        }else
                        {
                            $productCapacityRange = $productMinMaxInfo['max_qty'];
                        }

                        // i am multipling range qty * eaches bzc range my be CFC or inner
                        $productCapacityRange = $productCapacityRange*$eachesCount['no_of_eaches'];

                        if($bin_dim_type == 109005 || $bin_dim_type ==109006)
                        {
                            $eachesQty = $eachesCount['no_of_eaches']*$productMinMaxInfo['max_qty'];
                            $storageMaxQty = $productMinMaxInfo['max_qty']*$eachesCount['no_of_eaches'];
                            $productCapacityRange = $storageMaxQty-$binCurrentQty;
                           
                        }

                        if($binCurrentQty <= $eachesQty )
                        {
                            /* echo '<br>'.$grn_eaches_qty."==========".$remaining_grn_qty.'......'.$productCapacityRange."========negative values======<br>";*/
                           if($remaining_grn_qty <= $productCapacityRange && $remaining_grn_qty>0)
                           {

                                if(!in_array($bintypeValue['bin_id'], $this->binArray))
                                {
                                    $grn_eaches_qty = $grn_eaches_qty;
                                    
                                    $this->locationArray[] =array('wh_id'=>$bintypeValue['wh_id'],'product_id'=>$product_id,'bin_code'=>$bintypeValue['bin_code'],'bin_id'=>$bintypeValue['bin_id'],'pack_type'=>$pack_level,'qty'=>$remaining_grn_qty,'tot_grn_qty'=>$grn_eaches_qty,'bin_type'=>$bin_dim_type);
                                    $this->binArray[]= $bintypeValue['bin_id'];
                                    $loop_cnt = 1;
                                    $remaining_grn_qty = 0;
                                    $grn_eaches_qty =0;
                                    return $remaining_grn_qty;
                                }            
                           }
                           else if($remaining_grn_qty>0 && $remaining_grn_qty >=$productCapacityRange )
                           {
                                $remaining_grn_qty =  $remaining_grn_qty-$productCapacityRange;
                                if($remaining_grn_qty > 0)
                                {
                                        $this->locationArray[] =array('wh_id'=>$bintypeValue['wh_id'],'product_id'=>$product_id,'bin_code'=>$bintypeValue['bin_code'],'bin_id'=>$bintypeValue['bin_id'],'pack_type'=>$pack_level,'qty'=>$productCapacityRange,'tot_grn_qty'=>$remaining_grn_qty,'bin_type'=>$bin_dim_type);
                                        $this->binArray[]= $bintypeValue['bin_id'];
                                        $loop_cnt = 1;
                                } 
                           }                                  
                        }
                    }
                }
                if($remaining_grn_qty === 0)
                {
                    return $remaining_grn_qty;
                    //break;
                }
            }

            if($remaining_grn_qty > 0)
            {   
                if($loop_cnt >0 && $bin_dim_type==109004)
                {
                    $bin_dim_type =109004;
                }else if($loop_cnt >0 && $bin_dim_type==109005)
                {
                    $bin_dim_type =109005;
                }else
                {
                    $bin_dim_type= $bin_dim_type+1;
                }
                //if in this level unable to find bin goto next level
                if($bin_dim_type <= 109006)
                {
                    $remaining_grn_qty= $this->getProductWiseBinLocation($id,$type,$product_id,$remaining_grn_qty,$wh_id,$pack_level,$pack_wise_qty,$pack_wise_tot_qty,$bin_dim_type,1);

                }else
                {
                    $this->productBinConfigArray[]=array('product_id'=>$product_id);
                }
            }
          
        }else 
        {
            $bin_dim_type= $bin_dim_type+1;
            if($bin_dim_type <= 109005 )
            {     
                $remaining_grn_qty= $this->getProductWiseBinLocation($id,$type,$product_id,$remaining_grn_qty,$wh_id,$pack_level,$pack_wise_qty,$pack_wise_tot_qty,$bin_dim_type,0);
              
            }else if($bin_dim_type == 109006)
            {
                $remaining_grn_qty= $this->getProductWiseBinLocation($id,$type,$product_id,$remaining_grn_qty,$wh_id,$pack_level,$pack_wise_qty,$pack_wise_tot_qty,$bin_dim_type,1);
            }else
            {
                $this->productBinConfigArray[]=array('product_id'=>$product_id);
            }
        }
    }
    public function productMinMaxValues($wh_id,$productGrpId,$bintype)
    {
        $getProductPackInfo = DB::table('product_bin_config')
                                ->where('wh_id',$wh_id)
                                ->where('prod_group_id',$productGrpId)
                                ->where('bin_type_dim_id',$bintype)
                                ->select('pack_conf_id','min_qty','max_qty')
                                ->first();
        $getProductPackInfo = json_decode(json_encode($getProductPackInfo),true);
        return $getProductPackInfo;
    }
    public function checkProductPackConfig($pid)
    {
         $getProductPackInfo = DB::table('product_pack_config')
                                ->where('product_id',$pid)
                                ->select('no_of_eaches')
                                ->first();
        $getProductPackInfo = json_decode(json_encode($getProductPackInfo),true);
        return $getProductPackInfo;
    }
    public function getProductPackEaches($pid,$pack_type)
    {
         $getProductPackInfo = DB::table('product_pack_config')
                                ->where('product_id',$pid)
                                ->where('level',$pack_type)
                                ->select('no_of_eaches')
                                ->first();
        $getProductPackInfo = json_decode(json_encode($getProductPackInfo),true);
        return $getProductPackInfo;
    }
    public function getBinTypeName($id)
    {
        $sql = DB::table('bin_type_dimensions')
                ->where('bin_type_dim_id',$id)
                ->select('bin_type')
                ->first();
        $sql = json_decode(json_encode($sql),true);
        $bin_type_name=DB::select('select getMastLookupValue("'.$sql['bin_type'].'") as qty');
       $bin_type_name = json_decode(json_encode($bin_type_name),true);
       return $bin_type_name[0]['qty'];
    }
    public function salesReturnGrid($assignReturns)
    {
        $salesSql = DB::table('putaway_list')
                    ->join('gds_return_grid','gds_return_grid.return_grid_id','=','putaway_list.source_id')
                    ->join('gds_orders','gds_orders.gds_order_id','=','gds_return_grid.gds_order_id')
                    ->select('putaway_id','gds_orders.order_code as order_code','return_order_code','source_id','gds_orders.gds_order_id','putaway_by')
                    ->where('putaway_list.putaway_status','=','12801')
                    ->where('putaway_list.putaway_source','=',"SR")
                    ->get()->all();
        $salesSql = json_decode(json_encode($salesSql),true);
        $UnassignArray= array();
        $assignArray = array();
        if(!empty($salesSql))
        {
            $bin_type="Pick Face";
            foreach ($salesSql as $salesSqlvalue)
            {
                $crateArray = $this->getCrateCode($salesSqlvalue['gds_order_id']);
                if($assignReturns['assign']==0)
                {
                    if($salesSqlvalue['putaway_by']=='')
                    {
                        $UnassignArray[]= array('putaway_id'=>$salesSqlvalue['putaway_id'],'return_code'=>$salesSqlvalue['return_order_code'],'order_code'=>$salesSqlvalue['order_code'],"bin_type"=>$bin_type,"bin_type_id"=>109003,'crate_code'=>$crateArray);
                    }
                   
                }else if($assignReturns['assign']==1){
                     if($salesSqlvalue['putaway_by']>0)
                    {
                        $pickerSql=DB::select('select GetUserName("'.$salesSqlvalue['putaway_by'].'","2") as picker');
                        $pickerSql = json_decode(json_encode($pickerSql),true);
                        $assignArray[]= array('putaway_id'=>$salesSqlvalue['putaway_id'],'return_code'=>$salesSqlvalue['return_order_code'],'picker_id'=>$pickerSql[0]['picker'],'order_code'=>$salesSqlvalue['order_code'],"bin_type"=>$bin_type,"bin_type_id"=>109003,'crate_code'=>$crateArray);
                    }
                } 
            }
            if($assignReturns['assign']==0)
            {
                return json_encode(array('status'=>"success",'message'=> "Return data",'data'=>$UnassignArray));  
            }else if($assignReturns['assign']==1)
            {
                return json_encode(array('status'=>"success",'message'=> "Return data",'data'=>$assignArray));  
            }
        }
        else
        {
            return json_encode(array('status'=>"failed",'message'=> "Return not available",'data'=>""));  
        }
    }
    public function assignReturns($return_ids,$picker_id,$status,$bin_type)
    {
        DB::beginTransaction();
        $status=0;
        foreach($return_ids as $idValues)
        {
            $checkPutawayList = DB::table('putaway_list')
                                ->where('putaway_id','=',$idValues)
                                ->where('putaway_source','=',"GRN")
                                ->pluck('source_id')->all();
            if(!empty($checkPutawayList))
            {
                $inwd =DB::table('inward')
                                    ->where('inward_id','=',$checkPutawayList[0])
                                    ->update(array('picker_id'=>$picker_id)); 
            }
            $updatePutawaySql=DB::table('putaway_list')
                ->where('putaway_id','=',$idValues)
                ->update(array('putaway_by'=>$picker_id));

           // Log::info("update putaway_list table----------------------------");
            //Log::info(print_r($updatePutawaySql,true));
            $sql = DB::table('putaway_allocation')
                        ->where('putaway_list_id','=',$idValues)
                        ->where('bin_type','=',$bin_type)
                        ->update(array('picker_id'=>$picker_id));
           // Log::info("update putaway_allocation table-------------------bin type---------".$bin_type);
           // Log::info(print_r($sql,true));
                $updateQuery = DB::getquerylog();
              //  Log::info(print_r($updateQuery,true));
            $status =1;
        }
        if($status == 1)
        {
            DB::commit();
            return json_encode(array('status'=>"success",'message'=> "Successfully Assigned.",'data'=>""));  
        }
        else
        {
            DB::rollback();
            return json_encode(array('status'=>"failed",'message'=> "Please assign properly.",'data'=>""));  
        }
            
    }
    public function salesReturnBinLocation($id,$product_id,$wh_id,$grn_eaches_qty,$bin_dim_type)
    {
        try
        {
            $getBins = DB::Table('warehouse_config as wh_conf')
                    ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh_conf.bin_type_dim_id')
                    ->select('wh_conf.le_wh_id as wh_id','wh_conf.wh_location as bin_code','wh_conf.wh_loc_id as bin_id','wh_conf.res_prod_grp_id','wh_conf.pref_prod_id','wh_conf.bin_type_dim_id','bin_dim.length AS length','bin_dim.breadth AS breadth','bin_dim.heigth AS height')
                    ->where('le_wh_id',$wh_id)
                    ->where('wh_location_types','120006')
                    ->where('bin_dim.bin_type','=',$bin_dim_type)
                    ->Where('pref_prod_id','=',$product_id)
                    ->whereNotIn('wh_conf.wh_loc_id',array_unique($this->binArray))
                    ->get()->all();
            $getBins = json_decode(json_encode($getBins),true);
            $tot_grn_qty= $grn_eaches_qty;
            $remaining_qty=0;
            $qtyTot=0;
            if(!empty($getBins))
            {           
                foreach ($getBins as $bintypeValue)
                {
                 //   echo $bintypeValue['res_prod_grp_id'].'_________';
                    $productMinMaxInfo = $this->productMinMaxValues($wh_id,$bintypeValue['res_prod_grp_id'],$bintypeValue['bin_type_dim_id']);
                    $binCurrentQty =$this->binReservedQty($bintypeValue['bin_id'],$product_id);
                    if($qtyTot == $tot_grn_qty)
                    {
                       break;
                    }
                    $eachesCount = $this->getProductPackEaches($product_id,$productMinMaxInfo['pack_conf_id']);
                   // print_r($productMinMaxInfo);
                    if(!empty($productMinMaxInfo))
                    {
                      //min qty * eaches , min qty may be cfc or eaches
                        $eachesCount = $this->getProductPackEaches($product_id,$productMinMaxInfo['pack_conf_id']);
                          //min qty * eaches , min qty may be cfc or eaches
                        $eachesQty = $eachesCount['no_of_eaches']*$productMinMaxInfo['max_qty'];

                        $binRemainingQty = $eachesQty-$binCurrentQty; 
                      /*  echo '<br>'.$grn_eaches_qty.'____'.$binRemainingQty.'___'.$grn_eaches_qty.'_______'.$eachesQty.'__bin cur qty---'.$binCurrentQty;*/
                        if($grn_eaches_qty <= $binRemainingQty && $grn_eaches_qty >0)
                        {
                            $this->locationArray[] =array('wh_id'=>$bintypeValue['wh_id'],'product_id'=>$product_id,'bin_code'=>$bintypeValue['bin_code'],'bin_id'=>$bintypeValue['bin_id'],'pack_type'=>"eaches",'qty'=>$grn_eaches_qty,'tot_grn_qty'=>$tot_grn_qty,'bin_type'=>'109003');
                                
                                $this->binArray[]= $bintypeValue['bin_id'];
                                $qtyTot+= $grn_eaches_qty;
                        }else 
                        {  
                           if($grn_eaches_qty <= $binRemainingQty)
                           {
                                $remaining_qty = $grn_eaches_qty;
                           }
                           else if($binRemainingQty<0)
                           {
                                $binRemainingQtyReturn = $binCurrentQty-$eachesQty; 
                                if($binRemainingQtyReturn <= $grn_eaches_qty);
                                {
                                    $grn_eaches_qty = $grn_eaches_qty;
                                    $remaining_qty=0;
                                    $this->locationArray[] =array('wh_id'=>$bintypeValue['wh_id'],'product_id'=>$product_id,'bin_code'=>$bintypeValue['bin_code'],'bin_id'=>$bintypeValue['bin_id'],'pack_type'=>"eaches",'qty'=>$remaining_qty,'tot_grn_qty'=>$tot_grn_qty,'bin_type'=>'109003');
                                 
                                        $this->binArray[]= $bintypeValue['bin_id'];
                                        $qtyTot+= $remaining_qty;
                                        break;
                                }
                           }
                            else 
                           {
                                $grn_eaches_qty = ($grn_eaches_qty-$binRemainingQty); 
                                $remaining_qty= $binRemainingQty;
                           }    
                            if($grn_eaches_qty >0 )
                            {
                                $this->locationArray[] =array('wh_id'=>$bintypeValue['wh_id'],'product_id'=>$product_id,'bin_code'=>$bintypeValue['bin_code'],'bin_id'=>$bintypeValue['bin_id'],'pack_type'=>"eaches",'qty'=>$remaining_qty,'tot_grn_qty'=>$tot_grn_qty,'bin_type'=>'109003');
                                 
                                $this->binArray[]= $bintypeValue['bin_id'];
                                $qtyTot+= $remaining_qty;
                            }
                        }   
                    }else
                    {
                        $this->productBinConfigArray[]= array('product_id'=>$product_id);
                    }

                }
                if($qtyTot != $tot_grn_qty && $qtyTot >0)
                {
                    $this->locationArray=array();
                    $this->productBinConfigArray[]= array('product_id'=>$product_id);
                }
            }else
            {

                $this->nonLocationArray[]=array('product_id'=>$product_id);
            }
        }catch (ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        
    }
    public function getAllPutawayLists($data)
    {
        DB::enablequerylog();
        $offset = (isset($data['offset']) && $data['offset'] != '') ? $data['offset'] : 0;
        $perpage = (isset($data['perpage']) && $data['perpage'] != '') ? $data['perpage'] : 10;
        $picker_id = (isset($data['picker_id']) && $data['picker_id']!='')? $data['picker_id']:0;
        $putawayList_array =array();
        if($picker_id!=0)
        {
            $grnReturnRs = $this->getGRNList("assigned",$picker_id,$offset,$perpage);

            $listArray =array();
            foreach($grnReturnRs as $temp)
            {
                $putawayList_array[] = $temp;
            }
            $salesReturn1 = DB::table('putaway_list')
                            ->join('putaway_allocation','putaway_allocation.putaway_list_id','=','putaway_list.putaway_id')
                            ->join('gds_return_grid','gds_return_grid.return_grid_id','=','putaway_list.source_id')
                            ->join('gds_orders','gds_orders.gds_order_id','=','gds_return_grid.gds_order_id')
                            ->select('putaway_id','gds_orders.order_code as order_code','source_id','return_order_code as putaway_code',DB::raw("'SR Created' as putaway_status"),DB::raw("'Pick Face' as bin_type"),DB::raw("'109003' as bin_type_id"))
                            ->where('putaway_list.putaway_source','=','SR')
                            ->where('putaway_allocation.picker_id','=',$picker_id)
                            ->where('putaway_status','=','12801')
                            ->groupby('putaway_list_id')
                            ->get()->all();
            $salesReturn1 = json_decode(json_encode($salesReturn1), true);
            if(!empty($salesReturn1))
            {
                
                $i=0;
               
                foreach ($salesReturn1 as $salesKey)
                {
                    $getOrder_id = DB::table('gds_return_grid')
                            ->where('return_grid_id','=',$salesKey
                        ['source_id'])
                            ->pluck('gds_order_id')->all();
                    $order_id =$getOrder_id[0];
                    $salesReturn1[$i]['crate'] = $this->getCrateCode($order_id);
                    $i++;
                }
            }

            $putawayList_array= array_merge($putawayList_array,$salesReturn1);
            if (!empty($putawayList_array))
            {
                //$putawayList_array = call_user_func_array('array_merge', $putawayList_array);
                return json_encode(Array(
                                'status' => "success",
                                'message' => "Putaway Lists",
                                'data' => $putawayList_array
                            ));
            }else 
            {
                return json_encode(Array(
                                'status' => "success",
                                'message' => "No data",
                                'data' => []
                            ));
            }
        }else
        {
            return json_encode(Array(
                                'status' => "success",
                                'message' => "No data",
                                'data' => []
                            ));
        }
    }
    public function getSRList($putaway_id,$offset,$perpage)
    {
        $sr_status="SR Created.";
        $query = DB::table('putaway_list');
        $query->join('gds_return_grid','gds_return_grid.return_grid_id','=','putaway_list.source_id');
        $query->join('gds_orders','gds_orders.gds_order_id','=','gds_return_grid.gds_order_id');
        $query->select('putaway_id','gds_orders.order_code as order_code','return_order_code as putaway_code',DB::raw("'SR Created' as putaway_status"));
        $query->where(['putaway_list.putaway_id' => $putaway_id]);
        $query->orderBy('gds_return_grid.created_at', 'DESC');
        $query->skip($offset * $perpage)->take($perpage);
        $result=$query->get()->all();
        return json_decode(json_encode($result), true);
    }
    public function getGRNList($type,$picker_id='',$offset,$perpage) 
    {
        DB::enablequerylog();
        $query = DB::table('putaway_list');
        $query->join('putaway_allocation as putaway', 'putaway.putaway_list_id', '=', 'putaway_list.putaway_id');
        $query->join('inward', 'inward.inward_id','=','putaway_list.source_id');
        $query->join('po','po.po_id','=','inward.po_no');
        $query->select('po.po_id', 'po.po_code','inward.inward_id','inward.inward_code','putaway_list.putaway_id',
                DB::raw('GetUserName(putaway.picker_id,2) as picker_name'),
                DB::raw('getMastLookupValue(inward.inward_status) as inward_status'),
                DB::raw('getMastLookupValue(putaway.bin_type) as bin_type'),"putaway.bin_type as bin_type_id"
            );
        $query->where(['putaway_list.putaway_source' => 'GRN', 'putaway_list.putaway_status' => 12801]);
        if($type=='assigned'){
                $query->where('putaway.picker_id','!=',0);
        }else if($type=='unassigned'){
            $query->where('putaway.picker_id',0);
        }
        if($picker_id!=''){
            $query->where('putaway.picker_id',$picker_id);
        }
        $query->where('inward.inward_status',76001);
        $query->where('pending_qty','!=',0);
        $query->groupBy('inward.inward_id','putaway.bin_type');
        $query->orderBy('inward.created_at', 'DESC');
        $query->skip($offset * $perpage)->take($perpage);
        $result=$query->get()->all();
        return json_decode(json_encode($result), true);
    }
    public function binToBinTransfer($data)
    {
        DB::enablequerylog();
        if(isset($data['source_bin']) && !empty($data['source_bin']) && isset($data['destination_bin']) && !empty($data['destination_bin'] ) && isset($data['flag']) && isset($data['qty']) )
        {
            $source_bin_code= $data['source_bin'];
            $des_bin_code = $data['destination_bin'];
            $wh_id = $data['wh_id'];
            //check bin have putaway product or not
            $source_code_sql = DB::table('putaway_allocation as putaway')
                                ->join('putaway_list','putaway_list.putaway_id','=','putaway.putaway_list_id')
                                ->select('bin_id')
                                ->where('putaway_status','=','12801')
                                ->where('bin_id','=',$source_bin_code)
                                ->where('pending_qty','!=',0)
                                ->get()->all();
            if(empty($source_code_sql))
            {
                $destination_code_sql = DB::table('putaway_allocation as putaway')
                                        ->join('putaway_list','putaway_list.putaway_id','=','putaway.putaway_list_id')
                                        ->select('bin_id')
                                        ->where('putaway_status','=','12801')
                                        ->where('bin_id','=',$des_bin_code)
                                        ->where('pending_qty','!=',0)
                                        ->get()->all();
                if(empty($destination_code_sql))
                {
                    $getSourceBinDetails = $this->getBinDetails($wh_id,$source_bin_code);
                    $getDesBinDetails = $this->getBinDetails($wh_id,$des_bin_code);
                    if(!empty($getSourceBinDetails) && !empty($getDesBinDetails)  && $getSourceBinDetails[0]['bin_type'] == $getDesBinDetails[0]['bin_type'])
                    {
                        
                        if($data['flag']==0)
                        {
                            if($getDesBinDetails[0]['pref_prod_id'] ==  $getSourceBinDetails[0]['pref_prod_id'])
                            {
                                $getMinMax = $this->getProductMinMax($wh_id,$getDesBinDetails[0]['res_prod_grp_id'],$getDesBinDetails[0]['bin_type_dim_id']);
                                if(!empty($getMinMax))
                                {
                                    return $this->oneWayBinTransferred($wh_id,$getDesBinDetails[0]['qty'],$getSourceBinDetails[0]['qty'],$data['qty'],$des_bin_code,$source_bin_code,$getSourceBinDetails[0]['pref_prod_id'],$getDesBinDetails[0]['pref_prod_id'],$getMinMax['max_qty']);
                                }
                                else
                                {
                                    if(isset($data['min_qty'])  && $data['min_qty']!=0 &&   isset($data['max_qty']) && $data['max_qty']!=0 && isset($data['pack_type']) && $data['pack_type']!=0)
                                    {
                                        $this->insertMinMaxQty($wh_id,$getDesBinDetails[0]['res_prod_grp_id'],$getDesBinDetails[0]['bin_type_dim_id'],$data['min_qty'],$data['max_qty'],$data['pack_type']);
                                        return $this->oneWayBinTransferred($wh_id,$getDesBinDetails[0]['qty'],$getSourceBinDetails[0]['qty'],$data['qty'],$des_bin_code,$source_bin_code,$getSourceBinDetails[0]['pref_prod_id'],$getDesBinDetails[0]['pref_prod_id'],$data['max_qty']);
                                    }else
                                    {
                                        return  json_encode(array('status'=>"min_max",'message'=> 'Please send destination bin min, max qty and pack type.','data'=>"")); 
                                    }
                                }
                            }
                            else
                            {
                                return json_encode(array('status'=>"failed",'message'=> 'Please Transfer with same products.','data'=>""));
                            }
                        }else if($data['flag']==1)
                        {
                            $getSourceBinMinMax = $this->getProductMinMax($wh_id,$getSourceBinDetails[0]['res_prod_grp_id'],$getSourceBinDetails[0]['bin_type_dim_id']);
                            $getDesBinMinMax = $this->getProductMinMax($wh_id,$getDesBinDetails[0]['res_prod_grp_id'],$getDesBinDetails[0]['bin_type_dim_id']);
                            if($getSourceBinMinMax['pack_conf_id'] !='16001')
                            {
                                $sourceBinEaches = $this->getProductPackEaches($getSourceBinDetails[0]['pref_prod_id'],'16001');
                                $sourceBinEaches =$sourceBinEaches['no_of_eaches']*$getSourceBinMinMax['max_qty'];
                            }else
                            {
                                $sourceBinEaches = $getSourceBinMinMax['max_qty'];
                            }
                            if($getDesBinMinMax['pack_conf_id'] !='16001')
                            {
                                $desBinEaches = $this->getProductPackEaches($getDesBinDetails[0]['pref_prod_id'],'16001');
                                $desBinEaches =$desBinEaches['no_of_eaches']*$getDesBinMinMax['max_qty'];
                            }else
                            {
                                $desBinEaches = $getDesBinMinMax['max_qty'];
                            }
                            if(!empty($getSourceBinDetails[0]['qty']) && !empty($desBinEaches) && !empty($getDesBinDetails[0]['qty']) && !empty($sourceBinEaches))
                            {
                                //source bin to destination bin
                                $this->binInterChanges($des_bin_code,$getSourceBinDetails[0]['qty'],$getSourceBinDetails[0]['res_prod_grp_id'],$getSourceBinDetails[0]['pref_prod_id'],$wh_id);

                                $this->updateMinMaxQty($getDesBinMinMax['prod_bin_conf_id'],$getSourceBinDetails[0]['res_prod_grp_id'],$getSourceBinDetails[0]['bin_type_dim_id'],$getSourceBinMinMax['min_qty'],$getSourceBinMinMax['max_qty']);


                                //destination bin to source bin tranf
                                $this->binInterChanges($source_bin_code,$getDesBinDetails[0]['qty'],$getDesBinDetails[0]['res_prod_grp_id'],$getDesBinDetails[0]['pref_prod_id'],$wh_id);
                                $this->updateMinMaxQty($getSourceBinMinMax['prod_bin_conf_id'],$getDesBinDetails[0]['res_prod_grp_id'],$getDesBinDetails[0]['bin_type_dim_id'],$getDesBinMinMax['min_qty'],$getDesBinMinMax['max_qty']);
                                //update replanishment status as completed. 
                                $this->updateReplanishmentStatus($wh_id,$source_bin_code);
                                $this->updateReplanishmentStatus($wh_id,$des_bin_code);
                                return json_encode(array('status'=>"success",'message'=> 'Successfully Transferred.','data'=>"")); 

                            }else
                            {
                                return json_encode(array('status'=>"failed",'message'=> 'Bins min and max not Configure.', 'data'=>""));
                            }
                        }
                        
                    }else
                    {
                        return json_encode(array('status'=>"failed",'message'=> 'Please Transferred Same Bin Types (or) check bins configurations.','data'=>"")); 
                    }
                    
                }else
                {
                    return json_encode(array('status'=>"failed",'message'=> 'sorry destination bin have in putaway process.','data'=>"")); 
                }
            }else
            {
                return json_encode(array('status'=>"failed",'message'=> 'Sorry, Source bin is going to process the putaway.','data'=>""));  
            }
        }
        else
        {
           return json_encode(Array(
                                'status' => "failed",
                                'message' => "Invalid Data",
                                'data' => ""
                            ));
        }
    }
    public function getProductIdByBinCode($bin_code)
    {
        $rs = DB::table('warehouse_config')
                ->where('wh_loc_id',$bin_code)
                ->pluck('pref_prod_id')->all();
        return $rs[0];
    }
    public function getBinDimType($bin_type)
    {
        $rs = DB::table('warehouse_config')
                ->where('wh_loc_id',$bin_type)
                ->pluck('bin_type_dim_id')->all();
        return $rs[0];
    }
    public function showMinMaxQty($data)
    {
        DB::enablequerylog();
        if( isset($data['source_pro_id']) && !empty($data['source_pro_id']) && 
            isset($data['des_pro_id']) && !empty($data['des_pro_id']) && 
            isset($data['source_bin_code']) && !empty($data['source_bin_code'])&&
            isset($data['des_bin_code']) && !empty($data['des_bin_code']))
        {
            $source_pro_id =$data['source_pro_id'];
            $des_pro_id = $data['des_pro_id'];
            $source_bin_dim_type = $this->getBinDimType($data['source_bin_code']);
            $des_bin_dim_type =$this->getBinDimType($data['des_bin_code']);
            $sourceProDesBin = $this->checkProToBinDimType($source_pro_id,$des_bin_dim_type);
            $desProSourceBin = $this->checkProToBinDimType($des_pro_id,$source_bin_dim_type);
          
            $rsData = array('source_proid_des_bin'=>$sourceProDesBin,'des_proid_source_bin'=>$desProSourceBin);

           return json_encode(Array(
                                'status' => "success",
                                'message' => "Source and Destination bin min and max qty.",
                                'data' => $rsData
                            ));
        }else
        {
            return json_encode(Array(
                                'status' => "failed",
                                'message' => "Invalid Data",
                                'data' => ""
                            ));
        }
    }
    public function checkBinMinMax($proGroid,$bin_dim_type,$wh_id)
    {
        $rs= DB::table('warehouse_config')
            ->join('product_bin_config','product_bin_config.bin_type_dim_id','=','warehouse_config.bin_type_dim_id')
            ->where('prod_group_id',$proGroid)
            ->where('pack_conf_id','=','16001')
            ->where('product_bin_config.bin_type_dim_id','=',$bin_dim_type)
            ->pluck('prod_bin_conf_id')->all();
            if(empty($rs))
            {
                $rs[0]=array();
            }
        return $rs[0];
    }
    public function checkProToBinDimType($pid,$dimType)
    {
        $rs = DB::Table('warehouse_config')
            ->join('product_bin_config',function($join)
            {
                $join->on('product_bin_config.bin_type_dim_id','=','warehouse_config.bin_type_dim_id');
                $join->on('warehouse_config.res_prod_grp_id','=','product_bin_config.prod_group_id');
            })
            ->select('min_qty','max_qty')
            ->where('pref_prod_id',$pid)
            ->where('pack_conf_id','=','16001')
            ->where('warehouse_config.bin_type_dim_id',$dimType)
            ->first(); 
      /*  $rrr=DB::getquerylog();
        print_r(end($rrr)); */
        $rs= json_decode(json_encode($rs),true);
        return $rs;
    }
    public function updateMinMaxQty($prod_bin_id,$proGroId,$bin_dim_type,$min_qty,$max_qty)
    {
        $updateSql = DB::Table('product_bin_config')
                    ->where('prod_bin_conf_id',$prod_bin_id)
                    ->update(array('prod_group_id'=>$proGroId,"bin_type_dim_id"=>$bin_dim_type,"min_qty"=>$min_qty,"max_qty"=>$max_qty));
        return $updateSql;
    }
    public function insertMinMaxQty($wh_id,$group_id,$bin_type,$min_qty,$max_qty,$level)
    {
         $insertSql = DB::table('product_bin_config')
                    ->insert(["wh_id"=>$wh_id,"prod_group_id"=>$group_id,"bin_type_dim_id"=>$bin_type,"pack_conf_id"=>$level,"min_qty"=>$min_qty,"max_qty"=>$max_qty]);
        return $insertSql;
    }
    public function BinQty($bin_id)
    {
        $rs = DB::table('bin_inventory')
                ->where('bin_id',$bin_id)
                ->pluck('qty')->all();
        return $rs[0];
    }
    public function binInterChanges($bin_id,$bin_qty,$pro_grp_id,$pid,$wh_id)
    {
        $updateWarehouse_configTbl = DB::table('warehouse_config')
                                    ->where('wh_loc_id',$bin_id)
                                    ->where('le_wh_id',$wh_id)
                                    ->update(array("res_prod_grp_id"=>$pro_grp_id,
                                        "pref_prod_id"=>$pid));
        $bin_inventory_tbl = DB::table("bin_inventory")
                            ->where('wh_id',$wh_id)
                            ->where('bin_id',$bin_id)
                            ->update(array("product_id"=>$pid,"qty"=>$bin_qty));
        
    }
    
    public function oneToOneBinTransfer($wh_id,$sourceBinQty,$source_bin_code,$destinationBinCurrentQty,$des_bin_code,$min_qty,$max_qty,$sourcePid,$minMaxPackType)
    {
        //convert to pack type to eaches....
        $eachesCount = $this->getProductPackEaches($sourcePid,$minMaxPackType);
        $bin_max_qty = $max_qty-$min_qty;
        $bin_max_qty = $eachesCount['no_of_eaches']*$bin_max_qty;
        $des_bin_remaining_qty = $bin_max_qty-$destinationBinCurrentQty;
        if($des_bin_remaining_qty >0 && $sourceBinQty <= $des_bin_remaining_qty)
        {
           $tranferSql = DB::table('bin_inventory')
                        ->where('bin_id',$des_bin_code)
                        ->where('wh_id',$wh_id)
                        ->update(array('qty'=>$destinationBinCurrentQty+$sourceBinQty));
            $unReservedSourceBin = DB::table('bin_inventory')
                                    ->where('bin_id',$source_bin_code)
                                    ->where('wh_id',$wh_id)
                                    ->update(array('qty'=>0,'product_id'=>0));
            $unReservedSourceBinWh_config = DB::table('warehouse_config')
                                    ->where('wh_loc_id',$source_bin_code)
                                    ->where('le_wh_id',$wh_id)
                                    ->update(array('res_prod_grp_id'=>0,'pref_prod_id'=>0));
            return json_encode(array('status'=>"success",'message'=> 'Successfully Transferred.','data'=>""));
        }else
        {
            return json_encode(array('status'=>"failed",'message'=> '"Sorry source bin qty is not sufficient to destination 
            bin qty.','data'=>""));
        }
    }
    public function removeExistedProBinConfig($bin_type_dim_id,$wh_id,$prodGroupId)
    {
       $rs= DB::table('product_bin_config')
            ->where('bin_type_dim_id',$bin_type_dim_id)
            ->where("wh_id",$wh_id)
            ->where('prod_group_id',$prodGroupId)
            ->delete();
        return $rs;
    }
    public function getBinDetails($wh_id,$bin_code)
    {
        $getSourceBinDetails = DB::table('warehouse_config')
                            ->join('bin_inventory','warehouse_config.wh_loc_id','=','bin_inventory.bin_id')
                            ->join('bin_type_dimensions','bin_type_dimensions.bin_type_dim_id','=','warehouse_config.bin_type_dim_id')
                            ->where('wh_loc_id',$bin_code)
                            ->where('le_wh_id',$wh_id)
                            ->select('res_prod_grp_id','pref_prod_id','product_id','warehouse_config.bin_type_dim_id','qty','bin_type')
                            ->get()->all();
        $getSourceBinDetails = json_decode(json_encode($getSourceBinDetails),true);
        return $getSourceBinDetails;
    }
    public function getProductMinMax($wh_id,$proGid,$bin_dim_type)
    {
        $rs = DB::table('product_bin_config')
                ->where('prod_group_id',$proGid)
                ->where('wh_id',$wh_id)
                ->where('bin_type_dim_id',$bin_dim_type)
                ->select('min_qty','max_qty','pack_conf_id','prod_bin_conf_id')
                ->first();
        $rs = json_decode(json_encode($rs),true);
        return $rs;
    }
    public function updateOneToOneBinTransffered($wh_id,$bin_code,$pid,$qty)
    {
        $updateDesBin = DB::table('bin_inventory')
                    ->where('bin_id',$bin_code)
                    ->where('wh_id',$wh_id)
                    ->update(array(
                        'product_id'=>$pid,
                        'qty'=>$qty
                        ));
    }
   
    public function onoWayBinTransffered($pid,$grpId,$bin_dim_type,$des_bin_code,$source_bin_code,$wh_id,$qty)
    {
        $getSourceBinMinMax = $this->getProductMinMax($wh_id,$grpId,$bin_dim_type);
        if(!empty($getSourceBinMinMax))
        {
            $this->removeExistedProBinConfig($bin_dim_type,$wh_id,$grpId);
            $this->insertMinMaxQty($wh_id,$grpId,$bin_dim_type,$getSourceBinMinMax['min_qty'],$getSourceBinMinMax['max_qty'],$getSourceBinMinMax['pack_conf_id']);
            $this->updateOneToOneBinTransffered($wh_id,$des_bin_code,$source_bin_code,$pid,$grpId,$qty);
            return json_encode(array('status'=>"success",'message'=> 'Successfully Transferred.','data'=>"")); 
        }else{
             return json_encode(array('status'=>"failed",'message'=> 'Source bin do not have min and max qty.','data'=>"")); 
        } 
    }
    public function updateReplanishmentStatus($wh_id,$bin_id)
    {
        $rs= DB::table('replenishment_products')
            ->where('wh_id',$wh_id)
            ->where('bin_id',$bin_id)
            ->update(array(
                        'status'=>'Complete'
                        ));
        return $rs;
    }

    public function dynamicBins($bin_id,$bin_dim_type,$wh_id,$pid)
    {
        $checkEmptyBin = DB::table('warehouse_config as wh')
                        ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','wh.bin_type_dim_id')
                        ->join('product_bin_config as bin_con',function($joinQuery)
                        {
                            $joinQuery->on('bin_con.bin_type_dim_id','=','bin_dim.bin_type_dim_id');
                            $joinQuery->on('wh.res_prod_grp_id','=','bin_con.prod_group_id');
                            $joinQuery->on('wh.le_wh_id','=','bin_con.wh_id');
                        })
                        ->where('wh.bin_type_dim_id',$bin_dim_type)
                        ->wherein('bin_dim.bin_type',["109004","109005"])
                        ->where('wh_loc_id',$bin_id)
                        ->first();
        $checkEmptyBin = json_decode(json_encode($checkEmptyBin),true);
        if(!empty($checkEmptyBin))
        {
            if(!empty($checkEmptyBin['res_prod_grp_id']))
            {
                //check this product have min and max, apply to same prduct def bin.
                $productMinMaxInfo = $this->productMinMaxValues($wh_id,$checkEmptyBin['res_prod_grp_id'],$bin_dim_type);
                if(empty($productMinMaxInfo))
                {
                    $productVolumn = $this->productVolumn($pid);
                    $productVolumnByCFC = $this->getProductWiseCFC($pid);
                    if($productVolumnByCFC)
                    {
                        $bin_volumn = $this->getBinDimByBinId($bin_dim_type);
                        $bin_volumn = ($bin_volumn>0)?$bin_volumn:1;
                        $productVolumn = ($productVolumn>0)?$productVolumn:1;

                        $binTotalMinMaxCFCWise= floor($bin_volumn/$productVolumn);
                        $totProductVolumnByCFC = floor($binTotalMinMaxCFCWise/$productVolumnByCFC);

                        $productGrpid = $this->getProductGroupID($pid);
                        // add automatically cal bin volumn by cfc

                        $binVolum = DB::table('product_bin_config')
                                    ->insert(
                                        array("prod_group_id"=>$checkEmptyBin['res_prod_grp_id'],"wh_id"=>$wh_id,"bin_type_dim_id"=>$bin_dim_type,"pack_conf_id"=>"16004","min_qty"=>1,"max_qty"=>$totProductVolumnByCFC));
                    }
                }
            }else
            {
                    $productVolumn = $this->productVolumn($pid);
                    $productVolumnByCFC = $this->getProductWiseCFC($pid);
                    if(!empty($productVolumnByCFC))
                    {
                        $bin_volumn = $this->getBinDimByBinId($bin_dim_type);
                        $bin_volumn = ($bin_volumn>0)?$bin_volumn:1;
                        $productVolumn = ($productVolumn>0)?$productVolumn:1;
                        
                        $binTotalMinMaxCFCWise= floor($bin_volumn/$productVolumn);
                        $totProductVolumnByCFC = floor($binTotalMinMaxCFCWise/$productVolumnByCFC);

                        $productGrpid = $this->getProductGroupID($pid);
                        //add product to warehouse config table 
                        $binPro = $this->addProductToBin($bin_id,$productGrpid,$pid);
                
                        // add automatically cal bin volumn by cfc

                        $binVolum = DB::table('product_bin_config')
                                    ->insert(
                                        array("prod_group_id"=>$productGrpid,"wh_id"=>$wh_id,"bin_type_dim_id"=>$bin_dim_type,"pack_conf_id"=>"16004","min_qty"=>1,"max_qty"=>$totProductVolumnByCFC));
                    }
            }
        }else
        {
            $productVolumn = $this->productVolumn($pid);
            $productVolumnByCFC = $this->getProductWiseCFC($pid);
            if(!empty($productVolumnByCFC))
            {
                $bin_volumn = $this->getBinDimByBinId($bin_dim_type);
                $bin_volumn = ($bin_volumn>0)?$bin_volumn:1;
                $productVolumn = ($productVolumn>0)?$productVolumn:1;
                
                $binTotalMinMaxCFCWise= floor($bin_volumn/$productVolumn);
                $totProductVolumnByCFC = floor($binTotalMinMaxCFCWise/$productVolumnByCFC);

                $productGrpid = $this->getProductGroupID($pid);

                //add product to warehouse config table 
                $binPro = $this->addProductToBin($bin_id,$productGrpid,$pid);
                // add automatically cal bin volumn by cfc
                $binVolum = DB::table('product_bin_config')
                            ->insert(
                                array("prod_group_id"=>$productGrpid,"wh_id"=>$wh_id,"bin_type_dim_id"=>$bin_dim_type,"pack_conf_id"=>"16004","min_qty"=>1,"max_qty"=>$totProductVolumnByCFC));
            }
            
        }
    }
    public function productVolumn($pid)
    {
         $productEachLevel = DB::table('product_pack_config')
                            ->join('products','products.product_id','=','product_pack_config.product_id')
                            ->select('product_title','product_group_id','length','breadth','height')
                            ->where('level','=',16001)
                            ->where('product_pack_config.product_id',$pid)
                            ->first();
        $productEachLevel = json_decode(json_encode($productEachLevel),true);
        $arrayData['config']=["product_title"=>$productEachLevel['product_title']];
        $product_volumn = $productEachLevel['length']*$productEachLevel['breadth']*$productEachLevel['height'];
        return $product_volumn;
    }
    public function getProductWiseCFC($pid)
    {
        $query = DB::table('product_pack_config')
                ->where("product_id",$pid)
                ->where('level','=','16004')
                ->pluck('no_of_eaches')->all();
        if(!empty($query))
        {
            return $query[0];
        }else
        {
            return 1;
        }
    }
    public function getProductGroupID($pid)
    {
        $productGrpid = DB::table('products')
                        ->where('product_id',$pid)
                        ->pluck('product_group_id')->all();
        return $productGrpid[0];
    }
    public function addProductToBin($bin_id,$groupId,$pid)
    {
        $queryRs =  DB::table("warehouse_config")
                    ->where("wh_loc_id",$bin_id)
                    ->update(array("res_prod_grp_id"=>$groupId,"pref_prod_id"=>$pid));
        if($queryRs)
        {
            $rs1 = DB::Table('bin_inventory')
                ->where('bin_id',$bin_id)
                ->update(array('product_id'=>$pid));
            return $rs1;
        }
    }
    public function checkReservedProductMinMax($pid,$wh_id,$dim_type)
    {
        $productGrpid = $this->getProductGroupID($pid);
        $checkMinMax = DB::table('product_bin_config')
                        ->where('prod_group_id',$productGrpid)
                        ->where('wh_id',$wh_id)
                        ->where("bin_type_dim_id",'=',$dim_type)
                        ->first();
        return $checkMinMax;
    }
    public function binReplenishmentStatus($bin_id)
    {
        $statusRs =  DB::table('replenishment_products')
                    ->where('bin_id',$bin_id)
                    ->whereIn('status',array("Open","Assigned"))
                    ->first();
        return $statusRs;
    }
    public function getCrateCode($order_id)
    {
        $crateRs = DB::table("picker_container_mapping")
                    ->where("order_id",'=',$order_id)
                    //->select("container_barcode")
                    ->groupby('container_barcode')
                    ->pluck('container_barcode')->all();
        $crateRs = json_decode(json_encode($crateRs),true);
        return $crateRs;
    }
    public function getGRNPackTypeQtyByPutawayId($inward_id)
    {
        $packRs = DB::table('inward_products')
                ->join('inward_product_details as inw_pro','inw_pro.inward_prd_id','=','inward_products.inward_prd_id')
                ->select("pack_level as pack_level_id","inw_pro.received_qty as pack_level_qty",DB::raw('getMastLookupValue(pack_level) as pack_level'))
                ->where('inward_products.inward_id','=',$inward_id)
                ->get()->all();
        $packRs = json_decode(json_encode($packRs),true);
        return $packRs;
    }
    public function productBinCategoryType($pid)
    {
        $rs = DB::table('product_characteristics')
             ->where("product_id","=",$pid)
             ->pluck("bin_category_type")->all();
        $rs = json_decode(json_encode($rs), true);
        return $rs[0];
    }
    public function cratesUpdate($order_id,$wh_id,$token)
    {
        try 
        {
            $getOrder_id = DB::table('gds_return_grid')
                            ->where('return_grid_id','=',$order_id)
                            ->pluck('gds_order_id')->all();
            $order_id = $getOrder_id[0];
           // log::info(print_r($getOrder_id, true));
            //log::info("Enter into crate update method with.....".$order_id."---------adn ------".$wh_id);
            $crateRs = DB::table('picker_container_mapping')
                        ->select("container_barcode")
                        ->where("order_id",'=',$order_id)
                        ->get()->all();
            $crateRs = json_decode(json_encode($crateRs),true);
            $newArr = array();
           // log::info("crates array .....");
            //Log::info(print_r($crateRs, true));
            foreach($crateRs as $each)
            {
                $crateInfoArr["crate_code"] = $each['container_barcode'];
                $crateInfoArr["status"] = 136001;
                $crateInfoArr["transaction_status"] = 137001;
                $newArr[] = $crateInfoArr;
            }

            $queryData = array("lp_token" => $token, "le_wh_id" => $wh_id, "crate_info" => $newArr);
            $queryDataEncode["cratestatuslist_params"] = json_encode($queryData);
          // log::info("crates user token .....".$token);
            $curlCall = curl_init();
            curl_setopt($curlCall, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlCall, CURLOPT_URL, $_SERVER['HTTP_HOST']."/cratemanagement/setcratestatus");
            // curl_setopt($curlCall, CURLOPT_URL, "http://dev.ebutor.com/cratemanagement/setcratestatus");
            curl_setopt($curlCall, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curlCall, CURLOPT_POSTFIELDS, $queryDataEncode);
            $output = curl_exec($curlCall);
            $info = curl_getinfo($curlCall);
            $error = curl_error($curlCall);
            curl_close($curlCall);
            return $output;
        }
        catch (Exception $ex)
        {
           Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
        }
    }
    public function oneWayBinTransferred($wh_id,$desBinQty,$sourceBinQty,$qty,$des_bin_code,$source_bin_code,$sourcePid,$desPid,$max_qty)
    {
        $DesBinEaches = $this->getProductPackEaches($desBinQty,'16001');
        $no_of_eaches = (!empty($DesBinEaches['no_of_eaches']))?$DesBinEaches['no_of_eaches']:1;
        $desBinTotalEaches = $max_qty*$no_of_eaches;
        $restOfBinQty = $desBinTotalEaches-$desBinQty;
        if($restOfBinQty >= $qty)
        {
            $desTotQty = $desBinQty+$qty;
            $sourceTotQty = $sourceBinQty-$qty;

            $this->updateOneToOneBinTransffered($wh_id,$des_bin_code,$desPid,$desTotQty);

            $this->updateOneToOneBinTransffered($wh_id,$source_bin_code,$sourcePid,$sourceTotQty);


            $this->updateReplanishmentStatus($wh_id,$source_bin_code);

            $this->updateReplanishmentStatus($wh_id,$des_bin_code);
            
            return json_encode(array('status'=>"success",'message'=> 'Successfully Transferred.','data'=>""));
            
        }else
        {
            return  json_encode(array('status'=>"failed",'message'=> 'Please enter sufficient qty (or) check bins max quantity.','data'=>""));
        }        
    }
}