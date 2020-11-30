<?php
/*
FileName :pricingDashboardModel.php
Author   :eButor
Description : Pricing dashboard model.
CreatedDate :9/aug/2016
*/
//defining namespace
namespace App\Modules\Pricing\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;
use DB;
use Session;
use UserActivity;
use Utility;

class pricingDashboardModel extends Model{

	protected $table = 'product_prices_history';
    protected $primaryKey = "product_price_id";
    private $pricesColumns = "product_price_id,product_id,state_id,customer_type,price,ptr,legal_entity_id,effective_date,status,is_markup,history_reff_id,created_by,updated_by,dc_id";

	public function getBrandDetails(){
		$getBrandDetails = DB::table('brands');

		if(Session::get('legal_entity_id')!=0){
			$getBrandDetails->where('legal_entity_id', '=', Session::get('legal_entity_id'));
		}
        return $getBrandDetails->get()->all();
	}

	public function getManufactureDetails(){
		$getDetails = DB::table('legal_entities')
							->where('legal_entity_type_id', '=', '1006');
		if(Session::get('legal_entity_id')!=0){
			$getDetails->where('legal_entity_id', '=', Session::get('legal_entity_id'));
		}
							
        return $getDetails->get()->all();
	}

	public function getCategoryDetails(){
		$getDetails = DB::table('categories')
							->where('is_active', '=', '1')
							->get()->all();
        return $getDetails;
	}

	public function getStateDetails(){
		$getDetails = DB::table('zone')
							->where('status', '=', '1')
							->where('country_id', '=', '99')
							->orderBy('name')
							->get()->all();
        return $getDetails;
	}

	public function getStateForTAX($stateid){
		$getDetails = DB::table('zone')
							->where('status', '=', '1')
							->where('country_id', '=', '99')
							->where('zone_id', '!=', $stateid)
							->first();
        return $getDetails;
	}

	public function getCustomerGroup(){
		$getDetails = DB::table('master_lookup')
							->where('mas_cat_id', '=', '3')
							->where('is_active', '=', '1')
							->orderBy('master_lookup_name')
							->get()->all();
        return $getDetails;
	}

	public function getBenificiaryName(){
		$getDetails = DB::table('roles')
							->where('is_active', '=', '1')
							->orderBy('name')
							->get()->all();
        return $getDetails;
	}

	public function getBenificiaryNameForExcel(){
		$getDetails = DB::table('roles')
							->where('is_active', '=', '1')
							->orderBy('name')
							->get()->all();
        return json_encode($getDetails);
	}


	public function getWarehousesForExcel(){
		$getDetails = DB::table('legalentity_warehouses')
							->where('lp_name', '=', 'Custom')
							->orderBy('lp_wh_name')
							->get()->all();
        return json_encode($getDetails);
	}

	public function getProductStarsExcel(){
		$getDetails = DB::table('master_lookup')
							->where('mas_cat_id', '=', '140')
							->get()->all();

        return json_encode($getDetails);
	}

	public function getWarehouses(){
		$getDetails = DB::table('legalentity_warehouses')
							->where('lp_name', '=', 'Custom')
							->where('status', '=', 1)
							->orderBy('lp_wh_name')
							->get()->all();
        return $getDetails;
	}

	public function getProductStars(){
		$getDetails = DB::table('master_lookup')
							->where('mas_cat_id', '=', '140')
							->get()->all();

        return $getDetails;
	}

	public function getBrandsAsManufacId($manufac){

		$getBrand = DB::table('brands')
							->where('mfg_id', '=', $manufac)
							->where('is_active', '=', '1')
							->get()->all();
        return $getBrand;
	}

