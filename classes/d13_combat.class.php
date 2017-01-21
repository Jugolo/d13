<?php

// ========================================================================================
//
// COMBAT.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_combat

{

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct()
	{
	}

	// ----------------------------------------------------------------------------------------
	// doCombat
	// ----------------------------------------------------------------------------------------

	public static

	function doCombat($data)
	{
		global $d13;
		
		$data['output']['attacker']['groups'] = array();
		$data['output']['defender']['groups'] = array();
		
		$classes = array();
		
		foreach($d13->getGeneral('classes') as $key => $class) {
			$classes['attacker'][$key] = 0;
			$classes['defender'][$key] = 0;
		}

		foreach($d13->getGeneral('stats') as $stat) {
			$data['input']['attacker'][$stat] = 0;
			$data['input']['defender'][$stat] = 0;
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CALCULATE ATTACKER STATS

		$node = new node();
		$status = $node->get('id', $data['input']['attacker']['nodeId']);
		
		$armyBonus = array();	
		foreach($data['input']['attacker']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {
				$unit = new d13_unit($group['unitId'], $node);
				if (!empty($unit->data['armyAttackModifier'])) {
					foreach ($unit->data['armyAttackModifier'] as $modifier) {
						$armyBonus[] = $modifier;
					}
				}
			}
		}
		
		foreach($data['input']['attacker']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {
			
				$unit = new d13_unit($group['unitId'], $node);
				$stats = $unit->getStats();
				$upgrades = $unit->getUpgrades();
			
				foreach($d13->getGeneral('stats') as $stat) {
			
					$data['input']['attacker']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					
					if (!empty($armyBonus)) {
						foreach ($armyBonus as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['attacker']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}
				
					if (!empty($unit->data['attackModifier'])) {
						foreach ($unit->data['attackModifier'] as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['attacker']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}
				
					$data['input']['attacker'][$stat] += $data['input']['attacker']['groups'][$key][$stat];
				
				}
			
				$data['input']['attacker']['groups'][$key]['critdmg'] = 0;
				$crit = $stats['critical'] + $upgrades['critical'];
			
				if ($crit > 100) { $crit = 100; }
				if (rand(1,100) <= $crit) {
					$data['input']['attacker']['groups'][$key]['critdmg'] += $data['input']['attacker']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
					$data['input']['attacker']['damage'] += $data['input']['attacker']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
				}
				
				//- - - - - Add to Class List
				$class = $d13->getUnit($data['input']['attacker']['faction'],$group['unitId'],'class');
				foreach ($d13->getGeneral('classes', $class) as $classKey => $classMod) {
					$classes['attacker'][$classKey] += $classMod;
				}
		
			}
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CALCULATE DEFENDER STATS

		$node = new node();
		$status = $node->get('id', $data['input']['defender']['nodeId']);
		
		$armyBonus = array();	
		foreach($data['input']['defender']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {
				$unit = new d13_unit($group['unitId'], $node);
				if (!empty($unit->data['armyDefenseModifier'])) {
					foreach ($unit->data['armyDefenseModifier'] as $modifier) {
						$armyBonus[] = $modifier;
					}
				}
			}
		}

		foreach($data['input']['defender']['groups'] as $key => $group) {

			// - - - - - UNITS

			if ($group['type'] == 'unit' && $group['quantity'] > 0) {
			
				$unit = new d13_unit($group['unitId'], $node);
				$stats = $unit->getStats();
				$upgrades = $unit->getUpgrades();
				
				foreach($d13->getGeneral('stats') as $stat) {
				
					$data['input']['defender']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					
					if (!empty($armyBonus)) {
						foreach ($armyBonus as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}
					
					if (!empty($unit->data['defenseModifier'])) {
						foreach ($unit->data['defenseModifier'] as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}
				
					$data['input']['defender'][$stat] += $data['input']['defender']['groups'][$key][$stat];
					
				}
				
				$data['input']['defender']['groups'][$key]['critdmg'] = 0;
				$crit = $stats['critical'] + $upgrades['critical'];
				if ($crit > 100) { $crit = 100; }
				if (rand(1,100) <= $crit) {
					$data['input']['defender']['groups'][$key]['critdmg'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
					$data['input']['defender']['damage'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
				}

				//- - - - - Add to Class List
				$class = $d13->getUnit($data['input']['defender']['faction'],$group['unitId'],'class');
				foreach ($d13->getGeneral('classes', $class) as $classKey => $classMod) {
					$classes['defender'][$classKey] += $classMod;
				}
				
			// - - - - - MODULES

			} else if ($group['type'] == 'module' && $group['input'] > 0) {
			
				$modulit = new d13_modulit($group['moduleId'], $group['level'], $group['input'], $group['unitId'], $node);
				$stats = $modulit->getStats();
				$upgrades = $modulit->getUpgrades();
				
				foreach($d13->getGeneral('stats') as $stat) {
				
					$data['input']['defender']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['level'];
					
					foreach ($armyBonus as $modifier) {
						if ($modifier['stat'] == $stat) {
							$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
						}
					}
				
					if (!empty($unit->data['defenseModifier'])) {
						foreach ($unit->data['defenseModifier'] as $modifier) {
							if ($modifier['stat'] == $stat) {
								$data['input']['defender']['groups'][$key][$stat] += floor($stats[$stat] * $modifier['value']);
							}
						}
					}

					$data['input']['defender'][$stat]+= $data['input']['defender']['groups'][$key][$stat];

				}

				// - - - - - Special rule for Module HP

				if ($d13->getGeneral('options', 'defensiveModuleDamage')) {
					$data['input']['defender']['groups'][$key]['hp'] = ($data['input']['defender']['groups'][$key]['input'] / $data['input']['defender']['groups'][$key]['maxInput']) * $data['input']['defender']['groups'][$key]['hp'];
				}
				
				$data['input']['defender']['groups'][$key]['critdmg'] = 0;
				$crit = $stats['critical'] + $upgrades['critical'];
				if ($crit > 100) { $crit = 100; }
				if (rand(1,100) <= $crit) {
					$data['input']['defender']['groups'][$key]['critdmg'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
					$data['input']['defender']['damage'] += $data['input']['defender']['groups'][$key]['damage'] * $d13->getGeneral('factors', 'critical');
				}
				
				//- - - - - Add to Class List
				$class = $d13->getUnit($data['input']['defender']['faction'],$group['unitId'],'class');
				foreach ($d13->getGeneral('classes', $class) as $classKey => $classMod) {
					$classes['defender'][$classKey] += $classMod;
				}

			}
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Class Ratios (Damage Bonus)
/*
		foreach($d13->getGeneral('classes') as $key => $class) {
			if ($data['input']['attacker']['damage'] > 0) {
				$classes['attacker'][$key] = $classes['attacker'][$key] / $data['input']['attacker']['damage'];
			}

			if ($data['input']['defender']['damage'] > 0) {
				$classes['defender'][$key] = $classes['defender'][$key] / $data['input']['defender']['damage'];
			}
		}
*/
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Base Damage
		
		$percentCap = 400;
		
		$attackerRatio = 0;
		$defenderRatio = 0;
		
		$data['input']['attacker']['damage']++;
		$data['input']['defender']['damage']++;
		$data['input']['attacker']['armor']++;
		$data['input']['defender']['armor']++;
		
		$attackerRatio = sqrt($data['input']['attacker']['damage'] / $data['input']['defender']['armor']);
		$defenderRatio = sqrt($data['input']['defender']['damage'] / $data['input']['attacker']['armor']);
		
		$d13->logger("----------");
		$d13->logger("atk%: ".$attackerRatio." def%: ".$defenderRatio);
		
		
		
		$d13->logger("atkDAM: ".$data['input']['attacker']['damage']." defDAM: ".$data['input']['defender']['damage']);
		
		$attackerDamage = $data['input']['attacker']['damage'] * $attackerRatio;
		$defenderDamage = $data['input']['defender']['damage'] * $defenderRatio;
		
		$d13->logger("atkDAM: ".$attackerDamage." defDAM: ".$defenderDamage);
		
		/*		
		if ($data['input']['attacker']['damage'] >= $data['input']['defender']['armor']) {
			$data['input']['attacker']['trueDamage'] = $data['input']['attacker']['damage'] + $attackerDamage;
		} else {
			$data['input']['attacker']['trueDamage'] = $data['input']['attacker']['damage'] - $attackerDamage;
		}
		
		if ($data['input']['defender']['damage'] >= $data['input']['attacker']['armor']) {
			$data['input']['defender']['trueDamage'] = $data['input']['defender']['damage'] + $defenderDamage;
		} else {
			$data['input']['defender']['trueDamage'] = $data['input']['defender']['damage'] - $defenderDamage;
		}
		*/
		
		$data['input']['attacker']['trueDamage'] = floor($attackerDamage);
		$data['input']['defender']['trueDamage'] = floor($defenderDamage);
		
		$data['output']['attacker']['totalDamage'] = 0;
		$data['output']['defender']['totalDamage'] = 0;

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Attacker takes damage

		foreach($data['input']['attacker']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {

				$damage = 0;
				$casualties = 0;
				$baseDamage = $data['input']['defender']['trueDamage'];
				$bonusDamage = 0;
		
				$d13->logger("defenderDamage:".$baseDamage);									//DEBUG
		
				$class = $d13->getUnit($data['input']['attacker']['faction'], $group['unitId'], 'class');
				foreach ($classes['defender'] as $classKey => $damageMod) {
					if ($classKey == $class) {
						$bonusDamage += floor($baseDamage * $damageMod);
					}
				}
				
				$damage = 1+ $baseDamage + $bonusDamage;
				
				$d13->logger("defenderDamage:".$damage);									//DEBUG
				
				$casualties = floor($damage / ($group['hp']/$group['quantity']));
				$data['input']['defender']['trueDamage'] -= $casualties * $group['hp'];
				
				$d13->logger("atk-casualties: ".$casualties);
				
				$group['quantity'] -= $casualties;
				
				if ($group['quantity'] < 0) { $group = 0; }
				
				$data['output']['attacker']['groups'][$key] = $group;
				$data['output']['defender']['totalDamage'] += $casualties;
			
			}
		}
		
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Defender takes damage

		foreach($data['input']['defender']['groups'] as $key => $group) {
			if ($group['quantity'] > 0) {
				
				$damage = 0;
				$casualties = 0;
				$baseDamage = $data['input']['attacker']['trueDamage'];
				$bonusDamage = 0;
		
				$d13->logger("attackerDamage:".$baseDamage);									//DEBUG
		
				$class = $d13->getUnit($data['input']['defender']['faction'], $group['unitId'], 'class');
				foreach ($classes['attacker'] as $classKey => $damageMod) {
					$d13->logger($classKey);
					if ($classKey == $class) {
						$bonusDamage += floor($baseDamage * $damageMod);
						$d13->logger($bonusDamage);
					}
				}
				
				$damage = 1+ $baseDamage + $bonusDamage;
				
				$d13->logger("attackerDamage:".$damage);									//DEBUG
				
				$casualties = floor($damage / ($group['hp']/$group['quantity']));
				$data['input']['attacker']['trueDamage'] -= $casualties * $group['hp'];
				
				$d13->logger("def-casualties: ".$casualties);
				
				if ($group['type'] == 'unit') {
					$group['quantity'] -= $casualties;
					if ($group['quantity'] < 0) { $group = 0; }
				} else if ($group['type'] == 'module') {
					$group['input'] -= $casualties;
					if ($group['input'] < 0) { $group = 0; }
				}

				$data['output']['defender']['groups'][$key] = $group;
				$data['output']['attacker']['totalDamage']	+= $casualties;
			
			}
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Determine Winner

		if ($data['output']['defender']['totalDamage'] >= $data['output']['attacker']['totalDamage']) {
			$data['output']['attacker']['winner'] = 0;
			$data['output']['defender']['winner'] = 1;
		}
		else {
			$data['output']['attacker']['winner'] = 1;
			$data['output']['defender']['winner'] = 0;
		}

		if ((!$data['input']['attacker']['hp']) && (!$data['input']['defender']['hp'])) {
			$data['output']['attacker']['winner'] = 0;
			$data['output']['defender']['winner'] = 0;
		}
		else
		if (($data['input']['attacker']['hp']) && (!$data['input']['defender']['hp'])) {
			$data['output']['attacker']['winner'] = 1;
			$data['output']['defender']['winner'] = 0;
		}

		return $data;
	}

	// ----------------------------------------------------------------------------------------
	// doScoutCheck
	// ----------------------------------------------------------------------------------------
	
	public static
	
	function doScoutCheck($data)
	{
	
		global $d13;
		
		
		
		
		
		
		
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doScout
	// ----------------------------------------------------------------------------------------
	
	public static
	
	function doScout($data)
	{
	
		global $d13;
		
		
		
		
		
		
		
	
	}

	// ----------------------------------------------------------------------------------------
	// doSabotage
	// ----------------------------------------------------------------------------------------
	
	public static
	
	function doSabotage($data)
	{
	
		global $d13;
		
		
		
		
		
		
		
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doAdjustLeague
	// ----------------------------------------------------------------------------------------
	public static
	
	function doAdjustLeague($data)
	{
		global $d13;
		
		$exp_value = 0;
		$attackerTrophies = $d13->getGeneral('factors', 'experience') + count($d13->getLeague());
		$defenderTrophies = $d13->getGeneral('factors', 'experience') + count($d13->getLeague());
		
		$attackerUser = new user();
		$status = $attackerUser->get('id', $data['input']['attacker']['userId']);
		
		$defenderUser = new user();
		$status = $defenderUser->get('id', $data['input']['defender']['userId']);
		
		if ($status == 'done') {
		
			$exp_value 		= floor($data['input']['attacker']['trueDamage'] / $d13->getGeneral('factors', 'experience'));
			$difference		= misc::percent_difference($attackerUser->data['trophies'], $defenderUser->data['trophies']) + 1;
		
			if ($attackerUser->data['trophies'] > $defenderUser->data['trophies']) {
				$attackerTrophies -= ($difference/100)*$attackerTrophies;
				$defenderTrophies += ($difference/100)*$defenderTrophies;
			} else if ($attackerUser->data['trophies'] <= $defenderUser->data['trophies']) {
				$attackerTrophies += ($difference/100)*$attackerTrophies;
				$defenderTrophies -= ($difference/100)*$defenderTrophies;
			}
			
			if ($data['output']['attacker']['winner']) {
				$attackerTrophies = abs($attackerTrophies);
				$defenderTrophies = $defenderTrophies * -1;
			} else if ($data['output']['defender']['winner']) {
				$attackerTrophies = $attackerTrophies * -1;
				$defenderTrophies = abs($defenderTrophies);
			}
			
			$attackerTrophies = floor($attackerTrophies);
			$defenderTrophies = floor($defenderTrophies);
		
		}
		
		// - - - - - Attacker Stats
		$status = $attackerUser->setStat('experience', $exp_value);
		$status = $attackerUser->setStat('trophies', $attackerTrophies);
		
		// - - - - - Defender Stats
		$status = $defenderUser->setStat('trophies', $defenderTrophies);
		
		return $status;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// doStealResources
	// ----------------------------------------------------------------------------------------
	public static
	
	function doStealResources($data)
	{
	
		global $d13;
		
		// ============================== Calculate Ratio according to Leagues
		
		$resourceRatio = 20; 
		
		$attackerUser = new user();
		$status = $attackerUser->get('id', $data['input']['attacker']['userId']);
		
		$defenderUser = new user();
		$status = $defenderUser->get('id', $data['input']['defender']['userId']);
		
		if ($status == 'done') {
		
			$attackerLeague = $d13->getLeague(misc::getLeague($attackerUser->data['level'], $attackerUser->data['trophies']));
			$defenderLeague = $d13->getLeague(misc::getLeague($defenderUser->data['level'], $defenderUser->data['trophies']));
			
			$attackerLeague['id']++;
			$defenderLeague['id']++;
			
			if ($attackerLeague['id'] >= $defenderLeague['id']) {
				$resourceRatio -= floor($resourceRatio * ($attackerLeague['id']-$defenderLeague['id'])/$attackerLeague['id']);
			} else {
				$resourceRatio += floor($resourceRatio * ($defenderLeague['id']-$attackerLeague['id'])/$defenderLeague['id']);
			}
		
			if ($resourceRatio < 1) {
				$resourceRatio = 1;
			} else if ($resourceRatio > 60) {
				$resourceRatio = 60;
			}
		
		}
		
		// ============================== Calculate total Capacity and Ratio
		
		$totalResCapacity	= 0;
		$totalResAvailable	= 0;
		$resList 			= array();
		$realResList		= array();
		
		$data['output']['attacker']['resources'] = array();
		$data['output']['defender']['resources'] = array();
		
		// - Attacker Capacity
		$attackerNode = new node();
		$status = $attackerNode->get('id', $data['input']['attacker']['nodeId']);
		
		if ($status == 'done') {
		
			foreach($data['input']['attacker']['groups'] as $key => $group) {
				$unit = new d13_unit($group['unitId'], $attackerNode);
				$stats = $unit->getStats();
				$upgrades = $unit->getUpgrades();
			
				foreach($d13->getGeneral('stats') as $stat) {
					$data['input']['attacker']['groups'][$key][$stat] = ($stats[$stat] + $upgrades[$stat]) * $group['quantity'];
					$data['input']['attacker'][$stat]+= $data['input']['attacker']['groups'][$key][$stat];
				}

				$totalResCapacity += $data['input']['attacker']['groups'][$key]['capacity'];
			}
		
		}
		
		// - Defender Availability
		$defenderNode = new node();
		$status = $defenderNode->get('id', $data['input']['defender']['nodeId']);
		
		if ($status == 'done') {
			$defenderNode->getResources();

			foreach ($defenderNode->resources as $resource) {
			
				$tmp_res = $d13->getResource($resource['id']);
			
				if ($tmp_res['active'] && $tmp_res['type'] == 'dynamic' && $tmp_res['carryable'] && $resource['value'] > 0) {
					
						$resAvailable = $resource['value'] * ($resourceRatio/100);
						$totalResAvailable += $resAvailable;
						$resList[] = array('resource'=>$resource['id'], 'value'=>$resAvailable);
						$realResList[] = array('resource'=>$resource['id'], 'value'=>0);
					
				}
			}
		
		}
		
		$totalResCapacity = floor($totalResCapacity);
		$totalResAvailable = floor($totalResAvailable);

		// ============================== Check existing Loot
		$continue = true;
		
		if (!empty($resList)) {
			while ($continue) {
			
				// - select random from available resources
				$key = array_rand($resList);	
			
				// - choose 1/10 from available resources
				$value = floor($resList[$key]['value']/10);
				
				if ($value > $totalResCapacity) {
					$value = $totalResCapacity;
				}
				
				$totalResCapacity -= $value;
				$totalResAvailable -= $value;
				$resList[$key]['value'] -= $value;
				$realResList[$key]['value'] += $value;
				
				// - stop when either capacity or resources are depleted
				if ($totalResCapacity <= 0 || $totalResAvailable <= 0) {
					$continue = false;
				}

			}
		}
		
		// ============================== Check virtual Loot
		if ($d13->getGeneral('options', 'bonusLoot')) {

		}
				
		// ============================== Process Data
		$d13->dbQuery('start transaction');
		
		$data['output']['defender']['resources'] = $realResList;
		$ok = $defenderNode->setResources($realResList);
		
		$data['output']['attacker']['resources'] = $realResList;
		$ok = $attackerNode->setResources($realResList, 1);

		if ($ok) {
			$d13->dbQuery('commit');
		}
		else {
			$d13->dbQuery('rollback');
		}
		
		// ============================== Return Data
		
		return $data;
	
	}
	
	
	
	// ----------------------------------------------------------------------------------------
	// checkCombat
	// ----------------------------------------------------------------------------------------

	public static
	
	function checkCombat()
	{
	
	
	
	}

	// ----------------------------------------------------------------------------------------
	// assembleReport
	// ----------------------------------------------------------------------------------------

	public static
	
	function assembleReport($data, $attackerNode, $defenderNode, $type, $other=false)
	{
		
		global $d13;
		
		$html = '';
		$tvars = array();
		
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Report Header

		if (!$other) {
			if ($data['output']['attacker']['winner']) {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($type) . ' ' . $d13->getLangUI("won");
			}
			else {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($type) . ' ' . $d13->getLangUI("lost");
			}
		} else {
			if ($data['output']['defender']['winner']) {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($type) . ' ' . $d13->getLangUI("won");
			}
			else {
				$tvars['tvar_msgHeader'] = $d13->getLangUI($type) . ' ' . $d13->getLangUI("lost");
			}
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  Report Attacker
		$html = '';
		$totalAmount 	= 0;
		$leftAmount 	= 0;
		$totalSpeed 	= 0;
		$totalDamage 	= 0;
		$totalArmor 	= 0;
		$totalVision 	= 0;
		$totalStealth 	= 0;
		$limit = 7;
		$i = 0;
		$open = false;
		
		// - - - - - 
		foreach($data['output']['attacker']['groups'] as $key => $group) {
			
			if ($data['input']['attacker']['groups'][$key]['quantity']) {
				
				$totalAmount 	+= $data['input']['attacker']['groups'][$key]['quantity'];
				$leftAmount		+= $group['quantity'];
				
				$totalSpeed 	+= $data['input']['attacker']['groups'][$key]['speed'];
				$totalArmor 	+= $data['input']['attacker']['groups'][$key]['armor'];
				$totalVision	+= $data['input']['attacker']['groups'][$key]['vision'];
				$totalStealth 	+= $data['input']['attacker']['groups'][$key]['stealth'];
				$totalDamage 	+= $data['input']['attacker']['groups'][$key]['damage'];
				
				$name 			= $d13->getLangGL('units', $attackerNode->data['faction'], $group['unitId'], 'name');
				$label 			= $group['quantity']. "/" .$data['input']['attacker']['groups'][$key]['quantity'];
				$image 			= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $attackerNode->data['faction'] . '/' . $d13->getUnit($attackerNode->data['faction'], $group['unitId'], 'image');
					
				$vars = array();
				$vars['tvar_listImage'] = $image;
				$vars['tvar_listLabel'] = $name;
				$vars['tvar_listAmount'] = $label;
				
				if ($i == 0) {
					$html .= '<div class="row">';
					$open = true;
				}
		
				$html .= $d13->templateSubpage("msg.combat.entry", $vars);
				
				if ($i == $limit) {
					$html .= '</div>';
					$open = false;
				}
				
				$i++;
				
			}
		}
		
		if ($open) {
			$html .= '</div>';
		}
		
		if (empty($html)) {
			$html = '<div class="row"><div class="col-auto">' . $d13->getLangUI('none') . '</div></div>';
		}
		
		
		// - - - - - Army Stats
		
		$vars = array();
		$vars['tvar_totalAmount'] 	= $totalAmount;
		$vars['tvar_leftAmount'] 	= $leftAmount;
		$vars['tvar_totalSpeed'] 	= $totalSpeed;
		$vars['tvar_totalDamage'] 	= $totalDamage;
		$vars['tvar_totalArmor'] 	= $totalArmor;
		$vars['tvar_totalVision'] 	= $totalVision;
		$vars['tvar_totalStealth'] 	= $totalStealth;
		
		if (!$other) {
			$tvars['tvar_msgSelfStats'] = $d13->templateSubpage("msg.combat.stats", $vars);
			$tvars['tvar_msgSelfRowName'] = $attackerNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgSelfRow'] = $html;
		} else {
			$tvars['tvar_msgOtherStats'] = $d13->templateSubpage("msg.combat.stats", $vars);
			$tvars['tvar_msgOtherRowName'] = $attackerNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgOtherRow'] = $html;
		}

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Report Defender
		$html = '';
		$totalAmount 	= 0;
		$leftAmount 	= 0;
		$totalSpeed 	= 0;
		$totalDamage 	= 0;
		$totalArmor 	= 0;
		$totalVision 	= 0;
		$totalStealth 	= 0;
		$i = 0;
		$open = false;
		
		// - - - - - 
		foreach($data['output']['defender']['groups'] as $key => $group) {

			if ($group['type'] == 'unit') {
				// - - - - - Unit
				if ($data['input']['defender']['groups'][$key]['quantity']) {

					$totalAmount 	+= $data['input']['defender']['groups'][$key]['quantity'];
					$leftAmount		+= $group['quantity'];
					
					$totalSpeed 	+= $data['input']['defender']['groups'][$key]['speed'];
					$totalArmor 	+= $data['input']['defender']['groups'][$key]['armor'];
					$totalVision	+= $data['input']['defender']['groups'][$key]['vision'];
					$totalStealth 	+= $data['input']['defender']['groups'][$key]['stealth'];
					$totalDamage 	+= $data['input']['defender']['groups'][$key]['damage'];
					
					$name 			= $d13->getLangGL('units', $defenderNode->data['faction'], $group['unitId'], 'name');
					$label 			= $group['quantity']. "/" .$data['input']['defender']['groups'][$key]['quantity'];
					$image 			= CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $defenderNode->data['faction'] . '/' . $d13->getUnit($defenderNode->data['faction'], $group['unitId'], 'image');
					
					$vars = array();
					$vars['tvar_listImage'] = $image;
					$vars['tvar_listLabel'] = $name;
					$vars['tvar_listAmount'] = $label;
					
					if ($i == 0) {
						$html .= '<div class="row">';
						$open = true;
					}
					
					$html .= $d13->templateSubpage("msg.combat.entry", $vars);
					
					if ($i == $limit) {
						$html .= '</div>';
						$open = false;
					}
					
					$i++;
				
				}

			} else if ($group['type'] == 'module') {
				// - - - - - Module
				if ($data['input']['defender']['groups'][$key]['input']) {

					$totalAmount 	+= $data['input']['defender']['groups'][$key]['input'];
					$leftAmount		+= $group['input'];
					
					$totalSpeed 	+= $data['input']['defender']['groups'][$key]['speed'];
					$totalArmor 	+= $data['input']['defender']['groups'][$key]['armor'];
					$totalVision	+= $data['input']['defender']['groups'][$key]['vision'];
					$totalStealth 	+= $data['input']['defender']['groups'][$key]['stealth'];
					$totalDamage 	+= $data['input']['defender']['groups'][$key]['damage'];
					
					$name = $d13->getLangGL('units', $defenderNode->data['faction'], $group['unitId'], 'name');
					$label = $group['input']."/".$data['input']['defender']['groups'][$key]['input'];
					$image = CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $defenderNode->data['faction'] . '/' . $d13->getUnit($defenderNode->data['faction'], $group['unitId'], 'image');
					
					$vars = array();
					$vars['tvar_listImage'] = $image;
					$vars['tvar_listLabel'] = $name;
					$vars['tvar_listAmount'] = $label;
					
					if ($i == 0) {
						$html .= '<div class="row">';
						$open = true;
					}
					
					$html .= $d13->templateSubpage("msg.combat.entry", $vars);
					
					if ($i == $limit) {
						$html .= '</div>';
						$open = false;
					}
					
					$i++;
				
				}
			}
		}
		
		if ($open) {
			$html .= '</div>';
		}
		
		if (empty($html)) {
			$html = '<div class="row"><div class="col-auto">' . $d13->getLangUI('none') . '</div></div>';
		}
		
		// - - - - - Army Stats
		$vars = array();
		$vars['tvar_totalAmount'] 	= $totalAmount;
		$vars['tvar_leftAmount'] 	= $leftAmount;
		$vars['tvar_totalSpeed'] 	= $totalSpeed;
		$vars['tvar_totalDamage'] 	= $totalDamage;
		$vars['tvar_totalArmor'] 	= $totalArmor;
		$vars['tvar_totalVision'] 	= $totalVision;
		$vars['tvar_totalStealth'] 	= $totalStealth;
		
		

		if (!$other) {
			$tvars['tvar_msgOtherStats'] = $d13->templateSubpage("msg.combat.stats", $vars);
			$tvars['tvar_msgOtherRowName'] = $defenderNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgOtherRow'] = $html;
		} else {
			$tvars['tvar_msgSelfStats'] = $d13->templateSubpage("msg.combat.stats", $vars);
			$tvars['tvar_msgSelfRowName'] = $defenderNode->data['name']."'s " . $d13->getLangUI('army');
			$tvars['tvar_msgSelfRow'] = $html;
		}

		// - - - - Report Results etc.
		
		$tvars['tvar_msgSelfResRowName'] = $d13->getLangUI("loot") . ' ' . $d13->getLangUI("resource");
		$tvars['tvar_msgSelfResRow'] = '';
		$html = '';
		$i = 0;
		
		if (!$other) {
		
			foreach ($data['output']['attacker']['resources'] as $resource) {
			
				$vars = array();
				$vars['tvar_listImage'] = CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($resource['resource'], 'image');
				$vars['tvar_listLabel'] = $d13->getLangGL('resources', $resource['resource'], 'name');
				$vars['tvar_listAmount'] = '+'.$resource['value'];
				
				if ($i == 0) { $html .= '<div class="row">'; }
		
				$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
				if ($i == $limit) {
					$html .= '</div>';
					$i = 0;
				}
				
				$i++;

			}
		
		
		} else {
			
			foreach ($data['output']['defender']['resources'] as $resource) {
			
				$vars = array();
				$vars['tvar_listImage'] = CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($resource['resource'], 'image');
				$vars['tvar_listLabel'] = $d13->getLangGL('resources', $resource['resource'], 'name');
				$vars['tvar_listAmount'] = '-'.$resource['value'];
				
				if ($i == 0) { $html .= '<div class="row">'; }
		
				$html .= $d13->templateSubpage("msg.combat.resource", $vars);
				
				if ($i == $limit) {
					$html .= '</div>';
					$i = 0;
				}
				
				$i++;
				
			}
		
			
		}
		
		$tvars['tvar_msgSelfResRow'] = $html;
					
		// - - - - - Return Report
		$html = $d13->templateSubpage("msg.combat", $tvars);
		
		$html = str_replace("\r\n", "", $html);
		$html = str_replace("\n", "", $html);
		
		$html = $d13->dbRealEscapeString($html);
		
		return $html;
	}

}

// =====================================================================================EOF

?>