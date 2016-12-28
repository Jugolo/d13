<?php

// ========================================================================================
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
// ========================================================================================
// ----------------------------------------------------------------------------------------
// sub_resources
// ----------------------------------------------------------------------------------------

function sub_resources($node)
{
	global $d13;
	$tvars = array();
	$tvars['tvar_resEntry'] = '';
	if (isset($node) && isset($node->resources)) {
		foreach($node->resources as $resource) {
			if ($d13->getResourceByID($resource['id'], 'active') && $d13->getResourceByID($resource['id'], 'visible')) {
				$tvars['tvar_resName'] = $d13->getLangGL('resources', $resource['id'], 'name');
				$tvars['tvar_resImage'] = $d13->getResourceByID($resource['id'], 'image');
				$tvars['tvar_resValue'] = floor($resource['value']) . '/' . $node->storage[$resource['id']];
				$tvars['tvar_resProduction'] = '';
				if ($node->production[$resource['id']]) {
					if (floor($resource['value']) < $node->storage[$resource['id']]) {
						$tvars['tvar_resProduction'] = ' [+' . round($node->production[$resource['id']]) . $d13->getLangUI('perHour') . ']';
					}
					else {
						$tvars['tvar_resProduction'] = ' [' . $d13->getLangUI("full") . ']';
					}
				}

				$tvars['tvar_resEntry'].= $d13->templateSubpage("sub.resource.entry", $tvars);
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// Setup Template Variables
	// ----------------------------------------------------------------------------------------

	$tvars['tvar_nodeResources'] = $tvars['tvar_resEntry'];

	// ----------------------------------------------------------------------------------------
	// Parse & Render Template
	// ----------------------------------------------------------------------------------------

	return $d13->templateSubpage("sub.resources", $tvars);
}

// =====================================================================================EOF

?>