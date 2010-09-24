<?php

require_once 'lib/autoload.php';

class RequestTest extends PHPUnit_Framework_TestCase {
	
	public function test() {
		$_COOKIE['foo'] = 'bar';
		$_ENV['foo'] = 'bar';
		$_GET['foo'] = 'bar';
		$_POST['foo'] = 'bar';
		$_REQUEST['foo'] = 'bar';
		$_SERVER['foo'] = 'bar';
		
		$this->assertEquals($_GET['foo'], Request::$COOKIE->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$ENV->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$GET->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$POST->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$REQUEST->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$SERVER->utf8('foo'));
	}
}
