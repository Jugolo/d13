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
// d13_module_craft
//
// ----------------------------------------------------------------------------------------

class d13_module_craft extends d13_object_module

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

			foreach($this->node->components as $uid => $unit) {
				if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
					if ($unit['value'] > 0) {
						
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $uid . '.png" title="' . $d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			
			if ($i>0) {
				$tooltip = d13_misc::toolTip($d13->getLangUI("tipInventoryCraft"));
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
	
		global $d13;
		
		$tvars['tvar_sub_popupswiper'] = '';
		$html = '';

		// - - - Craft Popup

		foreach($d13->getComponent($this->node->data['faction']) as $cid => $component) {
			if ($component['active'] && in_array($cid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
				
				$args = array();
				$args['supertype'] 	= 'component';
				$args['obj_id'] 	= $cid;
				$args['node'] 		= $this->node;
				
				$tmp_component = new d13_object_component($args);
				
				// - - - - Cost and Requirements
				$costData = $tmp_component->getCostList();
				$requirementsData = $tmp_component->getRequirementsList();

				// - - - Check Affordable Maximum

				$costLimit = $this->node->checkCostMax($component['cost'], 'craft');
				$reqLimit = $this->node->checkRequirementsMax($component['requirements']);
				$upkeepLimit = floor($this->node->resources[$d13->getComponent($this->node->data['faction'], $cid, 'storageResource') ]['value'] / $d13->getComponent($this->node->data['faction'], $cid, 'storage'));
				$unitLimit = abs($this->node->components[$cid]['value'] - $d13->getGeneral('types', $component['type'], 'limit'));
				$limitData = min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);
				$limitData = floor($limitData);

				// - - - - - Check Permissions

				$disableData = '';
				$check_requirements = $this->node->checkRequirements($component['requirements']);
				$check_cost = $this->node->checkCost($component['cost'], 'research');
				if ($check_requirements['ok'] && $check_cost['ok']) {
					$disableData = '';
				}
				else {
					$disableData = 'disabled';
				}

				if ($check_requirements['ok']) {
					$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
				}
				else {
					$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
				}

				if ($check_cost['ok']) {
					$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
				}
				else {
					$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
				}

				$tvars['tvar_nodeID'] = $this->node->data['id'];
				$tvars['tvar_slotID'] = $this->data['slotId'];
				$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_requirementsData'] = $requirementsData;
				$tvars['tvar_disableData'] = $disableData;
				$tvars['tvar_cid'] = $cid;
				$tvars['tvar_componentName'] = $tmp_component->data['name'];
				$tvars['tvar_componentDescription'] = $tmp_component->data['description'];
				$tvars['tvar_duration'] = d13_misc::sToHMS( (($component['duration'] - $component['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'craft')) * 60, true);
				$tvars['tvar_compLimit'] = $limitData;
				$vars['tvar_disableData']		= '';
				if ($limitData <= 0) {
					$vars['tvar_disableData']		= 'disabled';
				}
				$tvars['tvar_compValue'] = $tmp_component->data['amount'];
				$tvars['tvar_compStorage'] = $component['storage'];
				$tvars['tvar_compResource'] = $component['storageResource'];
				$tvars['tvar_compResourceName'] = $d13->getLangGL("resources", $component['storageResource'], "name");
				$tvars['tvar_compMaxValue'] = $tmp_component->data['amount'] + $limitData;
				
				$tvars['tvar_sliderID'] 	= $cid;
				$tvars['tvar_sliderMin'] 	= "00";
				$tvars['tvar_sliderMax'] 	= $limitData;
				$tvars['tvar_sliderValue'] 	= "00";

				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.craft", $tvars);
			}
		}

		$d13->templateInject($d13->templateSubpage("sub.popup.swiper", $tvars));
		$d13->templateInject($d13->templateSubpage("sub.swiper.horizontal", $tvars));
		return $tvars['tvar_sub_popupswiper'];
	}

	// ----------------------------------------------------------------------------------------
	// getQueue
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getQueue()
	{
		global $d13;
		$html = '';

		// - - - Check Queue
		
		$this->data['busy'] = false;
		
		if (count($this->node->queue['craft'])) {
			foreach($this->node->queue['craft'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					if (!$item['stage']) {
						$stage = $d13->getLangUI('craft');
					} else {
						$stage = $d13->getLangUI('remove');
					}
					
					$remaining = d13_misc::sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $item['obj_id'] . '.png">';
					$tvars['tvar_listLabel'] 	= $stage . ' ' . $item['quantity'] . 'x ' . $d13->getLangGL("components", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="craft_' . $item['id'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("craft_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelComponent&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&craftId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}

		// - - - Popover if Queue empty

		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$tvars = array();
				$tooltip = d13_misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$tvars['tvar_buttonColor'] 	= 'theme-'.$_SESSION[CONST_PREFIX.'User']['color'];
				$tvars['tvar_buttonData'] 	= 'class="button active open-popup '.$tooltip.'" data-popup=".popup-swiper" onclick="swiperUpdate();"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("craft");
				$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
			} else {
				$tvars = array();
				$tooltip = d13_misc::toolTip($d13->getLangUI('tipModuleDisabled'));
				$tvars['tvar_buttonColor'] 	= 'theme-gray';
				$tvars['tvar_buttonData'] 	= 'class="button '.$tooltip.'"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("craft");
				$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
			}
		}

		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getStats()
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
		if (isset($this->data['components'])) {
			foreach($this->data['components'] as $component) {
				if ($d13->getComponent($this->node->data['faction'], $component, 'active')) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("components", $this->node->data['faction'], $component) ["name"] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $component . '.png" title="' . $d13->getLangGL("components", $this->node->data['faction'], $component) ["name"] . '"></a>';
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