<?php

/**
 * Database exception
 * 
 * @author sebcsaba
 */
class DbException extends Exception {
	
	/**
	 * The base error message, without the SQL statement information
	 * 
	 * @var string
	 */
	private $errorMessage;
	
	/**
	 * The SQL statement that caused the exception
	 * 
	 * @var SQL
	 */
	private $statement;

	/**
	 * Creates the exception
	 * 
	 * @param string $errorMessage The base error message, without the SQL statement information
	 * @param SQL or null $statement The SQL statement that caused the exception
	 */
	public function __construct($errorMessage, SQL $statement = null) {
		parent::__construct(self::fmtMessage($errorMessage, $statement));
		$this->errorMessage = $errorMessage;
		$this->statement = $statement;
	}
	
	/**
	 * Creates the full message that includes the SQL statement information (if available)
	 * 
	 * @param string $errorMessage The base error message, without the SQL statement information
	 * @param SQL or null $statement The SQL statement that caused the exception
	 * @return string The full message with the SQL statement information included
	 */
	private static function fmtMessage($errorMessage, SQL $statement = null) {
		if (is_null($statement)) {
			return $errorMessage;
		} else {
			$params = implode_assoc($statement->convertToParamsArray(),'=','',';');
			return sprintf('%s on executing [%s] with parameters [%s]', $errorMessage, $statement->convertToString(), $params);
		}
	}

	/**
	 * Returns the base error message, without the SQL statement information
	 * 
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * Returns the SQL statement that caused the exception
	 * 
	 * @return SQL
	 */
	public function getSQL() {
		return $this->statement;
	}

}
