<?php
namespace Core\Model;

interface IConnection
{
	public function connect();

	public function close();

	public function transaction();

	public function rollBack();

	public function commit();

	public function setConfig();

	public function getConfig();

	public function getConnect();

	public function ping();
}