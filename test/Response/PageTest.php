<?php

require_once 'lib/autoload.php';

class Response_PageTest extends PHPUnit_Framework_TestCase {
	
	public function testRenderingWithManualTemplating() {
		$dir = 'test/response/template/simple';
		$content = new Response_Template("$dir/foo.php");
		$header = new Response_Template("$dir/header.php");
		$footer = new Response_Template("$dir/footer.php");
		
		$page = new Response_Page($content);
		$page->header = $header;
		$page->footer = $footer;
		
		$result = $page->render();
		
		$this->assertEquals("HEADER\nfoo\nFOOTER\n", $result);
	}
	
	public function testRenderingWithLazyFactoryTemplating() {
		$factory = new Response_Factory('test/Response/template/simple');
		
		$page = $factory->createPage('/foo');
		$page->header = '/header';
		$page->footer = '/footer';
		
		$result = $page->render();
		
		$this->assertEquals("HEADER\nfoo\nFOOTER\n", $result);
	}
	
	public function testRenderingInsestuousTemplates() {
		$factory = new Response_Factory('test/Response/template/insestuous');
		$factory->default_header = '/header';
		$factory->default_footer = '/footer';
		
		$page = $factory->createPage('/page');
		
		$result = $page->render();
		
		$this->assertEquals("MyPage\nMyPage content...\nCopyright &copy; 2010\n", $result);
	}
	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testContentPathWithoutFactory_Throws_UnexpectedValueException() {
		$page = new Response_Page('test/response/template/simple/foo');
	}
	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testHeaderPathWithoutFactory_Throws_UnexpectedValueException() {
		$dir = 'test/response/template/simple';
		$content = new Response_Template("$dir/foo.php");
		$page = new Response_Page($content);
		$page->header = "$dir/header.php";
		$page->render();
	}
	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testFooterPathWithoutFactory_Throws_UnexpectedValueException() {
		$dir = 'test/response/template/simple';
		$content = new Response_Template("$dir/foo.php");
		$page = new Response_Page($content);
		$page->footer = "$dir/footer.php";
		$page->render();
	}
}
