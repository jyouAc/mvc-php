<?php
namespace Core\Model;

class Mysql extends Connection
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

	public function __construct($config = array())
	{
		$this->config = array_merge($this->config, $config);
	}

	public function connect()
	{
		$this->conn = mysql_connect($this->config['server'], $this->config['username'], $this->config['password']);
		mysql_select_db($this->config['database'], $this->conn) or die ("Can\'t use $this->conn : " . mysql_error());
		$this->setCharset($this->config['charset']);
	}

	public function close()
	{
		if($this->conn != null) {
			return mysql_close($this->conn);
		}
		return false;
	}

	public function ping()
	{
		if($this->conn != null) {
			return mysql_ping($this->conn);
		}
		return false;
	}

	public function transaction()
	{
		mysql_query("START TRANSACTION", $this->getConnect());
	}

	public function rollBack()
	{
		mysql_query("ROLLBACK", $this->getConnect());
	}

	public function setCharset($charset)
	{
		mysql_query("set names " . $charset, $this->getConnect());
	}

	public function commit()
	{
		mysql_query("COMMIT", $this->getConnect());
	}

}