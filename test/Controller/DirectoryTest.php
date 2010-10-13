<?php

require_once 'lib/autoload.php';

class Controller_DirectoryTest extends PHPUnit_Framework_TestCase {
	
	public function requests() {
		return array(
			array('/foo/', 'foo'),
			array('/FOO/bar/', 'FOO'),
			array('/bar', 'bar'),
			array('/barfoo', 'barfoo'),
			array('/subdir/alex', 'alex'),
			array('/subdir/subdir/Leila', 'Leila'),
			array('/', 'lastresort')
		);
	}
	
	/**
	 * @dataProvider requests
	 */
	public function test($request_uri, $expected) {
		$_SERVER['REQUEST_URI'] = $request_uri;
		
		$controller = new Controller_Directory(new DirContainer(), 'lastresort');
		
		ob_start();
		$controller->control();
		$result = ob_get_contents();
		ob_end_clean();
		
		$this->assertEquals($expected, $result);
	}
}

class DirContainer extends Container {
	public function getInstance($property) {
		if ($property == 'subdir') {
			$controller = new Controller_Directory($this);
		} else {
			$controller = new LambdaController(function() use ($property) {
				echo $property;
			});
		}
		return $controller;
	}
}

class LambdaController implements Controller {
	
	private $lambda;
	
	public function __construct($lambda) {
		$this->lambda = $lambda;
	}
	
	public function control() {
		call_user_func($this->lambda);
	}
}
