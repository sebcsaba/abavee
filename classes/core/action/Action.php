<?php

/**
 * Represents an executable action in the request flow
 * 
 * @author sebcsaba
 */
interface Action {
	
	/**
	 * Serve the given action
	 * 
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request);
	
}
