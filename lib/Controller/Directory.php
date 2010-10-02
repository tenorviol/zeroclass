<?php

abstract class Controller_Directory implements Controller {
	
	private $path;
	
	public function __construct($path = null) {
		$this->path = $path === null ? $_SERVER['REQUEST_URI'] : $path;
	}
	
	public function control() {
		preg_match('/^\\/*([^\\/?]*)(.*)$/', $this->path, $matches);
		$directory = $matches[1];
		$remainder = $matches[2];
		$this->direct($directory, $remainder);
	}
	
	protected abstract function direct($directory, $remainder);
}
