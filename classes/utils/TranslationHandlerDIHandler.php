<?php

class TranslationHandlerDIHandler implements DISpecialHandler {
	
	public function create(DI $di, $interfaceName, $className) {
		$request = $di->get('Request');
		$config = $di->get('Config');
		$httpUtils = $di->get('HttpUtils');
		$lang = $httpUtils->getSelectedLanguage($request, $config);
		return new TranslationHandler($lang);
	}
	
}
