<?php

/**
 * Represents a Forward that returns an application message
 * 
 * @author sebcsaba
 */
class MessageForward implements Forward {
	
	/**
	 * The message to display
	 * 
	 * @var string
	 */
	private $message;
	
	public function __construct($message) {
		$this->message = $message;
	}
	
	public function run(DI $di, Config $config, Request $request) {
		if ($request->isAjax()) {
			$httpUtils = $di->get('HttpUtils');
			header('X-Error: '.$httpUtils->encodeHeader($this->message));
			return null;
		} else {
			$request->setData('title', $config->get('messages/title.message'));
			$request->setData('message', $this->message);
			$page = $config->get('default.message.page');
			return new PageForward($page);
		}
	}
	
}
