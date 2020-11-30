<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Features\Controllers'], function () {
        Route::group(['prefix' => 'rbac', 'before'=>'authenticates'],function(){
            Route::get('/','RbacController@index');
//            Route::any('getRoles/{mfg_id}','RbacController@getRoles');
            Route::get('add','RbacController@create');
            Route::put('saveRole/{role_id}','RbacController@saveRole');
            Route::get('edit/{role_id}','RbacController@edit');
            Route::get('delete/{role_id}','RbacController@delete');
            Route::get('getUserDetail/{user_id}','RbacController@getUser');
            Route::get('getChild/{feature_id}','RbacController@getChild');
            Route::put('saveUser','RbacController@saveUser');
            Route::post('getRoleforInherit/{role_id}','RbacController@getRoleforInherit');
            Route::get('features','RbacController@features');
            Route::get('getdata','RbacController@getdata');
            Route::post('update/{feature_id}','RbacController@update'); 
            Route::post('store','RbacController@store');
            Route::get('editfeature/{feature_id}','RbacController@editFeature');
            Route::post('deletefeature/{feature_id}','RbacController@destroy');
            Route::post('deleteParentfeature/{feature_id}','RbacController@FeatureDelete');
            Route::post('uploadProfilePic','RbacController@uploadProfilePic');
            Route::post('updatemenu','RbacController@updateMenu');
            Route::post('updateactive','RbacController@updateActive');
        });
    });
});
