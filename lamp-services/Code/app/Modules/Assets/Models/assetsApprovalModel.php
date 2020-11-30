<?php
//defining namespace
namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Assets\Models\assetsApprovalModel;
use DB;
use Session;
use UserActivity;

class assetsApprovalModel extends Model
{
    protected $table = 'asset_approval_details';
    protected $primaryKey = "asset_approval_id";

    public function approvalProductsData($makeFinalSql, $orderBy, $page, $pageSize){

        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;

        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'WhERE ' . $value;
            }elseif( count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' AND ' .$value;
            }
            $countLoop++;
        }

        $concatQuery="";
           $concatQuery = ", CONCAT('<center><code>',
                            '<a href=\"javascript:void(0)\" onclick=\"updateApprove(',asset_approval_id,')\">
                            <i class=\"fa fa-pencil\"></i></a>&nbsp&nbsp
                            <a href=\"javascript:void(0)\" onclick=\"viewapproval(',asset_approval_id,')\">
                            <i class=\"fa fa-eye\"></i></a>&nbsp&nbsp    
                            </code></center>') 
           AS 'CustomAction'";
         
        
        
        $assetsDetails = "select * from (select prd.product_title,mlp.master_lookup_name,aad.asset_comment,date_format(aad.created_at,'%Y-%m-%d') as created_at,
                            (SELECT CONCAT( u.firstname, ' ', u.lastname ) 
                            FROM users AS u WHERE u.user_id=aad.asset_allocate_to) AS 'AllocatedName'
                            ".$concatQuery."
                            from asset_approval_details as aad
                            left join products as prd on prd.product_id=aad.asset_product_id
                            left join master_lookup as mlp on mlp.value=aad.asset_approval_status_id ) as innertbl
                            ".$sqlWhrCls.$orderBy;

        $assetData = DB::select(DB::raw($assetsDetails));
       
        return $assetData;
    }

    public function getApproveProduct(){

        $getDetails = DB::table('products')
                            ->where('product_type_id', '=', '130001')
                            ->get()->all();

        return $getDetails;
    }

    public function getInformationFromTable($id){

        $prodDetails = "select *,
                            (SELECT CONCAT( u.firstname, ' ', u.lastname ) 
                            FROM users AS u WHERE u.user_id=aad.asset_allocate_to) AS 'AllocatedName'
                            from asset_approval_details as aad
                            inner join products as prd on prd.product_id=aad.asset_product_id
                            left join master_lookup as mlp on mlp.value=aad.asset_approval_status_id
                            where aad.asset_approval_id='".$id."'";

        $assetData = DB::select(DB::raw($prodDetails));
       
        return $assetData;

    }

    public function getManufactureDetails(){
        $getDetails = DB::table('legal_entities')
                            ->where('legal_entity_type_id', '=', '1006');
        return $getDetails->get()->all();
    }

    public function getCategoryDetails(){
        $getDetails = DB::table('categories')
                            ->where('is_active', '=', '1')
                            ->get()->all();
        return $getDetails;
    }

    public function getNamesFromUsersTable(){

        $names = "select usr.user_id AS 'user_id', CONCAT(usr.firstname, ' ', usr.lastname ) AS 'firstname'
                FROM users AS usr where usr.legal_entity_id=2
                UNION
                SELECT bu.`bu_id` AS 'user_id', CONCAT(bu.`bu_name`, ' (', bu.cost_center, ')') COLLATE utf8_general_ci AS 'firstname'
                FROM business_units AS bu";

        $names = DB::select(DB::raw($names));
        return $names;
    }

    public function getBusinessData(){
        $businessData =DB::table("business_units as bu")->get()->all();
        return $businessData;
    }

    public function getBrandsAsManufacId($manufac){
        $getBrand = DB::table('brands')
                            ->where('mfg_id', '=', $manufac)
                            ->where('is_active', '=', '1')
                            ->get()->all();
        return $getBrand;
    }

    public function getProductIdByCategory($category, $brand){

        $category = $category=="" | $category=="0" | $category=="null" ? "" : " AND  category_id='" . $category . "'";
        $brand = $brand=="" | $brand=="0"  | $brand=="null" ? "" : " AND  brand_id='" . $brand . "'";


        $loadproduct = "select * from products where product_type_id='130001'" . $category . $brand;


        $assetData = DB::select(DB::raw($loadproduct));
       
        return $assetData;
    }

    public function getBrandsWiseCategoryName($brandid){

        $getDetails = DB::table('legal_entities')
                            ->where('legal_entity_type_id', '=', '1006');
        return $getDetails->get()->all();


    }

    public function saveApprovalWithProduct($approvalData){
        $this->asset_manfacture_id = isset($approvalData['mdl_manufac']) ? $approvalData['mdl_manufac'] : 0;
        $this->asset_brand_id = isset($approvalData['mdl_brand']) ? $approvalData['mdl_brand'] : 0;
        $this->asset_category_id = isset($approvalData['mdl_category']) ? $approvalData['mdl_category'] : 0;
        $this->asset_product_id = $approvalData['approve_product'];
        $this->asset_allocate_to = $approvalData['asset_allocate_to'];
        $this->asset_comment = $approvalData['appr_notes'];
        $this->created_by   =  Session::get('userId');

        if($this->save())
        {
            return $this->asset_approval_id;
        }else{
            return false;
        }
    }

    public function updateApprovalStatusToDB($flowTypeForID,$statusid){
        DB::table("asset_approval_details")
            ->where('asset_approval_id', '=', $flowTypeForID)
            ->update(['asset_approval_status_id' => $statusid]);

        return "Added Sucessfully";
    }

    public function updateApproveColoumnInTable($data){

        $next = $data['NextStatusID'];
        $explodevalue = explode(',', $next);

        DB::table("asset_approval_details")
            ->where('asset_approval_id', '=', $data['hidden_approval_id'])
            ->update(['asset_approval_status_id' => $explodevalue[0]]);

        return "Approved Sucessfully";

    }
}