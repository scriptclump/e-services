<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\DeviceDetails\Controllers'], function () {
    Route::get('/devicedetails','DeviceDetailsController@index');
    Route::any('/devicedetails/devicedetailslist','DeviceDetailsController@devicedetailslist');
    Route::any('/devicedetailswarehouse', 'DeviceDetailsController@DeviceWarehouse');
    Route::any('/devicedetailshubs', 'DeviceDetailsController@DeviceHubs');
    });
});
