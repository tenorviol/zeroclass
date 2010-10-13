<?php

require_once 'lib/autoload.php';

class Controller_PathMethodTest extends PHPUnit_Framework_TestCase {
	
	public function requests() {
		return array(
			array('/foo/', 'getFoo foo|'),
			array('/foo/other/crap/here', 'getFoo foo|other|crap|here'),
			array('/FOO/bar/', 'getFoo_Bar FOO|bar|'),
			array('/FOO/bar.html', 'getFoo_Bar FOO|bar.html'),
			array('/foo.json', 'getFoo_Json foo.json'),
			array('/', 'get '),
			array('', 'get '),
			array('foo/bar/2112', 'getFoo_Bar foo|bar|2112'),
		);
	}
	
	/**
	 * @dataProvider requests
	 */
	public function test($request_uri, $expected) {
		$_SERVER['REQUEST_URI'] = $request_uri;
		
		$controller = new TestController();
		
		ob_start();
		$controller->control();
		$result = ob_get_contents();
		ob_end_clean();
		
		$this->assertEquals($expected, $result);
	}
}

class TestController extends Controller_PathMethod {
	
	const PREFIX = 'get';
	
	public function __construct() {
		
	}
	
	protected function get(array $parts) {
		echo __FUNCTION__.' '.implode('|', $parts);
	}
	
	protected function getFoo(array $parts) {
		echo __FUNCTION__.' '.implode('|', $parts);
	}
	
	protected function getFoo_Json(array $parts) {
		echo __FUNCTION__.' '.implode('|', $parts);
	}
	
	protected function getFoo_Bar(array $parts) {
		echo __FUNCTION__.' '.implode('|', $parts);
	}
}
