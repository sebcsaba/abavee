<?php

/**
 * Class for building SQL DELETE statements
 * 
 * @author sebcsaba
 */
class DeleteBuilder extends SQLBuilder {
	
	/**
	 * The table to delete from
	 *
	 * @var string
	 */
	private $table;
	
	/**
	 * Creates a new builder instance
	 * 
	 * @return DeleteBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Appends a WHERE clause to the statement. (The conjunction of these will give the whole condition.)
	 * (Redeclared only for declaring the more specific return type.)
	 *
	 * @param string $where Condition expression
	 * @param mixed... $data Data for the expression (as vararg)
	 * @return DeleteBuilder $this
	 */
	public function where($where, $data=null) {
		return parent::where($where, $data);
	}
	
	/**
	 * Sets the table to delete from
	 * 
	 * @param string $table
	 * @return DeleteBuilder
	 */
	public function from($table) {
		$this->table = $table;
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
		return 'DELETE FROM ' . $this->table . $this->getWhereClause();
	}
	
	/**
	 * Returns the array of the parameters of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return array (mixed)
	 */
	public function convertToParamsArray(DbDialect $dialect = null) {
		return $this->getWhereData();
	}
	
}
