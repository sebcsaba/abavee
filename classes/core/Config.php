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
	 * If no value found at the given point, returns the default value if it's
	 * not null, otherwise throws an exception.
	 * 
	 * @param string $path
	 * @param mixed $returnDefault = null
	 * @return mixed
	 * @throws Exception
	 */
	public function get($path, $returnDefault = null) {
		$result =& $this->data;
		foreach (explode('/', $path) as $index) {
			if (!array_key_exists($index, $result)) {
				if (!is_null($returnDefault)) {
					return $returnDefault;
				} else {
					throw new Exception('no configuration value found: '.$path);
				}
			}
			$result =& $result[$index];
		}
		return $result;
	}
	
}
