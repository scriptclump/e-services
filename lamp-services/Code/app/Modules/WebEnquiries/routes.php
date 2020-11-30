<?php

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\WebEnquiries\Controllers'], function () {
        //webenquiries list
        Route::any('/webenquiries','WebEnquiriesController@index');
        Route::any('/webenquiries/list','WebEnquiriesController@getList');
        Route::any('/webenquiries/delete/{id}','WebEnquiriesController@delete');
        Route::any('/webenquiries/edit/{id}','WebEnquiriesController@edit');
        Route::any('/webenquiries/update','WebEnquiriesController@update');

    });
});