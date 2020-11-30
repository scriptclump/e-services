<?php

namespace App\Modules\WarehouseConfig\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Log;
use App\Modules\Roles\Models\Role;

Class WarehouseConfig extends Model
{

        protected $table = "warehouse_config";
    protected $primaryKey = 'wh_loc_id';
    public $binLocationLoop=array(); 
    public function getWarehouseConfig()
    {
        $legalentityid = Session::get('legal_entity_id');
        $userId = Session::get('userId');
        $role=new Role();
        $dcList = $role->getWarehouseData($userId, 6);
        $dc=json_decode($dcList,true);
        $dc=implode($dc,',');
        $dc=explode(',',$dc);
        $parentWhs = DB::table('warehouse_config')
                ->leftJoin('bin_type_dimensions','bin_type_dimensions.bin_type_dim_id','=','warehouse_config.bin_type_dim_id')
                ->leftjoin('legalentity_warehouses as le','le.le_wh_id','=','warehouse_config.le_wh_id')
                ->select('wh_loc_id','warehouse_config.le_wh_id','wh_location_types','wh_location',
                DB::raw('getMastLookupValue(wh_location_types) as wh_location_types'),'parent_loc_id','warehouse_config.sort_order',
                DB::raw('getProductGrpName(res_prod_grp_id) as res_prod_grp_id'),DB::raw('getProductName(pref_prod_id) as pref_prod_id'),
                'bin_type_dimensions.length as length','bin_type_dimensions.breadth as breadth','bin_type_dimensions.heigth as height',
                DB::raw('getMastLookupValue(bin_type) as bin_type'))
                ->where('parent_loc_id','0')
                //->where('le.legal_entity_id',$legalentityid) 
                ->whereIn('le.le_wh_id',$dc)
                ->get()->all();
       
        //$locationTypes = $this->locationTypes();
        foreach($parentWhs as $Wh)
        {
            //$level =0;
            $Wh->locations = $this->getChilds($Wh->wh_loc_id);
            $Wh->Actions = '<a data-type="edit" data-id="'.$Wh->wh_loc_id.'" data-toggle="modal"  onclick="editWarehouseConfiguration('.$Wh->wh_loc_id.',\''.$Wh->wh_location_types.'\');"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';
            //$level++;
        }

        if (count($parentWhs) > 0)
        {
            return $parentWhs;
        }
        else
        {
            return array();
        }
    }
    
    public function getChilds($whid)
    {
        $subArray = array();
        $parentWh = DB::table('warehouse_config')
                ->leftJoin('bin_type_dimensions','bin_type_dimensions.bin_type_dim_id','=','warehouse_config.bin_type_dim_id')
                ->select('wh_loc_id','le_wh_id','wh_location','wh_location_types as wh_level_id',
                        DB::raw('getMastLookupValue(wh_location_types) as wh_location_types'),'parent_loc_id','sort_order',
                        DB::raw('getProductGrpName(res_prod_grp_id) as res_prod_grp_id'),
                        DB::raw('getProductName(pref_prod_id) as pref_prod_id'),
                        DB::raw('if(wh_location_types =120005,warehouse_config.length,bin_type_dimensions.length) as length'),
                        DB::raw('if(wh_location_types =120005,warehouse_config.breadth,bin_type_dimensions.breadth) as breadth'),
                        DB::raw('if(wh_location_types =120005,warehouse_config.height,bin_type_dimensions.heigth) as height'),
                        DB::raw('getMastLookupValue(bin_type) as bin_type'),DB::raw('getMastLookupValue(bin_category) as bin_category'))
                ->where('parent_loc_id',$whid)->get()->all();
       //print_r($parentWh); die;
        foreach($parentWh as $subwh)
        {
                      
            array_push($subArray,$subwh);
            $subwh->locations = $this->getChilds($subwh->wh_loc_id);
            /*$subwh->Actions = '<a data-type="edit" data-id="'.$subwh->wh_loc_id.'" data-toggle="modal"  onclick="editLevelConfiguration('.$subwh->wh_loc_id.',\''.$subwh->wh_location_types.'\');"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a>';*/
            $subwh->Actions = '<a data-type="edit" data-id="'.$subwh->wh_loc_id.'" data-toggle="modal"  onclick="editLevelConfiguration('.$subwh->wh_loc_id.',\''.$subwh->wh_location_types.'\');"><span  style="padding-left:15px;"><i class="fa fa-pencil"></i></span></a><a data-type="edit" data-id="'.$subwh->wh_loc_id.'" data-toggle="modal"  onclick="deleteLevelConfiguration('.$subwh->wh_loc_id.');"><span  style="padding-left:15px;"><i class="fa fa-trash-o"></i></span></a>';
        }
        return $parentWh;
    }
    
    public function locationTypes() {
        $location_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as location_name', 'master_lookup.value as location_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name', 'Warehouse location Types')
                        ->where('master_lookup.is_active',1)
                        ->orderBy('location_value')
                        ->get()->all();
        return $location_types;
    } 
    public function masterLookUpData($name) {
        $location_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as location_name', 'master_lookup.value as location_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name','=',$name )->get()->all();
        $location_types= json_decode(json_encode($location_types),true);
        return $location_types;
    } 
     public function masterLookUpDataWithWeightUOM($name) {
        $location_types = DB::table('master_lookup_categories')->select('master_lookup.master_lookup_name as location_name', 'master_lookup.value as location_value')
                        ->join('master_lookup', 'master_lookup.mas_cat_id', '=', 'master_lookup_categories.mas_cat_id')
                        ->where('mas_cat_name','=',$name )
                        ->whereIn('master_lookup.value',[86001,86002])
                        ->get()->all();
        $location_types= json_decode(json_encode($location_types),true);
        return $location_types;
    } 
    public function getWarehouseDetails($wh_id)
    {
         $wh_rs = DB::table('warehouse_config')
                ->leftjoin('products as p','warehouse_config.res_prod_grp_id','=','p.product_id')->select('product_title','wh_loc_id','le_wh_id','wh_location','wh_location_types','parent_loc_id','warehouse_config.sort_order as sort_order','pref_prod_id','bin_type_dim_id','length','breadth','height','lenght_UOM','res_prod_grp_id','x','y','z',DB::raw('getLeWhName(le_wh_id) as location_name'),'bin_category')->where('wh_loc_id',$wh_id)->get()->all();
         $wh_rs= json_decode(json_encode($wh_rs),true);
         return $wh_rs;
    }
   
      public function saveWarehouseDetails($data)
    {  
        $pref_pro_id='0';
        $bin_dim_list='0';
        $bin_category='0';
        $rack_uom='0';
        $level_type='0';
        $bin_category="00";
        if(isset($data['pref_pro_id']))
        {
           $pref_pro_id=$data['pref_pro_id'];
        }
        if(isset($data['bin_dim_list']) && $data['bin_dim_list']!=0)
        {
           $bin_dim_list=$data['bin_dim_list'];
        }else if(!empty($data['selected_bin_dim']))
        {
            $bin_dim_list=$data['selected_bin_dim'];
        }
        if(isset($data['rack_lenghtUom']))
        {
           $rack_uom=$data['rack_lenghtUom'];
        }
        if(isset($data['level_type_value']))
        {
            $level_type=$data['level_type_value'];
        }
        if(isset($data['level_type']))
        {
            $level_type=$data['level_type'];
        }
        if($level_type=='120006' && $bin_dim_list =='0')
        {
            return "lbh_false";
        }
        if(isset($data['bin_category_type']) && $data['bin_category_type']==0 && $level_type=='120006')
        {
           return "bin_cat";
        }
        else if(isset($data['bin_category_type']) && $data['bin_category_type']!=0 && $level_type=='120006')
        {
            $bin_category=$data['bin_category_type'];
        }
 /*       if($level_type==120006 )
        {
            $rack_volumn=$this->checkBincapcityByRack($data['location_name_type']);
            $binVolumn=$this->getBinDimByBinTypeId($bin_dim_list);
            $binVolumn=$binVolumn['length']*$binVolumn['breadth']*$binVolumn['heigth'];
            $rack_cap= floor($rack_volumn['volumn']/$binVolumn);
            if($rack_cap==0)
            {
                return "rack_config";
            }
        }*/
        if(isset($data['location_name']))
        {
            $wh_name = rtrim($data['location_name']);
        }else
        {
            $wh_name = rtrim($data['edit_location_name']);
        }
        if($data['wh_loc_id']!='')
        {
            $checkLocationName=0;

            if($checkLocationName==0)
            {
                $checkBinStatus = DB::table('bin_inventory')
                                ->where('bin_id',$data['wh_loc_id'])
                                ->select('qty')
                                ->first();
                $checkBinStatus = json_decode(json_encode($checkBinStatus), true);
                if($checkBinStatus['qty'] == 0 )
                {
                   $wh= DB::Table('warehouse_config')
                    ->where('le_wh_id', $data['le_wh_id'])
                    ->where('wh_loc_id',$data['wh_loc_id'])
                    ->update(array(
                        'sort_order'=> $data['sort_order'],
                        'res_prod_grp_id'=>$data['product_group2'],  
                        'pref_prod_id'=>$pref_pro_id, 
                        'bin_type_dim_id'=>$bin_dim_list,
                        'bin_category'=>$bin_category,
                        'length'=> $data['rack_length'],
                        'breadth'=>$data['rack_breadth'],  
                        'height'=>$data['rack_height'], 

                        'lenght_UOM'=>$rack_uom,                  
                        'updated_by'=>Session::get('user_id')
                    ));
                    DB::Table('bin_inventory')
                        ->where('wh_id',$data['le_wh_id'])
                        ->where('bin_id',$data['wh_loc_id'])
                        ->update(array('product_id'=>$pref_pro_id)); 
                    return 1;
                }else{
                    return  "bin_pro";
                } 
            }
            else
            {
                return "false";
            }
        }
        else
        {   
            $checkLevelWiseLocationName= DB::Table('warehouse_config')
                ->where('le_wh_id', $data['warehouse_name'])
                ->where('wh_location',$data['location_name'])
                ->get()->all();
            if(empty($checkLevelWiseLocationName))
            {
                $wh_rs = DB::Table('warehouse_config')->insertGetId(array(
                    'le_wh_id'=>$data['warehouse_name'],
                    'wh_location' =>$data['location_name'],
                    'wh_location_types' => $data['level_type'],
                    'parent_loc_id' =>$data['location_name_type'],
                    'res_prod_grp_id'=>($data['product_group2']=='')?0:$data['product_group2'],  
                    'pref_prod_id'=>$pref_pro_id,  
                    'bin_type_dim_id'=>$bin_dim_list,
                    'sort_order'=> ($data['sort_order']=='')?0:$data['sort_order'],
                    'bin_category'=>$bin_category,
                    'length'=> $data['rack_length'],
                    'breadth'=>$data['rack_breadth'],  
                    'height'=>$data['rack_height'], 
                    'lenght_UOM'=>$data['rack_lenghtUom'],
                    'x' => ($data['x_axis']=='')?0:$data['x_axis'],
                    'y' =>($data['y_axis']=='')?0:$data['y_axis'],
                    'z' => ($data['z_axis']=='')?0:$data['z_axis'],  
                    'created_by'=>Session::get('user_id')
                )); 
                if($level_type=='120006')
                {
                     DB::Table('bin_inventory')->insert([
                    'wh_id'=>$data['warehouse_name'],
                    'bin_id' =>$wh_rs,
                    'product_id' => $pref_pro_id
                        ]); 
                }
                if($pref_pro_id!=0)
                {
                    $rs=$this->addProductBinConfig($pref_pro_id,$bin_dim_list,$data['warehouse_name']);
                }
                return '1';
             }
            else
            {
                return "false";
            }
        }        
    }
    public function checkBincapcityByRack($rack_id){
        $rs=DB::table('warehouse_config')
            ->where('wh_loc_id',$rack_id)
            ->select(DB::raw('LENGTH*breadth*height as volumn'))
            ->first();
            $rs=json_decode(json_encode($rs),1);
            return $rs;
    }
    public function saveEditWarehouse($data)
    {
        
            $wh= DB::Table('warehouse_config')
                ->where('wh_loc_id',$data['edit_wh_loc_id'])
                ->update(array( 'length'=>$data['edit_wh_loc_id'],
                    'sort_order'=> $data['edit_sort_order'],
                    'x' => $data['edit_x_axis'],
                    'y' =>$data['edit_y_axis'],
                    'z' => $data['edit_z_axis'],
                    'updated_by'=>Session::get('legal_entity_id')
                ));
                return 1; 
         
    }
    public function getGroupedProducts($name)
    {
        $wh= DB::Table('products as p')
                ->join('product_bin_config as pro_bin', 'p.product_id', '=', 'pro_bin.product_id')
                ->select('pro_bin.product_id as value','p.product_title as label')
                ->where('p.product_title','like','%'.$name.'%')
                ->get()->all();
                $wh=json_encode($wh);
                return $wh;
    }
    public function deleteWarehouseLevels($wh_id)
    {
        $wh= DB::Table('warehouse_config')
            ->where('parent_loc_id',$wh_id)
            ->pluck('parent_loc_id')->all(); 
        if(empty($wh))
        {   
            $checkBinStatus = DB::table('bin_inventory')
                                ->where('bin_id',$wh_id)
                                ->where('qty','!=',0)
                                ->pluck('bin_id')->all();
            if(empty($checkBinStatus))
            {
                DB::table('bin_inventory')
                    ->where('bin_id',$wh_id)
                    ->delete();
                $delete_wh= DB::Table('warehouse_config')
                            ->where('wh_loc_id',$wh_id)
                            ->delete(); 
                return $delete_wh;
            }else
            {
                return "bin_pro";
            }
        }else
        {
           return "false";
        }
    }
    public function warehouseList()
    {
        $legalentityid = Session::get('legal_entity_id');
        $userId=Session::get('userId');
        $role=new Role();
        $dcList = $role->getWarehouseData($userId, 6);
        $dc=json_decode($dcList,true);
        $dc=implode($dc,',');
        $dc=explode(',',$dc);
         $wh= DB::Table('warehouse_config')
         ->leftjoin('legalentity_warehouses as le','le.le_wh_id','=','warehouse_config.le_wh_id')
            ->select('wh_loc_id','warehouse_config.le_wh_id','wh_location','wh_location_types')
            ->where('parent_loc_id',0)
            ->whereIn('le.le_wh_id',$dc)
            ->get()->all(); 
        return $wh;
    }
    public function getLevelWiseDetails($level_id,$wh_id){
        $wh= DB::Table('warehouse_config')
            ->select('wh_loc_id','le_wh_id','wh_location','wh_location_types','parent_loc_id')
            ->where('le_wh_id',$wh_id)
            ->where('wh_location_types',$level_id)
            ->get()->all(); 
        return $wh;
    }
    public function getChildLocations($wh_id,$level_id)
    {
        $select = '<select><option>Select location</option>';
        $whArray  = DB::Table('warehouse_config')
                    ->where('le_wh_id',$wh_id)->where('wh_location_types','<',$level_id)->where('wh_location_types','<>','120001')->orderBy('wh_location_types','asc')->get()->all();

        $finalArray = json_decode(json_encode($whArray),1);

        $firstLoc = $finalArray[0]['parent_loc_id'] ;
        foreach($finalArray as $obj)
        {
            if($obj['parent_loc_id'] == $firstLoc)
            {
            $select .= '<option id='.$obj["wh_loc_id"].'>'.$obj["wh_location"].'</option>';
            $select .= $this->getChildLocs($obj["wh_loc_id"],$whArray,$level_id);
            }
        }
        return $select;
    }
    
    public function getChildLocs($chkVal,$WhObj,$level_id)
    {
        $finalArray = json_decode(json_encode($WhObj),1);
        $select = '';
        foreach($finalArray as $obj)
        { 
            if($obj['wh_location_types']<$level_id && $obj['parent_loc_id'] == $chkVal)
            {
                $select .= '<option id='.$obj["wh_loc_id"].'>'.$obj["wh_location"].'</option>';
                $select .= $this->getChildLocs($obj["wh_loc_id"],$finalArray,$level_id);
            }
            
        }
        return $select;
    }   
    public function getMasterLookupNameByValue($value_id)
    {
        $masterRs=DB::table('master_lookup')
                    ->where('value',$value_id)
                    ->pluck('master_lookup_name')->all();
        return $masterRs;
    }
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
        if(!empty($data) && !empty($data['product_id']) && !empty($data['capacity']))
        {
             $rs = DB::Table('product_bin_config')->insert([ 
                    'product_id'=>$data['product_id'],                    
                    'level_id'=>120006,
                    'qty_uom'=>16001,
                    'qty'=>$data['capacity']]); 
            if(!empty($rs))
            {
                return  json_encode(array('status'=>"success",'message'=>'check how many inventory it have for respective bin','data'=>json_encode($rs)));
            }else
            {
                return json_encode(array('status'=>"failed",'message'=> 'Invaid data','data'=>""));  
            }   
        }
    }
    public function checkBinCapacityData($data)
    {
        if(!empty($data) && !empty($data['bin_id']))
        {
        
            $rs = DB::Table('warehouse_config as wh_conf')
                    ->join('product_bin_config as pro_conf','wh_conf.res_prod_grp_id','=','pro_conf.product_id')
                    ->select('pro_conf.qty','pro_conf.product_id','wh_conf.wh_loc_id as bin_id','wh_conf.wh_location as bin_name')
                    ->where('wh_conf.wh_loc_id','=',$data['bin_id'])
                    ->get()->all();
            if(!empty($rs))
            {
                return  json_encode(array('status'=>"success",'message'=>'Check bin capacity for respective bin id','data'=>json_encode($rs)));
            }else
            {
                return json_encode(array('status'=>"failed",'message'=> 'There is no product configuration for this bin','data'=>""));  
            }   
        }else
        {
             return json_encode(array('status'=>"failed",'message'=> 'Invaid data','data'=>""));
        }
    }
    
    public function saveBinDimensionsCongData($data)
    {
        $checkExists= DB::table('bin_type_dimensions')
                    ->where('bin_type',$data['bin_dim_name'])
                    ->where('length',$data['bin_dim_lenght'])
                    ->where('breadth',$data['bin_dim_width'])
                    ->where('heigth',$data['bin_dim_height'])
                    ->get()->all();
        if(empty($checkExists))
        {
            $wh= DB::Table('bin_type_dimensions')
                ->insert(['bin_type'=>$data['bin_dim_name'],
                    'length' => $data['bin_dim_lenght'],
                    'breadth'=> $data['bin_dim_width'],
                    'heigth' =>$data['bin_dim_height'],
                    'weigth' =>$data['bin_weight'],
                    'lbh_uom' =>$data['bin_lenghtUOm'],
                    'weigth_uom' =>$data['bin_lenghtUOm'] ]);

                return 1; 
        }
        else
        {
           return "false";
        }
    }
    public function getBinDimensionsData()
    {
        $getBinDim= DB::table('bin_type_dimensions')
                    ->join('master_lookup','master_lookup.value','=','bin_type_dimensions.bin_type')
                    ->select('bin_type_dim_id',DB::raw("CONCAT(master_lookup_name,'(',LENGTH,',',breadth,',',heigth,')') AS bin_dim_name"))
                    ->get()->all();
        $getBinDim=json_decode(json_encode($getBinDim),true);
        return $getBinDim;
    }
    public function getProductsByProdutGrpData($grp_id,$dc_id)
    {
       /*$rs= DB::table('products')
            ->select('product_id','product_title','sku')
            ->get();*/
       $rs= DB::table('inventory as i')
             ->join('products as p','i.product_id','=','p.product_id')
             ->select('p.product_id','p.product_title','p.sku')
             ->where('i.le_wh_id',$dc_id)
             ->where('p.product_group_id',$grp_id)
             ->get()->all();
        $rs=json_decode(json_encode($rs),true);
        return $rs;
    }
    public function getGroupedProductsList($dcId=null)
    {
        if($dcId!=null){
            $dc[0]=$dcId;
        }else{
             $userId=Session::get('userId');
            $role=new Role();
            $dcList = $role->getWarehouseData($userId, 6);
            $dc=json_decode($dcList,true);
            $dc=implode($dc,',');
            $dc=explode(',',$dc);
        }       
        $productGroup=DB::table('inventory as i')
                     ->join('products as p','i.product_id','=','p.product_id')
                     ->select('p.product_group_id')
                     ->whereIn('i.le_wh_id',$dc)
                     ->distinct()
                     ->get()->all();
        $productGroup=json_decode(json_encode($productGroup),1);
        $productGroupArray=[];
        for($i=0;$i<count($productGroup);$i++){
            $productGroupArray[$i]=$productGroup[$i]['product_group_id'];
        }
        $rs= DB::table('product_groups')
            ->select('product_grp_name as product_title','product_grp_ref_id as product_group_id')
            ->whereIn('product_grp_ref_id',$productGroupArray)
            ->groupby('product_grp_ref_id')
            ->get()->all();
        $rs=json_decode(json_encode($rs),true);
        return $rs;   
    }
    public function getProductGrpBinId($grp_id,$wh_id)
    {
        $rs= DB::table('product_bin_config as pro_bin_conf')
            ->join('bin_type_dimensions as bin_dim','bin_dim.bin_type_dim_id','=','pro_bin_conf.bin_type_dim_id')
            ->join('master_lookup','master_lookup.value','=','bin_dim.bin_type')
            ->select('pro_bin_conf.bin_type_dim_id',DB::raw("CONCAT(master_lookup_name,'(',LENGTH,',',breadth,',',heigth,')') AS bin_dim_name"))
            ->where('pro_bin_conf.prod_group_id',$grp_id)
            ->where('pro_bin_conf.wh_id',$wh_id)
            ->get()->all();          
       
        $rs=json_decode(json_encode($rs),true);
        return $rs;      
    }
    public function multiBinLevelConfigData($data)
    {
        $rs='';
        $wh_id=$data['Rack_level_warehouse_name'];
        $rack_id=$data['rack_level_name'];
        $bin_dim_id=$data['rack_bin_type'];
        $no_of_bins=$data['rack_no_bins'];     
        $wh= DB::Table('warehouse_config')
            ->where('le_wh_id',$wh_id)
            ->where('parent_loc_id',$rack_id)
            ->first(); 
        if(empty($wh))
        {
           $rack_name= DB::Table('warehouse_config')
                    ->where('le_wh_id',$wh_id)
                    ->where('wh_loc_id',$rack_id)
                    ->pluck('wh_location')->all();
            $rack_name[0];
            for($i=1;$i<=$no_of_bins;$i++)
            {
                 $rs = DB::Table('warehouse_config')->insert([ 
                    'le_wh_id'=>$wh_id,                    
                    'wh_location'=>$rack_name[0].'_'.$i,
                    'wh_location_types'=>120006,
                    'parent_loc_id'=>$rack_id,
                    'bin_type_dim_id'=>$bin_dim_id,
                    'created_by'=>Session::get('legal_entity_id')]); 
            }
           $rs='1';
        }else
        {
          $rs="false";
        }
        return $rs;
    }
    public function checkRackCapacityData($data)
    {
        $bin_id=$data['bin_dim_id'];
        $wh_id=$data['wh_id'];
        $rack_id= $data['rack_id'];
        $checkVolumn= DB::Table('warehouse_config')
                    ->where('le_wh_id',$wh_id)
                    ->where('wh_loc_id',$rack_id)
                    ->select('length','breadth','height')
                    ->first();
        $checkVolumn= json_decode(json_encode($checkVolumn),1);
        if(!empty($checkVolumn['length']) && !empty($checkVolumn['breadth']) && !empty($checkVolumn['height']))
        {
            $rack_volumn=$checkVolumn['length']*$checkVolumn['breadth']*$checkVolumn['height'];
            $getBinVolumn= $this->getBinDimByBinTypeId($bin_id);
            $binVolumn=$getBinVolumn['length']*$getBinVolumn['breadth']*$getBinVolumn['heigth'];        
            $rs= $rack_volumn/$binVolumn;
            return floor($rs);

        }else
        {
           return "false";
        }
    }
    public function getBinDimByBinTypeId($binDimId){

         $checkDim= DB::table('bin_type_dimensions')
                    ->where('bin_type_dim_id',$binDimId)
                    ->select('length','breadth','heigth')
                    ->first();
         return json_decode(json_encode($checkDim),1);
    }
    public function addProductBinConfig($proId,$binTypeDimId,$wh_id)
    {
        $status=0;
        $getProGrpId = DB::table('products')
                        ->join('product_pack_config','products.product_id','=','product_pack_config.product_id')
                        ->select('products.product_group_id','product_pack_config.length','product_pack_config.breadth','product_pack_config.height')
                        ->where('level','=','16004')
                        ->where('products.product_id','=',$proId)
                        ->first();
        $getProGrpId = json_decode(json_encode($getProGrpId),true);
        $getBinDimQuery = DB::table('bin_type_dimensions')
                            ->where('bin_type_dim_id',$binTypeDimId)
                            ->where('bin_type','!=','109003')
                            ->first();
        $getBinDimQuery = json_decode(json_encode($getBinDimQuery),true);
        if(!empty($getBinDimQuery) && !empty($getProGrpId))
        {
            $checkBinConfQuery = DB::table('product_bin_config')
                                ->where('wh_id','=',$wh_id)
                                ->where('prod_group_id','=',$getProGrpId['product_group_id'])
                                ->where('bin_type_dim_id','=',$binTypeDimId)
                                ->first();
            $checkBinConfQuery = json_decode(json_encode($checkBinConfQuery),true);
            if(empty($checkBinConfQuery) )
            {
                $prodcutBinVolumn = ($getBinDimQuery['length']*$getBinDimQuery['breadth']*$getBinDimQuery['heigth'])/($getProGrpId['length']*$getProGrpId['breadth']*$getProGrpId['height']);

                $prodcutBinVolumn= floor($prodcutBinVolumn);
                $sqlQuery = DB::Table('product_bin_config')->insert([ 
                        'prod_group_id'=>$getProGrpId['product_group_id'],                    
                        'wh_id'=>$wh_id,
                        'bin_type_dim_id'=>$binTypeDimId,
                        'pack_conf_id'=>'16004',
                        'min_qty'=>'1',
                        'max_qty'=>$prodcutBinVolumn]);
                $status = $sqlQuery;
            }           
        }  
        return $status;
    }    
    public function getProductBinMapping($whId,$binType)
    {
        try
        {
            if($binType == 1)
            {
                $DimensionIdsList = DB::table('bin_type_dimensions')->pluck('bin_type_dim_id')->all();
            }
            else
            {
                $DimensionIdsList = DB::table('bin_type_dimensions')->where('bin_type',$binType)->pluck('bin_type_dim_id')->all();
            }
            if ($whId)
            {
                $data = DB::table('warehouse_config')->select('wh_location', DB::raw('getLocNameById(parent_loc_id,2) as level'), DB::raw('getLocNameById(parent_loc_id,1) as zone'), DB::raw('getLocNameById(parent_loc_id,0) as aisle'), 'pref_prod_id', DB::raw('getProductName(pref_prod_id) as title'), DB::raw('getSkuById(pref_prod_id) as sku'), 'res_prod_grp_id', DB::Raw('getBinDimById(bin_type_dim_id) as bin_type'), 'length', 'breadth', 'height', 'bin_inventory.qty', 'bin_type_dim_id')
                                ->leftJoin('bin_inventory', 'wh_loc_id', '=', 'bin_inventory.bin_id')
                                ->where('wh_location_types', '120006')->where('wh_id', '=', $whId)->whereIn('bin_type_dim_id',$DimensionIdsList)->get()->all();
            }
            else
            {
                $data = DB::table('warehouse_config')->select('wh_location', DB::raw('getLocNameById(parent_loc_id,2) as level'), DB::raw('getLocNameById(parent_loc_id,1) as zone'), DB::raw('getLocNameById(parent_loc_id,0) as aisle'), 'pref_prod_id', DB::raw('getProductName(pref_prod_id) as title'), DB::raw('getSkuById(pref_prod_id) as sku'), 'res_prod_grp_id', DB::Raw('getBinDimById(bin_type_dim_id) as bin_type'), 'length', 'breadth', 'height', 'bin_inventory.qty', 'bin_type_dim_id')
                                ->leftJoin('bin_inventory', 'wh_loc_id', '=', 'bin_inventory.bin_id')
                                ->where('wh_location_types', '120006')->whereIn('bin_type_dim_id',$DimensionIdsList)->get()->all();
            }
            return json_decode(json_encode($data), 1);
        }
        catch (Exception $ex)
        {
            return $ex->getMessage();
        }
    }
}