<?php

Route::get('login', 'AuthenticationController@index');
Route::any('register','AuthenticationController@register');
Route::get('signup', 'AuthenticationController@signup');
Route::group(['middleware' => ['web']], function () {
    Route::get('signup/{legal_id}/{userId}', 'AuthenticationController@bussinessSignup');
}); 
Route::any('login/checkAuth', 'AuthenticationController@checkAuth');
Route::get('logout', 'AuthenticationController@logout');
Route::post('forgot', 'AuthenticationController@forgot');
Route::get('password/reset/{token}', 'AuthenticationController@reset');
Route::any('passwordreset', 'AuthenticationController@passwordreset');
Route::any('login/setSessionData/{mfgId}', 'AuthenticationController@setSessionData');
Route::post('resetPassword/checkEmail', 'AuthenticationController@checkEmail');
 Route::group(['before' => 'authenticates'], function() {        
    Route::get('users/add', 'AuthenticationController@addUser');
    Route::get('users/addnew', 'AuthenticationController@addNewUser');
    Route::get('users/edit/{user_id}', 'AuthenticationController@editUser');
    Route::get('users/delete/{user_id}', 'AuthenticationController@deleteUser');    
    Route::post('users/save/{user_id}', 'AuthenticationController@saveUser');
    Route::post('users/newsave', 'AuthenticationController@newsave');
    Route::post('users/switchUser/{id}', 'AuthenticationController@switchUser');
    Route::post('users/switchAdmin', 'AuthenticationController@switchAdmin');
    Route::post('users/uploadProfilePic', 'AuthenticationController@uploadProfilePic');
});
