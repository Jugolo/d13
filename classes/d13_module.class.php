<?php

//========================================================================================
//
// MODULE.CLASS
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
// d13_module_factory
// 
//----------------------------------------------------------------------------------------
class d13_module_factory {

	public static function create($moduleId, $slotId, $type, $node) {

		switch ($type) {
		
			case 'storage':
				return new d13_module_storage($moduleId, $slotId, $type, $node);
				break;
				
			case 'harvest':
				return new d13_module_harvest($moduleId, $slotId, $type, $node);
				break;
			
			case 'craft':
				return new d13_module_craft($moduleId, $slotId, $type, $node);
				break;

			case 'train':
				return new d13_module_train($moduleId, $slotId, $type, $node);
				break;

			case 'research':
				return new d13_module_research($moduleId, $slotId, $type, $node);
				break;

			case 'alliance':
				return new d13_module_alliance($moduleId, $slotId, $type, $node);
				break;

			case 'command':
				return new d13_module_command($moduleId, $slotId, $type, $node);
				break;

			case 'defense':
				return new d13_module_defense($moduleId, $slotId, $type, $node);
				break;

			case 'warfare':
				return new d13_module_warfare($moduleId, $slotId, $type, $node);
				break;

		}

	}

}

//----------------------------------------------------------------------------------------
// d13_module
// 
//----------------------------------------------------------------------------------------
class d13_module {

	public $moduleId, $slotId, $type, $data, $node, $checkRequirements, $checkCost;
	
	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct($moduleId, $slotId, $type, $node) {
		
		$this->setNode($node);
		$this->setAttributes($moduleId, $slotId, $type);
		#$this->checkUpgrades();
	}

	//----------------------------------------------------------------------------------------
	// setNode
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function setNode($node) { 
		$this->node	= $node;
		$this->node->getTechnologies();
		$this->node->getModules();
	}		
	
