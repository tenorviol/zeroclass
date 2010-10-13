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

class Controller_PathMethod implements Controller {
	
	const PREFIX = 'call';
	private $path = null;
	
	public function setPath($path) {
		$this->path = $path;
	}
	
	public function control() {
		$path = $this->path === null ? $_SERVER['REQUEST_URI'] : $this->path;
		$this->executePathMethod($path);
	}
	
	protected function executePathMethod($path) {
		$q = strpos($this->path, '?');
		$path = $q === false ? $path : substr($path, 0, $q);
		$path = preg_replace('#^/+#', '', $path);
		$parts = preg_split('#/+#', $path);
		
		$words = array();
		foreach ($parts as $part) {
			if ($part == '') continue;
			if (!preg_match('/^[a-zA-Z0-9_.]*$/', $part)) break;
			$bits = explode('.', $part);
			$words = array_merge($words, $bits);
		}
		
		do {
			$method = static::PREFIX.implode($words);
			if (method_exists($this, $method)) {
				$this->$method($parts);
				return;
			}
			array_pop($words);
		} while (isset($words[0]));
		
		throw new NotFoundException();
	}
}
