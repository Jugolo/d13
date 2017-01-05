<?php

// ========================================================================================
//
// MISC.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class misc

{

	// ----------------------------------------------------------------------------------------
	// time_format
	// ----------------------------------------------------------------------------------------

	public static

	function time_format($secs = 0)
	{
		if ($secs > 0) {
			$dtF = new DateTime('@0');
			$dtT = new DateTime('@' . floor($secs));
			$time = $dtF->diff($dtT)->format('%ad %hh %im %ss');
			$time = str_replace("0d", "", $time);
			$time = str_replace("0h", "", $time);
			$time = str_replace("0m", "", $time);
			$time = str_replace("0s", "", $time);
			return $time;
		}
	}

	// ----------------------------------------------------------------------------------------
	// percentage
	// ----------------------------------------------------------------------------------------

	public static

	function percentage($fraction, $total)
	{
		$percentage = 0;
		if ($fraction > 0 && $total > 0) {
			$percentage = ($fraction/$total)*100;
		}

		return $percentage;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function clean($data, $type = 0)
	{
		global $d13;
		if (is_array($data))
		foreach($data as $key => $value) {
			if (($type) && ($type == 'numeric'))
			if (!is_numeric($value)) $value = 0;
			else $value = floor(abs($value));
			$value = $d13->dbRealEscapeString($value);
			$data[$key] = htmlspecialchars($value);
		}
		else {
			if (($type) && ($type == 'numeric'))
			if (!is_numeric($data)) $data = 0;
			else $data = floor(abs($data));
			$data = $d13->dbRealEscapeString($data);
			$data = htmlspecialchars($data);
		}

		return $data;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function newId($type)
	{
		global $d13;
		$result = $d13->dbQuery('select min(id) as id from free_ids where type="' . $type . '"');
		$id = $d13->dbFetch($result);
		if (isset($id['id'])) {
			$d13->dbQuery('delete from free_ids where id="' . $id['id'] . '" and type="' . $type . '"');
			return $id['id'];
		}
		else {
			$result = $d13->dbQuery('select max(id) as id from ' . $type);
			$id = $d13->dbFetch($result);
			if (isset($id['id'])) return $id['id'] + 1;
			else return 1;
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function sToHMS($seconds)
	{
		$h = floor($seconds / 3600);
		$m = floor($seconds % 3600 / 60);
		$s = $seconds % 3600 % 60;
		return array(
			$h,
			$m,
			$s
		);
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function microTime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

// =====================================================================================EOF

?>