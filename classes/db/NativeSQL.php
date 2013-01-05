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
	 * @return string
	 */
	public function convertToString() {
		return $this->statement;
	}
	
	/**
	 * Returns the array of the parameters of this SQL statement.
	 * 
	 * @return array (mixed)
	 */
	public function convertToParamsArray() {
		return $this->params;
	}
	
}
