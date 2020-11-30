<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\EmployeeAttendance\Controllers'], function () {
        Route::any('employeeattendance', 'EmployeeAttendanceController@indexAction');
        Route::any('deviceattendance', 'EmployeeAttendanceController@getDeviceAttendance');
        Route::any('getdevices', 'EmployeeAttendanceController@getDeviceData');

         // Routes for emp attandence reports grid
        Route::any('/myattendance', 'AttendanceGridController@myattendanceIndex');
        Route::any('/getattendancegridedata', 'AttendanceGridController@getAttendanceGrideData');
        Route::any('/subordinatesattendance', 'AttendanceGridController@allEmpAttendanceReports');
        Route::any('/getAllAttendancegridedata','AttendanceGridController@getAllAttendanceGrideData');

        Route::any('vehicleattendancereport','VehicleController@vehicleAttReport');
        Route::any('vehicleattdownload','VehicleController@vehicleattdownload');

    });
});

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\EmployeeAttendance\Controllers'], function () {
        Route::any('employeeattendance/attendancehistory', 'EmployeeAttendanceController@attendanceHistory');
        Route::any('employeeattendance/getmysubordinates', 'EmployeeAttendanceController@getMySubordinates');
    });
});
