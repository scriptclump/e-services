<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\HubInventory\Controllers'], function () {
        Route::any('hubinventory/index', 'HubInventoryController@indexAction');
        Route::any('hubinventory/gethubinventory', 'HubInventoryController@getHubInventory');
        Route::any('hubinventory/gethuborderinventory', 'HubInventoryController@getHubOrderInventory');
        Route::any('hubinventory/hubinventoryxls', 'HubInventoryController@hubInventoryXls');
        Route::any('gethubs', 'HubInventoryController@getHubs');
    });
});
