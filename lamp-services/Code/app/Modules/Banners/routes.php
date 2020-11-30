<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'banners', 'namespace' => 'App\Modules\Banners\Controllers'], function () {
       
       Route::get('/', 'BannersController@index');
       Route::get('/index', 'BannersController@index');
       Route::get('/banner', 'BannersController@banner');
       Route::post('/addbanners', 'BannersController@addbanners'); 
       Route::get('/editbanner/{id}','BannersController@editbanner');
       Route::any('/bannersList','BannersController@bannerlist');
       Route::any('/deletebanner','BannersController@DeleteBanner');
       Route::post('/gethubs','BannersController@getHubs');
       Route::post('/getbeats','BannersController@getBeats');
       Route::post('/bannerType','BannersController@bannerType');
       Route::post('/dchubmapping','BannersController@dchubmapping');
       Route::post('/hubbeatsmap','BannersController@hubbeatsmap');
       Route::post('/imageupload','BannersController@imageUpload');
       Route::any('/createpopupExport','BannersController@createpopupExport');
       Route::any('/createbannersExport','BannersController@createbannersExport');
       Route::post('/blockbanner','BannersController@blockBanner');
       Route::post('/checkpopupsts','BannersController@checkPopupStatus');
       Route::post('/getitemsbytype','BannersController@getItemsbyType');
    });

    Route::group(['prefix' => 'sponsors', 'namespace' => 'App\Modules\Banners\Controllers'], function () {
       Route::get('/', 'SponsorsController@index');
       Route::any('/sponsorsList','SponsorsController@sponsorslist');
       Route::get('/editsponsor/{id}','SponsorsController@editsponsor');
       Route::any('/deletesponsor','SponsorsController@DeleteSponsor');
       Route::get('/sponsor', 'SponsorsController@sponsor');
       Route::any('/createsponsorsExport', 'SponsorsController@createsponsorsExport');
        Route::post('blocksponsor','SponsorsController@blockSponsor');
      });
});
