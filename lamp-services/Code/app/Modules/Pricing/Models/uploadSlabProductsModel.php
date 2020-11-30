<?php
/*
FileName : uploadSlabProductsModel.php
Author   : eButor
Description : All the outbound order related functions are here.
CreatedDate :15/Aug/2016
*/
//defining namespace

namespace App\Modules\Pricing\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use UserActivity;
class uploadSlabProductsModel extends Model
{
    protected $table = 'product_prices_history';
    protected $primaryKey = "product_price_id";

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

    // get ref id for cashback table
    public function getcashback_ref_id($product_id){

        $product_price_id = "select  MAX(product_price_id)  AS product_price_id FROM product_prices_history WHERE product_id = '$product_id'";

        $product_price_id = DB::select(DB::raw($product_price_id));

        if( isset($product_price_id[0]->product_price_id) ){
            return $product_price_id[0]->product_price_id;
        }else{
            return 0;
        }
    }

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

    public function getCustomerType($customer_type){
        $customer_type = trim($customer_type);
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

     

    // get product stars
   

    public function getProductstarForExcel($productstars){
        $productstars = DB::table("master_lookup")
                    ->where('master_lookup_name', '=', $productstars)
                    ->where('mas_cat_id', "=", 140)
                    ->first();
        if(isset($productstars->value)){
            return $productstars->value;
        }else{
            return 0;
        }
    }

    public function getBenificiaryIdForExcel($benificiary){
        $benificiary = DB::table("roles")
                    ->where('name', '=', $benificiary)
                    ->where('is_active', '=', '1')
                    ->first();
        if(isset($benificiary->role_id)){
            return $benificiary->role_id;
        }else{
            return 0;
        }
    }


    // get warehouses
    public function getWarehousesforexcel($warehouse){
        $getWarehouses = DB::table('legalentity_warehouses')
                        ->where('lp_name', '=', 'Custom')
                        ->where('lp_wh_name','=',$warehouse)
                        ->first();
        if(isset($getWarehouses->le_wh_id)){
            return $getWarehouses->le_wh_id;
        }else{
            return 0;
        }
    }


    public function getBenificiaryName($benificiary){
        $benificiary = DB::table('roles')
                            ->where('is_active', '=', '1')
                            ->where('name','=',$benificiary)
                            ->first();
        if(isset($benificiary->role_id)){
            return $benificiary->role_id;
        }else{
            return 0;
        }
    }

    // Function to insert / update Uploaded product
    public function insertUploadProducts($slab_data, $is_delete_flag=0){

        $returnResult = array();

        try{
            // check for the delete part
            if($is_delete_flag==1){

                // Search for the Product price id for delete
                $product_id = DB::table("product_prices_history")
                            ->where('product_id', '=', $slab_data['product_id'])
                            ->where('state_id', '=', $slab_data['state_id'])
                            ->where('dc_id', '=', $slab_data['dc_id'])
                            ->where('customer_type', '=', $slab_data['customer_type'])
                            ->where('effective_date', '=', $slab_data['effective_date'])
                            ->where('price', '=', $slab_data['price'])
                            ->where('ptr', '=', $slab_data['ptr'])
                            ->first();                

                if(isset($product_id->product_price_id)){
                    // write a data into Mongo for log
                    // get OLD Data
                    $oldData = DB::table("product_prices_history")
                            ->where('product_price_id', '=', $product_id->product_price_id)
                            ->first();

                    // Making a variable for delete information
                    $oldDataForDelte = $oldData;

                    //==============================================
                    // delete the data
                    //==============================================
                    DB::table('product_prices_history')->where('product_price_id', '=', $product_id->product_price_id )->delete();
                    $returnResult['message'] = "Price Deleted Successfully";

                    // Check the Deleted Data is more that Today ( We are not deleting the data if it is of a future date )
                    if( $oldDataForDelte->effective_date <= date('Y-m-d') ){

                        $getPriceUniqueData = DB::table("product_prices")
                            ->where('product_id', '=', $oldDataForDelte->product_id)
                            ->where('state_id', '=', $oldDataForDelte->state_id)
                            ->where('dc_id', '=', $oldDataForDelte->dc_id)
                            ->where('customer_type', '=', $oldDataForDelte->customer_type)
                            ->where('effective_date', '=', $oldDataForDelte->effective_date)
                            ->first();

                        // We are deleting the record if user wants to delte the current record
                        if( count($getPriceUniqueData)>0 ){
                            // Delete data from the Unique table
                            $deleteSlabData = DB::table('product_prices')
                                        ->where('product_price_id', '=', $getPriceUniqueData->product_price_id)
                                        ->delete();

                            // Insert back the Next available Max date from History Table
                            $insertQuery = "
                            INSERT INTO product_prices
                            SELECT DISTINCT '0' AS 'product_price_id', pr.`product_id`, pr.`state_id`, pr.`customer_type`, pr.`price`, 
                            pr.`ptr`, pr.`legal_entity_id`, pr.`effective_date`, pr.`status`, pr.`is_markup`,pr.product_price_id as 'history_reff_id',pr.`fc_sku_margin`,pr.`dc_sku_margin`,pr.`fc_margin_type`,pr.`dc_margin_type`,pr.`created_by`, pr.`created_at`, pr.`updated_by`, pr.`updated_at`
                            FROM product_prices_history AS pr
                            INNER JOIN (
                                SELECT product_id, MAX(effective_date) AS MaxDate
                                FROM product_prices_history
                                where product_id in (".$getPriceUniqueData->product_id.")
                                and state_id=".$getPriceUniqueData->state_id."
                                and dc_id=".$getPriceUniqueData->dc_id."
                                and customer_type=".$getPriceUniqueData->customer_type."
                                GROUP BY product_id
                            ) AS innertbl ON innertbl.product_id=pr.`product_id`
                            AND innertbl.MaxDate=pr.`effective_date`";
                            $resp = DB::insert($insertQuery);
                            // updating flat tabel as per requirement
                            // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$getPriceUniqueData->product_id.")"));
                        }
                    }

                        
                    // Prepare the data for Mongo    
                    $oldData = array(
                            'OLDVALUES'         =>  json_decode(json_encode($oldData)),
                            'NEWVALUES'         =>  'Deleted Data'
                        );
                    // Write in Mongo table
                    UserActivity::userActivityLog('Pricing', $oldData, 'Price Deleted from Excel Upload');

                    $returnResult['counter_flag'] = "1";
                    $returnResult['old_new_data'] = $oldData;
                    
                }else{
                    $returnResult['message'] = "Price can't be deleted";
                    $returnResult['counter_flag'] = "2";
                    $returnResult['old_new_data'] = array();
                }
            }else{

                // Search for the Product price Data for Insert / Update
                $product_id = DB::table("product_prices_history")
                            ->where('product_id', '=', $slab_data['product_id'])
                            ->where('state_id', '=', $slab_data['state_id'])
                            ->where('dc_id', '=', $slab_data['dc_id'])
                            ->where('customer_type', '=', $slab_data['customer_type'])
                            ->where('effective_date', '=', $slab_data['effective_date'])
                            ->first();


                $created_by = $slab_data['created_by'];
                $created_at = date("Y-m-d");
                // check given state and dc is in same state
                $checkStateAndDc = $this->checkStateAndDc($slab_data['state_id'],$slab_data['dc_id']);
                if($checkStateAndDc == 0){
                    $returnResult['message'] = "DC Should be in same state.";
                    $returnResult['counter_flag'] = "5";
                    $returnResult['old_new_data'] = array();
                    return $returnResult;
                }
                if(isset($product_id->product_price_id)){

                    // Update the data into Price Table
                    DB::table('product_prices_history')
                    ->where('product_price_id', '=', $product_id->product_price_id )
                    ->update(['price' => $slab_data['price'], 'ptr' => $slab_data['ptr'], 'effective_date' => $slab_data['effective_date'], 'updated_by' => $created_by, 'updated_at' => $created_at,'fc_sku_margin' => $slab_data['fc_sku_margin'], 'dc_sku_margin' => $slab_data['dc_sku_margin'],'fc_margin_type' => $slab_data['fc_margin_type'], 'dc_margin_type' => $slab_data['dc_margin_type']]);


                    // Check in the Pricing Unique Table ( Naresh Last requirement 07-02-2017 to keep the unique data in the main table )
                    $getPriceUniqueData = DB::table("product_prices")
                                            ->where('product_id', '=', $slab_data['product_id'])
                                            ->where('state_id', '=', $slab_data['state_id'])
                                            ->where('dc_id', '=', $slab_data['dc_id'])
                                            ->where('customer_type', '=', $slab_data['customer_type'])
                                            ->first();
                    
                    $update_flag=0;
                    if( $getPriceUniqueData ){

                        // if the Record exist in the main table and effective date is lesser than the new date, then update
                        if( $getPriceUniqueData->effective_date <= $slab_data['effective_date'] ){

                            // also checking for the current date
                            if( $slab_data['effective_date'] <= date('Y-m-d') ){

                                DB::table('product_prices')
                                ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
                                ->update([ 'price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $slab_data['effective_date'],'history_reff_id' => $product_id->product_price_id, 
                                    'updated_by' => $created_by, 'updated_at' => $created_at, 'fc_sku_margin' => $slab_data['fc_sku_margin'], 'dc_sku_margin' => $slab_data['dc_sku_margin'], 'fc_margin_type' => $slab_data['fc_margin_type'], 'dc_margin_type' => $slab_data['dc_margin_type']]);
                                // updating flat tabel as per requirement
                                // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slabdata['product_id'].")"));
                                $update_flag=1;

                            }
                        }   
                    }else{
                        // inserting
                        $slab_data['history_reff_id'] = $product_id->product_price_id;
                        DB::table("product_prices")->insert($slab_data);
                        $update_flag = 1;
                        // updating flat tabel as per requirement
                        // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slabdata['product_id'].")"));
                    }

                    //========================================================
                    // Prepare the Data for User Log
                    //========================================================
                    $oldData = array(
                        'OLDVALUES'         =>  json_decode(json_encode($product_id)),
                        'NEWVALUES'         =>  $slab_data,
                    );
                    if($update_flag == 0){
                       $oldData['NEWVALUES'] = array(); 
                    }
                    // Making an entry into Mongo Database
                    UserActivity::userActivityLog('Pricing', $oldData, 'Price Updated from Excel Import');

                    $oldPrice   = isset($oldData['OLDVALUES']->price) ? $oldData['OLDVALUES']->price : 'No Value';
                    $oldPTR     = isset($oldData['OLDVALUES']->ptr) ? $oldData['OLDVALUES']->ptr : 'No Value';

                    $newPrice   = isset($oldData['NEWVALUES']['price']) ? $oldData['NEWVALUES']['price'] : 'No Value';
                    $newPTR     = isset($oldData['NEWVALUES']['ptr']) ? $oldData['NEWVALUES']['ptr'] : 'No Value';

                    $returnResult['message'] = "Price Updated Successfully! Details : OldPrice-".$oldPrice.", OldPRT-".$oldPTR.", NewPrice-".$newPrice.", NewPTR-".$newPTR;
                    $returnResult['counter_flag'] = "3";
                    $returnResult['old_new_data'] = $oldData;

                }else{
                    // insert data into Price Table
                    $insert = $this->insert($slab_data);
                    $lastid = DB::getPdo()->lastInsertId($insert);
                    // inserting

                    // Check in the Pricing Unique Table ( Naresh Lst requirement 07-02-2017 to keep the unique data in the main table )
                    $getPriceUniqueData = DB::table("product_prices")
                                        ->where('product_id', '=', $slab_data['product_id'])
                                        ->where('state_id', '=', $slab_data['state_id'])
                                        ->where('dc_id', '=', $slab_data['dc_id'])
                                        ->where('customer_type', '=', $slab_data['customer_type'])
                                        ->first();
                    $update_flag = 0;
                    if( $getPriceUniqueData ){

                        // if the Record exist in the main table and effective date is lesser than the new date, then update
                        if( $getPriceUniqueData->effective_date <= $slab_data['effective_date'] ){

                            // also checking for the current date
                            if( $slab_data['effective_date'] <= date('Y-m-d') ){
                                //updating
                                DB::table('product_prices')
                                ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
                                ->update(['price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $slab_data['effective_date'],'history_reff_id'=>$lastid, 
                                'updated_by' => $created_by, 'updated_at' => $created_at, 'fc_sku_margin' => $slab_data['fc_sku_margin'], 'dc_sku_margin' => $slab_data['dc_sku_margin'], 'fc_margin_type' => $slab_data['fc_margin_type'], 'dc_margin_type' => $slab_data['dc_margin_type']]);
                                $update_flag = 1;
                                // updating flat tabel as per requirement
                                // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$getPriceUniqueData->product_id.")"));
                            }
                        }
                    
                    }else{
                        // inserting
                        $slab_data['history_reff_id'] = $lastid;
                        DB::table("product_prices")->insert($slab_data);
                        $update_flag = 1;
                        // // updating flat tabel as per requirement
                        // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slab_data['product_id'].")"));
                    }

                    // Write the data in Mongo
                    UserActivity::userActivityLog('Pricing', $slab_data, 'Price added from Excel Import');

                    $returnResult['message'] = "Price Inserted Successfully";
                    $returnResult['counter_flag'] = "4";
                    $returnResult['old_new_data'] = array( 
                                                    'NEWVALUES'         =>  $slab_data
                                                    );
                    if($update_flag == 0){
                       $oldData['NEWVALUES'] = array(); 
                    }
                    if(isset($getPriceUniqueData)){
                        $returnResult['old_new_data']['OLDVALUES'] = json_decode(json_encode($getPriceUniqueData));
                    }
                }
            }
        }catch(\ErrorException $ex){
            $returnResult['message'] = "Error occures, please check with system admin.";
            $returnResult['counter_flag'] = "5";
            $returnResult['old_new_data'] = array();
        }

        return $returnResult;
    }

    /**
     * Update the product ESP 
     * @param  array  $slab_data       Data required to match the product ID
     * @param  integer $is_delete_flag Flag to delete the data
     * @return [type]                  [description]
     */
    public function insertUploadProductsESP($slab_data, $is_delete_flag=0){

        $returnResult = array();

        try{
            // check for the delete part
            if($is_delete_flag==1){

                // Search for the Product price id for delete
                $product_id = DB::table("product_prices_history")
                            ->where('product_id', '=', $slab_data['product_id'])
                            ->where('state_id', '=', $slab_data['state_id'])
                            ->where('dc_id', '=', $slab_data['dc_id'])
                            ->where('customer_type', '=', $slab_data['customer_type'])
                            ->where('effective_date', '=', $slab_data['effective_date'])
                            ->where('price', '=', $slab_data['price'])
                            ->where('ptr', '=', $slab_data['ptr'])
                            ->first();                

                if(isset($product_id->product_price_id)){
                    // write a data into Mongo for log
                    // get OLD Data
                    $oldData = DB::table("product_prices_history")
                            ->where('product_price_id', '=', $product_id->product_price_id)
                            ->first();

                    // Making a variable for delete information
                    $oldDataForDelte = $oldData;

                    //==============================================
                    // delete the data
                    //==============================================
                    DB::table('product_prices_history')->where('product_price_id', '=', $product_id->product_price_id )->delete();
                    $returnResult['message'] = "Price Deleted Successfully";

                    // Check the Deleted Data is more that Today ( We are not deleting the data if it is of a future date )
                    if( $oldDataForDelte->effective_date <= date('Y-m-d') ){

                        $getPriceUniqueData = DB::table("product_prices")
                            ->where('product_id', '=', $oldDataForDelte->product_id)
                            ->where('state_id', '=', $oldDataForDelte->state_id)
                            ->where('dc_id', '=', $oldDataForDelte->dc_id)
                            ->where('customer_type', '=', $oldDataForDelte->customer_type)
                            ->where('effective_date', '=', $oldDataForDelte->effective_date)
                            ->first();

                        // We are deleting the record if user wants to delte the current record
                        if( count($getPriceUniqueData)>0 ){
                            // Delete data from the Unique table
                            $deleteSlabData = DB::table('product_prices')
                                        ->where('product_price_id', '=', $getPriceUniqueData->product_price_id)
                                        ->delete();

                            // Insert back the Next available Max date from History Table
                            $insertQuery = "
                            INSERT INTO product_prices
                            SELECT DISTINCT '0' AS 'product_price_id', pr.`product_id`, pr.`state_id`, pr.`customer_type`, pr.`price`, 
                            pr.`ptr`, pr.`legal_entity_id`, pr.`effective_date`, pr.`status`, pr.`is_markup`,pr.`product_price_id` as 'history_reff_id',pr.`fc_sku_margin`,pr.`dc_sku_margin`,pr.`fc_margin_type`,pr.`dc_margin_type`,pr.`created_by`, pr.`created_at`, pr.`updated_by`, pr.`updated_at`
                            FROM product_prices_history AS pr
                            INNER JOIN (
                                SELECT product_id, MAX(effective_date) AS MaxDate
                                FROM product_prices_history
                                where product_id in (".$getPriceUniqueData->product_id.")
                                and state_id=".$getPriceUniqueData->state_id."
                                and dc_id=".$getPriceUniqueData->dc_id."
                                and customer_type=".$getPriceUniqueData->customer_type."
                                GROUP BY product_id
                            ) AS innertbl ON innertbl.product_id=pr.`product_id`
                            AND innertbl.MaxDate=pr.`effective_date`";
                            $resp = DB::insert($insertQuery);
                            // updating flat tabel as per requirement
                            // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$getPriceUniqueData->product_id.")"));
                        }
                    }

                        
                    // Prepare the data for Mongo    
                    $oldData = array(
                            'OLDVALUES'         =>  json_decode(json_encode($oldData)),
                            'NEWVALUES'         =>  'Deleted Data'
                        );
                    // Write in Mongo table
                    UserActivity::userActivityLog('Pricing', $oldData, 'Price(ESP) Deleted from Excel Upload');

                    $returnResult['counter_flag'] = "1";
                    $returnResult['old_new_data'] = $oldData;
                    
                }else{
                    $returnResult['message'] = "Price can't be deleted";
                    $returnResult['counter_flag'] = "2";
                    $returnResult['old_new_data'] = array();
                }
            }else{

                // Search for the Product price Data for Insert / Update
                $product_id = DB::table("product_prices_history")
                            ->where('product_id', '=', $slab_data['product_id'])
                            ->where('state_id', '=', $slab_data['state_id'])
                            ->where('dc_id', '=', $slab_data['dc_id'])
                            ->where('customer_type', '=', $slab_data['customer_type'])
                            ->where('effective_date', '=', $slab_data['effective_date'])
                            ->first();


                $created_by = $slab_data['created_by'];
                $created_at = date("Y-m-d");
                // check given state and dc is in same state
                $checkStateAndDc = $this->checkStateAndDc($slab_data['state_id'],$slab_data['dc_id']);
                if($checkStateAndDc == 0){
                    $returnResult['message'] = "DC Should be in same state.";
                    $returnResult['counter_flag'] = "5";
                    $returnResult['old_new_data'] = array();
                    return $returnResult;
                }
                if(isset($product_id->product_price_id)){

                    // Update the data into Price Table
                    DB::table('product_prices_history')
                    ->where('product_price_id', '=', $product_id->product_price_id )
                    ->update(['price' => $slab_data['price'], 'ptr' => $slab_data['ptr'], 'effective_date' => $slab_data['effective_date'], 'updated_by' => $created_by, 'updated_at' => $created_at]);


                    // Check in the Pricing Unique Table ( Naresh Last requirement 07-02-2017 to keep the unique data in the main table )
                    $getPriceUniqueData = DB::table("product_prices")
                                            ->where('product_id', '=', $slab_data['product_id'])
                                            ->where('state_id', '=', $slab_data['state_id'])
                                            ->where('dc_id', '=', $slab_data['dc_id'])
                                            ->where('customer_type', '=', $slab_data['customer_type'])
                                            ->first();
                    
                    $update_flag=0;
                    if( $getPriceUniqueData ){

                        // if the Record exist in the main table and effective date is lesser than the new date, then update
                        if( $getPriceUniqueData->effective_date <= $slab_data['effective_date'] ){

                            // also checking for the current date
                            if( $slab_data['effective_date'] <= date('Y-m-d') ){

                                DB::table('product_prices')
                                ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
                                ->update([ 'price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $slab_data['effective_date'],'history_reff_id' => $product_id->product_price_id, 
                                    'updated_by' => $created_by, 'updated_at' => $created_at]);
                                // updating flat tabel as per requirement
                                // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slabdata['product_id'].")"));
                                $update_flag=1;

                            }
                        }   
                    }else{
                        // inserting
                        $slab_data['history_reff_id'] = $product_id->product_price_id;
                        DB::table("product_prices")->insert($slab_data);
                        $update_flag = 1;
                        // updating flat tabel as per requirement
                        // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slabdata['product_id'].")"));
                    }

                    //========================================================
                    // Prepare the Data for User Log
                    //========================================================
                    $oldData = array(
                        'OLDVALUES'         =>  json_decode(json_encode($product_id)),
                        'NEWVALUES'         =>  $slab_data,
                    );
                    if($update_flag == 0){
                       $oldData['NEWVALUES'] = array(); 
                    }
                    // Making an entry into Mongo Database
                    UserActivity::userActivityLog('Pricing', $oldData, 'Price(ESP) Updated from Excel Import');

                    $oldPrice   = isset($oldData['OLDVALUES']->price) ? $oldData['OLDVALUES']->price : 'No Value';
                    $oldPTR     = isset($oldData['OLDVALUES']->ptr) ? $oldData['OLDVALUES']->ptr : 'No Value';

                    $newPrice   = isset($oldData['NEWVALUES']['price']) ? $oldData['NEWVALUES']['price'] : 'No Value';
                    $newPTR     = isset($oldData['NEWVALUES']['ptr']) ? $oldData['NEWVALUES']['ptr'] : 'No Value';

                    $returnResult['message'] = "Price(ESP) Updated Successfully! Details : OldPrice-".$oldPrice.", OldPRT-".$oldPTR.", NewPrice-".$newPrice.", NewPTR-".$newPTR;
                    $returnResult['counter_flag'] = "3";
                    $returnResult['old_new_data'] = $oldData;

                }else{
                    // insert data into Price Table
                    $insert = $this->insert($slab_data);
                    $lastid = DB::getPdo()->lastInsertId($insert);
                    // inserting

                    // Check in the Pricing Unique Table ( Naresh Lst requirement 07-02-2017 to keep the unique data in the main table )
                    $getPriceUniqueData = DB::table("product_prices")
                                        ->where('product_id', '=', $slab_data['product_id'])
                                        ->where('state_id', '=', $slab_data['state_id'])
                                        ->where('dc_id', '=', $slab_data['dc_id'])
                                        ->where('customer_type', '=', $slab_data['customer_type'])
                                        ->first();
                    $update_flag = 0;
                    if( $getPriceUniqueData ){

                        // if the Record exist in the main table and effective date is lesser than the new date, then update
                        if( $getPriceUniqueData->effective_date <= $slab_data['effective_date'] ){

                            // also checking for the current date
                            if( $slab_data['effective_date'] <= date('Y-m-d') ){
                                //updating
                                DB::table('product_prices')
                                ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
                                ->update(['price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $slab_data['effective_date'],'history_reff_id'=>$lastid, 
                                'updated_by' => $created_by, 'updated_at' => $created_at]);
                                $update_flag = 1;
                                // updating flat tabel as per requirement
                                // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$getPriceUniqueData->product_id.")"));
                            }
                        }
                    
                    }else{
                        // inserting
                        $slab_data['history_reff_id'] = $lastid;
                        DB::table("product_prices")->insert($slab_data);
                        $update_flag = 1;
                        // // updating flat tabel as per requirement
                        // $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slab_data['product_id'].")"));
                    }

                    // Write the data in Mongo
                    UserActivity::userActivityLog('Pricing', $slab_data, 'Price(ESP) added from Excel Import');

                    $returnResult['message'] = "Price Inserted Successfully";
                    $returnResult['counter_flag'] = "4";
                    $returnResult['old_new_data'] = array( 
                                                    'NEWVALUES'         =>  $slab_data
                                                    );
                    if($update_flag == 0){
                       $oldData['NEWVALUES'] = array(); 
                    }
                    if(isset($getPriceUniqueData)){
                        $returnResult['old_new_data']['OLDVALUES'] = json_decode(json_encode($getPriceUniqueData));
                    }
                }
            }
        }catch(\ErrorException $ex){
            $returnResult['message'] = "Error occures, please check with system admin.";
            $returnResult['counter_flag'] = "5";
            $returnResult['old_new_data'] = array();
        }

        return $returnResult;
    }

    // Get Slab data for the template download
    public function getDataAsPerQuery($makeFinalSqlOuter, $makeFinalSqlInner){

        $sqlWhrClsOuter = '';
        $sqlWhrClsInner = '';
        $countLoop = 0;

        // remove code
        
        // make outer query
        foreach ($makeFinalSqlOuter as $value) {
            $sqlWhrClsOuter .= ' AND ' . $value;
            $countLoop++;
        }
        // make inner query
        $countLoop=0;
        foreach ($makeFinalSqlInner as $value) {
            $sqlWhrClsInner .= ' AND ' . $value;
            $countLoop++;
        }

        $sqlQuery ="select 
                      prd.`product_id`,
                      prd.product_title,
                      prd.mrp,
                      prd.sku,
                      prd.upc,
                      pp.customer_type,
                      pp.price,
                      pp.ptr,
                      date_format(pp.effective_date, '%m/%d/%Y') as 'effective_date',
                      prd.manufacturer_id,
                      prd.brand_id,
                      pp.state_id,
                      prd.category_id,
                      lw.dc_type,
                      lw.lp_wh_name,
                      getStateNameById (pp.state_id) AS 'StateName',
                      `getMastLookupValue` (pp.customer_type) AS 'CustomerType'
                    FROM
                      products AS prd 
                      LEFT JOIN product_prices_history AS pp 
                        ON  prd.product_id =pp.product_id
                    LEFT JOIN legalentity_warehouses as lw on lw.le_wh_id = pp.dc_id
                        ".$sqlWhrClsInner." 
                    WHERE prd.legal_entity_id = '".Session::get('legal_entity_id')."' " . $sqlWhrClsOuter;

        $allData = DB::select(DB::raw($sqlQuery));

        return json_encode($allData);
    }

    public function getAllState(){
        $sqlQuery = "select zn.name AS 'ItemName', '1' AS 'DataFlag' FROM zone AS zn WHERE zn.`country_id`=99";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }

    public function getAllCustomerType(){
        $sqlQuery = "select ml.master_lookup_name AS 'ItemName', '2' AS 'DataFlag' FROM master_lookup AS ml WHERE ml.`mas_cat_id`=3 AND is_active=1";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);
    }


