<?php

// ========================================================================================
//
// UNIT.CLASS
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
// Represents combat units like soldiers, space-ships, orcs, knights and so on. stationed
// at a node (town), can march from and to nodes and participate in battles, scouts and
// other activities. feature they own set of combat/scout related statistics that allow
// to represent tactical strengths/weaknesses.
//
// ========================================================================================

class d13_gameobject_unit extends d13_gameobject_base

{

	// ----------------------------------------------------------------------------------------
	// construct
	// @ Calls base object constructor with an array based argument list
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args, &$node, d13_engine &$d13)
	{
		parent::__construct($args, $node, $d13);
	}
	
	// ----------------------------------------------------------------------------------------
	// checkStatsExtended
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function checkStatsExtended()
	{
		

		
		
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
		
		$upgradeData = $this->getUpgrades();
		
			
		$tvars['tvar_id'] 				= $this->data['id'];
		$tvars['tvar_type'] 			= $this->data['type'];
		$tvars['tvar_class'] 			= $this->d13->getLangGL('classes', $this->data['class']);
		$tvars['tvar_nodeFaction'] 		= $this->node->data['faction'];
		
		$tvars['tvar_attackModifier']		= '';
		$tvars['tvar_defenseModifier']		= '';
		$tvars['tvar_armyAttackModifier']	= '';
		$tvars['tvar_armyDefenseModifier']	= '';
		
		if (!empty($this->data['attackModifier'])) {
			foreach ($this->data['attackModifier'] as $modifier) {
				$tvars['tvar_attackModifier'] 	.= $this->d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_attackModifier'] 	= $this->d13->getLangUI('none');
		}
		
		if (!empty($this->data['defenseModifier'])) {
			foreach ($this->data['defenseModifier'] as $modifier) {
				$tvars['tvar_defenseModifier'] 	.= $this->d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_defenseModifier'] 	= $this->d13->getLangUI('none');
		}
		
		if (!empty($this->data['armyAttackModifier'])) {
			foreach ($this->data['armyAttackModifier'] as $modifier) {
				$tvars['tvar_armyAttackModifier'] 	.= $this->d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_armyAttackModifier'] 	= $this->d13->getLangUI('none');
		}
		
		if (!empty($this->data['armyDefenseModifier'])) {
			foreach ($this->data['armyDefenseModifier'] as $modifier) {
				$tvars['tvar_armyDefenseModifier'] 	.= $this->d13->getLangUI($modifier['stat']) . " +".($modifier['value']*100)."% ";
			}
		} else {
			$tvars['tvar_armyDefenseModifier'] 	= $this->d13->getLangUI('none');
		}
		
		foreach($this->d13->getGeneral('stats') as $stat) {
			$tvars['tvar_unit'.$stat] 			= $this->data[$stat];
			$tvars['tvar_unit'.$stat.'Plus'] 	= $this->data['upgrade_'.$stat];
		}
		
		$tvars['tvar_costData'] = $this->getCostList();
		$tvars['tvar_requirementsData'] = $this->getRequirementsList();
		
		$check_requirements = $this->getCheckRequirements();
		$check_cost = $this->getCheckCost();
		
		if ($check_requirements) {
			$tvars['tvar_requirementsIcon'] = $this->d13->templateGet("sub.requirement.ok");
		} else {
			$tvars['tvar_requirementsIcon'] = $this->d13->templateGet("sub.requirement.notok");
		}

		if ($check_cost) {
			$tvars['tvar_costIcon'] = $this->d13->templateGet("sub.requirement.ok");
		} else {
			$tvars['tvar_costIcon'] = $this->d13->templateGet("sub.requirement.notok");
		}
		

		$tvars['tvar_unitValue'] = $this->data['amount'];
		
		$tvars['tvar_unitLimit'] = $this->getMaxProduction();

		$tvars['tvar_unitUpkeepResourceName'] = $this->d13->getLangGL('resources', $this->data['upkeepResource'], 'name');
		
		return $tvars;
	}	
	
}

// =====================================================================================EOF
