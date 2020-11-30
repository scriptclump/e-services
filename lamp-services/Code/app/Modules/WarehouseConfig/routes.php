<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\WarehouseConfig\Controllers'], function () {
        Route::any('warehouseconfig/index', 'WarehouseConfigController@indexAction');
        Route::any('warehouseconfig/getwarehouseconfig', 'WarehouseConfigController@getWarehouseConfig');
        Route::any('getWarehouseDetails/{wh_id}', 'WarehouseConfigController@getWarehouseDetails');
        Route::any('savewarehousedata', 'WarehouseConfigController@saveWarehouseData');
        Route::any('getWarehouseName/{wh_id}', 'WarehouseConfigController@getWarehouseName');
        Route::any('editwarehousedata', 'WarehouseConfigController@editWarehouseData');
        Route::any('getGroupedProducts', 'WarehouseConfigController@getGroupedProducts');
        Route::any('deleteWarehouseLevel/{wh_id}', 'WarehouseConfigController@deleteWarehouseLevel');
        Route::any('getLevelWiseDetails/{level_id}/{wh_id}', 'WarehouseConfigController@getLevelWiseDetails');
        Route::any('saveBinDimensionsCong', 'WarehouseConfigController@saveBinDimensionsCong');
        Route::any('getBinDimensionsData/{grp_id}/{wh_id}', 'WarehouseConfigController@getBinDimensionsData');
        Route::any('getProductsByProdutGrp/{grp_id}/{wh_id}', 'WarehouseConfigController@getProductsByProdutGrp');
        Route::any('binLevelWiseTransffer/{wh_id}', 'WarehouseConfigController@binLevelWiseTransffer');
        Route::any('multiBinLevelConfig', 'WarehouseConfigController@multiBinLevelConfig');
        Route::any('checkRackCapacity', 'WarehouseConfigController@checkRackCapacity');
        
        Route::any('getproductGrpByWarehouse/{wh_id}', 'WarehouseConfigController@getProductGrpByWh');
        Route::any('replenishment/create', 'WmsReplenishmentApiController@reservedReplanishment');
        Route::any('warehouseconfig/dashboard', 'WarehouseConfigController@binInvDashBoard');
        Route::any('warehouseconfig/getInvdata', 'WarehouseConfigController@getInvdata');
        Route::any('warehouseconfig/downloadbinexcel','WarehouseConfigController@downloadBinExcel');
        Route::any('warehouseconfig/importbinexcel','WarehouseConfigController@importBinExcel');   
        Route::any('getbintypelist','WarehouseConfigController@getBinTypeList');
	    Route::any('binuploadmsg/{refid}','WarehouseConfigController@readMessage');
        Route::any('getBinCategory','WarehouseConfigController@getBinCategory');

        //warehouse crud operations
        Route::any('beatconfig','warehouseController@index');
        Route::any('/beatconfig/list','warehouseController@getlist');
        Route::any('/beatconfig/edit/{id}','warehouseController@edit');
        Route::any('/beatconfig/add','warehouseController@add');
        Route::any('/beatconfig/update','warehouseController@update');
        Route::any('/beatconfig/delete/{id}','warehouseController@delete');
        Route::any('/beatconfig/display/{id}','warehouseController@display');
        Route::any('/beatconfig/access/{id}','warehouseController@access');
    });
});

Route::group(['middleware' => ['mobile']], function () {
        Route::group(['namespace' => 'App\Modules\WarehouseConfig\Controllers'], function () {
        Route::any('getBinLocationbyProId', 'warehouseConfigApiController@getBinLocationbyProId');
        Route::any('showInvByBinId', 'warehouseConfigApiController@showInvByBinId');
        Route::any('deriveBinCapacityByProductId', 'warehouseConfigApiController@deriveBinCapacityByProductId');    
        Route::any('cpmanager/checkBinInventoryByBinCode', 'warehouseConfigApiController@checkBinInventoryByBinCode');        
        Route::any('cpmanager/grnPutawayProducts', 'warehouseConfigApiController@poGrnProductsWithBinLocation'); 
        Route::any('cpmanager/getProductInfoByEanNo', 'warehouseConfigApiController@getProductInfoByEan'); 
        Route::any('cpmanager/placePutawayProduct', 'warehouseConfigApiController@putaway');
        Route::any('cpmanager/cratesList', 'warehouseConfigApiController@cratesList'); 
        Route::any('cpmanager/crateCodeInfo', 'warehouseConfigApiController@crateCodeInfo');
        Route::any('cpmanager/productBinCapacity', 'warehouseConfigApiController@binCapacityByProdId');
        Route::any('cpmanager/binAllocation/{id}', 'warehouseConfigApiController@binAllocation');
        Route::any('cpmanager/getProductBinLocation', 'warehouseConfigApiController@getProductBinLocation');
        Route::any('cpmanager/salesreturns', 'warehouseConfigApiController@salesreturns');
        Route::any('cpmanager/assignPutaway', 'warehouseConfigApiController@assignReturns');
        Route::any('createreplenishment', 'WmsReplenishmentApiController@reservedReplanishment');
        Route::any('warehouseconfig/getreplenishmentlist', 'WmsReplenishmentApiController@getReplenishmentList');
        Route::any('warehouseconfig/savereplenishassign', 'WmsReplenishmentApiController@saveReplenishAssign');
        Route::any('warehouseconfig/getassignedlist', 'WmsReplenishmentApiController@getAssignedList');
        Route::any('warehouseconfig/savereplenishqty', 'WmsReplenishmentApiController@saveReplenishQty');
        Route::any('warehouseconfig/getallassignlist', 'WmsReplenishmentApiController@getAssignedReplenishmentList');
        Route::any('cpmanager/getProductBinLocation', 'warehouseConfigApiController@getProductBinLocation');
        Route::any('cpmanager/putawaylists','warehouseConfigApiController@getAllPutawayLists');
        Route::any('cpmanager/bintobintransfer','warehouseConfigApiController@bintobintransfer');
        Route::any('warehouseconfig/getusercompletedlist', 'WmsReplenishmentApiController@getUserCompletedList');
        Route::any('cpmanager/binMinMaxQty','warehouseConfigApiController@showMinMaxQty');

        Route::any('cpmanager/grnlist', 'warehouseConfigApiController@getGRNList');
        Route::any('allocation/{id}','warehouseConfigApiController@putawayBinAllocation');
        Route::any('getGroupedProductsList/{wh_id}','WarehouseConfigController@getProductGroupId'); 
        Route::any('warehouseconfig/putawaygrid','HoldPutawayListController@putawayHoldList');
        Route::any('warehouseconfig/wmsPutawayGridData/{status}','HoldPutawayListController@getPutawaydata');
        Route::any('warehouseconfig/binsDetailsByPutawayid/{id}','HoldPutawayListController@binsDetailsByPutawayid');
        Route::any('warehouseconfig/putaway_cnt/{id}','HoldPutawayListController@putaway_cnt');
       
    });
     
});