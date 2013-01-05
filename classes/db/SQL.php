<?php

/**
 * Represents an SQL query or DML statement.
 * 
 * The convertToString() method is used to convert the
 * implementation instance of this interface to string,
 * to be able to execute on some database engine.
 * 
 * The question marks ('?') in this strings represents items from
 * the array returned by the convertToParamsArray() method. These
 * items will be the data of the statement. The first data item
 * will be assigned to the first question mark, and so on.
 * 
 * @author sebcsaba
 */
interface SQL {
	
	/**
	 * Returns the string representation of this SQL statement.
	 * 
	 * @return string
	 */
	public function convertToString();
	
	/**
	 * Returns the array of the parameters of this SQL statement.
	 * 
	 * @return array (mixed)
	 */
	public function convertToParamsArray();
	
}
