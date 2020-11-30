<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.UpdateInventory
 */

  
  Route::any('ebayapis/AddItem/{api_name}/{product_id}/{product_attributes}','EbayApiController@AddItem');
  Route::any('ebayapis/UpdateItem/{api_name}/{item_id}/{product_attributes}','EbayApiController@UpdateItem');
  Route::any('ebayapis/UpdateInventory/{api_name}/{item_id}','EbayApiController@UpdateInventory');
  Route::any('ebayapis/placeorder/{api_name}','EbayApiController@placeorder');
  Route::any('ebayapis/getorders/{api_name}','EbayApiController@getorders');
  Route::any('ebayapis/viewOrder/{api_name}','EbayApiController@viewOrder');
  Route::any('ebayapis/updateorder/{api_name}/{item_id}','EbayApiController@updateorder');
  Route::any('ebayapis/completeOrder/{api_name}','EbayApiController@completeOrder');
  Route::any('ebayapis/getallCategories/{api_name}','EbayApiController@getallCategories');
  Route::any('ebayapis/endListing/{api_name}','EbayApiController@endListing');
  Route::any('ebayapis/addDispute/{json}/{order_id}/{item_id}/{dispute_explaination}','EbayApiController@addDispute');
  Route::any('ebayapis/addDisputeResponse/{json}','EbayApiController@addDisputeResponse');
  Route::any('ebayapis/getUserDisputes/{json}','EbayApiController@getUserDisputes');
  Route::any('ebayapis/relistProduct/{input_data}/{product_id}/{channel_id}','EbayApiController@relistProduct');
    


