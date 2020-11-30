<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\MasterLookup\Controllers'], function () {
		Route::group(['before' => 'authenticates'], function() {  
			Route::get('lookupcategories','ConfigurationController@lookupCategory');
			Route::get('lookupcategories/add_lookupcategories/','ConfigurationController@addLookupCategories');
			Route::get('lookupcategories/edit_lookupcategories/{id}','ConfigurationController@editLookupCategories');


			Route::get('lookups','ConfigurationController@lookups');
			Route::get('lookups/getTreeData','ConfigurationController@getTreeData');
			Route::get('lookups/create','ConfigurationController@createLookup');
			Route::get('lookups/edit/{id}','ConfigurationController@editLookup');
			Route::put('lookups/updateLookup/{id}','ConfigurationController@updateLookup');
			Route::post('lookups/deleteLookup/{id}','ConfigurationController@deleteLookup');
			Route::post('lookups/storeLookup/','ConfigurationController@storeLookup');

			Route::get('lookupscategory','ConfigurationController@lookupsCategory');
			Route::get('lookupscategory/show','ConfigurationController@showCategory');
			Route::post('lookupscategory/store/','ConfigurationController@saveCategory');
			Route::put('lookupscategory/update/{id}','ConfigurationController@updateCategory');
			Route::get('lookupscategory/addLookCat','ConfigurationController@addLookCat');
			Route::get('lookupscategory/edit/{id}','ConfigurationController@editCategory');
			Route::post('lookupscategory/delete/{id}','ConfigurationController@deleteCategory');
			Route::any('lookupscategory/validatename','ConfigurationController@validatename');


			Route::get('email','ConfigurationController@emailTemplate');
			Route::get('email/show','ConfigurationController@showTemplate');
			Route::get('email/add','ConfigurationController@addEmailTemplate');
			Route::get('email/edit/{id}','ConfigurationController@editEmailTemplate');
			Route::post('email/store/','ConfigurationController@saveEmailTemplate');
			Route::put('email/update/{id}','ConfigurationController@updateEmailTemplate');
			Route::post('email/delete/{id}','ConfigurationController@destroyEmailTemplate');

			Route::get('pricemaster','ConfigurationController@priceMaster');
			Route::get('pricemaster/show','ConfigurationController@showPriceMaster');
			Route::get('pricemaster/add','ConfigurationController@addPriceMaster');
			Route::post('pricemaster/store','ConfigurationController@storePriceMaster');
			Route::get('pricemaster/edit/{id}','ConfigurationController@editPriceMaster');
			Route::put('pricemaster/update/{id}','ConfigurationController@updatePriceMaster');
			Route::post('pricemaster/delete/{id}','ConfigurationController@destroyPriceMaster');

			Route::get('languages','ConfigurationController@languages');
			Route::get('languages/add_language/','ConfigurationController@addLanguage');
			Route::get('languages/edit_language/{lang_id}','ConfigurationController@editLanguage');
		});
	});
});


