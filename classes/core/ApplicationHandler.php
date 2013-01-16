<?php

/**
 * The implementation of this interface will interact with RequestHandler and
 * manages non-general use cases required by the application.
 * 
 * @author sebcsaba
 */
interface ApplicationHandler {
	
	/**
	 * Creates the initial Forward, based on the given request.
	 * 
	 * @param Request $request
	 * @return Forward
	 */
	public function determineInitialForward(Request $request);
	
}
