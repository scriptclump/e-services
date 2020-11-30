<?php
//defining namespace
namespace App\Modules\Assets\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Assets\Models\assetsImplodeModel;
use DB;
use App\Central\Repositories\productRepo;
use Session;
use UserActivity;

class assetsImplodeModel extends Model
{
    protected $table = 'assets';
    protected $primaryKey = "asset_id";


    public function getTheIdWithManufactureName($manuFactName){
        $getDetails = DB::table('legal_entities')
                            ->where('business_legal_name', '=', $manuFactName)
                            ->get()->all();

        if( !empty($getDetails) ){
            return  $getDetails[0]->legal_entity_id;
        }else{
            return "0";
        }
    }

    public function getTheIdWithBrandName($brandName){
        $getDetails = DB::table('brands')
                            ->where('brand_name', '=', $brandName)
                            ->get()->all();

        if( !empty($getDetails) ){
            return $getDetails[0]->brand_id;
        }else{
            return "0";
        }
    }

    public function getTheIdWithCategoryName($catName){
        $getDetails = DB::table('categories')
                            ->where('cat_name', '=', $catName)
                            ->get()->all();

        if( !empty($getDetails) ){
            return  $getDetails[0]->category_id;
        }else{
            return "0";
        }
    }
    public function getTheIdWithBusinessName($BusName){
        $getDetails = DB::table('business_units')
                            ->where('bu_name', '=', $BusName)
                            ->orWhere('cost_center','=',$BusName)
                            ->get()->all();

        if( !empty($getDetails) ){
            return  $getDetails[0]->bu_id;
        }else{
            return "0";
        }
    }

    public function saveIntoProductsTable($import_asset_data){

        $save=DB::table('products')->insert($import_asset_data);

        $lastid = DB::getPdo()->lastInsertId($save);

        
        return $lastid;

    }

    public function checkProductNameInDB($prodName){

        $getDetails = DB::table('products')
                            ->where('product_title', '=', $prodName)
                            ->where('product_type_id','=', "130001")
                            ->count();

        return $getDetails;

    }
    public function getIdByCategoryName($name){
        $getId = DB::table('master_lookup') 
            ->where('master_lookup_name','=',$name)
            ->where('mas_cat_id','=',"153")
            ->get()->all();
        if(!empty($getId)){
            return $getId[0]->value;
        }
        else{
            return 0;
        }


    }

}