<?php

/**
 * Common interface for any type that describe a mode that an action can return
 * 
 * @author sebcsaba
 */
interface Forward {
	
	/**
	 * Do the required steps for the given forward
	 * 
	 * @param DI $di
	 * @param Config $config
	 * @param Request $request
	 * @return Forward
	 */
	public function run(DI $di, Config $config, Request $request);
	
}
