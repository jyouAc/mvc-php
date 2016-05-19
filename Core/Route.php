<?php
namespace Core;
use Core\Config;

Class Route
{
	private static $route;
	private static $methods = array(
		'GET' => array(),
		'POST' => array()
		);
	private static $middleware = array();

	public static function __callStatic($method, $param)
	{
		if(count($param) != 2) {
			throw new Exception("error route prarm");
		}
		$method = strtoupper($method);
		if(!isset(self::$methods[$method])) {
			throw new Exception("$method not found!");
		}
		list($uri, $callback) = $param;
		$uri = '/' . ltrim($uri, '/');
		self::$methods[$method][$uri] = $callback;
	}

	public static function dispatch()
	{
		Logger::info('router dispatch start', self::$methods);
		$request = new Request();
		if(!isset(self::$methods[$request->method][$request->path])) {
			Logger::error($request->method . $request->path . ' not found');
			self::notfound();
		}

		$callback = self::$methods[$request->method][$request->path];
		$request = self::handleMiddleware($request);

		Logger::info('controller start');
		if($callback instanceof \Closure) {
			call_user_func_array($callback, array($request, new Response()));
		} else {
			$callback =  explode('@', $callback);

			if(!isset($callback[0])) {
				Logger::error('empty controller error');
				throw new Exception("empty controller error");
			}

			$controller = self::getController($callback[0]);
			$controller->request = $request;
			$controller->response = new Response();

			$action = isset($callback[1]) ? $callback[1] : DEFALULT_ACTION;

			call_user_func_array(array($controller, $action), array());

		}
	}

	public static function notfound()
	{
		exit('404!');
	}

	private static function getController($str)
	{
		$namespace =  __CONTROLLER__ . '\\' . ltrim($str, '\\');
		$controller = new $namespace;
		
		return $controller;
	}

	protected static function handleMiddleware($request)
	{
		$middlewares = Config::get('Middleware');
		Logger::info('handle Middleware start', $middlewares);
		$pre = null;
		foreach ($middlewares as $middleware) {
			$m = new $middleware;
			$m->setNext($pre);
			$pre = $m;
		}
		Logger::info('handle Middleware end');
		return $m->handle($request);
	}
}