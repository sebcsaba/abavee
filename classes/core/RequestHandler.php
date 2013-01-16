<?php

/**
 * This can handle the request by using Struts-like Actions and Forwards.
 * 
 * @author sebcsaba
 */
class RequestHandler {
	
	/**
	 * The application configuration
	 * 
	 * @var Config
	 */
	private $config;
	
	/**
	 * This manages the authentication
	 * 
	 * @var AuthenticationService
	 */
	private $authenticationService;
	
	/**
	 * Handler for the application-dependent use cases
	 * 
	 * @var ApplicationHandler
	 */
	private $applicationHandler;
	
	/**
	 * The database to run the transaction-handling on
	 * 
	 * @var Database
	 */
	private $database;
	
	/**
	 * A Dependency Injector for instantiating new Action objects.
	 * 
	 * @var DI
	 */
	private $di;
	
	/**
	 * Creates a request handler.
	 * 
	 * @param Config $config
	 * @param AuthenticationService $authenticationService
	 * @param ApplicationHandler $applicationHandler
	 * @param Database $database
	 * @param DI $di This will instantiate new Action objects.
	 */
	public function __construct(Config $config, AuthenticationService $authenticationService, ApplicationHandler $applicationHandler, Database $database, DI $di) {
		$this->config = $config;
		$this->authenticationService = $authenticationService;
		$this->applicationHandler = $applicationHandler;
		$this->database = $database;
		$this->di = $di;
	}
	
	/**
	 * This does the work:
	 * 1. opens a transaction
	 * 2. authenticates the user
	 * 3. parses the request
	 * 4. creates forward
	 * 5. process the given forward
	 * 6. if new forward is given, go to step 4.
	 * 7. commits the transaction
	 * *. rollbacks the transaction when an exception occured
	 */
	public function run() {
		$this->initializePhpEnvironment();
		try {
			$this->database->startTransaction();
			$user = $this->authenticationService->authenticate();
			$request = $this->parseRequest($user);
			$this->di->setInstance($request, 'Request');
			$forward = $this->applicationHandler->determineInitialForward($request);
			do {
				$forward = $this->processForward($request, $forward);
			} while (!is_null($forward));
			$this->database->commit();
		} catch (Exception $ex) {
			$this->database->rollback();
			$forward = new ErrorForward($ex);
			do {
				$forward = $this->processForward($request, $forward);
			} while (!is_null($forward));
		}
	}
	
	/**
	 * Creates a Request object by collecting the request data from
	 * different PHP sources: $_REQUEST and getallheaders().
	 * 
	 * @param User or null $user
	 * @return Request
	 */
	private function parseRequest(User $user = null) {
		return new Request(getallheaders(), $_REQUEST, $user);
	}
	
	/**
	 * Processes the given forward, using the given request.
	 * 
	 * @param Request $request
	 * @param Forward $forward
	 * @return Forward
	 */
	private function processForward(Request $request, Forward $forward) {
		return $forward->run($this->di, $this->config, $request);
	}
	
	/**
	 * Sets some PHP environment setting, depending on the current lifecycle.
	 */
	private function initializePhpEnvironment() {
		if ('development'==$this->config->get('lifecycle')) {
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', 0);
		}
	}
	
}
