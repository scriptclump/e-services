<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'ncttracker', 'namespace' => 'App\Modules\NCTTracker\Controllers'], function () {

		Route::post('/addnctdata', 'nctTrackerController@saveNctDetails');
		//Route::post('/updatestatusnctdetails', 'nctTrackerController@updateStatusNctDetails');
		Route::get('/getuserlist', 'nctTrackerController@getUsersList');
		Route::get('/getncttrackerdetailsbyid/{nctid}', 'nctTrackerController@getNctTrackerDetailsById');
		Route::get('/gethistorydetailsbyid/{nctid}', 'nctTrackerController@getHistoryDetailsById');
		Route::get('/', 'nctTrackerController@nctDashboard');
		Route::get('/ncttrackerdata', 'nctTrackerController@nctTrackerDataDashboard');
		Route::get('/getnctdetailsbyid/{nctid}', 'nctTrackerController@getNctDetailsById');

		Route::get('/getholdernamefromtallyledger', 'nctTrackerController@getNameFromTallyLedgers');
		// view and  update data by each row using ref no
		Route::get('/getNctDataByRow/{nctid}', 'nctTrackerController@getNctDataByRow');
		Route::post('/updatenctdata', 'nctTrackerController@updateEachNctDetails');	

		// Route to get Cheque Image
		Route::get('/getcollectionimagebyid/{nctid}', 'nctTrackerController@getCoillectionImageByID');

		//Nct Bulk Update for Sales Orders
		Route::get('/NctBulkDataUpdate', 'nctTrackerController@NctBulkDataUpdate');

		//Nct cron notify
		Route::get('/nctnotify', 'nctNotifyController@nctnotify');	
		// IFSC codes
		Route::get('/getifsclist', 'nctTrackerController@getIFSCList');
		// get deposited to by status
		Route::get('/getDeposited/{option}', 'nctTrackerController@getDeposited');

	});
});