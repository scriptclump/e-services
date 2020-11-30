<?php


Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Vouchers\Controllers'], function () {
    
	Route::get('/savevouchers','VouchersController@saveVoucherAction');
	
	});
});
