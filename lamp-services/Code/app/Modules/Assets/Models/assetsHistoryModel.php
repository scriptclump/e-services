<?php
//defining namespace
namespace App\Modules\Assets\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Assets\Models\assetsHistoryModel;
use DB;
use Session;
use UserActivity;

class assetsHistoryModel extends Model
{
    protected $table = 'assets_history';
    protected $primaryKey = "assets_history_id";

    public function saveIntoAssetsHistoryTable($assetdata,$getId){

        $this->asset_id = $getId;
        $this->user_id = Session::get('userId');
        $this->product_id = $assetdata['hidden_product_id'];
        $this->fromdate = date('Y/m/d H:i:s');
        $this->isactive = $assetdata['is_working'];
        $this->created_at=Session::get('userId');

        $this->save();
    }


    public function updateAssetInformationHistoryTable($allocationdata){
        $update = DB::table('assets_history')
                    ->where('asset_id', '=', $allocationdata['hidden_asset_id'] )
                    ->update(['fromdate'      => date('Y/m/d H:i:s'),
                            ]);
        return $update;
    }


    public function countWithAssetHistoryData($assetid){

         $checkData = DB::table("assets_history")
                    ->where("asset_id", "=", $assetid)
                    ->count();

        return $checkData;

    }

    public function saveIntoHistoryTable($updatedata){

        $this->asset_id = $updatedata['hidden_asset_id'];
        $this->user_id=$updatedata['asset_user_id'];
        $this->product_id = $updatedata['hidden_product_id'];
        $this->allocation_name=$updatedata['allocate_to'];
        $this->allocation_date=$updatedata['allocation_date'];
        $this->fromdate = $updatedata['allocation_date'];
        $this->allocation_status=$updatedata['select_part'];
        $this->comment=$updatedata['allocation_comment'];
        $this->created_by=Session::get('userId');
        $this->created_at=date('Y-m-d');
        $this->save();

        return 1;
    }

   

}