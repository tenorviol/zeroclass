<?php

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
 