<?php

class Response_Page extends Response {
	
	public $model;
	public $content;
	public $header;
	public $footer;
	
	public $title;
	
	public function display(array $vars = array()) {
		$vars['page'] = $this;
		
		$content = $this->content->render($vars);
		
		if (isset($this->header)) {
			$this->header->display($vars);
		}
		
		echo $content;
		
		if (isset($this->footer)) {
			$this->footer->display($vars);
		}
	}
}
