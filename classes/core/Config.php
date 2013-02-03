<?php

/**
 * Wraps some array-based configuration data, and gives read-only access for them
 * 
 * @author sebcsaba
 */
class Config {
	
	/**
	 * The configuration data
	 * 
	 * @var array (key=>mixed)
	 */
	private $data;
	
	/**
	 * Creates the configuration wrapper
	 * 
	 * @param array (key=>mixed) $data
	 */
	public function __construct(array $data) {
		$this->data = $data;
	}
	
	/**
	 * Returns the value from the configuration data. The path can contain '/'.
	 * It will be splitted by the slashes, and for each part, the part will be
	 * used as a key indexing the array found by the previous part.
	 * 
	 * @param string $path
	 * @return mixed
	 */
	public function get($path) {
		$result =& $this->data;
		foreach (explode('/', $path) as $index) {
			if (!array_key_exists($index, $result)) {
				throw new Exception('no configuration value found: '.$path);
			}
			$result =& $result[$index];
		}
		return $result;
	}
	
}
