<?php
namespace Core;

use Core\View\Compile;

class View
{
	private $data = array();
	private $path = '';
	private $compile = null;
	private $view_path;
	private $config;

	public function __construct($view_path, $config = array())
	{
		$this->path = VIEW_ROOT . '/' .str_replace('.', '/', $view_path) . VIEW_SUFFIX;
		$this->view_path = $view_path;
		$this->config = array_merge(Config::get('view'), $config);
		$this->template_file = $view_path;
	}

	public function with($key, $value = null)
	{
		$this->data[$key] = $value;
	}

	public function show()
	{
		$this->checkPath();
		if($this->needReCompile()) {
			$this->compile = new Compile($this->view_path);
			$this->content = $this->compile->compile(file_get_contents($this->getTemplateRealPath()));
			file_put_contents($this->getCompileRealPath(), $this->content);
		}

		if($this->needCache()) {
			if(!$this->needReCache()) {
				require_once $this->getCacheRealPath();
				exit();
			}
			ob_start();
			extract($this->data);
			require_once $this->getCompileRealPath();
			file_put_contents($this->getCacheRealPath(), ob_get_contents());
		} else {
			extract($this->data);
			require_once $this->getCompileRealPath();
		}
		exit();
	}

	public function __call($method, $parameter)
	{
		if(starts_with($method, 'with')) {
			$key = strtolower(substr($method, 4));
			$param = isset($parameter[0]) ? $parameter[0] : null;
			$this->with($key, $param);
			return $this;
		}
		throw new Exception("$method not exist", 1);
	}

	private function getTemplateRealPath()
	{
		return $this->config['template_dir'] . '/' . str_replace('.', '/', $this->template_file) . $this->config['template_suffix'];
	}

	private function getCacheRealPath()
	{
		return $this->config['cache_dir'] . '/' . $this->getCacheFileName() . $this->config['cache_suffix'];
	}

	private function getCompileRealPath()
	{
		return $this->config['compile_dir'] . '/' . $this->getCompileFileName() . $this->config['compile_suffix'];
	}

	private function getCacheFileName()
	{
		return md5($this->template_file);
	}

	private function getCompileFileName()
	{
		return $this->getCacheFileName();
	}

	private function needReCache()
	{
		if(!$this->config['need_cache']) {
			return false;
		}
		
		if(!is_file($this->getCacheRealPath())) {
			return true;
		}
		
		if($this->config['cache_time'] == 0) {
			return false;
		}

		if((time() - filemtime($this->getCacheRealPath())) > $this->config['cache_time']) {
			return true;
		}
		
		return false;
	}

	private function needReCompile()
	{
		if(!is_file($this->getCompileRealPath())) {
			return true;
		}
		
		if($this->config['compile_expired_time'] == 0) {
			return false;
		}
		$dealt_time = filemtime($this->getTemplateRealPath()) - filemtime($this->getCompileRealPath());

		if($dealt_time > $this->config['compile_expired_time']) {
			return true;
		}
		
		return false;
	}

	private function checkPath()
	{
		if(!is_file($this->getTemplateRealPath())) {
			throw new Exception("template file: " . $this->getTemplateRealPath() . ' not exists!');
		}

		if(!is_dir($this->config['compile_dir'])) {
			if(!mkdir($this->config['compile_dir'], 0777)) {
				throw new Exception("can not  mkdir : " . $this->config['compile_dir']);
			}
		}

		if(!is_dir($this->config['cache_dir'])) {
			if(!mkdir($this->config['cache_dir'], 0777)) {
				throw new Exception("can not  mkdir : " . $this->config['cache_dir']);
			}
		}
	}

	private function needCache()
	{
		return $this->config['need_cache'];
	}


}