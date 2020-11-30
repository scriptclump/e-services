<?php
Route::group(['middleware' => ['web']], function () {

 Route::group(['prefix'=>'mfccompany','namespace' =>'App\Modules\MFCcompany\Controllers'],function () {

		Route::get('/','MFCCompanyController@index');

		Route::any('mfCcompany','MFCCompanyController@companyGridData');

		Route::any('/details/{id}','MFCCompanyController@companyDetailsInGrid');

		Route::any('/editData/{id}','MFCCompanyController@editGridDetails');

		Route::post('/updateUsersData','MFCCompanyController@updateUsersInfo');

		Route::post('/updateCustomerInfo','MFCCompanyController@updateCustomerInfo');

		Route::any('/getUsersList','MFCCompanyController@getUsersList');

		Route::any('/addingUsers','MFCCompanyController@CreatingUsers');

		Route::get('/getuserdata/{id}','MFCCompanyController@getUsersData');

		Route::any('/validator','MFCCompanyController@validateMobileno');

	});
}); 