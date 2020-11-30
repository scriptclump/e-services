<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'inbound', 'namespace' => 'App\Modules\Inbound\Controllers'], function () {
        Route::get('/', ['as' => 'Inbound.index', 'uses' => 'InboundRequestController@indexAction']);
        Route::get('/index', 'InboundRequestController@indexAction');
        Route::get('/index/{status}', 'InboundRequestController@indexAction'); //  for sorting 
        Route::get('/add', 'InboundRequestController@addAction');
        Route::get('/edit', 'InboundRequestController@editAction');
        Route::any('/productsmongodb/', 'InboundRequestController@allProductsFromMongoDb');
        Route::post('/create', ['as' => 'Inbound.create', 'uses' => 'InboundRequestController@createInwardRequest']);
        Route::any('/cancelrequest', 'InboundCancelRequestController@cancelRequest');
        Route::get('/searchorderwise', 'DashboardRequestController@searchorderwise'); //Search Results
        Route::get('/searchorderwise/{status}', 'DashboardRequestController@searchorderwise'); //for sorting
        Route::post('/getInwardDetails', 'DashboardRequestController@getInwardDetails'); //get Details
        Route::get('/getTotalQuantity/{id}', 'DashboardRequestController@getTotalQuantity'); //Total Quantity
        Route::get('/agnrequeststatus', ['as' => 'Inbound.agnrequeststatus', 'uses' => 'ApiController@getAgnRequestStatusNodeJs']);
        Route::any('/updateproductid/{productid}/withproductflag/{flagid}', ['as' => 'Inbound.updateProductFlagMongoDb', 'uses' => 'InboundRequestController@updateProductFlagMongoDb']);
        Route::any('getAllCountHere', 'DashboardRequestController@getAllCountHere');
    });
});
