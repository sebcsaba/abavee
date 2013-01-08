<?php

/**
 * Represents a Forward that redirects to another location
 * 
 * @author sebcsaba
 */
class RedirectForward implements Forward {
	
	/**
	 * The URL where we will redirect the browser
	 * 
	 * @var URL
	 */
	private $location;
	
	public function __construct($location) {
		$this->location = $location;
	}
	
	public function run(DI $di, Config $config, Request $request) {
		if ($request->isAjax()) {
			header('X-Location: '.$this->location);
		} else {
			header('Location: '.$this->location);
		}
		return null;
	}
	
}
