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
		require_once('pages/'.$this->page.'.tpl.php');
		return null;
	}
	
}
