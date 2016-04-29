<?php
namespace Core\View;

class Compile
{
	private  $config;
	private $content;

	private $T_L = array();
	private $T_R = array();

	private $template_file;

	private function initTemplate()
	{
		if($this->config['php_turn'] == false) {
			$this->T_L[] = "#<\?(=|php|)(.+?)\?>#is";
			$this->T_R[] = "&lt;?\\1 \\2 ?&gt";
		}

		$this->T_L[] = "#\{\\$([a-zA-Z_\x7f-\xff].*)\}#";
		$this->T_R[] = "<?php echo \$\\1; ?>";

		$this->T_L[] = "#@foreach\\s+\(\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\\s+[aA][sS]\\s+\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\\s*(=>\\s+\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*\)#";
		$this->T_R[] = "<?php foreach (\$\\1 as \$\\2 \\3) {?>";

		$this->T_L[] = "#@endforeach#";
		$this->T_R[] = "<?php }?>";

		$this->T_L[] = "#\{\!([a-zA-Z_\x7f-\xff/][a-zA-Z0-9_\x7f-\xff/]*\.js)\}#";
		$this->T_R[] = "<script src = '\\1' type=\"text/javascript\"> </script>";
	}

	public function compile($content)
	{
		$this->initTemplate();
		return preg_replace($this->T_L, $this->T_R, $content);
	}
}