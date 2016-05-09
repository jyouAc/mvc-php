<?php
namespace App\Controller;
use Core\Controller;

class HomeController extends Controller
{
	public function index()
	{
		$this->view('index')
			 ->withTitle('this is a title!')
			 ->withBody('body content!')
			 // ->withPages($this->Db->select('pages', '*'))
			 ->show();
	}
}