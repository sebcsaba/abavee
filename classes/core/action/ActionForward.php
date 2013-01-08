<?php

/**
 * Represents a Forward that runs another action
 * 
 * @author sebcsaba
 */
class ActionForward implements Forward {
	
	/**
	 * The name of the next action class
	 * 
	 * @var string
	 */
	private $className;
	
	public function __construct($className) {
		$this->className = $className;
	}
	
	public function run(DI $di, Config $config, Request $request) {
		$action = $di->get($this->className);
		return $action->serve($request);
	}
	
}
