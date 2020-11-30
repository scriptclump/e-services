<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
View::composer('layouts.sideview', 'App\Http\Controllers\HeaderController');

if(Session::has('userId'))
    Route::get('/','WelcomeController@index');
else
    Route::get('/','AuthenticationController@index');
Route::get('about', function()
{
    return View::make('about');
});


//Consolidates all the routes from routes folder
foreach (glob(__DIR__ . '/routes/*.php') as $route_file)
{
    require $route_file;
}
