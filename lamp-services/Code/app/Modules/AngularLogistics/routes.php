<?php
Route::group(['middleware' => ['web']], function () {
   Route::group(['prefix' => '/logistics/', 'namespace' => 'App\Modules\AngularLogistics\Controllers'], function () { 
   			Route::any('/', 'AngularLogisticsController@index');
   			Route::any('/reports', 'AngularLogisticsController@reports');
            Route::any('/api', 'AngularLogisticsController@apiData');
            Route::any('/WorkingCapital','AngularLogisticsController@WorkingCapitalData');

            Route::any('/workingcapitalreport','AngularLogisticsController@WorkingCapitalReport');
            
            Route::any('/getdamagereport','AngularLogisticsController@getdamageReport');
            

            Route::post('/getdncleader','AngularLogisticsController@getDncLeader');
   			// routes for HUB OPS
            Route::any('/hubops', 'AngularLogisticsController@hubops');
            
            //routes for sales lead
            Route::any('/getsalesleaddata', 'AngularLogisticsController@getSalesData');

            // Delivery Route
            Route::any('/getdeliveryleaderdata', 'AngularLogisticsController@getDeliveryLeader');

            // Vehicle Details
            Route::any('/getvehiclereport','AngularLogisticsController@getVehicleReport');

            // Vehicle Details
            Route::any('/getpurchaseleaderdata','AngularLogisticsController@getPurchaseLeader');

    });
});