<?php


Route::group(['before'=>'authenticates'],function(){
Route::any('reportapis/OrdersReport','ReportApiController@OrdersReport');
Route::any('reportapis/amazon','ReportApiController@amazon');
Route::any('reportapis/Flipkart','ReportApiController@Flipkart');
Route::any('reportapis/eBay','ReportApiController@eBay');
Route::any('reportapis/Factail','ReportApiController@Factail');
Route::any('reportapis/eBayGrid','ReportApiController@eBayGrid');
Route::any('reportapis/FlipkartGrid','ReportApiController@eBayGrid');
Route::any('reportapis/amazonGrid','ReportApiController@eBayGrid');
Route::any('reportapis/allGrid','ReportApiController@eBayGrid');

Route::any('reportapis/allGrid','ReportApiController@allGrid');
Route::any('/','ReportApiController@index');
Route::any('reportapis/grid','ReportApiController@grid');
Route::any('reportapis/all','ReportApiController@all');
Route::any('reportapis/piechart/{order_status}/{from_date}/{to_date}','ReportApiController@piechart');
Route::get('reportapis/getstatus/{cname}','ReportApiController@getStatus');
Route::get('reportapis/ChannelOrderDetails/{orderdetails}','ReportApiController@ChannelOrderDetails');



});
Route::get('reportapis/innergrid/{orders}','ReportApiController@innergrid');
Route::get('reportapis/todayorders','ReportApiController@todayorders');
Route::any('reportapis/gettodaysorders','ReportApiController@getTodaysOrders');
Route::get('reportapis/orders/{os}','ReportApiController@unshippedgrid');
Route::any('reportapis/ViewOrder/{order_id}','ReportApiController@ViewOrder');
Route::any('reportapis/UpdateOrder/{order_id}','ReportApiController@UpdateOrder');
Route::any('reportapis/PrintInvoice/{order_id}/{print_invoice}','ReportApiController@PrintInvoice');
