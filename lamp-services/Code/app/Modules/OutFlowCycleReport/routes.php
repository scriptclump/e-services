<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\OutFlowCycleReport\Controllers'], function () {
        Route::get('/outflowcyclereport/index', 'OutFlowCycleReportController@indexAction');     
        Route::get('/outflowcyclereport/griddata', 'OutFlowCycleReportController@gridData');     
        Route::get('/outflowcyclereport/exportgrid', 'OutFlowCycleReportController@exportData');     
    });
});
