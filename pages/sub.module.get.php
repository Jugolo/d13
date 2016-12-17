<?php

//========================================================================================
//
// SUB.MODULE.GET
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
// 
//----------------------------------------------------------------------------------------

function sub_module_get($node, $module, $mid, $sid, $message) {

	global $d13, $gl, $game;

	/* -- demolish, permissions can go here --- */
	/* --- all global module options can go here - requires refactoring -- */

	if (isset($node->modules[$_GET['slotId']])) {
		if (isset($module)) {
	 
			 switch ($module['type']) {
			 
				// - - - - 
				case 'storage':
						include("sub.module.storage.php");
						sub_module_storage($node, $module, $mid, $sid, $message);
						break;
					
				// - - - - 
				case 'harvest':
						include("sub.module.harvest.php");
						sub_module_harvest($node, $module, $mid, $sid, $message);
						break;
			
				  // - - - - 
				case 'craft':
						include("sub.module.craft.php");
						sub_module_craft($node, $module, $mid, $sid, $message);
						break;
			
				// - - - - 
				case 'train':
						include("sub.module.train.php");
						sub_module_train($node, $module, $mid, $sid, $message);
						break;
			
				// - - - - 
				case 'research':
						include("sub.module.research.php");
						sub_module_research($node, $module, $mid, $sid, $message);
						break;
			
				// - - - - 
				case 'alliance':
						include("sub.module.alliance.php");
						sub_module_alliance($node, $module, $mid, $sid, $message);
						break;
				
				// - - - - 
				case 'command':
						include("sub.module.command.php");
						sub_module_command($node, $module, $mid, $sid, $message);
						break;
				
				// - - - - 
				case 'defense':
						include("sub.module.defense.php");
						sub_module_defense($node, $module, $mid, $sid, $message);
						break;
				
				// - - - - 
				case 'warfare':
						include("sub.module.warfare.php");
						sub_module_warfare($node, $module, $mid, $sid, $message);
						break;
					
				//-- add new building types here
				
			}
		}
	}

}

//=====================================================================================EOF

?>