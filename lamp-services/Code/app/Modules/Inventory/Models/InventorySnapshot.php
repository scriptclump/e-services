<?php

namespace App\Modules\Inventory\Models;

use DB;
use Log;
use Illuminate\Database\Eloquent\Model;

class InventorySnapshot extends Model {
    
    protected $table        = "inventory_snapshot";
    public function generateReport($from_date,$to_date,$product_id='NULL',$dc_id,$userId) {
        $query = "CALL getInvSnapshot('".$from_date."','".$to_date."',".$product_id.",".$dc_id.",".$userId.")";
        $file_name = 'Inventory_Snapshot'.date('d-m-Y-H-i-s').'.csv';
        $filePath = public_path().'/uploads/reports/'.$file_name;
        $this->exportToExcel($query,$file_name,$filePath);
    }
    public function opencloseReport($dc_id,$from_date,$to_date) {
        $query = "CALL getInventorySnapshotByMTD(".$dc_id.",'".$from_date."','".$to_date."')";
        $file_name = 'Inventory_OpeningClosing_Report'.date('d-m-Y-H-i-s').'.csv';
        $filePath = public_path().'/uploads/reports/'.$file_name;
        $this->exportToExcel($query,$file_name,$filePath);
    }

     public function exportToExcel($query,$filename,$filePath){
                $host = env('DB_HOST');
                $port = env('DB_PORT');
                $dbname = env('DB_DATABASE');
                $uname = env('DB_USERNAME');
                $pwd = env('DB_PASSWORD');
                $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
                $sqlCommit = 'COMMIT';
                $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;
                system($exportCommand);        
                header("Content-Type: application/force-download");
                header("Content-Disposition:  attachment; filename=\"" . $filename . "\";" );
                header("Content-Transfer-Encoding:  binary");
                header("Accept-Ranges: bytes");
                header('Content-Length: ' . filesize($filePath));        
                $readFile = file($filePath);
                foreach($readFile as $val){
                    echo $val;
                    
            }           
    }

    public function generateCycleCountReport($fromDate,$toDate,$dc_id){
  
     $query = DB::selectFromWriteConnection(DB::raw("CALL getCycleCountReport('".$fromDate."','".$toDate."',".$dc_id.")")); 

     return $query;
     
    }   

    public function productFilterOption()
    {
    	$options = DB::select(DB::raw('SELECT DISTINCT product_id, product_title FROM vw_inventory_report'));

    	return $options;
    }
    public function invWriteoffData($from_date,$to_date,$wh_id)
    {
        $dataRs = DB::table('inventory_snapshot as ins')
                    ->join('products as pros','pros.product_id','=','ins.product_id')
                    ->select('ins.product_id','product_title','pros.mrp','pros.sku','esp','elp','dit_qty','dnd_qty','ins.created_at')
                    ->where('le_wh_id',$wh_id)
                    //->wherein('ins.product_id',[1,2,3,4])
                    ->wherebetween('ins.created_at',[$from_date,$to_date])
                    ->get()->all();
        return json_decode(json_encode($dataRs), true);
    }
    public function getWhList()
    {
        $rs = DB::table('legalentity_warehouses')->where('lp_wh_name', '!=', NULL)->where('lp_wh_name', '!=', '')->where('dc_type', '=', '118001')->orderBy('lp_wh_name', 'asc')->select('lp_wh_name', 'le_wh_id')->get()->all();
        return json_decode(json_encode($rs),true);
    }
}