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
		
		$my_upgrades = array();
				
		// - - - - - - - - - - - - - - - MODULE UPGRADES
		if (!empty($this->data['upgrades']) && $this->data['type'] != 'unit' && $this->data['level'] > 1) {
			foreach ($this->data['upgrades'] as $upgrade_id) {
				$tmp_upgrade = $d13->getUpgrade($this->node->data['faction'], $upgrade_id);
				if ($tmp_upgrade['active'] && in_array($tmp_upgrade['id'], $this->data['upgrades'])) {
					$tmp_upgrade['level'] = $this->data['level'];
					$my_upgrades[] = $tmp_upgrade;
				}
			}
		}
		
		// - - - - - - - - - - - - - - - TECHNOLOGY UPGRADES
		$tmp_list = array();
		foreach($this->node->technologies as $technology) {
			if ($technology['level'] > 0) {
				$tmp_technology = $d13->getTechnology($this->node->data['faction'], $technology['id']);
				foreach ($tmp_technology['upgrades'] as $tmp_upgrade) {
					$tmp_levels[$tmp_upgrade] = $technology['level'];
					$tmp_list[] = $tmp_upgrade;
				}
			}
		}
		
		if (!empty($tmp_list)) {
			foreach ($d13->getUpgrade($this->node->data['faction']) as $tmp_upgrade) {
				if ($tmp_upgrade['active'] && in_array($tmp_upgrade['id'], $tmp_list)) {
					
					
					$pass = false;
					if (empty($tmp_upgrade['targets']) && ($tmp_upgrade['type'] == $this->data['type'])) {
						$pass = true;
					} else if (!empty($tmp_upgrade['targets']) && in_array($this->data['id'], $tmp_upgrade['targets'])) {
						$pass = true;
					}
					
					
					
					if ($pass) {
						$tmp_upgrade['level'] = $tmp_levels[$tmp_upgrade['id']];
						$my_upgrades[] = $tmp_upgrade;
						unset($tmp_list[$tmp_upgrade['id']]);
					}
				}
			}
		}
		
		// - - - - - - - - - - - - - - - APPLY UPGRADES
		if (!empty($my_upgrades)) {
			foreach ($my_upgrades as $upgrade) {
			
				//- - - Cost Upgrade
				if (isset($upgrade['cost'])) {
					$this->data['upgrade_cost'] = $upgrade['cost'];
				}
		
				//- - - Requirements Upgrade
				if (isset($upgrade['requirements'])) {
					$this->data['upgrade_requirements'] = $upgrade['requirements'];
				}
				
				//- - - Attributes Upgrade
				foreach ($upgrade['attributes'] as $attribute) {
					if (isset($attribute['stat'])) {
						if ($attribute['stat'] == 'all' && ($this->data['type'] == 'unit')) {
							foreach($d13->getGeneral('stats') as $stat) {
								$value = $attribute['value'] * $upgrade['level'];
								$this->data[$stat] += $value;
								$this->data['upgrade_' . strtolower($stat)] += $value;
							}
						} else if ($attribute['stat'] != 'all') {
							$value = $attribute['value'] * $upgrade['level'];
							$this->data[$attribute['stat']] += $value;
							$this->data['upgrade_' . strtolower($attribute['stat'])] += $value;
						}
					}
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
		$this->data['busy'] = false;
		$this->data['available'] = 0;
		$this->data['upgrade_cost'] = array();
		$this->data['upgrade_requirements'] = array();
		$this->data['base_maxinput'] = $this->data['maxInput'];
		$this->data['base_ratio'] = $this->data['ratio'];
		$this->data['moduleId'] = $moduleId;
		$this->data['slotId'] = $slotId;
		$this->data['type'] = $type;
		$this->data['level'] = 0;
		$this->data['units'] = 0;
		$this->data['totalIR'] = $this->node->modules[$slotId]['input'] * $this->data['ratio'];
		$this->data['cost'] = $this->getCost();
		
		foreach($d13->getGeneral('stats') as $stat) {
			$this->data['upgrade_' . $stat] = 0;
		}
		
		if ($this->node->modules[$slotId]['level'] > 0) {
			
			$this->data['level'] = $this->node->modules[$slotId]['level'];
			$this->checkUpgrades();
			$this->data['cost'] = $this->getCost(true);
		}
		
		$this->data['moduleImage'] = '';
		$this->data['name'] = $d13->getLangGL('modules', $this->node->data['faction'], $this->data['moduleId'], 'name');
		$this->data['description'] = $d13->getLangGL('modules', $this->node->data['faction'], $this->data['moduleId'], 'description');
		$this->data['inputLimit'] = floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'], $this->node->modules[$this->data['slotId']]['input'])); #floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'] + $this->node->modules[$this->data['slotId']]['input']));
		$this->data['costData'] = $this->node->checkCost($this->data['cost'], 'build');
		$this->data['reqData'] = $this->node->checkRequirements($this->data['requirements']);
		
		if (isset($this->data['inputResource'])) {
			$this->data['moduleInput'] = $this->data['inputResource'];
			$this->data['moduleInputName'] = $d13->getLangGL('resources', $this->data['inputResource'], 'name');
			$this->data['moduleSlotInput'] = $this->node->modules[$slotId]['input'];
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
		
		$this->getModuleImage();
		
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
		
		foreach ($this->data as $key => $value) {
			if (!is_array($value)) {
				$tvars['tvar_'.$key] = $value;
			}
		}
		
		$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
		$tvars['tvar_nodeID'] = $this->node->data['id'];
		$tvars['tvar_slotID'] = $this->data['slotId'];
		$tvars['tvar_demolishLink'] 		= $this->getDemolish();
		$tvars['tvar_inventoryLink'] 		= $this->getInventory();
		$tvars['tvar_linkData'] 			= $this->getModuleUpgrade();
		$tvars['tvar_moduleItemContent'] 	= $this->getOptions();
		$tvars['tvar_image'] = $this->data['image'];
		$tvars['tvar_moduleDescription'] = misc::toolTip($this->data['name'] . '<br>' . $this->data['description']);
		
		if ($this->data['level'] > 0) {
			$tvars['tvar_popup'] = $this->getPopup();
			$tvars['tvar_queue'] = $this->getQueue();
			
			
			if ($this->node->resources[$this->data['inputResource']]['value'] < $this->data['maxInput']) {
				$max = $this->node->modules[$this->data['slotId']]['input'] + $this->node->resources[$this->data['inputResource']]['value'];
				$max = min($max, $this->data['maxInput']);
			} else {
				$max = $this->data['maxInput'];
			}

			if ($this->node->resources[$this->data['inputResource']]['value'] < $this->node->storage[$this->data['inputResource']]) {
				$min = $this->node->modules[$this->data['slotId']]['input'] - ($this->node->storage[$this->data['inputResource']] - $this->node->resources[$this->data['inputResource']]['value']);
				if ($min<0) { $min=0; }
			} else {
				$min = $this->node->modules[$this->data['slotId']]['input'];
			}
			
			$tvars['tvar_inputSlider'] = $this->getInputSlider(
				"?p=module&action=set&nodeId=".$this->node->data['id']."&slotId=".$this->data['slotId'], 
				$this->data['slotId'].'_'.$this->data['moduleId'], 
				floor($min), 
				floor($max), 
				$this->node->modules[$this->data['slotId']]['input'], 
				$this->data['busy']);
		}
		
		$tvars['tvar_levelLabel'] = '';
		if ($this->data['maxLevel'] > 1) {
			$tvars['tvar_levelLabel'] = '('.$this->data['level'].'/'.$this->data['maxLevel'].')';
		}
		
		$tvars['tvar_costData'] = $this->getCostList();
		$tvars['tvar_requirementsData'] = $this->getRequirementsList();
		$tvars['tvar_outputData'] = $this->getOutputList();
		
		if ($this->data['level'] <= 0) {
		
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
	// getModuleImage
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
			if ($image['level'] == 1) {
				$this->data['trueimage'] = $image['image'];
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// getPendingImage
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function getPendingImage()
	{
		global $d13;
		
		foreach($this->data['images'] as $image) {
			if ($image['level'] == 0) {
				return $image['image'];
			}
		}
		return NULL;
	}

	// ----------------------------------------------------------------------------------------
	// getInputSlider
	// Generate and return a Range Input Slider with given parameters for min/max/disabling
	// ----------------------------------------------------------------------------------------
	public

	function getInputSlider($action, $id, $min, $max, $value, $disabled=false, $tooltip=true)
	{
	
		global $d13;

		$vars = array();		
		$vars['tvar_formAction'] 		= $action;
		$vars['tvar_sliderID'] 			= $id;
				
		if ($min < 10) { $min = "0".$min; }
		if ($max < 10) { $max = "0".$max; }
		if ($value < 10) { $value = "0".$value; }
		
		$vars['tvar_sliderValue'] 		= max($min, $value);
		$vars['tvar_sliderMin'] 		= $min;
		$vars['tvar_sliderMax'] 		= $max;
		$vars['tvar_disableData']		= '';
		$vars['tvar_sliderTooltip']		= '';
		
		if ($tooltip) {
		$vars['tvar_sliderTooltip']		= misc::toolTip($d13->getLangUI("tipRangeSliderTooltip"));
		}
		
		if ($disabled || $max <= 0) {
			$vars['tvar_disableData']	= 'disabled';
			$vars['tvar_sliderTooltip']	= misc::toolTip($d13->getLangUI("tipRangeSliderDisabled"));
		}
		
		return $d13->templateSubpage("sub.range.slider", $vars);
				
	}

	// ----------------------------------------------------------------------------------------
	// getCheckDemolish
	// Checks if the module can be demolished and returns either an enabled or disabled button
	// ----------------------------------------------------------------------------------------
	public

	function getDemolish()
	{
		global $d13;
		$html = '';
		
		if ($this->data['level'] > 0) {
			if ($d13->getGeneral('options', 'moduleDemolish')) {
				if ($this->node->modules[$this->data['slotId']]['input'] <= 0) {
					$tooltip = misc::toolTip($d13->getLangUI("tipDemolishModule"));
					$html .= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$html .= '<a href="?p=module&action=remove&nodeId='.$this->node->data['id'].'&slotId='.$this->data['slotId'].'" class="external button active '.$tooltip.'">'.$d13->getLangUI("removeModule").'</a>';
					$html .= '</p>';
				} else {
					$tooltip = misc::toolTip($d13->getLangUI("tipRemoveWorkersfirst"));
					$html .= '<p class="buttons-row">';
					$html .= '<a href="#" class="external button '.$tooltip.'">'.$d13->getLangUI("removeModule").'</a>';
					$html .= '</p>';
				}
			}
		}

		return $html;
	}
	
	// ----------------------------------------------------------------------------------------
	// getModuleUpgrade
	// generates and returns either a build or an upgrade button
	// ----------------------------------------------------------------------------------------

	public

	function getModuleUpgrade()
	{
		global $d13;
		$html = '';
		
		if ($this->data['level'] > 0 && $this->data['maxLevel'] == 1) {
			return $html;
		} else {
		
			if ($this->node->resources[$this->data['inputResource']]['value'] < $this->data['maxInput']) {
				$max = $this->node->modules[$this->data['slotId']]['input'] + $this->node->resources[$this->data['inputResource']]['value'];
				if ($max > $this->data['maxInput']) {
					$max = $this->data['maxInput'];
				}
			} else {
				$max = $this->data['maxInput'];
			}	
			
			
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
			
			if ($this->data['level'] <= 0) {
		
				if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData']['ok'] && $this->data['reqData']['ok'] && ($this->node->getModuleCount($this->data['slotId'], $this->data['moduleId']) < $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'maxInstances'))) {
					
					$tvars['tvar_title'] 			= $d13->getLangUI("addModule");
					$tvars['tvar_moduleInputName'] 	= $this->data['moduleInputName'];
					$tvars['tvar_moduleInputImage'] = $d13->getResource($this->data['moduleInput'], 'image');
					$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
					$tvars['tvar_costData'] = $this->getCostList();
					$tvars['tvar_requirementsData'] = $this->getRequirementsList();
					$tvars['tvar_moduleAction'] 	= '?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
					$tvars['tvar_id'] 				= $this->data['moduleId'];
					$tvars['tvar_moduleInput']		= $this->data['moduleSlotInput'];
					$tvars['tvar_moduleLimit'] 		= floor(min($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput'],$this->data['maxInput']));
					$tvars['tvar_disableData'] 		= '';
					$tvars['tvar_inputSlider'] 		= $this->getInputSlider(
						'?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'], 
						'b'.$this->data['slotId'].'_'.$this->data['moduleId'], 
						1, 
						floor($max), 
						0,
						false,
						false);
					
					$d13->templateInject($d13->templateSubpage("sub.popup.build" , $tvars, true));
					$tooltip = misc::toolTip($d13->getLangUI("addModule") . ' ' . $d13->getLangUI("tipModuleBuildup"));
					$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$html.= '<a href="#" class="button active open-popup '.$tooltip.'" data-popup=".popup-build-'.$this->data['moduleId'].'">' . $d13->getLangUI("addModule") . '</a>';
					$html.= '</p>';
				} else {
					$tooltip = misc::toolTip($d13->getLangUI("tipModuleBuildupDisabled"));
					$html.= '<p class="buttons-row theme-gray">';
					$html.= '<a href="#" class="button '.$tooltip.'">' . $d13->getLangUI("addModule") . " " . $d13->getLangUI("impossible") . '</a>';
					$html.= '</p>';
				}
			
			} else {
				
				if ($d13->getGeneral('options', 'moduleUpgrade')) {
					if ($this->data['level'] < $this->data['maxLevel']) {
						if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData']['ok'] && $this->data['reqData']['ok'] && $this->node->modules[$this->data['slotId']]['level'] < $this->data['maxLevel'] && $this->data['maxLevel'] > 1) {
						
							$tvars['tvar_title'] 			= $d13->getLangUI("upgrade");
							$tvars['tvar_moduleInputName'] 	= $this->data['moduleInputName'];
							$tvars['tvar_moduleInputImage'] = $d13->getResource($this->data['moduleInput'], 'image');
							$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
							$tvars['tvar_costData'] 		= $this->getCostList(true);
							$tvars['tvar_requirementsData'] = $this->getRequirementsList();
							$tvars['tvar_moduleAction'] 	= '?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
							$tvars['tvar_id'] 				= $this->data['moduleId'];
							$tvars['tvar_moduleInput']		= $this->data['moduleSlotInput'];
							$tvars['tvar_moduleLimit'] 		= floor(min($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput'],$this->data['maxInput']));
							$tvars['tvar_disableData'] 		= '';
							$tvars['tvar_inputSlider']		= $this->getInputSlider(
								'?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'], 
								'u'.$this->data['slotId'].'_'.$this->data['moduleId'], 
								1, 
								floor($max), 
								$this->node->modules[$this->data['slotId']]['input'],
								false,
								false);
				
							$d13->templateInject($d13->templateSubpage("sub.popup.build" , $tvars, true));
							$tooltip = misc::toolTip($d13->getLangUI("upgrade") . ' ' . $d13->getLangUI("tipModuleBuildup"));
							$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
							$html.= '<a href="#" class="button active open-popup '.$tooltip.'" data-popup=".popup-build-'.$this->data['moduleId'].'">' . $d13->getLangUI("upgrade") . '</a>';
							$html.= '</p>';
						} else {
							$tooltip = misc::toolTip($d13->getLangUI("tipModuleBuildupDisabled"));
							$html.= '<p class="buttons-row theme-gray">';
							$html.= '<a href="#" class="button '.$tooltip.'">' . $d13->getLangUI("upgrade") . " " . $d13->getLangUI("impossible") . '</a>';
							$html.= '</p>';
						}
					} else {
 						$tooltip = misc::toolTip($d13->getLangUI("tipModuleMaxLevel"));
						$html.= '<p class="buttons-row theme-gray">';
						$html.= '<a href="#" class="button '.$tooltip.'">' . $d13->getLangUI("maxModuleLevel") . '</a>';
						$html.= '</p>';
					}
				}
			}
		
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
				$html.= '<div class="cell"><a class="tooltip-left" data-tooltip="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></a></div><div class="cell">' . $cost['value'] . '</div>';
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
		} else {
			foreach($this->data['requirements'] as $key => $requirement) {
				
				if (isset($requirement['level'])) {
					$value = $requirement['level'];
					$tooltip = $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . " [L".$value."]";
				}
				else {
					$value = $requirement['value'];
					$tooltip = $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . " [x".$value."]";
				}

				if ($requirement['type'] == 'modules') {
					$images = array();
					$images = $d13->getModule($this->node->data['faction'], $requirement['id'], 'images');
					$image = $images[1]['image'];
				} else if ($requirement['type'] == 'technology') {
					$image = $d13->getTechnology($this->node->data['faction'], $requirement['id'], 'image');
				} else if ($requirement['type'] == 'component') {
					$image = $d13->getComponent($this->node->data['faction'], $requirement['id'], 'image');
				} else {
					$image = $requirement['id'];
				}

				$html.= '<div class="cell">' . $value . '</div><div class="cell"><a class="tooltip-left" data-tooltip="' . $tooltip . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/' . $requirement['type'] . '/' . $this->node->data['faction'] . '/' . $image . '" title="' . $d13->getLangUI($requirement['type']) . ' - ' . $d13->getLangGL($requirement['type'], $this->node->data['faction'], $requirement['id'], 'name') . '"></a></div>';
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
			if ($upgrade && !empty($this->data['upgrade_cost'])) {
				foreach($this->data['upgrade_cost'] as $key => $upcost) {
					$tmp2_array = array();
					$tmp2_array['resource'] = $upcost['resource'];
					$tmp2_array['value'] = $upcost['value'] * $d13->getGeneral('users', 'cost', 'build');
					$tmp2_array['name'] = $d13->getLangGL('resources', $upcost['resource'], 'name');
					$tmp2_array['icon'] = $d13->getResource($upcost['resource'], 'image');
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
		return '';
	}

	// ----------------------------------------------------------------------------------------
	// getInventory
	// Assemble this modules inventory, build a popup window and return a link
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
			foreach($this->node->units as $uid => $unit) {
				if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
					$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $d13->getUnit($this->node->data['faction'], $uid, 'image') . '" title="' . $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'] . '">';
					$tvars['tvar_listLabel'] = $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'];
					$tvars['tvar_listAmount'] = $unit['value'];
					$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
					$i++;
				}
			}
			
			if ($i>0) {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryTrain"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html .= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html .= '</p>';
			} else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html .= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html .= '</p>';
			}
		}
		
		$this->data['units'] = $i;

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
	
		global $d13;
		$html = '';
		$i = 0;
		$tvars = array();
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 1;
		
		foreach ($d13->getCombat() as $key => $combatType) {
			if ($combatType['active']) {
				if (isset($this->data['options'][$key]) && $this->data['options'][$key]) {
					if ($this->data['moduleSlotInput'] > 0 && $this->data['level'] >= $combatType['level']) {
						$tvars['tvar_Label'] = $d13->getLangGL("combatTypes", $combatType['id'], "name");
						$tvars['tvar_Description'] = $d13->getLangGL("combatTypes", $combatType['id'], "description");
						$tvars['tvar_Link'] = '?p=combat&action=add&nodeId=' . $this->node->data['id'] . '&type='.$combatType['string'].'&slotId='.$this->data['slotId'];
						$tvars['tvar_LinkLabel'] = $d13->getLangUI("set");
						$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
						$i++;
					}
				}
			}
		}
		
		if ($i > 0) {
			$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
			$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
			$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list-1">' . $d13->getLangUI("launch") . " " . $d13->getLangUI("combat") . '</a>';
			$html.= '</p>';
		} else {
			$html.= '<p class="buttons-row theme-gray">';
			$html.= '<a href="#" class="button">' . $d13->getLangUI("unit") . " " . $d13->getLangUI("launch") . " " . $d13->getLangUI("combat") .'</a>';
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
		global $d13;
		$html = '';

		// - - - Check Queue
		
		$this->data['busy'] = false;
		
		if (count($this->node->queue['combat'])) {
			foreach($this->node->queue['combat'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					$stage = '';
					$cancel = '';
					
					if (!$item['stage']) {
						if ($item['sender'] == $this->node->data['id']) {
							$stage = $d13->getLangUI('outgoing');
							$cancel = '<div class="cell"><a class="external" href="?p=combat&action=cancel&nodeId=' . $this->node->data['id'] . '&combatId=' . $item['id'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
						} else {
							$stage = $d13->getLangUI('incoming');
						}
					} else if ($item['sender'] == $this->node->data['id']) {
						$stage = $d13->getLangUI('returning');
					}
					
					$remaining = ($item['start'] + $item['duration']) - time();
					
					$otherNode = new node();
					if ($item['sender'] == $this->node->data['id']) {
						$status = $otherNode->get('id', $item['recipient']);
					}
					else {
						$status = $otherNode->get('id', $item['sender']);
					}
					
					if ($status == 'done') {
						$tvars = array();
						$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/flag.png">';
						$tvars['tvar_listLabel'] 	= $stage . ' ' . $d13->getLangUI("combat") . ' ' . $otherNode->data['name'];
						$tvars['tvar_listAmount'] 	= '<span id="combat_' . $item['id'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("combat_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> '.$cancel;
				
					
				
						$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
					}
					
				}
			}
		}
		
		// - - - Popover
		
		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $this->data['units'] > 0) {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$tvars['tvar_buttonColor'] 	= 'theme-'.$_SESSION[CONST_PREFIX.'User']['color'];
				$tvars['tvar_buttonData'] 	= 'class="button active open-popup '.$tooltip.'" data-popup=".popup-list-1"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("combat");
				$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
			} else {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleDisabled'));
				$tvars['tvar_buttonColor'] 	= 'theme-gray';
				$tvars['tvar_buttonData'] 	= 'class="button '.$tooltip.'"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("combat");
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
		$tvars = array();;
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
			
			$this->data['units'] = $i;											// !important
			
			if ($i > 0) {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryResource"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html .= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html .= '</p>';
			} else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html .= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html .= '</p>';
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
					$html.= $d13->getLangUI('storage') . '<a class="tooltip-left" data-tooltip="' . $d13->getLangUI('storage') . ' ' . $d13->getLangGL("resources", $res, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $res . '.png" title="' . $d13->getLangGL("resources", $res, "name") . '"></a>';
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
		$tvars = array();;
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
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryResource"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
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
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryCraft"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html.= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
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
			if ($component['active'] && in_array($cid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'components'))) {
				
				
				$tmp_component = new d13_component($cid, $this->node);
				
				// - - - - Cost and Requirements
				$costData = $tmp_component->getCostList();
				$requirementsData = $tmp_component->getRequirementList();

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
				$tvars['tvar_componentDescription'] = $d13->getLangGL("components", $this->node->data['faction'], $cid, "description");
				$tvars['tvar_duration'] = misc::sToHMS( (($component['duration'] - $component['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'craft')) * 60, true);
				$tvars['tvar_compLimit'] = $limitData;
				$vars['tvar_disableData']		= '';
				if ($limitData <= 0) {
					$vars['tvar_disableData']		= 'disabled';
				}
				$tvars['tvar_compValue'] = $this->node->components[$cid]['value'];
				$tvars['tvar_compStorage'] = $component['storage'];
				$tvars['tvar_compResource'] = $component['storageResource'];
				$tvars['tvar_compResourceName'] = $d13->getLangGL("resources", $component['storageResource'], "name");
				$tvars['tvar_compMaxValue'] = $this->node->components[$cid]['value'] + $limitData;
				
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
					
					$remaining = misc::sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$tvars = array();;
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
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$tvars['tvar_buttonColor'] 	= 'theme-'.$_SESSION[CONST_PREFIX.'User']['color'];
				$tvars['tvar_buttonData'] 	= 'class="button active open-popup '.$tooltip.'" data-popup=".popup-swiper" onclick="swiperUpdate();"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("craft");
				$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
			} else {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleDisabled'));
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
		$tvars = array();;
		$tvars['tvar_sub_popuplist'] = '';
		$tvars['tvar_listID'] = 0;
		$html = '';
		$i=0;
		
		if ($this->data['options']['inventoryList']) {
			foreach($this->node->units as $uid => $unit) {
				if (in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'units'))) {
					if ($d13->getUnit($this->node->data['faction'], $uid, 'active') && $unit['value'] > 0) {
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $d13->getUnit($this->node->data['faction'], $uid, 'image') . '" title="' . $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('units', $this->node->data['faction'], $uid) ['name'];
						$tvars['tvar_listAmount'] = $unit['value'];
						$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			
			if ($i>0) {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryTrain"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html.= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
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
		$tvars = array();;
		$tvars['tvar_sub_popupswiper'] = '';
		
		foreach($d13->getUnit($this->node->data['faction']) as $uid => $unit) {
			if ($unit['active'] && in_array($uid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'units'))) {
				
				$tmp_unit = new d13_unit($uid, $this->node);
				
				$vars = array();
				$vars = $tmp_unit->getTemplateVariables();
				
				$vars['tvar_duration'] = misc::sToHMS((($tmp_unit->data['duration'] - $tmp_unit->data['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'train')) * 60, true);
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
				$vars['tvar_unitMaxValue'] = $this->node->units[$tmp_unit->data['unitId']]['value'] + $tmp_unit->getMaxProduction();
				
				$tvars['tvar_sub_popupswiper'] .= $d13->templateSubpage("sub.module.train", $vars);
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
		
		if (count($this->node->queue['train'])) {
			foreach($this->node->queue['train'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					if (!$item['stage']) {
						$stage = $d13->getLangUI('train');
					} else {
						$stage = $d13->getLangUI('remove');
					}
					$remaining = misc::sToHMS(($item['start'] + $item['duration']) - time(), true);
					
					$tvars = array();;
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $d13->getUnit($this->node->data['faction'], $item['obj_id'], 'image') . '">';
					$tvars['tvar_listLabel'] 	= $stage . ' ' . $item['quantity'] . 'x ' . $d13->getLangGL("units", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="train_' . $item['id'] . '">' . $remaining . '</span><script type="text/javascript">timedJump("train_' . $item['id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelUnit&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&trainId=' . $item['id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}
		
		// - - - Popover if Queue empty

		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0) {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$tvars['tvar_buttonColor'] 	= 'theme-'.$_SESSION[CONST_PREFIX.'User']['color'];
				$tvars['tvar_buttonData'] 	= 'class="button active open-popup '.$tooltip.'" data-popup=".popup-swiper" onclick="swiperUpdate();"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("train");
				$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
			} else {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleDisabled'));
				$tvars['tvar_buttonColor'] 	= 'theme-gray';
				$tvars['tvar_buttonData'] 	= 'class="button '.$tooltip.'"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("train");
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
		if (isset($this->node->data['units'])) {
			foreach($this->node->data['units'] as $unit) {
				if ($d13->getUnit($this->node->data['faction'], $unit, 'active')) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("units", $this->node->data['faction'], $unit) ["name"] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $this->node->data['faction'] . '/' . $unit . '.png" title="' . $d13->getLangGL("units", $this->node->data['faction'], $unit) ["name"] . '"></a>';
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
		$tvars['tvar_listID'] = 0;
		$i=0;
		
		if ($this->data['options']['inventoryList']) {

			foreach($d13->getTechnology($this->node->data['faction']) as $tid => $tech) {
				if ($tech['active'] && in_array($tid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'technologies'))) {
					if ($this->node->technologies[$tid]['level'] > 0) {
						$tvars['tvar_listImage'] = '<img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $tid . '.png" title="' . $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'] . '">';
						$tvars['tvar_listLabel'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'];
						$tvars['tvar_listAmount'] = $d13->getLangUI("level") . " " . $this->node->technologies[$tid]['level'];
						$tvars['tvar_sub_popuplist'].= $d13->templateSubpage("sub.module.listcontent", $tvars);
						$i++;
					}
				}
			}
			if ($i>0) {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryResearch"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html .= '<a href="#" class="button active '.$tooltip.' open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			}else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
				$html.= '<p class="buttons-row theme-gray">';
				$html .= '<a href="#" class="button '.$tooltip.'">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
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
		
		// - - - Research Popup

		$tvars['tvar_sub_popupswiper'] = "";
		foreach($d13->getTechnology($this->node->data['faction']) as $tid => $technology) {
			if ($technology['active'] && in_array($tid, $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'technologies')) && ($this->node->technologies[$tid]['level'] < $technology['maxLevel'])) {
				
				$i++;
				
				$tmp_technology = new d13_technology($tid, $this->node);
				
				// - - - - - Check Cost & Requirements
				$costData = $tmp_technology->getCostList();
				$requirementsData = $tmp_technology->getRequirementList();

				// - - - - - Check Permissions

				$linkData = '';
				$check_requirements = NULL;
				$check_cost = NULL;
				$check_requirements = $this->node->checkRequirements($tmp_technology->data['requirements']);
				$check_cost = $this->node->checkCost($tmp_technology->data['cost'], 'research');
				if ($check_requirements['ok'] && $check_cost['ok'] && $this->node->technologies[$tid]['level'] < $technology['maxLevel']) {
					$linkData.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$linkData.= '<a href="?p=module&action=addTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&technologyId=' . $tid . '" class="external button active">' . $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research") . '</a>';
					$linkData.= '</p>';
				}
				else {
					$linkData.= '<p class="buttons-row theme-gray">';
					$linkData.= '<a href="#" class="button">' . $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research") . '</a>';
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
				$tvars['tvar_image'] = $d13->GetTechnology($this->node->data['faction'], $tid, 'image');
				$tvars['tvar_techName'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid) ['name'];
				$tvars['tvar_techDescription'] = $d13->getLangGL('technologies', $this->node->data['faction'], $tid, 'description');
				$tvars['tvar_techTier'] = $this->node->technologies[$tid]['level'];
				$tvars['tvar_techMaxTier'] = $technology['maxLevel'];
				$tvars['tvar_duration'] = misc::sToHMS((($technology['duration'] - $technology['duration'] * $this->data['totalIR']) * $d13->getGeneral('users', 'speed', 'research')) * 60, true);
				$tvars['tvar_sub_popupswiper'].= $d13->templateSubpage("sub.module.research", $tvars);
				
			}
		}
		
		$this->data['available'] = $i;
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
		
		if (count($this->node->queue['research'])) {
			foreach($this->node->queue['research'] as $item) {
				if ($item['slot'] == $this->data['slotId']) {
					
					$this->data['busy'] = true;
					
					
					$remaining = ($item['start'] + $item['duration'] ) - time();
					
					$tvars = array();;
					$tvars['tvar_listImage'] 	= '<img class="d13-resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $d13->getTechnology($this->node->data['faction'], $item['obj_id'], 'image') .'">';
					$tvars['tvar_listLabel'] 	= $d13->getLangGL("technologies", $this->node->data['faction'], $item['obj_id'], "name");
					$tvars['tvar_listAmount'] 	= '<span id="research_' . $item['obj_id'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("research_' . $item['obj_id'] . '", "?p=module&action=get&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '");</script> <a class="external" href="?p=module&action=cancelTechnology&nodeId=' . $this->node->data['id'] . '&slotId=' . $this->data['slotId'] . '&technologyId=' . $item['obj_id'] . '"> <img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>';
				
					$html = $d13->templateSubpage("sub.module.listcontent", $tvars);
				
				}
			}
		}

		// - - - Popover if Queue empty

		if ($this->data['busy'] == false) {
			if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $this->data['available'] > 0) {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleInactive'));
				$tvars['tvar_buttonColor'] 	= 'theme-'.$_SESSION[CONST_PREFIX.'User']['color'];
				$tvars['tvar_buttonData'] 	= 'class="button active open-popup '.$tooltip.'" data-popup=".popup-swiper" onclick="swiperUpdate();"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research");
				$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
			} else {
				$tvars = array();
				$tooltip = misc::toolTip($d13->getLangUI('tipModuleDisabled'));
				$tvars['tvar_buttonColor'] 	= 'theme-gray';
				$tvars['tvar_buttonData'] 	= 'class="button '.$tooltip.'"';
				$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("research");
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
		if (isset($this->data['technologies'])) {
			foreach($this->data['technologies'] as $technology) {
				if ($d13->getTechnology($this->node->data['faction'], $technology, 'active')) {
					$html.= '<a class="tooltip-left" data-tooltip="' . $d13->getLangGL("technologies", $this->node->data['faction'], $technology) ["name"] . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $this->node->data['faction'] . '/' . $d13->getTechnology($this->node->data['faction'], $technology, 'image') . '" title="' . $d13->getLangGL("technologies", $this->node->data['faction'], $technology, "name") . '"></a>';
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
		
		// - - - - Option: Alliance List

		if ($this->data['options']['allianceGet']) {
			$tvars['tvar_Label'] = $d13->getLangUI("new") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=get&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("new") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Edit

		if ($this->data['options']['allianceEdit']) {
			$tvars['tvar_Label'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=add&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("edit") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Remove

		if ($this->data['options']['allianceRemove']) {
			$tvars['tvar_Label'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_Link'] = '?p=alliance&action=remove&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("remove") . ' ' . $d13->getLangUI("alliance");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Invite

		if ($this->data['options']['allianceInvite']) {
			$tvars['tvar_Label'] = $d13->getLangUI("invite") . ' ' . $d13->getLangUI("members");
			$tvars['tvar_Link'] = '?p=alliance&action=addInvitation&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("invite") . ' ' . $d13->getLangUI("members");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}

		// - - - - Option: Alliance Go to War

		if ($this->data['options']['allianceWar']) {
			$tvars['tvar_Label'] = $d13->getLangUI("warDeclaration");
			$tvars['tvar_Link'] = '?p=alliance&action=addWar&nodeId=' . $this->node->data['id'];
			$tvars['tvar_LinkLabel'] = $d13->getLangUI("warDeclaration");
			$tvars['tvar_description'] = '';
			$tvars['tvar_sub_popuplist'] .= $d13->templateSubpage("sub.module.itemcontent", $tvars);
			$i++;
		}
		
		if ($this->node->modules[$this->data['slotId']]['input'] > 0 && $i > 0) {
			$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
			$tvars = array();
			$tooltip = misc::toolTip($d13->getLangUI('tipModuleInactive'));
			$tvars['tvar_buttonColor'] 	= 'theme-'.$_SESSION[CONST_PREFIX.'User']['color'];
			$tvars['tvar_buttonData'] 	= 'class="button active open-popup '.$tooltip.'" data-popup=".popup-list-1"';
			$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("alliance");
			$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
		} else {
			$tvars = array();
			$tooltip = misc::toolTip($d13->getLangUI('tipModuleDisabled'));
			$tvars['tvar_buttonColor'] 	= 'theme-gray';
			$tvars['tvar_buttonData'] 	= 'class="button '.$tooltip.'"';
			$tvars['tvar_buttonName'] 	= $d13->getLangUI("launch") . ' ' . $d13->getLangUI("alliance");
			$html = $d13->templateSubpage("sub.module.listbutton", $tvars);
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
		$tvars = array();;
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
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryResource"));
				$d13->templateInject($d13->templateSubpage("sub.popup.list", $tvars));
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup '.$tooltip.'" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$tooltip = misc::toolTip($d13->getLangUI("tipInventoryEmpty"));
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
		$tvars['tvar_unitVisionPlus'] = "[+" . $upgradeData['vision'] . "]";
		$tvars['tvar_unitCriticalPlus'] = "[+" . $upgradeData['critical'] . "]";
		$tvars['tvar_unitType'] = $unit->data['type'];
		$tvars['tvar_unitClass'] = $unit->data['class'];
		$tvars['tvar_unitHP'] = $unit->data['hp'];
		$tvars['tvar_unitDamage'] = $unit->data['damage'];
		$tvars['tvar_unitArmor'] = $unit->data['armor'];
		$tvars['tvar_unitSpeed'] = $unit->data['speed'];
		$tvars['tvar_unitVision'] = $unit->data['vision'];
		$tvars['tvar_unitCritical'] = $unit->data['critical'];
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
		$tvars = array();;
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
				$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
				$html.= '<a href="#" class="button active open-popup" data-popup=".popup-list-0">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . '</a>';
				$html.= '</p>';
			} else {
				$html.= '<p class="buttons-row theme-gray">';
				$html.= '<a href="#" class="button">' . $this->data['name'] . " " . $d13->getLangUI("inventory") . " " . $d13->getLangUI("empty") .'</a>';
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


// =====================================================================================EOF

?>