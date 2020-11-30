<?php

//Route::get('inward','Inbound\Controllers\InwardController@index');
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Categories\Controllers'], function () 
	{
		// Route::get('categories/index', 'CategoryController@indexAction');

		
		Route::any('categories/uniqueNameValidation', 'CategoryController@uniqueNameValidation');
		Route::any('categories/uniqueValidation', 'CategoryController@uniqueValidation');
		Route::get('categories/index', 'CategoryController@treegrid');
		Route::get('categories/getParentCategory/{cat_id}', 'CategoryController@getParentCategory');
		Route::get('categories/getSegments/{cat_id}', 'CategoryController@getSegments');
        Route::get('categories/getCategoryImage/{cat_id}', 'CategoryController@getCategoryImage');
		Route::any('categories/checkCategoryId', 'CategoryController@checkCategoryId');
		
		Route::any('categories/getparentcats', 'CategoryController@getParentCategories');
		Route::any('categories/getchildcats', 'CategoryController@getChildCategories');
		Route::any('categories/treeCats', 'CategoryController@treeCats');
		Route::any('categories/saveparentcategory', 'CategoryController@addNewcategory');
		Route::get('categories/deletecategory/{category_id}', 'CategoryController@deleteCategory');
		//
		Route::post('categories/downloadexcelforcategorymargins', 'CategoryController@downloadExcel');
		Route::post('categories/uploadcatmargin', 'CategoryController@uploadCatmargin');
		
	});
});
