<?php
Route::group(['middleware' => ['mobile']], function () {

 Route::group(['prefix'=>'mfc','namespace' =>'App\Modules\MFC\Controllers'],function () {

		Route::post('/register','MFCRegistrationController@registration');

	});
}); 