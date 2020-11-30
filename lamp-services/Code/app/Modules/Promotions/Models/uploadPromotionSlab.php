<?php
/*
FileName : uploadSlabProductsModel.php
Author   : eButor
Description : All the outbound order related functions are here.
CreatedDate :15/Aug/2016
*/
//defining namespace


namespace App\Modules\Promotions\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Promotions\Models\slabDetailsModel;
use App\Central\Repositories\RoleRepo;
use DB;
use Session;

class uploadPromotionSlab extends Model
{
    protected $table = 'promotion_details';
    protected $primaryKey = "prmt_det_id";
    /**
     * [getProductID get product id on the basis of sku]
     * @param  [string] $sku [sku]
     * @return [int]      [product id]
     */
    public function getProductID($sku){
    	$product_id = DB::table("products")
    				->where('sku', '=', $sku)
    				->first();
    	if( isset($product_id->product_id) ){
    		return $product_id->product_id;
    	}else{
    		return 0;
    	}
    }
    /**
     * [getState get state information]
     * @param  [string] $state [state name]
     * @return [int]        [state id]
     */
    public function getState($state){
        $state_id = DB::table("zone")
                    ->where('name', '=', $state)
                    ->first();

        if(isset($state_id->zone_id)){
            return $state_id->zone_id;
        }else{
            return 0;
        }
    }
    /**
     * [getStateByID get state by id]
     * @param  [int] $stateID [state id]
     * @return [int]          [state id]
     */
    public function getStateByID($stateID){

        $state_id = DB::table("zone")
                    ->where('zone_id', '=', $stateID)
                    ->first();

        if(isset($state_id->zone_id)){
            return $state_id->zone_id;
        }else{
            return 0;
        }
    }
    /**
     * [getCustomerType Get customer type id using value]
     * @param  [int] $customer_type [customer type string]
     * @return [int]                [customer type id]
     */
    public function getCustomerType($customer_type){

        $customer_type = DB::table("master_lookup")
                    ->where('master_lookup_name', '=', $customer_type)
                    ->where('mas_cat_id', "=", 3)
                    ->first();
        if(isset($customer_type->value)){
            return $customer_type->value;
        }else{
            return 0;
        }

    }

    // Function to insert / update Uploaded slab
    public function insertPromotionSlabsMain($promotion_data){
        try{
            $returnResult = array();
                // check for the existing data
                $tableData = DB::table("products_slab_rates AS slab")
                            ->where("slab.product_id", "=", $promotion_data['applied_ids'])
                            ->where("slab.state_id", "=", $promotion_data['prmt_states'])
                            ->where("slab.customer_type", "=", $promotion_data['prmt_customer_group'])
                            ->where("slab.start_date", "=", $promotion_data['start_date'])
                            ->where("slab.end_date", "=", $promotion_data['end_date'])
                            ->where("slab.wh_id", "=", $promotion_data['warehouse'])
                            ->first();

                // insert the data is the combincation not found
                if(count($tableData) == 0){
                    $this->insert($promotion_data);
                    $mainTblID = \DB::getPdo()->lastInsertId();
                    $returnResult['message'] = "Slab Inserted Successfully";
                    $returnResult['counter_flag'] = "1";
                    $returnResult['main_table_id'] = $mainTblID;
                }else{ 

                    // Update the Slab Main Table
                    $updateData = uploadPromotionSlab::find($tableData->prmt_det_id);
                    $updateData->prmt_lock_qty = $promotion_data['prmt_lock_qty'];
                    $updateData->start_date = $promotion_data['start_date'];
                    $updateData->end_date = $promotion_data['end_date'];
                    $updateData->updated_by=Session::get('userId');
                    $updateData->save();

                    $returnResult['message'] = "Slab Updated Successfully!";
                    $returnResult['counter_flag'] = "3";
                    $returnResult['main_table_id'] = $tableData->prmt_det_id;
                }
           
            return $returnResult;
                                
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        } 
    }

    // Insert data into slab table
    public function insertSlabData($slab_data){
        try{

            // Check for duplicate record before insert into Slab Table
            $newSlabID=0;
            $tableData = DB::table("products_slab_rates AS slab")
                        ->where("slab.product_id", "=", $slab_data['product_id'])
                        ->where("slab.state_id", "=", $slab_data['state_id'])
                        ->where("slab.customer_type", "=", $slab_data['customer_type'])
                        ->where("slab.end_range", "=", $slab_data['end_range'])
                        ->where("slab.pack_type","=",$slab_data['pack_type'])
                        ->where("slab.esu","=",$slab_data['esu'])
                        ->where("slab.start_date", "=", $slab_data['start_date'])
                        ->where("slab.end_date", "=", $slab_data['end_date'])
                       ->where("slab.wh_id","=",$slab_data['wh_id'])
                        ->count();

            if($tableData==0){
                $objSlab = new slabDetailsModel();
                $newSlabID = $objSlab->insertSlabDetails($slab_data);
            }
            $returnResult['message'] = "Slab Inserted";
            $returnResult['counter_flag'] = "1";
            $returnResult['main_table_id'] = $newSlabID;

       

            return $newSlabID;
                            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        } 
    }

