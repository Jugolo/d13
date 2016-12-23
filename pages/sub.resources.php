<?php

//========================================================================================
//
// SUB.RESOURCES
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
// sub_resources
//----------------------------------------------------------------------------------------

function sub_resources($node) {

	global $d13, $gl, $ui, $game;
	
	$res = '';
	
	if (isset($node) && isset($node->resources)) {
		foreach ($node->resources as $key=>$resource) {
			if ($game['resources'][$key]['visible']) {
				$res .= '<span class="badge">';
				$res .= '<a class="tooltip-bottom" data-tooltip="'.$gl["resources"][$key]["name"].'"><img class="d13-resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$key.'.png" title="'.$gl["resources"][$key]["name"].'"></a>';
				$res .= floor($resource['value']).'/'.$node->storage[$key];
				if ($node->production[$key]) {
					if (floor($resource['value']) < $node->storage[$key]) {
						$res .= ' (+'.$node->production[$key].$ui['perHour'].')';
					} else {
						$res .= ' ('.$d13->data->getUI("full").')';
					}
				}
				$res .= ' </span>&nbsp;';
			}
		}
	}
	
	//----------------------------------------------------------------------------------------
	// Setup Template Variables
	//----------------------------------------------------------------------------------------

	$tvars = array();
	$tvars['tvar_nodeResources'] 	= $res;

	//----------------------------------------------------------------------------------------
	// Parse & Render Template
	//----------------------------------------------------------------------------------------

	return $d13->tpl->render_subpage("sub.resources", $tvars);

}

//=====================================================================================EOF

?>