<?php

class Response_Json extends Response {
	
	public function display(array $vars = array()) {
		echo json_encode($vars);
	}
}
