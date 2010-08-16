<?php

class Request_Server extends Request_Filter {
	
	private $request_path;
	
	public function __construct(array $array = null) {
		parent::__construct($array === null ? $_SERVER : $array);
	}
	
	/**
	 * Extracts the path part of the REQUEST_URI,
	 * resolves all /.. and /. directories,
	 * and filters invalid characters.
	 * 
	 * TODO: enforce ANSII character set
	 *
	 * @return string
	 */
	public function requestPath() {
		if ($this->request_path !== null) {
			return $this->request_path;
		}
		if (!isset($this['REQUEST_URI'])) {
			return null;
		}
		$uri = $this['REQUEST_URI'];
		$q = strpos($uri, '?');
		if ($q === false) {
			$f = strpos($uri, '#');
			$path = $f === false ? $uri : substr($uri, 0, $f);
		} else {
			$path =  substr($uri, 0, $q);
		}
		$path = self::normalizePath($path);
		$this->request_path = $path;
		return $this->request_path;
	}
	
	/**
	 * Removes multi-slashes, and current and parent directory references.
	 *
	 * TODO: strip invalid characters
	 *
	 * @param string $path 
	 * @return string
	 */
	public static function normalizePath($path) {
		$path = preg_replace('#//+#', '/', $path);
		$path = preg_replace('#/\\.(/|$)#', '/', $path);
		do {
			$path = preg_replace('#(/[^/]+)?/\\.\\.(/|$)#', '/', $path, 1, $count);
		} while ($count);
		return $path;
	}
}
