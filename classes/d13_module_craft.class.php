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

class d13_module_craft extends d13_gameobject_module

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
		
		$html = '';
		$inventoryData = '';
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach($this->node->components as $uid => $unit) {
				if (in_array($uid, $this->d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
					if ($unit['value'] > 0) {
						
						$image = $this->d13->getComponent($this->node->data['faction'], $uid, 'icon');
						
						
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $image . '" title="' . $this->d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $this->d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $this->d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			
			if ($i>0) {
				
				$this->d13->templateInject($this->d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 =  $this->data['name'] . " " . $this->d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipInventoryCraft"));
				$html = $this->d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $this->d13->getLangUI("inventory");
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipInventoryEmpty"));
				$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
			}
		}
		
		$this->data['count'] = $i;
		
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
	
		
		
		$tvars['tvar_sub_popupswiper'] = '';
		$html = '';

		// - - - Craft Popup
		
		$comp_list = $this->d13->getComponent($this->node->data['faction']);
		$comp_list = $this->d13->misc->record_sort($comp_list, 'priority', true);

		foreach($comp_list as $cid => $component) {
			if ($component['active'] && in_array($cid, $this->d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
				
				$args = array();
				$args['supertype'] 	= 'component';
				$args['id'] 		= $cid;
				
				$tmp_component = $this->d13->createGameObject($args, $this->node);
				
				$vars = array();
				$vars = $tmp_component->getTemplateVariables();

				$vars['tvar_nodeID'] 				= $this->node->data['id'];
				$vars['tvar_slotID'] 				= $this->data['slotId'];
				$vars['tvar_nodeFaction'] 			= $this->node->data['faction'];
				$vars['tvar_cid'] 					= $cid;
				$vars['tvar_componentName'] 		= $tmp_component->data['name'];
				$vars['tvar_componentDescription'] 	= $tmp_component->data['description'];
				$vars['tvar_duration'] 				= $this->d13->misc->sToHMS( (($component['duration'] - $component['duration'] * $this->data['totalIR']) * $this->d13->getGeneral('users', 'duration', 'craft')) * 60, true);
				$vars['tvar_compLimit'] 			= $tmp_component->getMaxProduction();;
				$vars['tvar_disableData']			= '';
				if ($tmp_component->getMaxProduction() <= 0) {
					$vars['tvar_disableData']		= 'disabled';
				}
				$vars['tvar_compValue'] 			= $tmp_component->data['amount'];
				$vars['tvar_compStorage'] 			= $component['storage'];
				$vars['tvar_compResource'] 			= $component['storageResource'];
				$vars['tvar_compResourceName'] 		= $this->d13->getLangGL("resources", $component['storageResource'], "name");
				$vars['tvar_compMaxValue'] 			= $tmp_component->data['amount'] + $tmp_component->getMaxProduction();
				$vars['tvar_sliderID'] 				= $cid;
				$vars['tvar_sliderMin'] 			= "0";
				$vars['tvar_sliderMax'] 			= $tmp_component->getMaxProduction();
				$vars['tvar_sliderValue'] 			= "0";

				$tvars['tvar_sub_popupswiper'].= $this->d13->templateSubpage("sub.module.craft", $vars);
			}
		}

		$this->d13->templateInject($this->d13->templateSubpage("sub.popup.swiper", $tvars));
		$this->d13->templateInject($this->d13->templateSubpage("sub.swiper.horizontal", $tvars));
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
		
		$html = '';

		// - - - Check Queue
		
		$this->data['busy'] = false;
		$this->node->queues->getQueue('craft');
		
		if (count($this->node->queues->queue['craft'])) {
			foreach($this->node->queues->queue['craft'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					if (!$item['stage']) {
						$stage = $this->d13->getLangUI('craft');
					} else {
						$stage = $this->d13->getLangUI('remove');
					}
					
					$remaining = $this->d13->misc->sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$image = $this->d13->getComponent($this->node->data['faction'], $item['obj_id'], 'images');
					$image = $image[0]['image'];
									
					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $image .'">';
					$tvars['tvar_listLabel'] 	= $stage . ' ' . $item['quantity'] . 'x ' . $this->d13->getLangGL("components", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="craft_' . $item['id'] . '">' . $remaining . '</span> <a class="external" href="?p=module&action=cancelComponent&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&craftId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					#$tvars['tvar_listAmount'] 	= '<span id="craft_' . $item['id'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("craft_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelComponent&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&craftId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $this->d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}

		// - - - Popover if Queue empty

		if ((bool)$this->data['busy'] === false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
			
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("launch") . ' ' . $this->d13->getLangUI("craft");
				$vars['tvar_list_id'] 	 	 = "swiper";
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipModuleInactive"));
				$html = $this->d13->templateSubpage("button.popup.swiper", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("launch") . ' ' . $this->d13->getLangUI("craft");
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipModuleDisabled"));
				$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
			}
		}

		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getOutputList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOutputList()
	{
		
		$html = '';
		if (isset($this->data['components'])) {
			foreach($this->data['components'] as $component) {
				if ($this->d13->getComponent($this->node->data['faction'], $component, 'active')) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $this->d13->getLangGL("components", $this->node->data['faction'], $component, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $component . '.png" title="' . $this->d13->getLangGL("components", $this->node->data['faction'], $component, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $this->d13->getLangUI("none");
		}

		return $html;
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

?>