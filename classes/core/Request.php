<?php

/**
 * Represent a HTTP request
 * 
 * @author sebcsaba
 */
class Request {
	
	/**
	 * Contains the HTTP request headers
	 * 
	 * @var array (key=>value)
	 */
	private $headers;
	
	/**
	 * Contains the HTTP requets parameters
	 * 
	 * @var array (key=>value)
	 */
	private $request;
	
	/**
	 * Contains additional request data
	 * 
	 * @var array (key=>value)
	 */
	private $data;
	
	/**
	 * The authenticated user, or null
	 * 
	 * @var User or null
	 */
	private $user;
	
	/**
	 * Creates a Request object from the given header and parameter arrays.
	 * 
	 * @param array (key=>value) $headers Contains the HTTP request headers
	 * @param array (key=>value) $request Contains the HTTP request parameters
	 * @param User or null $user The authenticated user or null
	 */
	public function __construct(array $headers, array $request, User $user = null) {
		$this->headers = array();
		foreach ($headers as $name=>$value) {
			$this->headers[strtolower($name)] = $value;
		}
		$this->request = $request;
		$this->data = array();
		$this->user = $user;
	}
	
	public function isAjax() {
		return strtolower(I($this->headers,'x-requested-with'))==strtolower('XMLHttpRequest');
	}
	
	/**
	 * Retrun the header value for the given name
	 * 
	 * @param string $name
	 * @return string or null
	 */
	public function getHeader($name) {
		return I($this->headers, strtolower($name));
	}
	
	/**
	 * Return true, if there's a parameter with the given key
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function has($key) {
		return array_key_exists($key, $this->request);
	}
	
	/**
	 * Return the parameter value for the given key
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return I($this->request, $key);
	}
	
	/**
	 * Sets the given value as a parameter for the given key
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		$this->request[$key] = $value;
	}
	
	/**
	 * Return true, if there's additional data with the given key
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key) {
		return array_key_exists($key, $this->data);
	}
	
	/**
	 * Return the additional data for the given key
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getData($key) {
		return I($this->data, $key);
	}
	
	/**
	 * Sets the given value as an additional data for the given key
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function setData($key, $value) {
		$this->data[$key] = $value;
	}
	
	/**
	 * Returns the authenticated user, or null
	 * 
	 * @return User or null
	 */
	public function getUser() {
		return $this->user;
	}
	
}
