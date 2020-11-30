<?php

/*Route::any('configuration/logisticpartners','ConfigurationController@logisticpartners');
Route::any('configuration/lplist','ConfigurationController@lpList');
Route::any('configuration/logisticpartners/edit/{lp_name}','ConfigurationController@editlp');
Route::any('configuration/savelp','ConfigurationController@savelp');
Route::any('configuration/savelocations','ConfigurationController@savelocations');
Route::any('configuration/removeWh','ConfigurationController@removeWh');
Route::get('configuration/downloadExcel/{type}', 'ConfigurationController@downloadExcel');
Route::post('configuration/importExcel', 'ConfigurationController@importExcel');*/

/*Route::get('configuration/logisticpartners/{lp_name}', function($lpname){
    die('dd');
    $lp = App\LogisticsPartner::whereLpName($lpname)->first();
    return View::make('configuration.logisticssetup',['lp'=>$lp]);
});*/
/*Route::any('configuration/logisticpartners',function(){
    $lp = new \App\LogisticsPartner;
            $lp->lp_name = '222';
            $lp->lp_legal_name = 'test';
            $lp->save();    return $lp;
});*/