    public function saveCashBackDataIntoTableExcel($cashbackdata){

            // Check for combination
            $returnFlag = "";
            $findData = DB::table("promotion_cashback_details")
                        ->where('product_id', '=', $cashbackdata['product_id'])
                        ->where('state_id', '=', $cashbackdata['state_id'])
                        ->where('wh_id', '=', $cashbackdata['wh_id'])
                        ->where('customer_type', '=', $cashbackdata['customer_type'])
                        ->where('benificiary_type', '=', $cashbackdata['benificiary_type'])
                        ->where('product_star', '=', $cashbackdata['product_star'])
                        ->where('end_date', '=', $cashbackdata['end_date'])
                        //->where('cbk_type', '=', $cashbackdata['cbk_type'])
                        //->where('cbk_value', '=', $cashbackdata['cbk_value'])
                        ->where('cbk_status', '=', 1)
                        ->where('cbk_source_type', '=', 2)
                        ->first();

            $findDataCount = count($findData);

            if($findDataCount >= 1){
                //Update existing cashback
                $cbk_id = isset($findData->cbk_id) ? $findData->cbk_id : '';
                $updatedCount = DB::table('promotion_cashback_details')
                ->where('cbk_id', '=', $cbk_id )
                ->update(['cbk_type' => $cashbackdata['cbk_type'],
                        'cbk_value' =>$cashbackdata['cbk_value']]);
                $returnFlag = 1;
            
            }else{
                // Insert into cashback table
                $data = DB::table("promotion_cashback_details")->insert($cashbackdata);
                //return DB::getPdo()->lastInsertId($data);
                $returnFlag = 2;


            }


            return $returnFlag;



    }
    
