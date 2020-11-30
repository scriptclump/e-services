<?php
Route::group(['middleware' => ['web']], function () {
	
	Route::group(['prefix' => 'businessunit', 'namespace' => 'App\Modules\BusinessUnit\Controllers'], function () {

		Route::get('/', 'businessUnitDashboardController@businessUnitDashboard');
  		Route::get('/dashboard', 'businessUnitDashboardController@businessUnitDashboard');

		Route::get('/businesstree', 'businessUnitDashboardController@businessTreeData');

		Route::post('/deletebusinessdata', 'businessUnitDashboardController@deleteBusinessTreeData');
		Route::post('/saveeditbusiness', 'businessUnitDashboardController@saveEditBusinessTreeData');
		Route::get('/updatebusinessdata/{updateBusinessID}', 'businessUnitDashboardController@getUpdateBusinessData');
		Route::get('/getbusinesslist/{updateBusinessID}', 'businessUnitDashboardController@getBusinessUnitList');

	});
}); 


Route::group(['middleware' => ['mobile']], function () {
	
	Route::group(['prefix' => 'businessunit', 'namespace' => 'App\Modules\BusinessUnit\Controllers'], function () {
		Route::any('/ledger', 'businessUnitDashboardController@updateSalesLedger');

	});
}); 