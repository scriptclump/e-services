<?php
   
Route::group(['middleware' => ['mobile']], function () {
    Route::group(['prefix' => '/discountcashback/','namespace' => 'App\Modules\DiscountCashback\Controllers'], function () {
    	
    	Route::any('getcashbackapplied', 'CashbackController@getCashbackApplicable');
    	Route::any('getcashbackbyorder', 'CashbackController@getCashbackApplicableByOrder');
    });
});
