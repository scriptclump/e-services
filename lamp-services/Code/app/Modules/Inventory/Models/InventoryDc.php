<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;
use Session;
use DB;

class InventoryDc extends Model {

    protected $table = "vw_inventory_dc";

    public function getAllInventory($dcName = '') {
        $sql = $this;
        if (!empty($dcName)) {
            $sql = $sql->whereIn('le_wh_id', $dcName);
        }

        if (Session('roleId') != '1') {
            $wh_ids = $this->getWarehouseByLegalEntityId();
            $sql = $sql->whereIn('le_wh_id', $wh_ids);
        }

        $sql = $sql->groupBy('le_wh_id')->get(array('dcname', 'cpvalue', 'ptrvalue', 'mrpvalue', 'mapvalue', 'le_wh_id', 'espvalue'))->all();

        return $sql;
    }

    public function getWarehouseByLegalEntityId() {

           $this->_roleModel = new Role();

            $wh_list = json_decode($this->_roleModel->getFilterData(6), 1);
            $wh_list = (json_decode($wh_list['sbu'],true));

            if(count(@$wh_list['118001']) > 0 ){
                return DB::table('legalentity_warehouses')->whereIn('le_wh_id', explode(',', $wh_list['118001']))->pluck('le_wh_id')->all();
            }else{
                return DB::table('legalentity_warehouses')->whereIn('le_wh_id', 0)->pluck('le_wh_id')->all();

            }
       
           
    }
public function getWareHouseName($warehouseId)
{
    $sql = DB::table("legalentity_warehouses")->where("le_wh_id", "=", $warehouseId)->get(array('lp_wh_name'))->all();
    $warehousename = json_decode(json_encode($sql), true);
    return $warehousename[0]['lp_wh_name'];
}

public function getAllInventoryByName($dcName) {
        //$dcname = "select * from vw_inventory_dc where le_wh_id IN (".$dcName.")";
        $dcname = "select MRP as mrpvalue,ESP as espvalue,PTR as ptrvalue,le_wh_id from vw_inventory_display where le_wh_id IN (".$dcName.") group by le_wh_id";
        $Data = DB::select(DB::raw($dcname));   
        return $Data;

    }
public function getAllInventoryFromLeWh($dcname=''){
    $inventory = DB::table('legalentity_warehouses');
    if (!empty($dcname)) {
        $inventory = $inventory->whereIn('le_wh_id', $dcname);
    }
    else{
        $wh_ids = $this->getWarehouseByLegalEntityId();
        $inventory = $inventory->whereIn('le_wh_id', $wh_ids);
    }
    $inventory = $inventory->groupBy('le_wh_id')->get(array('display_name as dcname', 'le_wh_id'))->all();

    return $inventory;
}
    public function getDataForMail(){
        $sql = DB::table('legalentity_warehouses')->where('dc_type',118001)->get(array('display_name as dcname','le_wh_id'))->all();
        return $sql;
    }
}


