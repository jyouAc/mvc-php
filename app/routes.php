<?php

use Core\Route;

Route::get('/admin','Admin\AdminController@index');

Route::get('/', 'HomeController');

Route::get('/test', function($request, $response){
	var_dump($request->input());
});

Route::dispatch();