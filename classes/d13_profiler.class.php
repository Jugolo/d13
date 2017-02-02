<?php

// ========================================================================================
//
// PROFILER.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// a simple class to measure execution time. disabled in the demo project. messes up
// caching of templates. should be replaced with a more sophisticated solution later. 
//
// ========================================================================================

class d13_profiler

{
	private $starttime, $endtime, $duration;

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct()
	{
		$this->starttime = 0;
		$this->endtime = 0;
		$this->duration = 0;
		$this->subdata = '';
		$this->profile_start();
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	private
	function profile_start()
	{
		$this->starttime = $this->microtime_float();
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	private
	function profile_end($note = "")
	{
		$this->endtime = $this->microtime_float();
		$this->duration = round(($this->endtime - $this->starttime) * 1000, 3);
		$fd = fopen("./logs/profile.txt", "a");
		fwrite($fd, ($this->duration) . "," . $note . "\n");
		fclose($fd);
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	function profile_get($note = "")
	{
		$this->profile_end($note);
		return $this->duration;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	private
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

// =====================================================================================EOF

