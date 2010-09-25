<?php

require_once 'lib/autoload.php';

class Response_TemplateTest extends PHPUnit_Framework_TestCase {
	
	public function testDisplay_Should_WriteToOutputBuffer() {
		$template = new Response_Template('test/Response/template/simple/foo.php');
		
		ob_start();
		$template->display();
		$output = ob_get_contents();
		ob_end_clean();
		
		$this->assertEquals("foo\n", $output);
	}
}
