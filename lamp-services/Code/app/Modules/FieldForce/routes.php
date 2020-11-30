<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'fftarget', 'namespace' => 'App\Modules\FieldForce\Controllers'], function () {
  		Route::get('/dashboard', 'fieldForceDashboardController@fieldForceDashboard');
  		Route::get('/showfieldforceDetails', 'fieldForceDashboardController@showFieldForceDashboardDetails');
  		Route::post('/getfieldforcedata/{ffid}', 'fieldForceDashboardController@getFieldForceData');
  		Route::post('/savefieldforcedata', 'fieldForceDashboardController@saveFieldForcedata');
  		Route::post('/deletefieldforce', 'fieldForceDashboardController@deleteFieldForce');
  		Route::post('/loadfieldforcedata', 'fieldForceDashboardController@loadFieldForceData');
  		Route::post('/getuserdetailswithid/{ffid}', 'fieldForceDashboardController@getUserDetailsWithId');

  		
  		
  		
});
}); 