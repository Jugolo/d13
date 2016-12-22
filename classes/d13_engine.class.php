<?php

//========================================================================================
//
// ENGINE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13 {

	public $db, $tpl, $flags, $session, $controller, $data, $profile;
	
	public function __construct() {
		$this->db 			= new d13_db();
		$this->tpl			= new d13_tpl();
		$this->flags 		= new d13_flags();
		$this->controller 	= new d13_controller();
		$this->session		= new d13_session();
		$this->data			= new d13_data();
		
		if (CONST_FLAG_PROFILER) {
			$this->profile		= new d13_profiler();
		}
		
	}
	
}

//=====================================================================================EOF

?>