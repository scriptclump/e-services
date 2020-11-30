<?php

Route::get('cockpit', 'GridController@index');
Route::get('cockpit/products', 'GridController@getCockpitProducts');
Route::get('cockpit/childs', 'GridController@getCockpitChilds');

