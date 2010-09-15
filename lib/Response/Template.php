<?php

class Response_Template extends Response {
	
	private $path;
	private $vars;
	
	public function __construct($path) {
		$this->path = $path;
		$this->vars = array();
	}
	
	public function __get($name) {
		return $this->vars[$name];
	}
	
	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}
	
	public function __isset($name) {
		return isset($this->vars[$name]);
	}
	
	public function __unset($name) {
		unset($this->vars[$name]);
	}
	
	public function display(array $vars = array()) {
		extract($this->vars);
		extract($vars);
		include $this->path;
	}
}
