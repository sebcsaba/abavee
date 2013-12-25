<?php

class TestDbDialect implements DbDialect {
	
	public function getSqlStartTransaction() {}
	
	public function getSqlCommitTransaction() {}
	
	public function getSqlRollbackTransaction() {}
	
	public function getConnectionInitializerQueries() {
		return array();
	}
	
	public function getLimitClause($limit,$offset) {
		throw new DbException('Not implemented yet: getLimitClause');
	}
	
	/**
	 * Prepares the SQL statement to the engine. The parameter can contain subqueries
	 * that should be flattened. Also, if the engine not supports parameters, the parameter
	 * values also should be included (and escaped!) to the query string.
	 * 
	 * This implementation does the following:
	 * - flattens the subqueries
	 * - inserts the prepared parameter values to the sql query
	 * - converts the boolean parameter values to 0 or 1 number
	 * - converts the DateTime values to string
	 * (The engine cannot accept placeholders in the SQL statement.)
	 *
	 * @param SQL $query General SQL statement
	 * @return NativeSQL Prepared native SQL statement for the corresponding engine
	 */
	public function prepareQuery(SQL $query) {
		$srcString = $query->convertToString($this);
		$srcParams = array_values($query->convertToParamsArray($this));
		$resultString = '';
		
		$srcParamIndex = 0;
		for ($i=0; $i<strlen($srcString); ++$i) {
			if ($srcString[$i]=='?') {
				$param = $srcParams[$srcParamIndex];
				++$srcParamIndex;
				if ($param instanceof SQL) {
					$preparedInnerQuery = $this->prepareQuery($param);
					$resultString .= '('.$preparedInnerQuery->convertToString($this).')';
				} else {
					$resultString .= $this->preparePrimitiveParam($param);
				}
			} else {
				$resultString .= $srcString[$i];
			}
		}
		return new NativeSQL($resultString,array());
	}
	
	/**
	 * Converts primitive values for the test database
	 * - converts the boolean parameter values to 0 or 1 number
	 * - converts the DateTime values to string
	 * 
	 * @param mixed $param
	 * @throws DbException If the value has unknown type
	 * @return mixed
	 */
	private function preparePrimitiveParam($param) {
		if (is_null($param)) {
			return 'null';
		} else if (is_bool($param)) {
			return ($param ? 1 : 0);
		} else if ($param instanceof Timestamp) {
			return "'".$param->toSecondsString()."'";
		} else if (is_string($param)) {
			return "'".addslashes($param)."'";
		} else if (is_int($param) || is_float($param)) {
			return $param;
		} else {
			throw new DbException('cannot convert php variable to primitive');
		}
	}
	
	public function convertPrimitive($type, $value) {
		throw new DbException('Not implemented yet: convertPrimitive');
	}
	
}
