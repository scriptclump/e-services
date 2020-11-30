<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'collections', 'namespace' => 'App\Modules\PaymentVsCollections\Controllers'], function () {
       Route::any('/', 'PaymentVsCollectionsController@index');
       Route::any('/GridData' , 'PaymentVsCollectionsController@getPaymentsDashBoardGridData');
       Route::any('/getexportdetails' , 'PaymentVsCollectionsController@getExportDetails');
    });
});