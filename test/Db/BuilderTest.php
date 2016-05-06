<?php
namespace Test\Db;

use PHPUnit_Framework_TestCase;
use Core\Db\Builder\Mysql as builder;
use Core\Db;

class BuilderTest extends PHPUnit_Framework_TestCase
{

	/**
     * @dataProvider parseTableProvider
     */
	public function testParseTable($tables, $expected)
	{
		$builder = new Builder(Db::connect());
		$tables_str = $builder->parseTable($tables);
		$this->assertEquals($expected, $tables_str);
	}

	public function parseTableProvider()
	{
		return array(
			array('pages,users', 'pages , users'),
			array(array('pages', 'users'), 'pages , users'),
			array(array('pages', 'users', 'articles'), 'pages , users , articles'),
			array(array('pages' => 'p','users' => 'u'), 'pages AS p , users AS u'),
		);
	}

	/**
     * @dataProvider parseJoinProvider
     */
	public function testParseJoin($joins, $expected)
	{
		$builder = new Builder(Db::connect());
		$join_str = $builder->parseJoin($joins);
		$this->assertEquals($expected, $join_str);
	}

	public function parseJoinProvider()
	{
		return array(
			array(array(
						array('join' => array('users' => 'u'), 'on' => 'p.user_id=u.id', 'type' => 'INNER JOIN'),
						array('join' => array('articles' => 'a'), 'on' => 'p.user_id=a.id', 'type' => 'INNER JOIN'),
					), 
				' INNER JOIN users AS u ON p.user_id=u.id INNER JOIN articles AS a ON p.user_id=a.id '
			),
			array(array(
						array('join' => 'users u', 'on' => 'p.user_id=u.id', 'type' => 'INNER JOIN'),
					),
				' INNER JOIN users u ON p.user_id=u.id '
			)
		);
	}

	/**
     * @dataProvider parseFieldProvider
     */
	public function testParseField($fields, $expected)
	{
		$builder = new Builder(Db::connect());
		$fields_str = $builder->parseField($fields);
		$this->assertEquals($expected, $fields_str);
	}

	public function parseFieldProvider()
	{
		return array(
			array('pages,users', 'pages , users'),
			array(array('pages', 'users'), 'pages , users'),
			array(array('pages', 'users', 'articles'), 'pages , users , articles'),
			array(array('pages' => 'p','users' => 'u'), 'pages AS p , users AS u'),
			array('artist_id,count(id) count', 'artist_id , count(id) count')
		);
	}

	/**
     * @dataProvider selectProvider
     */
	public function testSelect($options, $expected)
	{
		$builder = new Builder(Db::connect());
		$sql = $builder->select($options);
		$this->assertEquals($expected, $sql);
	}

	public function selectProvider()
	{
		return array(
			array(array('table' => 'pages,user','field' => 'id,title,body'), 'SELECT id , title , body FROM pages , user ')
		);
	}

	/**
	 * @dataProvider parseWhereProvider
	 */
	public function testParseWhere($wheres, $expected)
	{
		$builder = new Builder(Db::connect());
		$where = $builder->parseWhere($wheres);
		$this->assertEquals($expected, $where);
	}

	public function parseWhereProvider()
	{
		return array(
			array(
				array(array('id', '>', 3), array('user_id', '=', 1), array('title', 'like', "'%4%'")), 
				' WHERE (id > 3) AND (user_id = 1) AND (title like \'%4%\') '
			)
		);
	}

	/**
	 * @dataProvider parseLimitProvider
	 */
	public function testParseLimit($limits, $expected)
	{
		$builder = new Builder(Db::connect());
		$limit = $builder->parseLimit($limits);
		$this->assertEquals($expected, $limit);
	}

	public function parseLimitProvider()
	{
		return array(
			array(array(2, 3), ' LIMIT 2,3 '),
			array(10, ' LIMIT 10 '),
			array('2,5', ' LIMIT 2,5 ')
		);
	}

	/**
	 * @dataProvider parseGroupProvider
	 */
	public function testParseGroup($groups, $expected)
	{
		$builder = new Builder(Db::connect());
		$groupBy = $builder->parseGroup($groups);
		$this->assertEquals($expected, $groupBy);
	}

	public function parseGroupProvider()
	{
		return array(
				array(array('pages.id','users.id'), ' GROUP BY pages.id , users.id'),
				array('pages.id,users.id', ' GROUP BY pages.id,users.id'),
			);
	}

	/**
	 * @dataProvider parseOrderProvider
	 */
	public function testParseOrder($orders, $expected)
	{
		$builder = new Builder(Db::connect());
		$orderBy = $builder->parseOrder($orders);
		$this->assertEquals($expected, $orderBy);
	}

	public function parseOrderProvider()
	{
		return array(
				array(array(array('pages.id' => 'DESC','users.id')), ' ORDER BY pages.id DESC , users.id'),
				array(array('pages.id,users.id'), ' ORDER BY pages.id,users.id'),
			);
	}
}