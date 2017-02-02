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

class d13_module_command extends d13_object_module

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
		
		if ($this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $rid => $res) {
				if ($d13->getResource($res['id'], 'active') && $res['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $rid . '.png" title="' . $d13->getLangGL('resources', $rid, 'name') . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('resources', $rid, 'name');
					$tvars['tvar_listAmount'] = floor($res['value']);
					$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
					$i++;
				}
			}
			if ($i>0) {
				$tooltip = d13_misc::toolTip($d13->getLangUI("tipInventoryResource"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup '.$tooltip.'" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$tooltip = d13_misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
				$html.= '</p>';
			}
		}

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
		
	
		global $d13;
		$html = '';
		$i = 0;
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 1;
		
		// - - - - Option: Remove Node
		$nodes = $this->node->getList($_SESSION[CONST_PREFIX . 'User']['id']);
		$t = count($nodes);
		if ($this->data['options']['nodeRemove'] && $t > 1) {
			$tvars['tvar_Label'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=remove&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("remove");
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Move Node
		if ($this->data['options']['nodeMove']) {
			$tvars['tvar_Label'] = $d13->getLangUI("move") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=move&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("move");
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Edit Node
		if ($this->data['options']['nodeEdit']) {
			$tvars['tvar_Label'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=set&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("edit");
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Add new Node
		if ($t < $d13->getGeneral('maxNodes')) {
			$tvars['tvar_Label'] = $d13->getLangUI("add") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=random';
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("add");
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}
		
		if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $i > 0) {
			$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
			$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
			$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list-1">' . $d13->getLangUI("launch") . " " . $d13->getLangUI("command") . '</a>';
			$html .= '</p>';
		} else {
			$html.= '<p class="buttons-row theme-gray">';
			$html.= '<a href="#" class="button">' . $d13->getLangUI("launch") . " " . $d13->getLangUI("command") .'</a>';
			$html.= '</p>';
		}
		
		return $html;

	
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