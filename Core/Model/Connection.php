<?php
namespace Core\Model;

class Connection implements IConnection
{

	protected $config = [
	    'database_type' => 'mysql',
	    'database_name' => 'database',
	    'server' => 'localhost',
	    'username' => 'root',
	    'password' => '',
	    'charset' => 'utf8'
	];

	protected $conn = null;

	public function __construct()
	{

	}

	public function connect()
	{

	}

	public function close()
	{

	}

	public function transaction()
	{

	}

	public function rollBack()
	{

	}

	public function setCharset()
	{

	}

	public function ping()
	{

	}
	
	public function commit()
	{

	}

	public function setConfig($config)
	{
		$this->config = array_merge($this->config, $config);
	}

	public function getConfig()
	{
		return $this->config;
	}

	public function getConnect()
	{
		return $this->conn;
	}
}