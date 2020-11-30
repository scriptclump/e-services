<?php
Route::group(['middleware' => ['mobile']], function () {

	Route::group(['prefix' => 'tallyconnector', 'namespace' => 'App\Modules\TallyConnector\Controllers'], function () {

		// LEDGER MASTER ROUTE
		Route::post('/createledgergroup', 'tallyLedgerMasterController@createLedgerGroupMaster');
		Route::post('/createledgermaster', 'tallyLedgerMasterController@createLedgerMaster');
		Route::post('/editledgergroupmaster', 'tallyLedgerMasterController@editLedgerGroupMaster');
        Route::post('/editledgermaster', 'tallyLedgerMasterController@editLedgerMaster');

        // LEDGER BLUK OPERATION
        Route::post('/alterTallyLedgerCreditors', 'tallyTransactionController@alterTallyLedgerCreditors');
        Route::post('/editLedgerMaster', 'tallyLedgerMasterController@editLedgerMaster');

		// VOUCHER MASTER ROUTE
		Route::post('/createvouchers', 'tallyVoucherMasterController@createVouchers');


		//COST CATEGORIES ROUTE
		Route::post('/createcostcategoriesmaster', 'tallyCostMasterController@createCostCategoriesMaster');
		Route::post('/editcostcategoriesmaster', 'tallyCostMasterController@editCostCategoriesMaster');


		//COST CENTRE ROUTE
	    Route::post('/createcostcentresmaster', 'tallyCostMasterController@createCostCentresMaster');
		Route::post('/editcostcentresmaster', 'tallyCostMasterController@editCostCentresMaster');

		// FETCH LEDGER MASTER ROUTE
		Route::post('/fetchtallyledger', 'tallyFetchLedgerMasterController@fetchLedgerMaster');

		// ROUTE FOR REPORTING
		Route::post('/fetchLedgerWithBalance', 'tallyFetchLedgerMasterController@fetchLedgerWithBalance');
		Route::post('/generateTallyVSEPReport', 'tallyGenerateReportController@generateTallyVSEPReportAPI');
		Route::post('/fetchvoucherdetails', 'tallyFetchLedgerMasterController@fetchVoucherDetails');

       //ROUTE FOR CUSTOMERPUSHVOUCHER 
		Route::post('/customerpushvoucher','CustomertallyPushVoucherController@CustomertallyPushVouchers');
		Route::post('/customervoucherupdate','CustomertallyPushVoucherController@CustomerVoucherUpdate');
		Route::post('/pushtallyledgers','CustomertallyPushVoucherController@pushTallyLedgers');
		Route::post('/tallyledgerupdate','CustomertallyPushVoucherController@CustomerLedgerUpdate');
		

		
        //Tally Crud Operations Routes
		Route::any('/tallycrud','tallyController@index');
	    Route::any('/list','tallyController@getlist');
		Route::any('/edit/{id}','tallyController@edit');
		Route::any('/add','tallyController@add');
		Route::any('/update','tallyController@update');
		Route::any('/delete/{id}','tallyController@delete');
		Route::any('/validatetallycode','tallyController@validatetallyCode');      

	});
}); 