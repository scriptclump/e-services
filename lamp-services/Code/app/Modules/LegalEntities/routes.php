<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\LegalEntities\Controllers'], function () {
		Route::group(['before' => 'authenticates'], function() {    
		    Route::get('legalentity/index', 'LegalEntityController@indexAction');
		    Route::get('legalentity/viewProfile/{id}', 'LegalEntityController@viewProfile');
		    Route::post('legalentity/updateDocs', 'LegalEntityController@updateDocs');
		    Route::get('legalentity/adminViewProfile/{id}', 'LegalEntityController@adminViewProfile');
		    Route::post('legalentity/saveProfilePic', 'LegalEntityController@saveProfilePic');
		    Route::get('legalentity/add', 'LegalEntityController@addAction');
		    Route::get('legalentity/edit', 'LegalEntityController@editAction');
		    Route::post('legalentity/save', 'LegalEntityController@saveAction');
		    Route::post('legalentity/resend','LegalEntityController@resendEmail');
		    Route::post('legalentity/savePassword', 'LegalEntityController@savePassword');
		    Route::post('legalentity/changePassword', 'LegalEntityController@changePassword');
		    Route::post('signup/savebusinessinfo', 'LegalEntityController@saveBusinessInfo');
		    Route::post('signup/checkUnique', 'LegalEntityController@checkUnique');
		    Route::post('legalentity/checkPassword', 'LegalEntityController@checkPassword');
		    Route::post('legalentity/saveBasicInfo', 'LegalEntityController@saveBasicInfo');
		    Route::post('legalentity/updateBusinessInfo', 'LegalEntityController@updateBusinessInfo');
		    Route::post('legalentity/saveBankInfo', 'LegalEntityController@saveBankInfo');
		    Route::get('profile/ifscs/{bank_name}', 'LegalEntityController@getIfscs');
		    Route::get('profile/bank_info/{ifsc}', 'LegalEntityController@getBankInfo');

			//state_code_city related routes
		    Route::any('/legalentities/index','StateController@index');
    		Route::any('/legalentities/list','StateController@getList');
    		Route::any('/legalentities/edit/{id}','StateController@edit');
    		Route::any('/legalentities/add','StateController@add');
    		Route::any('/legalentities/update','StateController@update');
    		Route::any('/legalentities/delete/{id}','StateController@delete');
    		Route::any('/legalentities/validatestatename','StateController@validateStateName');
    		Route::any('/legalentities/validatecityname','StateController@validateCityName');
    		Route::any('/legalentities/noaccess','StateController@noaccess');
    		//Route::any('/legalentities/statecode/{id}','StateController@getData');
    		
    		
		});
	});
});