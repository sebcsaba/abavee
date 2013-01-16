<?php

/**
 * Represent a user of the application
 * 
 * @author sebcsaba
 */
interface User {
	
	/**
	 * Returns the id of the user
	 * 
	 * @return int
	 */
	public function getId();
	
	/**
	 * Returns the name of the user
	 * 
	 * @return string
	 */
	public function getName();
	
}
