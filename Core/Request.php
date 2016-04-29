<?php
namespace Core;

class Request
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