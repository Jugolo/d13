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

	global $d13;
	
	$tvars = array();
	$tvars['tvar_resEntry'] 	= '';
	
	if (isset($node) && isset($node->resources)) {
		foreach ($node->resources as $key=>$resource) {
			if ($d13->data->resources->get($key,'active') && $d13->data->resources->get($key,'visible')) {
				
				$tvars['tvar_resName'] 			= $d13->data->gl->get('resources', $resource['id'], 'name');
				$tvars['tvar_resImage'] 		= $d13->data->resources->getbyid('image', $resource['id']);
				$tvars['tvar_resValue']			= floor($resource['value']) . '/' . $node->storage[$resource['id']];
				$tvars['tvar_resProduction'] 	= '';
				
				if ($node->production[$resource['id']]) {
					if (floor($resource['value']) < $node->storage[$resource['id']]) {
						$tvars['tvar_resProduction'] = ' [+' . $node->production[$resource['id']] . $d13->data->ui->get('perHour') . ']';
					} else {
						$tvars['tvar_resProduction'] = ' [' . $d13->data->ui->get("full") .']';
					}
				}
				
				$tvars['tvar_resEntry'] 		.= $d13->tpl->render_subpage("sub.resource.entry",$tvars);

			}
		}
	}
	
	//----------------------------------------------------------------------------------------
	// Setup Template Variables
	//----------------------------------------------------------------------------------------

	$tvars['tvar_nodeResources'] 	= $tvars['tvar_resEntry'];

	//----------------------------------------------------------------------------------------
	// Parse & Render Template
	//----------------------------------------------------------------------------------------

	return $d13->tpl->render_subpage("sub.resources", $tvars);

}

//=====================================================================================EOF

?>