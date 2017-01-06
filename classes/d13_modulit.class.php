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
		$unit_upgrades = array();

		// - - - - - - - - - - - - - - - COST & ATTRIBUTES

		foreach($d13->getUpgrade($this->node->data['faction']) as $upgrade) {
			if ($upgrade['type'] == $this->data['type'] && $upgrade['id'] == $this->data['moduleId']) {

				// - - - - - - - - - - - - - - - COST

				if (isset($upgrade['cost'])) {
					$this->data['cost_upgrade'] = $upgrade['cost'];
				}

				// - - - - - - - - - - - - - - - ATTRIBUTES

				if (isset($upgrade['attributes'])) {
					$this->data['attributes_upgrade'] = $upgrade['attributes'];
				}

				// - - - - - - - - - - - - - - - STATS by level

				if (isset($upgrade['attributes']) && $this->data['level'] > 1) {
					$unit_upgrades[] = array(
						'id' => $upgrade['id'],
						'level' => $this->data['level'],
						'upgrades' => array(
							$upgrade['id']
						)
					);
				}
			}
		}

		// - - - - - - - - - - - - - - - STATS Component Upgrades

		$unit_comp = array();
		foreach($this->data['requirements'] as $requirement) {
			if ($requirement['type'] == 'components' && $requirement['active']) {
				$unit_comp[] = array(
					'id' => $requirement['id'],
					'amount' => $requirement['value']
				);
			}
		}

		// - - - - - - - - - - - - - - - STATS Technology Upgrades

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

				// - - - - - - - - - - - - - - - STATS Technology Upgrades

				$unit_upgrades[] = array(
					'id' => $technology['id'],
					'level' => $technology['level'],
					'upgrades' => $d13->getTechnology($this->node->data['faction'], $technology['id'], 'upgrades')
				);
			}
		}

		// - - - - - - - - - - - - - - - STATS Apply Upgrades

		foreach($unit_upgrades as $technology) {
			foreach($technology['upgrades'] as $upgrade) {
				if ($d13->getUpgrade($this->node->data['faction']) [$upgrade]['id'] == $this->data['moduleId']) {
					foreach($d13->getUpgrade($this->node->data['faction']) [$upgrade]['attributes'] as $stats) {
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