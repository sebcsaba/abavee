<?php

/**
 * Manages the authentication for the application
 * 
 * @author sebcsaba
 */
interface AuthenticationService {
	
	/**
	 * Returns the authenticated user's data, or null, if no specific user is authenticated.
	 * 
	 * @return User or null
	 */
	public function authenticate();
	
}
