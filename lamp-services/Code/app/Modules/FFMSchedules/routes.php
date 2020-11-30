<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\FFMSchedules\Controllers'], function () {
        Route::any('ffmschedules/index', 'FFMScheduleController@indexAction');
        Route::post('ffmschedules/downloadexcelforffmschedules', 'FFMScheduleController@downloadExcel');
        Route::any('ffmschedules/getschedules', 'FFMScheduleController@getSchedules');
        Route::any('ffmschedules/uploadffmschedules', 'FFMScheduleController@uploadffmschedules');
        Route::any('ffmschedules/add', 'FFMScheduleController@addSchedule');
        Route::any('ffmschedules/edit/{id}', 'FFMScheduleController@editSchedule');
        Route::any('ffmschedules/update', 'FFMScheduleController@updateSchedule');
        Route::any('ffmschedules/delete/{id}', 'FFMScheduleController@deleteSchedule');
        Route::any('ffmschedules/getwarehouse/{id}', 'FFMScheduleController@getWarehouse');
        Route::any('ffmschedules/getpincodes', 'FFMScheduleController@getPincodes');
        Route::any('ffmschedules/exportffmschedules', 'FFMScheduleController@exportSchedules');
        Route::any('ffmschedules/getFFMList', 'FFMScheduleController@getFFMList');
        Route::any('ffmschedules/getcitynames', 'FFMScheduleController@getCitiesList');
    });
});



 