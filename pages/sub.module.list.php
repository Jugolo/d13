<?php

//========================================================================================
//
// SUB.MODULE.LIST
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
// sub_module_list
//----------------------------------------------------------------------------------------

function sub_module_list($node, $module_data, $message) {

global $d13, $ui, $gl, $game;

$tvars = array();
$tvars['tvar_global_message'] = $message;

$tvars['tvar_sub_list'] = "";

if (isset($node->modules[$_GET['slotId']])) {

	foreach ($game['modules'][$node->data['faction']] as $mid=>$module_data) {

		//- - - - - Check Permissions
		$linkData='';
		$check_requirements = NULL;
		$check_cost = NULL;
		
 		$check_requirements = $node->checkRequirements($module_data['requirements']);
        $check_cost 		= $node->checkCost($module_data['cost'], 'build');
      	
        if ($check_requirements['ok'] && $check_cost['ok'] && ($node->getModuleCount($_GET['slotId'], $mid) < $game['modules'][$node->data['faction']][$mid]['maxInstances'])) {
        	$linkData .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
        	$linkData .= '<a class="external button button-big active" href="index.php?p=module&action=add&nodeId='.$node->data['id'].'&slotId='.$_GET['slotId'].'&moduleId='.$mid.'">'.misc::getlang("addModule").'</a>';
        	$linkData .= '</p>';
        } else {
        	$linkData .= '<p class="buttons-row">';
        	$linkData .= '<a class="button button-big active color-gray" href="#">'.misc::getlang("addModule").'</a>';
        	$linkData .= '</p>';
        }
        
		 //- - - - - Cost List
		 $costData='';
		 foreach ($module_data['cost'] as $key=>$cost) {
			$costData.='<div class="cell"><a class="tooltip-left" data-tooltip="'.$gl["resources"][$cost['resource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></a></div><div class="cell">'.($cost['value']*$game['users']['cost']['train']).'</div>';
		 }
		 
		 //- - - - - Requirements List
		 if (!count($module_data['requirements'])) {
			$requirementsData=$ui['none'];
		 } else {
			$requirementsData='';
			foreach ($module_data['requirements'] as $key=>$requirement) {
				if (isset($requirement['level'])) {
						$value = $requirement['level'];
					} else {
						$value = $requirement['value'];
					}
				$requirementsData.='<div class="cell">'.$value.'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$gl[$requirement['type']][$node->data['faction']][$requirement['id']]['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$requirement['type'].'/'.$node->data['faction'].'/'.$requirement['id'].'.png" title="'.$ui[$requirement['type']].' - '.$gl[$requirement['type']][$node->data['faction']][$requirement['id']]['name'].'"></a></div>';
			}
		 }
		 
		 //- - - - - 
		 $outputData='';
		 switch ($module_data['type']) {
			  case 'harvest':
				$outputData='<a class="tooltip-left" data-tooltip="'.$gl["resources"][$module_data['outputResource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$module_data['outputResource'].'.png" title="'.$gl["resources"][$module_data['outputResource']]["name"].'"></a>';
				break;

			  case 'research':
				foreach ($module_data['technologies'] as $technology) {
					$outputData.='<a class="tooltip-left" data-tooltip="'.$gl["technologies"][$node->data['faction']][$technology]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/technologies/'.$node->data['faction'].'/'.$technology.'.png" title="'.$gl["technologies"][$node->data['faction']][$technology]["name"].'"></a>';
				}
				break;

			  case 'craft':
				foreach ($module_data['components'] as $component) {
					$outputData.='<a class="tooltip-left" data-tooltip="'.$gl["components"][$node->data['faction']][$component]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/components/'.$node->data['faction'].'/'.$component.'.png" title="'.$gl["components"][$node->data['faction']][$component]["name"].'"></a>';
				}
				break;

			  case 'train':
				foreach ($module_data['units'] as $unit) {
					$outputData.='<a class="tooltip-left" data-tooltip="'.$gl["units"][$node->data['faction']][$unit]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/units/'.$node->data['faction'].'/'.$unit.'.png" title="'.$gl["units"][$node->data['faction']][$unit]["name"].'"></a>';
				}
				break;
				
			case 'command':
				$outputData='<a class="tooltip-left" data-tooltip="'.$gl["resources"][$module_data['outputResource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$module_data['outputResource'].'.png" title="'.$gl["resources"][$module_data['outputResource']]["name"].'"></a>';
				break;	
				
			case 'warfare':
				$outputData='';
				break;		
				
				
			//-- add new module types here

			default:
				$outputData=$ui['none'];
				break;
				
		 }
				
		 $tvars['tvar_linkData'] 				= $linkData;
		 $tvars['tvar_costData'] 				= $costData;
		 $tvars['tvar_requirementsData'] 		= $requirementsData;
		 $tvars['tvar_outputData'] 				= $outputData;
		 
		 if ($check_requirements['ok']) {
		 	$tvars['tvar_requirementsIcon']		= $d13->tpl->get("sub.requirement.ok"); #'<i class="f7-icons size-22 color-green">check</i>';
		 } else {
		 	$tvars['tvar_requirementsIcon']		= $d13->tpl->get("sub.requirement.notok"); #'<i class="f7-icons size-22 color-red">close</i>';
		 }
		 if ($check_cost['ok']) {
			$tvars['tvar_costIcon']		= $d13->tpl->get("sub.requirement.ok"); #'<i class="f7-icons size-22 color-green">check</i>';
		 } else {
		 	$tvars['tvar_costIcon']		= $d13->tpl->get("sub.requirement.notok"); #'<i class="f7-icons size-22 color-red">close</i>';
		 }
		
		 $tvars['tvar_moduleName'] 				= $gl["modules"][$node->data['faction']][$mid]["name"];
		 $tvars['tvar_moduleDescription'] 		= $gl["modules"][$node->data['faction']][$mid]["description"];
		 $tvars['tvar_moduleInputResName']		= $gl["resources"][$module_data['inputResource']]["name"];
		 $tvars['tvar_moduleInputResource'] 	= $module_data['inputResource'];
		 $tvars['tvar_moduleRatio'] 			= $module_data['ratio'];
		 $tvars['tvar_moduleMaxInput'] 			= $module_data['maxInput'];
		 $tvars['tvar_moduleMaxInstances'] 		= $module_data['maxInstances'];
		 $tvars['tvar_moduleDuration'] 			= $module_data['duration']*$game['users']['speed']['build'];
		 $tvars['tvar_moduleSalvage'] 			= $module_data['salvage'];
		 $tvars['tvar_moduleRemoveDuration'] 	= $module_data['removeDuration']*$game['users']['speed']['build'];

		 $tvars['tvar_nodeID'] 					= $node->data['id'];
		 $tvars['tvar_nodeFaction'] 			= $node->data['faction'];
		 $tvars['tvar_mid'] 					= $mid;

		 $tvars['tvar_sub_list'] .= $d13->tpl->render_subpage("sub.module.list", $tvars);

	}
}

// - - - - Add Slider Initalize at bottom of the page
$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));

$d13->tpl->render_page("module.list", $tvars);

}

//=====================================================================================EOF

?>