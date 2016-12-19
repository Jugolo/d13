<?php

//========================================================================================
//
// SUB.MODULE.STORAGE
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
// sub_module_storage
//----------------------------------------------------------------------------------------
function sub_module_storage($node, $module, $mid, $sid, $message) {

	global $d13, $gl, $ui, $game;
	
	$totalIR = $game['modules'][$node->data['faction']][$mid]['ratio'];
	
	$tvars = array();
	$tvars['tvar_global_message'] = $message;
	
	//- - - - - Check Demolish
	$demolishData='';
	if ($game['options']['moduleDemolish']) {
		if ($node->modules[$sid]['input'] <= 0) {
			$demolishData .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$demolishData .= '<a class="external button" href="?p=module&action=remove&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'">'.misc::getlang("removeModule").'</a>';
			$demolishData .= '</p>';
		} else {
			$demolishData .= '<p class="buttons-row theme-gray>';
			$demolishData .= '<a class="button" href="#">'.misc::getlang("removeModule").'</a>';
			$demolishData .= '</p>';
		}
	}
	
	//- - - - - Check Inventory
	$inventoryData = '';
	$tvars['tvar_sub_popuplist'] = '';
	
	if ($module['options']['inventoryList']) {
		//- - - - - Popover if Inventory filled
		foreach ($node->resources as $uid=>$unit) {
			if ($unit['value'] > 0) {
					$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$uid.'.png" title="'.$gl['resources'][$uid]['name'].'">';
					$tvars['tvar_listLabel'] 		= $gl['resources'][$uid]['name'];
					$tvars['tvar_listAmount'] 		= floor($unit['value']);
					$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
			}
		}
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));
	
		$inventoryData .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
		$inventoryData .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
		$inventoryData .= '</p>';
	} else {
		$inventoryData .= '<p class="buttons-row theme-gray">';
		$inventoryData .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
		$inventoryData .= '</p>';
	}
	
	$tvars['tvar_inventoryLink'] 		= $inventoryData;
	$tvars['tvar_demolishLink'] 		= $demolishData;
	$tvars['tvar_mid'] 					= $mid;
	$tvars['tvar_moduleDescription']	= $gl["modules"][$node->data['faction']][$mid]["description"];
	$tvars['tvar_moduleInput']			= $module['inputResource'];
	$tvars['tvar_moduleInputLimit'] 	= floor(min($module['maxInput'], $node->resources[$module['inputResource']]["value"]+$node->modules[$sid]['input']));
	$tvars['tvar_moduleInputName']		= $gl["resources"][$module['inputResource']]["name"];
	$tvars['tvar_moduleMaxInput'] 		= $module['maxInput'];
	$tvars['tvar_moduleName'] 			= $gl["modules"][$node->data['faction']][$mid]["name"];
	$tvars['tvar_moduleRatio'] 			= $module['ratio'];
	$tvars['tvar_moduleSlotInput'] 		= $node->modules[$sid]['input'];
	$tvars['tvar_moduleStorage'] 		= $module['ratio']*$game['factors']['storage']*$node->modules[$sid]['input'];
	$tvars['tvar_nodeFaction'] 			= $node->data['faction'];
	$tvars['tvar_nodeID'] 				= $node->data['id'];
	$tvars['tvar_slotID'] 				= $_GET['slotId'];

	$i=0;
	foreach ($module['storedResource'] as $res) {
		$tvars['tvar_moduleStorageRes'.$i]		= $res;
		$tvars['tvar_moduleStorageResName'.$i]	= $gl["resources"][$res]["name"];
		$i++;
	}
	

	$page = "module.get.storage";
	$d13->tpl->render_page($page, $tvars);

}

//=====================================================================================EOF

?>