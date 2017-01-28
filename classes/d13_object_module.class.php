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

class d13_object_module extends d13_object_base

{
	#public $data, $node, $checkRequirements, $checkCost;

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
	// setNode
	// @
	//
	// ----------------------------------------------------------------------------------------
/*
	public

	function setNode($node)
	{
		$this->node = $node;
		$this->node->getModules();
		$this->node->getTechnologies();
		$this->node->getComponents();
		$this->node->getUnits();
	}
*/
	// ----------------------------------------------------------------------------------------
	// checkUpgrades
	// @
	//
	// ----------------------------------------------------------------------------------------
	/*
	public
	
	function checkUpgrades()
	{
		global $d13;
		
		$my_upgrades = array();
				
		// - - - - - - - - - - - - - - - MODULE UPGRADES
		if (!empty($this->data['upgrades']) && $this->data['type'] != 'unit' && $this->data['level'] > 1) {
			foreach ($this->data['upgrades'] as $upgrade_id) {
				$tmp_upgrade = $d13->getUpgradeModule($this->node->data['faction'], $upgrade_id);
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
			foreach ($d13->getUpgradeModule($this->node->data['faction']) as $tmp_upgrade) {
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
*/
	// ----------------------------------------------------------------------------------------
	// setAttributes
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkStatsExtended()
	{
		global $d13;
		
		
		
		#$moduleId = $this->data['obj_id']; #obsolete
		#$this->data['slotId'] = $this->data['slotId']; #obsolete
		#$type = $this->data['type']; #obsolete
		
		
		$this->data['busy'] 		= false;			// is this building currently busy?
		$this->data['count'] 		= 0;				// count of other objects in this building?
		$this->data['available'] 	= 0;				// is any action available in this building?
		
		
		#$this->data = array();
		#$this->data = $d13->getModule($this->node->data['faction'], $moduleId);
	
		#$this->data['upgrade_cost'] = array();
		#$this->data['upgrade_requirements'] = array();
		$this->data['base_maxinput'] = $this->data['maxInput'];
		$this->data['base_ratio'] = $this->data['ratio'];
		#$this->data['moduleId'] = $moduleId;
		#$this->data['slotId'] = $this->data['slotId'];
		#$this->data['type'] = $type;
		#$this->data['level'] = 0;
		
		$this->data['totalIR'] = $this->node->modules[$this->data['slotId']]['input'] * $this->data['ratio'];
		$this->data['cost'] = $this->getCost();
		
		#foreach($d13->getGeneral('stats') as $stat) {
		#	$this->data['upgrade_' . $stat] = 0;
		#}
		
		#if ($this->node->modules[$this->data['slotId']]['level'] > 0) {
			
		#	$this->data['level'] = $this->node->modules[$this->data['slotId']]['level'];
		#	$this->checkUpgrades();
		#	$this->data['cost'] = $this->getCost(true);
		#}
		
		#$this->data['moduleImage'] = '';
		#$this->data['name'] = $d13->getLangGL('modules', $this->node->data['faction'], $this->data['moduleId'], 'name');
		#$this->data['description'] = $d13->getLangGL('modules', $this->node->data['faction'], $this->data['moduleId'], 'description');
		$this->data['inputLimit'] = floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'], $this->node->modules[$this->data['slotId']]['input'])); #floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'] + $this->node->modules[$this->data['slotId']]['input']));
		
		if (isset($this->data['inputResource'])) {
			$this->data['moduleInput'] = $this->data['inputResource'];
			$this->data['moduleInputName'] = $d13->getLangGL('resources', $this->data['inputResource'], 'name');
			$this->data['moduleSlotInput'] = $this->node->modules[$this->data['slotId']]['input'];
		}

		if (isset($this->data['outputResource'])) {
			$this->data['moduleProduction'] = $this->data['ratio'] * $d13->getGeneral('factors', 'production') * $this->node->modules[$this->data['slotId']]['input'];
			$i = 0;
			foreach($this->data['outputResource'] as $res) {
				$this->data['moduleOutput' . $i] = $res;
				$this->data['moduleOutputName' . $i] = $d13->getLangGL("resources", $res, "name");
				$i++;
			}
		}

		if (isset($this->data['storedResource'])) {
			$this->data['moduleStorage'] = $this->data['ratio'] * $this->node->modules[$this->data['slotId']]['input'];
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
		$tvars['tvar_moduleDescription'] = d13_misc::toolTip($this->data['name'] . '<br>' . $this->data['description']);
		
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
		
			if ($this->data['reqData']) {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
			}

			if ($this->data['costData']) {
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
/*
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
*/
	// ----------------------------------------------------------------------------------------
	// getPendingImage
	// @
	//
	// ----------------------------------------------------------------------------------------
/*
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
*/
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
		$vars['tvar_sliderTooltip']		= d13_misc::toolTip($d13->getLangUI("tipRangeSliderTooltip"));
		}
		
		if ($disabled || $max <= 0) {
			$vars['tvar_disableData']	= 'disabled';
			$vars['tvar_sliderTooltip']	= d13_misc::toolTip($d13->getLangUI("tipRangeSliderDisabled"));
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
					$tooltip = d13_misc::toolTip($d13->getLangUI("tipDemolishModule"));
					$html .= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$html .= '<a href="?p=module&action=remove&nodeId='.$this->node->data['id'].'&slotId='.$this->data['slotId'].'" class="external button active '.$tooltip.'">'.$d13->getLangUI("removeModule").'</a>';
					$html .= '</p>';
				} else {
					$tooltip = d13_misc::toolTip($d13->getLangUI("tipRemoveWorkersfirst"));
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
			
			
			if ($this->data['reqData']) {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_requirementsIcon'] = $d13->templateGet("sub.requirement.notok");
			}

			if ($this->data['costData']) {
				$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.ok");
			}
			else {
				$tvars['tvar_costIcon'] = $d13->templateGet("sub.requirement.notok");
			}
			
			if ($this->data['level'] <= 0) {
		
				if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData'] && $this->data['reqData'] && ($this->node->getModuleCount($this->data['slotId'], $this->data['moduleId']) < $d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'maxInstances'))) {
					
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
					$tooltip = d13_misc::toolTip($d13->getLangUI("addModule") . ' ' . $d13->getLangUI("tipModuleBuildup"));
					$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
					$html.= '<a href="#" class="button active open-popup '.$tooltip.'" data-popup=".popup-build-'.$this->data['moduleId'].'">' . $d13->getLangUI("addModule") . '</a>';
					$html.= '</p>';
				} else {
					$tooltip = d13_misc::toolTip($d13->getLangUI("tipModuleBuildupDisabled"));
					$html.= '<p class="buttons-row theme-gray">';
					$html.= '<a href="#" class="button '.$tooltip.'">' . $d13->getLangUI("addModule") . " " . $d13->getLangUI("impossible") . '</a>';
					$html.= '</p>';
				}
			
			} else {
				
				if ($d13->getGeneral('options', 'moduleUpgrade')) {
					if ($this->data['level'] < $this->data['maxLevel']) {
						if (($this->node->resources[$this->data['inputResource']]['value']+$this->data['moduleSlotInput']) > 0 && $this->data['costData'] && $this->data['reqData'] && $this->node->modules[$this->data['slotId']]['level'] < $this->data['maxLevel'] && $this->data['maxLevel'] > 1) {
						
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
							$tooltip = d13_misc::toolTip($d13->getLangUI("upgrade") . ' ' . $d13->getLangUI("tipModuleBuildup"));
							$html.= '<p class="buttons-row theme-' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">';
							$html.= '<a href="#" class="button active open-popup '.$tooltip.'" data-popup=".popup-build-'.$this->data['moduleId'].'">' . $d13->getLangUI("upgrade") . '</a>';
							$html.= '</p>';
						} else {
							$tooltip = d13_misc::toolTip($d13->getLangUI("tipModuleBuildupDisabled"));
							$html.= '<p class="buttons-row theme-gray">';
							$html.= '<a href="#" class="button '.$tooltip.'">' . $d13->getLangUI("upgrade") . " " . $d13->getLangUI("impossible") . '</a>';
							$html.= '</p>';
						}
					} else {
 						$tooltip = d13_misc::toolTip($d13->getLangUI("tipModuleMaxLevel"));
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
/*
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
*/
	// ----------------------------------------------------------------------------------------
	// getRequirementsList
	// @
	//
	// ----------------------------------------------------------------------------------------
/*
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
*/
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
/*
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
			if ($upgrade && $this->data['level'] > 0 && !empty($this->data['upgrade_cost'])) {
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
*/
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

// =====================================================================================EOF
