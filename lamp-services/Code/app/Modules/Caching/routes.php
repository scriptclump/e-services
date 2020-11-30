<?php
   
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Caching\Controllers'], function () {
    Route::get('/cache','CachingController@index');
    Route::any('/cache/getAjaxData/{search_type}','CachingController@getAjaxData'); 
    Route::any('/cache/flush_product_slab/{product_id}/{customer_id}/{dc_id}','CachingController@flushProductsSlab'); 
    Route::any('/cache/view_product_slab/{product_id}/{customer_id}/{dc_id}','CachingController@viewProductsSlab');
    Route::any('/cache/view_item/{item_id}/{beat_id}/{item_type}','CachingController@viewCacheItemsList'); 
    Route::any('/cache/type/{dc_id}/{customer_id}/{item_type}/{segment_id}','CachingController@flushCacheItemsList'); 
    Route::any('/cache/dashboard_flush/{dashboard_id}/{day_id}/{ff_user_id}','CachingController@flushCacheDashboard'); 
    Route::any('/cache/dynamic_flush/{pattern}','CachingController@flushDynamicCacheData'); 
    });
});
