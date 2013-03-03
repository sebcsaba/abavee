<?php

class HttpUtils {
	
	/**
	 * Encodes the given string to quoted-printable
	 * 
	 * @param string $string
	 * @return string
	 */
	public function encodeHeader($string) {
		$string = preg_replace('/\s/', ' ',$string);
		$string = quoted_printable_encode($string);
		$string = preg_replace('/=\s+/','',$string);
		return '=?UTF-8?Q?'.$string.'?=';
	}
	
	/**
	 * Determines the requested language. Uses the following steps:
	 * - If a cookie selects an installed language, returns that.
	 * - If the request sepcifies an installed language, returns that.
	 * - Otherwise Return the default language code from the config.
	 * 
	 * @param Request $request
	 * @param Config $config
	 * @return Language
	 */
	public function getSelectedLanguage(Request $request, Config $config) {
		$installedLanguages = $config->get('language/installed');
		if ($request->has('lang')) {
			$lang = $request->get('lang');
			if (in_array($lang, $installedLanguages)) {
				return $lang;
			}
		}
		foreach ($this->getAcceptedLanguages($request) as $lang) {
			if (in_array($lang, $installedLanguages)) {
				return $lang;
			}
		}
		return $config->get('language/default');
	}
	
	/**
	 * Parses the Accept-language header.
	 * Returns a list of language codes that accepted by the client.
	 * 
	 * @param Request $request
	 * @return array(string)
	 */
	public function getAcceptedLanguages(Request $request) {
		$header = $request->getHeader('accept-language');
		$result = array();
		if (!empty($header)) {
			foreach (explode(',', $header) as $part) {
				$locale = I(explode(';', $part), 0);
				$lang = I(explode('-', $locale), 0);
				if (!in_array($lang, $result)) {
					$result []= $lang;
				}
			}
		}
		return $result;
	}
	
}
