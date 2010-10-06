<?php
/**
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

class Request_Iterator implements Iterator {
	
	private $array;
	private $keys;
	private $count;
	private $marker;
	
	public function __construct($array, $keys) {
		$this->array = $array;
		$this->keys = $keys;
		$this->count = count($keys);
		$this->marker = 0;
	}
	
	public function current() {
		return $this->array[$this->key()];
	}
	
	public function key() {
		return $this->keys[$this->marker];
	}
	
	public function next() {
		$this->marker++;
	}
	
	public function rewind() {
		$this->marker = 0;
	}
	
	public function valid() {
		return $this->marker < $this->count;
	}
}
 