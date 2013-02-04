<?php

/**
 * Represents an edge case when special instantialization is required for some class.
 * 
 * @author sebcsaba
 */
interface DISpecialHandler {
	
	/**
	 * Creates a new instance for the given interface.
	 * 
	 * @param DI $di The currently used DI
	 * @param string $interfaceName The name of the interface what we need an implementation for
	 * @param string $className The name of the class we should instantiate
	 * @return object An instance of the given interface and class
	 */
	public function create(DI $di, $interfaceName, $className);
	
}
