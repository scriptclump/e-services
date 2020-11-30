<?php
//defining namespace
namespace App\Modules\Assets\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Assets\Models\assetsModel;
use DB;
use App\Central\Repositories\productRepo;
use Session;
use UserActivity;
use Utility;
class assetsModel extends Model
{
    protected $table = 'assets';
    protected $primaryKey = "asset_id";

    public function DetailsAsPerAsset($makeFinalSql, $orderBy, $page, $pageSize){

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;

        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }

         $legalEntityQuery = "";
        if(Session::get('legal_entity_id')!=2){
            $legalEntityQuery=" AND prd.legal_entity_id='" . Session::get('legal_entity_id') . "'";
       }
        
        
        $assetsDetails = "select * FROM
                            (
                                 SELECT *,  (TotalAsset-TotalWarranty) AS 'TotalOutOfWarranty',(innertbl2.TotalAsset*innertbl2.mrp) AS 'TotalAssetValue'
                                 FROM
                                 (
                                      SELECT innertbl.product_id,innertbl.mrp,AssetDetails,
                                      SUM(innertbl.AssetWithGRN) AS 'TotalAsset',
                                      SUM(innertbl.Allocated) AS 'TotalAllocated',
                                      SUM(innertbl.Repaired) AS 'TotalRepaired',
                                      SUM(innertbl.Available) AS 'TotalAvail',
                                      SUM(innertbl.Warranty) AS 'TotalWarranty',
                                      innertbl.AssetName AS 'AssetCategory',
                                      if(innertbl.`asset_type`=1,'yes','no') AS 'is_movable',
                                      if(innertbl.AssetWithGRN=0,0, SUM(innertbl.mrp)) AS 'AssetCatMrp',
                                      innertbl.asset_category AS 'AssetCategoryId'
                                      FROM (
                                           SELECT prd.`product_id`,prd.mrp,prd.asset_type,prd.`asset_category`,ast.`depresiation_date`,ast.`depresiation_month`,ast.`depresiation_per_month`, CONCAT(prd.`product_title`, '( ', prd.`sku`, ' )') AS 'AssetDetails',  ast.`asset_status`,
                                            IF(ast.`asset_status` IS NULL, 0, 1) AS 'AssetWithGRN',
                                            IF(ast.`asset_status`=0, 1,0) AS 'Allocated',
                                            IF(ast.`asset_status`=1, 1,0) AS 'Available',
                                            IF(ast.`asset_status`=2, 1,0) AS 'Repaired',
                                            IF(ast.`asset_status` IS NULL, 0 , IF(DATE(ast.`warranty_end_date`) < DATE(NOW()), 0, 1)) AS 'Warranty',
                                            (SELECT master_lookup_name FROM master_lookup WHERE VALUE=prd.`asset_category`) AS 'AssetName' 
                                           FROM products AS prd
                                           LEFT JOIN assets AS ast ON prd.`product_id`=ast.`product_id`
                                           WHERE prd.`product_type_id`=130001 ".$legalEntityQuery."
                                          ) AS innertbl 
                                      GROUP BY innertbl.product_id
                                     ) AS innertbl2
                                ) AS innertbl3
                            ".$sqlWhrCls.$orderBy;
                                     

        $assetData = DB::select(DB::raw($assetsDetails));       
       
