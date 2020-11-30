<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\AppVersion\Controllers'], function () {
    Route::any('/appversion','AppVersionController@index');
    Route::any('/appversion/versionlist','AppVersionController@versionlist');
    Route::any('/appversion/editversion/{version_id}','AppVersionController@editVersion');
    Route::any('/appversion/addversion','AppVersionController@addVersion');
    Route::any('/appversion/updateversion','AppVersionController@updateVersion');
    });
});
