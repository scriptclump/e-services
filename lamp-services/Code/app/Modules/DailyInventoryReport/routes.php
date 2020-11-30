<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\DailyInventoryReport\Controllers'], function () {
        Route::get('/dailyinventory/index', 'DailyInventoryReportController@indexAction');
      
    });
});