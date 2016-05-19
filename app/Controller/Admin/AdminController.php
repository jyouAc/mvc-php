<?php
namespace App\Controller\Admin;

use Core\Controller;

use App\Model\Page;

use Core\Log\Log;

class AdminController extends Controller
{

	public function index()
	{
		exit;
		$page = new Page();
		$page->body = 'faef';
		$page->title = 'title test';
		// echo json_encode($page->find(3));exit;
		// echo json_encode($page->save());exit;
		echo json_encode($page->query('desc page'));exit;

		 $this->db
				->table('page')
				->where('id', 2)
				// ->where('title', 'title5')
				->getSql(true)
				->delete();exit;

		$this->db
			->table('page')
			->where('id', 17)
			->getSql(true)
			->data(array('title' => 4, 'body' => 'body85', 'slug' => 'slug'))
			->update();

		$this->db
			->table(array('page' => 'p'))
	 		->join(array('user' => 'u'), 'p.user_id=u.id')
	 		->field('p.id,title,body')
	 		->where('p.id', '>', 3)
	 		->where('user_id', 1)
	 		->where('title', 'like', "'%4%'")
	 		->order('p.body')
	 		->limit(4)
	 		->select();

		$this->db
			->table('page')
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
				 		->table('page')
						->field('id,title,body')
						->select()
			 	)
			 ->show();
	}
}