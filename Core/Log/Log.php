<?php
namespace Core\Log;

use RuntimeException;

use Psr\Log\AbstractLogger;

use Psr\Log\LogLevel;

class Log extends AbstractLogger
{
	const EMERGENCY 	= LogLevel::EMERGENCY;
	const ALERT 		= LogLevel::ALERT;
	const CRITICAL 		= LogLevel::CRITICAL;
	const ERROR 		= LogLevel::ERROR;
	const WARNING 		= LogLevel::WARNING;
	const NOTICE 		= LogLevel::NOTICE;
	const INFO 			= LogLevel::INFO;
	const DEBUG 		= LogLevel::DEBUG;

	private $log_level_threshold = [
		self::EMERGENCY => 7,
		self::ALERT 	=> 6,
		self::CRITICAL 	=> 5,
		self::ERROR 	=> 4,
		self::WARNING 	=> 3,
		self::NOTICE 	=> 2,
		self::INFO 		=> 1,
		self::DEBUG 	=> 0
	];

	private $log_option = [
		'ext'				=> '.log',
		'prefix'			=> '',
		'log_format' 		=> '[%date%] [%level%] %message%',
		'log_date_fromat'	=> 'Y-m-d H:i:s.%u%'
	];

	private $directory = null;

	private $log_file = null;

	private $log_file_handler = null;

	private $default_mode = 0777;

	private $log_threshold = 0;

	public function __construct($directory = '/tmp/logs', $level = self::DEBUG)
	{
		is_dir($directory) OR @mkdir($directory, $default_mode, true);
		$this->directory = $directory;

		$this->log_threshold = isset($this->log_level_threshold[$level]) ? $this->log_level_threshold[$level] : $this->log_level_threshold[self::DEBUG];
	}

	public function log($level = self::DEBUG, $message, array $context = array())
	{	
		if(!isset($this->log_level_threshold[$level]) || $this->log_level_threshold[$level] < $this->log_threshold) {
			return false;
		}

		if($this->log_file != $this->getLogFile() || !$this->log_file_handler) {
			$this->setLogFile('a');
		}
		$msg = str_replace(['%date%', '%level%', '%message%'], [$this->getLogDateFormat(), strtoupper($level), (string)$message], $this->log_option['log_format']) . PHP_EOL;

		$context = $this->contextConvertToStr($context);
		if(!empty($context)) {
			$msg .= $context . PHP_EOL;
		}

		return fwrite($this->log_file_handler, $msg);
	}

	private function getLogFile()
	{
		return $this->directory . DIRECTORY_SEPARATOR .$this->log_option['prefix'] . date("Y-m-d", time()) .$this->log_option['ext'];
	}

	private function getLogDateFormat($micro = '')
	{
		if(empty($micro)) {
			$micro = microtime();
		}
		$fromat_date = str_replace('%u%', substr($micro, 2, 6), $this->log_option['log_date_fromat']);
		return date($fromat_date);
	}

	private function setLogFile($mode = 'a')
	{
		$this->log_file = $this->getLogFile();
		$this->log_file_handler = fopen($this->log_file, $mode);
		if(!$this->log_file_handler) {
			throw new RuntimeException("logger not access to write file", 1);
		}
	}

	private function contextConvertToStr($context = array())
	{
		if(!is_array($context) || empty($context)) {
			return '';
		}
		return json_encode($context);
	}

	public function __set($name, $value)
	{
		$this->log_option[$name] = $value;
	}
}