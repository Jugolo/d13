<?php

//========================================================================================
//
// MODULIT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_modulit {

	public $moduleId, $level, $input, $unitId, $data, $node, $checkRequirements, $checkCost;
	
	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct($moduleId, $level, $input, $unitId, $node) {
		$this->moduleId = $moduleId;
		$this->unitId 	= $unitId;
		$this->level	= $level;
		$this->input	= $input;
		
		$this->setNode($node);
		$this->setStats($moduleId, $level, $input, $unitId);
		$this->checkUpgrades();
		
	}
	
	//----------------------------------------------------------------------------------------
	// setNode
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function setNode($node) { 
		$this->node		= $node;
		$this->node->getTechnologies();
	}
		
	//----------------------------------------------------------------------------------------
	// setStats
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function setStats($moduleId, $level, $input, $unitId) { 
		
		global $gl, $game;
		
		$this->data					= array();
		$this->data 				= $game['units'][$this->node->data['faction']][$this->unitId];
		$this->data['type']			= 'defense';
		$this->data['moduleId']		= $moduleId;
		$this->data['unitId']		= $unitId;
		$this->data['level']		= $level;
		$this->data['input']		= $input;
		$this->data['name']			= $gl["units"][$this->node->data['faction']][$this->unitId]["name"];
		$this->data['description']	= $gl["units"][$this->node->data['faction']][$this->unitId]["description"];
		
		foreach ($game['stats'] as $stat) {
			$this->data[$stat]				= $game['units'][$this->node->data['faction']][$this->unitId][$stat];
			$this->data['upgrade_'.$stat] 	= 0;
		}
		
	}

	//----------------------------------------------------------------------------------------
	// getMaxProduction
	// @ 
	// 
	//----------------------------------------------------------------------------------------	
	public function getMaxProduction() {
		
		global $game;
		
		$costLimit 		= $this->node->checkCostMax($this->data['cost'], 'train');
		$reqLimit 		= $this->node->checkRequirementsMax($this->data['requirements']);
		$upkeepLimit	= floor($this->node->resources[$game['units'][$this->node->data['faction']][$this->unitId]['upkeepResource']]['value'] / $game['units'][$this->node->data['faction']][$this->unitId]['upkeep']);
		$unitLimit 		= abs($this->node->units[$this->unitId]['value'] - $game['types'][$this->data['type']]['limit']);
		$limitData 		= min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);
		
		return $limitData;
	}
	
	//----------------------------------------------------------------------------------------
	// getCheckRequirements
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getCheckRequirements() { 
		$this->checkRequirements = $this->node->checkRequirements($this->data['requirements']);
		if ($this->checkRequirements['ok']) {
			return true;
		} else {
			return false;
		}
	}

	//----------------------------------------------------------------------------------------
	// getCheckCost
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getCheckCost() { 
		$this->checkCost = $this->node->checkCost($this->data['cost'], 'train');
		if ($this->checkCost['ok']) {
			return true;
		} else {
			return false;
		}
	}
	
	//----------------------------------------------------------------------------------------
	// getRequirements
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getRequirements() { 
	
		global $game, $ui, $gl;
	
		$req_array = array();

		foreach ($this->data['requirements'] as $key=>$requirement) {
			$tmp_array = array();
			if (isset($requirement['level'])) {
				$tmp_array['value'] = $requirement['level'];
			} else {
				$tmp_array['value'] = $requirement['value'];
			}
			$tmp_array['name'] 		= $gl[$requirement['type']][$this->node->data['faction']][$requirement['id']]['name'];
			$tmp_array['type'] 		= $requirement['type'];
			$tmp_array['icon'] 		= $requirement['id'].'.png';
			$tmp_array['type_name'] = $ui[$requirement['type']];
			$req_array[] = $tmp_array;
		}
		
		return $req_array;
		
	}

	//----------------------------------------------------------------------------------------
	// getCost
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getCost($upgrade=false) { 
		
		global $game, $ui, $gl;
		
		$cost_array = array();
		
		foreach ($this->data['cost'] as $key=>$cost) {
			$tmp_array = array();
			$tmp_array['resource']	= $cost['resource'];
			$tmp_array['value'] 	= $cost['value'] * $game['users']['cost']['build'];
			$tmp_array['name'] 		= $gl['resources'][$cost['resource']]['name'];
			$tmp_array['icon'] 		= $cost['resource'].'.png';
			$tmp_array['factor']	= 1;
			
			if ($upgrade) {
				foreach ($this->data['cost_upgrade'] as $key=>$upcost) {
					$tmp2_array = array();
					$tmp2_array['resource']	= $upcost['resource'];
					$tmp2_array['value'] 	= $upcost['value'] * $game['users']['cost']['build'];
					$tmp2_array['name'] 	= $gl['resources'][$upcost['resource']]['name'];
					$tmp2_array['icon'] 	= $upcost['resource'].'.png';
					$tmp2_array['factor'] 	= $upcost['factor'];
					
					if ($tmp_array['resource'] == $tmp2_array['resource']) {
						$tmp_array['value'] = $tmp_array['value'] + floor($tmp2_array['value'] * $tmp2_array['factor'] * $this->data['level']);
					}

				}
			}
			$cost_array[] = $tmp_array;
			
		}

		return $cost_array;
	}
	
	
	//----------------------------------------------------------------------------------------
	// getStats
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getStats() {
		
		global $game;
		
		$stats = array();
		foreach ($game['stats'] as $stat) {
			$stats[$stat] = $this->data[$stat];
		}
		return $stats;
	}

	//----------------------------------------------------------------------------------------
	// getUpgrades
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getUpgrades() {
	
		global $game;
		
		$stats = array();
		foreach ($game['stats'] as $stat) {
			$stats[$stat] = $this->data['upgrade_'.$stat];
		}
		return $stats;
	
	}

	//----------------------------------------------------------------------------------------
	// getCostList
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getCostList($upgrade=false) {
		
		global $d13, $gl, $game;
		
		$html='';
		
		if ($game['options']['moduleUpgrade'] && $this->data['level'] < $this->data['maxLevel']) {
			$this->data['cost'] = $this->getCost($upgrade);	
			foreach ($this->data['cost'] as $key=>$cost) {
				$html.='<div class="cell"><a class="tooltip-left" data-tooltip="'.$gl["resources"][$cost['resource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></a></div><div class="cell">'.$cost['value'].'</div>';
			}
		}
	
		return $html;
		
	}
	
	//----------------------------------------------------------------------------------------
	// checkUpgrades
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function checkUpgrades() { 
		
		global $d13, $game, $d13_upgrades;
		
		$unit_upgrades = array();
		
		//- - - - - - - - - - - - - - - COST & ATTRIBUTES
		foreach ($d13_upgrades[$this->node->data['faction']] as $upgrade) {
			if ($upgrade['type'] == $this->data['type'] && $upgrade['id'] == $this->data['moduleId']) {
				
				//- - - - - - - - - - - - - - - COST
				if (isset($upgrade['cost'])) {
					$this->data['cost_upgrade'] = $upgrade['cost'];
				}
				//- - - - - - - - - - - - - - - ATTRIBUTES
				if (isset($upgrade['attributes'])) {
					$this->data['attributes_upgrade'] = $upgrade['attributes'];
				}
				
				//- - - - - - - - - - - - - - - STATS by level
				if (isset($upgrade['stats']) && $this->data['level'] > 1) {
					$unit_upgrades[] = array('id'=>$upgrade['id'], 'level'=>$this->data['level'], 'upgrades'=>array($upgrade['id']));
				}
				
			}
		}
		
		//- - - - - - - - - - - - - - - STATS Component Upgrades
		$unit_comp = array();
		foreach ($this->data['requirements'] as $requirement) {
			if ($requirement['type'] == 'components' && $requirement['active']) {
				$unit_comp[] = array('id'=>$requirement['id'], 'amount'=>$requirement['value']);
			}
		}
		
		//- - - - - - - - - - - - - - - STATS Technology Upgrades	
		
		foreach ($this->node->technologies as $technology) {
			if ($technology['level'] > 0) {
				foreach ($unit_comp as $component) {
					if ($component['id'] == $technology['id']) {
						$unit_upgrades[] = array('id'=>$technology['id'], 'level'=>$technology['level'] * $component['amount'], 'upgrades'=>$game['technologies'][$this->node->data['faction']][$technology['id']]['upgrades']);
					}
				}
				//- - - - - - - - - - - - - - - STATS Technology Upgrades	
				$unit_upgrades[] = array('id'=>$technology['id'], 'level'=>$technology['level'], 'upgrades'=>$game['technologies'][$this->node->data['faction']][$technology['id']]['upgrades']);
			}
		}
		
		//- - - - - - - - - - - - - - - STATS Apply Upgrades
		
		foreach ($unit_upgrades as $technology) {
			foreach ($technology['upgrades'] as $upgrade) {
				if ($d13_upgrades[$this->node->data['faction']][$upgrade]['id'] == $this->data['moduleId']) {
					foreach ($d13_upgrades[$this->node->data['faction']][$upgrade]['stats'] as $stats) {
				
						if ($stats['stat'] == 'all') {
							foreach ($game['stats'] as $stat) {
								$this->data['upgrade_'.$stat] = floor(misc::percentage($stats['value'] * $technology['level'], $this->data[$stat]));
							}
						} else {
							$this->data['upgrade_'.$stats['stat']] = floor(misc::percentage($stats['value'] * $technology['level'], $this->data[$stats['stat']]));
						}

					}
				}
			}
		}
		
		
	}

}

//=====================================================================================EOF

?>