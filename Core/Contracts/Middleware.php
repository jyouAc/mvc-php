<?php

namespace Core\Contracts;
use Closure;

interface Middleware
{

	public function handle($request);

}