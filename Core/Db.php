<?php
namespace Core;

use Core\Exception;
use Core\Config;

class Db
{
	private static $instances = [];

	private static $instance = null;

	// 查询次数
    public static $queryTimes = 0;
    // 执行次数
    public static $executeTimes = 0;

	public static function connect($config = [])
	{
		$md5 = md5(serialize($config));

		if (!isset(self::$instances[$md5])) {

            $options = self::parseConfig($config);

            if (empty($options['type'])) {
                throw new Exception('db type error');
            }
            $connection = __NAMESPACE__ . '\\Db\\Connector\\' . ucfirst($options['type']);
            self::$instances[$md5] = new $connection($options);
        }

        self::$instance = self::$instances[$md5];
        return self::$instance;
	}

	public static function parseConfig($config)
	{
		if(empty($config)) {
			$config = Config::get('database');
		}
		
		if(is_string($config)) {
			$config = self::parseDsn($config);
		}

		return $config;
	}

	public static function parseDsn($config)
	{
		$info = parse_url($dsnStr);
        if (!$info) {
            return [];
        }
        $dsn = [
            'type'     => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'hostname' => isset($info['host']) ? $info['host'] : '',
            'hostport' => isset($info['port']) ? $info['port'] : '',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? $info['fragment'] : 'utf8',
        ];

        if (isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }
        return $dsn;
	}

	public static function __callStatic($method, $params)
	{
		if(is_null(self::$instance)) {
			self::connect();
		}

		return call_user_func_array([self::$instance, $method], $params);
	}
}