	//----------------------------------------------------------------------------------------
	// setAttributes
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function setAttributes($moduleId, $slotId, $type) { 
		
		global $gl, $game;
		
		$this->data = array();
		
		$this->data = $game['modules'][$this->node->data['faction']][$moduleId];
		
		$this->data['moduleInput']				= 0;
		$this->data['moduleInputLimit'] 		= 0;
		$this->data['moduleInputName']			= '';
		$this->data['moduleMaxInput'] 			= 0;
		$this->data['moduleSlotInput'] 			= 0;
		$this->data['moduleProduction'] 		= 0;
		$this->data['moduleStorage'] 			= 0;
				
		if (isset($this->data['inputResource'])) {
		$this->data['moduleInput']				= $this->data['inputResource'];
		$this->data['moduleInputLimit'] 		= floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value']+$this->node->modules[$slotId]['input']));
		$this->data['moduleInputName']			= $gl['resources'][$this->data['inputResource']]['name'];
		$this->data['moduleMaxInput'] 			= $this->data['maxInput'];
		$this->data['moduleRatio'] 				= $this->data['ratio'];
		$this->data['moduleSlotInput'] 			= $this->node->modules[$slotId]['input'];
		$this->data['totalIR'] 					= $this->node->modules[$slotId]['input'] * $this->data['moduleRatio'];
		}

		$this->data['moduleId']					= $moduleId;
		$this->data['slotId']					= $slotId;
		$this->data['type']						= $type;
		$this->data['name']						= $gl['modules'][$this->node->data['faction']][$this->data['moduleId']]['name'];
		$this->data['description']				= $gl['modules'][$this->node->data['faction']][$this->data['moduleId']]['description'];
		$this->data['image']					= $this->data['moduleId'];
		$this->data['totalIR'] 					= $this->data['ratio'];
		$this->data['inputLimit'] 				= floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'] + $this->node->modules[$this->data['slotId']]['input']));
		$this->data['level'] 					= $this->node->modules[$slotId]['level'];
		
		if (isset($this->data['outputResource'])) {
			$this->data['moduleProduction'] 	= $this->data['ratio'] * $game['factors']['production'] * $this->node->modules[$slotId]['input'];
			$i=0;
			foreach ($this->data['outputResource'] as $res) {
				$this->data['moduleOutput'.$i]		= $res;
				$this->data['moduleOutputName'.$i]	= $gl["resources"][$res]["name"];
				$i++;
			}
		}
		
		if (isset($this->data['storedResource'])) {
			$this->data['moduleStorage'] 		= $this->data['ratio'] * $this->node->modules[$slotId]['input'];
			$i=0;
			foreach ($this->data['storedResource'] as $res) {
				$this->data['moduleStorageRes'.$i]		= $res;
				$this->data['moduleStorageResName'.$i]	= $gl["resources"][$res]["name"];
				$i++;
			}
		}
		
	}	

	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		return '';
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getTemplateVariables() {

		$tvars = array();
		
		$tvars['tvar_demolishLink'] 		= '';
		$tvars['tvar_inventoryLink'] 		= '';
		$tvars['tvar_linkData'] 			= '';
		$tvars['tvar_moduleItemContent'] 	= '';
		
		
		$tvars['tvar_demolishLink'] 		= $this->getDemolish();
		$tvars['tvar_inventoryLink'] 		= $this->getInventory();
		$tvars['tvar_linkData'] 			= $this->getModuleUpgrade();
		$tvars['tvar_moduleItemContent'] 	= $this->getOptions();
		$tvars['tvar_queue'] 				= $this->getQueue();
		$tvars['tvar_popup'] 				= $this->getPopup();
		
		$tvars['tvar_moduleDescription']	= $this->data['description'];
		$tvars['tvar_moduleID'] 			= $this->data['moduleId'];
		$tvars['tvar_moduleImage']			= $this->data['image'];
		$tvars['tvar_moduleInput']			= $this->data['moduleInput'];
		$tvars['tvar_moduleInputLimit'] 	= $this->data['moduleInputLimit'];
		$tvars['tvar_moduleInputName']		= $this->data['moduleInputName'];
		$tvars['tvar_moduleMaxInput'] 		= $this->data['moduleMaxInput'];
		$tvars['tvar_moduleName'] 			= $this->data['name'];
		$tvars['tvar_moduleProduction'] 	= $this->data['moduleProduction'];
		$tvars['tvar_moduleRatio'] 			= $this->data['moduleRatio'];
		$tvars['tvar_moduleSlotInput'] 		= $this->data['moduleSlotInput'];
		$tvars['tvar_moduleStorage'] 		= $this->data['moduleStorage'];
		$tvars['tvar_totalIR'] 				= $this->data['totalIR'];
		
		$tvars['tvar_nodeFaction'] 			= $this->node->data['faction'];
		$tvars['tvar_nodeID'] 				= $this->node->data['id'];
		$tvars['tvar_slotID'] 				= $this->data['slotId'];
		$tvars['tvar_moduleLevel'] 			= $this->data['level'];
		
		if (isset($this->data['storedResource'])) {
			$i=0;
			while (isset($this->data['moduleStorageRes'.$i])) {
				$tvars['tvar_moduleStorageRes'.$i] 		= $this->data['moduleStorageRes'.$i];
				$tvars['tvar_moduleStorageResName'.$i] 	= $this->data['moduleStorageResName'.$i];
				$i++;
			}
		}
		
		if (isset($this->data['outputResource'])) {
			$i=0;
			while (isset($this->data['moduleOutput'.$i])) {
				$tvars['tvar_moduleOutput'.$i]			= $this->data['moduleOutput'.$i];
				$tvars['tvar_moduleOutputName'.$i]		= $this->data['moduleOutputName'.$i];
				$i++;
			}
		}

		return $tvars;
	
	}
	
	
	//----------------------------------------------------------------------------------------
	// getCheckDemolish
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getDemolish() {
	
		global $game;
		
		$html='';
		
		if ($game['options']['moduleDemolish']) {
			if ($this->node->modules[$this->data['slotId']]['input'] <= 0) {
				$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
				$html .= '<a class="external button" href="?p=module&action=remove&nodeId='.$this->node->data['id'].'&slotId='.$this->data['slotId'].'">'.misc::getlang("removeModule").'</a>';
				$html .= '</p>';
			} else {
				$html .= '<p class="buttons-row theme-gray>';
				$html .= '<a class="button" href="#">'.misc::getlang("removeModule").'</a>';
				$html .= '</p>';
			}
		}
		
		return $html;
		
	}

	//----------------------------------------------------------------------------------------
	// getModuleUpgrade
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getModuleUpgrade() {
		
		global $game;
		
		$html='';
		
		if ($game['options']['moduleUpgrade']) {
			if ($this->node->modules[$this->data['slotId']]['level'] < $this->data['maxLevel'] && $this->data['maxLevel'] > 1) {
				$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
				$html .= '<a class="external button" href="?p=module&action=upgrade&nodeId='.$this->node->data['id'].'&moduleId='.$this->data['moduleId'].'&slotId='.$this->data['slotId'].'">'.misc::getlang("upgrade").'</a>';
				$html .= '</p>';
			} else {
				$html .= '<p class="buttons-row theme-gray">';
				$html .= '<a class="button" href="#">'.misc::getlang("upgrade").'</a>';
				$html .= '</p>';
			}
		}
		
		return $html;
		
	}
	//----------------------------------------------------------------------------------------
	// getTemplate
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getTemplate() {
		return "module.get.".$this->data['type'];
	}

}

