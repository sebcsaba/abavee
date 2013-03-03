<?php

/**
 * Represents a parametrized url.
 * 
 * Usage: fluent interface. You can use the parameter name as a method name,
 * and the value will be the parameter of that method. For example:
 * 
 * print Url::create('index.php')->foo('bar')->id(42);
 * 
 * @author sebcsaba
 */
class Url {
	
	/**
	 * The base of the URL (the part before the parameters and the question mark)
	 * 
	 * @var string
	 */
	private $base;
	
	/**
	 * The parameters
	 * 
	 * @var array (key=>value)
	 */
	private $params;
	
	/**
	 * Create from a base string and the parameter array.
	 * If the base contains a question mark, it will be parsed to the parameter array.
	 * If no base is given, the PHP_SELF will be used.
	 * 
	 * @param string $base
	 * @param array (key=>value) $params
	 */
	public function __construct($base=null, array $params = array()) {
		if (is_null($base)) {
			$base = $_SERVER['PHP_SELF'];
		} else if (strpos($base,'?')>0) {
			$base = self::parse($base,$params);
		}
		$this->base = $base;
		$this->params = $params;
	}
	
	/**
	 * Parses an URL. The parameters found in that will be set to the giben $params array.
	 * The part before the question mark will be returned.
	 * 
	 * @param string $base
	 * @param &array (key=>value) $params
	 * @return string
	 */
	private static function parse($base, &$params) {
		$q = strpos($base,'?');
		$p = substr($base,$q+1);
		foreach (explode('&',$p) as $part) {
			if (strlen($part)>0) {
				@list($name,$value) = explode('=',$part);
				$params[urldecode($name)] = urldecode($value);
			}
		}
		$base = substr($base,0,$q);
		return $base;
	}

	/**
	 * Creates a new URL instance.
	 * 
	 * @param string $base
	 * @param array $params
	 */
	public static function create($base=null, array $params = array()) {
		return new Url($base,$params);
	}
	
	/**
	 * All the undefined method calls will be used as parameter setting:
	 * - the method name will be the parameter name
	 * - the first argument or null will be the parameter value
	 * 
	 * @param string $name
	 * @param array (idx=>value) $args
	 */
	public function __call($name, $args) {
		$value = array_key_exists(0,$args) ? $args[0] : null;
		return $this->setParam($name,$value);
	}
	
	/**
	 * Sets the given value as a parameter for the given name
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value) {
		$this->params[$name] = $value;
		return $this;
	}
	
	/**
	 * Return the parameter value for the given name. If not exists, return null.
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function getParam($name){
		return I($this->params,$name);
	}
	
	/**
	 * Converts this URL to string. Therefore you can write this object with
	 * the print statement, and the well escaped URL will appear on the output.
	 * 
	 * (Well-escaped = urlencode() will be called to all parameter name and value.
	 * Of course, if we print it to HTML, we also need to htmlspecialchars() the result.)
	 * 
	 * @return string
	 */
	public function __toString() {
		$result = '';
		foreach ($this->params as $name=>$value) {
			$result .= '&'.urlencode($name).'='.urlencode($value);
		}
		return $this->base.'?'.substr($result,1);
	}
	
}
