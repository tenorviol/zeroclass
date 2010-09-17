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
		return $this->get($name);
	}
	
	public function offsetSet($name, $value) {
		$this->unset($name);
		$this->raw[$name] = $value;
	}
	
	public function offsetUnset($name) {
		unset($this->raw[$name]);
		unset($this->text[$name]);
		unset($this->filter[$name]);
	}
	
	public function getIterator() {
		return new Request_Iterator($this, array_keys($this->raw));
	}
	
	public function get($name, $default = null) {
		if (!isset($this->filter[$name])) {
			$raw = $this->raw[$name];
			if (is_array($raw)) {
				$filter = new Request_Filter($raw);
			} else {
				$filter = strval($raw);
				if ($this->isValidEncoding($filter)) {
					$filter = $this->stripControlCodes(filter_var($filter, FILTER_SANITIZE_STRING));
				} else {
					return $default;
				}
			}
			$this->filter[$name] = $filter;
		}
		return $this->filter[$name];
	}
	
	/**
	 * Returns a character encoding validated version of the text.
	 * Control codes should be eliminated?
	 */
	public function text($name, $default = null) {
		if (!isset($this->text[$name])) {
			if (isset($this->raw[$name]) && $this->isValidEncoding($this->raw[$name])) {
				$this->text[$name] = $this->stripControlCodes($this->raw[$name]);
			} else {
				return $default;
			}
		}
		return $this->text[$name];
	}
	
	private function isValidEncoding($text) {
		$encoding = 'UTF-8';
		return mb_check_encoding($text, $encoding);
	}
	
	private function stripControlCodes($text) {
		$needles = array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
		                 "\x08", /* \t */ /* \n */ "\x0b", "\x0c", /* \r */ "\x0e", "\x0f",
		                 "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17",
		                 "\x18", "\x19", "\x1a", "\x1b", "\x1c", "\x1d", "\x1e", "\x1f");
		return str_replace($needles, '', $text);
	}
	
	public function boolean($name, $default = false) {
		$boolean = filter_var(@$this->raw[$name], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if ($boolean === null) {
			return $default;
		} else {
			return $boolean;
		}
	}
	
	public function binary($name, $default = null) {
		if (isset($this->raw[$name])) {
			return $this->raw[$name];
		} else {
			return $default;
		}
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
