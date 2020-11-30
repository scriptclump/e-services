<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\HubIgnoreList\Controllers'], function () {
    Route::get('/ignorelist','HubIgnoreListController@index');
    Route::any('/ignorelist/addnewhubignorelist','HubIgnoreListController@addNewHubIgnoreList');
    Route::get('/ignorelist/viewhubignorelist','HubIgnoreListController@viewHubIgnoreList');
    Route::get('/ignorelist/deleteHubIgnoreListById/{id}','HubIgnoreListController@deleteHubIgnoreList');
    });
});