    public function refreshPriceData(){
        
        $flatTbaleUpdate = DB::statement(DB::raw("CALL getRefreshProducts(1)"));
        return 1;
    }

    public function getAllDCType(){

         $sqlQuery = "select * from legalentity_warehouses where dc_type=118001 and status=1";
        $allData = DB::select(DB::raw($sqlQuery));
        return json_encode($allData);

    }

    public function getdcID($dcname){
    
        $dc_type = trim($dcname);
        $dc_type = DB::table("legalentity_warehouses")
                    ->where('lp_wh_name', '=', $dc_type)
                    ->first();
        
        return $dc_type;       

    }

    public function getdcData($le_wh_id){
    
        $dc_data = DB::table("legalentity_warehouses")
                    ->where('le_wh_id', '=', $le_wh_id)
                    ->first();
        
        return $dc_data;       

    }

    public function getAllDCFCs($le_wh_id){
        $fc_data = DB::table("dc_fc_mapping")
                    ->select("fc_le_wh_id as le_wh_id","fc_le_id as legal_entity_id")
                    ->where('dc_le_wh_id', '=', $le_wh_id)
                    ->get()->all();
        return isset($fc_data[0]->le_wh_id) ? $fc_data : array();   
    }

    public function getAllDCByState($state_id,$all_dcs='yes',$all_fcs='yes',$is_apob='yes'){
        $state_data = DB::table("legalentity_warehouses")
                    ->select("le_wh_id","legalentity_warehouses.legal_entity_id")
                    ->leftjoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'legalentity_warehouses.legal_entity_id')
                    ->where('state', '=', $state_id)
                    ->where('dc_type', '=', 118001);

