<?php

// ========================================================================================
//
// FACTORY.NODE.CLASS
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
// 
//
// ========================================================================================

class d13_factory_node extends d13_factory_base

{

	// ----------------------------------------------------------------------------------------
	// constructor
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct(d13_engine &$d13)
	{
		parent::__construct($d13);
	}

	// ----------------------------------------------------------------------------------------
	// create
	//
	// ----------------------------------------------------------------------------------------
	public

	function create()
	{
		return new d13_node($this->d13);
	}

	// ----------------------------------------------------------------------------------------
	// getNodeList
	// ----------------------------------------------------------------------------------------
	public

	function getNodeList($userId, $otherNode = FALSE)
	{
		
		
		$nodes = array();
		
		if ($otherNode) {
			$result = $this->d13->dbQuery('select * from nodes where user != "' . $userId . '"');
		} else {
			$result = $this->d13->dbQuery('select * from nodes where user = "' . $userId . '"');
		}

		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) {
			$nodes[$i] = $this->create();
			$nodes[$i]->data = $row;
		}

		return $nodes;
	}


}

// =====================================================================================EOF