//========================================================================================
//									DERIVED MODULE CLASSES
//========================================================================================

//----------------------------------------------------------------------------------------
// d13_module_warfare
// 
//----------------------------------------------------------------------------------------
class d13_module_warfare extends d13_module {

	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		
		global $d13;
		
		$tvars = array();
		$html = '';
		
		//- - - - Option: Raid
		if ($this->data['options']['combatRaid']) {
			$tvars['tvar_Label']				= misc::getlang("launch") . ' ' . misc::getlang("raid");
			$tvars['tvar_Link']					= '?p=combat&action=add&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("set");
			$html	.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Conquer
		//- - - - Option: Raze
		//- - - - Option: Scout
		//- - - - Option: 
		//- - - - Option: 
		
		return $html;
	
	}
	
	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		return '';
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}

}

//----------------------------------------------------------------------------------------
// d13_module_storage
// 
//----------------------------------------------------------------------------------------
class d13_module_storage extends d13_module {

	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		
		global $d13;
		
		$tvars = array();
		$html = '';
			
		if ($this->data['options']['inventoryList']) {
			foreach ($this->node->resources as $uid=>$unit) {
				if ($unit['value'] > 0) {
					$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$uid.'.png" title="'.$gl['resources'][$uid]['name'].'">';
					$tvars['tvar_listLabel'] 		= $gl['resources'][$uid]['name'];
					$tvars['tvar_listAmount'] 		= floor($unit['value']);
					$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
				}
			}
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));
	
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		}

		return $html;
	
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}

}

//----------------------------------------------------------------------------------------
// d13_module_harvest
// 
//----------------------------------------------------------------------------------------
class d13_module_harvest extends d13_module {

	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		
		global $d13, $gl;
		
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		
		$html = '';
		
		if ($this->data['options']['inventoryList']) {
			foreach ($this->node->resources as $uid=>$unit) {
				if ($unit['value'] > 0) {
					$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$uid.'.png" title="'.$gl['resources'][$uid]['name'].'">';
					$tvars['tvar_listLabel'] 		= $gl['resources'][$uid]['name'];
					$tvars['tvar_listAmount'] 		= floor($unit['value']);
					$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
				}
			}
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));
	
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		}

		return $html;
	
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}

}

//----------------------------------------------------------------------------------------
// d13_module_craft
// 
//----------------------------------------------------------------------------------------
class d13_module_craft extends d13_module {


	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		
		global $d13, $game, $ui, $gl;
		
		$html = '';
	
		//- - - - - Check Inventory
		$inventoryData = '';
		$tvars['tvar_sub_popuplist'] = '';
	
