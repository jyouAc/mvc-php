<?php
namespace Core;
use Core\Contracts\Request as RequestContracts;

class Request implements RequestContracts
{

	public $get_data;

	public $post_data;

	public $path;

	public $method;

	public function __construct()
	{
		$this->get_data = $_GET;
		$this->post_data = $_POST;
		$this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$this->method = $_SERVER['REQUEST_METHOD'];
	}


	public function input($name)
	{
		if(isset($this->get_data[$name])) {
			return $this->get_data[$name];
		}

		if(isset($this->post_data[$name])) {
			return $this->post_data[$name];
		}

		return null;
	}

	// public function getGetData()
	// {
	// 	return $this->getData;
	// }

	// public function getPostData()
	// {
	// 	return $this->postData;
	// }

	// public function getPath()
	// {
	// 	return $this->path;
	// }

}