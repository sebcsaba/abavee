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
	
	/**
	 * The name of the javascript function that should be called after the page is displayed.
	 * (This should be handled by additional javascript)
	 * 
	 * @var string
	 */
	private $callJavascript;
	
	public function __construct($page, $callJavascript = null) {
		$this->page = $page;
		$this->callJavascript = $callJavascript;
	}
	
	public function run(DI $di, Config $config, Request $request) {
		if ($config->get('language/enabled', false)) {
			$t = $di->get('TranslationHandler')->getTranslationFunction();
		} else {
			$t = function() { return 'No translations are supported. Set the "language/enabled" configuration value!'; };
		}
		if (!is_null($this->callJavascript)) {
			$httpUtils = $di->get('HttpUtils');
			header('X-Call-Javascript: '.$httpUtils->encodeHeader($this->callJavascript));
		}
		require_once('pages/'.$this->page.'.tpl.php');
		return null;
	}
	
}
