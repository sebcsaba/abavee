<?php

class ExceptionUtils {
	
	/**
	 * Creates a plaintext representation of the given exception, including the stacktrace.
	 * 
	 * @param Exception $ex
	 * @return string
	 */
	public function convertExceptionToPlainText(Exception $ex) {
		$result = sprintf("%s: %s\n", get_class($ex), $ex->getMessage());
		foreach ($this->normalizeStackTrace($ex) as $i=>$item) {
			$result .= sprintf("#%d %s->%s(...) line: %d\n", $i, I($item,'class'), I($item,'function'), I($item,'line'));
		}
		if (!is_null($ex->getPrevious())) {
			$result .= "Caused by:\n" . $this->convertExceptionToPlainText($ex->getPrevious());
		}
		return $result;
	}
	
	/**
	 * Converts that disgusting PHP stacktrace to a normal one.
	 * Each item in the result has the following keys:
	 * - class (string) name of the class
	 * - function (string) name of the function or method
	 * - line (integer) number of the line
	 * - file (string) file path
	 * 
	 * @param Exception $ex
	 * @return array(array(string=>mixed))
	 */
	public function normalizeStackTrace(Exception $ex) {
		$stack = $ex->getTrace();
		$topLine = $ex->getLine();
		$topFile = $ex->getFile();
		$result = array();
		foreach ($stack as $depth => $oldItem) {
			$result[$depth] = array(
				'class' => I($oldItem,'class'),
				'function' => I($oldItem,'function'),
				'line' => ($depth==0) ? $topLine : I($stack[$depth-1],'line'),
				'file' => ($depth==0) ? $topFile : I($stack[$depth-1],'file'),
			);
		}
		return $result;
	}
	
}
