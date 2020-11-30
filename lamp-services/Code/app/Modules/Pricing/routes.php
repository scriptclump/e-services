<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'pricing', 'namespace' => 'App\Modules\Pricing\Controllers'], function () {
		Route::get('/', 'pricingDashboadController@pricingDashboard');
		Route::get('/dashboard', 'pricingDashboadController@pricingDashboard');
		Route::get('/pricingdata', 'pricingDashboadController@pricingData');
		Route::get('/getlist','pricingDashboadController@getlist');	
		Route::get('/getbrandsasmanufac/{manufac_id}','pricingDashboadController@getBrandsAsManufac');
		Route::get('/getproductasbrand/{brand_id}','pricingDashboadController@getProductAsBrand');
		Route::get('/getproductbyid/{myId}', 'pricingDashboadController@getProductbyID');
		Route::post('/deletepricedetails', 'pricingDashboadController@deletePriceDetails');

		Route::post('/addeditslabdata', 'pricingDashboadController@addEditSlabData');
		Route::get('/getupdatedata/{priceid}', 'pricingDashboadController@getUpdateData');
		Route::get('/getrightsideinfo/{prdid}/{stateid}', 'pricingDashboadController@getRightSideInfo');

		/*--------------------------Upload Promotion File------------------*/
  		Route::post('/uploadpriceslab', 'uploadPriceSlabFiles@uploadPriceSlab');


  		Route::post('/savecashbackdata', 'pricingDashboadController@saveCashBackData');
  		Route::post('/deletecashbackdata', 'pricingDashboadController@deleteCashBackData');

  			
  		Route::post('/downloadexcelforslabprice', 'uploadPriceSlabFiles@downloadExcelWithData');

  		// excel for cash back 
  		Route::post('/downloadexcelforcashback', 'uploadCashbackController@downloadCashbackExcelWithData');
  		// excel upload for cash back


  		Route::post('/uploadcashbacks', 'UploadCashbackFile@uploadCashbackdata');
       
       /*pricingmaster routes*/
        Route::any('/pricemaster','pricingMasterController@Pricemanager');
  		Route::any('/productlist','pricingMasterController@PricemanagerData');
  		Route::any('/bulist','pricingMasterController@BusinessunitList');
  		Route::any('/getpricingdata','pricingMasterController@Exportpricingdata');
      Route::any('/importpricingdata','pricingMasterController@Importpricingdata');
      Route::any('/apob_dc_list','pricingMasterController@ApobDcList');
      Route::post('/upload_esp_price','uploadPriceSlabFiles@upload_esp_price');



  		
	});
}); 