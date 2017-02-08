<?php

// ========================================================================================
//
// SHIELD.CLASS
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
// ABOUT OBJECTS:
// 
// The most important objects in the game have been grouped into a class "objects". This
// includes modules, technologies, units, components and so on. 
//
// NOTES:
//
//
// ========================================================================================

class d13_gameobject_shield extends d13_gameobject_base
{
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @ Calls base object constructor with an array based argument list
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args, &$node)
	{
		parent::__construct($args, $node);
	}

	// ----------------------------------------------------------------------------------------
	// checkStatsExtended
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkStatsExtended()
	{
		global $d13;

		
		
	}


}

// =====================================================================================EOF
