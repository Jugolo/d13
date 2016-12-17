<?php

//========================================================================================
//
// PROFILER.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_profiler {

	private $starttime, $endtime, $duration;
	
	//----------------------------------------------------------------------------------------
	// 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct() {
		$this->starttime 	= 0;
		$this->endtime 		= 0;
		$this->duration		= 0;
		$this->subdata 		= '';
		$this->profile_start();
	}
	
	//----------------------------------------------------------------------------------------
	// 
	// 
	//----------------------------------------------------------------------------------------
	function profile_start() {
		$this->starttime = $this->microtime_float();
	}
	
	//----------------------------------------------------------------------------------------
	// 
	// 
	//----------------------------------------------------------------------------------------
	function profile_end($note="") {
		
		$this->endtime = $this->microtime_float();
		$this->duration = round(($this->endtime - $this->starttime)*1000,3);
		
		$fd = fopen("profile.txt", "a");
		fwrite($fd, ( $this->duration ) . "," . $note . "\n");
		fclose($fd);
	}
	
	//----------------------------------------------------------------------------------------
	// 
	// 
	//----------------------------------------------------------------------------------------
	function profile_get($note="") {
		$this->profile_end($note);
		return $this->duration;
	}
	
	//----------------------------------------------------------------------------------------
	// 
	// 
	//----------------------------------------------------------------------------------------	
	private function microtime_float() {
    	list($usec, $sec) = explode(" ", microtime());
    	return ((float)$usec + (float)$sec);
	}

}

//=====================================================================================EOF

?>