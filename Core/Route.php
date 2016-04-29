<?php
namespace Core;

Class Route
{
	private static $route;
	private static $methods = array(
		'GET' => array(),
		'POST' => array()
		);

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
		$request = new Request();
		
		if(!isset(self::$methods[$request->method][$request->path])) {
			self::notfound();
		}

		$callback = self::$methods[$request->method][$request->path];
		if($callback instanceof \Closure) {
			$view = call_user_func_array($callback, array($request, new Response()));
		} else {
			$callback =  explode('@', $callback);

			if(!isset($callback[0])) {
				throw new Exception("empty controller error");
			}
			$controller = self::getController($callback[0]);
			$controller->request = $request;
			$controller->response = new Response();

			$action = isset($callback[1]) ? $callback[1] : DEFALULT_ACTION;

			$view = call_user_func_array(array($controller, $action), array());

		}

		if($view instanceof View) {

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

}