		if ($this->data['options']['inventoryList']) {
			//- - - - - Popover if Inventory filled
			foreach ($this->node->components as $uid=>$unit) {
				if (in_array($uid, $game['modules'][$this->node->data['faction']][$this->data['moduleId']]['components'])) {
					if ($unit['value'] > 0) {
						$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/components/'.$this->node->data['faction'].'/'.$uid.'.png" title="'.$gl['components'][$this->node->data['faction']][$uid]['name'].'">';
						$tvars['tvar_listLabel'] 		= $gl['components'][$this->node->data['faction']][$uid]['name'];
						$tvars['tvar_listAmount'] 		= $unit['value'];
						$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
					}
				}
			}
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));
	
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		}
	
		return $html;
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
	
		global $d13, $game, $ui, $gl;
	
		$tvars['tvar_sub_popupswiper'] = '';
		$html = '';
		
		// - - - Craft Popup
		foreach ($game['components'][$this->node->data['faction']] as $cid=>$component) {
			if (in_array($cid, $game['modules'][$this->node->data['faction']][$this->data['moduleId']]['components'])) {
				$costData='';
				foreach ($component['cost'] as $key=>$cost) {
					$costData.='<div class="cell">'.($cost['value']*$game['users']['cost']['train']).'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$gl["resources"][$cost['resource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></a></div>';
				}
				if (!count($component['requirements'])) {
					$requirementsData=$ui['none'];
				} else {
					$requirementsData='';
					foreach ($component['requirements'] as $key=>$requirement) {
						$requirementsData.='<div class="cell">'.$requirement['value'].'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$gl[$requirement['type']][$this->node->data['faction']][$requirement['id']]['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$requirement['type'].'/'.$this->node->data['faction'].'/'.$requirement['id'].'.png" title="'.$ui[$requirement['type']].' - '.$gl[$requirement['type']][$this->node->data['faction']][$requirement['id']]['name'].'"></a></div>';
					}
				}
			
				// - - - Check Affordable Maximum
				$costLimit 	= $this->node->checkCostMax($component['cost'], 'craft');
				$reqLimit 	= $this->node->checkRequirementsMax($component['requirements']);
				$upkeepLimit = floor($this->node->resources[$game['components'][$this->node->data['faction']][$cid]['storageResource']]['value'] / $game['components'][$this->node->data['faction']][$cid]['storage']);
				$unitLimit = abs($this->node->components[$cid]['value'] - $game['types'][$component['type']]['limit']);
				$limitData = min($costLimit, $reqLimit, $upkeepLimit, $unitLimit);
				$limitData = floor($limitData);
			
				//- - - - - Check Permissions
				$disableData='';
				$check_requirements = NULL;
				$check_cost = NULL;
		
				$check_requirements = $this->node->checkRequirements($component['requirements']);
				$check_cost 		= $this->node->checkCost($component['cost'], 'research');
		
				if ($check_requirements['ok'] && $check_cost['ok']) {
					$disableData = '';
				} else {
					$disableData = 'disabled';
				}
			
				if ($check_requirements['ok']) {
					$tvars['tvar_requirementsIcon']		= '<i class="f7-icons size-22 color-green">check</i>';
				 } else {
					$tvars['tvar_requirementsIcon']		= '<i class="f7-icons size-22 color-red">close</i>';
				}
				if ($check_cost['ok']) {
					$tvars['tvar_costIcon']		= '<i class="f7-icons size-22 color-green">check</i>';
				} else {
					$tvars['tvar_costIcon']		= '<i class="f7-icons size-22 color-red">close</i>';
				}
				
				$tvars['tvar_nodeID'] 				= $this->node->data['id'];
				$tvars['tvar_slotID'] 				= $this->data['slotId'];
				$tvars['tvar_nodeFaction'] 			= $this->node->data['faction'];
				$tvars['tvar_costData'] 			= $costData;
				$tvars['tvar_requirementsData'] 	= $requirementsData;
				$tvars['tvar_disableData'] 			= $disableData;
				$tvars['tvar_cid'] 					= $cid;
				$tvars['tvar_componentName'] 		= $gl["components"][$this->node->data['faction']][$cid]["name"];
				$tvars['tvar_componentDescription'] = $gl["components"][$this->node->data['faction']][$cid]["description"];
				$tvars['tvar_duration'] 			= misc::time_format((($component['duration']-$component['duration']*$this->data['totalIR']) * $game['users']['speed']['craft'])*60);
				$tvars['tvar_compLimit'] 			= $limitData;
				$tvars['tvar_compValue'] 			= $this->node->components[$cid]['value'];
				$tvars['tvar_compStorage'] 			= $component['storage'];
				$tvars['tvar_compResource'] 		= $component['storageResource'];
				$tvars['tvar_compResourceName'] 	= $gl["resources"][$component['storageResource']]["name"];
				$tvars['tvar_sub_popupswiper'] 		.=  $d13->tpl->render_subpage("sub.module.craft", $tvars);
				
			
			}
		}
		
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.swiper"), $tvars));
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));

		
		return $tvars['tvar_sub_popupswiper'];
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		
		global $d13, $game, $ui, $gl;
		
		$html = '';
	
		// - - - Queue
		if (count($this->node->queue['craft'])) {
			$html = '';
			foreach ($this->node->queue['craft'] as $item) {
				if (!$item['stage']) {
					$stage=$ui['craft'];
				} else {
					$stage=$ui['remove'];
				}
				$remaining=$item['start']+$item['duration']*60-time();
				$html .= '<div>'.$stage.' '.$item['quantity'].' '.$gl["components"][$this->node->data['faction']][$item['component']]["name"].'(s) <span id="craft_'.$item['id'].'">'.implode(':', misc::sToHMS($remaining)).'</span><script type="text/javascript">timedJump("craft_'.$item['id'].'", "?p=module&action=get&nodeId='.$this->node->data['id'].'&slotId='.$_GET['slotId'].'");</script> <a class="external" href="?p=module&action=cancelComponent&nodeId='.$this->node->data['id'].'&slotId='.$_GET['slotId'].'&craftId='.$item['id'].'"><i class="f7-icons size-16">close_round</i></a></div>';
			}
		}
		// - - - Popover if Queue empty
		if ($html == '') {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				#$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
				$html .= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">'.misc::getlang("craft").'</a>';
				#$html .= '</p>';
			} else {
				#$html .= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button active">'.misc::getlang("craft").'</a>';
				#$html .= '</p>';
			}
		}
	
		return $html;
	}

}

//----------------------------------------------------------------------------------------
// d13_module_train
// 
//----------------------------------------------------------------------------------------
class d13_module_train extends d13_module {

	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		
		global $d13, $game, $ui, $gl;
		
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$html = '';
		
