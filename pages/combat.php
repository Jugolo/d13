<?php

//========================================================================================
//
// COMBAT
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//----------------------------------------------------------------------------------------
// PROCESS MODEL
//----------------------------------------------------------------------------------------

global $d13, $ui, $gl, $game;

$message = "";

$d13->db->query('start transaction');

if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action'], $_GET['nodeId'])) {

	$node=new node();
	if ($node->get('id', $_GET['nodeId'])=='done') {
	
  		$flags=$d13->flags->get('name');
  		$node->checkAll(time());
		
		switch ($_GET['action']) {
  
		//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = 
		case 'add':
			if ($flags['combat']) {
				if ($node->checkOptions('combatRaid')) {

					if (isset($_POST['name'], $_POST['attackerGroupUnitIds'], $_POST['attackerGroups'])) {
	 
						foreach ($_POST as $key=>$value) {
							if (!in_array($key, array('name', 'attackerGroupUnitIds', 'attackerGroups', 'attackerFocus'))) {
								$_POST[$key]=misc::clean($value, 'numeric');
							} else if (!in_array($key, array('name', 'attackerFocus'))) {
								$nr = count($_POST[$key]);
								for ($i=0; $i<$nr; $i++) {
									$_POST[$key][$i] = misc::clean($_POST[$key][$i], 'numeric');
								}
							} else {
								$_POST[$key]=misc::clean($value);
							}
						}
			
						$target=new node();
						if ($target->get('name', $_POST['name']) == 'done') {
						$targetUser=new user();
						if ($targetUser->get('id', $target->data['user']) == 'done') {

							$pass = true;
							$alliance = new alliance();
							$targetAlliance = new alliance();
		
							if (($targetAlliance->get('id', $targetUser->data['alliance'])=='done') && ($alliance->get('id', $_SESSION[CONST_PREFIX.'User']['alliance'])=='done')) {
								$war=$alliance->getWar($targetAlliance->data['id']);
								if (isset($war['type'])) {
									$pass = true;
								} else {
									$pass = false;
									$message=$d13->data->ui->get("noWar");
								}
							}
					
							if ($pass) {
								$gotStatic = false;
								$data = array();
								$data['input']['attacker']['focus'] = $_POST['attackerFocus'];
								$data['input']['attacker']['faction'] = $node->data['faction'];
								foreach ($_POST['attackerGroupUnitIds'] as $key=>$unitId) {
									$data['input']['attacker']['groups'][$key]=array('unitId'=>$unitId, 'quantity'=>$_POST['attackerGroups'][$key]);
									if (!$game['units'][$node->data['faction']][$unitId]['speed']) {
										$gotStatic=true;
									}
								}
								if (!$gotStatic) {
									$status=$node->addCombat($target->data['id'], $data);
								} else {
									$status='cannotSendStatic';
								}
								$message=$ui[$status];
							}

						} else {
							$message=$d13->data->ui->get("noUser");
						}
					} else {
						$message=$d13->data->ui->get("noNode");
					}
		
		
		
					}
			
				} else {
					$message=$d13->data->ui->get("accessDenied");
				}
			} else {
				$message=$d13->data->ui->get("featureDisabled");
			}
			break;
   
		//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = 
		case 'cancel':
   		
			if ($node->checkOptions('combatCancel')) {
				if (isset($_GET['combatId'])) {
					$combat=node::getCombat($_GET['combatId']);
					if (isset($combat['id'])) {
						if ($combat['sender']==$node->data['id']) {
							$status=$node->cancelCombat($combat['id']);
							if ($status=='done') {
								header('Location: node.php?action=get&nodeId='.$node->data['id']);
							} else {
								$message=$ui[$status];
							}
						} else {
							$message=$d13->data->ui->get("accessDenied");
						}
					} else {
						$message=$d13->data->ui->get("noCombat");
					}
				} else {
					$message=$d13->data->ui->get("noCombat");
				}
			} else {
				$message=$d13->data->ui->get("accessDenied");
			}
			break;

		//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = 
	  }
  		
	} else {
		$message=$d13->data->ui->get("noNode");
	}
} else {
	$message=$d13->data->ui->get("insufficientData");
}

if ((isset($status)) && ($status=='error')) {
	$d13->db->query('rollback');
} else {
	$d13->db->query('commit');
}

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

$tvars['tvar_unitImagePath'] 	= $_SESSION[CONST_PREFIX.'User']['template'].'/images/units/'.$node->data['faction'];
$tvars['tvar_nodeFaction'] 		= $node->data['faction'];
$tvars['tvar_nodeID'] 			= $node->data['id'];

//- - - - Available Units
$tvars['tvar_units'] = "";
$tvars['tvar_unitsHTML'] = "";
foreach ($gl['units'][$node->data['faction']] as $ukey=>$unit) {
	$tvars['tvar_units'] .= '<option value=\''.$ukey.'\'>'.$unit['name'].'</option>';
}
$tvars['tvar_units'] .= '"';
$factions='';

foreach ($gl['factions'] as $key=>$faction) {
    	$factions.='<option value="'.$key.'">'.$faction['name'].'</option>';
   		$attacker=array('output'=>'', 'outcome'=>'');
   		$defender=array('output'=>'', 'outcome'=>'');
}

$tvars['tvar_unitsHTML'] .= '';
   		
foreach ($node->units as $key=>$unit) {
	if ($unit['value'] > 0) {
		$tvars['tvar_unitName']		= $gl['units'][$node->data['faction']][$key]['name'];
		$tvars['tvar_unitId']		= $key;
		$tvars['tvar_unitAmount']	= $unit['value'];
		$tvars['tvar_unitLevel']	= $unit['level'];    		
		$tvars['tvar_unitsHTML'] 	.= $d13->tpl->parse($d13->tpl->get("sub.combat.unit"), $tvars);
	}
}

$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));


//- - - - Available Enemies
$tvars['tvar_nodeList'] = '<option>...</option>';
$nodes=node::getList($_SESSION[CONST_PREFIX.'User']['id'], TRUE);
foreach ($nodes as $node) {
	$tvars['tvar_nodeList'] .= '<option>'.$node->data['name'].'</option>';
	#$tvars['tvar_nodeList'] .=  '<div><a class="external" href="index.php?p=node&action=get&nodeId='.$node->data['id'].'">'.$node->data['name'].'</a></div>';
}

//- - - - Combat Cost
$tvars['tvar_costData'] = "";
foreach ($game['factions'][$node->data['faction']]['costs']['combat'] as $key=>$cost) {
	$tvars['tvar_costData'] = '<div class="cell">'.$cost['value'].'</div><div class="cell"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl['resources'][$cost['resource']]['name'].'"></div>';
}

//- - - - Template according to map system
if (isset($node->data['id'])) {
	switch ($_GET['action']) {
		case 'add':
			if ($game['options']['gridSystem'] > 0) {
				$page = "combat.add.map";
			} else {
				$page = "combat.add.abstract";
			}
			break;
	}
}

//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->tpl->render_page($page, $tvars);

//=====================================================================================EOF

?>
