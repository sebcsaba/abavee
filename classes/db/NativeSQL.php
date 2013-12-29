<?php

/**
 * Represents a native SQL statement, where no further conversations are needed,
 * and can be executed by the engine immediately
 * 
 * @author sebcsaba
 */
class NativeSQL implements SQL {
	
	/**
	 * Contains the whole SQL statement
	 * 
	 * @var string
	 */
	private $statement;
	
	/**
	 * Data for the SQL statement
	 * 
	 * @var array (mixed)
	 */
	private $params;

	/**
	 * Creates a native SQL statement from the given string and given data
	 * 
	 * @param string $statement
	 * @param array (mixed) $params
	 */
	public function __construct($statement, array $params = array()) {
		$this->statement = $statement;
		$this->params = $params;
	}
	
	/**
	 * Returns the string representation of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null) {
		return $this->statement;
	}
	
	/**
	 * Returns the array of the parameters of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return array (mixed)
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return $this->params;
	}
	
}
