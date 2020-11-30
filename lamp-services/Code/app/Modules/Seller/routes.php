<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Seller\Controllers'], function () {
		Route::group(['before' => 'authenticates'], function() {  
			Route::get('seller/index','SellerController@indexAction');
			Route::get('seller/add/{legalEntityId}','SellerController@addSellerAction');
			Route::get('seller/editseller/{id}','SellerController@editSellerAction');
			Route::get('seller/edit/{legalEntityId}/{sellerId}','SellerController@editSellerChildAction');
			Route::get('seller/sellerconfig','SellerController@sellerConfig');
			Route::get('seller/sellerconfig/{channelId}/{sellerId}','SellerController@sellerConfig');
			Route::get('seller/channelImage','SellerController@channelImage');
			Route::get('seller/savesellerdata','SellerController@saveSellerData');
			Route::get('seller/update','SellerController@updateSellerData');
			Route::post('seller/updateSeller/{id}','SellerController@updateSeller');
			Route::get('seller/gridvalues','SellerController@getGridValues');
			Route::get('seller/showsellerlist','SellerController@showSellerList');
			Route::get('seller/showchildsellerlist','SellerController@showChildSellerList');
			Route::get('seller/editseller/{se_id}','SellerController@editAction');
			Route::get('seller/authenticationkeys','SellerController@authenticationKeys');
			Route::get('seller/legalentity/delete/','SellerController@legalEntityDelete');
		});
	});
});