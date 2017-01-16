<?php

// ========================================================================================
//
// UNIT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_unit

{
	public $data, $node, $checkRequirements, $checkCost;

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct($unitId, $node)
	{
		$this->setNode($node);
		$this->setStats($unitId);
		$this->checkUpgrades();
	}

	// ----------------------------------------------------------------------------------------
	// setNode
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function setNode($node)
	{
		$this->node = $node;
		if (isset($node)) {
		$this->node->getTechnologies();
		}
	}

	// ----------------------------------------------------------------------------------------
	// setStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function setStats($unitId)
	{
		global $d13;
		
		$this->data = array();
		$this->data = $d13->getUnit($this->node->data['faction'], $unitId);
		
		$this->data['unitId'] = $unitId;
		$this->data['name'] = $d13->getLangGL("units", $this->node->data['faction'], $this->data['unitId'], "name");
		$this->data['description'] = $d13->getLangGL("units", $this->node->data['faction'], $this->data['unitId'], "description");
		
		foreach($d13->getGeneral('stats') as $stat) {
			$this->data[$stat] = $d13->getUnit($this->node->data['faction'], $this->data['unitId'], $stat);
			$this->data['upgrade_' . $stat] = 0;
		}
		
	}

	// ----------------------------------------------------------------------------------------
	// getMaxProduction
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getMaxProduction()
	{
		global $d13;
		
		$costLimit 		= $this->node->checkCostMax($this->data['cost'], 'train');
		$reqLimit 		= $this->node->checkRequirementsMax($this->data['requirements']);
		$upkeepLimit 	= floor($this->node->resources[$d13->getUnit($this->node->data['faction'], $this->data['unitId'], 'upkeepResource') ]['value'] / $d13->getUnit($this->node->data['faction'], $this->data['unitId'], 'upkeep'));
		$unitLimit 		= abs($this->node->units[$this->data['unitId']]['value'] - $d13->getGeneral('types', $this->data['type'], 'limit'));
		$limitData 		= min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);

		return $limitData;
	}

	// ----------------------------------------------------------------------------------------
	// getCheckRequirements
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCheckRequirements()
	{
		$this->checkRequirements = $this->node->checkRequirements($this->data['requirements']);
		if ($this->checkRequirements['ok']) {
			return true;
		}
		else {
			return false;
		}
	}

	// ----------------------------------------------------------------------------------------
	// getCheckCost
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCheckCost()
	{
		$this->checkCost = $this->node->checkCost($this->data['cost'], 'train');
		if ($this->checkCost['ok']) {
			return true;
		}
		else {
			return false;
		}
	}

	// ----------------------------------------------------------------------------------------
	// getRequirements
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getRequirements()
	{
		global $d13;
		$req_array = array();
		foreach($this->data['requirements'] as $key => $requirement) {
			$tmp_array = array();
			if (isset($requirement['level'])) {
				$tmp_array['value'] = $requirement['level'];
			}
			else {
				$tmp_array['value'] = $requirement['value'];
			}

			$tmp_array['name'] = $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name');
			$tmp_array['type'] = $requirement['type'];
			$tmp_array['icon'] = $requirement['id'] . '.png';
			$tmp_array['type_name'] = $d13->getLangUI($requirement['type']);
			$req_array[] = $tmp_array;
		}

		return $req_array;
	}

	// ----------------------------------------------------------------------------------------
	// getCost
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCost($upgrade = false)
	{
		global $d13;
		$cost_array = array();
		foreach($this->data['cost'] as $key => $cost) {
			$tmp_array = array();
			$tmp_array['resource'] = $cost['resource'];
			$tmp_array['value'] = $cost['value'] * $d13->getGeneral('users', 'cost', 'build');
			$tmp_array['name'] = $d13->getLangGL('resources', $cost['resource'], 'name');
			$tmp_array['icon'] = $cost['resource'] . '.png';
			$tmp_array['factor'] = 1;
			if ($upgrade) {
				foreach($this->data['cost_upgrade'] as $key => $upcost) {
					$tmp2_array = array();
					$tmp2_array['resource'] = $upcost['resource'];
					$tmp2_array['value'] = $upcost['value'] * $d13->getGeneral('users', 'cost', 'build');
					$tmp2_array['name'] = $d13->getLangGL('resources', $upcost['resource'], 'name');
					$tmp2_array['icon'] = $upcost['resource'] . '.png';
					$tmp2_array['factor'] = $upcost['factor'];
					if ($tmp_array['resource'] == $tmp2_array['resource']) {
						$tmp_array['value'] = $tmp_array['value'] + floor($tmp2_array['value'] * $tmp2_array['factor'] * $this->data['level']);
					}
				}
			}

			$cost_array[] = $tmp_array;
		}

		return $cost_array;
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
		$stats = array();
		foreach($d13->getGeneral('stats') as $stat) {
			$stats[$stat] = $this->data[$stat];
		}

		return $stats;
	}

	// ----------------------------------------------------------------------------------------
	// getUpgrades
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getUpgrades()
	{
		global $d13;
		$stats = array();
		foreach($d13->getGeneral('stats') as $stat) {
			$stats[$stat] = $this->data['upgrade_' . $stat];
		}

		return $stats;
	}

	// ----------------------------------------------------------------------------------------
	// checkUpgrades
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkUpgrades()
	{
		global $d13;

		// - - - - - - - - - - - - - - - COST & ATTRIBUTES

		foreach($d13->getUpgrade($this->node->data['faction']) as $upgrade) {
			if ($upgrade['type'] == $this->data['type'] && $upgrade['id'] == $this->data['unitId']) {

				// - - - - - - - - - - - - - - - COST

				if (isset($upgrade['cost'])) {
					$this->data['cost_upgrade'] = $upgrade['cost'];
				}

				// - - - - - - - - - - - - - - - ATTRIBUTES

				if (isset($upgrade['attributes'])) {
					$this->data['attributes_upgrade'] = $upgrade['attributes'];
				}
			}
		}

		// - - - - - - - - - - - - - - - Component Upgrades

		$unit_comp = array();
		foreach($this->data['requirements'] as $requirement) {
			if ($requirement['type'] == 'components') {
				$unit_comp[] = array(
					'id' => $requirement['id'],
					'amount' => $requirement['value']
				);
			}
		}

		// - - - - - - - - - - - - - - - Technology Upgrades

		$unit_upgrades = array();
		foreach($this->node->technologies as $technology) {
			if ($technology['level'] > 0) {
				foreach($unit_comp as $component) {
					if ($component['id'] == $technology['id']) {
						$unit_upgrades[] = array(
							'id' => $technology['id'],
							'level' => $technology['level'] * $component['amount'],
							'upgrades' => $d13->getTechnology($this->node->data['faction'], $technology['id'], 'upgrades')
						);
					}
				}

				// - - - - - - - - - - - - - - - Technology Upgrades

				$unit_upgrades[] = array(
					'id' => $technology['id'],
					'level' => $technology['level'],
					'upgrades' => $d13->getTechnology($this->node->data['faction'], $technology['id'], 'upgrades')
				);
			}
		}

		// - - - - - - - - - - - - - - - Apply Upgrades

		foreach($unit_upgrades as $technology) {
			foreach($technology['upgrades'] as $upgrade) {
				if ($d13->getUpgrade($this->node->data['faction'], $upgrade, 'id') == $this->data['unitId'] && $d13->getUpgrade($this->node->data['faction'], $upgrade, 'type') == $this->data['type']) {
					foreach($d13->getUpgrade($this->node->data['faction'], $upgrade, 'attributes') as $stats) {
						if ($stats['stat'] == 'all') {
							foreach($d13->getGeneral('stats') as $stat) {
								$this->data['upgrade_' . $stat] = floor(misc::percentage($stats['value'] * $technology['level'], $this->data[$stat]));
							}
						}
						else {
							$this->data['upgrade_' . $stats['stat']] = floor(misc::percentage($stats['value'] * $technology['level'], $this->data[$stats['stat']]));
						}
					}
				}
			}
		}
	}
	
	// ----------------------------------------------------------------------------------------
	// getCostList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCostList()
	{
	
		$get_costs = $this->getCost();
		
		$costData = '';
		foreach($get_costs as $cost) {
			if ($cost['value'] > 0) {
				$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $cost['name'] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['icon'] . '" title="' . $cost['name'] . '"></a></div>';
			}
		}

		return $costData;

	}
	
	// ----------------------------------------------------------------------------------------
	// getRequirementList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getRequirementList()
	{
		
		global $d13;
		
		$get_requirements = $this->getRequirements();
		
		if (empty($get_requirements)) {
			$requirementsData = $d13->getLangUI('none');
		}
		else {
			$requirementsData = '';
		}

		foreach($get_requirements as $req) {
			$requirementsData.= '<div class="cell">' . $req['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $req['name'] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $req['type'] . '/' . $this->node->data['faction'] . '/' . $req['icon'] . '" title="' . $req['type_name'] . ' - ' . $req['name'] . '"></a></div>';
		}
				
		return $requirementsData;
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
		
		$upgradeData = $this->getUpgrades();
		
		foreach ($this->data as $key => $value) {
			if (!is_array($value)) {
				$tvars['tvar_'.$key] = $value;
			}
		}
		
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
		$tvars['tvar_requirementsData'] = $this->getRequirementList();
		
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
		

		$tvars['tvar_unitValue'] = $this->node->units[$this->data['unitId']]['value'];
		
		$tvars['tvar_unitLimit'] = $this->getMaxProduction();

		$tvars['tvar_unitUpkeepResourceName'] = $d13->getLangGL('resources', $this->data['upkeepResource'], 'name');
		
		return $tvars;
	}	
	
}

// =====================================================================================EOF

?>