<?php
namespace App\Controller\Admin;
use Core\Controller;

class AdminController extends Controller
{
	public function index()
	{

		$this->db
			->table('pages')
			->where('id', 5)
			->getSql(true)
			->data(array('title' => 'title0.0', 'body' => 'body85'))
			->update();

		$this->db
			->table('pages')
			->where('id', 5)
			->field('p.id,title,body')
			->getSql(true)
			->select();

		$this->db
			->table('pages')
			->field('title,body')
			->data(array(array('title5','body85'),array('tit44lefewa','bodyf444awe85')), true)
			->data(array('titlefewa','bodyfawe85'))
			->getSql(true)
			->insert();

		$this->view('admin.home')
			 ->withTitle('this is a title!')
			 ->withBody('body content!')
			 ->withPages(
			 		$this->db
			 		->table(array('pages' => 'p'))
			 		->join(array('users' => 'u'), 'p.user_id=u.id')
			 		->field('p.id,title,body')
			 		->where('p.id', '>', 3)
			 		->where('user_id', 1)
			 		->where('title', 'like', "'%4%'")
			 		->order('p.body')
			 		->limit(4)
			 		->select()
			 	)
			 ->show();
	}
}