		if ($this->data['options']['inventoryList']) {
			
			foreach ($this->node->units as $uid=>$unit) {
				if (in_array($uid, $game['modules'][$this->node->data['faction']][$this->data['moduleId']]['units'])) {
					if ($unit['value'] > 0) {
						$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/units/'.$this->node->data['faction'].'/'.$uid.'.png" title="'.$gl['units'][$this->node->data['faction']][$uid]['name'].'">';
						$tvars['tvar_listLabel'] 		= $gl['units'][$this->node->data['faction']][$uid]['name'];
						$tvars['tvar_listAmount'] 		= $unit['value'];
						$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
					}
				}
			}
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));
	
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		}
		
		return $html;
		
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		
		global $d13, $game, $gl, $ui;
		
		$html = '';
		$tvars = array();
		$tvars['tvar_sub_popupswiper'] = '';
		
		foreach ($game['units'][$this->node->data['faction']] as $uid=>$unit) {
			if (in_array($uid, $game['modules'][$this->node->data['faction']][$this->data['moduleId']]['units'])) {
			
				$unit = new d13_unit($uid, $this->node);
			
				//- - - - - Assemble Costs
				$get_costs = $unit->getCost();
				$costData = '';
				foreach ($get_costs as $cost) {
					$costData .= '<div class="cell">'.$cost['cost'].'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$cost['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['icon'].'" title="'.$cost['name'].'"></a></div>';
				}
			
				//- - - - - Assemble Requirements
				$get_requirements = $unit->getRequirements();
				if (empty($get_requirements)) {
					$requirementsData = $ui['none'];
				} else {
					$requirementsData = '';
				}
				foreach ($get_requirements as $req) {
					$requirementsData .= '<div class="cell">'.$req['value'].'</div><div class="cell"><a class="tooltip-left" data-tooltip="'.$req['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$req['type'].'/'.$this->node->data['faction'].'/'.$req['icon'].'" title="'.$req['type_name'].' - '.$req['name'].'"></a></div>';
				}

				//- - - - - Check Permissions
				$disableData='';
			
				$check_requirements = $unit->getCheckRequirements();
				$check_cost 		= $unit->getCheckCost();

				if ($check_requirements && $check_cost) {
					$disableData = '';
				} else {
					$disableData = 'disabled';
				}

				if ($check_requirements) {
					$tvars['tvar_requirementsIcon']	= '<i class="f7-icons size-22 color-green">check</i>';
				 } else {
					$tvars['tvar_requirementsIcon']	= '<i class="f7-icons size-22 color-red">close</i>';
				}
				if ($check_cost) {
					$tvars['tvar_costIcon']			= '<i class="f7-icons size-22 color-green">check</i>';
				} else {
					$tvars['tvar_costIcon']			= '<i class="f7-icons size-22 color-red">close</i>';
				}
			
				//- - - - - Check Upgrades
				$upgradeData = array();
				$upgradeData = $unit->getUpgrades();

				$tvars['tvar_unitHPPlus'] 				= "[+". $upgradeData['hp'] . "]";
				$tvars['tvar_unitDamagePlus'] 			= "[+". $upgradeData['damage'] . "]";
				$tvars['tvar_unitArmorPlus'] 			= "[+". $upgradeData['armor'] . "]";
				$tvars['tvar_unitSpeedPlus'] 			= "[+". $upgradeData['speed'] . "]";
			
				//- - - - - Setup Template Data
				$tvars['tvar_nodeFaction'] 				= $this->node->data['faction'];
				$tvars['tvar_costData'] 				= $costData;
				$tvars['tvar_requirementsData'] 		= $requirementsData;
				$tvars['tvar_disableData'] 				= $disableData;
				$tvars['tvar_uid'] 						= $uid;
				$tvars['tvar_unitName'] 				= $unit->data['name'];
				$tvars['tvar_unitDescription'] 			= $unit->data['description'];
				$tvars['tvar_unitValue'] 				= $this->node->units[$uid]['value'];
				$tvars['tvar_unitType'] 				= $unit->data['type'];
				$tvars['tvar_unitClass'] 				= $unit->data['class'];
				$tvars['tvar_unitHP'] 					= $unit->data['hp'];
				$tvars['tvar_unitDamage'] 				= $unit->data['damage'];
				$tvars['tvar_unitArmor'] 				= $unit->data['armor'];
				$tvars['tvar_unitSpeed'] 				= $unit->data['speed'];
				$tvars['tvar_unitLimit'] 				= $unit->getMaxProduction();
				$tvars['tvar_unitDuration'] 			= misc::time_format((($unit->data['duration'] - $unit->data['duration'] * $this->data['totalIR']) * $game['users']['speed']['train']) * 60);
				$tvars['tvar_unitUpkeep'] 				= $unit->data['upkeep'];
				$tvars['tvar_unitUpkeepResource'] 		= $unit->data['upkeepResource'];
				$tvars['tvar_unitUpkeepResourceName']	= $gl['resources'][$unit->data['upkeepResource']]['name'];
				
				$tvars['tvar_sub_popupswiper'] 			.= $d13->tpl->render_subpage("sub.module.train", $tvars);
				
			}
		}
		
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.swiper"), $tvars));
	$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
		return $tvars['tvar_sub_popupswiper'];
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		
		$html = '';

		if (count($this->node->queue['train'])) {
			foreach ($this->node->queue['train'] as $item) {
				if (!$item['stage']) {
					$stage=$ui['train'];
				} else {
					$stage=$ui['remove'];
				}
				$remaining=$item['start']+$item['duration']*60-time();
				$html .= '<div>'.$stage.' '.$item['quantity'].$gl["units"][$this->node->data['faction']][$item['unit']]["name"].' <span id="train_'.$item['id'].'">'.implode(':', misc::sToHMS($remaining)).'</span><script type="text/javascript">timedJump("train_'.$item['id'].'", "?p=module&action=get&nodeId='.$this->node->data['id'].'&slotId='.$_GET['slotId'].'");</script> <a class="external link" href="?p=module&action=cancelUnit&nodeId='.$this->node->data['id'].'&slotId='.$_GET['slotId'].'&trainId='.$item['id'].'"><i class="f7-icons size-16">close_round</i></a></div>';
			}
		}

		// - - - Popover if Queue empty
		if ($html == '') {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
				$html .= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">'.misc::getlang("train").'</a>';
				$html .= '</p>';
			} else {
				$html .= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button active">'.misc::getlang("train").'</a>';
				$html .= '</p>';
			}
		}
		
		return $html;
		
	}

}

