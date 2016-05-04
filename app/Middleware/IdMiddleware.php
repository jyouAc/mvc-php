<?php
namespace App\Middleware;
use Closure;
use Core\Middleware;

class IdMiddleware extends Middleware{

	public function handle($request)
	{

		if($request->input('id') >= 20) {
			exit('.....');
		}

		return $this->next($request);
	}

}