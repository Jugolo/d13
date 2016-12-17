<?php

//========================================================================================
//
// COMBAT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_combat {

	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct() {
		
	}
	
	//----------------------------------------------------------------------------------------
	// doCombat
	//----------------------------------------------------------------------------------------
	public static function doCombat($data) {
	
		global $game;
		
		$data['output']['attacker']['groups'] = array();
		$data['output']['defender']['groups'] = array();
		
		$classes=array();
		foreach ($game['classes'] as $key=>$class) {
			$classes['attacker'][$key]=0;
			$classes['defender'][$key]=0;
		}
		
		foreach ($game['stats'] as $stat) {
			$data['input']['attacker'][$stat] = 0;
			$data['input']['defender'][$stat] = 0;
		}
		
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CALCULATE ATTACKER STATS
		$node = new node();
		$status	= $node->get('id', $data['input']['attacker']['nodeId']);
			
		foreach ($data['input']['attacker']['groups'] as $key=>$group) {
			
			$unit 		= new d13_unit($group['unitId'], $node);
			$stats 		= $unit->getStats();
			$upgrades 	= $unit->getUpgrades();
			
			foreach ($game['stats'] as $stat) {
				$data['input']['attacker']['groups'][$key][$stat]	= ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
				$data['input']['attacker'][$stat]					+= $data['input']['attacker']['groups'][$key][$stat];
			}
			
			$classes['attacker'][$game['units'][$data['input']['attacker']['faction']][$group['unitId']]['class']] += $data['input']['attacker']['groups'][$key]['damage'];
		}
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CALCULATE DEFENDER STATS
		$node = new node();
		$status	= $node->get('id', $data['input']['defender']['nodeId']);
				
		foreach ($data['input']['defender']['groups'] as $key=>$group) {
			
			// - - - - - UNITS
			if ($group['type'] == 'unit') {
				
				$unit 		= new d13_unit($group['unitId'], $node);
				$stats 		= $unit->getStats();
				$upgrades 	= $unit->getUpgrades();
				
				foreach ($game['stats'] as $stat) {
					$data['input']['attacker']['groups'][$key][$stat]	= ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					$data['input']['attacker'][$stat]					+= $data['input']['attacker']['groups'][$key][$stat];
				}
				
				$classes['defender'][$game['units'][$data['input']['defender']['faction']][$group['unitId']]['class']] += $data['input']['defender']['groups'][$key]['damage'];

			// - - - - - MODULES
			} else if ($group['type'] == 'module') {
			
				$modulit 	= new d13_modulit($group['moduleId'], $group['level'], $group['input'], $group['unitId'], $node);
				$stats 		= $modulit->getStats();
				$upgrades 	= $modulit->getUpgrades();
				
				foreach ($game['stats'] as $stat) {
					$data['input']['defender']['groups'][$key][$stat]	= ($stats[$stat] + $upgrades[$stat]) * $group['level'];
					$data['input']['defender'][$stat]					+= $data['input']['defender']['groups'][$key][$stat];
				}
				
				//- - - - - Special rule for Module HP
				if ($game['defensiveModuleDamage']) {
					$data['input']['defender']['groups'][$key]['hp']	= ($data['input']['defender']['groups'][$key]['input']/$data['input']['defender']['groups'][$key]['maxInput'])*$data['input']['defender']['groups'][$key]['hp'];
				}
				
				$classes['defender'][$game['units'][$data['input']['defender']['faction']][$group['unitId']]['class']] += $data['input']['defender']['groups'][$key]['damage'];
				
			}

		}
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Class Ratios (Damage Bonus)
		foreach ($game['classes'] as $key=>$class) {
			if ($data['input']['attacker']['damage']) {
				$classes['attacker'][$key]=$classes['attacker'][$key] / $data['input']['attacker']['damage'];
			}
			if ($data['input']['defender']['damage']) {
				$classes['defender'][$key]=$classes['defender'][$key] / $data['input']['defender']['damage'];
			}
		}
		$data['input']['attacker']['trueDamage'] = max($data['input']['attacker']['damage']-$data['input']['defender']['armor'], 0);
		$data['input']['defender']['trueDamage'] = max($data['input']['defender']['damage']-$data['input']['attacker']['armor'], 0);
		$data['output']['attacker']['totalDamage'] = $data['output']['defender']['totalDamage'] = 0;
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Attacker takes damage
		foreach ($data['input']['attacker']['groups'] as $key=>$group)
		{
			if ($data['input']['attacker'][$data['input']['defender']['focus']]) {
				$ratio=$group[$data['input']['defender']['focus']]/$data['input']['attacker'][$data['input']['defender']['focus']];
			} else {
				$ratio=0;
			}
			
			$baseDamage=ceil($data['input']['defender']['trueDamage']*$ratio);
			$bonusDamage=0;
			foreach ($game['classes'][$game['units'][$data['input']['attacker']['faction']][$group['unitId']]['class']] as $classKey=>$damageMod) {
				$bonusDamage+=floor($baseDamage*$classes['defender'][$classKey]*$damageMod);
			}
			$damage = $baseDamage + $bonusDamage;
			$group['hp'] = max($group['hp'] - $damage, 0);
			
			if ($data['input']['attacker']['groups'][$key]['hp']) {
				$ratio = $group['hp'] / $data['input']['attacker']['groups'][$key]['hp'];
			} else {
				$ratio = 0;
			}
			$group['quantity']=floor($data['input']['attacker']['groups'][$key]['quantity']*$ratio);
			$data['output']['attacker']['groups'][$key]=$group;
			$data['output']['defender']['totalDamage']+=$damage;
		}
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Defender takes damage
		foreach ($data['input']['defender']['groups'] as $key=>$group)
		{
			if ($data['input']['defender'][$data['input']['attacker']['focus']]) {
				$ratio = $group[$data['input']['attacker']['focus']]/$data['input']['defender'][$data['input']['attacker']['focus']];
			} else { 
				$ratio = 0;
			}
			$baseDamage=ceil($data['input']['attacker']['trueDamage']*$ratio);
			$bonusDamage = 0;
			foreach ($game['classes'][$game['units'][$data['input']['defender']['faction']][$group['unitId']]['class']] as $classKey=>$damageMod) {
				$bonusDamage+=floor($baseDamage*$classes['attacker'][$classKey]*$damageMod);
			}
			$damage = $baseDamage + $bonusDamage;
			$group['hp'] = max($group['hp'] - $damage, 0);
			
			if ($data['input']['defender']['groups'][$key]['hp']) {
				$ratio = $group['hp'] / $data['input']['defender']['groups'][$key]['hp'];
			} else { 
				$ratio = 0;
			}
			if ($group['type'] == 'unit') {
				$group['quantity'] = floor($data['input']['defender']['groups'][$key]['quantity'] * $ratio);
			} else if ($group['type'] == 'module') {
				$group['input'] = floor($data['input']['defender']['groups'][$key]['input'] * $ratio);
			}
			$data['output']['defender']['groups'][$key]=$group;
			$data['output']['attacker']['totalDamage']+=$damage;
		}
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Determine Winner
		if ($data['output']['defender']['totalDamage']>=$data['output']['attacker']['totalDamage']) {
			$data['output']['attacker']['winner']=0;
			$data['output']['defender']['winner']=1;
		} else {
			$data['output']['attacker']['winner']=1;
			$data['output']['defender']['winner']=0;
		}
		if ((!$data['input']['attacker']['hp'])&&(!$data['input']['defender']['hp'])) {
			$data['output']['attacker']['winner']=0;
			$data['output']['defender']['winner']=0;
		} else if (($data['input']['attacker']['hp'])&&(!$data['input']['defender']['hp'])) {
			$data['output']['attacker']['winner']=1;
			$data['output']['defender']['winner']=0;
		}
		return $data;
	}

}

//=====================================================================================EOF

?>