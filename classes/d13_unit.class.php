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

	public $data, $node, $checkRequirements, $checkCost;
	
	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct($unitId, $node) {
		
		$this->setNode($node);
		$this->setStats($unitId);
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
	public function setStats($unitId) { 
		
		global $gl, $game;
		
		$this->data = array();
		
		
		$this->data 				= $game['units'][$this->node->data['faction']][$unitId];
		$this->data['type']			= 'unit';
		$this->data['unitId'] 		= $unitId;
		$this->data['name']			= $gl["units"][$this->node->data['faction']][$this->data['unitId']]["name"];
		$this->data['description']	= $gl["units"][$this->node->data['faction']][$this->data['unitId']]["description"];
		
		foreach ($game['stats'] as $stat) {
			$this->data[$stat]		= $game['units'][$this->node->data['faction']][$this->data['unitId']][$stat];
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
		$upkeepLimit	= floor($this->node->resources[$game['units'][$this->node->data['faction']][$this->data['unitId']]['upkeepResource']]['value'] / $game['units'][$this->node->data['faction']][$this->data['unitId']]['upkeep']);
		$unitLimit 		= abs($this->node->units[$this->data['unitId']]['value'] - $game['types'][$this->data['type']]['limit']);
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
	// checkUpgrades
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function checkUpgrades() { 
		
		global $d13, $game, $d13_upgrades;
		
		//- - - - - - - - - - - - - - - COST & ATTRIBUTES
		foreach ($d13_upgrades[$this->node->data['faction']] as $upgrade) {
			if ($upgrade['type'] == $this->data['type'] && $upgrade['id'] == $this->data['unitId']) {
				
				//- - - - - - - - - - - - - - - COST
				if (isset($upgrade['cost'])) {
					$this->data['cost_upgrade'] = $upgrade['cost'];
				}
				//- - - - - - - - - - - - - - - ATTRIBUTES
				if (isset($upgrade['attributes'])) {
					$this->data['attributes_upgrade'] = $upgrade['attributes'];
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
		$unit_upgrades = array();
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
				if ($d13_upgrades[$this->node->data['faction']][$upgrade]['id'] == $this->data['unitId']) {
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