<?php

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\LogisticReports\Controllers'], function () {
        Route::any('logisticsummaryreportsapi', 'LogisticReportApiController@LogisticSummaryApi');
        Route::any('cratesummaryreport', 'LogisticReportApiController@crateSummaryReport');
    });
});
