<?php

//========================================================================================
//
// SUB.MODULE.RESEARCH
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
// sub_module_research
//----------------------------------------------------------------------------------------
function sub_module_research($node, $module, $mid, $sid, $message) {

	global $d13, $gl, $ui, $game;

	$totalIR = $game['modules'][$node->data['faction']][$mid]['ratio'];
	$demolishData='';
	
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
	$html = '';
	if (count($node->queue['research'])) {
		foreach ($node->queue['research'] as $item) {
			$remaining=$item['start']+$item['duration']*60-time();
			$html .= '<div>'.misc::getlang("research").' '.$gl['technologies'][$node->data['faction']][$item['technology']]["name"].' <span id="research_'.$item['node'].'_'.$item['technology'].'">'.implode(':', misc::sToHMS($remaining)).'</span> <script type="text/javascript">timedJump("research_'.$item['node'].'_'.$item['technology'].'", "?p=module&action=get&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'");</script> <a class="external" href="?p=module&action=cancelTechnology&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'&technologyId='.$item['technology'].'"><i class="f7-icons size-16">close_round</i></a></div>';
		}
	}
	
	// - - - Popover if Queue empty
	if ($html == '') {
		if ($node->modules[$sid]['input'] > 0) {
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">'.misc::getlang("research").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("research").'</a>';
			$html .= '</p>';
		}
	}
	$tvars['tvar_queue'] = $html;
	
	// - - - Research Popup
	$tvars['tvar_sub_popupswiper'] = "";
	
	foreach ($game['technologies'][$node->data['faction']] as $tid=>$technology) {
	
		if ($technology['active'] && in_array($tid, $game['modules'][$node->data['faction']][$mid]['technologies'])) {
			
			//- - - - - Check Cost & Requirements
			$costData='';
			foreach ($technology['cost'] as $key=>$cost) {
				$costData.='<div class="cell"><a class="tooltip-left" data-tooltip="'.$gl["resources"][$cost['resource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></a></div><div class="cell">'.($cost['value']*$game['users']['cost']['research']).'</div>';
			}
			if (!count($technology['requirements'])) {
				$requirementsData=$ui['none'];
			} else {
				$requirementsData='';
				foreach ($technology['requirements'] as $key=>$requirement) {
					$requirementsData.='<div class="cell"><a class="tooltip-left" data-tooltip="'.$gl[$requirement['type']][$node->data['faction']][$requirement['id']]['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$requirement['type'].'/'.$node->data['faction'].'/'.$requirement['id'].'.png" title="'.$ui[$requirement['type']].' - '.$gl[$requirement['type']][$node->data['faction']][$requirement['id']]['name'].'"></a></div><div class="cell">'.$requirement['level'].'</div>';
				}
			}
			
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
			
			//- - - - - Check Permissions
			$linkData='';
			$check_requirements = NULL;
			$check_cost = NULL;
		
 			$check_requirements = $node->checkRequirements($technology['requirements']);
   	    	$check_cost 		= $node->checkCost($technology['cost'], 'research');
      	
    	    if ($check_requirements['ok'] && $check_cost['ok'] && $node->technologies[$tid]['level'] < $technology['maxLevel']) {
    	    	$linkData .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
   	     		$linkData .= '<a href="?p=module&action=addTechnology&nodeId='.$node->data['id'].'&slotId='.$sid.'&technologyId='.$tid.'" class="external button active">'.misc::getlang("research").'</a>';
   	     		$linkData .= '</p>';
        	} else {
        		$linkData .= '<p class="buttons-row theme-gray">';
        		$linkData .= '<a href="#" class="button active">'.misc::getlang("research").'</a>';
        		$linkData .= '</p>';
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
			
			$tvars['tvar_linkData'] 		= $linkData;
			$tvars['tvar_costData'] 		= $costData;
			$tvars['tvar_requirementsData'] = $requirementsData;
			$tvars['tvar_tid'] 				= $tid;
			$tvars['tvar_techName'] 		= $gl['technologies'][$node->data['faction']][$tid]['name'];
			$tvars['tvar_techDescription'] 	= $gl['technologies'][$node->data['faction']][$tid]['description'];
			$tvars['tvar_techTier'] 		= $node->technologies[$tid]['level'];
			$tvars['tvar_techMaxTier'] 		= $technology['maxLevel'];
			$tvars['tvar_techDuration'] 	= misc::time_format((($technology['duration']-$technology['duration']*$totalIR)*$game['users']['speed']['research'])*60);
			$tvars['tvar_sub_popupswiper'] .= $d13->tpl->render_subpage("sub.module.research", $tvars);
			$tvars['tvar_demolishLink'] 		= $demolishData;
			
		}
	}

	$page = "module.get.research";
	
	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.swiper"), $tvars));
	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
	$d13->tpl->render_page($page, $tvars);

}

//=====================================================================================EOF

?>