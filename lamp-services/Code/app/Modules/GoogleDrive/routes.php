<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\GoogleDrive\Controllers'], function () {
    Route::any('driveupload/image','GoogleDriveController@uploadImage');   
    Route::any('driveupload/authurl','GoogleDriveController@authUrl'); 	
	Route::any('driveupload/savetoken','GoogleDriveController@saveToken'); 
});
	});
