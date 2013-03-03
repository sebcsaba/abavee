<?php

/**
 * Represents a Forward that shows a page
 * 
 * @author sebcsaba
 */
class PageForward implements Forward {
	
	/**
	 * The name of the page to display
	 * 
	 * @var string
	 */
	private $page;
	
	public function __construct($page) {
		$this->page = $page;
	}
	
	public function run(DI $di, Config $config, Request $request) {
		if ($config->get('language/use')) {
			$t = $di->get('TranslationHandler')->getTranslationFunction();
		} else {
			$t = function() { return 'No translations are supported. Set the "language/use" configuration value!'; };
		}
		require_once('pages/'.$this->page.'.tpl.php');
		return null;
	}
	
}
