<?php

// ========================================================================================
//
// MODULE.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
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

class d13_module_storage extends d13_gameobject_module

{
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct($args, &$node, d13_engine &$d13)
	{
		parent::__construct($args, $node, $d13);
	}

	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$html = '';
		$i=0;
		
		if (isset($this->data['options']['inventoryList']) && $this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $uid => $unit) {
				if ($this->d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $uid . '.png" title="' . $this->d13->getLangGL('resources', $uid, 'name') . '">';
					$tvars['tvar_listLabel'] = $this->d13->getLangGL('resources', $uid, 'name');
					$tvars['tvar_listAmount'] = floor($unit['value']);
					$tvars['tvar_sub_popuplist'].= $this->d13->templateSubpage("sub.module.listcontent", $tvars);
					$i++;
				}
			}
			
			$this->data['units'] = $i;											// !important
			
			if ($i > 0) {
				
				$this->d13->templateInject($this->d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipInventoryResource"));
				$html = $this->d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $this->d13->getLangUI("inventory");
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipInventoryResource"));
				$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
			}
		
		}

		return $html;
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
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOutputList()
	{
		
			return parent::getStoredResourceList();
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------

	public
	
	function getTemplateVariables()
	{
		return parent::getTemplateVariables();
	}
	
}

// =====================================================================================EOF
