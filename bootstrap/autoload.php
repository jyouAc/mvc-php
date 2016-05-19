<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constant.php';

if(DEBUG) {
    register_shutdown_function('shutdown_function');  //Fatal error
    set_error_handler("myErrorHandler");              // error but not Fatal error
    set_exception_handler('exception_handler');
}

class_alias('\Core\Logger', 'Logger', true);

Logger::createLogger('system', __APP__ . '/logs');

require_once __DIR__ . '/../app/routes.php';