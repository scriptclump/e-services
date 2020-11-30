<?php
Route::group(['middleware' => ['mobile']], function () {
	Route::group(['prefix' => 'expensestracker', 'namespace' => 'App\Modules\ExpensesTracker\Controllers'], function () {

		Route::get('/', 'expensesTrackController@expensesDashboard');
		Route::get('/openpopup/{id}', 'expensesTrackController@expensesDashboard');
		Route::get('/expensestrackdata', 'expensesTrackController@expensesTrackerDashboard');
		Route::get('/getexpensesdetails/{expid}', 'expensesTrackController@getExpensesDetails');
		Route::post('/updateexpensedata', 'expensesTrackController@updateExpenseData');
		Route::post('/downloadexpensesdata', 'expensesTrackController@downloadExpensesData');
		Route::get('/gethistoryexpensesdetails/{expid}', 'expensesTrackController@getHistoryExpensesData');
		Route::post('/updatereftypeonly/{expid}', 'expensesTrackController@UpdateExpensesTypeInDetailsTable');
		Route::post('/updateaproovedamountonly', 'expensesTrackController@UpdateExpensesAmountInDetailsTable');		
		Route::post('/updatebusinessUnit', 'expensesTrackController@updateBusinessUnit');		
		
		
		

		// ====================================================================
		// Routes for Expences API
		// ====================================================================
		Route::get('/API/getexpenses', 'expensesTrackGetAPIController@getAllExpenses');
		Route::get('/API/getexpensesbyid', 'expensesTrackGetAPIController@getExpensesByID');
		Route::get('/API/getmastervalforexpses', 'expensesTrackGetAPIController@getMasterLookupValueForExp');
		Route::get('/API/getexpenseslineitems', 'expensesTrackGetAPIController@getExpensesLineItems');
		Route::get('/API/getallusersexpenses', 'expensesTrackGetAPIController@getAllUsersExpenses');
		
		Route::post('/API/addexpensesdetails', 'expensesTrackPostAPIController@addExpencesDetails');
		Route::post('/API/saveexpenseslineitems', 'expensesTrackPostAPIController@saveExpensesLineItems');
		Route::post('/API/mapdetailswithexpenses', 'expensesTrackPostAPIController@mapDetailsWithExpenses');

		Route::post('/API/deleteunclaimedexp', 'expensesTrackPostAPIController@deleteUnclaimedExp');

		Route::get('/API/getapprovaldata', 'approvalAPIController@getApprovalData');
		Route::post('/API/saveapprovaldata', 'approvalAPIController@saveApprovalData');
		Route::get('/API/getapprovaldataasperrole', 'approvalAPIController@getExpensesAsPerApprovalRole');
		Route::get('/API/getapprovalhistorybyid', 'approvalAPIController@getApprovalHistoryByID');

		Route::get('/API/getapprovalactivitydetails', 'approvalAPIController@getApprovalActivityDetails');

		Route::get('/API/gettallyledgers', 'expensesTrackGetAPIController@getTallyLedgers');
		Route::post('/API/adddirectadvanceexpenses', 'expensesTrackPostAPIController@addDirectAdvanceExpenses');

		
		// routes for direct expenses 
		Route::get('/directexpenses', 'DirectExpensesController@directexpensesDashboard');
		Route::get('/directexpensesdata', 'DirectExpensesController@directexpensesTrackerDashboard');
		Route::get('/getdirectexpensesdetails/{submited_by_id}', 'DirectExpensesController@getDirectExpensesData');
		Route::post('/downloaddirectexpenses', 'DirectExpensesController@downloadDirectExpensesData');


	});
});