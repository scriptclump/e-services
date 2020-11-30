<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\CustomerFeedback\Controllers'], function () {
        Route::any('customerfeedback/index', 'CustomerFeedbackController@indexAction');
        Route::any('customerfeedback/getcustomerfeedback', 'CustomerFeedbackController@getCustomerFeedback');
        Route::any('customerfeedback/deletefeedback', 'CustomerFeedbackController@deleteFeedback');
        Route::any('customerfeedback/custfeedbackxls', 'CustomerFeedbackController@custFeedbackXls');
    });


    /*ECASH CREDITLIMIT ROUTES */

      Route::group(['prefix'=>'ecashLimit','namespace'=>'App\Modules\CustomerFeedback\Controllers'],function(){
      Route::get('/','customersMovController@index');
      Route::post('add','customersMovController@saveEcashCreditlimitUser');
      Route::get('gridData','customersMovController@ecashCreditlimitGrid');
      Route::get('editecashCreditLimit/{id}','customersMovController@editecashCreditLimit');
      Route::post('updateEcashLimit','customersMovController@updateEcashLimit');
     });
});




