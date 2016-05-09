<?php
namespace Core;
use Core\Contracts\Request as RequestContracts;

class Request implements RequestContracts
{

	public $get;

	public $post;

	public $path;

	public $method;

	public function __construct()
	{
		$this->get = $_GET;
		$this->post = $_POST;
		$this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$this->method = $_SERVER['REQUEST_METHOD'];
	}


	public function input($name)
	{
		if(isset($this->get[$name])) {
			return $this->get[$name];
		}

		if(isset($this->post[$name])) {
			return $this->post[$name];
		}

		return null;
	}

}