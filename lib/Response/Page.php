<?php

class Response_Page extends Response {
	
	private $factory;
	private $page;
	public $header;
	public $footer;
	
	/**
	 * Create a new response page, featuring a center template.
	 * Header and footer templates can be added by setting
	 * those properties directly, both of which also take
	 * either string paths or Response_Templates;
	 *
	 * @param string|Response_Template $template 
	 * @param Response_Factory $factory 
	 */
	public function __construct($template, Response_Factory $factory = null) {
		$this->factory = $factory;
		$this->page = $this->templatize($template);
	}
	
	public function display(array $vars = array()) {
		$vars['page'] = $this;
		
		$content = $this->page->render($vars);
		
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
