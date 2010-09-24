<?php

require_once 'lib/autoload.php';

class Request_ServerTest extends PHPUnit_Framework_TestCase {
	
	public function pathProvider() {
		return array(
			array(new Request_Server(array('REQUEST_URI'=>'/')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo/bar/index.html')), '/foo/bar/index.html'),
			array(new Request_Server(array('REQUEST_URI'=>'/?caligula=true')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/#minotaur')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo/../index.html')), '/index.html'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo/../')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo/..')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/..')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo/.../')), '/foo/.../'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo/./index.html')), '/foo/index.html'),
			array(new Request_Server(array('REQUEST_URI'=>'/foo//../')), '/'),
			array(new Request_Server(array('REQUEST_URI'=>'/a/very/deep/directory/../../structure/index.html')), '/a/very/structure/index.html'),
			array(new Request_Server(array('REQUEST_URI'=>'/a/very/deep/directory/../../../structure/index.html')), '/a/structure/index.html'),
			array(new Request_Server(array()), null),
			array(new Request_Server(array('REQUEST_URI'=>"/not/utf8/\xff")), null),
		);
	}
	
	/**
	 * @dataProvider pathProvider
	 */
	public function testRequestPathShouldReturnNormalizedUriPath($uri, $expected) {
		$path = $uri->requestPath();
		$this->assertEquals($expected, $path);
	}
}
