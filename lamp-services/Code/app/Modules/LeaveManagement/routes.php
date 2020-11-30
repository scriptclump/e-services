<?php

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\LeaveManagement\Controllers'], function () {
        Route::any('/leavemanagement/leavemasterinfo', 'LeaveManagementController@leaveMasterInfo');
        Route::any('/leavemanagement/leaverequest', 'LeaveManagementController@leaveRequest');
        Route::any('/leavemanagement/dayscalculation', 'LeaveManagementController@daysCalculation');
        Route::any('/leavemanagement/leavehistory', 'LeaveManagementController@leaveHistory');
        Route::any('/leavemanagement/pendingapproval', 'LeaveManagementController@getPendingApprovals');
        Route::any('/leavemanagement/updateleavestatus', 'LeaveManagementController@updateLeaveStatus');

        //LeaveManagement
        Route::any('/leavemanagement/empleavehistory','LeaveHistoryController@leavehistory');
        Route::any('/leavemanagement/list','LeaveHistoryController@getList');
    });
});
