<?php
namespace App\Controller\Admin;
use Core\Controller;

class AdminController extends Controller
{
	public function index()
	{
		$this->view('admin.home')
			 ->withTitle('this is a title!')
			 ->withBody('body content!')
			 ->withPages($this->Db->select('pages', '*'))
			 ->show();
	}
}