<?php

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b> ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b> WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b> NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr in file $errfile($errline)<br />\n";
        break;
    }
    return true;
}

function exception_handler($exception) {
  echo "Uncaught exception: " , $exception->getMessage(), "\n";
}

function shutdown_function()  
{  
    $e = error_get_last();    
    print_r($e);  
}

function starts_with($a, $b)
{
	return substr($a, 0, strlen($b)) == $b;
}