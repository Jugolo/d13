<?php

// ========================================================================================
//
// COMBAT.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_combatController extends d13_controller
{
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
		parent::__construct($d13);
		$tvars = array();
		
		
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);

	}

	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function doControl()
	{
	
		
		
		$tvars = array();
		
		switch ($_GET['action']) {
		
			case 'add':
				$tvars = $this->doCombatAdd();
				break;
			
			case 'cancel':
				$tvars = $this->doCombatCancel();
				break;
		
		}
		
		return $tvars;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// doCombatAdd
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doCombatAdd()
	{
		
		
		
		$tvars = array();
	
		//====================================================== ASSEMBLE ATTACK ARMY AND START COMBAT
		
		if (isset($_GET['type'], $_GET['slotId'])) {
		
			$pass = false;
			
			// - - - - Check if Node allows this combat type
			if ($this->d13->node->checkOptions($_GET['type'])) {
				$pass = true;
			} else {
				$message = $this->d13->getLangUI("featureDisabled");
			}
			
			if ($pass && isset($_POST['type'], $_POST['id'], $_POST['attackerGroupUnitIds'], $_POST['attackerGroups'])) {
				
				$target = new d13_node();
				if ($target->get('id', $_POST['id']) == 'done') {
					
					// - - - - Check Alliance Status
					$targetUser = $this->d13->createObject('user');
					if ($targetUser->get('id', $target->data['user']) == 'done') {
						$pass = true;
						$alliance = new d13_alliance();
						$targetAlliance = new d13_alliance();
						if (($targetAlliance->get('id', $targetUser->data['alliance']) == 'done') && ($alliance->get('id', $_SESSION[CONST_PREFIX . 'User']['alliance']) == 'done')) {
							$war = $alliance->getWar($targetAlliance->data['id']);
							if (isset($war['type'])) {
								$pass = true;
							}
							else {
								$pass = false;
								$message = $this->d13->getLangUI("noWar");
							}
						}

						if ($pass) {
						
							$gotNoArmy = true;
							$gotIllegal = false;
							$gotLeader = false;
							$gotLimits = array();
							
							foreach ($this->d13->getGeneral('types') as $key => $type) {
								$gotLimits[$key] = 0;
							}
							
							$data = array();
							$data['input']['attacker']['focus'] = $this->d13->node->data['focus'] ;
							$data['input']['attacker']['faction'] = $this->d13->node->data['faction'];
							
							// - - - - Check for static, multiple leaders and limited units
							
							foreach($_POST['attackerGroupUnitIds'] as $key => $unitId) {
								$data['input']['attacker']['groups'][$key] = array(
									'unitId' => $unitId,
									'quantity' => $_POST['attackerGroups'][$key]
								);
								
								if ($_POST['attackerGroups'][$key] > 0) {
									$gotNoArmy = false;
								}
								
								
								if (!$this->d13->getUnit($this->d13->node->data['faction'], $unitId, 'speed')) {
									$gotIllegal = true;
								}
								if (!in_array($this->d13->getUnit($this->d13->node->data['faction'], $key, 'movementType'), $this->d13->getCombat($_GET['type'], 'movementTypes') )) {
									$gotIllegal = true;
								}
								
								$type = $this->d13->getUnit($this->d13->node->data['faction'], $unitId, 'type');
								
								if ($this->d13->getGeneral('types', $type, 'unique')) {
									$gotLimits[$type] += $_POST['attackerGroups'][$key];
								}
								
							}
							
							$pass = true;
							
							if ($gotIllegal || $gotNoArmy) {
								$pass = false;
							}
							
							foreach ($gotLimits as $key => $limit) {
								if ($limit > $this->d13->getGeneral('types', $key, 'limit')) {
									$pass = false;
								}
								if ($key == 'leader' && $limit == 1) {
									$gotLeader = true;
								}
							}
							
							if (!$gotLeader && $this->d13->getCombat($_GET['type'], 'requiresLeader')) {
								$pass = false;
							}
							
							if ($pass) {
								$status = $this->d13->node->addCombat($target->data['id'], $data, $_GET['type'], $_GET['slotId']);
								header("location: ?p=node&action=list&nodeId=0");
							} else {
								$status = 'error';
							}
							$message = $this->d13->getLangUI($status);
							
						}
					} else {
						$message = $this->d13->getLangUI("noUser");
					}
				} else {
					$message = $this->d13->getLangUI("noNode");
				}
			}
			
		}
		
		//----
		

		$tvars['tvar_unitImagePath'] 	= $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->d13->node->data['faction'];
		$tvars['tvar_nodeFaction'] 		= $this->d13->node->data['faction'];
		$tvars['tvar_nodeID'] 			= $this->d13->node->data['id'];
		$tvars['tvar_combatType'] 		= $_GET['type'];

		if (isset($_GET['type'])) {
			$tvars['tvar_type'] 		= $_GET['type'];
		}

		if (isset($_GET['slotId'])) {
			$tvars['tvar_slotId'] 		= $_GET['slotId'];
		}

		//====================================================== BUILD AVAILABLE UNITS LIST

		$tvars['tvar_units'] = "";
		$tvars['tvar_unitsHTML'] = "";

		foreach($this->d13->node->units as $key => $unit) {
			if ($unit['value'] > 0 && $this->d13->getUnit($this->d13->node->data['faction'], $key, 'speed') > 0) {
				if ( in_array($this->d13->getUnit($this->d13->node->data['faction'], $key, 'movementType'), $this->d13->getCombat($_GET['type'], 'movementTypes') )) {
					
					$id = $this->d13->getUnit($this->d13->node->data['faction'], $key, 'id');
					
					$args = array();
					$args['supertype'] = 'unit';
					$args['id'] = $id;
					
					$tmp_unit = $this->d13->createGameObject($args, $this->d13->node);
					
					$this->d13->templateInject($this->d13->templateSubpage("sub.popup.unit", $tmp_unit->getTemplateVariables()));
	
					$tvars['tvar_unitName'] 	= $this->d13->getLangGL('units', $this->d13->node->data['faction'], $id, 'name');
					$tvars['tvar_unitId'] 		= $id;
					$tvars['tvar_unitImage']	= $tmp_unit->data['image'];
					$tvars['tvar_unitType'] 	= $tmp_unit->data['type'];
					$tvars['tvar_unitUnique'] 	= (int)$this->d13->getGeneral('types', $tmp_unit->data['type'], 'unique');
					$tvars['tvar_unitAmount'] 	= min($unit['value'], $this->d13->getGeneral('types', $tmp_unit->data['type'], 'limit'));			
					$tvars['tvar_unitFuel'] 	= $tmp_unit->data['fuel'];
		
					$tvars['tvar_unitdamage'] 	= $tmp_unit->data['damage'] + $tmp_unit->data['upgrade_damage'];
					$tvars['tvar_unitspeed'] 	= $tmp_unit->data['speed'] + $tmp_unit->data['upgrade_speed'];
					$tvars['tvar_unitstealth'] 	= $tmp_unit->data['stealth'] + $tmp_unit->data['upgrade_stealth'];
					$tvars['tvar_unithp'] 		= $tmp_unit->data['hp'] + $tmp_unit->data['upgrade_hp'];
					$tvars['tvar_unitarmor'] 	= $tmp_unit->data['armor'] + $tmp_unit->data['upgrade_armor'];
					$tvars['tvar_unitcritical'] = $tmp_unit->data['critical'] + $tmp_unit->data['upgrade_critical'];
					$tvars['tvar_unitcapacity'] = $tmp_unit->data['capacity'] + $tmp_unit->data['upgrade_capacity'];
			
					foreach ($tmp_unit->data['attackModifier'] as $modifier) {
						$tvars['tvar_unit'.$modifier['stat']] += floor($this->d13->getUnit($this->d13->node->data['faction'], $id, $modifier['stat']) * $modifier['value']);
					}
		
					$modifiers = array();
					foreach ($this->d13->getGeneral('stats') as $stat) {
						$modifiers[$stat] = 0;
					}
		
					foreach ($tmp_unit->data['armyAttackModifier'] as $modifier) {
						$modifiers[$modifier['stat']] += $modifier['value'];
					}
		
					foreach ($this->d13->getGeneral('stats') as $stat) {
						$tvars['tvar_armyMod'.$stat] = $modifiers[$stat];
					}
		
					$tvars['tvar_unitsHTML']	.= $this->d13->templateSubpage("sub.combat.unit", $tvars);
	
				}
			}
		}

		$this->d13->templateInject($this->d13->templateSubpage("sub.swiper.horizontal", $tvars));

		//====================================================== BUILD AVAILABLE ENEMIES LIST
		
		$showAll = true;
		$tvars['tvar_nodeList'] = '<option disabled>...</option>';
		$target_nodes = $this->d13->getNodeList($_SESSION[CONST_PREFIX . 'User']['id'], TRUE);
		$this->d13->node->getLocation();

		foreach($target_nodes as $target_node) {
			$disabled = '';
			$text = '';
			$target_node->getLocation();
			$distance = ceil(sqrt(pow(abs($this->d13->node->location['x'] - $target_node->location['x']) , 2) + pow(abs($this->d13->node->location['y'] - $target_node->location['y']) , 2)));
	
			if ($target_node->getShield($_GET['type'])) {
				$disabled = 'disabled';
				$text = ' (shielded)';
			}
	
			if ($showAll || empty($disabled))  {
				$tvars['tvar_nodeList'].= '<option value="'.$target_node->data['id'].'" '.$disabled.'>' . $target_node->data['name'] . ' [' . $distance . $this->d13->getLangUI('miles') . '] ' .$text . '</option>';
			}
		}

		// - - - - Is a leader required?
		$tvars['tvar_leaderRequired'] = '';
		$tvars['tvar_leader'] = 0;
		if ($this->d13->getCombat($_GET['type'], 'requiresLeader')) {
			$tvars['tvar_leaderRequired'] = $this->d13->templateGet("sub.combat.leader");
			$tvars['tvar_leader'] = 1;
		}
					
		$tvars['tvar_wipeoutRequired'] = '';						
		if ($this->d13->getCombat($_GET['type'], 'requiresWipeout')) {
			$tvars['tvar_wipeoutRequired'] = $this->d13->templateGet("sub.combat.wipeout");
		}
					
		// - - - - Combat Cost & Fuel
		$cost = $this->d13->getFaction($this->d13->node->data['faction'], 'costs', $_GET['type']);
		$tvars['tvar_costData'] = '';
		$tvars['tvar_resources'] = '';

		foreach ($cost as $res) {

			if (isset($res['resource'])) {
		
				$idv = "";
				$idr = "";
		
				$resource = $res['resource'];
				$cost =  $res['value'];
		
				if (isset($res['isFuel']) && $res['isFuel']) {
					$idv = "availableFuel";
					$idr = "totalFuel";
					$cost = 0;
					$tvars['tvar_fuelFactor'] = floor($res['value']);
					$tvars['tvar_fuelResource'] = floor($this->d13->node->resources[$res['resource']]['value']);
				}
		
				$tvars['tvar_resources'] .= '<input type="hidden" name="availableRes[]" id="'.$idv.'" value="'.floor($this->d13->node->resources[$resource]['value']).'">';
				$tvars['tvar_costData'] .=  '<span class="badge"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $this->d13->getResource($resource, 'icon') . '" title="' . $this->d13->getLangGL('resources', $resource, 'name') . '"><span name="totalRes[]" id="'.$idr.'">'.$cost . '</span></span>';

			} else if (isset($res['component'])) {
		
				$idv = "";
				$idr = "";
				
				$args = array();
				$args['supertype'] 	= 'component';
				$args['id'] 		= $res['component'];
				
				$tmp_component = new d13_gameobject_component($args, $this->d13->node);
		
				$resource = $res['component'];
				$cost =  $res['value'];
		
				if (isset($res['isFuel']) && $res['isFuel']) {
					$idv = "availableFuel";
					$idr = "totalFuel";
					$cost = 0;
					$tvars['tvar_fuelFactor'] = floor($res['value']);
					$tvars['tvar_fuelResource'] = floor($this->d13->node->components[$res['resource']]['value']);
				}
		
				$tvars['tvar_resources'] .= '<input type="hidden" name="availableRes[]" id="'.$idv.'" value="'.floor($this->d13->node->components[$resource]['value']).'">';
				$tvars['tvar_costData'] .=  '<span class="badge"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->d13->node->data['faction'] . "/" . $tmp_component->data['image'] . '" title="' . $tmp_component->data['name'] . '"><span name="totalRes[]" id="'.$idr.'">'.$cost . '</span></span>';

			}
	
		}

		$tvars['tvar_page'] = "combat.add";

		return $tvars;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// doCombatCancel
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doCombatCancel()
	{
		
		$tvars = array();
		
		if (isset($_GET['combatId'])) {
			$combat = $this->d13->node->getCombat($_GET['combatId']);
			if (isset($combat['id'])) {
				if ($combat['sender'] == $this->d13->node->data['id']) {
					$status = $this->d13->node->cancelCombat($combat['id']);
					if ($status == 'done') {
						header('Location: ?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
						exit();
					} else {
						$message = $this->d13->getLangUI($status);
					}
				} else {
					$message = $this->d13->getLangUI("accessDenied");
				}
			} else {
				$message = $this->d13->getLangUI("noCombat");
			}
		} else {
			$message = $this->d13->getLangUI("noCombat");
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		
		
		$this->d13->outputPage($tvars['tvar_page'], $tvars);
		
	}

}

// =====================================================================================EO