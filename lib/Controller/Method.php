<?php

abstract class Controller_Method implements Controller {
	
	public $path = null;
	
	public function control() {
		$path = $this->path === null ? $_SERVER['REQUEST_URI'] : $this->path;
		$q = strpos($this->path, '?');
		$path = $q === false ? $path : substr($path, 0, $q);
		$path = preg_replace('#^/+#', '', $path);
		$parts = preg_split('#/+#', $path);
		$this->call($parts);
	}
	
	protected function call(array $parts) {
		$words = array();
		foreach ($parts as $part) {
			if ($part == '') continue;
			if (!preg_match('/^[a-zA-Z0-9_.]*$/', $part)) break;
			$bits = explode('.', $part);
			$words = array_merge($words, $bits);
		}
		while (true) {
			$method = 'get'.implode($words);
			if (method_exists($this, $method)) {
				$this->$method($parts);
				return;
			}
			array_pop($words);
		}
	}
	
	protected abstract function get(array $parts);
}
