<?php

class Auth_Facebook implements Auth {
	
	private $facebook;
	
	public function __construct(Facebook $facebook) {
		$this->facebook = $facebook;
	}
	
	public function userId() {
		return $this->facebook->user();
	}
}
