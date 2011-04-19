<?php 

class TemplateException extends Exception { }

class Template
{
	protected $file_name;
	
	function __construct($file_name) {
		$this->file_name = TEMPLATE_DIR . $file_name;
		
		if(!file_exists($this->file_name))
			throw new TemplateException("Template file " . $this->file_name . " not found");
		
	}

	public function process($tpl_vars){
		ob_start();
		include($this->file_name);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

}
?>