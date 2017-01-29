<?php

// ========================================================================================
//
// UNIT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_object_unit extends d13_object_base

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
		
		$upgradeData = $this->getUpgrades();
		
			
		$tvars['tvar_id'] 				= $this->data['id'];
		$tvars['tvar_type'] 			= $this->data['type'];
		$tvars['tvar_class'] 			= $d13->getLangGL('classes', $this->data['class']);
		$tvars['tvar_nodeFaction'] 		= $this->node->data['faction'];
		
		$tvars['tvar_attackModifier']		= '';
		$tvars['tvar_defenseModifier']		= '';
		$tvars['tvar_armyAttackModifier']	= '';
		$tvars['tvar_armyDefenseModifier']	= '';
		
		if (!empty($this->data['attackModifier'])) {
			foreach ($this->data['attackModifier'] as $modifier) {
				$tvars['tvar_attackModifier'] 	.= $d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_attackModifier'] 	= $d13->getLangUI('none');
		}
		
		if (!empty($this->data['defenseModifier'])) {
			foreach ($this->data['defenseModifier'] as $modifier) {
				$tvars['tvar_defenseModifier'] 	.= $d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_defenseModifier'] 	= $d13->getLangUI('none');
		}
		
		if (!empty($this->data['armyAttackModifier'])) {
			foreach ($this->data['armyAttackModifier'] as $modifier) {
				$tvars['tvar_armyAttackModifier'] 	.= $d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_armyAttackModifier'] 	= $d13->getLangUI('none');
		}
		
		if (!empty($this->data['armyDefenseModifier'])) {
			foreach ($this->data['armyDefenseModifier'] as $modifier) {
				$tvars['tvar_armyDefenseModifier'] 	.= $d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_armyDefenseModifier'] 	= $d13->getLangUI('none');
		}
		
		foreach($d13->getGeneral('stats') as $stat) {
			$tvars['tvar_unit'.$stat] 			= $this->data[$stat];
			$tvars['tvar_unit'.$stat.'Plus'] 	= "[+".$this->data['upgrade_'.$stat]."]";
		}
		
		$tvars['tvar_costData'] = $this->getCostList();
		$tvars['tvar_requirementsData'] = $this->getRequirementsList();
		
		$check_requirements = $this->getCheckRequirements();
		$check_cost = $this->getCheckCost();
		
		if ($check_requirements) {
			$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
		} else {
			$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
		}

		if ($check_cost) {
			$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
		} else {
			$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
		}
		

		$tvars['tvar_unitValue'] = $this->data['amount'];
		
		$tvars['tvar_unitLimit'] = $this->getMaxProduction();

		$tvars['tvar_unitUpkeepResourceName'] = $d13->getLangGL('resources', $this->data['upkeepResource'], 'name');
		
		return $tvars;
	}	
	
}

// =====================================================================================EOF
