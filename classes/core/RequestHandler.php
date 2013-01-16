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
	 * @param DI $di This will instantiate new Action objects.
	 */
	public function __construct(Config $config, AuthenticationService $authenticationService, ApplicationHandler $applicationHandler, DI $di) {
		$this->config = $config;
		$this->authenticationService = $authenticationService;
		$this->applicationHandler = $applicationHandler;
		$this->di = $di;
	}
	
	/**
	 * This does the work:
	 * 1. parses the request
	 * 2. creates forward
	 * 3. process the given forward
	 * 4. if new forward is given, go to step 2.
	 */
	public function run() {
		// TODO start transaction
		$user = $this->authenticationService->authenticate();
		$request = $this->parseRequest($user);
		$this->di->setInstance($request, 'Request');
		$forward = $this->applicationHandler->determineInitialForward($request);
		do {
			$forward = $this->processForward($request, $forward);
		} while (!is_null($forward));
		// TODO commit/rollback transaction
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
	
}
