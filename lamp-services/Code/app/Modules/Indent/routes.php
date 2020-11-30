<?php

    
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Indent\Controllers'], function () {
        
	Route::get('/indents/index','IndentController@indentList');
	Route::get('/indents','IndentController@indentList');
	Route::get('/indents/getOrderIndent','IndentController@getOrderIndentAction');
	Route::any('/indents/createIndent','IndentController@createIndent');
	Route::any('/indents/createIndentAction','IndentController@createIndentAction');
	
	Route::get('/indents/detail/{indentId}','IndentController@getIndentDetailAction');
	Route::get('/indents/editdetails/{indentId}','IndentController@updateIndentDetails'); //edit details
	// Route::get('/indents/detail/{indentId}/{message}','IndentController@getIndentDetailAction'); //edit details
	Route::any('/indents/savedetails','IndentController@saveDetails');
	Route::any('/indents/getselectedsupplieraddress/{supllierId}','IndentController@getSelectedSupplierAddress');
	

	Route::get('/indents/pdf/{indentId}','IndentController@getIndentPdfAction');
	Route::get('/indents/print/{indentId}','IndentController@printAction');
	Route::any('/indents/supplierWarehouseBrandOptions','IndentController@supplierWarehouseBrandOptions');
	Route::any('/indents/supplierSupplierOptions','IndentController@supplierSupplierOptions');
	Route::any('/indents/productsBySupplier','IndentController@productsBySupplier');
	Route::any('/indents/getProductInfo','IndentController@getProductInfo');

	Route::post('/indents/updateIndent','IndentController@updateIndentAction');
	Route::post('/indents/removeProducts','IndentController@removeProductAction');
	Route::any('/indents/autoindent','IndentController@createAutoIndentAction');
	Route::get('/indents/pdfEmail/{indentId}','IndentController@downloadPDF');
	Route::get('/indents/printSession','IndentController@printSession');

	Route::any('/indents/updateIndentSupplier','IndentController@updateIndentSupplier');
	Route::any('/indents/createExport','IndentController@createExportIndents');
	Route::any('/indents/createStockits','IndentController@createStockitsIndents');

	
	Route::post('/indents/deleteindent','IndentController@deleteIndentAction');
	});
});
