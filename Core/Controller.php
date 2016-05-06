<?php
namespace Core;
use Core\Db;

class Controller
{
	public $request;
	public $response;
	private $view = null;
	private $db = null;

	public function view($view_path)
	{
		if($this->view == null) {
			$this->view = new View($view_path);
		}
		return $this->view;
	}

	public function __get($name)
	{
		switch (strtolower($name)) {
			case 'db':
				return $this->db();
				break;
			
			default:
				# code...
				break;
		}
	}

	private function db()
	{
		return Db::connect();
	}

}