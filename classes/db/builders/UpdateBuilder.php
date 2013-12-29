<?php

/**
 * Class for building SQL UPDATE statements
 * 
 * @author sebcsaba
 */
class UpdateBuilder extends SQLBuilder {
	
	/**
	 * The table to update
	 *
	 * @var string
	 */
	private $table;
	
	/**
	 * SET clauses in the statement
	 *
	 * @var array (string)
	 */
	private $setSql = array();
	
	/**
	 * Data for the SET clauses in the statement
	 *
	 * @var array (mixed)
	 */
	private $setData = array();
	
	/**
	 * Creates a new builder instance
	 * 
	 * @return UpdateBuilder
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
	 * @return UpdateBuilder $this
	 */
	public function where($where, $data=null) {
		return parent::where($where, $data);
	}
	
	/**
	 * Sets the table to update
	 * 
	 * @param string $table
	 * @return UpdateBuilder
	 */
	public function update($table) {
		$this->table = $table;
		return $this;
	}
	
	/**
	 * Appends a SET clause to the statement
	 *
	 * @param string $where The field to set
	 * @param mixed $data The value to set
	 * @return UpdateBuilder $this
	 */
	public function set($field, $value) {
		$this->setSql []= $field.'=?';
		$this->setData []= $value;
		return $this;
	}
	
	/**
	 * Appends a SET clause to the statement
	 *
	 * @param string $where The field to set
	 * @param string $value The native SQL expression to set
	 * @return UpdateBuilder $this
	 */
	public function setNative($field, $value) {
		$this->setSql []= $field.'='.$value;
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
		$sql = 'UPDATE ' . $this->table;
		if (!empty($this->setSql)) {
			$sql .= ' SET ' . implode(', ',$this->setSql);
		}
		$sql .= $this->getWhereClause();
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
		return array_merge($this->setData, $this->getWhereData());
	}
	
}
