<?php
namespace App\Controller\Admin;
use Core\Controller;
use Core\Db;

class AdminController extends Controller
{
	public function index()
	{
		$this->view('admin.home')
			 ->withTitle('this is a title!')
			 ->withBody('body content!')
			 ->withPages(Db::query('select * from pages'))
			 ->show();
	}
}