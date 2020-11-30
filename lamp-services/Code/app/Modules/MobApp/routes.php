<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'mobapp', 'namespace' => 'App\Modules\MobApp\Controllers'], function () {
		Route::get('/', 'appVersionController@appVersionIndex');
		Route::get('/appversionlist', 'appVersionController@appVersionList');
		Route::get('/addappversion', 'appVersionController@addAppVersion');
		Route::post('/saveappversion', 'appVersionController@saveAppVersion');
		Route::post('/deleteappversion', 'appVersionController@deleteAppVersion');
		Route::get('/updateappversion/{updateId}', 'appVersionController@updateData');
		Route::post('/updateId', 'appVersionController@updatewithId');
	});
}); 