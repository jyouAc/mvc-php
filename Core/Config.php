<?php
namespace Core;

class Config
{
	public static function get($name)
	{
		$file = CONFIG_ROOT . '/' . $name . '.php';
		if(is_file($file)) {
			$config = require_once $file;
			return $config;
		}
		throw new Exception($name . " not exitst!");
	}
}