<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'jobs', 'namespace' => 'App\Modules\ScheduleJobs\Controllers'], function () {
        Route::any('/inventorysnapshot', 'InventorySnapshotController@index');
    });
});
