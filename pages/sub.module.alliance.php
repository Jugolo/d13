<?php

//========================================================================================
//
// SUB.MODULE.ALLIANCE
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
// sub_module_alliance
//----------------------------------------------------------------------------------------
function sub_module_alliance($node, $module, $mid, $sid, $message) {

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
	
	$tvars['tvar_mid'] 					= $mid;
	$tvars['tvar_moduleDescription']	= $gl["modules"][$node->data['faction']][$mid]["description"];
	$tvars['tvar_moduleInput']			= $module['inputResource'];
	$tvars['tvar_moduleInputName']		= $gl["resources"][$module['inputResource']]["name"];
	$tvars['tvar_moduleInputLimit'] 	= floor(min($module['maxInput'], $node->resources[$module['inputResource']]["value"]+$node->modules[$sid]['input']));
	$tvars['tvar_moduleMaxInput'] 		= $module['maxInput'];
	$tvars['tvar_moduleName'] 			= $gl["modules"][$node->data['faction']][$mid]["name"];
	#$tvars['tvar_moduleOutput']		= $module['outputResource'];
	#$tvars['tvar_moduleOutputName']	= $gl["resources"][$module['outputResource']]["name"];
	$tvars['tvar_moduleProduction'] 	= $module['ratio']*$game['factors']['production']*$node->modules[$sid]['input'];
	$tvars['tvar_moduleRatio'] 			= $module['ratio'];
	$tvars['tvar_moduleSlotInput'] 		= $node->modules[$sid]['input'];
	$tvars['tvar_nodeFaction'] 			= $node->data['faction'];
	$tvars['tvar_nodeID'] 				= $node->data['id'];
	$tvars['tvar_slotID'] 				= $_GET['slotId'];
	$tvars['tvar_demolishLink'] 		= $demolishData;
	$tvars['tvar_moduleStorage'] 		= $module['ratio']*$node->modules[$sid]['input'];

	$i=0;
	foreach ($module['storedResource'] as $res) {
		$tvars['tvar_moduleStorageRes'.$i]		= $res;
		$tvars['tvar_moduleStorageResName'.$i]	= $gl["resources"][$res]["name"];
		$i++;
	}

	
	$tvars['tvar_moduleItemContent'] = "";
	
	//- - - - Option: Alliance List
	if ($module['options']['allianceGet']) {
		$tvars['tvar_Label']				= misc::getlang("get") . ' ' . misc::getlang("alliance");
		$tvars['tvar_Link']					= '?p=alliance&action=get&nodeId='.$node->data['id'];
		$tvars['tvar_LinkLabel']			= misc::getlang("get") . ' ' . misc::getlang("alliance");
		$tvars['tvar_moduleItemContent']	.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
	}
	
	//- - - - Option: Alliance Edit
	if ($module['options']['allianceEdit']) {
		$tvars['tvar_Label']				= misc::getlang("edit") . ' ' . misc::getlang("alliance");
		$tvars['tvar_Link']					= '?p=alliance&action=add&nodeId='.$node->data['id'];
		$tvars['tvar_LinkLabel']			= misc::getlang("edit") . ' ' . misc::getlang("alliance");
		$tvars['tvar_moduleItemContent']	.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
	}
	
	//- - - - Option: Alliance Remove
	if ($module['options']['allianceRemove']) {
		$tvars['tvar_Label']				= misc::getlang("remove") . ' ' . misc::getlang("alliance");
		$tvars['tvar_Link']					= '?p=alliance&action=remove&nodeId='.$node->data['id'];
		$tvars['tvar_LinkLabel']			= misc::getlang("remove") . ' ' . misc::getlang("alliance");
		$tvars['tvar_moduleItemContent']	.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
	}
	
	//- - - - Option: Alliance Invite
	if ($module['options']['allianceInvite']) {
		$tvars['tvar_Label']				= misc::getlang("invite") . ' ' . misc::getlang("members");
		$tvars['tvar_Link']					= '?p=alliance&action=addInvitation&nodeId='.$node->data['id'];
		$tvars['tvar_LinkLabel']			= misc::getlang("invite") . ' ' . misc::getlang("members");
		$tvars['tvar_moduleItemContent']	.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
	}
	
	//- - - - Option: Alliance Go to War
	if ($module['options']['allianceWar']) {
		$tvars['tvar_Label']				= misc::getlang("warDeclaration");
		$tvars['tvar_Link']					= '?p=alliance&action=addWar&nodeId='.$node->data['id'];
		$tvars['tvar_LinkLabel']			= misc::getlang("warDeclaration");
		$tvars['tvar_moduleItemContent']	.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
	}
	
	//- - - - Option: 
	//- - - - Option: 
	//- - - - Option: 
	
	$page = "module.get.alliance";
	$d13->tpl->render_page($page, $tvars);
	break;
					
}

//=====================================================================================EOF

?>