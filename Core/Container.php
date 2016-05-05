<?php
namespace Core;

use Closure;

class Container
{
	private $binds = array();

	private $instances = array();

	public function bind($abstract, $concrete)
	{
		if($concrete instanceof Closure) {
			$this->binds[$abstract] = $concrete;
		} else {
			$this->instances[$abstract] = $concrete;
		}

	}

	public function make($abstract, $param)
	{
		if(isset($this->instances[$abstract])) {
			return $this->instances[$abstract];
		}
		array_unshift($param, $this);

		return call_user_func_array($this->binds[$abstract], $param);
	}
}