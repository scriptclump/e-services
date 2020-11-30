<?php
Route::group(['middleware' => ['mobile']], function () {
	Route::group(['prefix' => 'h2haxis', 'namespace' => 'App\Modules\H2HAxis\Controllers'], function () {
		

		// ====================================================================
		// Routes for H2H APIs
		// ====================================================================
		Route::post('/API/sendPaymentRequestToAxis', 'h2hAxisAPIController@sendPaymentRequestToAxis');
		Route::post('/API/h2hCallBackResponseAxis', 'h2hAxisAPIController@h2hCallBackResponseAxis');
		
	});
});