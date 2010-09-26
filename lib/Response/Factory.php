<?php

class Response_Factory {
	
	private $template_dir;
	private $suffix;
	private $index;
	
	public $default_header = null;
	public $default_footer = null;
	
	public function __construct($template_dir, $suffix = '.php', $index = '/index') {
		$this->template_dir = $template_dir;
		$this->suffix = $suffix;
		$this->index = $index;
	}
	
	public function realTemplatePath($path) {
		$realpath = $this->template_dir.$path;
		if (is_dir($realpath)) {
			$realpath = realpath($realpath);
			$realpath .= $this->index;
		}
		$realpath .= $this->suffix;
		if (!is_file($realpath)) {
			throw new InvalidArgumentException("No template file found at '$path'. realpath=$realpath");
		}
		return $realpath;
	}
	
	public function createTemplate($path) {
		$path = $this->realTemplatePath($path);
		return new Response_Template($path);
	}
	
	public function createPage($path) {
		$page = new Response_Page($path, $this);
		$page->header = $this->default_header;
		$page->footer = $this->default_footer;
		return $page;
	}
}
