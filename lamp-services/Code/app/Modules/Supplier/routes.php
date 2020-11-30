<?php

    
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Supplier\Controllers'], function () {
        
	Route::get('suppliers','SupplierController@suppliersList');
	Route::get('suppliers/export','SupplierController@downloadVendorExcel');
	//Route::any('getbu','SupplierController@odersTabGetBuUnit');

	Route::get('serviceproviders','SupplierController@serviceProviderList');
	Route::get('vehicle','SupplierController@vehiclesList');
	Route::get('vehicleproviders','SupplierController@vehicleProvidersList');
	Route::get('space','SupplierController@spaceList');
	Route::get('spaceprovider','SupplierController@spaceProvidersList');
	Route::get('humanresource','SupplierController@manpowerProvidersList');
        Route::post('vehicle/additional','SupplierController@vehiclesAdditional');
        Route::post('space/additional','SupplierController@spaceAdditional');
	Route::any('suppliers/add','SupplierController@index');
        Route::get('serviceproviders/add','SupplierController@index');
        Route::get('vehicle/add','SupplierController@index');
        Route::get('vehicleproviders/add','SupplierController@index');
        Route::get('humanresource/add','SupplierController@index');
        Route::get('space/add','SupplierController@index');
        Route::get('spaceprovider/add','SupplierController@index');
	
	Route::any('suppliers/show','SupplierController@show');
	Route::any('suppliers/edit/{supplier_id}','SupplierController@editAction');
	Route::any('suppliers/approval/{supplier_id}','SupplierController@approvalAction');
  Route::any('serviceproviders/edit/{supplier_id}','SupplierController@editAction');
	Route::any('vehicle/edit/{vehicle_id}','SupplierController@editAction');
	Route::any('vehicleproviders/edit/{supplier_id}','SupplierController@editAction');
	Route::any('humanresource/edit/{supplier_id}','SupplierController@editAction');
	Route::any('space/edit/{supplier_id}','SupplierController@editAction');
	Route::any('spaceprovider/edit/{supplier_id}','SupplierController@editAction');
        Route::any('suppliers/approval/{supplier_id}','SupplierController@editAction');
	Route::post('suppliers/delete','SupplierController@destroy');
        Route::post('suppliers/deletedoc/{id}','SupplierController@deleteDoc');
	Route::get('suppliers/getBrandProducts/{brand_id}/{wh_id}','SupplierController@getProductsOnBrand');	
	Route::get('suppliers/getProducts/{legalentity_id}','SupplierController@getProducts');
	Route::get('suppliers/getBrands/{legalentity_id}','SupplierController@getBrands');
	Route::get('suppliers/getBrandsGrid','SupplierController@getBrandsFromView');
	Route::get('suppliers/getProductsGrid','SupplierController@getProductsFromView');
	Route::get('suppliers/getSuppliers','SupplierController@getSuppliers');
	Route::get('suppliers/getBrandProducts/{brand_id}/{wh_id}','SupplierController@getProductsOnBrand');
	Route::post('suppliers/enableDisableProduct','SupplierController@enableDisableProduct');
	Route::any('tot/save','SupplierController@totSave');
	Route::any('brand/save','SupplierController@brandSave');
	Route::any('suppliers/create','SupplierController@create');
        Route::any('suppliers/supplierdocs','SupplierController@supplierdocs');
        Route::any('suppliers/getWarehouseList', 'SupplierController@getSupplierWarehouseList');
	Route::any('suppliers/importTOTExcel','SupplierController@importTOTExcel');
	Route::any('suppliers/deletewh/{wh_id}', 'SupplierController@deleteWareHouseAction');
       	Route::any('suppliers/savewh', 'SupplierController@saveWarehouseAction');    
    	Route::get('suppliers/editwh/{wh_id}', 'SupplierController@editWareHouseAction');
	Route::any('suppliers/deletebrand/{brand_id}', 'SupplierController@deleteBrandAction');
	Route::any('suppliers/deleteProduct/{product_id}', 'SupplierController@deleteProductAction');
	Route::post('suppliers/editSetPrice/{price_id}', 'SupplierController@editSetPrice');
	Route::post('setPrice/save', 'SupplierController@saveSetPrice');
        Route::get('suppliers/getpurhistory', 'SupplierController@getPurhaseHistory');
    
    Route::get('suppliers/editBrand/{brand_id}', 'SupplierController@editBrandAction');
    Route::get('suppliers/editProduct/{product_id}/{supp_id}', 'SupplierController@editProductAction');

	Route::any('suppliers/importTOTPIMExcel','SupplierController@importTOTPIMExcel');
	Route::any('suppliers/getCatList','SupplierController@getCatList');
	Route::any('suppliers/downloadTOTExcel','SupplierController@downloadTOTExcel');
	Route::any('suppliers/reqdocs','SupplierController@reqdocs');
	Route::any('suppliers/downloadTOTPIMExcel','SupplierController@downloadTOTPIMExcel');
        Route::any('suppliers/requireddocs','SupplierController@requiredDocs');
        Route::any('suppliers/requireddocscreate','SupplierController@requireddocsCreate');
	Route::any('suppliers/approve','SupplierController@approveSupplier');
	Route::any('suppliers/reject','SupplierController@rejectSupplier');
        Route::any('suppliers/googlepincode/{pincode}','SupplierController@googlepincode');
        Route::post('suppliers/warehuniq/{legalentity_id}', 'SupplierController@warehuniq');
        Route::post('suppliers/productsbybrand', 'SupplierController@productsbybrand');
        Route::post('suppliers/categoriesbyproducts', 'SupplierController@categoriesbyproducts');
        Route::post('suppliers/productotdetails', 'SupplierController@productotdetails');
        Route::any('suppliers/importDcMappingExcel/{dc_id}', 'SupplierController@importDcMappingExcel');
        Route::any('suppliers/downloadDcMappingExcel/{xls}', 'SupplierController@downloadDcMappingExcel');
        Route::any('suppliers/getinventorymode', 'SupplierController@getinventorymodes');
        Route::any('suppliers/downloadtemplate/{xls}', 'SupplierController@downloadTemplate');
        Route::post('suppliers/importExcel/{legalentity_id}', 'SupplierController@importExcel');
        Route::any('suppliers/dcInventory/{supplier_id}', 'SupplierController@dcInventory');
        Route::any('suppliers/agrterms', 'SupplierController@agreementTerms');
        Route::any('suppliers/suppuniq', 'SupplierController@suppuniq');
        Route::any('suppliers/uniquemail', 'SupplierController@uniqueEmail');
        Route::post('suppliers/prdwhmapping', 'SupplierController@prdwhmapping');
        Route::any('suppliers/childprodutList/{wh_id}', 'SupplierController@childProdutList');
        Route::any('suppliers/totmapping', 'SupplierController@totmapping');
        Route::any('suppliers/activate', 'SupplierController@setActive');
        Route::any('suppliers/checkprovider', 'SupplierController@checkProvider');
        Route::any('suppliers/gethrproviders', 'SupplierController@getHrProviders');        
        Route::any('suppliers/getvehproviders', 'SupplierController@getVehProviders');        
        Route::any('suppliers/getvehicleslist', 'SupplierController@getVehiclesList');        
        Route::any('suppliers/getserviceprovider', 'SupplierController@getServiceProvider');        
        Route::any('suppliers/getspace', 'SupplierController@getSpace');        
        Route::any('suppliers/getspaceprovider', 'SupplierController@getSpaceProvider');        
        Route::any('serviceproviders/approval/{supplier_id}','SupplierController@editAction');
	Route::any('vehicle/approval/{supplier_id}','SupplierController@editAction');
	Route::any('vehicleproviders/approval/{supplier_id}','SupplierController@editAction');
	Route::any('humanresource/approval/{supplier_id}','SupplierController@editAction');
	Route::any('space/approval/{supplier_id}','SupplierController@editAction');
	Route::any('spaceprovider/approval/{supplier_id}','SupplierController@editAction');
	Route::any('suppliers/uniqueregistration','SupplierController@uniqueRegistation');
	Route::any('suppliers/uniqueinsurance','SupplierController@uniqueInsurance');
	Route::any('suppliers/uniquelicense','SupplierController@uniqueLicense');
	Route::any('suppliers/hubslist','SupplierController@getHubList');
	Route::any('suppliers/gstZoneCode','SupplierController@gstZoneCode');
	Route::any('get/suppliers/{supplierName}', 'SupplierController@getSupplierDetails');

	// routes for supplier mapping
	Route::any('suppliers/mapping','SupplierMappingController@supplierMapIndex');
	Route::any('suppliers/mappinggrid','SupplierMappingController@supplierMappingGrid');
	Route::any('suppliers/addsuppliermap','SupplierMappingController@addNewMapping');
	Route::any('suppliers/deletesuppliermap/{map_id}','SupplierMappingController@deleteSupplierMapping');
	Route::any('suppliers/getsupppliermapdata/{map_id}','SupplierMappingController@getSupplierMapping');
	Route::any('suppliers/updatesuppliermap','SupplierMappingController@updateSuppMaping');
	Route::get('suppliers/changemapstatus/{map_id}/{status}','SupplierMappingController@changeMapStatus');
	Route::post('suppliers/getbannerslist','SupplierController@getBannersList');
	Route::post('suppliers/getimpressionclicksbybannerid','SupplierController@getImpressionClicksbyBannerId');
	Route::post('suppliers/checkgststatecode/{gst_no}', 'SupplierController@checkGstStateCode');
  });
});
