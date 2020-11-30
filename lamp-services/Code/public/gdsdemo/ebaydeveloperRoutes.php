<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.getallCategories getAllInventory 
 */

  Route::any('ebaydeveloper/AddItem','EbaydeveloperController@AddItem');
  Route::any('ebaydeveloper/UpdateItem','EbaydeveloperController@UpdateItem');
  Route::any('ebaydeveloper/UpdateInventory','EbaydeveloperController@UpdateInventory');
  Route::any('ebaydeveloper/placeorder','EbaydeveloperController@placeorder');
  Route::any('ebaydeveloper/getorders','EbaydeveloperController@getorders');
  Route::any('ebaydeveloper/viewOrder','EbaydeveloperController@viewOrder');
  Route::any('ebaydeveloper/updateorder','EbaydeveloperController@updateorder');
  Route::any('ebaydeveloper/completeOrder','EbaydeveloperController@completeOrder');
  Route::any('ebaydeveloper/getallCategories','EbaydeveloperController@getallCategories');
  Route::any('ebaydeveloper/endListing','EbaydeveloperController@endListing');
  Route::any('ebaydeveloper/addDispute/{data}','EbaydeveloperController@addDispute');
  Route::any('ebaydeveloper/addDispute','EbaydeveloperController@addDispute');
  Route::any('ebaydeveloper/addDisputeResponse','EbaydeveloperController@addDisputeResponse');
  Route::any('ebaydeveloper/getUserDisputes','EbaydeveloperController@getUserDisputes');
  Route::any('ebaydeveloper/scoCurlrequest','EbaydeveloperController@scoCurlrequest');
  Route::any('ebaydeveloper/getAllProducts','EbaydeveloperController@getAllProducts');
  Route::any('ebaydeveloper/test','EbaydeveloperController@test');
  Route::any('gds/gdsDashboard','EbaydeveloperController@gdsDashboard');
  Route::any('ebaydeveloper/relistProduct','EbaydeveloperController@relistProduct');
  Route::any('ebaydeveloper/addScoProducts','EbaydeveloperController@addScoProducts');
  Route::any('ebaydeveloper/UpdateAllOrders/{order_id}','EbaydeveloperController@UpdateAllOrders');



