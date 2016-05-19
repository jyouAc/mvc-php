<?php
namespace Core;

use Core\Log\Log;

use RuntimeException;

class Logger{

	private static $loggers = [];

	private static $current_key = '';

	public static function createLogger($key, $directory = '/tmp/logs', $level = Log::DEBUG)
	{
		if(isset(self::$loggers[$key])) {
			throw new RuntimeException("loggers $key already exists", 1);
		}

		self::$current_key = $key;

		self::$loggers[$key] = new Log($directory, $level);
		self::$loggers[$key]->prefix = $key;

		return true;
	}

	public static function setCurrentLogger($key)
	{
		if(isset(self::$loggers[$key])) {
			self::$current_key = $key;
			return true;
		}
		return false;
	}

	public static function __callStatic($method, $param)
	{
		return call_user_func_array([self::$loggers[self::$current_key], strtolower($method)], $param);
	}

}
