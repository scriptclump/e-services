<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'promotions', 'namespace' => 'App\Modules\Promotions\Controllers'], function () {

		/*---------------- Route Needed for Template ------------------------------------------*/
		/* dashboard */
		Route::get('/template', 'promotionController@promotionsIndex');
		Route::get('/promotiondata', 'promotionController@promotionData');

		/* add / edit / delelte */
		Route::get('/addpromotion', 'promotionController@addPromotion');
		Route::get('/updatepromotion/{updateId}', 'promotionController@updateData');
		Route::post('/updateid', 'promotionController@updatewithId');
		Route::post('/savepromotion', 'promotionController@savePromotion');
		Route::post('/deletedata', 'promotionController@deleteData');
		

		/*---------------- Route Needed for Promotion Details ------------------------------------------*/

		Route::get('/addnewpromotion', 'AddpromotionController@addnewPromotion');
		Route::post('/savenewpromotion', 'AddpromotionController@savenewpromotion');
		Route::get('/productgrid', 'AddpromotionController@productGridDetails');
		Route::get('/getfreeproductlist', 'AddpromotionController@getFreeProductList');
		Route::get('/getpack/{id}', 'AddpromotionController@getPackData');



		/* add / edit / delelte */

		Route::get('/', 'promotionDetDashboardController@viewPromotionDetails');
		Route::get('/viewpromotiondetails', 'promotionDetDashboardController@viewPromotionDetails');
		Route::get('/showpromotionDetails', 'promotionDetDashboardController@showpromotionDetails');
		Route::get('/editnewpromotion/{updateId}', 'updatePromotionController@editNewPromotion');
		Route::post('/updatenewpromotion', 'updatePromotionController@updatenewpromotion');
		Route::post('/deletepromotiondetails', 'promotionDetDashboardController@deletepromotiondetails');


		/*--------------------------Common Method Routes------------------*/
		Route::get('/getpromotiondetails', 'viewpromotionDetailsController@getPromotionDetails');

		/*--------------------------Upload and Download Routes------------------*/
		Route::post('/downloadexcelforslabpromotion', 'uploadPromotionFiles@downloadExcelWithData');
		Route::post('/uploadslabpromotion', 'uploadPromotionFiles@uploadPromotionSlab');
		Route::get('/getbrandsasmanufac/{manufac_id}','uploadPromotionFiles@getBrandsAsManufac');

		Route::get('/getmanufac','AddpromotionController@getManufac');


		/*-----------------------slab report template---------------------------*/
		Route::get('/slabreport', 'promotionSlabController@slabReportdashboard');
		Route::get('/reportdata', 'promotionSlabController@slabReportData');
		Route::post('/slabreportdates', 'promotionSlabController@slabExceldataDownload');

		Route::any('/getallpromotiondata', 'promotionDetDashboardController@getAllPromotionDetailsData');
		Route::any('/getproductPackData/{id}','AddpromotionController@getproductPackData');
		Route::any('/getbrandsbymanufac','AddpromotionController@getBrandsAsManufac');
		Route::any('/discounton/{id}','AddpromotionController@getTradeItems');

	});
}); 