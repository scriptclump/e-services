<?php
Route::group(['middleware' => ['web']], function () {
   Route::group(['prefix' => '/routingadmin/', 'namespace' => 'App\Modules\RoutingAdmin\Controllers'], function () { 
            Route::any('admin', 'RoutingAdminController@admin');
            Route::any('/', 'RoutingAdminController@index');
            Route::post('getordersbyhub','RoutingAdminController@getOrdersByHub');
            Route::post('generateroutes','RoutingAdminController@generateRoutes'); 
            Route::post('generateshortestpath','RoutingAdminController@generateShortestPath');
            Route::any('generateLoadSheet','RoutingAdminController@generateLoadSheet');
            Route::any('generateViewMap','RoutingAdminController@generateViewMap');
            Route::post('clearroutes','RoutingAdminController@clearRoutes');
            Route::post('getDeliveryExecutiveList','RoutingAdminController@getDeliveryExecutiveList');
            Route::post('assignDeliveryExcecutive','RoutingAdminController@assignDeliveryExcecutive');
            Route::any('generateViewMapAll','RoutingAdminController@generateViewMapAll');
            Route::any('gethubroutes','RoutingAdminController@getRoutesOnHUBapi');
            Route::post('gethistoricalroutes','RoutingAdminController@getHistoricalRoutes');
            Route::post('getroutesinfo','RoutingAdminController@getRoutesInfoFromRouteId');
            Route::any('generatenewroutes','RoutingAdminController@generateNewRoutes');
            Route::any('viewroutehistory','RoutingAdminController@viewRouteHistory');
            Route::get('generateloadsheetonrouteid','RoutingAdminController@generateLoadSheetOnRouteId');
            Route::get('generateviewmaponrouteid','RoutingAdminController@generateViewMapOnRouteId');
            Route::post('changeorderfromroute','RoutingAdminController@changeOrdersFormRoutes');
            Route::post('setdeorvehicleroute','RoutingAdminController@setDeliveryExecutiveAndVehicle');
            Route::post('updateroutedistanceTime','RoutingAdminController@updateRouteDistanceTime');
            Route::post('getsortedorders','RoutingAdminController@getSortedDataApi');
            Route::any('getordershubsocket','RoutingAdminController@getOrdersHubSocket');            
            Route::any('downloadtripsheet','RoutingAdminController@downloadTripSheet');
            Route::post('movetounassigned','RoutingAdminController@moveToUnAssigned');
            Route::post('moveunassignedtoroute','RoutingAdminController@moveUnassignedToRoute');


            /**
             *  Mobile map api
             */
            Route::post('getordersinfocrate','RoutingAdminApiController@getOrdersInfoOncrate');
            Route::post('getvechiclelistapi','RoutingAdminApiController@getAllVehiclesByHubId');
            Route::post('getpositioncrate','RoutingAdminApiController@getPositionCrate');


            /*
                Order Map Dashboard Routes
             */
            Route::post('ordermapviewapi','OrderMapDashboardController@OrderMapDashboard');
            Route::any('ordermapview','OrderMapDashboardController@OrderMapView');
            Route::any('filterelements','OrderMapDashboardController@filterElements');

            /*
               trackroutingdelivery
            */
            Route::any('trackDeliveryExecutive','UserGeoTrackerController@generateTrackEx');
            Route::any('getlastknownlocation','UserGeoTrackerController@getLastKnownLocation');
            Route::any('getgeotrack','UserGeoTrackerController@getGeoTrackHistory');
            Route::any('trackhistorykml/{user_id}/{date}/{file_format}','UserGeoTrackerController@trackHistoryKML');
            Route::any('storeTrackHistory','UserGeoTrackerController@storeTrackHistory'); 
            Route::any('getTrackHistoryByHub','UserGeoTrackerController@getTrackHistoryByHub');
            Route::any('getTrackHistoryByDE','UserGeoTrackerController@getTrackHistoryByDE');
            Route::any('exportTrackDataToExcel','UserGeoTrackerController@exportTrackDataToExcel');
            Route::any('nonQueue','UserGeoTrackerController@nonQueue');
    });
});
