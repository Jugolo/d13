<?php

// ========================================================================================
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
// ========================================================================================
// ----------------------------------------------------------------------------------------
// d13_module_factory
//
// ----------------------------------------------------------------------------------------

class d13_module_factory

{
	public static

	function create($moduleId, $slotId, $node)
	{
		global $d13;
		$type = $d13->getModule($node->data['faction'], $moduleId, 'type');
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

		case 'trade':
			return new d13_module_trade($moduleId, $slotId, $type, $node);
			break;
			
		case 'storvest':
			return new d13_module_storvest($moduleId, $slotId, $type, $node);
			break;

		case 'market':
			return new d13_module_market($moduleId, $slotId, $type, $node);
			break;

		default:
			return NULL;
			break;
		}
	}
}

// ----------------------------------------------------------------------------------------
// d13_module
//
// ----------------------------------------------------------------------------------------

class d13_module

{
	public $data, $node, $checkRequirements, $checkCost;

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct($moduleId, $slotId, $type, $node)
	{
		$this->setNode($node);
		$this->setAttributes($moduleId, $slotId, $type);
		$this->getModuleImage();
	}

	// ----------------------------------------------------------------------------------------
	// setNode
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function setNode($node)
	{
		$this->node = $node;
		$this->node->getModules();
		$this->node->getTechnologies();
		$this->node->getComponents();
		$this->node->getUnits();
	}