        return $assetData;
    }

    public function totalAssetDetails($product_id){

        $concatQuery="";

        $concatQuery = ", CONCAT('<center><code>',
                            '<a href=\"javascript:void(0)\" onclick=\"allocateasset(',ast.asset_id,')\">
                            <i class=\"fa fa-plus\"></i></a>&nbsp&nbsp
                            <a href=\"javascript:void(0)\" onclick=\"updateAsset(',ast.asset_id,')\">
                            <i class=\"fa fa-pencil\"></i></a>&nbsp&nbsp
                            <a href=\"javascript:void(0)\" onclick=\"viewAsset(',ast.asset_id,')\">
                            <i class=\"fa fa-eye\"></i></a>
                            </code></center>') 
           AS 'CustomAction' ";
          

        $assetsDetails = "select ast.*, p.mrp,
                            case ast.asset_status
                                when 1 then '<div style=\"color:blue; font-weight:bold;\">Available</div>'
                                when 0 then '<div style=\"color:red; font-weight:bold;\">Allocated</div>'
                                when 2 then '<div style=\"color:red; font-weight:bold;\">Repair</div>'
                            else 'Unknown'
                            end 'AssetStatus'".$concatQuery."
                            FROM assets as ast   
                            join products as p on (p.product_id = ast.product_id)
                            where ast.product_id=$product_id";

        $assetData = DB::select(DB::raw($assetsDetails));          
       
        return $assetData;

    }


    public function countWithAssetData($assetid){
        $checkData = DB::table("assets")
                    ->where("asset_id", "=", $assetid)
                    ->count();

        return $checkData;
    }

    public function getAssetTotalCost(){

        $legalid = Session::get("legal_entity_id");

         $assetsTotalCost = "select ifnull(SUM(TotalAssetValue),0) AS 'Total'
                                FROM(
                                    SELECT  (total_product*mrp) AS 'TotalAssetValue' 
                                    FROM(
                                            SELECT prd.`product_id`, prd.mrp, COUNT(ast.product_id) AS 'total_product' 
                                            FROM products AS prd
                                            INNER JOIN assets AS ast ON prd.`product_id`=ast.`product_id` where prd.legal_entity_id = '".$legalid."'
                                            GROUP BY prd.product_id
                                        ) AS innertbl
                                ) AS innertbl2";

        $assetData = DB::select(DB::raw($assetsTotalCost));   

              return $assetData;
        

}


    public function updateAssetInformationData($updatedata,$assetid){
        $update = DB::table('assets')
                    ->where('asset_id', '=', $assetid )
                    ->update(['company_asset_code'      =>  $updatedata['update_company_asset_code'], 
                            'purchase_date'             =>  $updatedata['update_purchase_date'],					
                            //'invoice_number'            =>  $updatedata['update_invoice_number'], 
                            'serial_number'             =>  $updatedata['update_serial_no'],
                            'warranty_status'           =>  $updatedata['update_warranty'],
                            'warranty_end_date'         =>  $updatedata['update_warranty_amc_date'],
                            'is_working'                =>  $updatedata['update_is_working'],
                            'notes'                     =>  $updatedata['update_notes'],
                            'business_unit'             =>  isset($updatedata['update_business_unit']) ? $updatedata['update_business_unit'] : '',
                            'warranty_month'            =>  isset($updatedata['month']) ? $updatedata['month'] : 0,
                            'warranty_year'             =>  isset($updatedata['year']) ? $updatedata['year'] : '',
                            'depresiation_date'         =>  isset($updatedata['depresiation_date']) ? $updatedata['depresiation_date'] : '' ,
                            'depresiation_per_month'    =>  isset($updatedata['depression_amount']) ? $updatedata['depression_amount'] : 0,
                            'depresiation_month'        =>  isset($updatedata['depresiation_age']) ? $updatedata['depresiation_age'] : 0
                            ]);
        return $update;
    }

    public function UpdateAssetCategoryInProducts($productId,$assetCategoryId){
        $updateProducts = DB::table('products')
                ->where('product_id','=',$productId)
                ->update(['asset_category' => $assetCategoryId]);
        $updateAssets  = DB::table('assets')
                ->where('product_id','=',$productId)
                ->update(['asset_category' => $assetCategoryId]);
        
        return 1;

    }

    public function updateAllocateid($allocatedid,$assetid,$assetstatus,$allocationdata){

        $update = DB::table('assets')
                    ->where('asset_id', '=', $assetid )
                    ->update(['allocated_to_id'                =>      $allocatedid,
                                'asset_status'              =>      $assetstatus,
                                'allocated_to_name'         =>      $allocationdata['allocate_to'],
                                'asset_allocated_date'      =>      isset($allocationdata['allocation_date']) ? $allocationdata['allocation_date'] : '',
                            ]);
        return $update;

    }

    public function getDetailsFromAssetsTable($id){
        $sqlData = DB::table("products as prd")
                        ->leftjoin("assets as ast","prd.product_id", "=", "ast.product_id")
                        ->where("ast.asset_id","=",$id)
                        ->get()->all();
         return $sqlData;
    }

    public function getNamesFromUsersTable(){
        $names = DB::table("users as usr")
                    ->where("usr.legal_entity_id","=",2)
                    ->get()->all();
        return $names;
    }

    public function getBusinessData(){
        $businessData =DB::table("business_units as bu")->get()->all();
        return $businessData;
    }

    public function getAssetClassification(){
        $assetclassification=DB::table("master_lookup")
                                ->where("mas_cat_id","=",132)
                                ->get()->all();

        return $assetclassification;
    }

    public function getMainTableAssetId($updateid){
        $sqlData = DB::table("assets")
                        ->where("product_id","=",$updateid)
                        ->get()->all();
        return $sqlData[0]->asset_id;
    }

    // As per Satish's comment we have hardcoded the legal_entity
    public function getUserDetails($term){
        
        $getlist = "select * FROM
                    (
                    SELECT user_id, firstname, lastname, legal_entity_id
                    FROM users
                    UNION
                    SELECT bu_id AS 'user_id', bu_name AS 'firstname', CONVERT(cost_center USING utf8) AS 'lastname', legal_entity_id
                    FROM business_units
                    ) AS innertbl WHERE legal_entity_id=2 AND CONCAT(firstname,lastname) LIKE '%".$term."%'";

        $allData = DB::select(DB::raw($getlist));
        $users_arr = array();

        foreach($allData  as $getnames) {
            $users = array("label" => $getnames->firstname. ' (' . $getnames->lastname . ')',"lastname" => $getnames->lastname,"user_id" => $getnames->user_id);
            array_push($users_arr, $users); 
        }
        return $users_arr;
    }

    public function getUserIdFromAssets($updatedata){
         $checkData = DB::table("assets")
                    ->where("product_id", "=", $updatedata['update_product_id'])
                    ->where("allocated_to","=",$updatedata['update_asset_user_id'])
                    ->count();
        return $checkData;
    }
    public function updateOnlyAllocatedId($allocatedid,$assetid,$isactive){
        $update = DB::table('assets')
                    ->where('asset_id', '=', $assetid )
                    ->update([
                        'allocated_to'      =>  $allocatedid, 
                        'isactive'          =>  $isactive
                            ]);
        return $update;
    }

    public function getAssetHistoryData($id){

        $query ="select ast.allocated_to_id, ast.serial_number, ast.company_asset_code,
                ast.warranty_status, 
                CASE ash.allocation_status
                    WHEN 0 THEN 'De-Allocated / Available'
                    WHEN 1 THEN 'Allocated'
                    WHEN 2 THEN 'Repair'
                END AS 'allocation_status',
                DATE_FORMAT(ash.allocation_date, '%Y-%m-%d') AS 'fromdate',
                ash.allocation_name,
                (SELECT pr.product_title FROM products AS pr WHERE pr.product_id=ast.product_id ) AS 'producttitle',
                (SELECT pr.primary_image FROM products AS pr WHERE pr.product_id=ast.product_id ) AS 'image'
                FROM assets AS ast
                INNER JOIN assets_history AS ash
                ON ast.asset_id=ash.asset_id
                where ast.asset_id=$id";
        $allData = DB::select(DB::raw($query));
        return $allData;
    }

    public function getManufactureDetails(){
        $getDetails = DB::table('legal_entities')
                            ->where('legal_entity_type_id', '=', '1006')
                            ->orderBy('business_legal_name', 'ASC');
        return $getDetails->get()->all();
    }

    public function getBrandsAsManufacId($manufac){
        $getBrand = DB::table('brands')
                            ->where('mfg_id', '=', $manufac)
                            ->where('is_active', '=', '1')
                            ->get()->all();
        return $getBrand;
    }

    public function getCategoryDetails(){
        $getDetails = DB::table('categories')
                            ->where('is_active', '=', '1')
                            ->orderBy('cat_name', 'ASC')
                            ->get()->all();
        return $getDetails;
    }
    public function getAssetCategoryDetails(){ 
        $getAssetCategoryDetails=DB::table('master_lookup')
                                ->where('mas_cat_id','=','153')
                                ->orderBy('master_lookup_name')
                                ->get()->all();

        return $getAssetCategoryDetails;
        
    }

    public function saveAssetProductIntoTable($product_data,$url,$sku){

        
        $insert=DB::table('products')->insert(
            ['brand_id' => $product_data['mdl_brand'],'category_id' => $product_data['mdl_category'],'mrp' => $product_data['prd_mrp'],
            'manufacturer_id'=>$product_data['mdl_manufac'],'product_title'=>$product_data['asset_name'],
            'primary_image'=>$url,'business_unit_id'=>$product_data['business_unit_asset'],'product_type_id'=>130001,
            'sku'=>$sku,'legal_entity_id'=>2,'asset_type'=>$product_data['asset_type'],'asset_category'=>$product_data['ast_category'],'created_by'=>Session::get('userId'),'updated_by' => Session::get('userId'), 'updated_at' => date('Y-m-d H:i:s') ]);

        $lastid = DB::getPdo()->lastInsertId($insert);

        return $lastid;

    }


    public function saveIntoPackCinfigTable($prdid){

        $insert=DB::table('product_pack_config')->insert(
            ['product_id' => $prdid,
            'level' => 16001,
            'no_of_eaches' =>  1 ,
            'pack_sku_code'=>0,
            'length'=>1,
            'breadth'=>1,
            'height'=>1,
            'weight'=>1,
            'is_cratable'=>1,
            'effective_date'=>date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s') ]);

    }

    public function saveQtyWiseProducts($qty_data){

        $codeFound = 0;
        $refNoArr = 0;
        do{
            // Get the Serial Number count from the Table
            
            $refNoArr = Utility::getReferenceCode('AST','TS');
            
            $codeFound = DB::table("assets")
                            ->where("company_asset_code", "=", $refNoArr)
                            ->count();

        }while ( $codeFound >= 1);

        $qty_data['company_asset_code']  =  $refNoArr;

        DB::table("assets")->insert($qty_data);
    }

    public function getProductId($id){

         $product_id = DB::table("assets as ast")
                    ->where("ast.asset_id", "=", $id)
                    ->first();
        return $product_id->product_id;
    }
    public function getInwardDetails($productid){
       $checkData = DB::table("products as prd")
                    ->join("inward_products as ip","prd.product_id","=","ip.product_id")
                    ->where("prd.product_id", "=", $productid)
                    ->count();
        return $checkData;
    }
    public function getAssetCategoryData(){
        $data="select master_lookup_name  from master_lookup where mas_cat_id=153";
        $asssetCategoryData= DB::select(DB::raw($data));
        return json_encode($asssetCategoryData);
    }

    public function getDataAsPerQueryForAsset($makeFinalSql){

        $countLoop = 0;
        $sqlWhrCls = '';
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }
        
            $sqlquery = "select *, (DepMonthLeft*depresiation_per_month) AS 'ProductValue'
                FROM
                (
                 SELECT 

                 ast.`company_asset_code`, ast.`business_unit`, ast.`allocated_to_name`,ast.serial_number,prd.manufacturer_id,prd.brand_id,prd.category_id,
                 ast.warranty_status,ast.purchase_date,ast.`asset_status`, CONCAT(prd.`product_title`, '-', prd.sku) AS 'PrdName',if(prd.asset_type=1,'y','n') as is_movable,

                 case ast.`asset_status`
                    when 0 then '<div style=\"color:blue; font-weight:bold;\">Available</div>'
                    when 1 then '<div style=\"color:#FF0000; font-weight:bold;\">Allocated</div>'
                    when 2 then '<div style=\"color:#00ff00; font-weight:bold;\">Repair</div>'
                     else 'Unknown' 
                    end as 'AstStatus',

                 (SELECT business_legal_name FROM legal_entities le WHERE le.`legal_entity_id`=prd.manufacturer_id) AS 'ManufacName',
                 (SELECT brand_name FROM brands br WHERE br.`brand_id`=prd.brand_id) AS 'BrandName',
                 (SELECT cat_name FROM categories cat WHERE cat.`category_id`=prd.category_id) AS 'categoryName',
                 (select m.master_lookup_name from master_lookup m where m.value=ast.asset_category and m.mas_cat_id=153 ) as 'assetCategory',

                 TIMESTAMPDIFF(MONTH, DATE(NOW()), DATE( ast.`depresiation_date` ) ) + 1  AS 'DepMonthLeft', 
                 ast.`depresiation_per_month`, ast.`depresiation_month`, ast.`depresiation_date`

                 FROM assets AS ast
                 INNER JOIN products AS prd on prd.`product_id`=ast.`product_id` ".$sqlWhrCls."
                ) AS innertbl" ;

                
        $assetData = DB::select(DB::raw($sqlquery));

        return json_encode($assetData);
    }

    public function getDataAsPerQueryForDepreciationCalculation($makeFinalSql){

        $countLoop = 0;
        $sqlWhrCls = '';
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' WHERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }

        $sqlquery = "select *,
                        CASE 
                        WHEN DaysLeft<=1 THEN depresiation_month
                        ELSE depresiation_month +1
                        END AS 'DepMonthLoop', round((ActualMRP * 5 / 100),2) AS 'AssetFlatDep'
                        FROM (
                            SELECT *, (365-DaysDiff) AS 'DaysLeft'
                            FROM (
                                SELECT ast.*, 
                                (SELECT product_title FROM products prd WHERE prd.`product_id`=ast.product_id) AS 'ProductName',
                                (SELECT round(mrp,2) FROM products prd WHERE prd.`product_id`=ast.product_id) AS 'ActualMRP',
                                IF( DATE_FORMAT(ast.purchase_date, '%m')>=4 , 
                                TIMESTAMPDIFF(DAY, ast.purchase_date, CONCAT(DATE_FORMAT(ast.purchase_date, '%Y')+1,'-03-31')) + 1,
                                TIMESTAMPDIFF(DAY, ast.purchase_date, CONCAT(DATE_FORMAT(ast.purchase_date, '%Y'),'-03-31')) ) AS 'DaysDiff', 
                                IF( DATE_FORMAT(ast.purchase_date, '%m')>=4,
                                DATE_FORMAT(ast.purchase_date, '%Y'),
                                DATE_FORMAT(ast.purchase_date, '%Y')-1) AS 'PurchaseYear', prd.category_id
                                FROM assets AS ast 
                                INNER JOIN products as prd on prd.product_id=ast.product_id
                                WHERE ast.depresiation_month IS NOT NULL
                            ) AS innertbl ".$sqlWhrCls." 
                        ) AS innertbl2";

        $calculateData = DB::select(DB::raw($sqlquery));
        return json_encode($calculateData);

    }

}