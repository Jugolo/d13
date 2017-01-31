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

	function checkStatsExtended()
	{
		global $d13;
		
		parent::checkStatsExtended();
		
		$args = array();
		$args['supertype'] 	= 'turret';
		$args['obj_id'] 	= $this->data['unitId'];
		$args['level'] 		= $this->data['level'];
		$args['input'] 		= $this->data['input'];
		$args['node'] 		= $this->node;
				
		$this->turret = new d13_object_turret($args);
	
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
		
		#$baseData = array();
		#$baseData = $this->turret->getStats();
		
		foreach($d13->getGeneral('stats') as $stat) {
			$tvars['tvar_unit'.$stat] 			= $this->data[$stat];
			$tvars['tvar_unit'.$stat.'Plus'] 	= "[+".$this->data['upgrade_'.$stat]."]";
		}
		
		$tvars['tvar_unitType'] 			= $this->data['type'];
		$tvars['tvar_unitClass'] 			= $d13->getLangGL('classes', $this->turret->data['class']);
		$tvars['tvar_nodeFaction'] 		= $this->node->data['faction'];
		
		// - - - - - Base Stats
		/*
		foreach ($this->turret->data as $key => $stat) {
			$this->data[$key] = $stat;
			if (!is_array($stat)) {
			$d13->logger($key." = ".$this->data[$key]);
			}
		}
		*/
			
		// - - - - - Check Upgrades



/*
		$upgradeData = array();
		$upgradeData = $this->turret->getUpgrades();
		
		foreach ($upgradeData as $key => $stat) {
			
			$this->data['upgrade_'.$key] = $stat;
			
			if (!is_array($stat)) {
			$d13->logger('upgrade_'.$key." = ".$this->data['upgrade_'.$key]);
			}
		}
		*/
		/*
		$this->data['unitHPPlus'] 		= "[+" . $upgradeData['hp'] . "]";
		$this->data['unitDamagePlus'] 	= "[+" . $upgradeData['damage'] . "]";
		$this->data['unitArmorPlus'] 	= "[+" . $upgradeData['armor'] . "]";
		$this->data['unitSpeedPlus'] 	= "[+" . $upgradeData['speed'] . "]";
		$this->data['unitVisionPlus'] 	= "[+" . $upgradeData['vision'] . "]";
		$this->data['unitCriticalPlus'] = "[+" . $upgradeData['critical'] . "]";
		$this->data['unitType'] 		= $this->turret->data['type'];
		$this->data['unitClass'] 		= $this->turret->data['class'];
		$this->data['unitHP'] 			= $this->turret->data['hp'];
		$this->data['unitDamage'] 		= $this->turret->data['damage'];
		$this->data['unitArmor'] 		= $this->turret->data['armor'];
		$this->data['unitSpeed'] 		= $this->turret->data['speed'];
		$this->data['unitVision'] 		= $this->turret->data['vision'];
		$this->data['unitCritical'] 	= $this->turret->data['critical'];
		*/
		
		
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