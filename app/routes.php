<?php

use Core\Route;

Route::get('/admin', 'Admin\AdminController');

Route::get('/', 'HomeController');

Route::dispatch();