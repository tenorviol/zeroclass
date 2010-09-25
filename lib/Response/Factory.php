<?php

class Response_Factory {
	
	private $template_dir;
	private $suffix;
	
	public function __construct($template_dir, $suffix = '.php') {
		$this->template_dir = $template_dir;
		$this->suffix = $suffix;
	}
	
	public function realTemplatePath($path) {
		$realpath = $this->template_dir.$path;
		if (is_dir($realpath)) {
			$realpath = realpath($realpath);
			$realpath .= '/index';
		}
		$realpath .= $this->suffix;
		if (!is_file($realpath)) {
			throw new NotFoundException("template file '$path'");
		}
		return $realpath;
	}
	
	public function createTemplate($path) {
		$path = $this->realTemplatePath($path);
		return new Response_Template($path);
	}
	
	public function createPage($path) {
		$page = new Response_Page($path, $this);
		return $page;
	}
}
