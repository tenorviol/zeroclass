<?php

/**
 * Filter stuff coming from a request array (i.e. $_GET or $_POST).
 * These validations are exclusively UTF-8.
 * TODO: alternate character encodings?
 */
class Request_Filter implements ArrayAccess, IteratorAggregate {
    
	private $raw;
	private $utf8 = array();
	private $text = array();
	private $strip = array();
	
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
		if (!isset($this->strip[$name])) {
			if (is_array($this->raw[$name])) {
				$this->strip[$name] = new Request_Filter($this->raw[$name]);
			} else {
				$text = $this->text($name);
				$this->strip[$name] = $text === null ? null : filter_var($text, FILTER_SANITIZE_STRING);
			}
		}
		if ($this->strip[$name] !== null) {
			return $this->strip[$name];
		} else {
			return $default;
		}
	}
	
	/**
	 * Returns a character encoding validated version of the text.
	 * Control codes should be eliminated?
	 */
	public function text($name, $default = null) {
		if (!isset($this->text[$name])) {
			$utf8 = $this->utf8($name);
			$this->text[$name] = $this->stripControlCodes($utf8);
		}
		if ($this->text[$name] !== null) {
			return $this->text[$name];
		} else {
			return $default;
		}
	}
	
	private function stripControlCodes($text) {
		if ($text === null) {
			return null;
		}
		$needles = array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
		                 "\x08", /* \t */ /* \n */ "\x0b", "\x0c", /* \r */ "\x0e", "\x0f",
		                 "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17",
		                 "\x18", "\x19", "\x1a", "\x1b", "\x1c", "\x1d", "\x1e", "\x1f");
		return str_replace($needles, '', $text);
	}
	
	public function utf8($name, $default = null) {
		if (!isset($this->utf8[$name])) {
			$this->utf8[$name] = isset($this->raw[$name]) && mb_check_encoding($this->raw[$name], 'UTF-8');
		}
		if ($this->utf8[$name]) {
			return $this->raw[$name];
		} else {
			return $default;
		}
	}
	
	public function binary($name, $default = null) {
		if (isset($this->raw[$name])) {
			return $this->raw[$name];
		} else {
			return $default;
		}
	}
	
	public function boolean($name, $default = false) {
		$boolean = filter_var(@$this->raw[$name], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if ($boolean === null) {
			return $default;
		} else {
			return $boolean;
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
