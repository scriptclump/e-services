<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'App\Modules\Notifications\Controllers'], function () {
        Route::group(['prefix' => 'notification', 'before' => 'authenticates'], function() {
            Route::get('index', 'NotificationsController@indexAction');
            Route::get('addnotification', 'NotificationsController@addNotification');
            Route::get('getmynotifications/{type_id}', 'NotificationsController@getMyNotifications');
            Route::post('changestatus', 'NotificationsController@changeStatus');
            Route::any('tasks', 'NotificationsController@getTasks');
            Route::any('templates', 'NotificationsController@getTemplates');
            Route::any('deletetemplate/{id}', 'NotificationsController@deleteTemplate');
            Route::any('edittemplate', 'NotificationsController@editTemplate');
            Route::any('addtemplate', 'NotificationsController@addTemplate');
            Route::any('updatetemplate', 'NotificationsController@updateTemplate');
            Route::any('notifyrm', 'NotificationsController@notifyRm');
            Route::any('validatecode', 'NotificationsController@validateCode');
            Route::any('viewall', 'NotificationsController@viewAll');
            Route::get('all_notifications', 'NotificationsController@allNotifications');
        });
    });
});