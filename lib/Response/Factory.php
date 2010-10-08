<?php
/**
 * Zeroclass library
 * http://github.com/tenorviol/zeroclass
 *
 * Copyright (c) 2010 Christopher Johnson
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. 
 */

class Response_Factory {
	
	private $template_dir;
	private $suffix;
	private $index;
	
	public $default_header = null;
	public $default_footer = null;
	
	public function __construct($template_dir, $suffix = '.php', $index = '/index') {
		$this->template_dir = $template_dir;
		$this->suffix = $suffix;
		$this->index = $index;
	}
	
	public function realTemplatePath($path) {
		$realpath = $this->template_dir.$path;
		if (is_dir($realpath)) {
			$realpath = realpath($realpath);
			$realpath .= $this->index;
		}
		$realpath .= $this->suffix;
		if (!is_file($realpath)) {
			throw new InvalidArgumentException("No template file found at '$path'. realpath=$realpath");
		}
		return $realpath;
	}
	
	public function createTemplate($path) {
		$path = $this->realTemplatePath($path);
		return new Response_Template($path);
	}
	
	public function createPage($path) {
		$page = new Response_Page($path, $this);
		$page->header = $this->default_header;
		$page->footer = $this->default_footer;
		return $page;
	}
	
	public function displayPage($path, $vars = array()) {
		$page = $this->createPage($path);
		$page->display($vars);
	}
}
