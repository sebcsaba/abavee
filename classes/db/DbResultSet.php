<?php

/**
 * Database resultset as an interable object. In all iteration step it
 * must return the next row of the resultset, as and associative array.
 * 
 * @author sebcsaba
 */
interface DbResultSet extends Iterator {
	
	/**
	 * Closes the object, ans releases the used resource
	 */
	public function close();
	
}
