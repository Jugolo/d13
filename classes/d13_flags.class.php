<?php

// ========================================================================================
//
// FLAGS.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_flags

	{
	public static

	function get($index)
		{
		global $d13;
		$result = $d13->dbQuery('select * from flags');
		$flags = array();
		if ($index == 'name')
			{
			while ($row = $d13->dbFetch($result))
				{
				$flags[$row['name']] = $row['value'];
				}
			}
		  else
			{
			for ($i = 0; $row = $d13->dbFetch($result); $i++)
				{
				$flags[$i] = $row;
				}
			}

		return $flags;
		}

	public static

	function set($name, $value)
		{
		global $d13;
		$d13->dbQuery('update flags set value="' . $value . '" where name="' . $name . '"');
		if ($d13->dbAffectedRows() > - 1)
			{
			$status = 'done';
			}
		  else
			{
			$status = 'error';
			}

		return $status;
		}
	}

// =====================================================================================EOF

?>