<?php

// ========================================================================================
//
// FLAGS.CLASS
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
// Handles flags (on/off) for several options for each individual user. Flag settings
// are stored in the database. Not used often, but can be used to turn certain reports
// on and off.
//
// ========================================================================================

class d13_flags

{

	protected $d13;

	// ----------------------------------------------------------------------------------------
	// constructor
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct(d13_engine &$d13)
	{
		$this->d13 = $d13;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public static

	function get($index)
	{
	
		
		
		$result = $this->d13->dbQuery('select * from flags');
		$flags = array();
		
		if ($index == 'name') {
			while ($row = $this->d13->dbFetch($result)) {
				$flags[$row['name']] = $row['value'];
			}
		} else {
			for ($i = 0; $row = $this->d13->dbFetch($result); $i++) {
				$flags[$i] = $row;
			}
		}

		return $flags;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public static

	function set($name, $value)
	{
	
		
		
		$this->d13->dbQuery('update flags set value="' . $value . '" where name="' . $name . '"');
		
		if ($this->d13->dbAffectedRows() > - 1) {
			$status = 'done';
		} else {
			$status = 'error';
		}

		return $status;
		
	}
	
}

// =====================================================================================EOF