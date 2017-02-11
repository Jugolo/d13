<?php

// ========================================================================================
//
// LOGGER.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
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
// a simple class to measure execution time, queries and number of queries per page.
// can also be used to dump simple debug messages
//
// ========================================================================================

class d13_logger

{
	
	protected $d13;
	
	private $starttime, $endtime, $duration;

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct(d13_engine &$d13)
	{
		$this->d13 = $d13;
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
	public

	function log($note = "")
	{
		$fd = fopen("./logs/log.txt", "a");
		fwrite($fd, $note . "\n");
		fclose($fd);
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
	public
	
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
	
	// ----------------------------------------------------------------------------------------
	// debugLog
	// 
	// 
	// ----------------------------------------------------------------------------------------
	public
	
	function debugLog($page)
	{
	
		global $d13;
	
		$queries = array();
		$count 		= $d13->getQueryCount();
		$queries 	= $d13->getQueries();
	
		$this->log("======================= PROFILE (".$page.") =======================");
		$this->log( "Execution Time: " . $this->profile_get() . 'ms');
		$this->log("-------------------- QUERIES (".$count.") --------------------");
		foreach ($queries as $query) {
			$this->log($query);
		}
		$this->log("=================================================================");
		
	}
	
	// ----------------------------------------------------------------------------------------
	// generateCallTrace
	// 
	// 
	// ----------------------------------------------------------------------------------------
	public
	
	function generateCallTrace()
	{
		$e = new Exception();
		$trace = explode("\n", $e->getTraceAsString());
		// reverse array to make steps line up chronologically
		$trace = array_reverse($trace);
		array_shift($trace); // remove {main}
		array_pop($trace); // remove call to this method
		$length = count($trace);
		$result = array();
	
		for ($i = 0; $i < $length; $i++)
		{
			$result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
		}
	
		return "\t" . implode("\n\t", $result);
	}
	
}

// =====================================================================================EOF

