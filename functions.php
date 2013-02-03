<?php

/**
 * Indexing to array, and fallback if no key exists
 * 
 * @param array $x
 * @param scalar $i
 * @param mixed $default
 * @return mixed item of $x indexed by $i if exists, $default otherwise
 */
function I(&$x,$i,$default=null) {
	return isset($x[$i]) ? $x[$i] : $default;
}

/**
 * Like func_get_args(), returns its invoker function call's parameters, but without the first item
 * 
 * @return array (mixed)
 */
function func_get_args_but_first() {
	$st = debug_backtrace();
	$args = $st[1]['args'];
	array_shift($args);
	return $args;
}

/**
 * Returns the first not null argument.
 * If there's no such argument, returns null
 * 
 * @param mixed Any data
 * @return mixed The first not-null parameter, if exists
 */
function coalesce() {
	foreach (func_get_args() as $arg) {
		if (!is_null($arg)) {
			return $arg;
		}
	}
	return null;
}

/**
 * Implodes the content of the given associative array.
 * 
 * @param array (mixed) $array
 * @param string $betweenKeyAndValue
 * @param string $beforeItems
 * @param string $afterItems
 * @return string
 */
function implode_assoc(array $array, $betweenKeyAndValue, $beforeItems='', $afterItems='') {
	$result = '';
	foreach ($array as $k=>$v) {
		$result .= $beforeItems.$k.$betweenKeyAndValue.$v.$afterItems;
	}
	return $result;
}

/**
 * Return true, if the given parameter is empty.
 * Wrapper function for php empty predicate
 * 
 * @param mixed $data
 * @return boolean
 */
function is_empty($data) {
	return empty($data);
}
