<?php

/**
 * Abstract class for building SQL command and query statements
 * It's subclasses using fluent interface: except some special
 * case, all the functions return the builder itself, so you can
 * use the return of the building function to the next building step.
 * 
 * @author sebcsaba
 */
abstract class SQLBuilder implements SQL {
	
	/**
	 * WHERE clauses in the statement
	 *
	 * @var array (string)
	 */
	private $whereSql = array();
	
	/**
	 * Data for the WHERE clauses in the statement
	 *
	 * @var array (mixed)
	 */
	private $whereData = array();
	
	/**
	 * Appends a WHERE clause to the statement. (The conjunction of these will give the whole condition.)
	 *
	 * @param string $where Condition expression
	 * @param mixed... $data Data for the expression (as vararg)
	 * @return SQLBuilder $this
	 */
	public function where($where, $data=null) {
		$this->whereSql []= $where;
		$this->whereData = array_merge($this->whereData, func_get_args_but_first());
		return $this;
	}

	/**
	 * Return the string format of the WHERE clause
	 * 
	 * @return string
	 */
	protected function getWhereClause() {
		if (!empty($this->whereSql)) {
			return ' WHERE (' . implode(') AND (',$this->whereSql) . ')';
		} else {
			return '';
		}
	}
	
	/**
	 * Returns the collected data for the WHERE clause
	 * 
	 * @return array (mixed)
	 */
	protected function getWhereData() {
		return $this->whereData;
	}
	
}
