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
// ========================================================================================

// ----------------------------------------------------------------------------------------
// d13_module_harvest
//
// ----------------------------------------------------------------------------------------

class d13_module_harvest extends d13_object_module

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
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$tooltip = d13_misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html.= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
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
		if (isset($this->data['outputResource'])) {
			foreach($this->data['outputResource'] as $res) {
				if ($d13->getResource($res, 'active')) {
					$html.= '' . $d13->getLangUI('production') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('production') . " " . $d13->getLangGL("resources", $res, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
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