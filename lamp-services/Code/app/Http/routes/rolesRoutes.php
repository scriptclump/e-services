<?php
Route::group(['prefix' => 'roles', 'before'=>'authenticates'],function(){
    Route::get('index','RolesController@index');
   // Route::get('roles/index', 'RolesController@indexAction');
     
    Route::any('getRoles/{mfg_id}','RolesController@getRoles');
    Route::get('add','RolesController@create');
    //Route::put('saveRole/{role_id}','RolesController@saveRole');
    Route::get('saveRole/{role_id}','RolesController@saveRole');
    Route::get('updaterole/{role_id}','RolesController@Updaterole');
    Route::get('edit/{role_id}','RolesController@edit');
    Route::get('delete/{role_id}','RolesController@delete');
    Route::get('getUserDetail/{user_id}','RolesController@getUser');
    Route::get('getChild/{feature_id}','RolesController@getChild');
    Route::put('saveUser','RolesController@saveUser');
    Route::post('getRoleforInherit/{role_id}','RolesController@getRoleforInherit');
    Route::get('features','RolesController@features');
    Route::get('getdata','RolesController@getdata');
    Route::put('update/{feature_id}','RolesController@update');
    Route::post('store','RolesController@store');
    Route::get('editfeature/{feature_id}','RolesController@editFeature');
    Route::post('deletefeature/{feature_id}','RolesController@destroy');
    Route::post('deleteParentfeature/{feature_id}','RolesController@FeatureDelete');
    Route::post('uploadProfilePic','RolesController@uploadProfilePic');
    Route::any('getiggridusers','RolesController@getIgGridUsers');
    Route::get('insertrolepermission','RolesController@insertRoleperMission');
    Route::get('insertusersrole','RolesController@insertUsersroles');
    Route::get('getpermissionids','RolesController@getPermissionIds');
    
});
