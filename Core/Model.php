<?php
namespace Core;
use Core\Db;

class Model
{

	protected $pk = 'id';
	protected $table_name;

	protected $data = [];

	public function __construct()
	{
		$this->table_name = array_pop(explode('\\', strtolower(get_class($this))));
	}

	public function __call($method, $params)
	{
		return call_user_func_array('Core\Db::' . $method, $params);
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

	public function setPk($pk = 'id')
	{
		$this->pk = $pk;
	}

	public function find($id = '')
	{
		$table = $this->table($this->table_name);
		if(!empty($id)) {
			$table->where($this->pk, $id);
		}
		return $table->select();
	}

	public function db($config = null)
	{
		return Db::connect($config);
	}

	public function setTable($table_name = '')
	{
		if(!empty($table_name)) {
			$this->table_name = $table_name;
			return true;
		}
		return false;
	}

	public function __set($name, $value)
	{
		if($name == 'data' && is_array($value)) {
			$this->data = array_merge($this->data, $value);
		}

		$this->data[$name] = $value;
	}

	public function save($data = [])
	{
		$this->data = array_merge($this->data, $data);
		if(!empty($this->data)) {
			$res = $this->table($this->table_name)->field(array_keys($this->data))->data($this->data)->insert();
			$this->data = [];
			return $res;
		}
		return false;
	}

}