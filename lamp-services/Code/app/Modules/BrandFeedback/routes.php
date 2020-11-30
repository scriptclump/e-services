<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'brandfeedback' , 'namespace' => 'App\Modules\BrandFeedback\Controllers'], function () {
        
        //Brand Feedback list
        Route::any('/','BrandFeedbackController@index');
        Route::any('/list','BrandFeedbackController@getList');
        Route::any('/delete/{id}','BrandFeedbackController@delete');
        Route::any('/edit/{id}','BrandFeedbackController@edit');
        Route::any('/update','BrandFeedbackController@update');
        Route::any('/export', 'BrandFeedbackController@downloadBrandFeedbackExcel');

    });
});