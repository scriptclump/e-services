<?php
Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'documentMaster', 'namespace' => 'App\Modules\DocumentsMaster\Controllers'], function () {
		Route::group(['before' => 'authenticates'], function() {    

	    Route::get('/getMasterDocs/{master_type}', 'DocumentMasterController@getMasterDocs');
		});
	});
}); 

Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'documents', 'namespace' => 'App\Modules\DocumentsMaster\Controllers'], function () {
		Route::group(['before' => 'authenticates'], function() {  

	    Route::get('/', 'DocumentMasterController@index');

		Route::any('/uploadDoc','DocumentMasterController@uploadDoc');

		Route::any('/getdocuments','DocumentMasterController@getUploadedDoc');

		Route::any('/editDocument/{id}','DocumentMasterController@editDocument');

		Route::any('/deleteDocumentId/{did}','DocumentMasterController@deleteDocumentId');

		Route::any('/updateDocsData','DocumentMasterController@updateDocsData');
		});
	});
}); 



