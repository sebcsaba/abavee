<?php

class NormalApplicationHandlerImpl implements ApplicationHandler {
	
	public function determineInitialForward(Request $request) {
		$do = $request->get('do');
		if (is_null($do)) {
			$do = 'Init';
		}
		return new ActionForward($do.'Action');
	}
	
}
