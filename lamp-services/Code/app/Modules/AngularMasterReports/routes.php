<?php
Route::group(['middleware' => ['web']], function () {
   Route::group(['prefix' => '/master-report/', 'namespace' => 'App\Modules\AngularMasterReports\Controllers'], function () { 
            //call angular route
   			Route::any('/', 'AngularMasterReports@index');
        });
});


?>