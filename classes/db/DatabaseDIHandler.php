<?php

/**
 * Instantiates a Database object.
 * 
 * @author sebcsaba
 */
class DatabaseDIHandler implements DISpecialHandler {
	
	/**
	 * Index of the configuration data
	 * 
	 * @var string
	 */
	private $configFieldName;
	
	/**
	 * Creates a DISpecialHandler for the Database instantialization.
	 * 
	 * @param string $configFieldName This will be used as an index of the configuration data,
	 *     that will used for the connection parameters.
	 */
	public function __construct($configFieldName = 'db') {
		$this->configFieldName = $configFieldName;
	}
	
	public function create(DI $di, $interfaceName, $className) {
		$config = $di->get('Config')->get($this->configFieldName);
		$params = DbConnectionParameters::createFromArray($config);
		$engine = $di->get('DbEngine', array('DbConnectionParameters'=>$params));
		return $di->get($className, array('DbEngine'=>$engine), false);
	}
	
}
