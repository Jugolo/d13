<?php

// ========================================================================================
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
// ========================================================================================
// ----------------------------------------------------------------------------------------
// sub_module_list
// ----------------------------------------------------------------------------------------

function sub_module_list($node, $module_data, $message)
{
	global $d13;
	$tvars = array();
	$tvars['tvar_sub_list'] = "";
	$tvars['tvar_global_message'] = $message;
	if (isset($node->modules[$_GET['slotId']])) {
		foreach($d13->getModule($node->data['faction']) as $mid => $module_data) {
			$module = NULL;
			$module = d13_module_factory::create($mid, $_GET['slotId'], $d13->getModule($node->data['faction'], $mid, 'type') , $node);
			$tvars = array_merge($tvars, $module->getTemplateVariables());
			$tvars['tvar_sub_list'].= $d13->templateSubpage("sub.module.list", $tvars);
		}
	}

	// - - - - Add Slider Initalize at bottom of the page

	$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));
	$d13->templateRender("module.list", $tvars);
}

// =====================================================================================EOF

?>