//----------------------------------------------------------------------------------------
// d13_module_research
// 
//----------------------------------------------------------------------------------------
class d13_module_research extends d13_module {


	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		
		global $d13, $game, $gl, $ui;
		
		$html = '';
		
		//- - - - - Check Inventory
		$tvars['tvar_sub_popuplist'] = '';

		if ($this->data['options']['inventoryList']) {
			//- - - - - Popover if Inventory filled
			foreach ($game['technologies'][$this->node->data['faction']] as $uid=>$unit) {
				if ($unit['active'] && in_array($uid, $game['modules'][$this->node->data['faction']][$this->data['moduleId']]['technologies'])) {
					if ($this->node->technologies[$uid]['level'] > 0) {
						$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/technologies/'.$this->node->data['faction'].'/'.$uid.'.png" title="'.$gl['technologies'][$this->node->data['faction']][$uid]['name'].'">';
						$tvars['tvar_listLabel'] 		= $gl['technologies'][$this->node->data['faction']][$uid]['name'];
						$tvars['tvar_listAmount'] 		= $this->node->technologies[$uid]['level'];
						$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
					}
				}
			}
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));

			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		}
	
		return $html;
		
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		
		global $d13, $game, $gl, $ui;
		
		$html = '';
		
		// - - - Research Popup
		$tvars['tvar_sub_popupswiper'] = "";
	
		foreach ($game['technologies'][$this->node->data['faction']] as $tid=>$technology) {
	
			if ($technology['active'] && in_array($tid, $game['modules'][$this->node->data['faction']][$this->data['moduleId']]['technologies'])) {
			
				//- - - - - Check Cost & Requirements
				$costData='';
				foreach ($technology['cost'] as $key=>$cost) {
					$costData.='<div class="cell"><a class="tooltip-left" data-tooltip="'.$gl["resources"][$cost['resource']]["name"].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></a></div><div class="cell">'.($cost['value']*$game['users']['cost']['research']).'</div>';
				}
				if (!count($technology['requirements'])) {
					$requirementsData=$ui['none'];
				} else {
					$requirementsData='';
					foreach ($technology['requirements'] as $key=>$requirement) {
						$requirementsData.='<div class="cell"><a class="tooltip-left" data-tooltip="'.$gl[$requirement['type']][$this->node->data['faction']][$requirement['id']]['name'].'"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/'.$requirement['type'].'/'.$this->node->data['faction'].'/'.$requirement['id'].'.png" title="'.$ui[$requirement['type']].' - '.$gl[$requirement['type']][$this->node->data['faction']][$requirement['id']]['name'].'"></a></div><div class="cell">'.$requirement['level'].'</div>';
					}
				}
			
				//- - - - - Check Permissions
				$linkData='';
				$check_requirements = NULL;
				$check_cost = NULL;
		
				$check_requirements = $this->node->checkRequirements($technology['requirements']);
				$check_cost 		= $this->node->checkCost($technology['cost'], 'research');
		
				if ($check_requirements['ok'] && $check_cost['ok'] && $this->node->technologies[$tid]['level'] < $technology['maxLevel']) {
					$linkData .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
					$linkData .= '<a href="?p=module&action=addTechnology&nodeId='.$this->node->data['id'].'&slotId='.$this->data['slotId'].'&technologyId='.$tid.'" class="external button active">'.misc::getlang("research").'</a>';
					$linkData .= '</p>';
				} else {
					$linkData .= '<p class="buttons-row theme-gray">';
					$linkData .= '<a href="#" class="button active">'.misc::getlang("research").'</a>';
					$linkData .= '</p>';
				}

				if ($check_requirements['ok']) {
					$tvars['tvar_requirementsIcon']		= '<i class="f7-icons size-22 color-green">check</i>';
				 } else {
					$tvars['tvar_requirementsIcon']		= '<i class="f7-icons size-22 color-red">close</i>';
				}
				if ($check_cost['ok']) {
					$tvars['tvar_costIcon']		= '<i class="f7-icons size-22 color-green">check</i>';
				} else {
					$tvars['tvar_costIcon']		= '<i class="f7-icons size-22 color-red">close</i>';
				}
				
				$tvars['tvar_nodeFaction'] 			= $this->node->data['faction'];
				$tvars['tvar_linkData'] 		= $linkData;
				$tvars['tvar_costData'] 		= $costData;
				$tvars['tvar_requirementsData'] = $requirementsData;
				$tvars['tvar_tid'] 				= $tid;
				$tvars['tvar_techName'] 		= $gl['technologies'][$this->node->data['faction']][$tid]['name'];
				$tvars['tvar_techDescription'] 	= $gl['technologies'][$this->node->data['faction']][$tid]['description'];
				$tvars['tvar_techTier'] 		= $this->node->technologies[$tid]['level'];
				$tvars['tvar_techMaxTier'] 		= $technology['maxLevel'];
				$tvars['tvar_techDuration'] 	= misc::time_format((($technology['duration']-$technology['duration'] * $this->data['totalIR'])*$game['users']['speed']['research'])*60);
				$tvars['tvar_sub_popupswiper'] .= $d13->tpl->render_subpage("sub.module.research", $tvars);
			
			}
		}
		
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.swiper"), $tvars));
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
		
		return $tvars['tvar_sub_popupswiper'];
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		
		global $gl;
		
		$html = '';
		
		// - - - Queue
		if (count($this->node->queue['research'])) {
			foreach ($this->node->queue['research'] as $item) {
				$remaining=$item['start']+$item['duration']*60-time();
				$html .= '<div>'.misc::getlang("research").' '.$gl['technologies'][$this->node->data['faction']][$item['technology']]["name"].' <span id="research_'.$item['node'].'_'.$item['technology'].'">'.implode(':', misc::sToHMS($remaining)).'</span> <script type="text/javascript">timedJump("research_'.$item['node'].'_'.$item['technology'].'", "?p=module&action=get&nodeId='.$this->node->data['id'].'&slotId='.$_GET['slotId'].'");</script> <a class="external" href="?p=module&action=cancelTechnology&nodeId='.$this->node->data['id'].'&slotId='.$_GET['slotId'].'&technologyId='.$item['technology'].'"><i class="f7-icons size-16">close_round</i></a></div>';
			}
		}
	
		// - - - Popover if Queue empty
		if ($html == '') {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
				$html .= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">'.misc::getlang("research").'</a>';
				$html .= '</p>';
			} else {
				$html .= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button active">'.misc::getlang("research").'</a>';
				$html .= '</p>';
			}
		}
	
		return $html;
	}
	
}

