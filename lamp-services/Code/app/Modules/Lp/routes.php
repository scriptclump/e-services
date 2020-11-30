<?php

//Route::get('inward','Inbound\Controllers\InwardController@index');
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Lp\Controllers'], function () {
        
    Route::any('logisticpartners/downloadTemplate/{type}', 'LogisticPartnersController@downloadTemplate');
    Route::any('logisticpartners/downloadExcel/{type}/{lp_id}', 'LogisticPartnersController@downloadExcel');
    Route::post('logisticpartners/importExcel/{lp_id}', 'LogisticPartnersController@importExcel');
	Route::group(['before' => 'authenticates'], function() {    
	Route::get('logisticpartners','LogisticPartnersController@indexAction');
      
    Route::any('logisticpartners/add', 'LogisticPartnersController@addAction');
	Route::any('logisticpartners/deletewh/{wh_id}', 'LogisticPartnersController@deleteWareHouseAction');
    Route::any('logisticpartners/edit/{lp_id}', 'LogisticPartnersController@editAction');
    Route::any('logisticpartners/save', 'LogisticPartnersController@saveAction');
    Route::post('logisticpartners/delete', 'LogisticPartnersController@deleteAction');
    Route::any('logisticpartners/savewh', 'LogisticPartnersController@saveWarehouseAction');
    Route::get('logisticpartners/editwh/{wh_id}', 'LogisticPartnersController@editWareHouseAction');

    Route::get('logisticpartners/getLpList', 'LogisticPartnersController@getLpList');
    Route::get('logisticpartners/getWarehouseList', 'LogisticPartnersController@getWarehouseList');
    Route::get('logisticpartners/getWarehouseList/{LpID}', 'LogisticPartnersController@getLpWarehouseList');
    Route::get('logisticpartners/getLogisticPartners', 'LogisticPartnersController@getLogisticPartners');
    Route::get('logisticpartners/getLpWarehouses/{lp_id}', 'LogisticPartnersController@getLpWarehouses');
	
	Route::post('logisticpartners/warehuniq/{lp_id}', 'LogisticPartnersController@warehuniq');
        Route::any('logisticpartners/googlepincode/{pincode}','LogisticPartnersController@googlepincode');

    
});













		
		
		
		
	});
});
