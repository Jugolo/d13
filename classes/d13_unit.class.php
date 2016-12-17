<?php

//========================================================================================
//
// UNIT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_unit {

	public $unitId, $data, $node, $checkRequirements, $checkCost;
	
	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct($unitId, $node) {
		$this->unitId 	= $unitId;
		$this->setNode($node);
		$this->setStats();
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
	public function setStats() { 
		
		global $gl, $game;
		
		$this->data = array();
		$this->data 				= $game['units'][$this->node->data['faction']][$this->unitId];
		$this->data['name']			= $gl["units"][$this->node->data['faction']][$this->unitId]["name"];
		$this->data['description']	= $gl["units"][$this->node->data['faction']][$this->unitId]["description"];
		
		foreach ($game['stats'] as $stat) {
			$this->data[$stat]		= $game['units'][$this->node->data['faction']][$this->unitId][$stat];
			$this->data['upgrade_'.$stat] = 0;
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
	public function getCost() { 
		
		global $game, $ui, $gl;
		
		$cost_array = array();
		foreach ($this->data['cost'] as $key=>$cost) {
			$tmp_array = array();
			$tmp_array['cost'] = $cost['value'] * $game['users']['cost']['train'];
			$tmp_array['name'] = $gl['resources'][$cost['resource']]['name'];
			$tmp_array['icon'] = $cost['resource'].'.png';
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
	// checkUpgrades
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function checkUpgrades() { 
		
		global $d13, $game, $d13_upgrades;
		
		//- - - - - - - - - - - - - - - Component Upgrades
		$unit_comp = array();
		foreach ($this->data['requirements'] as $requirement) {
			if ($requirement['type'] == 'components' && $requirement['active']) {
				$unit_comp[] = array('id'=>$requirement['id'], 'amount'=>$requirement['value']);
			}
		}

		$unit_upgrades = array();
		foreach ($this->node->technologies as $technology) {
			if ($technology['level'] > 0) {
				foreach ($unit_comp as $component) {
					if ($component['id'] == $technology['id']) {
						$unit_upgrades[] = array('id'=>$technology['id'], 'level'=>$technology['level']*$component['amount'], 'upgrades'=>$game['technologies'][$this->node->data['faction']][$technology['id']]['upgrades']);
					}
				}
				//- - - - - - - - - - - - - - - Technology Upgrades	
				$unit_upgrades[] = array('id'=>$technology['id'], 'level'=>$technology['level'], 'upgrades'=>$game['technologies'][$this->node->data['faction']][$technology['id']]['upgrades']);
			}
		}
		
		foreach ($unit_upgrades as $technology) {
			foreach ($technology['upgrades'] as $upgrade) {
				if ($d13_upgrades[$this->node->data['faction']][$upgrade]['id'] == $this->unitId && $d13_upgrades[$this->node->data['faction']][$upgrade]['type'] == 'unit') {
				
					foreach ($d13_upgrades[$this->node->data['faction']][$upgrade]['stats'] as $stats) {
				
						switch ($stats['stat']) {
		
							case 'all':
								foreach ($game['stats'] as $stat) {
									$this->data['upgrade_'.$stat] = floor(misc::percentage($stats['value']*$technology['level'], $this->data[$stat]));
								}
								break;
				
							case 'hp':
								$this->data['upgrade_hp'] = floor(misc::percentage($stats['value']*$technology['level'], $this->data['hp']));
								break;
		
							case 'damage':
								$this->data['upgrade_damage'] = floor(misc::percentage($stats['value']*$technology['level'], $this->data['damage']));
								break;
				
							case 'armor':
								$this->data['upgrade_armor'] = floor(misc::percentage($stats['value']*$technology['level'], $this->data['armor']));
								break;
				
							case 'speed':
								$this->data['upgrade_speed'] = floor(misc::percentage($stats['value']*$technology['level'], $this->data['speed']));
								break;
		
						}
					
					}
			
				}
			}
		}
		
	}

}

//=====================================================================================EOF

?>