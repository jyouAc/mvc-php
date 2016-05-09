<?php
namespace App\Controller\Admin;
use Core\Controller;

class AdminController extends Controller
{
	public function index()
	{

		$this->db
				->table('pages')
				->where('title', 'title5')
				->getSql(true)
				->delete();

		$this->db
			->table('pages')
			->where('id', 17)
			->getSql(true)
			->data(array('title' => 4, 'body' => 'body85', 'slug' => 'slug'))
			->update();

		$this->db
			->table(array('pages' => 'p'))
	 		->join(array('users' => 'u'), 'p.user_id=u.id')
	 		->field('p.id,title,body')
	 		->where('p.id', '>', 3)
	 		->where('user_id', 1)
	 		->where('title', 'like', "'%4%'")
	 		->order('p.body')
	 		->limit(4)
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
				 		->table('pages')
						->field('id,title,body')
						->select()
			 	)
			 ->show();
	}
}