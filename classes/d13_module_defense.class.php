<?php

// ========================================================================================
//
// MODULE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

// ----------------------------------------------------------------------------------------
// d13_module_defense
//
// ----------------------------------------------------------------------------------------

class d13_module_defense extends d13_object_module

{
	
	private $turret;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args)
	{
		parent::__construct($args);
	}
	// ----------------------------------------------------------------------------------------
	// getStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getStats()
	{
		global $d13;
		
		$args = array();
		$args['supertype'] 	= 'unit';
		$args['obj_id'] 	= $this->data['id'];
		$args['level'] 		= $this->data['level'];
		$args['input'] 		= $this->data['moduleInput'];
		$args['unitId'] 	= $this->data['unitId'];
		$args['node'] 		= $this->node;
				
		
		$this->turret = new d13_object_turret($args);
			
		// - - - - - Check Upgrades

		$upgradeData = array();
		$upgradeData = $this->turret->getUpgrades();
		
		$tvars['tvar_unitHPPlus'] 		= "[+" . $upgradeData['hp'] . "]";
		$tvars['tvar_unitDamagePlus'] 	= "[+" . $upgradeData['damage'] . "]";
		$tvars['tvar_unitArmorPlus'] 	= "[+" . $upgradeData['armor'] . "]";
		$tvars['tvar_unitSpeedPlus'] 	= "[+" . $upgradeData['speed'] . "]";
		$tvars['tvar_unitVisionPlus'] 	= "[+" . $upgradeData['vision'] . "]";
		$tvars['tvar_unitCriticalPlus'] = "[+" . $upgradeData['critical'] . "]";
		$tvars['tvar_unitType'] 		= $this->turret->data['type'];
		$tvars['tvar_unitClass'] 		= $this->turret->data['class'];
		$tvars['tvar_unitHP'] 			= $this->turret->data['hp'];
		$tvars['tvar_unitDamage'] 		= $this->turret->data['damage'];
		$tvars['tvar_unitArmor'] 		= $this->turret->data['armor'];
		$tvars['tvar_unitSpeed'] 		= $this->turret->data['speed'];
		$tvars['tvar_unitVision'] 		= $this->turret->data['vision'];
		$tvars['tvar_unitCritical'] 	= $this->turret->data['critical'];
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
	// getOptions
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOptions()
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
		global $d13;
		return $d13->getLangUI("none");
	}
}
?>