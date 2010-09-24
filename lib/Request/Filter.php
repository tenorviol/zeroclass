<?php

/**
 * Filter data coming from a request array (i.e. $_GET or $_POST).
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
	
	/*** ArrayAccess interface ***/
	
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
		unset($this->utf8[$name]);
		unset($this->text[$name]);
		unset($this->strip[$name]);
	}
	
	
	/*** IteratorAggregate interface ***/
	
	public function getIterator() {
		return new Request_Iterator($this, array_keys($this->raw));
	}
	
	
	/**
	 * Return the array offset, stripped of all tags and
	 * exotic control characters, and verified as valid utf8.
	 * If the array offset does not exist or its value does
	 * not pass muster, the default value is returned.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return string|mixed
	 */
	public function get($name, $default = null) {
		if (!isset($this->strip[$name])) {
			if (is_array($this->raw[$name])) {
				$this->strip[$name] = new Request_Filter($this->raw[$name]);
			} else {
				$text = $this->text($name);
				$this->strip[$name] = $text === null ? false : filter_var($text, FILTER_SANITIZE_STRING);
			}
		}
		if ($this->strip[$name] !== false) {
			return $this->strip[$name];
		} else {
			return $default;
		}
	}
	
	/**
	 * Return the array offset, stripped of exotic control
	 * characters and verified as utf8.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return string|mixed
	 */
	public function text($name, $default = null) {
		if (!isset($this->text[$name])) {
			$utf8 = $this->utf8($name, false);
			$this->text[$name] = $this->stripControlCodes($utf8);
		}
		if ($this->text[$name] !== false) {
			return $this->text[$name];
		} else {
			return $default;
		}
	}
	
	/**
	 * Remove all control codes, with the exceptions of tab,
	 * newline and carriage return.
	 *
	 * @param string|null $text 
	 * @return string|null
	 */
	private function stripControlCodes($text) {
		if (!is_string($text)) {
			return $text;
		}
		$needles = array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
		                 "\x08", /* \t */ /* \n */ "\x0b", "\x0c", /* \r */ "\x0e", "\x0f",
		                 "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17",
		                 "\x18", "\x19", "\x1a", "\x1b", "\x1c", "\x1d", "\x1e", "\x1f");
		return str_replace($needles, '', $text);
	}
	
	/**
	 * Return the array offset if it contains valid utf8.
	 * Otherwise, return the default.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return string|mixed
	 */
	public function utf8($name, $default = null) {
		if (!isset($this->utf8[$name])) {
			$this->utf8[$name] = isset($this->raw[$name]) && mb_check_encoding($this->raw[$name], 'UTF-8');
		}
		return $this->utf8[$name] ? $this->raw[$name] : $default;
	}
	
	/**
	 * Return the array offset as is, if it exists.
	 * Otherwise, return the default.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return string|mixed
	 */
	public function binary($name, $default = null) {
		return isset($this->raw[$name]) ? $this->raw[$name] : $default;
	}
	
	/**
	 * Return a boolean based on the value at the offset.
	 * Otherwise, return default.
	 * Valid values for true: '1', 'true', 'on', 'yes'.
	 * Valid values for false: '0', 'false', 'off', 'no', ''.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return boolean|mixed
	 */
	public function boolean($name, $default = false) {
		$boolean = filter_var(@$this->raw[$name], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		return $boolean === null ? $default : $boolean;
	}
	
	/**
	 * Return an int based on the value at the offset.
	 * Otherwise, return default.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return int|mixed
	 */
	public function int($name, $default = 0) {
		$int = filter_var(@$this->raw[$name], FILTER_VALIDATE_INT);
		return $int === false ? $default : $int;
	}
	
	/**
	 * Return a float based on the value at the offset.
	 * Otherwise, return default.
	 *
	 * @param string $name 
	 * @param mixed $default 
	 * @return float|mixed
	 */
	public function float($name, $default = 0.0) {
		$float = filter_var(@$this->raw[$name], FILTER_VALIDATE_FLOAT);
		return $float === false ? $default : $float;
	}
	
	/**
	 * Apply a callable function to the value at offset.
	 * If the offset does not exist or the callable returns
	 * either null or false, return the default.
	 *
	 * @param Callback $callable 
	 * @param string $name 
	 * @param mixed $default 
	 * @return mixed
	 */
	public function apply($callable, $name, $default = null) {
		$utf8 = $this->utf8($name);
		$result = null;
		if ($utf8 !== null) {
			$result = call_user_func($callable, $this->raw[$name]);
		}
		if ($result === null || $result === false) {
			return $default;
		} else {
			return $result;
		}
	}
}
