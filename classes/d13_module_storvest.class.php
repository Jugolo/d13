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

class d13_module_storvest extends d13_object_module

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
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$html = '';
		$i=0;
		
		if (isset($this->data['options']['inventoryList']) && $this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $uid => $unit) {
				if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $uid . '.png" title="' . $d13->getLangGL('resources', $uid, 'name') . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('resources', $uid, 'name');
					$tvars['tvar_listAmount'] = floor($unit['value']);
					$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
					$i++;
				}
			}
			if ($i > 0) {
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
								
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = "";
				$html = $d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty");
				$vars['tvar_button_tooltip'] = "";
				$html = $d13->templateSubpage("button.popup.disabled", $vars);
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
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOutputList()
	{
		global $d13;
		$html = '';
		if (isset($this->data['storedResource'])) {
			foreach($this->data['storedResource'] as $res) {
				if ($d13->getResource($res, 'active')) {
					$html.= $d13->getLangUI('production') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('production') . ' ' . $d13->getLangGL("resources", $res, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
					$html.= ' ' . $d13->getLangUI('storage') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('storage') . ' ' . $d13->getLangGL("resources", $res, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}

?>