<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\SerialNumber\Controllers'], function () {
    Route::get('/sno/index','SerialNumberController@indexAction');
    
	});
});
