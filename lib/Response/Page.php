<?php

class Response_Page extends Response {
	
	private $factory;
	private $page;
	public $header;
	public $footer;
	
	public function __construct($template, Response_Factory $factory = null) {
		$this->factory = $factory;
		$this->page = $this->getTemplate($template);
	}
	
	public function display(array $vars = array()) {
		$vars['page'] = $this;
		
		$content = $this->page->render($vars);
		
		if (isset($this->header)) {
			$header = $this->getTemplate($this->header);
			$header->display($vars);
		}
		
		echo $content;
		
		if (isset($this->footer)) {
			$footer = $this->getTemplate($this->footer);
			$footer->display($vars);
		}
	}
	
	private function getTemplate($template) {
		if ($template instanceof Response_Template) {
			return $template;
		} elseif ($this->factory) {
			return $this->factory->createTemplate($template);
		} else {
			throw new UnexpectedValueException("Factory required for lazy templating. template=$template");
		}
	}
}
