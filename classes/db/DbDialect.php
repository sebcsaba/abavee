<?php

/**
 * Specifies the exact SQL syntax for a given database driver
 * 
 * @author sebcsaba
 */
interface DbDialect {
	
	/**
	 * Returns the string that begins a transaction
	 *
	 * @return string
	 */
	public function getSqlStartTransaction();
	
	/**
	 * Returns the string that commits a transaction
	 *
	 * @return string
	 */
	public function getSqlCommitTransaction();
	
	/**
	 * Returns the string that rollbascks a transaction
	 *
	 * @return string
	 */
	public function getSqlRollbackTransaction();
	
	/**
	 * Returns the strings that runs at the initialization of a connection
	 *
	 * @return array (string)
	 */
	public function getConnectionInitializerQueries();
	
	/**
	 * Returns the SQL clause string for the given limit and offset values.
	 *
	 * @param integer or null $limit
	 * @param integer or null $offset
	 * @return string
	 */
	public function getLimitClause($limit,$offset);
	
	/**
	 * Prepares the SQL statement to the engine. The parameter can contain subqueries
	 * that should be flattened. Also, if the engine not supports parameters, the parameter
	 * values also should be included (and escaped!) to the query string.
	 *
	 * @param SQL $query General SQL statement
	 * @return NativeSQL Prepared native SQL statement for the corresponding engine
	 */
	public function prepareQuery(SQL $query);
	
	/**
	 * Prepares the original data that arrived from the database to the general php type.
	 *  
	 * @param string $type The requested primitive type, on of: boolean, integer, float, string, datetime
	 * @param mixed $value The original value from the database
	 * @return mixed The converted value (bool, int, float, string, or DateTime)
	 * @throws DbException If the value cannot be converted to the given type
	 * @throws DbException If the type is unknown
	 */
	public function convertPrimitive($type, $value);
	
}
