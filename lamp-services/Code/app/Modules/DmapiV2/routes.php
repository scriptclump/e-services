<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => '/dmapi/v2/', 'namespace' => 'App\Modules\DmapiV2\Controllers'], function () {	
    		Route::any('checktaxapi', 'Dmapiv2Controller@checkTaxApi');
	        Route::any('{api_name}', 'Dmapiv2Controller@checkUserPermission');
	        Route::any('rectifyTaxByOrder/{order_id}', 'Dmapiv2Controller@rectifyTaxByOrderId');
         //Failed Order processing interface
	        Route::any('/fo/failedorder', 'failedorderController@failedorderinterface');
	        Route::any('/fo/failedorderlist', 'failedorderController@failedorderlist');
	        Route::any('/fo/edit/{id}','failedorderController@edit');
	        Route::any('/fo/placeorder','failedorderController@processFailedOrder');
	        Route::any('/fo/updatecomments','failedorderController@updateOrderStatus');
    });
});
