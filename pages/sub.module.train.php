<?php

//========================================================================================
//
// SUB.MODULE.TRAIN
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
// sub_module_train
//----------------------------------------------------------------------------------------
function sub_module_train($node, $module, $mid, $sid, $message) {

	global $d13, $gl, $ui, $game;
	
	$totalIR = $game['modules'][$node->data['faction']][$mid]['ratio'];
	$html = "";
	
	$tvars = array();
	$tvars['tvar_global_message'] = $message;

	$tvars['tvar_mid'] 					= $mid;
	$tvars['tvar_moduleDescription']	= $gl["modules"][$node->data['faction']][$mid]["description"];
	$tvars['tvar_moduleInput']			= $module['inputResource'];
	$tvars['tvar_moduleInputName']		= $gl["resources"][$module['inputResource']]["name"];
	$tvars['tvar_moduleMaxInput'] 		= $module['maxInput'];
	$tvars['tvar_moduleInputLimit'] 	= floor(min($module['maxInput'], $node->resources[$module['inputResource']]["value"]+$node->modules[$sid]['input']));
	$tvars['tvar_moduleName'] 			= $gl["modules"][$node->data['faction']][$mid]["name"];
	$tvars['tvar_moduleRatio'] 			= $module['ratio'];
	$tvars['tvar_moduleSlotInput'] 		= $node->modules[$sid]['input'];
	$tvars['tvar_nodeFaction'] 			= $node->data['faction'];
	$tvars['tvar_nodeID'] 				= $node->data['id'];
	$tvars['tvar_slotID'] 				= $_GET['slotId'];
	$tvars['tvar_totalIR'] 				= $node->modules[$sid]['input'] * $totalIR;

	// - - - Queue
	if (count($node->queue['train'])) {
		foreach ($node->queue['train'] as $item) {
			if (!$item['stage']) {
				$stage=$ui['train'];
			} else {
				$stage=$ui['remove'];
			}
			$remaining=$item['start']+$item['duration']*60-time();
			$html .= '<div>'.$stage.' '.$item['quantity'].$gl["units"][$node->data['faction']][$item['unit']]["name"].' <span id="train_'.$item['id'].'">'.implode(':', misc::sToHMS($remaining)).'</span><script type="text/javascript">timedJump("train_'.$item['id'].'", "?p=module&action=get&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'");</script> <a class="external link" href="?p=module&action=cancelUnit&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'&trainId='.$item['id'].'"><i class="f7-icons size-16">close_round</i></a></div>';
		}
	}
	
	// - - - Popover if Queue empty
	if ($html == '') {
		if ($node->modules[$sid]['input'] > 0) {
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">'.misc::getlang("train").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("train").'</a>';
			$html .= '</p>';
		}
	}
	$tvars['tvar_queue'] = $html;
	
	//- - - - - Check Node Demolish
	$demolishData='';
	if ($game['options']['moduleDemolish']) {
		if ($node->modules[$sid]['input'] <= 0) {
			#$demolishData .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$demolishData .= '<a class="external button" href="?p=module&action=remove&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'">'.misc::getlang("removeModule").'</a>';
			#$demolishData .= '</p>';
		} else {
			#$demolishData .= '<p class="buttons-row theme-gray>';
			$demolishData .= '<a class="button" href="#">'.misc::getlang("removeModule").'</a>';
			#$demolishData .= '</p>';
		}
	}
	
	// - - - Setup Popup
	$tvars['tvar_sub_popupswiper'] = "";

	foreach ($game['units'][$node->data['faction']] as $uid=>$unit) {
		if (in_array($uid, $game['modules'][$node->data['faction']][$mid]['units'])) {
			
			$unit = new d13_unit($uid, $node);
			
			//- - - - - Assemble Costs
			$get_costs = $unit->getCost();
			$costData = '';
			foreach ($get_costs as $cost) {
				$costData .= '<div class="cell">'.$cost['cost'].'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$cost['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['icon'].'" title="'.$cost['name'].'"></a></div>';
			}
			
			//- - - - - Assemble Requirements
			$get_requirements = $unit->getRequirements();
			if (empty($get_requirements)) {
				$requirementsData = $ui['none'];
			} else {
				$requirementsData = '';
			}
			foreach ($get_requirements as $req) {
				$requirementsData .= '<div class="cell">'.$req['value'].'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$req['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$req['type'].'/'.$node->data['faction'].'/'.$req['icon'].'" title="'.$req['type_name'].' - '.$req['name'].'"></a></div>';
			}

			//- - - - - Check Permissions
			$disableData='';
			
			$check_requirements = $unit->getCheckRequirements();
			$check_cost 		= $unit->getCheckCost();

			if ($check_requirements && $check_cost) {
				$disableData = '';
			} else {
				$disableData = 'disabled';
			}

			if ($check_requirements) {
				$tvars['tvar_requirementsIcon']	= '<i class="f7-icons size-22 color-green">check</i>';
			 } else {
				$tvars['tvar_requirementsIcon']	= '<i class="f7-icons size-22 color-red">close</i>';
			}
			if ($check_cost) {
				$tvars['tvar_costIcon']			= '<i class="f7-icons size-22 color-green">check</i>';
			} else {
				$tvars['tvar_costIcon']			= '<i class="f7-icons size-22 color-red">close</i>';
			}
			
			//- - - - - Check Upgrades
			$upgradeData = array();
			$upgradeData = $unit->getUpgrades();

			$tvars['tvar_unitHPPlus'] 				= "[+".$upgradeData['hp']."]";
			$tvars['tvar_unitDamagePlus'] 			= "[+". $upgradeData['damage']."]";
			$tvars['tvar_unitArmorPlus'] 			= "[+". $upgradeData['armor']."]";
			$tvars['tvar_unitSpeedPlus'] 			= "[+".$upgradeData['speed']."]";
			
			//- - - - - Setup Template Data
			$tvars['tvar_costData'] 				= $costData;
			$tvars['tvar_requirementsData'] 		= $requirementsData;
			$tvars['tvar_disableData'] 				= $disableData;
			$tvars['tvar_uid'] 						= $uid;
			$tvars['tvar_unitName'] 				= $unit->data['name'];
			$tvars['tvar_unitDescription'] 			= $unit->data['description'];
			$tvars['tvar_unitValue'] 				= $node->units[$uid]['value'];
			$tvars['tvar_unitType'] 				= $unit->data['type'];
			$tvars['tvar_unitClass'] 				= $unit->data['class'];
			$tvars['tvar_unitHP'] 					= $unit->data['hp'];
			$tvars['tvar_unitDamage'] 				= $unit->data['damage'];
			$tvars['tvar_unitArmor'] 				= $unit->data['armor'];
			$tvars['tvar_unitSpeed'] 				= $unit->data['speed'];
			$tvars['tvar_unitLimit'] 				= $unit->getMaxProduction();
			$tvars['tvar_unitDuration'] 			= misc::time_format((($unit->data['duration'] - $unit->data['duration'] * $totalIR) * $game['users']['speed']['train']) * 60);
			$tvars['tvar_unitUpkeep'] 				= $unit->data['upkeep'];
			$tvars['tvar_unitUpkeepResource'] 		= $unit->data['upkeepResource'];
			$tvars['tvar_unitUpkeepResourceName']	= $gl['resources'][$unit->data['upkeepResource']]['name'];
			$tvars['tvar_demolishLink'] 			= $demolishData;
			$tvars['tvar_sub_popupswiper'] 			.= $d13->tpl->render_subpage("sub.module.train", $tvars);
				
		}
	}
	
	// - - - Setup Template
	$page = "module.get.train";

	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.swiper"), $tvars));
	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
	$d13->tpl->render_page($page, $tvars);

}

//=====================================================================================EOF

?>