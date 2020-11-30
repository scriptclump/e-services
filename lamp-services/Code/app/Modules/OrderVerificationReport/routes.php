<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\OrderVerificationReport\Controllers'], function () {
        Route::get('/orderverificationreport/index', 'OrderVerificationController@indexAction');
        Route::any('/orderverificationreport/exportdata', 'OrderVerificationController@getOrderVerificationData');
          Route::any('/orderverificationreport/viewdata', 'OrderVerificationController@viewVerificationData');
          Route::any('/orderverificationreport/ordercodes', 'OrderVerificationController@orderCodes');  
          Route::any('/orderverificationreport/summaryreport', 'OrderVerificationController@summaryReport');  
    });
});
