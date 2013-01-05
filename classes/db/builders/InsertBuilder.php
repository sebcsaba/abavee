<?php

/**
 * Class for building SQL INSERT statements
 * 
 * @author sebcsaba
 */
class InsertBuilder implements SQL {
	
	/**
	 * The table to insert into
	 *
	 * @var string
	 */
	private $table;
	
	/**
	 * The fields to set
	 * 
	 * @var array (string)
	 */
	private $setFields = array();
	
	/**
	 * The value expressions for each field
	 *
	 * @var array (string)
	 */
	private $valueSql = array();
	
	/**
	 * Data for the value expressions
	 *
	 * @var array (mixed)
	 */
	private $valueData = array();
	
	/**
	 * Creates a new builder instance
	 * 
	 * @return InsertBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Sets the table to insert into
	 * 
	 * @param string $table
	 * @return InsertBuilder
	 */
	public function into($table) {
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Set a field to the given value
	 *
	 * @param string $where The field to set
	 * @param mixed $data The value to set
	 * @return InsertBuilder $this
	 */
	public function set($field, $value) {
		$this->setFields []= $field;
		$this->valueSql []= '?';
		$this->valueData []= $value;
		return $this;
	}

	/**
	 * Set a field to the given expression
	 *
	 * @param string $where The field to set
	 * @param string $value The native SQL expression to set
	 * @return InsertBuilder $this
	 */
	public function setSQL($field, $value) {
		$this->setFields []= $field;
		$this->valueSql []= $value;
		return $this;
	}
	
	/**
	 * Returns the string representation of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null) {
		$sql = 'INSERT INTO ' . $this->table;
		$sql .= ' ( ' . implode(', ', $this->setFields) . ' ) ';
		$sql .= ' VALUES ( ' . implode(', ',$this->valueSql) . ' ) ';
		return $sql;
	}
	
	/**
	 * Returns the array of the parameters of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return array (mixed)
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return $this->valueData;
	}
	
}
