<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constant.php';

if(DEBUG) {
    register_shutdown_function('shutdown_function');  //Fatal error
    set_error_handler("myErrorHandler");              // error but not Fatal error
    set_exception_handler('exception_handler');
}

require_once __DIR__ . '/../app/routes.php';