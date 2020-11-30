<?php

Route::any('Commerceplatform', 'ChannelController@index');
Route::any('Commerceplatform/create/{channel_id?}', 'ChannelController@addChannel');
Route::any('Commerceplatform/edit/{channel_id?}', 'ChannelController@addChannel');
Route::any('Commerceplatform/checkMPExist', 'ChannelController@checkMPExist');
Route::any('Commerceplatform/checkMPUrlExist', 'ChannelController@checkMPUrlExist');
Route::any('Commerceplatform/addchannelstatus', 'ChannelController@addchannelstatus');
Route::any('Commerceplatform/getEbutorDropList','ChannelController@getEbutorDropList');

Route::any('Commerceplatform/storeChannelData', 'ChannelController@storeChannelData');
Route::any('Commerceplatform/getAllChannels', 'ChannelController@getAllChannels');
Route::any('Commerceplatform/deleteChannel', 'ChannelController@deleteChannel');
Route::any('Commerceplatform/channelStatuschange', 'ChannelController@channelStatuschange');

Route::any('Commerceplatform/channelChargesStore', 'ChannelController@channelChargesStore');
Route::any('Commerceplatform/delteChannelCharges', 'ChannelController@delteChannelCharges');
Route::any('Commerceplatform/getChannelCharges', 'ChannelController@getChannelCharges');
Route::any('Commerceplatform/getChannelChargesData', 'ChannelController@getChannelChargesData');

Route::any('Commerceplatform/checkCatgoryExist', 'ChannelController@checkCatgoryExist');
Route::any('Commerceplatform/checkCatgoryIDExist', 'ChannelController@checkCatgoryIDExist');
Route::any('Commerceplatform/categoryStore', 'ChannelController@categoryStore');

Route::any('Commerceplatform/delChannelstatus', 'ChannelController@delChannelstatus');
Route::any('Commerceplatform/order_status', 'ChannelController@order_status');
Route::any('Commerceplatform/ordermapstore', 'ChannelController@ordermapstore');
Route::any('Commerceplatform/checkUniquchannelstatus', 'ChannelController@checkUniquchannelstatus');
Route::any('Commerceplatform/addchannelstatus', 'ChannelController@addchannelstatus');
Route::any('Commerceplatform/getOrderstatusList', 'ChannelController@getOrderstatusList');
Route::any('Commerceplatform/getmapdetails', 'ChannelController@getmapdetails');
Route::any('Commerceplatform/getupdatedstatus', 'ChannelController@getupdatedstatus');
Route::any('categoryImportExcel', 'ChannelController@categoryImportExcel');

Route::get('Commerceplatform/getCannelCategoryCharges', 'ChannelController@getTreechannelCategories');
//Route::get('Commerceplatform/getChannelCategories', 'ChannelController@getChannelCategoriesMapping');
Route::get('Commerceplatform/getTreechannelcategorymapping', 'ChannelController@getTreechannelcategorymapping');
Route::any('Commerceplatform/getChannelCategories/{channel_id?}', 'ChannelController@getchannelcategories_mapping');
Route::get('Commerceplatform/getebutorcategories', 'ChannelController@getebutorcategories');
Route::get('Commerceplatform/getebutorattributes', 'ChannelController@getebutorattributes');
Route::get('Commerceplatform/getebutorCategoryattributes', 'ChannelController@getebutorCategoryattributes'); //Added By Naresh 

Route::any('Commerceplatform/getchannelattributes/{channel_id?}', 'ChannelController@getchannelattributes');

Route::any('Commerceplatform/getparentcategories', 'ChannelController@getparentcategories');
Route::any('Commerceplatform/addchannelcategories', 'ChannelController@addchannelcategories');
Route::any('Commerceplatform/editChannelCategories', 'ChannelController@editChannelCategories');
Route::any('Commerceplatform/checkUniquevalue', 'ChannelController@checkUniquevalue');
Route::any('Commerceplatform/checkUniquecategoryid', 'ChannelController@checkUniquecategoryid');

Route::get('Commerceplatform/getChannelCategoriesGrid', 'ChannelController@getChannelCategoriesGrid');
Route::get('Commerceplatform/getCannelAttributesGrid', 'ChannelController@getCannelAttributesGrid');
Route::get('Commerceplatform/getCannelVariantsGrid', 'ChannelController@getCannelVariantsGrid');

Route::get('Commerceplatform/getChannelCategoriesMapGrid', 'ChannelController@getChannelCategoriesMapGrid');
Route::get('Commerceplatform/getCannelAttributesMapGrid', 'ChannelController@getCannelAttributesMapGrid');
Route::get('Commerceplatform/getCannelVariantsMapGrid', 'ChannelController@getCannelVariantsMapGrid');

Route::any('Commerceplatform/categoryDelete/{catid}', 'ChannelController@categoryDelete');
Route::any('Commerceplatform/attributeDelete/{attr_id}', 'ChannelController@attributeDelete');
Route::any('Commerceplatform/variantDelete/{variant_id}', 'ChannelController@variantDelete');

Route::any('Commerceplatform/mapcategoryDelete/{catid}', 'ChannelController@mapcategoryDelete');
Route::any('Commerceplatform/mapattributeDelete/{attr_id}', 'ChannelController@mapattributeDelete');
Route::any('Commerceplatform/mapvariantDelete/{variant_id}', 'ChannelController@mapvariantDelete');

Route::any('Commerceplatform/getCategorydata', 'ChannelController@getCategorydata');
?>