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
    protected $options = [
    	'table' 		=> null,
    	'distinct' 		=> null,
    	'field'			=> null,
    	'join'			=> null,
    	'where'			=> null,
    	'group'			=> null,
    	'having'		=> null,
    	'order'			=> null,
    	'limit'			=> null,
    	'union'			=> null,
    	'lock'			=> null,
    	'comment'		=> null,
    	'force'			=> null,
    	'using'			=> null,
    ];

    // 数据库表达式
    protected $exp = ['eq' => '=', 'neq' => '<>', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'notlike' => 'NOT LIKE', 'like' => 'LIKE', 'in' => 'IN', 'exp' => 'EXP', 'notin' => 'NOT IN', 'not in' => 'NOT IN', 'between' => 'BETWEEN', 'not between' => 'NOT BETWEEN', 'notbetween' => 'NOT BETWEEN', 'exists' => 'EXISTS', 'notexists' => 'NOT EXISTS', 'not exists' => 'NOT EXISTS', 'null' => 'NULL', 'notnull' => 'NOT NULL', 'not null' => 'NOT NULL'];
    // 查询表达式
    protected $selectSql    = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';
    protected $insertSql    = 'INSERT INTO %TABLE% (%FIELD%) VALUES %VALUES% %COMMENT%';
    protected $updateSql    = 'UPDATE %TABLE% SET %SET% %JOIN% %WHERE% %ORDER%%LIMIT% %LOCK%%COMMENT%';
    protected $deleteSql    = 'DELETE FROM %TABLE% %USING% %JOIN% %WHERE% %ORDER%%LIMIT% %LOCK%%COMMENT%';

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function setQuery($query)
	{
		$this->query = $query;
	}

	public function select($options)
	{
		$options = array_merge($this->options, $options);
		$sql = str_replace(
            ['%TABLE%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'],
            [
                $this->parseTable($options['table']),
                $this->parseDistinct($options['distinct']),
                $this->parseField($options['field']),
                $this->parseJoin($options['join']),
                $this->parseWhere($options['where']),
                $this->parseGroup($options['group']),
                $this->parseHaving($options['having']),
                $this->parseOrder($options['order']),
                $this->parseLimit($options['limit']),
                $this->parseUnion($options['union']),
                $this->parseLock($options['lock']),
                $this->parseComment($options['comment']),
                $this->parseForce($options['force'])
            ], $this->selectSql);
        return $sql;
	}

	public function update($options)
	{
		$options = array_merge($this->options, $options);
        $data = isset($options['data'][0]) ? $options['data'][0] : [];
        $set = [];
        if (empty($data)) {
            return '';
        }
        foreach ($data as $key => $val) {
            $set[] = $key . '=' . $val;
        }
		$sql = str_replace(
			['%TABLE%', '%SET%', '%JOIN%', '%WHERE%', '%ORDER%', '%LIMIT%', '%LOCK%', '%COMMENT%'],
			[
				$this->parseTable($options['table']),
				implode(',', $set),
				$this->parseJoin($options['join']),
				$this->parseWhere($options['where']),
				$this->parseOrder($options['order']),
				$this->parseLimit($options['limit']),
				$this->parseLock($options['lock']),
				$this->parseComment($options['comment']),
			], 
			$this->updateSql);
		return $sql;
	}

	public function delete($options)
	{
		$options = array_merge($this->options, $options);
		$sql = str_replace(
			['%TABLE%', '%USING%', '%JOIN%', '%WHERE%', '%ORDER%', '%LIMIT%', '%LOCK%', '%COMMENT%'],
			[
				$this->parseTable($options['table']),
				$this->parseUsing($options['using']),
				$this->parseJoin($options['join']),
				$this->parseWhere($options['where']),
				$this->parseOrder($options['order']),
				$this->parseLimit($options['limit']),
				$this->parseLock($options['lock']),
				$this->parseComment($options['comment']),
			], 
			$this->deleteSql);
		return $sql;
	}

	public function insert($options)
	{
		$options = array_merge($this->options, $options);
		if(empty($options['data'])) {
			return '';
		}
		$sql = str_replace(
			['%TABLE%', '%FIELD%', '%VALUES%', '%COMMENT%'],
			[
				$this->parseTable($options['table']),
				$this->parseField($options['field']),
				$this->parseValues($options['data']),
				$this->parseComment($options['comment']),
			], 
			$this->insertSql);
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

	public function parseJoin($joins)
	{
		$res = ' ';
		foreach ((array)$joins as $key => $join) {
			if(is_string($join['join'])) {
				$join['join'] = explode(',', $join['join']);
			}
			$arr = [];
			foreach ($join['join'] as $table => $alias) {
				$arr[] = is_numeric($table) ? $this->parseKey($alias) : $this->parseKey($table) . ' AS ' . $this->parseKey($alias);
			}
			$table = implode(' , ', $arr);
			$res .= $join['type'] . ' ' . $table . ' ON ' . $join['on'] . ' ';
		}
		if(empty(trim($res))) {
			return '';
		}
		return $res;
	}

	public function parseKey($key)
	{
		return $key;
		// return addcslashes($key);
	}

	public function parseField($fields)
	{
		if(empty($fields)) {
			return '*';
		}
		if(is_string($fields)) {
			$fields = explode(',', $fields);
		}
		$arr = [];
		foreach ($fields as $field => $alias) {
			$arr[] = is_numeric($field) ? $this->parseKey($alias) : $this->parseKey($field) . ' AS ' . $this->parseKey($alias);
		}
		return implode(' , ', $arr);
	}

	public function parseWhere($wheres)
	{
		$where_str = '';
		foreach ((array)$wheres as $key => $where) {
			$where_str .= 'AND (' . implode(' ', $where) . ') ';
		}
		$where_str = substr($where_str, 4);

		if(!empty($where_str)) {
			$where_str = ' WHERE ' . $where_str;
		}
		return $where_str;
	}

	public function parseDistinct($distinct)
	{
		return '';
	}

	public function parseGroup($groups)
	{
		if(empty($groups)) {
			return '';
		}
		return ' GROUP BY ' . ( is_array($groups) ? implode(' , ', $groups) : $groups );
	}

	public function parseHaving($havings)
	{
		return '';
	}

	public function parseOrder($orders)
	{
		if(empty($orders)) {
			return '';
		}

		foreach ((array)$orders as $order) {
			if(is_array($order)) {
				foreach ($order as $field => $by) {
					$arr[] = is_numeric($field) ? $by : $field . ' ' . $by;
				}
			}else {
				$arr[] = (string) $order;
			}
		}
		return ' ORDER BY ' . implode(' , ', $arr);
	}

	public function parseLimit($limits)
	{
		$limit = '';
		if(is_array($limits)) {
			if(count($limits) == 1) {
				$limit = '0,' . $limits[0];
			}else if(count($limits) == 2) {
				$limit = implode(',', $limits);
			}
		} else {
			$limit = (string)$limits;
		}
		if(!empty($limit)) {
			$limit = ' LIMIT ' . $limit . ' ';
		}
		return $limit;
	}

	public function parseValues($values)
	{
		if(empty($values)) {
			return '';
		}
		foreach ((array)$values as $value) {
			$res[] = '(' . implode(',', $value) . ')'; 
		}
		return implode(' , ', $res);
	}

	public function parseUnion($unions)
	{
		return '';
	}

	public function parseLock($locks)
	{
		return '';
	}

	public function parseComment($comments)
	{
		return '';
	}

	public function parseForce($forces)
	{
		return '';
	}

	public function parseUsing($using)
	{
		return '';
	}

}