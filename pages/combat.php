<?php

// ========================================================================================
//
// COMBAT
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
// PROCESS MODEL
// ----------------------------------------------------------------------------------------

global $d13;
$message = "";
$d13->dbQuery('start transaction');

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'], $_GET['nodeId'])) {
	$node = new node();
	if ($node->get('id', $_GET['nodeId']) == 'done') {
		$flags = $d13->flags->get('name');
		$node->checkAll(time());
		
		switch ($_GET['action']) {

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'add':
			
			if (isset($_GET['type'], $_GET['slotId'])) {
			
				$pass = false;
				if ($_GET['type'] == 'scout' && $node->checkOptions('combatScout')) {
					$pass = true;
				} else if ($_GET['type'] == 'raid' && $node->checkOptions('combatRaid')) {
					$pass = true;
				} else if ($_GET['type'] == 'conquer' && $node->checkOptions('combatConquer')) {
					$pass = true;
				} else if ($_GET['type'] == 'skirmish' && $node->checkOptions('combatSkirmish')) {
					$pass = true;
				} else if ($_GET['type'] == 'sabotage' && $node->checkOptions('combatSabotage')) {
					$pass = true;
				} else if ($_GET['type'] == 'raze' && $node->checkOptions('combatRaze')) {
					$pass = true;	
				} else {
					$message = $d13->getLangUI("featureDisabled");
				}
				
				if ($pass && isset($_POST['type'], $_POST['id'], $_POST['attackerGroupUnitIds'], $_POST['attackerGroups'])) {
					
					$target = new node();
					if ($target->get('id', $_POST['id']) == 'done') {
						$targetUser = new user();
						if ($targetUser->get('id', $target->data['user']) == 'done') {
							$pass = true;
							$alliance = new alliance();
							$targetAlliance = new alliance();
							if (($targetAlliance->get('id', $targetUser->data['alliance']) == 'done') && ($alliance->get('id', $_SESSION[CONST_PREFIX . 'User']['alliance']) == 'done')) {
								$war = $alliance->getWar($targetAlliance->data['id']);
								if (isset($war['type'])) {
									$pass = true;
								}
								else {
									$pass = false;
									$message = $d13->getLangUI("noWar");
								}
							}

							if ($pass) {
								$gotStatic = false;
								$data = array();
								$data['input']['attacker']['focus'] = $node->data['focus'] ;
								$data['input']['attacker']['faction'] = $node->data['faction'];
								foreach($_POST['attackerGroupUnitIds'] as $key => $unitId) {
									$data['input']['attacker']['groups'][$key] = array(
										'unitId' => $unitId,
										'quantity' => $_POST['attackerGroups'][$key]
									);
									if (!$d13->getUnit($node->data['faction'], $unitId, 'speed')) {
										$gotStatic = true;
									}
								}

								if (!$gotStatic) {
									$status = $node->addCombat($target->data['id'], $data, $_GET['type'], $_GET['slotId']);
									header("location: ?p=node&action=list&nodeId=0");
								} else {
									$status = 'cannotSendStatic';
								}

								$message = $d13->getLangUI($status);
							}
						}
						else {
							$message = $d13->getLangUI("noUser");
						}
					}
					else {
						$message = $d13->getLangUI("noNode");
					}
				}
				
			}
			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		case 'cancel':
			if (isset($_GET['combatId'])) {
				$combat = node::getCombat($_GET['combatId']);
				if (isset($combat['id'])) {
					if ($combat['sender'] == $node->data['id']) {
						$status = $node->cancelCombat($combat['id']);
						if ($status == 'done') {
							header('Location: ?p=node&action=get&nodeId=' . $node->data['id']);
						}
						else {
							$message = $d13->getLangUI($status);
						}
					}
					else {
						$message = $d13->getLangUI("accessDenied");
					}
				}
				else {
					$message = $d13->getLangUI("noCombat");
				}
			}
			else {
				$message = $d13->getLangUI("noCombat");
			}
			

			break;

			// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =

		}
	}
	else {
		$message = $d13->getLangUI("noNode");
	}
}
else {
	$message = $d13->getLangUI("insufficientData");
}

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;


if (isset($node)) {

	$tvars['tvar_unitImagePath'] 	= $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $node->data['faction'];
	$tvars['tvar_nodeFaction'] 		= $node->data['faction'];
	$tvars['tvar_nodeID'] 			= $node->data['id'];
	$tvars['tvar_combatType'] 		= $_GET['type'];
	
	if (isset($_GET['type'])) {
		$tvars['tvar_type'] 		= $_GET['type'];
	}
	
	if (isset($_GET['slotId'])) {
		$tvars['tvar_slotId'] 		= $_GET['slotId'];
	}

	// - - - - Available Units

	$tvars['tvar_units'] = "";
	$tvars['tvar_unitsHTML'] = "";

	foreach($node->units as $key => $unit) {
		if ($unit['value'] > 0 && $d13->getUnit($node->data['faction'], $key, 'speed') > 0) {
			
			$id = $d13->getUnit($node->data['faction'], $key, 'id');
			$tmp_unit = new d13_unit($id, $node);
		
			$d13->templateInject($d13->templateSubpage("sub.popup.unit", $tmp_unit->getTemplateVariables()));
		
			$tvars['tvar_unitName'] = $d13->getLangGL('units', $node->data['faction'], $id) ['name'];
			$tvars['tvar_unitId'] = $id;
			$tvars['tvar_unitAmount'] = $unit['value'];
			$tvars['tvar_unitLevel'] = $unit['level'];
			$tvars['tvar_unitsHTML'].= $d13->templateParse($d13->templateGet("sub.combat.unit") , $tvars);
		}
	}

	$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));

	// - - - - Available Enemies
	$tvars['tvar_nodeList'] = '<option>...</option>';
	$nodes = node::getList($_SESSION[CONST_PREFIX . 'User']['id'], TRUE);
	foreach($nodes as $node) {
		$tvars['tvar_nodeList'].= '<option value="'.$node->data['id'].'">' . $node->data['name'] . '</option>';
	}

	// - - - - Combat Cost
	$cost = $d13->getGeneral('factions', $node->data['faction'], 'costs', $_GET['type']);
	$tvars['tvar_costData'] = '';
	foreach ($cost as $res) {
		$resource = $res['resource'];
		$cost =  $res['value'];
		$tvars['tvar_costData'] .=  '<span class="badge"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $d13->getResource($resource, 'image') . '" title="' . $d13->getLangGL('resources', $resource, 'name') . '">'.$cost . '</span>';
	}
	
	// - - - - Template according to map system
	if (isset($node->data['id'])) {
		switch ($_GET['action']) {
		case 'add':
			if ($d13->getGeneral('options', 'gridSystem') > 0) {
				$page = "combat.add.map";
			}
			else {
				$page = "combat.add.abstract";
			}

			break;
		}
	}

}

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender($page, $tvars);

// =====================================================================================EOF

?>