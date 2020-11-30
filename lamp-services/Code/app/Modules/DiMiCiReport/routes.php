<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\DiMiCiReport\Controllers'], function () {
        Route::get('/dimici/index', 'DimiciController@indexAction');
        Route::get('/dimici', 'DimiciController@indexAction');
        Route::get('/dimici/grid', 'DimiciController@gridAction');
        Route::any('/dimici/uploadreport', 'DimiciController@uploadAction');
        Route::any('/dimici/downloadreport', 'DimiciBySPController@downloadAction');
    });
});