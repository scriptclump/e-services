<?php
Route::group(['middleware' => ['web']], function () {

 Route::group(['prefix'=>'legalentity','namespace' =>'App\Modules\LegalEntity\Controllers'],function () {

		Route::get('/','LegalEntityController@index');

		Route::any('legalentity','LegalEntityController@legalentityGridData');

		Route::any('/details/{id}','LegalEntityController@legalentityDetails');

		Route::any('/editData/{id}','LegalEntityController@editGridDetails');

		Route::post('/updateintotable','LegalEntityController@updateintotable');

		Route::post('/updateCustomerInfo','LegalEntityController@updateCustomerInfo');

		Route::any('/getUsersList','LegalEntityController@getUsersList');

		Route::any('/warehousesList','LegalEntityController@warehousesList');

		Route::any('/savevianodeapilegalentity','LegalEntityController@createLegalEntity');	

		Route::get('/stockistpayment/{leid}','LegalEntityController@StockistPaymentsByLeID');	

		Route::any('/savestockistamount','LegalEntityController@saveStockistDetails');

		Route::any('/transactionhistory/{userid}','LegalEntityController@getTransactionhistory');

		Route::any('/validator','LegalEntityController@validateMobileno');
		
		Route::any('/uploadDoc','LegalEntityController@uploadDoc');	

		Route::any('/deleteDoc','LegalEntityController@deleteDoc');

		Route::any('/viewdetails/{id}','LegalEntityController@legalentityDetailsView');

		Route::post('/getcitiesbystateid','LegalEntityController@getCitiesByStateId');

		Route::post('/getdcfccode','LegalEntityController@getDcFcCode');

		Route::post('/getlegalentityidfordc','LegalEntityController@getLegalentityIdforDc');

		Route::post('/getcityname','LegalEntityController@getCityName');

		Route::any('/creditlimitapproval','LegalEntityController@creditLimitApproval');
		//
		Route::any('/creditdebitapproval','LegalEntityController@creditDebitApproval');
		Route::any('/creditdetails/{Id}', 'LegalEntityController@creditDetails');
		Route::any('/approvalSubmit','LegalEntityController@approvalSubmit');
		//
		Route::any('/generateCostcenter','LegalEntityController@generateCostcenter');

		Route::any('/approvedCreditLimit','LegalEntityController@approvedCreditLimit');
		//Delete Payment
		Route::any('/deletePayment/{pay_id}', 'LegalEntityController@deletePayment');

		Route::any('/stockistLedger/{legalentityId}','LegalEntityController@getStockistLedger');
		Route::any('/exportData','LegalEntityController@exportData');

		// update balance test route
		Route::any('/updatebalance/{legalentityId}/{up?}','LegalEntityController@updateBalanceAmount');
		Route::any('/creditLimitHistory/{leId}','LegalEntityController@getCreditHistroy');
		Route::any('/creditLimitGrid','LegalEntityController@creditLimitHistory');
		//removing creditlimit
		Route::any('/rmvcredit/{id}','LegalEntityController@rmvCreditLimit');
		Route::any('/editcredit/{id}','LegalEntityController@editCreditLimit');
		Route::any('/updatecredit','LegalEntityController@updateCredit');
		Route::any('/emailValidator','LegalEntityController@getEmailValidator');

		//check gst state code
		Route::post('/checkgstin', 'LegalEntityController@checkGstStateCode');
		Route::any('/warehouseValidator','LegalEntityController@getWarehouseValidator');
	});
}); 