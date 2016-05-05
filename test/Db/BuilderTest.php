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


}