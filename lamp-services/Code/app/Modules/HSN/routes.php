<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'hsn', 'namespace' => 'App\Modules\HSN\Controllers'], function () {
    Route::any('/index','HSNController@index');
    Route::any('/list','HSNController@getList');
    Route::any('/edit/{id}','HSNController@edit');
    Route::any('/add','HSNController@add');
    Route::any('/update','HSNController@update');
    Route::any('/delete/{id}','HSNController@delete');
    Route::any('/validatehsncode','HSNController@validateHsnCode');
    });
});
