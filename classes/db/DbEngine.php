<?php

/**
 * Database engine wrapper class. The subclasses of this class
 * will implement the real database connection functionality.
 * 
 * @author sebcsaba
 */
abstract class DbEngine {
	
	/**
	 * The dialect used for the database connection
	 *
	 * @var DbDialect
	 */
	private $dialect;
	
	/**
	 * The number of the opened (embedded) transactions
	 *
	 * @var integer
	 */
	private $transactions = 0;
	
	/**
	 * Create a database connection. This will immediately connect to the database
	 *
	 * @param DbConnectionParameters $params The parameters of the connection
	 * @param DbDialect $dialect The dialect used for the database connection
	 * @throws DbException
	 */
	public function __construct(DbConnectionParameters $params, DbDialect $dialect) {
		$this->dialect = $dialect;
		$supported = $this->getSupportedProtocols();
		if (!in_array($params->getProtocol(),$supported)) {
			throw new DbException('Cannot connect because protocol "'.$params->getProtocol().'" is not supported by this engine.');
		}
		$this->connect($params);
		foreach ($this->dialect->getConnectionInitializerQueries() as $initializer) {
			$this->execPrimitive($initializer);
		}
	}
	
	/**
	 * Retutns the list of the supported protocol names
	 *
	 * @return array (string)
	 */
	protected abstract function getSupportedProtocols();
	
	/**
	 * Connects to the database
	 *
	 * @param DbConnectionParameters $params
	 * @throws DbException
	 */
	protected abstract function connect(DbConnectionParameters $params);
	
	/**
	 * Returns the dialect used for the database connection
	 *
	 * @return DbDialect
	 */
	public function getDialect() {
		return $this->dialect;
	}
	
	/**
	 * Prepares the given string to insert to SQL statement.
	 * Escapes the apostrophe and other relevant characters.
	 * Use this for all strings arrived from untrusted source to prevent SQL injection!
	 *
	 * @param string $string The original string
	 * @return string The result that can inserted to an SQL statement
	 */
	public abstract function escape($string);
	
	/**
	 * Closes the database connection
	 *
	 * @throws DbException
	 */
	public abstract function close();
	
	/**
	 * Execute the given primitive SQL statement. No parameters are enabled.
	 *
	 * @param string $sql The SQL statement to execute
	 * @return numeric The number of the affected rows
	 * @throws DbException
	 */
	protected abstract function execPrimitive($sql);
	
	/**
	 * Execute the given SQL statement
	 *
	 * @param NativeSQL $statement The statement to execute
	 * @return integer The number of the affected rows.
	 * @throws DbException
	 */
	public abstract function execNative(NativeSQL $statement);
	
	/**
	 * Execute the given SQL insert statement
	 *
	 * @param NativeSQL $statement The insert statement to execute
	 * @return integer The id of the inserted item.
	 * @throws DbException
	 */
	public abstract function insertNative(NativeSQL $statement);
	
	/**
	 * Executes the native SQL query statement.
	 *
	 * @param NativeSQL $query The query statement
	 * @return mixed Reference to the resultset: the type depends on the engine, don't use it directly!
	 * @throws DbException
	 */
	public abstract function queryNative(NativeSQL $query);
	
	/**
	 * Check whether the result of a query is valid. If valid, returns it, otherwise throws a DbException.
	 *
	 * @param mixed $result Engine-dependent reference to the resultset
	 * @param SQL $query The SQL statement that gave the result (needed for construction exception)
	 * @return mixed the given parameter
	 * @throws DbException If the result is not valid
	 */
	public abstract function testResult($result, SQL $query);
	
	/**
	 * Returns the first row of the resultset as an associative array
	 *
	 * @param mixed $result Engine-dependent reference to the resultset
	 * @param resource $result Engine-függő eredményhalmaz, amit a queryNative() adott
	 * @return array (key=>mixed) or null The first row of the resultset
	 */
	public abstract function fetchFirstRowOnly($result);
	
	/**
	 * Returns a wrapper object for the given resultset.
	 *
	 * @param mixed $result Engine-dependent reference to the resultset
	 * @param boolean $autoClose If finished over the iteration, close the resultset automatically
	 * @return DbResultSet
	 * @throws DbException
	 */
	public abstract function getResultSet($result, $autoClose);
	
	/**
	 * Opens a new transaction. If a transaction is already opened, then only a
	 * counter is incremented to maintain the number of the 'embedded' transactions.
	 * 
	 * @throws DbException
	 */
	public function startTransaction() {
		if ($this->transactions == 0) {
			$this->execPrimitive($this->dialect->getSqlStartTransaction());
			$this->transactions = 1;
		} else {
			$this->transactions++;
		}
	}
	
	/**
	 * Closes the last transaction. This results a real COMMIT only if the outermost transaction is closed.
	 * Otherwise only the transaction counter is decremented
	 * 
	 * @throws DbException
	 */
	public function commit() {
		if ($this->transactions == 1) {
			$this->execPrimitive($this->dialect->getSqlCommitTransaction());
			$this->transactions = 0;
		} else {
			$this->transactions--;
		}
	}
	
	/**
	 * Rollback all the current transactions. The transaction counter will be reseted.
	 * 
	 * @throws DbException
	 */
	public function rollback() {
		$this->execPrimitive($this->dialect->getSqlRollbackTransaction());
		$this->transactions = 0;
	}
	
	/**
	 * Returns true, if there is an opened transaction
	 * 
	 * @return boolean
	 */
	public function isTransactionOpened() {
		return $this->transactions>0;
	}
	
}
