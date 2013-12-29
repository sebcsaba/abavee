<?php

/**
 * Database connection descriptor: contains all the data required to connect to a given database.
 * 
 * @author sebcsaba
 */
class DbConnectionParameters {
	
	/**
	 * The database engine name
	 * 
	 * @var string
	 */
	private $protocol;
	
	/**
	 * The database server hostname
	 * 
	 * @var string
	 */
	private $host;
	
	/**
	 * The database server port number
	 * 
	 * @var string
	 */
	private $port;
	
	/**
	 * The database account username
	 * 
	 * @var string
	 */
	private $username;
	
	/**
	 * The database account password
	 * 
	 * @var string
	 */
	private $password;
	
	/**
	 * The name of the database to use
	 * 
	 * @var string
	 */
	private $database;
	
	/**
	 * Creates a new connection descriptor. The parameters arrive
	 * from the given associative array, using the following keys:
	 * protocol,host,port,username,password,database
	 * 
	 * @param array (key=>string) $config
	 * @return DbConnectionParameters
	 */
	public static function createFromArray(array $config) {
		return new self(
			I($config,'protocol'),
			I($config,'host'),
			I($config,'port'),
			I($config,'username'),
			I($config,'password'),
			I($config,'database'));
	}
	
	/**
	 * Creates a new connection descriptor
	 * 
	 * @param string $protocol
	 * @param string $host
	 * @param string $port
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 */
	public function __construct($protocol, $host, $port, $username, $password, $database) {
		$this->protocol = $protocol;
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
	}
	
	/**
	 * Returns the database engine name
	 * 
	 * @return string
	 */
	public function getProtocol() {
		return $this->protocol;
	}
	
	/**
	 * Returns the database server hostname
	 * 
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}
	
	/**
	 * Returns the database server port number
	 * 
	 * @return string
	 */
	public function getPort() {
		return $this->port;
	}
	
	/**
	 * Returns the database account username
	 * 
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * Returns the database account password
	 * 
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}
	
	/**
	 * Returns the name of the database to use
	 * 
	 * @return string
	 */
	public function getDatabase() {
		return $this->database;
	}
	
}
