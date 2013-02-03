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
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null);
	
	/**
	 * Returns the array of the parameters of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return array (mixed)
	 */
	public function convertToParamsArray(DbDialect $dialect = null);
	
}
