<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\RouteIntegrate\Controllers'], function () {
			Route::get('routeintegrate/index','RoutingIntegrateController@index');
			
		
	});
});