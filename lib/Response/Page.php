<?php

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
	 * @param Response_Factory $factory 
	 */
	public function __construct($template, Response_Factory $factory = null) {
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
