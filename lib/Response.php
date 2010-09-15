<?php

abstract class Response {
	
	public abstract function display(array $vars = array());
	
	public function render(array $vars = array()) {
		ob_start();
		$this->display($vars);
		$render = ob_get_contents();
		ob_end_clean();
		return $render;
	}
}
