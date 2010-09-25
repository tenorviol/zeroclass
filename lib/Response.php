<?php

abstract class Response {
	
	public abstract function display(array $vars = array());
	
	public function render(array $vars = array()) {
		ob_start();
		try {
			$this->display($vars);
		} catch (Exception $e) {
			ob_end_clean();
			throw $e;
		}
		$render = ob_get_contents();
		ob_end_clean();
		return $render;
	}
}
