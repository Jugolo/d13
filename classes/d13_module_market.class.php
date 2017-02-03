<?php

// ========================================================================================
//
// MODULE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// ABOUT MODULES:
//
// Modules are Building Objects. Each Node (Town) can contain one or more Modules. Modules
// are the only objects that feature a level and can be upgraded directly. Most of the
// main gameplay features are handled using modules. Modules require a worker resource in
// order to be built/upgraded and require this worker resource in order to function as well.
//
// NOTES:
//
// 
//
// ========================================================================================

class d13_module_market extends d13_object_module

{
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args)
	{
		parent::__construct($args);
	}


	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		global $d13;
		
		$html = '';
		$inventoryData = '';
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			
			foreach ($this->data['inventory'] as $item) {
			
				switch ($item['object'])
				{
					case "resource":
						$name = $d13->getLangGL('resources', $item['id'], 'name');
						$image = $d13->getResource($item['id'], 'image');
						$dir = "resources";
						break;
					case "component":
						$object = $d13->getComponent($this->node->data['faction'], $item['id']);
						$name = $d13->getLangGL('components', $this->node->data['faction'], $item['id'], 'name');
						$image = $this->node->data['faction'] . '/' . $object['images'][0]['image'];
						$dir = "components";
						break;
					case "shield":
						$name = $d13->getLangGL('shields', $item['id'], 'name');
						$image = $d13->getShield($item['id'], 'image');
						$dir = "icon";
						break;
					case "buff":
						$name = $d13->getLangGL('buffs', $item['id'], 'name');
						$image = $d13->getBuff($item['id'], 'image');
						$dir = "icon";
						break;
				}
			
				

				$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $dir . '/' . $image . '" title="' . $name . '">';
				$tvars['tvar_listLabel'] = $name;
				$tvars['tvar_listAmount'] = "";
				$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
				$i++;
			
			
			}
			
			if ($i>0) {
				
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 =  $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryCraft"));
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_button_tooltip'] = d13_misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html = $d13->templateSubpage("button.popup.disabled", $vars);
			}
		}
		
		$this->data['count'] = $i;
		
		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getOptions
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOptions()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getPopup
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getPopup()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getQueue
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getQueue()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getOutputList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOutputList()
	{
		global $d13;
		return $d13->getLangUI("none");
	}
	
}

?>