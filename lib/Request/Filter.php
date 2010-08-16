<?php

/**
 * Filter stuff coming from a request array (i.e. $_GET or $_POST).
 * These validations are exclusively UTF-8.
 * TODO: alternate character encodings?
 */
class Request_Filter implements ArrayAccess, IteratorAggregate {
    
	private $raw;
	private $text = array();
	private $filter = array();
	
	public function __construct(array $array) {
		$this->raw = $array;
	}
	
	public function offsetExists($name) {
		return isset($this->raw[$name]);
	}
	
	public function offsetGet($name) {
		if (!isset($this->filter[$name])) {
			$raw = $this->raw[$name];
			if (is_array($raw)) {
				$filter = new Request_Filter($raw);
			} else {
				$filter = strval($raw);
				if ($this->isValidEncoding($filter)) {
					$filter = filter_var($filter, FILTER_SANITIZE_STRING);
				} else {
					$filter = null;
				}
			}
			$this->filter[$name] = $filter;
		}
		return $this->filter[$name];
	}
	
	public function offsetSet($name, $value) {
		$this->unset($name);
		$this->raw[$name] = $value;
	}
	
	public function offsetUnset($name) {
		unset($this->raw[$name]);
		unset($this->text[$name]);
		unset($this->text[$filter]);
	}
	
	public function getIterator() {
		return new Request_Iterator($this, array_keys($this->raw));
	}
	
	/**
	 * Returns a character encoding validated version of the text.
	 * Control codes should be eliminated?
	 */
	public function text($name) {
		if (!isset($this->text[$name])) {
			$text = $this->raw[$name];
			if (!$this->isValidEncoding($text)) {
				$text = false;
			}
			$this->text[$name] = $text;
		}
		return $this->text[$name];
	}
	
	private function isValidEncoding($text) {
		$encoding = 'UTF-8';
		return mb_check_encoding($text, $encoding);
	}
	
	public function binary($name, $default = null) {
		throw new Exception('Unimplemented');
	}
	
	public function int($name, $default = 0) {
		throw new Exception('Unimplemented');
	}
	
	public function float($name, $default = 0.0) {
		throw new Exception('Unimplemented');
	}
	
	public function strtotime($name, $default = false) {
		throw new Exception('Unimplemented');
	}
}
