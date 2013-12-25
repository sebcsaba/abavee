<?php

/**
 * Class for building SQL SELECT statements.
 * 
 * $qb = QueryBuilder::create()->select('field0')->from('table1')->where('field2=?',42);
 *
 * If SELECT clause is not specified, a default '*' wil be used.
 * 
 * @author sebcsaba
 */
class QueryBuilder extends SQLBuilder {
	
	/**
	 * SELECT clauses of the query
	 *
	 * @var array (string)
	 */
	private $fieldSql = array();
	
	/**
	 * Data for the SELECT clauses of the query
	 *
	 * @var array (mixed)
	 */
	private $fieldData = array();
	
	/**
	 * FROM clauses of the query
	 *
	 * @var array (string)
	 */
	private $fromSql = array();
	
	/**
	 * Data for the FROM clauses of the query
	 *
	 * @var array (mixed)
	 */
	private $fromData = array();
	
	/**
	 * GROUP BY clauses of the query
	 *
	 * @var array (string)
	 */
	private $groupSql = array();

	/**
	 * ORDER BY clauses of the query
	 *
	 * @var array (string)
	 */
	private $orderSql = array();
	
	/**
	 * LIMIT value of the query
	 *
	 * @var integer or null, if not specified
	 */
	private $limit;
	
	/**
	 * OFFSET value of the query
	 *
	 * @var integer or null, if not specified
	 */
	private $offset;
	
	/**
	 * Creates a new builder instance
	 * 
	 * @return QueryBuilder
	 */
	public static function create() {
		return new self();
	}
	
	/**
	 * Appends a WHERE clause to the query. (The conjunction of these will give the whole condition.)
	 * (Redeclared only for declaring the more specific return type.)
	 *
	 * @param string $where Condition expression
	 * @param mixed... $data Data for the expression (as vararg)
	 * @return QueryBuilder $this
	 */
	public function where($where, $data=null) {
		return call_user_func_array('parent::where', func_get_args());
	}
	
	/**
	 * Appends a SELECT clause to the statement.
	 *
	 * @param string $field Name of a field, or a field expression
	 * @param mixed... $data Data for the field expression (as vararg)
	 * @return QueryBuilder $this
	 */
	public function select($field, $data=null) {
		$this->fieldSql []= $field;
		$this->fieldData = array_merge($this->fieldData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Appends a SELECT COUNT(...) clause to the query
	 *
	 * @param string $field Name of a field, of a field expression. If null or undefined, COUNT(*) will be used.
	 * @param mixed... $data Data for the field expression (as vararg)
	 * @return QueryBuilder $this
	 */
	public function count($field=null, $data=null) {
		if (is_null($field)) {
			$field = '*';
		}
		$this->fieldSql []= 'COUNT('.$field.')';
		$this->fieldData = array_merge($this->fieldData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Appends a FROM clause to the query.
	 *
	 * @param string $from Name of a field, of a field expression.
	 * @param mixed... $data Data for the field expression (as vararg)
	 * @return QueryBuilder $this
	 */
	public function from($from, $data=null) {
		$this->fromSql []= $from;
		$this->fromData = array_merge($this->fromData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Appends a JOIN clause to the query. This is just some syntactic sugar for the from().
	 *
	 * @param string $join Name of a field, of a field expression.
	 * @param mixed... $data Data for the field expression (as vararg)
	 * @return QueryBuilder $this
	 */
	public function join($join, $data=null) {
		$this->fromSql []= 'JOIN '.$join;
		$this->fromData = array_merge($this->fromData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Appends a LEFT JOIN clause to the query. This is just some syntactic sugar for the from().
	 *
	 * @param string $join Name of a field, of a field expression.
	 * @param mixed... $data Data for the field expression (as vararg)
	 * @return QueryBuilder $this
	 */
	public function leftJoin($join, $data=null) {
		$this->fromSql []= 'LEFT JOIN '.$join;
		$this->fromData = array_merge($this->fromData, func_get_args_but_first());
		return $this;
	}
	
	/**
	 * Appends a GROUP BY clause to the query.
	 *
	 * @param string $groupBy Field name
	 * @return QueryBuilder $this
	 */
	public function groupBy($groupBy) {
		$this->groupSql []= $groupBy;
		return $this;
	}
	
	/**
	 * Appends a ORDER BY clause to the query.
	 *
	 * @param string $orderBy Field name
	 * @return QueryBuilder $this
	 */
	public function orderBy($orderBy) {
		$this->orderSql []= $orderBy;
		return $this;
	}
	
	/**
	 * Appends a ORDER BY ... ASC clause to the query.
	 *
	 * @param string $orderBy Field name
	 * @return QueryBuilder $this
	 */
	public function orderByAsc($orderBy) {
		$this->orderSql []= $orderBy.' ASC';
		return $this;
	}
	
	/**
	 * Appends a ORDER BY ... DESC clause to the query.
	 *
	 * @param string $orderBy Field name
	 * @return QueryBuilder $this
	 */
	public function orderByDesc($orderBy) {
		$this->orderSql []= $orderBy.' DESC';
		return $this;
	}
	
	/**
	 * Sets the LIMIT (and optionally the OFFSET) to the query.
	 *
	 * @param integer $limit
	 * @param integer $offset
	 * @return QueryBuilder $this
	 */
	public function limit($limit,$offset=null) {
		$this->limit = $limit;
		$this->offset = $offset;
		return $this;
	}
	
	/**
	 * Returns a clause that specifies the limit and the offset of the query.
	 * 
	 * This is not standardized in SQL, so this method can be used only for
	 * non-executed strings (for example error log messages).
	 * 
	 * @return string
	 */
	private function getDefaultLimitClause() {
		$ret = sprintf(' LIMIT %d ', $this->limit);
		if (!is_null($this->offset)){
			$ret .= sprintf(' OFFSET %d ', $this->offset);
		}
		return $ret;
	}
	
	/**
	 * Returns the string representation of this SQL statement.
	 * 
	 * @param DbDialect $dialect If not given, the implementation can skip or use a default behaviour where
	 * 	    dialect-dependent is needed.
	 * @return string
	 */
	public function convertToString(DbDialect $dialect = null) {
		$sql = 'SELECT ';
		if (!empty($this->fieldSql)) {
			$sql .= implode(', ',$this->fieldSql);
		} else {
			$sql .= '*';
		}
		if (!empty($this->fromSql)) {
			$sql .= ' FROM ' . implode(' ',$this->fromSql);
		}
		$sql .= $this->getWhereClause();
		if (!empty($this->groupSql)) {
			$sql .= ' GROUP BY ' . implode(', ',$this->groupSql);
		}
		if (!empty($this->orderSql)) {
			$sql .= ' ORDER BY ' . implode(', ',$this->orderSql);
		}
		if (!is_null($this->limit)) {
			if (is_null($dialect)) {
				$sql .= $this->getDefaultLimitClause();
			} else {
				$sql .= $dialect->getLimitClause($this->limit, $this->offset);
			}
		}
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
		return array_merge($this->fieldData, $this->fromData, $this->getWhereData());
	}
	
}
