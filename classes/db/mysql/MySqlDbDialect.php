<?php

/**
 * Specifies the exact SQL syntax for MySQL database driver
 * 
 * @author sebcsaba
 */
class MySqlDbDialect implements DbDialect {
	
	public function getSqlStartTransaction() {
		return 'START TRANSACTION';
	}
	
	public function getSqlCommitTransaction() {
		return 'COMMIT';
	}
	
	public function getSqlRollbackTransaction() {
		return 'ROLLBACK';
	}
	
	public function getConnectionInitializerQueries() {
		return array(
			'SET NAMES utf8',
			'SET AUTOCOMMIT=0',
		);
	}
	
	public function getLimitClause($limit,$offset) {
		if (is_null($limit)) {
			return '';
		} else if (is_null($offset)){
			return sprintf(' LIMIT %d ', $limit);
		} else {
			return sprintf(' LIMIT %d,%d ', $offset, $limit);
		}
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
	 * Converts primitive values for the MySQL database
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
			return "'".mysql_real_escape_string($param)."'";
		} else if (is_int($param) || is_float($param)) {
			return $param;
		} else {
			throw new DbException('cannot convert php variable to mysql primitive');
		}
	}
	
	public function convertPrimitive($type, $value) {
		if (is_null($value)){
			return $value;
		}
		switch ($type){
			case 'boolean':
				if ($value===1 || $value===true) return true;
				if ($value===0 || $value===false) return false;
				throw new DbException('cannot convert mysql boolean value ['.$value.'] for php');
			case 'integer':
				return intval($value);
			case 'float':
				return floatval($value);
			case 'datetime':
				return Timestamp::parse($value);
			case 'string':
			case 'enum':
				return strval($value);
			default:
				throw new DbException('unknown field type: '.$type);
		}
	}
	
}
