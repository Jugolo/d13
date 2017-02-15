<?php

// ========================================================================================
//
// MODULE.CLASS
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
// ABOUT MODULES:
//
// Modules are Building Objects. Each Node (Town) can contain one or more Modules. Modules
// are the only objects that feature a level and can be upgraded directly. Most of the
// main gameplay features are handled using modules. Modules require a worker resource in
// order to be built/upgraded and require this worker resource in order to function as well.
//
// NOTES:
//
// 
//
// ========================================================================================

class d13_module_defense extends d13_gameobject_module

{
	
	private $turret;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args, &$node, d13_engine &$d13)
	{
		parent::__construct($args, $node, $d13);
	}
	// ----------------------------------------------------------------------------------------
	// getStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkStatsExtended()
	{
		
		
		parent::checkStatsExtended();
		
		$args = array();
		$args['supertype'] 	= 'turret';
		$args['id'] 		= $this->data['unitId'];
		$args['level'] 		= $this->data['level'];
		$args['input'] 		= $this->data['input'];
				
		$this->turret = $this->d13->createGameObject($args, $this->node, $this->d13);
	
	}
	
	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTemplateVariables()
	{
		
		$tvars = array();
		
		$tvars = parent::getTemplateVariables();
		
		$tvars['tvar_unitType'] 			= $this->data['type'];
		$tvars['tvar_Class'] 			= $this->d13->getLangGL('classes', $this->turret->data['class']);
		$tvars['tvar_nodeFaction'] 		= $this->node->data['faction'];

		foreach($this->d13->getGeneral('stats') as $stat) {
			$tvars['tvar_'.$stat] 			= $this->turret->data[$stat];
			$tvars['tvar_'.$stat.'Plus'] 	= "[+".$this->turret->data['upgrade_'.$stat]."]";
		}

		return $tvars;
	}
	
	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getPopup
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getPopup()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getQueue
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getQueue()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getOutputList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOutputList()
	{
		
		return $this->d13->getLangUI("none");
	}
	
	
	
	
}

?>