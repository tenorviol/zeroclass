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

class Response_Page extends Response {
	
	private $factory;
	private $content;
	public $header;
	public $footer;
	
	/**
	 * Create a new response page, featuring a content template.
	 * Specifying a template by its path requires the optional factory.
	 *
	 * Header and footer templates can be added by setting
	 * those properties directly, both of which also take
	 * either string paths or Response_Templates.
	 *
	 * @param string|Response_Template $template 
	 * @param Response_Directory $factory 
	 */
	public function __construct($template, Response_Directory $factory = null) {
		$this->factory = $factory;
		$this->content = $this->templatize($template);
	}
	
	/**
	 * Write all templates (header, content and footer) to the output buffer.
	 *
	 * In addition to the associated values in vars, this object will be made
	 * available to all three templates via the 'page' vars element.
	 *
	 * The page template is rendered first to afford it the opportunity
	 * to make adjustments to this page object prior to rendering either the
	 * header or footer. Css or javascript can be added to the header and
	 * footer respectively via this method, or even the wrapping templates
	 * might be swapped out for replacement.
	 *
	 * @param array $vars 
	 */
	public function display(array $vars = array()) {
		if (isset($vars['page'])) {
			trigger_error('Overwriting $vars[page] with a reference to this object, suggest renaming', E_USER_NOTICE);
		}
		$vars['page'] = $this;
		
		$content = $this->content->render($vars);
		
		if (isset($this->header)) {
			$header = $this->templatize($this->header);
			$header->display($vars);
		}
		
		echo $content;
		
		if (isset($this->footer)) {
			$footer = $this->templatize($this->footer);
			$footer->display($vars);
		}
	}
	
	/**
	 * The template variables can be either strings or Response_Templates.
	 * This makes sure each has a Response_Template for rendering.
	 *
	 * @param string|Response_Template $template 
	 * @return Response_Template
	 * @throws UnexpectedValueException
	 */
	private function templatize($template) {
		if ($template instanceof Response_Template) {
			return $template;
		} elseif ($this->factory) {
			return $this->factory->createTemplate($template);
		} else {
			throw new UnexpectedValueException("Factory required for lazy templating. template=$template");
		}
	}
}