	//this function is using for cron set
	public function priceUpdateWithUpdatedDate($updateDate){

		// Get all the data from hisotry table with the date
		$sqlQuery ="select hist.*, prd.product_title, prd.sku 
		from product_prices_history as hist
		inner join products as prd on prd.product_id=hist.product_id
		where date(effective_date)='".$updateDate."'";

		$allData = DB::select(DB::raw($sqlQuery));


		// Update the Product which is found on the same date
		$tempArray = array();
		foreach($allData as $data){

			$getPriceUniqueData = DB::table("product_prices")
                ->where('product_id', '=', $data->product_id)
                ->where('state_id', '=', $data->state_id)
                ->where('customer_type', '=', $data->customer_type)
                ->first();

            if( count($getPriceUniqueData) > 0 ){

            	// Update command to update the table
	        	DB::table('product_prices')
		            ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
		            ->update(['price' => $data->price,'ptr'=>$data->ptr,'effective_date'=>$data->effective_date,'updated_by' => 1, 'updated_at' => date('Y-m-d H:i:s'),'history_reff_id'=>$data->product_price_id]);
                // updating flat tabel as per requirement
				$flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$getPriceUniqueData->product_id.")"));
            }else{
            	// Inserting the record in the table if the Unique combination not available in product prices table
            	$price_data = (array) $data;
            	unset($price_data['product_title']);
            	unset($price_data['sku']);
            	DB::table("product_prices")->insert($price_data);
                // updating flat tabel as per requirement
				$flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$price_data['product_id'].")"));
            }
			
            // Make the array for Mail
            $tempArray[] = array(
            	'prod_title' 				=> $data->product_title,
            	'prod_sku' 					=> $data->sku,
            	'price' 					=> $data->price,
            	'ptr' 						=> $data->ptr,
            	'effective_date' 			=> $data->effective_date,
            );
		}

		return $tempArray;
	}
 
	public function getProductDatabyID($myId){
		$legaEntityQuery = "";
		/*if(Session::get('legal_entity_id')!=0){
			$legaEntityQuery=" AND prd.legal_entity_id='". Session::get('legal_entity_id') ."'";
		}*/
		$sqlQuery = "select prd.product_id, prd.mrp, prd.product_title, prd.sku,
			(select rlp from product_tot as prdtot where prdtot.product_id=prd.product_id limit 0,1) as rlp,
			(SELECT is_markup FROM product_tot AS prdtot WHERE prdtot.product_id=prd.product_id limit 0,1) AS is_markup
			from products as prd
			WHERE prd.product_id='".$myId."' ". $legaEntityQuery;
		$allData = DB::select(DB::raw($sqlQuery));
        return $allData;
	}

	public function getPriceByID($priceID){
		$legaEntityQuery = "";
		if(Session::get('legal_entity_id')!=0){
			$legaEntityQuery=" AND price.legal_entity_id='". Session::get('legal_entity_id') ."'";
		}
		$sqlQuery = "select prd.`sku`,prd.product_id, prd.mrp, prd.product_title, price.state_id, price.customer_type, price.`price`, price.ptr, price.product_price_id,price.dc_id, 
			date_format(price.effective_date, '%d/%m/%Y') as effective_date,
			(SELECT rlp FROM product_tot AS prdtot WHERE prdtot.product_id=prd.product_id limit 0,1) AS rlp,
			(SELECT is_markup FROM product_tot AS prdtot WHERE prdtot.product_id=prd.product_id limit 0,1) AS is_markup
			FROM products AS prd
			INNER JOIN product_prices_history AS price ON price.product_id=prd.product_id
			AND price.product_price_id='".$priceID."' ". $legaEntityQuery;
        $allData = DB::select(DB::raw($sqlQuery));
        return $allData;
	}
	//for getting cashback

	public function getCashback($priceID){
		$sqlQuery = "select pd.`cbk_id`, pd.`cbk_label`, 
				pd.`state_id`, (SELECT NAME FROM zone zn WHERE zn.zone_id=pd.`state_id`) AS 'StateName',
				pd.`customer_type`, (SELECT master_lookup_name FROM master_lookup mst WHERE mst.value=pd.`customer_type`) AS 'CustomerType',
				pd.`wh_id`, (SELECT lp_wh_name FROM legalentity_warehouses lw WHERE lw.le_wh_id=pd.`wh_id` ) AS 'WareHouse',
				pd.`benificiary_type`, (SELECT NAME FROM roles rl WHERE rl.role_id=pd.`benificiary_type` ) AS 'Benificiary',
				pd.`product_star`, (SELECT master_lookup_name FROM master_lookup mst WHERE mst.value=pd.`product_star`) AS 'ProductStart',
				DATE_FORMAT(pd.`start_date`,'%d-%m-%Y') AS 'StartDate', DATE_FORMAT(pd.`end_date`,'%d-%m-%Y') AS 'endDate', pd.`range_to`,
				pd.`cbk_type`, pd.`cbk_value`
				FROM promotion_cashback_details AS pd
				WHERE pd.`cbk_ref_id`=$priceID AND pd.cbk_source_type=2";
		$allData = DB::select(DB::raw($sqlQuery));


        return $allData;

	}

	//save into cashback table

	public function saveCashBackDataIntoTable($cashbackdata){

			$findData = DB::table("promotion_cashback_details")
                        ->where('product_id', '=', $cashbackdata['product_id'])
                        ->where('state_id', '=', $cashbackdata['state_id'])
                        ->where('wh_id', '=', $cashbackdata['wh_id'])
                        ->where('customer_type', '=', $cashbackdata['customer_type'])
                        ->where('benificiary_type', '=', $cashbackdata['benificiary_type'])
                        ->where('product_star', '=', $cashbackdata['product_star'])
                        ->where('end_date', '=', $cashbackdata['end_date'])
                        ->where('cbk_type', '=', $cashbackdata['cbk_type'])
                        ->where('range_to', '=', $cashbackdata['range_to'])
                        ->where('cbk_value', '=', $cashbackdata['cbk_value'])
                        ->where('cbk_status', '=', 1)
                        ->where('cbk_source_type', '=', 2)
                        ->first();

            $findDataCount = count($findData);

            if($findDataCount >= 1){
            	$data = "";
            }else{
            	$data = DB::table("promotion_cashback_details")->insert($cashbackdata);

            }

            return DB::getPdo()->lastInsertId($data);



	}

	public function deleteCashBackDataById($id){

            $data = DB::table("promotion_cashback_details")->where('cbk_id','=',$id)->delete();

            return 1;



	}

	public function deletePricingData($deleteData){
		$name = Session::all();
		$environment = env('APP_ENV');
		// get OLD Data
    	$oldData = DB::table("product_prices_history")
                ->where('product_price_id', '=', $deleteData)
                ->first();

        // Making a variable for delete information
        $oldDataForDelte = $oldData;
        $cashback_ref_id = $deleteData;
        $getname = DB::table("products")
				->where('product_id', '=', $oldData->product_id)
				->get()->all();
                
		$oldData = array(
				'OLDVALUES' 		=> 	json_decode(json_encode($oldData)),
				'NEWVALUES'			=> 'Deleted data',
			);

		// delete the data from products_slab_rate table by slab_id
		$deleteSlabData = DB::table('product_prices_history')
							->where('product_price_id', '=', $deleteData)
							->delete();

		// delete the cashback data from promotion_cashback_details table by product_price_id
		$deleteCashbackData = DB::table('promotion_cashback_details')
							->where('cbk_ref_id', '=', $cashback_ref_id)
							->where('cbk_source_type', '=', 2)
							->delete();


		// Check the Deleted Data is more that Today ( We are not deleting the data if it is of a future date )
		if( $oldDataForDelte->effective_date <= date('Y-m-d') ){

			$getPriceUniqueData = DB::table("product_prices")
                ->where('product_id', '=', $oldDataForDelte->product_id)
                ->where('state_id', '=', $oldDataForDelte->state_id)
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
				pr.`ptr`, pr.`legal_entity_id`, pr.`effective_date`, pr.`status`, pr.`is_markup`, pr.product_price_id as 'history_reff_id',
				pr.`created_by`, pr.`created_at`, pr.`updated_by`, pr.`updated_at`

				FROM product_prices_history AS pr
				INNER JOIN (
				  SELECT product_id, MAX(effective_date) AS MaxDate
				  FROM product_prices_history
				  where product_id in (".$getPriceUniqueData->product_id.")
				  and state_id=".$getPriceUniqueData->state_id."
				  and customer_type=".$getPriceUniqueData->customer_type."
				  GROUP BY product_id
				) AS innertbl ON innertbl.product_id=pr.`product_id`
				AND innertbl.MaxDate=pr.`effective_date`";

				$resp = DB::insert($insertQuery);
				// inserting

            }

		}


    	UserActivity::userActivityLog('Pricing', $oldData, 'Price Deleted by the User');

		$oldPrice 	= isset($oldData['OLDVALUES']->price) ? $oldData['OLDVALUES']->price : 'No Value';
		$oldPTR 	= isset($oldData['OLDVALUES']->ptr) ? $oldData['OLDVALUES']->ptr : 'No Value';

		$mailHTML	= "
		<tr>
			<td>".$getname['0']->product_id."</td>
			<td>".$getname['0']->sku."</td>
			<td>".$getname['0']->product_title."</td>
			<td>".$oldPrice."</td>
			<td>".$oldPTR."</td>
			<td>--</td>
			<td>--</td>
			<td>Price Deleted</td>
		</tr>
		";

		$topMsg 	= "This is to notify you that pricing for the product has been deleted successfully.";

    	Mail::send('emails.pricingMail', ['topMsg'=>$topMsg, 'mailHTML' => $mailHTML, 'changedby' => $name['userName'], 'editFlag' => 3 ], function ($message) use ($environment) {
    	                
    	                if( $environment=='local' || $environment=='dev' || $environment=='qc' || $environment=='supplier' ){
    	                	$message->from("tracker@ebutor.com", $name = "Tech Support - " . $environment);
    	                	$message->to("venkatesh.burla@ebutor.com");
    	                }else{
    	                	$message->from("tracker@ebutor.com", $name = "Tech Support");
    	                	$message->to("satish.racha@ebutor.com");
    	                }
    	                $message->bcc("somnath.chowdhury@ebutor.com");
    	                $message->subject('Price deleted for the Product on :' . date('d-m-Y H:i:s') );
    	            });



        return $deleteSlabData;
	}
	public function getProductsAsPerBrand($brand_id){
		$getProductDetails = DB::table('products')
							->where('brand_id', '=', $brand_id);

		if(Session::get('legal_entity_id')!=0){
			$getProductDetails->where('legal_entity_id', '=', Session::get('legal_entity_id'));
		}
        return $getProductDetails->get()->all();
	}
    public function getSkuforNotification($deleteData){
    	$getSku = DB::table('products')
    						->join("product_prices_history","products.product_id","=","product_prices_history.product_id")
							->where('product_prices_history.product_price_id', '=', $deleteData)
							->first();					
        return $getSku;
    }
	public function productForSearch($item, $manufacID, $brandID){

        $getlist = DB::table('products')
            ->select('product_title','sku','primary_image','product_id')
            ->where((function ($query) use ($manufacID, $brandID) {

			    /*if(Session::get('legal_entity_id')!=0){
					$query->where('legal_entity_id', '=', Session::get('legal_entity_id'));
				}*/

			    if($manufacID!="" && $manufacID!='null'){
			    	$query->where('manufacturer_id', '=', $manufacID);
			    }
			    if($brandID!="" && $brandID!='null'){
			    	$query->where('brand_id', '=', $brandID);
			    }
			}))
            ->where((function ($query) use ($item) {
			    $query->where('product_title','like','%'.$item.'%')
			    		->orWhere('sku','like','%'.$item.'%');
			}))
            ->get()->all();
        $product_arr = array();
        foreach($getlist  as $get) {
            $product = array("label" => $get->product_title , "sku" => $get->sku,"image"=>$get->primary_image,"product_id"=>$get->product_id);
            array_push($product_arr, $product); 
        }
        return $product_arr;
	}

	public function viewPricingDetailsData($makeFinalSql, $orderBy, $page, $pageSize, $editAccess,$deleteAccess){

		if($orderBy!=''){
			$orderBy = ' ORDER BY ' . $orderBy;
		}

		$sqlWhrCls = '';
		$countLoop = 0;
		
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

		$legaEntityQuery = "";
		if(Session::get('legal_entity_id')!=0){
			$legaEntityQuery=" WHERE slab.legal_entity_id='". Session::get('legal_entity_id') ."'";
		}

		if($editAccess== 1 && $deleteAccess== 1){
			$concatQuery = "CONCAT('<center><code>',
			'<a href=\"javascript:void(0)\" onclick=\"updatePriceData(',slab.product_price_id,')\">
			  <i class=\"fa fa-pencil\"></i>
			  </a>&nbsp;&nbsp;&nbsp;
			  <a href=\"javascript:void(0)\" onclick=\"deleteData(',slab.product_price_id,')\">
			  <i class=\"fa fa-trash-o\"></i>
			  </a>
			</code>
			</center>') 
			AS 'CustomAction', ";

		}elseif($deleteAccess == 1){
			$concatQuery = "CONCAT('<center><code>',
			'<a href=\"javascript:void(0)\" onclick=\"deleteData(',slab.product_price_id,')\">
			  <i class=\"fa fa-trash-o\"></i>
			  </a>
			</code>
			</center>') 
			AS 'CustomAction', ";

		}elseif($editAccess == 1){
			$concatQuery = "CONCAT('<center><code>',
			'<a href=\"javascript:void(0)\" onclick=\"updatePriceData(',slab.product_price_id,')\">
			  <i class=\"fa fa-pencil\"></i>
			  </a>
			</code>
			</center>') 
			AS 'CustomAction', ";
		}else{
			$concatQuery = " "; 
		}
		$sqlQuery ="select * from vw_PricingGrid" .$sqlWhrCls .$orderBy;
		$countQuery="select count(*) as count from vw_PricingGrid" .$sqlWhrCls .$orderBy;
		$pageLimit = '';
		if($page!='' && $pageSize!=''){
			$pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
		}
		$allRecallData = DB::select(DB::raw($sqlQuery . $pageLimit));
		$recordsCount=DB::select(DB::raw($countQuery));
        $TotalRecordsCount =$recordsCount[0]->count;

		return json_encode(array('results'=>$allRecallData, 'TotalRecordsCount'=>(int)($TotalRecordsCount)));       
	}

	

	public function addEditProductPrice($slab_data, $product_price_id=0, $productName=''){
		$name = Session::all();
		$environment = env('APP_ENV');

		$responseMSG="";
		$effective_date = isset($slab_data['effective_date']) ? $slab_data['effective_date'] : '';
        $created_by = isset($slab_data['created_by']) ? $slab_data['created_by'] : '';

        // Edit Part
        if( $product_price_id!=0 ){

        	// get OLD Data

        	$oldData = DB::table("product_prices_history")
                    ->where('product_price_id', '=', $product_price_id)
                    ->join('products', 'product_prices_history.product_id', '=','products.product_id')
                    ->first();

            $tempOldData = $oldData;

			$oldData = array(
					'OLDVALUES' 		=> 	json_decode(json_encode($oldData)),
					'NEWVALUES'			=>	$slab_data
				);


            // Check in the Pricing Unique Table ( Naresh Lst requirement 07-02-2017 to keep the unique data in the main table )

    		$getPriceUniqueData = DB::table("product_prices")
    			->select(explode(',',$this->pricesColumns))
                ->where('product_id', '=', $slab_data['product_id'])
                ->where('state_id', '=', $tempOldData->state_id)
                ->where('dc_id', '=', $tempOldData->dc_id)
                ->where('customer_type', '=', $tempOldData->customer_type)
                ->first();
                
            // WE are restricting the user to update the currently active date for the pricing
            if( ($tempOldData->effective_date == $getPriceUniqueData->effective_date) && ($tempOldData->effective_date!=$effective_date) ){
            	return array("status"=>10,"old_price"=>"","new_price"=>"");
            }

            // Update Pricing History Table

			
        	DB::table('product_prices_history')
            	->where('product_price_id', '=', $product_price_id )
            	->update(['price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $effective_date,'updated_by' => $created_by, 'updated_at' => $slab_data['created_at']]);


	        $update_flag = 0;
            if( $getPriceUniqueData ){

            	// if the Record exist in the main table and effective date is lesser than the new date, then update
            	if( $getPriceUniqueData->effective_date <= $effective_date ){

            		// also checking for the current date
            		if( $effective_date <= date('Y-m-d') ){
				        // updating
        				DB::table('product_prices')
				            ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
				            ->update(['price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $effective_date, 
				            	'updated_by' => $created_by, 'updated_at' => $slab_data['created_at'], 'history_reff_id'=> $product_price_id]);
                            // updating flat tabel as per requirement
				            $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slab_data['product_id'].",".$slab_data['dc_id'].")"));
				       	$update_flag = 1;
            		}
            	}
            }

            // write a data into Mongo for log
    		UserActivity::userActivityLog('Pricing', $oldData, 'Price Updated by the User');

    		// Prepare the data for Mail compose
    		$oldPrice 	= isset($oldData['OLDVALUES']->price) ? $oldData['OLDVALUES']->price : 'No Value';
    		$oldPTR 	= isset($oldData['OLDVALUES']->ptr) ? $oldData['OLDVALUES']->ptr : 'No Value';
    		$productid 	= isset($oldData['OLDVALUES']->product_id) ? $oldData['OLDVALUES']->product_id : 'No Value';
    		$sku 		= isset($oldData['OLDVALUES']->sku) ? $oldData['OLDVALUES']->sku : 'No Value';

    		$newPrice 	= isset($oldData['NEWVALUES']['price']) ? $oldData['NEWVALUES']['price'] : 'No Value';
    		$newPTR 	= isset($oldData['NEWVALUES']['ptr']) ? $oldData['NEWVALUES']['ptr'] : 'No Value';
    		$oldCustType 	= isset($oldData['OLDVALUES']->customer_type) ? $oldData['OLDVALUES']->customer_type : 0;

    		$topMsg 	= "This is to notify you that the product Price has been updated successfully.";


    		$mailHTML	= "
				<tr>
					<td>".$productid."</td>
					<td>".$sku."</td>
					<td>".$productName."</td>
					<td>".$oldPrice."</td>
					<td bgcolor='#FFFF00'>".$newPrice."</td>
					<td>".$oldPTR."</td>
					<td bgcolor='#FFFF00'>".$newPTR."</td>
					<td>Price Updated</td>
				</tr>
				";

    		Mail::send('emails.pricingMail', ['topMsg'=>$topMsg, 'mailHTML'=>$mailHTML, 'changedby' => $name['userName'], 'editFlag' => 1 ], function ($message) use ($environment) {
    	                
    	                if( $environment=='local' || $environment=='dev' || $environment=='qc' || $environment=='supplier' ){
    	                	$message->from("tracker@ebutor.com", $name = "Tech Support - " . $environment);
    	                	$message->to("venkatesh.burla@ebutor.com");
    	                }else{
    	                	$message->from("tracker@ebutor.com", $name = "Tech Support");
    	                	$message->to("satish.racha@ebutor.com");
    	                }
    	                $message->bcc("somnath.chowdhury@ebutor.com");
    	                $message->subject('Price changed for the Product on :' . date('d-m-Y H:i:s') );
    	            });
    		if($update_flag==0){
    			$newPrice = 0;
    		}
	        $responseMSG= array("status"=>1,"old_price"=>$oldPrice,"new_price"=>$newPrice,"oldCustType"=>$oldCustType);

        }else{
        	// Add Part
            			     
        	// Check effective date combination
	        $findData = DB::table("product_prices_history")
	                    ->where('product_id', '=', $slab_data['product_id'])
	                    ->where('state_id', '=', $slab_data['state_id'])
	                   	->where('dc_id', '=', $slab_data['dc_id'])
	                    ->where('customer_type', '=', $slab_data['customer_type'])
	                    ->where('effective_date', '=', $effective_date)
	                    ->first();

	     	$findDataCount = count($findData);
	     	// add part (if no combination found then add the data)
        	if($findDataCount==0){
        		//inserting
        		$insert = $this->insert($slab_data);
        		$lastid = DB::getPdo()->lastInsertId($insert);

        		// Check in the Pricing Unique Table ( Naresh Lst requirement 07-02-2017 to keep the unique data in the main table )

        		$getPriceUniqueData = DB::table("product_prices")
    				->select(explode(',',$this->pricesColumns))
                    ->where('product_id', '=', $slab_data['product_id'])
                    ->where('state_id', '=', $slab_data['state_id'])
                    ->where('dc_id', '=', $slab_data['dc_id'])
                    ->where('customer_type', '=', $slab_data['customer_type'])
                    ->first();
                $update_flag=0;
                if( $getPriceUniqueData ){

                	// if the Record exist in the main table and effective date is lesser than the new date, then update
                	if( $getPriceUniqueData->effective_date <= $effective_date ){

                		// also checking for the current date
                		if( $effective_date <= date('Y-m-d') ){
                			// updating
                			DB::table('product_prices')
				            ->where('product_price_id', '=', $getPriceUniqueData->product_price_id )
				            ->update(['price' => $slab_data['price'],'ptr' =>$slab_data['ptr'], 'effective_date' => $effective_date, 
				            	'updated_by' => $created_by, 'updated_at' => $slab_data['created_at'], 'history_reff_id' => $lastid]);
                            // updating flat tabel as per requirement
				            $flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slab_data['product_id'].",".$slab_data['dc_id'].")"));
				            $update_flag=1;
                		}
                	}
                	
                }else{

                	// inserting
                	$slab_data['history_reff_id'] = $lastid;
                	DB::table("product_prices")->insert($slab_data);
                    // updating flat tabel as per requirement
					$flatTbaleUpdate = DB::statement(DB::raw("CALL ProdSlabFlatRefreshByProductId(".$slab_data['product_id'].",".$slab_data['dc_id'].")")); 
                }

                // Code to keep the history information
        		$getsku = DB::table('product_prices_history')
        							->join('products','products.product_id','=','product_prices_history.product_id')
        							->where('product_price_id', '=', $lastid)
        							->first();

        		// write a data into Mongo for log
        		$activityData = array(
					'NEWVALUES'			=>	$slab_data
				);

        		UserActivity::userActivityLog('Pricing', $activityData, 'Price Added by the User');

        		$newPrice 	= isset($activityData['NEWVALUES']['price']) ? $activityData['NEWVALUES']['price'] : 'No Value';
    			$newPTR 	= isset($activityData['NEWVALUES']['ptr']) ? $activityData['NEWVALUES']['ptr'] : 'No Value';
    			$productid 	= isset($activityData['NEWVALUES']['product_id']) ? $activityData['NEWVALUES']['product_id'] : 'No Value';

    			$topMsg 	= "This is to notify you that pricing for the product has been added successfully.";

    			$mailHTML	= "
				<tr>
					<td>".$productid."</td>
					<td>".$getsku->sku."</td>
					<td>".$productName."</td>
					<td>--</td>
					<td bgcolor='#FFFF00'>".$newPrice."</td>
					<td>--</td>
					<td bgcolor='#FFFF00'>".$newPTR."</td>
					<td>Price Added</td>
				</tr>
				";
				
				$subject = 'Price Added for the Product on :' . date('d-m-Y H:i:s') ;
				$getEmails=array('satish.racha@ebutor.com');
				if( $environment=='local' || $environment=='dev' || $environment=='qc' || $environment=='supplier' ){
                	$uname = "Tech Support - " . $environment;
                }else{
                	$uname = "Tech Support";
                }
				$body = array('template'=>'emails.pricingMail', 'attachment'=>'','topMsg'=>$topMsg, 'changedby' => $name['userName'], 'mailHTML' => $mailHTML, 'editFlag' => 0,
					'name'=>$uname);
                Utility::sendEmail($getEmails, $subject, $body);

        		// Mail::send('emails.pricingMail', ['topMsg' => $topMsg, 'mailHTML' =>$mailHTML,'changedby' => $name['userName'], 'editFlag' => 0 ], function ($message) use ($environment) {
    	     //            if( $environment=='local' || $environment=='dev' || $environment=='qc' || $environment=='supplier' ){
    	     //            	$message->from("tracker@ebutor.com", $name = "Tech Support - " . $environment);
    	     //            	$message->to("venkatesh.burla@ebutor.com");
    	     //            }else{
    	     //            	$message->from("tracker@ebutor.com", $name = "Tech Support");
    	     //            	$message->to("satish.racha@ebutor.com");
    	     //            }
    	     //            //$message->bcc("somnath.chowdhury@ebutor.com");
    	     //            $message->subject('Price Added for the Product on :' . date('d-m-Y H:i:s') );
    	     //        });
        		$oldCustType = isset($getPriceUniqueData->customer_type)?$getPriceUniqueData->customer_type:0;
        		$oldPrice = isset($getPriceUniqueData->price)?$getPriceUniqueData->price:0;
        		if($update_flag==0){
        			$newPrice = 0;
        		}
        		$responseMSG= array("status"=>2,"old_price"=>$oldPrice,"new_price"=>$newPrice,"oldCustType"=>$oldCustType);
        	}else{
        		$responseMSG= array("status"=>3,"old_price"=>"","new_price"=>"");	
        	}
        }
        return $responseMSG;
    }


    // get all dcs data

    public function getAllDCS(){

    	$legaEntityQuery = "";
		if(Session::get('legal_entity_id')!=2){
			$legaEntityQuery=" AND lw.legal_entity_id='". Session::get('legal_entity_id') ."'";
		}

    	$allDCS = "select * FROM legalentity_warehouses AS lw INNER JOIN zone AS z ON lw.state = z.zone_id WHERE lw.dc_type=118001 AND lw.status=1 ".$legaEntityQuery."";

    	/*$allDCS = DB::table("legalentity_warehouses")
                ->where('dc_type', '=', 118001)
                ->where('status', '=',1)
                ->get();
*/
        $allData = DB::select(DB::raw($allDCS));
         return $allData;



    }

    public function getstateIdByDC($dcid){
	
		$stateid = DB::table("legalentity_warehouses")
				->select('state')
                ->where('le_wh_id', '=', $dcid)
                ->get()->all();

         return $stateid[0]->state;
    }
}	