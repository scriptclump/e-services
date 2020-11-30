<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;

class GridController extends Controller
{
	public function index()
	{
        return View::make('cockpit/index');
	}

	public function getCockpitProducts(Request $request)
	{

		$page = $request->input('page');
		$pageSize = $request->input('pageSize');
		
		$skip = $page*$pageSize;
		
		$result = DB::table('products')->select('product_id as ProductID','product_name as Name','product_uom as ProductNumber', 'is_active as MakeFlag')->skip($page*5)->take(5)->get()->all();
		
		
		echo json_encode(array('Records'=>$result,'TotalRecordsCount'=>16));
		
		/*echo '{"Records":[{ "ProductID": 1, "Name": "Amsterdam", "ProductNumber": "BA-8444", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 1000, "ReorderPoint": 750, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "694215b7-08f7-4c0d-acb1-d734ba44c0c8", "ModifiedDate": "\/Date(1078992096827)\/" }, 
	{ "ProductID": 2, "Name": "Bearing Ball", "ProductNumber": "BA-8327", s"MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 1000, "ReorderPoint": 750, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "58ae3c20-4f3a-4749-a7d4-d568806cc537", "ModifiedDate": "\/Date(1078992096827)\/" }, 
	{ "ProductID": 3, "Name": "BB Ball Bearing", "ProductNumber": "BE-2349", "MakeFlag": true, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 800, "ReorderPoint": 600, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 1, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "9c21aed2-5bfa-4f18-bcb8-f11638dc2e4e", "ModifiedDate": "\/Date(1078992096827)\/" }, 
	{ "ProductID": 4, "Name": "Headset Ball Bearings", "ProductNumber": "BE-2908", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 800, "ReorderPoint": 600, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "ecfed6cb-51ff-49b5-b06c-7d8ac834db8b", "ModifiedDate": "\/Date(1078992096827)\/" }, 
	{ "ProductID": 316, "Name": "Blade", "ProductNumber": "BL-2036", "MakeFlag": true, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 800, "ReorderPoint": 600, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 1, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "e73e9750-603b-4131-89f5-3dd15ed5ff80", "ModifiedDate": "\/Date(1078992096827)\/" }, 
	{ "ProductID": 317, "Name": "LL Crankarm", "ProductNumber": "CA-5965", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": "Black", "SafetyStockLevel": 500, "ReorderPoint": 375, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": "L ", "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "3c9d10b7-a6b2-4774-9963-c19dcee72fea", "ModifiedDate": "\/Date(1078992096827)\/" } ]}
	';*/
	}
	
	public function getCockpitChilds(Request $request)
	{
		$path = explode(':',$request->input('path'));
		
		$Product_ID = $path[1];

		$result = DB::table('products')->select('products.product_id as ID','mp_name as ProductName','mp_url as UnitPrice','weight as UnitsInStock')
			->join('mp_product_add_update', 'mp_product_add_update.product_id', '=' , 'products.product_id')
			->join('mp', 'mp.mp_id', '=' , 'mp_product_add_update.mp_id')
			->get();
	
		echo json_encode(array('Records'=>$result,'TotalRecordsCount'=>16));
	}
}
