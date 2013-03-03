<?php

class TranslationHandler {
	
	private $data;
	
	public function __construct($lang) {
		$file = 'translations'.DIRECTORY_SEPARATOR.$lang.'.txt';
		$this->data = parse_ini_file($file, false, INI_SCANNER_RAW);
	}

	public function translate($key) {
		return I($this->data, $key, '?'.$key.'?');
	}
	
	public function getTranslationFunction() {
		$that = $this;
		return function($key) use ($that) {
			return $that->translate($key);
		};
	}
	
}
