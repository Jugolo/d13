<?php

// ========================================================================================
//
// BLACKLIST.CLASS
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
// Used to organize the message blacklist of a player. Allows to check, add, remove and
// retrieve entries to/from the blacklist. Blacklisting prevents a player from receiving
// messages from other players on his/her blacklist in order to prevent spam/molesting.
//
// ========================================================================================

class d13_blacklist

{

	protected $d13;
	
	// ----------------------------------------------------------------------------------------
	// 
	//
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
	public

	function check($type, $value)
	{
		
		$result = $this->d13->dbQuery('select count(*) as count from blacklist where type="' . $type . '" and value="' . $value . '"');
		$row = $this->d13->dbFetch($result);
		return $row['count'];
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public

	function get($type)
	{
		
		$result = $this->d13->dbQuery('select * from blacklist where type="' . $type . '"');
		$blacklist = array();
		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) $blacklist[$i] = $row;
		return $blacklist;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public

	function add($type, $value)
	{
		
		if (!self::check($type, $value)) {
			$this->d13->dbQuery('insert into blacklist (type, value) values ("' . $type . '", "' . $value . '")');
			if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'duplicateEntry';
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	public

	function remove($type, $value)
	{
		
		if (self::check($type, $value)) {
			$this->d13->dbQuery('delete from blacklist where type="' . $type . '" and value="' . $value . '"');
			if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}
}

// =====================================================================================EOF