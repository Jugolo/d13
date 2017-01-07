<?php

// ========================================================================================
//
// MODULIT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_modulit

{
	public $moduleId, $level, $input, $unitId, $data, $node, $checkRequirements, $checkCost;

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct($moduleId, $level, $input, $unitId, $node)
	{
		$this->moduleId = $moduleId;
		$this->unitId = $unitId;
		$this->level = $level;
		$this->input = $input;
		$this->setNode($node);
		$this->setStats($moduleId, $level, $input, $unitId);
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
		$this->node->getTechnologies();
	}

	// ----------------------------------------------------------------------------------------
	// setStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function setStats($moduleId, $level, $input, $unitId)
	{
		global $d13;
		$this->data = array();
		$this->data = $d13->getUnit($this->node->data['faction'], $this->unitId);
		$this->data['type'] = 'defense';
		$this->data['moduleId'] = $moduleId;
		$this->data['unitId'] = $unitId;
		$this->data['level'] = $level;
		$this->data['input'] = $input;
		$this->data['name'] = $d13->getLangGL("units", $this->node->data['faction'], $this->unitId) ["name"];
		$this->data['description'] = $d13->getLangGL("units", $this->node->data['faction'], $this->unitId) ["description"];
		foreach($d13->getGeneral('stats') as $stat) {
			$this->data[$stat] = $d13->getUnit($this->node->data['faction'], $this->unitId, $stat);
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
		$costLimit = $this->node->checkCostMax($this->data['cost'], 'train');
		$reqLimit = $this->node->checkRequirementsMax($this->data['requirements']);
		$upkeepLimit = floor($this->node->resources[$d13->getUnit($this->node->data['faction'], $this->unitId, 'upkeepResource') ]['value'] / $d13->getUnit($this->node->data['faction'], $this->unitId, 'upkeep'));
		$unitLimit = abs($this->node->units[$this->unitId]['value'] - $d13->getGeneral('types', $this->data['type'], 'limit'));
		$limitData = min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);
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
	// getCostList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCostList($upgrade = false)
	{
		global $d13;
		$html = '';
		if ($d13->getGeneral('options', 'moduleUpgrade') && $this->data['level'] < $this->data['maxLevel']) {
			$this->data['cost'] = $this->getCost($upgrade);
			foreach($this->data['cost'] as $key => $cost) {
				$html.= '<div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></a></div><div class="cell">' . $cost['value'] . '</div>';
			}
		}

		return $html;
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
		
		$my_upgrades = array();
				
		// - - - - - - - - - - - - - - - MODULE UPGRADES
		if (!empty($this->data['upgrades']) && $this->data['type'] != 'unit' && $this->data['level'] > 1) {
			foreach ($this->data['upgrades'] as $upgrade_id) {
				$tmp_upgrade = $d13->getUpgrade($this->node->data['faction'], $upgrade_id);
				if ($tmp_upgrade['active'] && in_array($tmp_upgrade['id'], $this->data['upgrades'])) {
					$tmp_upgrade['level'] = $this->data['level'];
					$my_upgrades[] = $tmp_upgrade;
				}
			}
		}
		
		#print_r($my_upgrades);
		
		// - - - - - - - - - - - - - - - TECHNOLOGY UPGRADES
		$tmp_list = array();
		foreach($this->node->technologies as $technology) {
			if ($technology['level'] > 0) {
				$tmp_technology = $d13->getTechnology($this->node->data['faction'], $technology['id']);
				foreach ($tmp_technology['upgrades'] as $tmp_upgrade) {
					$tmp_levels[$tmp_upgrade] = $technology['level'];
					$tmp_list[] = $tmp_upgrade;
				}
			}
		}
		
		if (!empty($tmp_list)) {
			foreach ($d13->getUpgrade($this->node->data['faction']) as $tmp_upgrade) {
				if ($tmp_upgrade['active'] && in_array($tmp_upgrade['id'], $tmp_list)) {
					
					
					$pass = false;
					if (empty($tmp_upgrade['targets']) && ($tmp_upgrade['type'] == $this->data['type'])) {
						$pass = true;
					} else if (!empty($tmp_upgrade['targets']) && in_array($this->data['id'], $tmp_upgrade['targets'])) {
						$pass = true;
					}
					
					
					
					if ($pass) {
						$tmp_upgrade['level'] = $tmp_levels[$tmp_upgrade['id']];
						$my_upgrades[] = $tmp_upgrade;
						unset($tmp_list[$tmp_upgrade['id']]);
					}
				}
			}
		}
		
		// - - - - - - - - - - - - - - - APPLY UPGRADES
		if (!empty($my_upgrades)) {
			foreach ($my_upgrades as $upgrade) {
			
				//- - - Cost Upgrade
				if (isset($upgrade['cost'])) {
					$this->data['upgrade_cost'] = $upgrade['cost'];
				}
		
				//- - - Requirements Upgrade
				if (isset($upgrade['requirements'])) {
					$this->data['upgrade_requirements'] = $upgrade['requirements'];
				}
				
				//- - - Attributes Upgrade
				foreach ($upgrade['attributes'] as $attribute) {
					
					if ($attribute['stat'] == 'all' && ($this->data['type'] == 'unit' || $this->data['type'] == 'defense')) {
						foreach($d13->getGeneral('stats') as $stat) {
							$value = $attribute['value'] * $upgrade['level'];
							#$this->data[$stat] += $value;
							$this->data['upgrade_' . strtolower($stat)] += $value;
						}
					} else if ($attribute['stat'] != 'all') {
						$value = $attribute['value'] * $upgrade['level'];
						$this->data[$attribute['stat']] += $value;
						$this->data['upgrade_' . strtolower($attribute['stat'])] += $value;
					}
				}
		
			}
		}
	
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
		$tvars = $this->getStats();
		
		foreach ($this->data as $key => $value) {
			if (!is_array($value)) {
				$tvars['tvar_'.$key] = $value;
			}
		}
		
		return $tvars;	
	}


}

// =====================================================================================EOF

?>