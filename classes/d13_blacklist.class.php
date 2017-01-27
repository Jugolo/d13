<?php

// ========================================================================================
//
// BLACKLIST.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_blacklist

{
	public static

	function check($type, $value)
	{
		global $d13;
		$result = $d13->dbQuery('select count(*) as count from blacklist where type="' . $type . '" and value="' . $value . '"');
		$row = $d13->dbFetch($result);
		return $row['count'];
	}

	public static

	function get($type)
	{
		global $d13;
		$result = $d13->dbQuery('select * from blacklist where type="' . $type . '"');
		$blacklist = array();
		for ($i = 0; $row = $d13->dbFetch($result); $i++) $blacklist[$i] = $row;
		return $blacklist;
	}

	public static

	function add($type, $value)
	{
		global $d13;
		if (!self::check($type, $value)) {
			$d13->dbQuery('insert into blacklist (type, value) values ("' . $type . '", "' . $value . '")');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'duplicateEntry';
		return $status;
	}

	public static

	function remove($type, $value)
	{
		global $d13;
		if (self::check($type, $value)) {
			$d13->dbQuery('delete from blacklist where type="' . $type . '" and value="' . $value . '"');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}
}

// =====================================================================================EOF

