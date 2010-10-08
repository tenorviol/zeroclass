<?php
/**
 * Zeroclass library
 * http://github.com/tenorviol/zeroclass
 *
 * Copyright (c) 2010 Christopher Johnson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. 
 */

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
