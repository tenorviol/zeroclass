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
			array('/', '')
		);
	}
	
	/**
	 * @dataProvider requests
	 */
	public function test($request_uri, $expected) {
		$_SERVER['REQUEST_URI'] = $request_uri;
		
		$controller = new TestDir();
		
		ob_start();
		$controller->control();
		$result = ob_get_contents();
		ob_end_clean();
		
		$this->assertEquals($expected, $result);
	}
}

class TestDir extends Controller_Directory {
	protected function direct($directory, $remainder) {
		if ($directory == 'subdir') {
			$controller = new TestDir($remainder);
		} else {
			$controller = new LambdaController(function() use ($directory) {
				echo $directory;
			});
		}
		$controller->control();
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