    //delete the record from slab table
    public function deleteFromDetailsTable($deleteid){

        $objSlab = new slabDetailsModel();
        return $objSlab->deleteSlabDetails($deleteid);
    }

    // Get Slab data for the template download
    public function getDataAsPerQuery($makeFinalSqlInner, $makeFinalSqlOuter, $mdl_state){
        try{

            $sqlWhrClsOuter = '';
            $sqlWhrClsInner = '';
            $countLoop = 0;
            $sqlWhrCls = '';
            // make outer query
            foreach ($makeFinalSqlOuter as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'WHERE ' . $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }

            $countLoop++;
            }
            // make inner query
            $countLoop=0;
            foreach ($makeFinalSqlInner as $value) {
                if( $countLoop==0 ){
                $sqlWhrCls .= 'WHERE ' . $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            }

            $legalEntityQuery = "";
            if(Session::get('legal_entity_id')!=0){
                $legalEntityQuery=" WHERE prd.legal_entity_id='". Session::get('legal_entity_id') ."'";
            }

            /*$sqlQuery ="select 
                          prd.product_id,
                          prd.sku,
                          prd.product_title,
                          prd.mrp,
                          prmt.prmt_det_id,
                          prmt.prmt_lock_qty,
                          slab.state_id,
                          slab.customer_type,
                          slab.end_range,
                          slab.price,
                          slab.pack_type,
                          slab.esu,
                          DATE_FORMAT(slab.start_date, '%m/%d/%y') AS 'start_date',
                          DATE_FORMAT(slab.end_date, '%m/%d/%y') AS 'end_date',
                          getStateNameById (slab.state_id) AS 'StateName',
                          getMastLookupValue (slab.customer_type) AS 'CustomerType',
                          '".$mdl_state."' as 'CommonState' 
                        FROM
                          products AS prd 
                          LEFT JOIN promotion_details AS prmt 
                            ON prd.product_id = prmt.applied_ids 
                            ".$sqlWhrClsInner."
                          LEFT JOIN products_slab_rates AS slab 
                            ON prmt.prmt_det_id = slab.prmt_det_id 
                        " . $legalEntityQuery . $sqlWhrClsOuter;*/

                        //AND prd.`product_id` IN (4134, 4226)
                        if($mdl_state == 0){
                            $mdl_state ="";
                        }
                        $sqlQuery = "select * from vw_promotion_slab as prd ".$legalEntityQuery.$sqlWhrCls;

                        //echo $sqlQuery;exit;

            $allData = DB::select(DB::raw($sqlQuery));

            return json_encode($allData);
                            
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            Redirect::to('/')->send();
        } 
    }
    /**
     * [getAllState Get states list]
     * @return [array] [states list]
     */
    public function getAllState(){
        $sqlQuery = "select zn.name AS 'ItemName', '1' AS 'DataFlag' FROM zone AS zn WHERE zn.`country_id`=99";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }
    /**
     * [getAllCustomerType Get customer types list]
     * @return [array] [customer types list]
     */
    public function getAllCustomerType(){
        $sqlQuery = "select ml.master_lookup_name AS 'ItemName', '2' AS 'DataFlag' FROM master_lookup AS ml WHERE ml.`mas_cat_id`=3 AND is_active=1";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }
    /**
     * [getBrandsAsManufacId Get brands list]
     * @param  [int] $manufac [manufacturer id]
     * @return [array]          [brands information]
     */
    public function getBrandsAsManufacId($manufac){

        $getBrand = DB::table('brands')
                            ->where('mfg_id', '=', $manufac)
                            ->where('is_active', '=', '1')
                            ->get()->all();
        return $getBrand;
    }
    
    //getting the pack name fro download slab template
    /**
     * [getPacktypeData get pack types list]
     * @return [array] [ pack types list]
     */
    public function getPacktypeData()
    {
        $sqlQuery  = "select master_lookup_name as 'packname' from master_lookup where mas_cat_id=16";
        $packdata = DB::select(DB::raw($sqlQuery));
        return json_encode($packdata);
    }

