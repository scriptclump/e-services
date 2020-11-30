<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'assets', 'namespace' => 'App\Modules\Assets\Controllers'], function () {

       	
		Route::get('/dashboard', 'assetsController@assetDashboard');
		Route::get('/assetData', 'assetsController@assetDashboardData');
		Route::post('/saveassetdata', 'assetsController@saveAssetData');
		Route::post('/updateassetdata', 'assetsController@updateAssetData');
		Route::post('/getdetailsfromassets/{id}', 'assetsController@getDetailsFromAssets');
		Route::get('/getuserlist','assetsController@getUserListDetails');
		Route::get('/getassethistorydetailsbyid/{id}','assetsController@getAssetHistoryDetails');
		Route::get('/getbrandsasmanufac/{manufac_id}','assetsController@getBrandsAsManufac');
		Route::post('/saveallocatedata', 'assetsController@saveAllocateData');
		//saving into products table
		Route::post('/saveassetintoproductstable', 'assetsController@saveDataIntoProductsTable');
		Route::post('/getinwardproducttabledatawithid/{id}', 'assetsController@getInwardProductwithId');
		
		
		// Routes for Asset Approval
		Route::get('/astaprdashboard/openpopup/{id}', 'assetsApprovalController@assetApprovalDashboard');
		
		Route::get('/astaprdashboard', 'assetsApprovalController@assetApprovalDashboard');
		Route::get('/getbrandsasmanufac/{manufac_id}','assetsApprovalController@getBrandsAsManufac');
		Route::get('/loadproductinlist/{category}/{brand}','assetsApprovalController@getProductAsPerCategory');
		Route::post('/saveapprovaldata','assetsApprovalController@saveApprovalData');
		Route::get('/approvaldata','assetsApprovalController@getApprovalData');
		Route::get('/approveasset/{id}','assetsApprovalController@getApprovalAsset');
		Route::post('/updateapprovestatus','assetsApprovalController@updateApproveStatus');
		Route::get('/gethistoryexpensesdetails/{id}','assetsApprovalController@getHistoryAssetsApprovaData');

		Route::get('/loadmanufacturedata','assetsController@loadManufactureData');

		Route::get('/loadcategories','assetsController@loadCategories');

		/* download asset template */
		Route::post('/downloadexcelforassets', 'assetsController@downloadExcelWithAssets');

		Route::post('/downloaddepreciationdata', 'assetsController@downloadExcelWithDepreciation');
		// Routes for Asset import Backend //
		Route::post('/importasset', 'assetsController@importAssetFromExcel');
		
	});
});