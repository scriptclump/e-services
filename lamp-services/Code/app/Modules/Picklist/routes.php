<?php

Route::group(['middleware' => ['web']], function () {

    Route::group(['namespace' => 'App\Modules\Picklist\Controllers'], function () {
      Route::get('/picklist/index','PicklistController@indexAction');
      Route::get('/picklist','PicklistController@indexAction');
      Route::post('/picklist/createAjax','PicklistController@savePicklist');
      Route::any('/picklist/update','PicklistController@updatePicklistAction');
      Route::any('/picklist/createshipment','PicklistController@createShipmentAction');
      Route::any('/picklist/printPicklist','PicklistController@printPicklist');
  });
});
