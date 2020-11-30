<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\HrmsEmployees\Controllers'], function () {
        Route::group(['before' => 'authenticates'], function() {
            Route::get('employee/addemployee', 'EmployeeController@addUsers');
            Route::any('employee/employeeDocs', 'EmployeeController@employeeDocs');
            Route::any('employee/deletedoc/{id}', 'EmployeeController@deleteDoc');
            Route::any('employee/saveProfilePic/{emp_id}', 'EmployeeController@saveProfilePic');
            Route::any('employee/savePanPic/{emp_id}', 'EmployeeController@savePanPic');
            Route::any('employee/saveAadharPic/{emp_id}', 'EmployeeController@saveAadharPic');
            Route::any('employee/saveusers', 'EmployeeController@saveUser');
            Route::get('employee/updateuser', 'EmployeeController@updateUser');
            Route::any('employee/checkEmailExist', 'EmployeeController@checkEmailExist');
            Route::any('employee/editemployee/{userid}', 'EmployeeController@editUsers');
            Route::any('employee/validateemail', 'EmployeeController@validateEmail');
            Route::any('employee/validatemobileno', 'EmployeeController@validateMobileno');
            Route::any('employee/getreportingmanagers', 'EmployeeController@getReportingManagers');
            Route::any('employee/updateemployeepersonaldetails', 'EmployeeController@updateEmployeePersonalDetails');
            Route::any('employee/validateaadharno', 'EmployeeController@validateAadharno');
            Route::any('employee/saveCertificationDetails/{id}', 'EmployeeController@saveCertificationDetails');
            Route::any('employee/deleteCertification/{id}/{empid}', 'EmployeeController@deleteCertification');
            Route::any('myprofile/{id}', 'EmployeeController@myProfile');
            Route::any('employee/updateEmpBankInfo/{id}', 'EmployeeController@updateEmpBankInfo');
            Route::any('employee/myprofile/{id}', 'EmployeeController@myProfile');
            Route::any('employee/uploadEductionDetails/{id}', 'EmployeeController@uploadEductionDetails');
            Route::any('employee/deleteeducation/{id}', 'EmployeeController@deleteeducation');
            Route::any('employee/saveInsuranceDetails/{empid}', 'EmployeeController@saveInsuranceDetails');
            Route::any('employee/saveEmpExperienceInfo/{empid}', 'EmployeeController@saveEmpExperienceInfo');
            Route::any('employee/getexperienceinfo/{empid}', 'EmployeeController@getExperienceInfo');
            Route::any('employee/getEmpExperienceInfobyid/{empid}', 'EmployeeController@getEmpExperienceInfobyid');
            Route::any('employee/deleteExperience/{id}', 'EmployeeController@deleteExperience');
            Route::any('employee/editCertification/{id}', 'EmployeeController@editCertification');
            Route::any('employee/editEducation/{id}', 'EmployeeController@editEducation');

            // Routes for Approval Process
            Route::any('employee/approvalRequest', 'ExitProcessController@exitApprovalByAssigned');
            Route::any('employee/exitprocess/{id}', 'ExitProcessController@exitprocessWithUserId');
            Route::any('employee/gethistoryofapproval/{id}', 'ExitProcessController@getHistoryApprovalData');
            
            // Routes for Dashboard
            Route::get('employee/dashboard', 'EmployeeGridController@indexAction');
            Route::get('employee/employeegrid', 'EmployeeGridController@employeeGrid');
            Route::get('employee/statuscount', 'EmployeeGridController@empStatusCount');
            //route for reports
            Route::any('employee/checkthereportname', 'ReportsController@checkTheReportNameBySelection');

            // Employee Extension detail page
            Route::get('employee/extensions', 'EmployeeGridController@employeeExtensions');           
            Route::get('employee/getextensions', 'EmployeeGridController@getEmpExtensions');           

            // check the official email id exist or tot
            Route::any('employee/checkoffcialemailid', 'ExitProcessController@checkOffcialEmailIdInTable');

            Route::any('employee/saveskills', 'ExitProcessController@saveSkillsEmployee');

            //get employee skills data
            Route::any('employee/getskillsbyemployeeid/{emp_id}', 'ExitProcessController@getSkillsWithId');
            Route::any('employee/deleteskill/{emp_id}', 'ExitProcessController@deleteSkillForEmployee');
            // route for varience and attendance reports
            Route::any('employee/reports', 'ReportsController@reportIndex');
            Route::any('employee/getskilllist', 'ExitProcessController@getSkillNameBySelection');
            Route::any('employee/getifsclist', 'EmployeeController@getIfscListFromDatabase');

            // change password 

            Route::post('employee/checkPassword', 'EmployeeController@checkPassword');
            Route::post('employee/changePassword', 'EmployeeController@changePassword');

            //export employees routes
            Route::any('employee/exportselectionstatus/{statusid}','ExportEmployeeController@exportDataByStatus');
            Route::post('employee/exportemployeesdata', 'ExportEmployeeController@exportEmployeesData');

            Route::any('employee/holidaypage', 'EmployeeController@holidaydaashboard');

            Route::any('employee/getholidaylistBySelection', 'EmployeeController@getholidaylist');

            Route::any('employee/Leavemanage', 'LeaveController@applyleaveManage');

            Route::any('employee/empapplyleave', 'LeaveController@empApplyLeave');
            Route::any('employee/employeedata/{employcode}', 'LeaveController@employeeData');
            Route::any('employee/getappliedleaves/{empid}', 'LeaveController@getAllTheappliedLeaves');
            
            Route::any('employee/withdrawleave/{leavetype}', 'LeaveController@withdrawleave');

            Route::any('employee/pendingleave', 'LeaveController@approvependingleaves');
            Route::any('employee/managerapprovelist/{managerid}', 'LeaveController@getEmployeeAppliedLeaveList');
            Route::any('employee/managerapproveorreject/{status}', 'LeaveController@approveorreject');

            Route::any('employee/gethistoryofallleaves/{managerid}', 'LeaveController@gethistory');

             Route::post('employee/passwordReset', 'EmployeeController@passwordReset');
            Route::post('employee/accessSpecificChangePassword', 'EmployeeController@accessSpecificChangePassword');
            //Import employees routes
            Route::any('employee/importholidayExcel', 'EmployeeController@importHolidayExcel'); 
            Route::any('employee/downloadholidayimport', 'EmployeeController@downloadHolidayImportExcel'); 


        });
    });
});

Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\HrmsEmployees\Controllers'], function () {
        Route::any('/employee/mobileawf/gethrmstkts', 'HrmsMobileApprController@getHrmsTkts');
        Route::any('/employee/mobileawf/awfhrmsdetails', 'HrmsMobileApprController@awfHrmsDetails');
        Route::any('/employee/mobileawf/approvehrmstkt', 'HrmsMobileApprController@approveHrmsTkt');
    });
});