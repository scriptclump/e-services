<?php

//Route::get('inward','Inbound\Controllers\InwardController@index');
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Attributes\Controllers'], function () 
	{
		

				Route::get('product/selectedAttributes', 'ProductController@seleteAttributes');

				
				Route::post('product/getattributelist', 'ProductController@getAttributeList');
				Route::any('product/getattributelistdata','ProductController@getAttributeListData');
				
				//searchn   getattributelistdatabyid
				/* Route::any('product/searchAttributes/{attribute_id}/{attribute_set_id}/{flag}', 'ProductController@searchAttributes'); */
				Route::any('getAttributeName/{att_id}','ProductController@getAttributeName');
				Route::any('product/getAllAttributeGroup/{cat_id}','ProductController@getAllAttributeGroup');
				//Route::post('product/getAllAttributeGroup','ProductController@getAllAttributeGroup');
				Route::post('product/addAttributeGroup','ProductController@addAttributeGroup');
				Route::post('product/searchAttributes', 'ProductController@searchAttributes');
				Route::post('product/edenabled/{attrId}/{attrSetId}/{status}', 'ProductController@vr_enabled');
				Route::post('product/edsecodaryenabled/{attrId}/{attrSetId}/{status}', 'ProductController@vr_secondary_enabled');
				Route::post('product/edthirdenabled/{attrId}/{attrSetId}/{status}', 'ProductController@vr_third_enabled');
				
				Route::post('product/filterenabled/{attrId}/{attrSetId}/{status}', 'ProductController@filter_enabled');

				//search
				//added fot Ajax
				Route::any('product/addAttributedata/{attribute_set_id}', 'ProductController@addSelectedAttributes');

				Route::any('product/getAttributedata/{attribute_set_id}', 'ProductController@customerAttributesAll');
				Route::any('product/checkSetAvailability', 'ProductController@checkSetAvailability');
				Route::any('product/checkGroupAvailability', 'ProductController@checkGroupAvailability');
				Route::any('product/checkAttributeAvailability', 'ProductController@checkAttributeAvailability');
				Route::any('product/checkDefaultAttributeAvailability', 'ProductController@checkDefaultAttributeAvailability');
				Route::any('product/checkAttrAvailability', 'ProductController@checkAttrAvailability');
				//added for Ajax
				//Route::get('attribute/index', 'ProductController@attributes');

				Route::get('product/getCustomers', 'ProductController@getCustomers');
				Route::get('product/getallattributes', 'ProductController@getAllAttributes');
				Route::get('product/delAttributeFromGroup/{attribute_id}/{attribute_group_id}', 'ProductController@delAttributeFromGroup');
				Route::post('product/delAttributeFromGroup', 'ProductController@delAttributeFromGroup');
				Route::get('product/editattribute/{attribute_id}/{attribute_set_id}', 'ProductController@editAttribute');
				Route::put('product/updateattribute/{attribute_id}', 'ProductController@updateAttribute');
				Route::post('product/saveattribute', 'ProductController@saveAttribute');
				Route::get('product/delete/{attribute_id}', 'ProductController@deleteAttribute');
				Route::post('product/saveAttributeGroup', 'ProductController@saveAttributeGroup');
				Route::any('product/saveattributeset', 'ProductController@saveAttributeSet');
				Route::post('product/assigngroups', 'ProductController@assignGroups');
				Route::any('product/getAssignGroupDetails/{attribute_set_id}', 'ProductController@getAssignGroupDetails');
				Route::any('product/getoptions/{attribute_id}', 'ProductController@getoptions');
				//update attributeset
				Route::any('product/setoptions','ProductController@setoptions');
				Route::post('product/updateattributeset', 'ProductController@updateattributeset');
				Route::get('product/editAttributeGroup/{attribute_group_id}', 'ProductController@editAttributeGroup');
				Route::get('product/editattributeset/{attribute_set_id}', 'ProductController@editAttributeSet');
				Route::put('product/updateAttributeGroup/{attribute_group_id}', 'ProductController@updateAttributeGroup');
				Route::get('product/deleteAttributeGroup/{attribute_group_id}', 'ProductController@deleteAttributeGroup');
				Route::post('product/deleteAttributeGroup', 'ProductController@deleteAttributeGroup');
				Route::post('product/deleteattributeset', 'ProductController@deleteAttributeSet');
				Route::any('product/multistore', 'ProductController@multistore');
				Route::any('product/submit', 'ProductController@submit');
				Route::any('product/individualstore', 'ProductController@individualstore');
				Route::any('product/getallstores', 'ProductController@getAllStores');
				Route::any('product/addAttributesFromExcel', 'ProductController@addAttributesFromExcel');

				//Attribute Sets HierarchicalGrid pages
				Route::any('attribute/index', 'ProductController@displayNewGridLayout');
				Route::any('product/getAttributeSets', 'ProductController@getAllAttributeSets');
				Route::any('product/getAttributesDetails', 'ProductController@getAttributesDetails');
	});


});
