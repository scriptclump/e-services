<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\NONSellingInventory\Controllers'], function () {
        Route::get('/nonsellinginventory/index', 'NONSellingInventoryController@indexAction');
        Route::get('/nonsellinginventory/filteredData', 'NONSellingInventoryController@getNonSellingResults');
        Route::get('/nonsellinginventory/exportData', 'NONSellingInventoryController@getNonSellingResultsExport');


        
    });
});