        if($all_dcs=='no'){
            $state_data->where('legal_entity_type_id', '!=', 1016);
        }

        if($all_fcs=='no'){
            $state_data->where('legal_entity_type_id', '!=', 1014);
        }

        if($is_apob=='no'){
            $state_data->where('legalentity_warehouses.is_apob', '!=', 1);
        }

        $state_data = $state_data->get()->all();
        
        return isset($state_data[0]->le_wh_id) ? $state_data : array();   
    }

    public function checkProductMapByDc($product_id,$le_wh_id){
        $productData = DB::table('inventory')
                        ->select("product_id")
                        ->where("le_wh_id",$le_wh_id)
                        ->where("product_id",$product_id)
                        ->first();
        return $productData;
    }

    public function checkStateAndDc($state_id,$dc_id){
        $check = DB::table("legalentity_warehouses")
                ->select("le_wh_id")
                ->where("state",$state_id)
                ->where("le_wh_id",$dc_id)
                ->count();
        return $check;
    }

    public function getProductSlabsByCust($product_id,$le_wh_id,$user_id=0,$customer_type){
        $productSlabs = DB::selectFromWriteConnection(DB::raw("CALL getProductSlabsByCust($product_id,'" . $le_wh_id . "',0,$customer_type)"));
        return $productSlabs;
    }

    public function prodSlabFlatRefreshByProductId($product_id,$le_wh_id){
        $productSlabs = DB::selectFromWriteConnection(DB::raw("CALL ProdSlabFlatRefreshByProductId($product_id,$le_wh_id)"));
        return $productSlabs;
    }
}