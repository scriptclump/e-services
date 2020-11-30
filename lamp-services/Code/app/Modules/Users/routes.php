<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Users\Controllers'], function () {
        Route::group(['before' => 'authenticates'], function() {
            Route::get('users/index', 'UsersController@usersList');
            Route::any('users/usersList', 'UsersController@usersGrid');
            /*route for getting the users count */
            Route::any('users/usersCount', 'UsersController@usersCount');
            Route::get('users/addusers', 'UsersController@addUsers');
            Route::get('users/getchannels', 'UsersController@getChannels');
            Route::get('users/getcompanys', 'UsersController@getCompanys');
            Route::get('users/getbusinessunit/{bu_id}', 'UsersController@getBusinessUnit');
            Route::get('users/getsegments', 'UsersController@getSegments');
            Route::get('users/getcategories', 'UsersController@getCategories');
            Route::get('users/getparentcategory/{user_id}/{category_id}', 'UsersController@getParentCategory');
            Route::get('users/getcategory/{user_id}/{category_id}', 'UsersController@getCategory');
            Route::get('users/getproducts', 'UsersController@getProducts');
            Route::get('users/saveusers', 'UsersController@saveUser');
            Route::get('users/updateuser', 'UsersController@updateUser');
            Route::post('users/saveusersaccess', 'UsersController@saveUserAccess');
            Route::any('users/checkEmailExist', 'UsersController@checkEmailExist');
            Route::any('users/editusers/{userid}', 'UsersController@editUsers');
            Route::any('users/validateemail', 'UsersController@validateEmail');
            Route::any('users/validatemobileno', 'UsersController@validateMobileno');
            Route::any('users/deleteUser', 'UsersController@deleteUser');
            Route::any('users/getreportingmanagers', 'UsersController@getReportingManagers');
            Route::any('users/assignChildUserToParentUser', 'UsersController@assignChildUserToParentUser');
            Route::any('users/blockuser', 'UsersController@blockUser');
            Route::any('users/exportusers', 'UsersController@exportUsers');
            Route::get('users/cashbackhistory/{legal_entity_id}/{user_id}', 'UsersController@getCashBackHistory');
            Route::get('users/applyredeem', 'UsersController@applyRedeem');
            Route::any('users/exportredeem', 'UsersController@exportRedeem');
            Route::any('users/importredeem', 'UsersController@importRedeem');
            Route::any('users/getbu','UsersController@odersTabGetBuUnit');
            Route::any('users/impersonateusers', 'UsersController@impersonateUsers');
            Route::any('users/backtoadmin', 'UsersController@backToAdmin');
            Route::any('users/userpassword', 'UsersController@getUserPassword');

        });
    });
});
