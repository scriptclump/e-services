<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\MeanProducts\Controllers'], function () {
        Route::get('/meanproducts/index', 'MeanProductsController@indexAction');
        Route::get('/meanproducts/griddata', 'MeanProductsController@gridData');
        Route::get('/meanproducts/getexport', 'MeanProductsController@exportExcel');
        Route::get('/meanproducts/dmsemail', 'DmsEmailSetupController@dmsRepMail');
        Route::any('/meanproducts/dmsemailsetup', 'DmsEmailSetupController@dmsEmailSetup');
    });
});