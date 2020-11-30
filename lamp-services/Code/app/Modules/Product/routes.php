<?php

    
Route::group(['middleware' => ['web']], function () 
{
    Route::group(['namespace' => 'App\Modules\Product\Controllers'], function () 
    {
        
		Route::any('products', 'ProductController@products');
		Route::get('getProductsList/{brand_id}', 'ProductController@getProductsList');

		Route::get('products/getProducts', 'ProductController@getCreationProducts');
		Route::get('cockpit', 'ProductController@indexAction');
		Route::get('cockpitProducts', 'ProductController@getCockpitProducts');
		Route::get('cockpitChilds', 'ProductController@getCockpitChilds');
		Route::any('relatedproducts/{product_id}', 'ProductController@getRelatedProducts');
		Route::any('deleterelsatedproduct/{product_id}', 'ProductController@deleteRelatedproduct');
		Route::any('packingproducts/{product_id}', 'ProductController@getPackingConfigproduct');
		Route::any('freeBieProducts/{product_id}','ProductController@freeBieProducts');
		Route::any('deleteproductpack/{pack_id}/{product_id}', 'ProductController@deleteProductPack');
		Route::any('productsuppliers/{product_id}', 'ProductController@getProductSuppliers');
		Route::any('deletesupplierproduct/{product_id}', 'ProductController@deleteSupplierProduct');
		Route::any('quickProductUpdate/{product_id}','ProductController@quickProductUpdate'); 
		Route::any('products/saveProductGeneralInfo/{product_id}','ProductController@saveProductGeneralInfo'); 
		Route::any('products/saveProductPackInfo/{product_id}','ProductController@saveProductPackInfo'); 
		Route::any('products/saveProductPriceInfo/{product_id}','ProductController@saveProductPriceInfo'); 
		Route::any('products/saveProductIsSellable','ProductController@saveProductIsSellable'); 
		Route::any('products/QuickProductCpStatus','ProductController@QuickProductCpStatus'); 
		Route::get('products/slabPrices', 'ProductController@slabPrices');
		Route::get('products/getOfferPackData', 'ProductController@getOfferPackData');
		Route::get('products/getShelfLifeUOMdata', 'ProductController@getShelfLifeUOMdata');
		Route::get('product/getTaxClassDropDown/{state_id}','ProductController@getTaxClassDropDown');
		Route::any('products/getAllWareHouse/{state_id}','ProductController@getAllWareHouse');
		Route::any('products/updateInventory/{product_id}','ProductController@updateInventory');

		Route::any('products/importPIMExcel','ProductController@importPIMExcel');
		Route::any('products/downloadPIMExcel','ProductController@downloadPIMExcel');
		Route::any('products/getAllProducts/{product_id}','ProductController@getAllProducts');

		Route::any('productlist/index', 'ProductController@productList');
		Route::any('productlist/getproductList', 'ProductController@getproductList');
		Route::any('productlist/childprodutList', 'ProductController@childProdutList');
		Route::any('productlist/editgrouprepo/{parent_id}', 'ProductController@editGroupRepo');
		Route::get('productlist/getRepoProducts', 'ProductController@getRepoProducts');
		Route::get('productlist/updateGroupRepo', 'ProductController@updateGroupRepo');
		Route::post('productlist/cpStatus', 'ProductController@cpStatus');
		Route::post('productlist/cpChildStatus', 'ProductController@cpChildStatus');
		Route::post('/productlist/deleteChildproducts', 'ProductController@deleteChildproducts');

		Route::get('/product/deleteProduct', 'ProductController@deleteProduct');
		Route::post('product/duplicate/{product_id}', 'ProductController@duplicateProduct');

		Route::any('products/downloadAllProductInfo', 'ProductController@QueueDownloadAllProductInfo');    
		//manufacturer code convert to product module
		 Route::any('getProductPrimaryImage/{product_id}','ProductEPController@getProductPrimaryImage');
        Route::any('getProductGalleryImage/{product_id}','ProductEPController@getProductGalleryImage');
		Route::any('editproduct/{product_id}','ProductEPController@editProduct');
		Route::any('getProductComments','ProductEPController@getProductComments');
		Route::any('productComments','ProductEPController@productComments');
		Route::any('getAllAttributes/{cat_id}/{product_id}',"ProductEPController@getAttGroupByCategory");
		Route::any('product/getAllProducts/{product_id}','ProductEPController@getAllProducts');
		Route::any('getManufacturersList', 'ProductEPController@getManufacturersList');
		Route::get('getBrandsList/{brand_id}', 'ProductEPController@getBrandsList');
		Route::get('product/getBrandProducts/{brand_id}','ProductEPController@getBrandProducts');
		Route::any('saveProductUrlImage/{product_id}','ProductEPController@saveProductUrlImage');
		Route::any('deleteProductImage/{product_id}','ProductEPController@deleteProductImage');
        Route::any('setAsDefaultImage/{product_id}','ProductEPController@setAsDefaultImage'); 
		Route::any('saveproductimages/{product_id}','ProductEPController@saveProductImgages');
		Route::any('freeBieConfigurations','ProductEPController@freeBieConfigurations');
		Route::any('editFreebieConfiguration/{freebie_id}','ProductEPController@editFreebieConfiguration');
		Route::any('deleteFreebieProduct/{freebie_id}','ProductEPController@deleteFreebieProduct');
		Route::any('editPackageLevel/{product_id}','ProductEPController@editPackageLevel');
		Route::any('editPackageConfiguration/{product_id}','ProductEPController@editPackageConfiguration');
		Route::any('packageConfigurations','ProductEPController@packageConfiguration');
		Route::any('checkproductname','ProductEPController@productNameChecking');
        Route::any('productUpdate','ProductEPController@productUpdate');
        Route::post('product/saveCPEnabled/{prodId}','ProductEPController@saveCPEnabled');
        Route::any('createRelativeProducts','ProductController@createRelativeProducts');
        Route::any('products/createproduct','ProductEPController@productCreation');
		Route::any('productSave','ProductEPController@saveProduct');
		Route::any('productpreview/{product_id}','ProductEPController@productPreview');
		Route::any('approveproduct','ProductEPController@approvalAccess');
		Route::any('groupedProducts/{product_id}','ProductEPController@groupedProducts');
		Route::any('getWhBinConfig/{product_id}','ProductEPController@getWhBinConfig');
		Route::any('getProductGroupList','ProductEPController@getProductGroupList');		
		Route::any('saveWhBinConfigData/{product_id}','ProductEPController@saveWhBinConfigData');		
		Route::any('saveProductGroup','ProductEPController@saveProductGroup');		
		Route::any('getProductGroupName/{pid}','ProductEPController@getProductGroupName');
		Route::any('getProductGroupList','ProductEPController@getProductGroupList');		
		Route::any('getwhlist','ProductController@getWhList');
                Route::any('products/downloadwhexcel','ProductController@downloadWhExcel');
                Route::any('products/importWhExcel','ProductController@importWhExcel');
        Route::any('getWhBinConfigDataByBinId/{wh_id}','ProductEPController@getWhBinConfigDataByBinId');
        Route::any('products/donwloadPackConfigExcel','ProductController@donwloadPackConfigExcel');
        Route::any('products/uploadPackConfigExcel','ProductController@uploadPackConfigExcel');        

                Route::any('products/creation','ProductController@products');
                Route::any('products/approval','ProductController@products');
                Route::any('products/filling','ProductController@products');
                Route::any('products/enablement','ProductController@products');
                Route::any('products/open','ProductController@products');
                Route::any('products/disabled','ProductController@products');
                Route::any('products/active','ProductController@products');
                Route::any('products/all','ProductController@products');
                Route::any('products/wh','ProductController@whChange');

        Route::any('getProductGroupListByManf/{id}','ProductEPController@getProductGroupListByManf');
        Route::any('/producthistorygrid/{id}','ProductEPController@productHistoryGrid');
        Route::any('/products/productpackgrid','ProductEPController@getProductpPacks');

        Route::any('/products/counts','ProductController@getCounts');

        Route::any('/products/product_config','ProductController@productConfig');

        Route::any('products/cpenabledcfcproducts', 'ProductController@cpEnableDcFcProducts');

        Route::any('products/cpenabled', 'ProductController@cpEnabled');

        Route::any('products/issellable', 'ProductController@isSellable');

        Route::any('products/saveesu', 'ProductController@saveEsuforDc');

        Route::any('products/downloadCPEnableExcel', 'ProductController@downloadCPEnableExcel');

        Route::any('products/uploadCPEnableExcelSheet', 'ProductController@uploadCPEnableExcelSheet');

        Route::get('products/skuproducts', 'MustSkuController@mustSkuindex');

        Route::get('products/getmustskuProducts', 'MustSkuController@getMustSkuProducts');

        Route::get('products/getmustskusearch', 'MustSkuController@searchMustSku');

        Route::post('products/addmustskuproduct', 'MustSkuController@addMustSkuProduct');

        Route::get('products/deleteskuProduct', 'MustSkuController@deleteSKUProduct');

        Route::get('products/changeskuProductstatus', 'MustSkuController@changeSKUProductStatus');


        Route::any('products/productmobileview', 'MustSkuController@productMobileView');
        Route::any('products/productcacheflush', 'MustSkuController@productcacheflush');
        Route::any('products/getelphistorybyproductid', 'ProductController@getElpHistoryByProduct');
        Route::any('products/exportproductelpsbyproductid', 'ProductController@exportElpsByProductId');
        Route::any('products/exportallproductelps', 'ProductController@exportAllProductsElps');
        Route::any('/checkconsumerpackoutsideforproduct', 'ProductEPController@checkConsumerPackOutsideforProduct');
        Route::any('products/customertypeesu', 'ProductController@customerTypeEsu');
        Route::any('products/savecustesu', 'ProductController@saveCustEsu');
        Route::any('products/downloadEsuExcel', 'ProductController@downloadCustEsuExcel');
        Route::any('products/uploadEsuExcel', 'ProductController@uploadCustEsuExcel');

       	//Color Configuration Routes Start       
        Route::any('/products/product_color_config','ProductController@productColorConfig');
        Route::get('products/getProductColorConfig', 'ProductController@getProductColorConfig');
        Route::get('/products/getProductNames', 'ProductController@getProductNamesForSearch');
        Route::any('/products/editProdColorConfig/{id}','ProductController@editProductColorConfig');
        Route::any('/products/updateProdColorConfig','ProductController@updateProductColorConfig');
        Route::any('/products/addProdColorConfig','ProductController@addProductColorConfig');
        Route::any('/products/delProdColorConfig/{id}','ProductController@delProductColorConfig');
        Route::any('/products/checkProdColorConfig/','ProductController@validateProdColorConfig');
        Route::any('products/downloadProdColorConfigExcel','ProductController@downloadProdColorConfigExcel');
        Route::any('products/uploadProdColorConfigExcel','ProductController@importProdColorConfigExcel');
        //Color Configuration Routes End                
	});
});


Route::group(['middleware' => ['mobile']], function () {
    Route::group(['namespace' => 'App\Modules\Product\Controllers'], function (){
        Route::any('cpmanager/uploadtos3', 'ProductS3Controller@uploadToS3');
        Route::any('cpmanager/products/crud/api', 'ProductApiController@createProductAPI');
        Route::any('cpmanager/products/list_all', 'ProductApiController@getAllParentProductsList');
        Route::any('cpmanager/products/pack_level_list', 'ProductApiController@getPackLevelList'); 
        Route::any('cpmanager/products/get_all_details', 'ProductApiController@getCategoriesAndBrands'); 
        Route::any('cpmanager/products/list/all', 'ProductApiController@getAllProductsList'); 
        Route::any('cpmanager/products/checkHSNCode', 'ProductApiController@validateHSNCode'); 
        Route::any('cpmanager/products/search', 'ProductApiController@searchProducts');
        Route::any('cpmanager/products/select/product', 'ProductApiController@getSelectedProduct');  
        Route::any('cpmanager/products/searchProducts', 'ProductApiController@searchAllProducts');  
    });
});
