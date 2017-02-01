<?php

// ========================================================================================
//
// TURRET.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_object_turret extends d13_object_base

{

	// ----------------------------------------------------------------------------------------
	// construct
	// @ Calls base object constructor with an array based argument list
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args)
	{
		parent::__construct($args);
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

	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTemplateVariables()
	{
	
		global $d13;
		$tvars = array();
		
		$tvars = parent::getTemplateVariables();
		
		return $tvars;
			
	}


}

// =====================================================================================EOF

?>