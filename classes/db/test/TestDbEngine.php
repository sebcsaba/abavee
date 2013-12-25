<?php

class TestDbEngine extends DbEngine {
	
	public static function createDatabase(PHPUnit_Framework_TestCase $test, array $testdata) {
		$params = DbConnectionParameters::createFromArray(array());
		$dialect = new TestDbDialect();
		$engine = $test->getMockBuilder('TestDbEngine')
			->setMethods(array('findTestData'))
			->setConstructorArgs(array($params, $dialect, $test, $testdata))
			->getMock();
		$engine->expects($test->exactly(count($testdata)))
			->method('findTestData')
			->will($test->returnCallback(array($engine,'findTestDataMockCallback')));
		return new Database($engine);
	}
	
	private $invocationCount = 0;
	private $testdata;
	private $testcase;
	
	public function __construct(DbConnectionParameters $params, DbDialect $dialect, PHPUnit_Framework_TestCase $testcase, array $testdata) {
		parent::__construct($params, $dialect);
		$this->testdata = $testdata;
		$this->testcase = $testcase;
	}
	
	public function findTestDataMockCallback($sql) {
		$this->testcase->assertLessThan(count($this->testdata), $this->invocationCount);
		$key = I(array_keys($this->testdata), $this->invocationCount);
		$this->testcase->assertEquals($key, $sql);
		++$this->invocationCount;
		return $this->testdata[$key];
	}
	
	protected function findTestData($sql) {
		throw new DbException('findTestData is not implemented in this class, must be mocked!');
	}
	
	protected function getSupportedProtocols() {
		return array('');
	}
	
	protected function connect(DbConnectionParameters $params) {}
	
	public function close() {}
	
	public function escape($string) {
		return addslashes($string);
	}
	
	protected function execPrimitive($sql) {
		return $this->findTestData($sql);
	}
	
	public function execNative(NativeSQL $statement) {
		$sql = $statement->convertToString($this->getDialect());
		return $this->findTestData($sql);
	}
	
	public function insertNative(NativeSQL $statement) {
		$sql = $statement->convertToString($this->getDialect());
		return $this->findTestData($sql);
	}
	
	public function queryNative(NativeSQL $query) {
		$sql = $query->convertToString($this->getDialect());
		return $this->findTestData($sql);
	}
	
	public function testResult($result, SQL $query) {
		if (is_null($result)) {
			throw new DbException('No test data found', $query);
		} else {
			return $result;
		}
	}
	
	public function fetchFirstRowOnly($result) {
		return I($result, 0);
	}
	
	public function getResultSet($result, $autoClose) {
		return new TestDbResultSet($result);
	}
	
}