//----------------------------------------------------------------------------------------
// d13_module_alliance
// 
//----------------------------------------------------------------------------------------
class d13_module_alliance extends d13_module {

	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		return '';
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		
		global $d13;
		
		$tvars = array();
		$html = '';
		
		//- - - - Option: Alliance List
		if ($this->data['options']['allianceGet']) {
			$tvars['tvar_Label']				= misc::getlang("get") . ' ' . misc::getlang("alliance");
			$tvars['tvar_Link']					= '?p=alliance&action=get&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("get") . ' ' . misc::getlang("alliance");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Alliance Edit
		if ($this->data['options']['allianceEdit']) {
			$tvars['tvar_Label']				= misc::getlang("edit") . ' ' . misc::getlang("alliance");
			$tvars['tvar_Link']					= '?p=alliance&action=add&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("edit") . ' ' . misc::getlang("alliance");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Alliance Remove
		if ($this->data['options']['allianceRemove']) {
			$tvars['tvar_Label']				= misc::getlang("remove") . ' ' . misc::getlang("alliance");
			$tvars['tvar_Link']					= '?p=alliance&action=remove&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("remove") . ' ' . misc::getlang("alliance");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Alliance Invite
		if ($this->data['options']['allianceInvite']) {
			$tvars['tvar_Label']				= misc::getlang("invite") . ' ' . misc::getlang("members");
			$tvars['tvar_Link']					= '?p=alliance&action=addInvitation&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("invite") . ' ' . misc::getlang("members");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Alliance Go to War
		if ($this->data['options']['allianceWar']) {
			$tvars['tvar_Label']				= misc::getlang("warDeclaration");
			$tvars['tvar_Link']					= '?p=alliance&action=addWar&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("warDeclaration");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: 
		//- - - - Option: 
		//- - - - Option: 
	
		return $html;
	
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}

}

//----------------------------------------------------------------------------------------
// d13_module_command
// 
//----------------------------------------------------------------------------------------
class d13_module_command extends d13_module {
	
	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
	
		global $d13, $gl;
		
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$html = '';
		
		if ($this->data['options']['inventoryList']) {
			foreach ($this->node->resources as $uid=>$unit) {
				if ($unit['value'] > 0) {
					$tvars['tvar_listImage'] 		= '<img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$uid.'.png" title="'.$gl['resources'][$uid]['name'].'">';
					$tvars['tvar_listLabel'] 		= $gl['resources'][$uid]['name'];
					$tvars['tvar_listAmount'] 		= floor($unit['value']);
					$tvars['tvar_sub_popuplist'] 	.= $d13->tpl->parse($d13->tpl->get("sub.module.listcontent"), $tvars);
				}
			}
			$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.popup.list"), $tvars));
	
			$html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';
			$html .= '<a href="#" class="button active open-popup" data-popup=".popup-list">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		} else {
			$html .= '<p class="buttons-row theme-gray">';
			$html .= '<a href="#" class="button active">'.misc::getlang("inventory").'</a>';
			$html .= '</p>';
		}

		return $html;
		
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		
		global $d13, $game;
		
		$tvars = array();
		$html = '';
		
		//- - - - Option: Remove Node
		$nodes = $this->node->getList($_SESSION[CONST_PREFIX.'User']['id']);
		$t = count($nodes);
		if ($this->data['options']['nodeRemove'] && $t > 1) {
			$tvars['tvar_Label']				= misc::getlang("remove") . ' ' . misc::getlang("node");
			$tvars['tvar_Link']					= '?p=node&action=remove&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("remove");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Move Node
		if ($this->data['options']['nodeMove']) {
			$tvars['tvar_Label']				= misc::getlang("move") . ' ' . misc::getlang("node");
			$tvars['tvar_Link']					= '?p=node&action=move&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("move");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Edit Node
		if ($this->data['options']['nodeEdit']) {
			$tvars['tvar_Label']				= misc::getlang("edit") . ' ' . misc::getlang("node");
			$tvars['tvar_Link']					= '?p=node&action=set&nodeId='.$this->node->data['id'];
			$tvars['tvar_LinkLabel']			= misc::getlang("edit");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
	
		//- - - - Option: Add new Node
		$nodes = $this->node->getList($_SESSION[CONST_PREFIX.'User']['id']);
		if (count($nodes)< $game['users']['maxNodes']) {
			$tvars['tvar_Label']				= misc::getlang("add") . ' ' . misc::getlang("node");
			if ($game['options']['gridSystem'] == 1) {
				$tvars['tvar_Link']				= '?p=node&action=add';
			} else {
				$tvars['tvar_Link']				= '?p=node&action=random';
			}
			$tvars['tvar_LinkLabel']			= misc::getlang("add");
			$html								.= $d13->tpl->parse($d13->tpl->get("sub.module.itemcontent"), $tvars);
		}
		
		return $html;
	
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}

}

//----------------------------------------------------------------------------------------
// d13_module_defense
// 
//----------------------------------------------------------------------------------------
class d13_module_defense extends d13_module {

	//----------------------------------------------------------------------------------------
	// getInventory
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getInventory() {
		return '';
	}
	
	//----------------------------------------------------------------------------------------
	// getOptions
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getOptions() {
		return '';
	}
	
	//----------------------------------------------------------------------------------------
	// getPopup
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getPopup() {
		return '';
	}

	//----------------------------------------------------------------------------------------
	// getQueue
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function getQueue() {
		return '';
	}
	
}

//=====================================================================================EOF

?>