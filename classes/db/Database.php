<?php

/**
 * A database handler. Independent from the underlying engine.
 * 
 * @author sebcsaba
 */
class Database {
	
	/**
	 * The used database engine
	 *
	 * @var DbEngine
	 */
	protected $engine;
	
	/**
	 * Creates a database handler
	 *
	 * @param DbEngine $engine The used database engine
	 */
	public function __construct(DbEngine $engine) {
		$this->engine = $engine;
	}
	
	/**
	 * Closes the database handler, and the underlying connection
	 */
	public function close() {
		$this->engine->close();
	}
	
	/**
	 * Opens a new transaction. If a transaction is already opened, then only a
	 * counter is incremented to maintain the number of the 'embedded' transactions.
	 * 
	 * @throws DbException
	 */
	public function startTransaction() {
		$this->engine->startTransaction();
	}
	
	/**
	 * Closes the last transaction. This results a real COMMIT only if the outermost transaction is closed.
	 * Otherwise only the transaction counter is decremented
	 * 
	 * @throws DbException
	 */
	public function commit() {
		$this->engine->commit();
	}
	
	/**
	 * Rollback all the current transactions. The transaction counter will be reseted.
	 * 
	 * @throws DbException
	 */
	public function rollback() {
		$this->engine->rollback();
	}
	
	/**
	 * Prepares the given string to insert to SQL statement.
	 * Escapes the apostrophe and other relevant characters.
	 * Use this for all strings arrived from untrusted source to prevent SQL injection!
	 *
	 * @param string $string The original string
	 * @return string The result that can inserted to an SQL statement
	 */
	public function escape($string) {
		return $this->engine->escape($string);
	}
	
	/**
	 * Runs the given SQL statement
	 *
	 * @param SQL $statement The statement to execute
	 * @return integer The number of the affected rows
	 * @throws DbException
	 */
	public function exec(SQL $statement) {
		$preparedQuery = $this->engine->getDialect()->prepareQuery($statement);
		return $this->engine->execNative($preparedQuery);
	}
	
	/**
	 * Runs the given SQL insert statement
	 *
	 * @param SQL $statement The statement to execute
	 * @return integer Id of the inserted item
	 * @throws DbException
	 */
	public function insert(SQL $statement) {
		$preparedQuery = $this->engine->getDialect()->prepareQuery($statement);
		return $this->engine->insertNative($preparedQuery);
	}
	
	/**
	 * Runs the given SQL query statement, and tests the result.
	 * The result of this function is engine-dependent.
	 *
	 * @param SQL $query The query statement
	 * @return mixed Reference to the resultset: the type depends on the engine, don't use it directly!
	 * @throws DbException
	 */
	private function queryNative(SQL $query) {
		$preparedQuery = $this->engine->getDialect()->prepareQuery($query);
		$result = $this->engine->queryNative($preparedQuery);
		return $this->engine->testResult($result,$preparedQuery);
	}
	
	/**
	 * Runs the SQL query statement
	 *
	 * @param SQL $query The query statement
	 * @param boolean $autoClose If finished over the iteration, close the resultset automatically
	 * @return DbResultSet The resultset, can be used in foreach(...)
	 * @throws DbException
	 */
	public function query(SQL $query, $autoClose=true) {
		$result = $this->queryNative($query);
		return $this->engine->getResultSet($result, $autoClose);
	}
	
	/**
	 * Returns the first row of the result of the query, as an associatie array
	 *
	 * @param SQL $query The query statement
	 * @param boolean $nullOnEmpty If true, return null on empty result. If false, throw exception in this case.
	 * @return array (key=>mixed) or null The first row of the resultset.
	 * @throws DbException If $nullOnEmpty is false and the query resulted an empty resultset
	 * @throws DbException If an error occured during the query
	 */
	public function queryRow(SQL $query, $nullOnEmpty=false) {
		$result = $this->queryNative($query);
		$row = $this->engine->fetchFirstRowOnly($result);
		if (!is_null($row)) {
			return $row;
		} else if ($nullOnEmpty) {
			return null;
		} else {
			throw new DbException('the query return empty resultset', $query);
		}
	}
	
	/**
	 * Returns one cell from the first row of the result of the query
	 *
	 * @param SQL $query The query statement
	 * @param string $fieldName The name of the field to return. If null, the first field will be returned.
	 * @param boolean $nullOnEmpty If true, return null on empty result. If false, throw exception in this case.
	 * @return mixed or null The selected cell from first row of the resultset.
	 * @throws DbException If $nullOnEmpty is false and the query resulted an empty resultset
	 * @throws DbException If an error occured during the query
	 */
	public function queryCell(SQL $query, $fieldName=null, $nullOnEmpty=false) {
		$row = $this->queryRow($query, $nullOnEmpty);
		if ($row===null) {
			return null;
		}
		return $this->getRowField($row,$fieldName);
	}
	
	/**
	 * Returns the selected column of the result of the query
	 *
	 * @param SQL $query The query statement
	 * @param string $fieldName The name of the field to return. If null, the first field will be returned.
	 * @return array (idx=>mixed) Contains the cells of the selected field from each row of the resultset
	 * @throws DbException
	 */
	public function queryColumn(SQL $query, $fieldName = null) {
		$result = array();
		foreach ($this->query($query) as $row) {
			$result []= $this->getRowField($row,$fieldName);
		}
		return $result;
	}
	
	/**
	 * Returns the resultset as an associative array. For each row, the value of the
	 * keyField will be used as a key, and the value of the valueField will be used
	 * as the corresponding value. If more rows have the same value in the key field,
	 * the last will be available in the result.
	 *
	 * @param SQL $query The query statement
	 * @param string $keyFieldName The name of the field that will be the key for each item. If null, the first field will be used.
	 * @param string $valueFieldName The name of the field that will be the value for each item. If null, the second field will be used.
	 * @return array (key=>mixed) The result az an associative array
	 * @throws DbException
	 */
	public function queryMapping(SQL $query, $keyFieldName = null, $valueFieldName = null) {
		$result = array();
		foreach ($this->query($query) as $row) {
			$key = $this->getRowField($row,$keyFieldName);
			$value = $this->getRowField($row,$valueFieldName);
			$result[$key] = $value;
		}
		return $result;
	}
	
	/**
	 * Returns the resultset of the query as a table: a number-indexed array,
	 * one item for each row, and each item is an associative array. 
	 *
	 * @param SQL $query The query statement
	 * @return array (idx=>array(key=>mixed)) The result array
	 * @throws DbException Ha nem sikerült a lekérdezést végrehajtani.
	 */
	public function queryAssocTable(SQL $query) {
		$result = array();
		foreach ($this->query($query) as $row) {
			$result []= $row;
		}
		return $result;
	}
	
	/**
	 * Returns the given field from the row.
	 * If no fieldName specified, return the first item, and remove it - the original array will be modified!
	 *
	 * @param array (key=>mixed) $row The row from the resultset
	 * @param string or null $fieldName The name of the field
	 * @return mixed The value of the selected field in the row, or the first item if no field is specified
	 */
	private function getRowField(array &$row, $fieldName = null) {
		if (is_null($fieldName)) {
			return array_shift($row);
		} else {
			return $row[$fieldName];
		}
	}
	
}
