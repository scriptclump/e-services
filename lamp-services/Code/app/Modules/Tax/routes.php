<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'tax', 'namespace' => 'App\Modules\Tax\Controllers'], function () {
        Route::any('/', 'TaxController@index');
        Route::any('/index', 'TaxController@index');
        Route::any('/deleterule/{deleteid}', 'TaxController@deleterule');
        Route::any('/dashboard', 'TaxController@dashboard');
        Route::any('/add', 'TaxController@addAction');
        Route::get('/edit/{taxClassId}', 'TaxController@editAction');
        Route::any('/create', 'TaxController@createAction');
        Route::any('/update', 'TaxController@updateAction');
        Route::any('/uploadexcelsheet', 'TaxController@uploadexcelsheet');
        Route::any('/taxmapdashboard', 'TaxMappingController@dashboardAction');
        Route::any('/products', 'TaxMappingController@dashboardProducts');
        Route::any('/products/update', 'TaxMappingController@productTaxMap');
        Route::any('/showtaxrules', 'TaxController@taxRules');
        Route::any('/formatJson', 'TaxController@formatJson');
        Route::any('/countryname', 'TaxController@countryName');
        Route::any('/onlystatenames', 'TaxController@onlyStateNames');
        Route::any('/getdetailsofproducts/{productid}', 'TaxMappingController@getDetailsOfProducts');
        Route::any('/deletetaxmap', 'TaxMappingController@deleteTaxMap');
        Route::any('/alltaxcodesbystateproductid', 'TaxMappingController@allTaxcodesByStateProductID');
        Route::any('/getTaxMappingStateWise', 'TaxMappingController@getTaxMappingStateWise');
        Route::any('/statusupdateformapping', 'TaxMappingController@updateMappingStatus');
        Route::any('/producttaxclasscodemapping', 'TaxMappingController@productTaxClassCodeMapping');
        Route::any('/avaliabletaxesforstateandproduct', 'TaxMappingController@getAvaliableTaxesForStateAndProduct');
        Route::any('/taxMapByPermissionGrid', 'TaxMappingController@taxMapByPermissionGrid');
        Route::any('/downloadExcelForMapping', 'TaxMappingController@downloadExcelForMapping');
        Route::any('/getbrandsbasedonCats', 'TaxMappingController@getBrandsBasedOnCats');
        Route::any('/getcatsbasedonbarnd', 'TaxMappingController@getCatsBasedOnBarnd');
        Route::any('/taxtypegrid', 'TaxMappingController@taxTypeProducts');
        Route::any('/getAllTaxTypes', 'TaxMappingController@getAllTaxTypes');
        Route::any('/approvealltaxes', 'TaxMappingController@approveAllTaxes');
        Route::any('/allStates', 'TaxMappingController@allStates');
        Route::any('/getavailabletaxesbyproductId', 'TaxMappingController@getAvailableTaxesByProductId');
//        Route::any('/errorLogging', 'TaxMappingController@errorLogging');
        Route::any('/taxapproval/{mappingid}', 'TaxApprovalController@indexAction');
        Route::any('/taxapprovalupdate', 'TaxApprovalController@updateAction');
        Route::any('/accesslogs/{refid}', 'TaxController@accessLogs'); //for showing the logs Data after excel was uploaded
        Route::any('/mappinglogs/{refid}', 'TaxMappingController@taxMappingLogs'); //for showing the logs Data after excel was uploaded
        Route::any('/taxapprovaldashboard/{productd_id}/{state_id}', 'TaxApprovalController@approvalDashboard');
        Route::any('/getuserapprovalstatus', 'TaxApprovalController@getApprovalWorkFlowStatusforTax');
        Route::any('/hsncodes', 'TaxMappingController@hsnCodes');
        Route::any('/downloadhsndetails', 'TaxMappingController@downloadHsnDetails');
        Route::any('/gethsninfo', 'TaxMappingController@getHSNInfo');
        Route::any('/hsncodesinfo', 'TaxMappingController@hsnDetails'); //for HSN tab

    });
});
