<?php

namespace App\Modules\Inventory\Models;

use DB;
use Log;
use Illuminate\Database\Eloquent\Model;

class InventorySummary extends Model {
    
    public function generateReport($from_date,$to_date,$product_id=NULL,$dc_id) {
     
        $fieldArr = array('inventory_summary.*');
        $condition = "=";
        $product_ids = "NULL";
        if(!empty($product_id)){
            $product_ids = rtrim(implode(',', $product_id), ',');
            $product_ids = "'".$product_ids."'";

        }

        $query = "CALL getInventorySummaryByDC(".$product_ids.",'".$dc_id."','".$from_date."','".$to_date."',1)";
        $file_name = 'Inventory_Summary_Report'.date('d-m-Y-H-i-s').'.csv';
        $filePath = public_path().'/uploads/reports/'.$file_name;
        //$this->exportToExcel($query,$file_name,$filePath);
        $this->exportToCsv($query, $file_name);
 

        // $fieldArr = array('inventory_summary.*');
        // $condition = "=";
        // $product_ids = "NULL";
        // if(!empty($product_id)){
        //     $product_ids = rtrim(implode(',', $product_id), ',');
        //     $product_ids = "'".$product_ids."'";

        // }
        // $productData = DB::selectFromWriteConnection(DB::raw("CALL getInventorySummaryByDC(".$product_ids.",'".$dc_id."','".$from_date."','".$to_date."',1)"));

        // return $productData;
    }

    public function productTitleSku(){
        $options = DB::select(DB::raw('SELECT DISTINCT product_id, CONCAT(product_title," (",sku,")") AS product_title FROM vw_inventory_report'));

        return $options;
    }

        public function exportToCsv($query, $filename) {
        $host = env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $uname = env('DB_USERNAME');
        $pwd = env('DB_PASSWORD');
        $filePath = public_path().'/uploads/reports/'.$filename;
        //echo $filePath;die;
        $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
        $sqlCommit = 'COMMIT';
        $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;
        //echo '<pre>'. $exportCommand;die;
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
        exit;
    }
}