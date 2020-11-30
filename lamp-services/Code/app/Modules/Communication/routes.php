<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'communication', 'namespace' => 'App\Modules\Communication\Controllers'], function () {
        Route::get('/index', 'CommunicationController@index');        
        Route::get('/getallmessages', 'CommunicationController@getAllMessages');
        Route::get('/add', 'CommunicationController@addAction');
        Route::get('/gethubs', 'CommunicationController@getHubs');
        Route::get('/getbeats', 'CommunicationController@getBeats');
        Route::post('/senddata', 'CommunicationController@sendData');
        Route::get('/pendingprocesses', 'CommunicationController@processPendingMessages');
        Route::get('/download/{id}', 'CommunicationController@downloadAction');
    });
});
