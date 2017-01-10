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

	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
	public static
	
	function getLeague($level, $trophies) {
		
		global $d13;
		
		$my_league = 0;
		
		foreach ($d13->getGeneral('leagues') as $league) {
		
			#if ($league['']) {
		
			#}
		
		}

		return $my_league;
		
	}
	
	//----------------------------------------------------------------------------------------
	// gq_percent_difference
	//----------------------------------------------------------------------------------------
	public static
	
	function percent_difference($value1, $value2) {
	
		$percentage = (max($value1, $value2) - min($value1, $value2))/(max($value1,$value2)*100);
		return $percentage;
		
	}
	
	//----------------------------------------------------------------------------------------
	// gq_nextlevelexp
	// @ Returns the experience points required to reach the next level, depending on the current
	//   level as well as the current grade. This formula can scale into infinite.
	// 3.0
	//----------------------------------------------------------------------------------------
	public static
	
	function nextlevelexp($level, $grade=1) {
	
		global $d13;
		$factor = $d13->getGeneral('factors', 'experience');
		$modifier = ceil($grade/4);													// !important - modifier is quarter of the grade
		$level++;																	// because we want next level
		$required_exp = ($level * ($grade * $factor)) * ($level * $modifier) * $factor;
		return ceil($required_exp);
		
	}

	// ----------------------------------------------------------------------------------------
	// record_sort
	//
	// ----------------------------------------------------------------------------------------

	public static
	
	function record_sort($records, $field, $reverse = false)
	{
		$hash = array();
		foreach($records as $key => $record) {
			$hash[$record[$field] . $key] = $record;
		}

		($reverse) ? krsort($hash) : ksort($hash);
		$records = array();
		foreach($hash as $record) {
			$records[] = $record;
		}

		return $records;
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

	function sToHMS($seconds, $asString=false)
	{
		$h = sprintf('%02d', floor($seconds / 3600));
		$m = sprintf('%02d', floor($seconds % 3600 / 60));
		$s = sprintf('%02d', floor($seconds % 3600 % 60));
		$t = array($h,$m,$s);
		
		if ($asString) {
			return implode(":", $t);
		} else {
			return $t;
		}
		
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