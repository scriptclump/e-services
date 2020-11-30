<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Attendance\Controllers'], function () {
        Route::any('attendreports', 'AttendanceController@indexAction');
        Route::any('attendreports/getattendancereports', 'AttendanceController@getAttendanceReports');
        Route::any('attendreports/excelattendancereports', 'AttendanceController@excelAttendanceReports');
//        Route::any('attendreports/getffnames', 'ReportsController@getFFNames');
    });
});
