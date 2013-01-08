<?php

/**
 * Represents a Forward that means that an error occured
 * 
 * @author sebcsaba
 */
class ErrorForward implements Forward {
	
	/**
	 * The occured exception
	 * 
	 * @var Exception
	 */
	private $exception;
	
	public function __construct(Exception $exception = null) {
		$this->exception = $exception;
	}
	
	public function run(DI $di, Config $config, Request $request) {
		$message = $this->handleErrorMessage($di, $config);
		if ($request->isAjax()) {
			$httpUtils = $di->get('HttpUtils');
			header('X-Error: '.$httpUtils->encodeHeader($message));
			return null;
		} else {
			$request->setData('title', $config->get('messages/title.error'));
			$request->setData('message', $message);
			$page = $config->get('default.error.page');
			return new PageForward($page);
		}
	}
	
	/**
	 * Handles the given error message, depending on the current lifecycle
	 * 
	 * @param DI $di
	 * @param Config $config
	 * @return string The message to show
	 */
	private function handleErrorMessage(DI $di, Config $config) {
		if ($this->showDetailedMessage($config)) {
			$exceptionUtils = $di->get('ExceptionUtils');
			return $exceptionUtils->convertExceptionToPlainText($this->exception);
		} else {
			$errorLine = sprintf("[%s] from [%s] accessing [%s]: %s\n", date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $this->exception->getMessage());
			error_log($errorLine, 3, 'temp/error.log');
			return $config->get('messages/general.error');
		}
	}
	
	/**
	 * Returns true, if we can show detailed error messages, false otherwise.
	 * 
	 * @param Config $config
	 * @return boolean
	 */
	private function showDetailedMessage(Config $config) {
		return 'development'==$config->get('lifecycle');
	}
	
}
