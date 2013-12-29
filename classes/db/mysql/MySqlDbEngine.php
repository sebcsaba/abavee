<?php

/**
 * Database engine implementation for MySQL databases
 * 
 * @author sebcsaba
 */
class MySqlDbEngine extends DbEngine {

	/**
	 * Resource for representing this connection
	 * 
	 * @var resource
	 */
	private $conn;

	protected function getSupportedProtocols() {
		return array('mysql');
	}
	
	protected function connect(DbConnectionParameters $params) {
		$host = sprintf("%s:%d", $params->getHost(), coalesce($params->getPort(), 3306));
		$this->conn = mysql_connect($host, $params->getUsername(), $params->getPassword(), true);
		mysql_select_db($params->getDatabase(), $this->conn);
	}
	
	public function escape($string) {
		return mysql_real_escape_string($string, $this->conn);
	}
	
	public function close() {
		if (!is_null($this->conn)) {
			mysql_close($this->conn);
			$this->conn = null;
		}
	}
	
	public function execPrimitive($sql) {
		$result = @mysql_query($sql, $this->conn);
		if ($result) {
			if ($result===true) {
				return 0;
			}
			$affected_rows = mysql_affected_rows($result);
			mysql_free_result($result);
			return $affected_rows;
		} else{
			throw new DbException(mysql_error($this->conn).' when executing primitive '.$sql);
		}
	}
	
	public function execNative(NativeSQL $statement) {
		$result = @mysql_query($statement->convertToString($this->getDialect()), $this->conn);
		if ($result) {
			if ($result===true) {
				$returnValue = 0;
			} else {
				$returnValue = mysql_affected_rows($result);
			}
			@mysql_free_result($result);
			return $returnValue;
		} else {
			throw new DbException(mysql_error($this->conn), $statement);
		}
	}
	
	public function insertNative(NativeSQL $statement) {
		$result = @mysql_query($statement->convertToString($this->getDialect()), $this->conn);
		if ($result) {
			$returnValue = mysql_insert_id($this->conn);
			@mysql_free_result($result);
			return $returnValue;
		} else {
			throw new DbException(mysql_error($this->conn), $statement);
		}
	}
	
	public function queryNative(NativeSQL $query) {
		return @mysql_query($query->convertToString($this->getDialect()), $this->conn);
	}
	
	public function testResult($result, SQL $query) {
		if ($result) {
			return $result;
		} else {
			throw new DbException(mysql_error($this->conn), $query);
		}
	}
	
	public function fetchFirstRowOnly($result) {
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		return ($row===false) ? null : $row;
	}
	
	public function getResultSet($result, $autoClose) {
		return new MySqlDbResultSet($result, $autoClose);
	}
	
}
