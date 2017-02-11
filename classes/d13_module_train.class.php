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

class d13_module_train extends d13_gameobject_module

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
		
		if ($this->data['options']['inventoryList']) {
			foreach($this->node->units as $uid => $unit) {
				if (in_array($uid, $this->d13->getModule($this->node->data['faction'], $this->data['id'], 'units'))) {
					if ($this->d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
						
						$image = $this->d13->getUnit($this->node->data['faction'], $uid, 'images');
						$image = $image[0]['image'];

						
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $image . '" title="' . $this->d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $this->d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $this->d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			
			if ($i>0) {
				
				$this->d13->templateInject($this->d13->templateSubpage("sub.popup.list", $tvars));
				
				$vars['tvar_button_name'] 	 = $this->data['name'] . " " . $this->d13->getLangUI("inventory");
				$vars['tvar_list_id'] 	 	 = "list-0";
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipInventoryTrain"));
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
		
		$html = '';
		$tvars = array();
		$tvars['tvar_sub_popupswiper'] = '';
		
		foreach($this->d13->getUnit($this->node->data['faction']) as $uid => $unit) {
			if ($unit['active'] && in_array($uid, $this->d13->getModule($this->node->data['faction'], $this->data['id'], 'units'))) {
				
				$args = array();
				$args['supertype'] = 'unit';
				$args['id'] = $uid;
				
				$tmp_unit = $this->d13->createGameObject($args, $this->node);
				
				$vars = array();
				$vars = $tmp_unit->getTemplateVariables();
				
				$vars['tvar_duration'] = $this->d13->misc->sToHMS((($tmp_unit->data['duration'] - $tmp_unit->data['duration'] * $this->data['totalIR']) * $this->d13->getGeneral('users', 'duration', 'train')) * 60, true);
				$vars['tvar_uid'] = $uid;
				$vars['tvar_nodeId'] = $this->node->data['id'];
				$vars['tvar_slotId'] = $this->data['slotId'];
				$vars['tvar_sliderID'] 	= $uid;
				$vars['tvar_sliderMin'] 	= "00";
				$vars['tvar_sliderMax'] 	= $tmp_unit->getMaxProduction();
				$vars['tvar_disableData']		= '';
				if ($tmp_unit->getMaxProduction() <= 0) {
					$vars['tvar_disableData']		= 'disabled';
				}
				$vars['tvar_sliderValue'] 	= "00";
				$vars['tvar_unitDescription'] = $tmp_unit->data['description'];
				$vars['tvar_unitMaxValue'] = $tmp_unit->data['amount'] + $tmp_unit->getMaxProduction();
				
				$tvars['tvar_sub_popupswiper'] .= $this->d13->templateSubpage("sub.module.train", $vars);
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
		$this->node->queues->getQueue('train');
		
		if (count($this->node->queues->queue['train'])) {
			foreach($this->node->queues->queue['train'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					if (!$item['stage']) {
						$stage = $this->d13->getLangUI('train');
					} else {
						$stage = $this->d13->getLangUI('remove');
					}
					$remaining = $this->d13->misc->sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$image = $this->d13->getUnit($this->node->data['faction'], $item['obj_id'], 'images');
					$image = $image[0]['image'];

					$tvars = array();
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $image . '">';
					$tvars['tvar_listLabel'] 	= $stage . ' ' . $item['quantity'] . 'x ' . $this->d13->getLangGL("units", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="train_' . $item['id'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("train_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelUnit&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&trainId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $this->d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}
		
		// - - - Popover if Queue empty

		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$tvars = array();
				
				
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("launch") . ' ' . $this->d13->getLangUI("train");
				$vars['tvar_list_id'] 	 	 = "swiper";
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI('tipModuleInactive'));
				$html = $this->d13->templateSubpage("button.popup.swiper", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("launch") . ' ' . $this->d13->getLangUI("train");
				$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI('tipModuleDisabled'));
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
				
		if (isset($this->data['units'])) {
			foreach($this->data['units'] as $unit) {
				if ($this->d13->getUnit($this->node->data['faction'], $unit, 'active')) {
					$id = $this->d13->getUnit($this->node->data['faction'], $unit, 'id');
					$image = $this->d13->getUnit($this->node->data['faction'], $id, 'images');
					$image = $image[0]['image'];
					$html.= '<a class="tooltip-left" data-tooltip="' . $this->d13->getLangGL("units", $this->node->data['faction'], $unit, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $image . '" title="' . $this->d13->getLangGL("units", $this->node->data['faction'], $unit, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $this->d13->getLangUI("none");
		}

		return $html;
	}
}

?>