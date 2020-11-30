<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\PickerEfficiencyReport\Controllers'], function () {
        Route::get('/pickereffcreport/index', 'PickerEfficiencyReportController@indexAction');     
        Route::get('/pickereffcreport/griddata', 'PickerEfficiencyReportController@gridData');     
        Route::get('/pickereffcreport/exportgrid', 'PickerEfficiencyReportController@exportData');  
        Route::get('/pickereffcreport/crateutilization', 'CrateUtilizationReportController@crateUtilizationReport');

        Route::get('/pickereffcreport/crateutilization', 'CrateUtilizationReportController@crateUtilizationReport'); 
        Route::get('/pickereffcreport/crateutilization/{start_date}/{end_date}/{email}', 'CrateUtilizationReportController@crateUtilizationReport');
        Route::any('/pickereffcreport/summaryreport', 'PickerEfficiencyReportController@pickerSummaryReport');    

        ///pickereffcreport/
        // /delexeeffcreport/exportgrid   
    });
});
