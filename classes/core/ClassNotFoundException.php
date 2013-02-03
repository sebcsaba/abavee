<?php

/**
 * This exception occurs when the some specified class couldn't have been loaded
 * 
 * @author sebcsaba
 */
class ClassNotFoundException extends Exception {
	
	/**
	 * The name of the class what couldn't have been loaded
	 * 
	 * @var string
	 */
	private $className;
	
	/**
	 * Createa a exception that means that the given class couldn't have been loaded
	 * 
	 * @param string $className
	 */
	public function __construct($className){
		parent::__construct(sprintf('Unable to load class: %s',$className));
		$this->className = $className;
	}
	
	/**
	 * Returns the name of the class what couldn't have been loaded
	 * 
	 * @return string
	 */
	public function getClassName(){
		return $this->className;
	}
	
}