    /**
     * [getPacktype Get pack type]
     * @param  [string] $name       [pack name]
     * @param  [int] $product_id [product id]
     * @param  [int] $esu        [esu]
     * @return [array]             [packs array]
     */
    public function getPacktype($name,$product_id,$esu){
        $pack_data="select COUNT(*) AS 'count', `level` ,`star`,ESULimit,`no_of_eaches`
                    FROM (
                        SELECT *, MOD('".$esu."',pc.esu) AS 'ESULimit'
                        FROM product_pack_config pc 
                        WHERE pc.`product_id`= $product_id
                    ) AS innertbl1 
                    WHERE ESULimit=0
                    AND `level`=(SELECT m.value FROM master_lookup m WHERE m.master_lookup_name='$name' AND m.`mas_cat_id`=16)";

      
        $packData   = DB::select(DB::raw($pack_data));

        return json_encode($packData);
    }
    /**
     * [deleteMainDetails delete promotion details]
     * @param  [int] $id [promotion id]
     * @return [int]     [1]
     */
    public function deleteMainDetails($id){

        $deleteDetails = "delete  FROM promotion_details where prmt_det_id =$id";
        $deleteDetails=DB::delete(DB::raw($deleteDetails));
        return 1;
    }
    /**
     * [getPackName get pack value by name]
     * @param  [string] $name [pack name]
     * @return [array]       [pack id]
     */
    public function getPackName($name){

        $Query = "select m.value  from master_lookup m where m.master_lookup_name = '$name' ";
        $PackID=DB::select(DB::raw($Query));

        return $PackID;
    }
    /**
     * [getPackNameById get pack name by id]
     * @param  [int] $id [pack id]
     * @return [string]     [pack name]
     */
    public function getPackNameById($id){

        $query="select m.master_lookup_name from master_lookup m where m.value = $id and m.mas_cat_id=16";
        $packName=DB::select(DB::raw($query));
        return $packName[0]->master_lookup_name;
    }
    /**
     * [getAllDCType get dc's list]
     * @return [array] [dc's list]
     */
    public function getAllDCType(){
        $entityid = Session::get('legal_entity_id');
        $rolerepo = new RoleRepo();
        $globalaccess=$rolerepo->checkPermissionByFeatureCode("GLB0001",Session::get('userId'));
        if($globalaccess){
            $sqlQuery = "select * from legalentity_warehouses where dc_type=118001 and status=1";
        }else{
            $sqlQuery = "select * from legalentity_warehouses where dc_type=118001 and status=1 and legal_entity_id =".$entityid."";
        }
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }
    /**
     * [getDcId get dc id by name]
     * @param  [string] $dcname [dc name]
     * @return [int]         [dc id]
     */
     public function getDcId($dcname){
    
        $dc_type = trim($dcname);
        $dc_id = DB::table("legalentity_warehouses")
                    ->select('le_wh_id','legal_entity_id')
                    ->where('lp_wh_name', '=', $dc_type)
                    ->first();
        
        if($dc_id)         
        return $dc_id;
        else return 0;
    }
    /**
     * [checkIsSlabExist check if slab exists on given date]
     * @param  [array] $promotion_data [promotion info]
     * @return [int]                 [no of promotions exist]
     */
    public  function checkIsSlabExist($promotion_data)
    {
        $start=$promotion_data['start_date'];
        $end=$promotion_data['end_date'];
        $tableData = DB::table("products_slab_rates AS slab")
                    ->where("slab.product_id", "=", $promotion_data['applied_ids'])
                    ->where("slab.customer_type", "=", $promotion_data['prmt_customer_group'])
                    ->where("slab.wh_id", "=", $promotion_data['warehouse'])
                    ->whereRaw("(('".$start ."' between slab.start_date AND slab.end_date)  or ('".$end."' between slab.start_date AND slab.end_date))")
                    ->get()->all();

        if(count($tableData)>0){
            return 1;
        }else{
            return 0;
        }
    }
    /**
     * [getDcNameById get dc name by id]
     * @param  [int] $dcid [dc id]
     * @return [array]       [dc name]
     */
    public function getDcNameById($dcid){
        $dc_id = DB::table("legalentity_warehouses")
                    ->select('display_name')
                    ->where('le_wh_id', '=', $dcid)
                    ->first();        
        return $dc_id;   
    }
    /**
     * [isProductExistForLe  checks If the product has inventory]
     * @param  [int]  $le_id      [warehouse id]
     * @param  [int]  $product_id [product id]
     * @return int             [count]
     */
    public function isProductExistForLe($le_id,$product_id)
    {
        $isExist=DB::table('inventory')
                ->where('le_wh_id','=',$le_id)
                ->where('product_id','=',$product_id)
                ->count();
        return $isExist;
    }
    /**
     * [getPacksToApplySlab Get packs which are eligible to apply slab]
     * @param  [int] $qty        [quantity]
     * @param  [int] $product_id [product id]
     * @return [array]             [packs list]
     */
    public function getPacksToApplySlab($qty,$product_id){
        $getPacks = DB::table('product_pack_config')
                    ->select('product_id','no_of_eaches','esu','star','level')
                    ->where('product_id',$product_id)
                    ->where(DB::raw('no_of_eaches*esu'),'>',$qty)
                    ->get()->all();
        return json_decode(json_encode($getPacks),1);
    }
}