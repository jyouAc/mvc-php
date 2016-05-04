<?php
namespace Core;

use Closure;
use Core\Contracts\Middleware as MiddlewareContracts;

class Middleware implements MiddlewareContracts
{
	protected $next = null;

	public function handle($request)
	{
		return $this->next($request);
	}

	protected function next($request)
	{
		if($this->next != null) {
			$this->next->handle($request);
		}
		return $request;
	}

	public function setNext($middleware)
	{
		$this->next = $middleware;
	}
}