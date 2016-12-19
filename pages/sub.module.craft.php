<?php

//========================================================================================
//
// SUB.MODULE.CRAFT
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
// sub_module_craft
//----------------------------------------------------------------------------------------
function sub_module_craft($node, $module, $mid, $sid, $message) {

	global $d13, $gl, $ui, $game;
	
	$totalIR = $game['modules'][$node->data['faction']][$mid]['ratio'];
	$html = "";
	$demolishData='';
	
	$tvars = array();
	$tvars['tvar_global_message'] = $message;

	$tvars['tvar_mid'] 					= $mid;
	$tvars['tvar_moduleDescription']	= $gl["modules"][$node->data['faction']][$mid]["description"];
	$tvars['tvar_moduleInput']			= $module['inputResource'];
	$tvars['tvar_moduleInputName']		= $gl["resources"][$module['inputResource']]["name"];
	$tvars['tvar_moduleMaxInput'] 		= $module['maxInput'];
	$tvars['tvar_moduleInputLimit'] 	= floor(min($module['maxInput'], $node->resources[$module['inputResource']]['value']+$node->modules[$sid]['input']));
	$tvars['tvar_moduleName'] 			= $gl["modules"][$node->data['faction']][$mid]["name"];
	$tvars['tvar_moduleRatio'] 			= $module['ratio'];
	$tvars['tvar_moduleSlotInput'] 		= $node->modules[$sid]['input'];
	$tvars['tvar_nodeFaction'] 			= $node->data['faction'];
	$tvars['tvar_nodeID'] 				= $node->data['id'];
	$tvars['tvar_slotID'] 				= $_GET['slotId'];
	$tvars['tvar_totalIR'] 				= $node->modules[$sid]['input'] * $totalIR;
	
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
		foreach ($node->components as $uid=>$unit) {
			if (in_array($uid, $game['modules'][$node->data['faction']][$mid]['components'])) {
				if ($unit['value'] > 0) {
					$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/components/'.$node->data['faction'].'/'.$uid.'.png" title="'.$gl['components'][$node->data['faction']][$uid]['name'].'">';
					$tvars['tvar_listLabel'] 		= $gl['components'][$node->data['faction']][$uid]['name'];
					$tvars['tvar_listAmount'] 		= $unit['value'];
					$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
				}
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
				
	// - - - Queue
	if (count($node->queue['craft'])) {
		$html = '';
		foreach ($node->queue['craft'] as $item) {
			if (!$item['stage']) {
				$stage=$ui['craft'];
			} else {
				$stage=$ui['remove'];
			}
			$remaining=$item['start']+$item['duration']*60-time();
			$html .= '<div>'.$stage.' '.$item['quantity'].' '.$gl["components"][$node->data['faction']][$item['component']]["name"].'(s) <span id="craft_'.$item['id'].'">'.implode(':', misc::sToHMS($remaining)).'</span><script type="text/javascript">timedJump("craft_'.$item['id'].'", "?p=module&action=get&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'");</script> <a class="external" href="?p=module&action=cancelComponent&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'&craftId='.$item['id'].'"><i class="f7-icons size-16">close_round</i></a></div>';
		}
	}
	// - - - Popover if Queue empty
	if ($html == '') {
		if ($node->modules[$sid]['input'] > 0) {
			#$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">'.misc::getlang("craft").'</a>';
			#$html .= '</p>';
		} else {
			#$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("craft").'</a>';
			#$html .= '</p>';
		}
	}
	$tvars['tvar_queue'] = $html;

	// - - - Craft Popup
	$tvars['tvar_sub_popupswiper'] = "";

	foreach ($game['components'][$node->data['faction']] as $cid=>$component) {
		if (in_array($cid, $game['modules'][$node->data['faction']][$mid]['components'])) {
			$costData='';
			foreach ($component['cost'] as $key=>$cost) {
				$costData.='<div class="cell">'.($cost['value']*$game['users']['cost']['train']).'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$gl["resources"][$cost['resource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></a></div>';
			}
			if (!count($component['requirements'])) {
				$requirementsData=$ui['none'];
			} else {
				$requirementsData='';
				foreach ($component['requirements'] as $key=>$requirement) {
					$requirementsData.='<div class="cell">'.$requirement['value'].'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$gl[$requirement['type']][$node->data['faction']][$requirement['id']]['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$requirement['type'].'/'.$node->data['faction'].'/'.$requirement['id'].'.png" title="'.$ui[$requirement['type']].' - '.$gl[$requirement['type']][$node->data['faction']][$requirement['id']]['name'].'"></a></div>';
				}
			}
			
			// - - - Check Affordable Maximum
			$costLimit 	= $node->checkCostMax($component['cost'], 'craft');
			$reqLimit 	= $node->checkRequirementsMax($component['requirements']);
			$upkeepLimit = floor($node->resources[$game['components'][$node->data['faction']][$cid]['storageResource']]['value'] / $game['components'][$node->data['faction']][$cid]['storage']);
			$unitLimit = abs($node->components[$cid]['value'] - $game['types'][$component['type']]['limit']);
			$limitData = min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);
			$limitData = floor($limitData);
			
			//- - - - - Check Permissions
			$disableData='';
			$check_requirements = NULL;
			$check_cost = NULL;
		
 			$check_requirements = $node->checkRequirements($component['requirements']);
   	    	$check_cost 		= $node->checkCost($component['cost'], 'research');
      	
    	    if ($check_requirements['ok'] && $check_cost['ok']) {
   	     		$disableData = '';
        	} else {
        		$disableData = 'disabled';
        	}
        	
			if ($check_requirements['ok']) {
		 		$tvars['tvar_requirementsIcon']		= '<i class="f7-icons size-22 color-green">check</i>';
			 } else {
		 		$tvars['tvar_requirementsIcon']		= '<i class="f7-icons size-22 color-red">close</i>';
		 	}
		 	if ($check_cost['ok']) {
				$tvars['tvar_costIcon']		= '<i class="f7-icons size-22 color-green">check</i>';
		 	} else {
		 		$tvars['tvar_costIcon']		= '<i class="f7-icons size-22 color-red">close</i>';
		 	}
			
			$tvars['tvar_inventoryLink'] 		= $inventoryData;
			$tvars['tvar_costData'] 			= $costData;
			$tvars['tvar_requirementsData'] 	= $requirementsData;
			$tvars['tvar_disableData'] 			= $disableData;
			$tvars['tvar_cid'] 					= $cid;
			$tvars['tvar_componentName'] 		= $gl["components"][$node->data['faction']][$cid]["name"];
			$tvars['tvar_componentDescription'] = $gl["components"][$node->data['faction']][$cid]["description"];
			$tvars['tvar_duration'] 			= misc::time_format((($component['duration']-$component['duration']*$totalIR)*$game['users']['speed']['craft'])*60);
			$tvars['tvar_compLimit'] 			= $limitData;
			$tvars['tvar_compValue'] 			= $node->components[$cid]['value'];
			$tvars['tvar_compStorage'] 			= $component['storage'];
			$tvars['tvar_compResource'] 		= $component['storageResource'];
			$tvars['tvar_compResourceName'] 	= $gl["resources"][$component['storageResource']]["name"];
			$tvars['tvar_sub_popupswiper'] 		.=  $d13->tpl->render_subpage("sub.module.craft", $tvars);
			$tvars['tvar_demolishLink'] 		= $demolishData;
			
		}
	}
	
	$page = "module.get.craft";
	
	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.swiper"), $tvars));
	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
	$d13->tpl->render_page($page, $tvars);

}

//=====================================================================================EOF

?>