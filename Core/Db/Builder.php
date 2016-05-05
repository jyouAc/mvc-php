<?php
namespace Core\Db;

use PDO;
use Core\Db;
use Core\Exception;

abstract class Builder
{
	// db对象实例
    protected $connection;
    protected $query;

    // 查询参数
    protected $options = [];

    // 数据库表达式
    protected $exp = ['eq' => '=', 'neq' => '<>', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'notlike' => 'NOT LIKE', 'like' => 'LIKE', 'in' => 'IN', 'exp' => 'EXP', 'notin' => 'NOT IN', 'not in' => 'NOT IN', 'between' => 'BETWEEN', 'not between' => 'NOT BETWEEN', 'notbetween' => 'NOT BETWEEN', 'exists' => 'EXISTS', 'notexists' => 'NOT EXISTS', 'not exists' => 'NOT EXISTS', 'null' => 'NULL', 'notnull' => 'NOT NULL', 'not null' => 'NOT NULL'];
    // 查询表达式
    protected $selectSql    = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';
    protected $insertSql    = '%INSERT% INTO %TABLE% (%FIELD%) VALUES (%DATA%) %COMMENT%';
    protected $insertAllSql = 'INSERT INTO %TABLE% (%FIELD%) %DATA% %COMMENT%';
    protected $updateSql    = 'UPDATE %TABLE% SET %SET% %JOIN% %WHERE% %ORDER%%LIMIT% %LOCK%%COMMENT%';
    protected $deleteSql    = 'DELETE FROM %TABLE% %USING% %JOIN% %WHERE% %ORDER%%LIMIT% %LOCK%%COMMENT%';

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function select($options)
	{
		$sql = str_replace(
            ['%TABLE%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'],
            [
                $this->parseTable($options['table']),
                $this->parseDistinct($options['distinct']),
                $this->parseField($options['field']),
                $this->parseJoin($options['join']),
                $this->parseWhere($options['where'], $options['table']),
                $this->parseGroup($options['group']),
                $this->parseHaving($options['having']),
                $this->parseOrder($options['order']),
                $this->parseLimit($options['limit']),
                $this->parseUnion($options['union']),
                $this->parseLock($options['lock']),
                $this->parseComment($options['comment']),
                $this->parseForce($options['force']),
            ], $this->selectSql);
        return $sql;
	}

	public function parseTable($tables)
	{
		if(is_string($tables)) {
			$tables = explode(',', $tables);
		}
		foreach ($tables as $table => $alias) {
			$arr[] = is_numeric($table) ? $this->parseKey($alias) : $this->parseKey($table) . ' AS ' . $this->parseKey($alias);
		}
		return implode(' , ', $arr);
	}

	public function parseKey($key)
	{
		return $key;
		// return addcslashes($key);
	}

	public function parseField($fields)
	{
		if(is_string($fields)) {
			$fields = explode(',', $fields);
		}
		foreach ($fields as $field => $alias) {
			$arr[] = is_numeric($field) ? $this->parseKey($alias) : $this->parseKey($field) . ' AS ' . $this->parseKey($alias);
		}
		return implode(' , ', $arr);
	}

}