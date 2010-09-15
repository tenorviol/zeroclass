<?php

class Logger_File {
	
	private $path;
	
	public function __construct($path) {
		$this->path = $path;
	}
	
	public function log($message) {
		$date = date('Y-m-d H:i:s');
		file_put_contents($this->path, "$date\t$message\n", FILE_APPEND | LOCK_EX);
	}
}
