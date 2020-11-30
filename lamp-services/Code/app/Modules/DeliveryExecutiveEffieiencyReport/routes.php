<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\DeliveryExecutiveEffieiencyReport\Controllers'], function () {
        Route::get('/delexeeffcreport/index', 'DeliveryExecutiveEffieiencyReportController@indexAction');     
        Route::get('/delexeeffcreport/griddata', 'DeliveryExecutiveEffieiencyReportController@gridData');     
        Route::get('/delexeeffcreport/exportgrid', 'DeliveryExecutiveEffieiencyReportController@exportData');     
        // /delexeeffcreport/exportgrid   
    });
});