	// ----------------------------------------------------------------------------------------
	// checkUpgrades
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkUpgrades()
	{
		global $d13;

		// - - - - - - - - - - - - - - - COST & ATTRIBUTES
		foreach($d13->getUpgrade($this->node->data['faction']) as $upgrade) {
			if ($upgrade['active'] && $upgrade['type'] == $this->data['type'] && $upgrade['id'] == $this->data['moduleId']) {
				// - - - - - - - - - - - - - - - COST
				if (isset($upgrade['cost'])) {
					$this->data['cost_upgrade'] = $upgrade['cost'];
				}
				// - - - - - - - - - - - - - - - ATTRIBUTES
				if (isset($upgrade['attributes'])) {
					$this->data['attributes_upgrade'] = $upgrade['attributes'];
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// setAttributes
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function setAttributes($moduleId, $slotId, $type)
	{
		global $d13;
		$this->data = array();
		$this->data = $d13->getModule($this->node->data['faction'], $moduleId);
		$this->data['cost_upgrade'] = array();
		$this->data['attributes_upgrade'] = array();
		$this->data['moduleInput'] = 0;
		$this->data['moduleInputLimit'] = 0;
		$this->data['moduleInputName'] = '';
		$this->data['moduleMaxInput'] = 0;
		$this->data['moduleSlotInput'] = 0;
		$this->data['moduleProduction'] = 0;
		$this->data['moduleStorage'] = 0;
		$this->data['moduleId'] = $moduleId;
		$this->data['slotId'] = $slotId;
		$this->data['type'] = $type;
		$this->data['level'] = 0;
		$this->data['cost'] = $this->getCost();
		
		// - - - - - - - - - - - - - - - APPLY ATTRIBUTE UPGRADES
		if ($this->node->modules[$slotId]['level'] > 0) {
			$this->data['level'] = $this->node->modules[$slotId]['level'];
			$this->checkUpgrades();
			$this->data['cost'] = $this->getCost(true);
			foreach($this->data['attributes_upgrade'] as $attribute) {
				$this->data[$attribute['stat']] += $attribute['value'] * ($this->data['level']);
			}
		}
	
		$this->data['moduleImage'] = '';
		$this->data['name'] = $d13->getLangGL('modules', $this->node->data['faction'], $this->data['moduleId'], 'name');
		$this->data['description'] = $d13->getLangGL('modules', $this->node->data['faction'], $this->data['moduleId'], 'description');
		$this->data['totalIR'] = $this->data['ratio'];
		$this->data['inputLimit'] = floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'] + $this->node->modules[$this->data['slotId']]['input']));

		$this->data['costData'] = $this->node->checkCost($this->data['cost'], 'build');
		$this->data['reqData'] = $this->node->checkRequirements($this->data['requirements']);
		
		if (isset($this->data['inputResource'])) {
			$this->data['moduleInput'] = $this->data['inputResource'];
			$this->data['moduleInputLimit'] = floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'] + $this->node->modules[$slotId]['input']));
			$this->data['moduleInputName'] = $d13->getLangGL('resources', $this->data['inputResource'], 'name');
			$this->data['moduleSlotInput'] = $this->node->modules[$slotId]['input'];
			$this->data['totalIR'] = $this->node->modules[$slotId]['input'] * $this->data['ratio'];
		}

		
		if (isset($this->data['outputResource'])) {
			$this->data['moduleProduction'] = $this->data['ratio'] * $d13->getGeneral('factors', 'production') * $this->node->modules[$slotId]['input'];
			$i = 0;
			foreach($this->data['outputResource'] as $res) {
				$this->data['moduleOutput' . $i] = $res;
				$this->data['moduleOutputName' . $i] = $d13->getLangGL("resources", $res, "name");
				$i++;
			}
		}

		if (isset($this->data['storedResource'])) {
			$this->data['moduleStorage'] = $this->data['ratio'] * $this->node->modules[$slotId]['input'];
			$i = 0;
			foreach($this->data['storedResource'] as $res) {
				$this->data['moduleStorageRes' . $i] = $res;
				$this->data['moduleStorageResName' . $i] = $d13->getLangGL("resources", $res, "name");
				$i++;
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTemplateVariables()
	{
		global $d13;
		$tvars = array();
		$tvars = $this->getStats();
		$tvars['tvar_demolishLink'] = '';
		$tvars['tvar_inventoryLink'] = '';
		$tvars['tvar_linkData'] = '';
		$tvars['tvar_moduleItemContent'] = '';
		
		$tvars['tvar_demolishLink'] = $this->getDemolish();
		$tvars['tvar_inventoryLink'] = $this->getInventory();
		$tvars['tvar_linkData'] = $this->getModuleUpgrade();
		$tvars['tvar_moduleItemContent'] = $this->getOptions();
		if ($this->data['level'] > 0) {
		$tvars['tvar_queue'] = $this->getQueue();
		$tvars['tvar_popup'] = $this->getPopup();
		}
		$tvars['tvar_moduleImage'] = $this->data['image'];
		$tvars['tvar_moduleDescription'] = $this->data['description'];
		$tvars['tvar_moduleID'] = $this->data['moduleId'];
		$tvars['tvar_moduleInput'] = $this->data['moduleInput'];
		$tvars['tvar_moduleInputLimit'] = $this->data['moduleInputLimit'];
		$tvars['tvar_moduleInputName'] = $this->data['moduleInputName'];
		$tvars['tvar_moduleMaxInput'] = $this->data['maxInput'];
		$tvars['tvar_moduleName'] = $this->data['name'];
		$tvars['tvar_moduleProduction'] = $this->data['moduleProduction'];
		$tvars['tvar_moduleRatio'] = $this->data['ratio'];
		$tvars['tvar_moduleSlotInput'] = $this->data['moduleSlotInput'];
		$tvars['tvar_moduleStorage'] = $this->data['moduleStorage'];
		$tvars['tvar_totalIR'] = $this->data['totalIR'];
		$tvars['tvar_costData'] = $this->getCostList();
		$tvars['tvar_requirementsData'] = $this->getRequirementsList();
		$tvars['tvar_outputData'] = $this->getOutputList();
		$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
		$tvars['tvar_nodeID'] = $this->node->data['id'];
		$tvars['tvar_slotID'] = $this->data['slotId'];
		$tvars['tvar_moduleLevel'] = $this->data['level'];
		$tvars['tvar_moduleMaxLevel'] = $this->data['maxLevel'];
		$tvars['tvar_moduleMaxInstances'] = $this->data['maxInstances'];
		$tvars['tvar_moduleDuration'] = $this->data['duration'];
		$tvars['tvar_moduleSalvage'] = $this->data['salvage'];
		$tvars['tvar_moduleRemoveDuration'] = $this->data['removeDuration'];
		
		if ($this->data['reqData']['ok']) {
			$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
		}
		else {
			$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
		}

		if ($this->data['costData']['ok']) {
			$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
		}
		else {
			$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
		}

		if (isset($this->data['storedResource'])) {
			$i = 0;
			while (isset($this->data['moduleStorageRes' . $i])) {
				$tvars['tvar_moduleStorageRes' . $i] = $this->data['moduleStorageRes' . $i];
				$tvars['tvar_moduleStorageResName' . $i] = $this->data['moduleStorageResName' . $i];
				$i++;
			}
		}

		if (isset($this->data['outputResource'])) {
			$i = 0;
			while (isset($this->data['moduleOutput' . $i])) {
				$tvars['tvar_moduleOutput' . $i] = $this->data['moduleOutput' . $i];
				$tvars['tvar_moduleOutputName' . $i] = $this->data['moduleOutputName' . $i];
				$i++;
			}
		}

		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// getImage
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getModuleImage()
	{
		global $d13;
		$this->data['image'] = '';
		foreach($this->data['images'] as $image) {
			if ($image['level'] <= $this->data['level']) {
				$this->data['image'] = $image['image'];
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// getCheckDemolish
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getDemolish()
	{
		global $d13;
		$html = '';
		
		if ($this->data['level'] > 0) {
			if ($d13->getGeneral('options', 'moduleDemolish')) {
				if ($this->node->modules[$this->data['slotId']]['input'] <= 0) {
					$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$html.= '<a class="external button" href="?p=module&action=remove&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '">' . $d13->getLangUI("removeModule") . '</a>';
					$html.= '</p>';
				} else {
					$html.= '<p class="buttons-row disabled>';
					$html.= '<a class="button" href="#">' . $d13->getLangUI("removeModule") . " " . $d13->getLangUI("impossible") .'</a>';
					$html.= '</p>';
				}
			}
		}

		return $html;
	}
	
	// ----------------------------------------------------------------------------------------
	// getModuleUpgrade
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getModuleUpgrade()
	{
		global $d13;
		$html = '';
		
		if ($this->data['level'] <= 0) {
		
			if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData']['ok'] && $this->data['reqData']['ok'] && ($this->node->getModuleCount($this->data['slotId'], $this->data['moduleId']) < $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'maxInstances'))) {
			
				$tvars['tvar_title'] 			= $d13->getLangUI("addModule");
				$tvars['tvar_moduleInputName'] 	= $this->data['moduleInputName'];
				$tvars['tvar_moduleInputImage'] = $d13->getResource($this->data['moduleInput'], 'image');
				$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
				$tvars['tvar_costList'] 		= $this->getCostList();
				$tvars['tvar_moduleAction'] 	= '?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
				$tvars['tvar_id'] 				= $this->data['moduleId'];
				$tvars['tvar_moduleInput']		= $this->data['moduleSlotInput'];
				$tvars['tvar_moduleLimit'] 		= floor(min($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput'],$this->data['maxInput']));
				$tvars['tvar_disableData'] 		= '';
				$d13->logger("ID-build:".$this->data['moduleId']); //DEBUG
				$d13->templateInject($d13->templateSubpage("sub.popup.build" , $tvars));
			
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-build-'.$this->data['moduleId'].'">' . $d13->getLangUI("addModule") . '</a>';
				$html.= '</p>';
			}
			else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("addModule") . " " . $d13->getLangUI("impossible") . '</a>';
				$html.= '</p>';
			}
			
			return $html;
			
		} else {
		
			
			if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData']['ok'] && $this->data['reqData']['ok'] && $this->node->modules[$this->data['slotId']]['level'] < $this->data['maxLevel'] && $this->data['maxLevel'] > 1) {
				
				$tvars['tvar_title'] 			= $d13->getLangUI("upgrade");
				$tvars['tvar_moduleInputName'] 	= $this->data['moduleInputName'];
				$tvars['tvar_moduleInputImage'] = $d13->getResource($this->data['moduleInput'], 'image');
				$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
				$tvars['tvar_costList'] 		= $this->getCostList(true);
				$tvars['tvar_moduleAction'] 	= '?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
				$tvars['tvar_id'] 				= $this->data['moduleId'];
				$tvars['tvar_moduleInput']		= $this->data['moduleSlotInput'];
				$tvars['tvar_moduleLimit'] 		= floor(min($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput'],$this->data['maxInput']));
				$tvars['tvar_disableData'] 		= '';
			$d13->logger("ID-build:".$this->data['moduleId']); //UPGRADE
				$d13->templateInject($d13->templateSubpage("sub.popup.build" , $tvars));
			
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-build-'.$this->data['moduleId'].'">' . $d13->getLangUI("upgrade") . '</a>';
				$html.= '</p>';
			}
			else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("upgrade") . " " . $d13->getLangUI("impossible") . '</a>';
				$html.= '</p>';
			}
			
			return $html;
		
		}
		
		return $html;

	}

	// ----------------------------------------------------------------------------------------
	// getCostList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCostList()
	{
		global $d13;
		$html = '';
		if ($d13->getGeneral('options', 'moduleUpgrade') && $this->data['level'] < $this->data['maxLevel']) {
			
			foreach($this->data['cost'] as $key => $cost) {
				$html.= '<div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></a></div><div class="cell">' . $cost['value'] . '</div>';
			}
		}

		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getRequirementsList
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getRequirementsList()
	{
		global $d13;
		$html = '';
		if (!count($this->data['requirements'])) {
			$html = $d13->getLangUI('none');
		}
		else {
			foreach($this->data['requirements'] as $key => $requirement) {
				if (isset($requirement['level'])) {
					$value = $requirement['level'];
				}
				else {
					$value = $requirement['value'];
				}

				if ($requirement['type'] == 'modules') {
					$images = array();
					$images = $d13->getModule($this->node->data['faction'], $requirement['id'], 'images');
					$image = $images[0]['image'];
				}
				else {
					$image = $requirement['id']; //change later to image
				}

				$html.= '<div class="cell">' . $value . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $requirement['type'] . '/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangUI($requirement['type']) . ' - ' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"></a></div>';
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
	}

	// ----------------------------------------------------------------------------------------
	// getCost
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getCost($upgrade = false)
	{
		global $d13;
		$cost_array = array();
		foreach($this->data['cost'] as $key => $cost) {
			$tmp_array = array();
			$tmp_array['resource'] = $cost['resource'];
			$tmp_array['value'] = $cost['value'] * $d13->getGeneral('users', 'cost', 'build');
			$tmp_array['name'] = $d13->getLangGL('resources', $cost['resource'], 'name');
			$tmp_array['icon'] = $cost['resource'] . '.png';
			$tmp_array['factor'] = 1;
			if ($upgrade) {
				foreach($this->data['cost_upgrade'] as $key => $upcost) {
					$tmp2_array = array();
					$tmp2_array['resource'] = $upcost['resource'];
					$tmp2_array['value'] = $upcost['value'] * $d13->getGeneral('users', 'cost', 'build');
					$tmp2_array['name'] = $d13->getLangGL('resources', $upcost['resource'], 'name');
					$tmp2_array['icon'] = $upcost['resource'] . '.png';
					$tmp2_array['factor'] = $upcost['factor'];
					if ($tmp_array['resource'] == $tmp2_array['resource']) {
						$tmp_array['value'] = $tmp_array['value'] + floor($tmp2_array['value'] * $tmp2_array['factor'] * $this->data['level']);
					}
				}
			}

			$cost_array[] = $tmp_array;
		}

		return $cost_array;
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTemplate()
	{
		return "module.get." . $this->data['type'];
	}

	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
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
	// getStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getStats()
	{
		return '';
	}
}

// ========================================================================================
//									DERIVED MODULE CLASSES
// ========================================================================================
// ----------------------------------------------------------------------------------------
// d13_module_warfare
//
// ----------------------------------------------------------------------------------------

class d13_module_warfare extends d13_module

{

	// ----------------------------------------------------------------------------------------
	// getOptions
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOptions()
	{
		global $d13;
		$tvars = array();
		$html = '';

		// - - - - Option: Raid

		if ($this->data['options']['combatRaid']) {
			$tvars['tvar_Label'] = $d13->getLangUI("launch") . ' ' . $d13->getLangUI("raid");
			$tvars['tvar_Link'] = '?p=combat&action=add&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("set");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Conquer
		// - - - - Option: Raze
		// - - - - Option: Scout
		// - - - - Option:
		// - - - - Option:

		return $html;
	}

	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
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
		return $d13->getLangUI("none");
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_storage
//
// ----------------------------------------------------------------------------------------

class d13_module_storage extends d13_module

{

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
		$html = '';
		$i=0;
		
		if (isset($this->data['options']['inventoryList']) && $this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $uid => $unit) {
				if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $uid . '.png" title="' . $d13->getLangGL('resources', $uid, 'name') . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('resources', $uid, 'name');
					$tvars['tvar_listAmount'] = floor($unit['value']);
					$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
					$i++;
				}
			}
			if ($i > 0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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
		if (isset($this->data['storedResource'])) {
			foreach($this->data['storedResource'] as $res) {
				if ($d13->getResource($res, 'active')) {
					$html.= $d13->getLangUI('storage') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('storage') . ' ' . $d13->getLangGL("resources", $res, "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_harvest
//
// ----------------------------------------------------------------------------------------

class d13_module_harvest extends d13_module

{

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
		$html = '';
		$i=0;
		
		if ($this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $rid => $res) {
				if ($d13->getResourceByID($res['id'], 'active') && $res['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $rid . '.png" title="' . $d13->getLangGL('resources', $rid, 'name') . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('resources', $rid, 'name');
					$tvars['tvar_listAmount'] = floor($res['value']);
					$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
					$i++;
				}
			}
			if ($i>0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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
		if (isset($this->data['outputResource'])) {
			foreach($this->data['outputResource'] as $res) {
				if ($d13->getResource($res, 'active')) {

					// if ($d13->data->resources->getbyid('active', $res)) {

					$html.= '' . $d13->getLangUI('production') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('production') . " " . $d13->getLangGL("resources", $res, "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_craft
//
// ----------------------------------------------------------------------------------------

class d13_module_craft extends d13_module

{

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
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach($this->node->components as $uid => $unit) {
				if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
					if ($unit['value'] > 0) {
						
						$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $uid . '.png" title="' . $d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('components', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
						$i++;
					}
				}
			}
			if ($i>0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("component") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("component") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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
		$tvars['tvar_sub_popupswiper'] = '';
		$html = '';

		// - - - Craft Popup

		foreach($d13->getComponent($this->node->data['faction']) as $cid => $component) {
			if (in_array($cid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
				$costData = '';
				foreach($component['cost'] as $key => $cost) {
					$costData.= '<div class="cell">' . ($cost['value'] * $d13->getGeneral('users', 'cost', 'train')) . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></a></div>';
				}

				if (!count($component['requirements'])) {
					$requirementsData = $d13->getLangUI('none');
				}
				else {
					$requirementsData = '';
					foreach($component['requirements'] as $key => $requirement) {
						$requirementsData.= '<div class="cell">' . $requirement['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $requirement['type'] . '/' . $this->node->data['faction'] . '/' . $requirement['id'] . '.png" title="' . $d13->getLangUI($requirement['type']) . ' - ' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"></a></div>';
					}
				}

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
				$tvars['tvar_componentName'] = $d13->getLangGL("components", $this->node->data['faction'], $cid) ["name"];
				$tvars['tvar_componentDescription'] = $d13->getLangGL("components", $this->node->data['faction'], $cid) ["description"];
				$tvars['tvar_duration'] = misc::time_format((($component['duration'] - $component['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'craft')) * 60);
				$tvars['tvar_compLimit'] = $limitData;
				$tvars['tvar_compValue'] = $this->node->components[$cid]['value'];
				$tvars['tvar_compStorage'] = $component['storage'];
				$tvars['tvar_compResource'] = $component['storageResource'];
				$tvars['tvar_compResourceName'] = $d13->getLangGL("resources", $component['storageResource'], "name");
				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.craft", $tvars);
			}
		}

		$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.swiper") , $tvars));
		$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));
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

		// - - - Queue

		if (count($this->node->queue['craft'])) {
			$html = '';
			foreach($this->node->queue['craft'] as $item) {
				if (!$item['stage']) {
					$stage = $d13->getLangUI('craft');
				}
				else {
					$stage = $d13->getLangUI('remove');
				}

				$remaining = $item['start'] + $item['duration'] * 60 - time();
				$html.= '<div>' . $stage . ' ' . $item['quantity'] . ' ' . $d13->getLangGL("components", $this->node->data['faction'], $item['component'], "name") . '(s) <span id="craft_' . $item['id'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("craft_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelComponent&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId'] . '&craftId=' . $item['id'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
			}
		}

		// - - - Popover if Queue empty

		if ($html == '') {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {

				// $html .= '<p class="buttons-row theme-'.$_SESSION[CONST_PREFIX.'User']['color'].'">';

				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">' . $d13->getLangUI("craft") . '</a>';

				// $html .= '</p>';

			}
			else {

				// $html .= '<p class="buttons-row theme-gray">';

				$html.= '<a href="#" class="button active">' . $d13->getLangUI("craft") . '</a>';

				// $html .= '</p>';

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

					// if ($d13->data->components->getbyid('active', $component, $this->node->data['faction'])) {

					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("components", $this->node->data['faction'], $component) ["name"] . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $this->node->data['faction'] . '/' . $component . '.png" title="' . $d13->getLangGL("components", $this->node->data['faction'], $component) ["name"] . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_train
//
// ----------------------------------------------------------------------------------------

class d13_module_train extends d13_module

{

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
		$html = '';
		$i=0;
		
		if ($this->data['options']['inventoryList']) {
			
			foreach($this->node->units as $uid => $unit) {
				if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'units'))) {
					if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
						$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $uid . '.png" title="' . $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
						$i++;
					}
				}
			}
			if ($i>0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("unit") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("unit") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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
		$tvars = array();
		$tvars['tvar_sub_popupswiper'] = '';
		foreach($d13->getUnit($this->node->data['faction']) as $uid => $unit) {
			if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'units'))) {
				$unit = new d13_unit($uid, $this->node);

				// - - - - - Assemble Costs

				$get_costs = $unit->getCost();
				$costData = '';
				foreach($get_costs as $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $cost['name'] . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['icon'] . '" title="' . $cost['name'] . '"></a></div>';
				}

				// - - - - - Assemble Requirements

				$get_requirements = $unit->getRequirements();
				if (empty($get_requirements)) {
					$requirementsData = $d13->getLangUI('none');
				}
				else {
					$requirementsData = '';
				}

				foreach($get_requirements as $req) {
					$requirementsData.= '<div class="cell">' . $req['value'] . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $req['name'] . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $req['type'] . '/' . $this->node->data['faction'] . '/' . $req['icon'] . '" title="' . $req['type_name'] . ' - ' . $req['name'] . '"></a></div>';
				}

				// - - - - - Check Permissions

				$disableData = '';
				$check_requirements = $unit->getCheckRequirements();
				$check_cost = $unit->getCheckCost();
				if ($check_requirements && $check_cost) {
					$disableData = '';
				}
				else {
					$disableData = 'disabled';
				}

				if ($check_requirements) {
					$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
				}
				else {
					$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
				}

				if ($check_cost) {
					$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
				}
				else {
					$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
				}

				// - - - - - Check Upgrades

				$upgradeData = array();
				$upgradeData = $unit->getUpgrades();
				$tvars['tvar_unitHPPlus'] = "[+" . $upgradeData['hp'] . "]";
				$tvars['tvar_unitDamagePlus'] = "[+" . $upgradeData['damage'] . "]";
				$tvars['tvar_unitArmorPlus'] = "[+" . $upgradeData['armor'] . "]";
				$tvars['tvar_unitSpeedPlus'] = "[+" . $upgradeData['speed'] . "]";

				// - - - - - Setup Template Data
				$tvars['tvar_nodeID'] = $this->node->data['id'];
				$tvars['tvar_slotID'] = $this->data['slotId'];
				$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_requirementsData'] = $requirementsData;
				$tvars['tvar_disableData'] = $disableData;
				$tvars['tvar_uid'] = $uid;
				$tvars['tvar_unitName'] = $unit->data['name'];
				$tvars['tvar_unitDescription'] = $unit->data['description'];
				$tvars['tvar_unitValue'] = $this->node->units[$uid]['value'];
				$tvars['tvar_unitType'] = $unit->data['type'];
				$tvars['tvar_unitClass'] = $unit->data['class'];
				$tvars['tvar_unitHP'] = $unit->data['hp'];
				$tvars['tvar_unitDamage'] = $unit->data['damage'];
				$tvars['tvar_unitArmor'] = $unit->data['armor'];
				$tvars['tvar_unitSpeed'] = $unit->data['speed'];
				$tvars['tvar_unitLimit'] = $unit->getMaxProduction();
				$tvars['tvar_unitDuration'] = misc::time_format((($unit->data['duration'] - $unit->data['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'train')) * 60);
				$tvars['tvar_unitUpkeep'] = $unit->data['upkeep'];
				$tvars['tvar_unitUpkeepResource'] = $unit->data['upkeepResource'];
				$tvars['tvar_unitUpkeepResourceName'] = $d13->getLangGL('resources', $unit->data['upkeepResource'], 'name');
				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.train", $tvars);
			}
		}

		$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.swiper") , $tvars));
		$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));
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
		if (count($this->node->queue['train'])) {
			foreach($this->node->queue['train'] as $item) {
				if (!$item['stage']) {
					$stage = $d13->getLangUI('train');
				}
				else {
					$stage = $d13->getLangUI('remove');
				}

				$remaining = $item['start'] + $item['duration'] * 60 - time();
				$html.= '<div>' . $stage . ' ' . $item['quantity'] . $d13->getLangGL("units", $this->node->data['faction'], $item['unit'], "name") . ' <span id="train_' . $item['id'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("train_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId'] . '");</script> <a class="external link" href="?p=module&action=cancelUnit&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId'] . '&trainId=' . $item['id'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
			}
		}

		// - - - Popover if Queue empty

		if ($html == '') {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">' . $d13->getLangUI("train") . '</a>';
				$html.= '</p>';
			}
			else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button active">' . $d13->getLangUI("train") . '</a>';
				$html.= '</p>';
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
		if (isset($this->data['units'])) {
			foreach($this->data['units'] as $unit) {
				if ($d13->getUnit($this->node->data['faction'], $unit, 'active')) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("units", $this->node->data['faction'], $unit) ["name"] . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $unit . '.png" title="' . $d13->getLangGL("units", $this->node->data['faction'], $unit) ["name"] . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_research
//
// ----------------------------------------------------------------------------------------

class d13_module_research extends d13_module

{

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
		$tvars['tvar_sub_popuplist'] = '';
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach($d13->getTechnology($this->node->data['faction']) as $tid => $tech) {
				if ($tech['active'] && in_array($tid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'technologies'))) {
					if ($this->node->technologies[$tid]['level'] > 0) {
						$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $tid . '.png" title="' . $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'];
						$tvars['tvar_listAmount'] = $d13->getLangUI("level") . " " . $this->node->technologies[$tid]['level'];
						$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
						$i++;
					}
				}
			}
			if ($i>0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("technology") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			}else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("technology") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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

		// - - - Research Popup

		$tvars['tvar_sub_popupswiper'] = "";
		foreach($d13->getTechnology($this->node->data['faction']) as $tid => $technology) {
			if ($technology['active'] && in_array($tid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'technologies'))) {

				// - - - - - Check Cost & Requirements

				$costData = '';
				foreach($technology['cost'] as $key => $cost) {
					$costData.= '<div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></a></div><div class="cell">' . ($cost['value'] * $d13->getGeneral('users', 'cost', 'research')) . '</div>';
				}

				if (!count($technology['requirements'])) {
					$requirementsData = $d13->getLangUI('none');
				}
				else {
					$requirementsData = '';
					foreach($technology['requirements'] as $key => $requirement) {
						$requirementsData.= '<div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $requirement['type'] . '/' . $this->node->data['faction'] . '/' . $requirement['id'] . '.png" title="' . $d13->getLangUI($requirement['type']) . ' - ' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"></a></div><div class="cell">' . $requirement['level'] . '</div>';
					}
				}

				// - - - - - Check Permissions

				$linkData = '';
				$check_requirements = NULL;
				$check_cost = NULL;
				$check_requirements = $this->node->checkRequirements($technology['requirements']);
				$check_cost = $this->node->checkCost($technology['cost'], 'research');
				if ($check_requirements['ok'] && $check_cost['ok'] && $this->node->technologies[$tid]['level'] < $technology['maxLevel']) {
					$linkData.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$linkData.= '<a href="?p=module&action=addTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&technologyId=' . $tid . '" class="external button active">' . $d13->getLangUI("research") . '</a>';
					$linkData.= '</p>';
				}
				else {
					$linkData.= '<p class="buttons-row theme-gray">';
					$linkData.= '<a href="#" class="button active">' . $d13->getLangUI("research") . '</a>';
					$linkData.= '</p>';
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

				$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
				$tvars['tvar_linkData'] = $linkData;
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_requirementsData'] = $requirementsData;
				$tvars['tvar_tid'] = $tid;
				$tvars['tvar_techName'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'];
				$tvars['tvar_techDescription'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['description'];
				$tvars['tvar_techTier'] = $this->node->technologies[$tid]['level'];
				$tvars['tvar_techMaxTier'] = $technology['maxLevel'];
				$tvars['tvar_techDuration'] = misc::time_format((($technology['duration'] - $technology['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'research')) * 60);
				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.research", $tvars);
			}
		}

		$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.swiper") , $tvars));
		$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));
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

		// - - - Queue

		if (count($this->node->queue['research'])) {
			foreach($this->node->queue['research'] as $item) {
				$remaining = $item['start'] + $item['duration'] * 60 - time();
				$html.= '<div>' . $d13->getLangUI("research") . ' ' . $d13->getLangGL('technologies', $this->node->data['faction'], $item['technology'], "name") . ' <span id="research_' . $item['node'] . '_' . $item['technology'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span> <script type="text/javascript">timedJump("research_' . $item['node'] . '_' . $item['technology'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $_GET['slotId'] . '&technologyId=' . $item['technology'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
			}
		}

		// - - - Popover if Queue empty

		if ($html == '') {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-swiper" onclick="swiperUpdate();">' . $d13->getLangUI("research") . '</a>';
				$html.= '</p>';
			}
			else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button active">' . $d13->getLangUI("research") . '</a>';
				$html.= '</p>';
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
		if (isset($this->data['technologies'])) {
			foreach($this->data['technologies'] as $technology) {
				if ($d13->getTechnology($this->node->data['faction'], $technology, 'active')) {

					// if ($d13->data->technologies->getbyid('active', $technology, $this->node->data['faction'])) {

					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("technologies", $this->node->data['faction'], $technology) ["name"] . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $technology . '.png" title="' . $d13->getLangGL("technologies", $this->node->data['faction'], $technology) ["name"] . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_alliance
//
// ----------------------------------------------------------------------------------------

class d13_module_alliance extends d13_module

{

	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getOptions
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getOptions()
	{
		global $d13;
		$tvars = array();
		$html = '';

		// - - - - Option: Alliance List

		if ($this->data['options']['allianceGet']) {
			$tvars['tvar_Label'] = $d13->getLangUI("get") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=get&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("get") . ' ' . $d13->getLangUI("alliance");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Alliance Edit

		if ($this->data['options']['allianceEdit']) {
			$tvars['tvar_Label'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=add&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("alliance");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Alliance Remove

		if ($this->data['options']['allianceRemove']) {
			$tvars['tvar_Label'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=remove&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("alliance");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Alliance Invite

		if ($this->data['options']['allianceInvite']) {
			$tvars['tvar_Label'] = $d13->getLangUI("invite") . ' ' . $d13->getLangUI("members");
			$tvars['tvar_Link'] = '?p=alliance&action=addInvitation&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("invite") . ' ' . $d13->getLangUI("members");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Alliance Go to War

		if ($this->data['options']['allianceWar']) {
			$tvars['tvar_Label'] = $d13->getLangUI("warDeclaration");
			$tvars['tvar_Link'] = '?p=alliance&action=addWar&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("warDeclaration");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option:
		// - - - - Option:
		// - - - - Option:

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
		return $d13->getLangUI("none");
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_command
//
// ----------------------------------------------------------------------------------------

class d13_module_command extends d13_module

{

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
		$html = '';
		$i=0;
		
		if ($this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $rid => $res) {
				if ($d13->getResourceByID($res['id'], 'active') && $res['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $rid . '.png" title="' . $d13->getLangGL('resources', $rid, 'name') . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('resources', $rid, 'name');
					$tvars['tvar_listAmount'] = floor($res['value']);
					$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
					$i++;
				}
			}
			if ($i>0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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
		global $d13;
		$tvars = array();
		$html = '';

		// - - - - Option: Remove Node

		$nodes = $this->node->getList($_SESSION[CONST_PREFIX . 'User']['id']);
		$t = count($nodes);
		if ($this->data['options']['nodeRemove'] && $t > 1) {
			$tvars['tvar_Label'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=remove&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("remove");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Move Node

		if ($this->data['options']['nodeMove']) {
			$tvars['tvar_Label'] = $d13->getLangUI("move") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=move&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("move");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Edit Node

		if ($this->data['options']['nodeEdit']) {
			$tvars['tvar_Label'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("node");
			$tvars['tvar_Link'] = '?p=node&action=set&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("edit");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
		}

		// - - - - Option: Add new Node

		$nodes = $this->node->getList($_SESSION[CONST_PREFIX . 'User']['id']);
		if (count($nodes) < $d13->getGeneral('maxNodes')) {
			$tvars['tvar_Label'] = $d13->getLangUI("add") . ' ' . $d13->getLangUI("node");
			if ($d13->getGeneral('options', 'gridSystem') == 1) {
				$tvars['tvar_Link'] = '?p=node&action=add';
			}
			else {
				$tvars['tvar_Link'] = '?p=node&action=random';
			}

			$tvars['tvar_LinkLabel'] = $d13->getLangUI("add");
			$html.= $d13->templateParse($d13->templateGet("sub.module.itemcontent") , $tvars);
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
		return $d13->getLangUI("none");
	}
}

// ----------------------------------------------------------------------------------------
// d13_module_defense
//
// ----------------------------------------------------------------------------------------

class d13_module_defense extends d13_module

{

	// ----------------------------------------------------------------------------------------
	// getStats
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getStats()
	{
		$unit = new d13_modulit($this->data['moduleId'], $this->data['level'], $this->data['moduleInput'], $this->data['unitId'], $this->node);

		// - - - - - Check Upgrades

		$upgradeData = array();
		$upgradeData = $unit->getUpgrades();
		$tvars['tvar_unitHPPlus'] = "[+" . $upgradeData['hp'] . "]";
		$tvars['tvar_unitDamagePlus'] = "[+" . $upgradeData['damage'] . "]";
		$tvars['tvar_unitArmorPlus'] = "[+" . $upgradeData['armor'] . "]";
		$tvars['tvar_unitSpeedPlus'] = "[+" . $upgradeData['speed'] . "]";
		$tvars['tvar_unitType'] = $unit->data['type'];
		$tvars['tvar_unitClass'] = $unit->data['class'];
		$tvars['tvar_unitHP'] = $unit->data['hp'];
		$tvars['tvar_unitDamage'] = $unit->data['damage'];
		$tvars['tvar_unitArmor'] = $unit->data['armor'];
		$tvars['tvar_unitSpeed'] = $unit->data['speed'];
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
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

// ----------------------------------------------------------------------------------------
// d13_module_trade
//
// ----------------------------------------------------------------------------------------

class d13_module_trade extends d13_module

{

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
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
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


// ----------------------------------------------------------------------------------------
// d13_module_market
//
// ----------------------------------------------------------------------------------------

class d13_module_market extends d13_module

{

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
	// getInventory
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function getInventory()
	{
		return '';
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

// ----------------------------------------------------------------------------------------
// d13_module_storvest
//
// ----------------------------------------------------------------------------------------

class d13_module_storvest extends d13_module

{

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
		$html = '';
		$i=0;
		
		if (isset($this->data['options']['inventoryList']) && $this->data['options']['inventoryList']) {
			
			foreach($this->node->resources as $uid => $unit) {
				if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $uid . '.png" title="' . $d13->getLangGL('resources', $uid, 'name') . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('resources', $uid, 'name');
					$tvars['tvar_listAmount'] = floor($unit['value']);
					$tvars['tvar_sub_popuplist'].= $d13->templateParse($d13->templateGet("sub.module.listcontent") , $tvars);
					$i++;
				}
			}
			if ($i > 0) {
				$d13->templateInject($d13->templateParse($d13->templateGet("sub.popup.list") , $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $d13->getLangUI("resource") . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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
		if (isset($this->data['storedResource'])) {
			foreach($this->data['storedResource'] as $res) {
				if ($d13->getResource($res, 'active')) {
					$html.= $d13->getLangUI('production') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('production') . ' ' . $d13->getLangGL("resources", $res, "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
					$html.= ' ' . $d13->getLangUI('storage') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('storage') . ' ' . $d13->getLangGL("resources", $res, "name") . '"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $d13->getLangUI("none");
		}

		return $html;
	}
}


// =====================================================================================EOF

?>