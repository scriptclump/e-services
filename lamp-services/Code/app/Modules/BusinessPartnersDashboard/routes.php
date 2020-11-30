<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'businessPartners', 'namespace' => 'App\Modules\BusinessPartnersDashboard\Controllers'], function () {
        Route::any('/', 'BusinessPartnersDashboardController@index');//used to get grid data from procedure
        Route::post('/', 'BusinessPartnersDashboardController@getIndexData');
        Route::any('/getbu','BusinessPartnersDashboardController@getBuUnit');//used to get Business unit details from db   
        Route::any('/GridData', 'BusinessPartnersDashboardController@getPartnersDashBoardGridData');//used to get stockistSales
        Route::any('/getPartnersReport', 'BusinessPartnersDashboardController@getPartnersReport');//used to get stockistSales
    });
});
