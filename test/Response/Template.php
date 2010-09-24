<?php

require_once 'lib/autoload.php';

class Response_TemplateTest extends PHPUnit_Framework_TestCase {
	
	public function testDisplayShouldWriteToOutputBuffer() {
		$template = new Response_Template(__DIR__.'/template/foo.php');
		$vars = array('rand'=>mt_rand());
		ob_start();
		$template->display($vars);
		$output = ob_get_contents();
		ob_end_clean();
		
		$this->assertEquals("foo\n$vars[rand]", $output);
	}
}
