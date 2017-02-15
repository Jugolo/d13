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
// ABOUT OBJECTS:
// 
// The most important objects in the game have been grouped into a class "objects". This
// includes modules, technologies, units, components and so on. 
//
// NOTES:
//
// Modules represent Buildings and are always part of a Node (town). This is the most
// complex object type and features several child classes as well. Almost all of the gameplay
// functionality is represented using modules.
//
// ========================================================================================

abstract

class d13_gameobject_module extends d13_gameobject_base

{

	// ----------------------------------------------------------------------------------------
	// construct
	// @ Calls base object constructor with an array based argument list
	// ----------------------------------------------------------------------------------------

	public

	function __construct($args, &$node, d13_engine &$d13)
	{
		parent::__construct($args, $node, $d13);
	}

	// ----------------------------------------------------------------------------------------
	// checkStatsExtended
	// @ check and setup stats that are unique to this object type
	//
	// ----------------------------------------------------------------------------------------
	public

	function checkStatsExtended()
	{
		
		$this->data['busy'] 		= false;			// is this building currently busy?
		$this->data['count'] 		= 0;				// count of other objects in this building?
		$this->data['available'] 	= 0;				// is any action available in this building?
		
		$this->data['base_maxinput'] 	 = $this->data['maxInput'];
		$this->data['base_ratio']		 = $this->data['ratio'];
		$this->data['moduleProduction']  = ($this->data['ratio'] + $this->data['upgrade_ratio']) * $this->d13->getGeneral('users', 'efficiency', 'harvest') * $this->node->getBuff('efficiency', 'harvest') * $this->node->modules[$this->data['slotId']]['input'];
		$this->data['totalIR'] 			 = $this->node->modules[$this->data['slotId']]['input'] * $this->data['ratio'];
		$this->data['cost'] 			 = $this->getCost();
		
		#$this->data['inputLimit'] = floor(min($this->data['maxInput'], $this->node->resources[$this->data['inputResource']]['value'], $this->node->modules[$this->data['slotId']]['input']));
		
		
		$this->data['inputLimit'] = min($this->getMaxInput(), $this->getMinInput());
		
		if (isset($this->data['inputResource'])) {
			$this->data['moduleInput'] = $this->data['inputResource'];
			$this->data['moduleInputName'] = $this->d13->getLangGL('resources', $this->data['inputResource'], 'name');
			$this->data['moduleSlotInput'] = $this->node->modules[$this->data['slotId']]['input'];
		}
	
		if (isset($this->data['storedResource'])) {
			$this->data['moduleStorage'] = $this->data['ratio'] * $this->node->modules[$this->data['slotId']]['input'];
		}
		
		foreach($this->d13->getGeneral('stats') as $stat) {
			$this->data[$stat] = $this->data[$stat] + $this->data['upgrade_'.$stat];
		}
		
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @ retrieve all template variables that the tpl class requires to display this object type
	//
	// ----------------------------------------------------------------------------------------
	public

	function getTemplateVariables()
	{
		
		$tvars = array();
		
		$tvars = parent::getTemplateVariables();
		
		$tvars = array_merge($tvars, $this->getTemplateInput());
		
		$tvars['tvar_nodeFaction'] 			= $this->node->data['faction'];
		$tvars['tvar_nodeID'] 				= $this->node->data['id'];
		$tvars['tvar_slotID'] 				= $this->data['slotId'];
		$tvars['tvar_image'] 				= $this->data['image'];
		$tvars['tvar_moduleDescription'] 	= $this->data['description'];
		$tvars['tvar_moduleProduction'] 	= $this->data['moduleProduction'];
		$tvars['tvar_demolishLink'] 		= $this->getDemolish();
		$tvars['tvar_linkData'] 			= $this->getModuleUpgrade();
		$tvars['tvar_inventoryLink'] 		= $this->getInventory();
		$tvars['tvar_costData'] 			= $this->getCostList();
		$tvars['tvar_requirementsData'] 	= $this->getRequirementsList();
		$tvars['tvar_storedData'] 			= $this->getStoredResourceList();
		$tvars['tvar_outputData'] 			= $this->getOutputResourceList();
		
		if ($this->data['level'] > 0) {
			$tvars['tvar_popup'] = $this->getPopup();
			$tvars['tvar_queue'] = $this->getQueue();
			
			$max = $this->getMaxInput();
			$min = $this->getMinInput();

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
		
		/*
			Get Cost and Requirements Visuals
		*/
		if ($this->data['level'] <= 0) {
		
			$tvars = array_merge($tvars, $this->getTemplateCost());
		
		}
		
		/*
			Get all stored Resources
		*/
		$html = '';
		if (isset($this->data['storedResource'])) {
			foreach ($this->data['storedResource'] as $res) {
				$vars = array();
				$vars['tvar_listImage']		= $this->d13->getResource($res, 'icon');
				$vars['tvar_listDirectory']	= 'resources';
				$vars['tvar_listLabel']		= $this->d13->getLangUI("storage") . ' ' . $this->d13->getLangGL("resources", $res, 'name');
				$vars['tvar_listContent'] 	= $this->data['moduleStorage'] . ' ' . $this->d13->getLangUI("perInput");
				$html 						.= $this->d13->templateSubpage("sub.module.listentry", $vars);
			}
		}
		
		$tvars['tvar_moduleStorage'] = $html;
		
		/*
			Get all output Resources
		*/
		$html = '';
		if (isset($this->data['outputResource'])) {
			foreach ($this->data['outputResource'] as $res) {
				$vars = array();
				$vars['tvar_listImage']		= $this->d13->getResource($res, 'icon');
				$vars['tvar_listDirectory']	= 'resources';
				$vars['tvar_listLabel']		= $this->d13->getLangUI("production") . ' ' . $this->d13->getLangGL("resources", $res, 'name');
				$vars['tvar_listContent'] 	= $this->data['ratio'] . ' ' . $this->d13->getLangUI("perHour");
				$html 						.= $this->d13->templateSubpage("sub.module.listentry", $vars);				
			}
		}
		
		$tvars['tvar_moduleOutput'] = $html;
		
		
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 	function getTemplateCost()

	// 
	// ----------------------------------------------------------------------------------------
	private
	
	function getTemplateCost()
	{
		
		$tvars = array();
		
		if ($this->data['reqData']['ok']) {
				$tvars['tvar_requirementsIcon'] = $this->d13->templateGet("sub.requirement.ok");
		}
		else {
			$tvars['tvar_requirementsIcon'] = $this->d13->templateGet("sub.requirement.notok");
		}

		if ($this->data['costData']['ok']) {
			$tvars['tvar_costIcon'] = $this->d13->templateGet("sub.requirement.ok");
		}
		else {
			$tvars['tvar_costIcon'] = $this->d13->templateGet("sub.requirement.notok");
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplateInput
	// 
	// ----------------------------------------------------------------------------------------
	private
	
	function getTemplateInput()
	{
		$tvars = array();
		
		$inputType 	= $this->data['inputType'][0]['object'];
		$inputID 	= $this->data['inputType'][0]['id'];
		
		if ($inputType == "resource") {
			$tvars['tvar_moduleInputName'] 			= $this->d13->getLangGL("resources", $inputID, 'name');
			$tvars['tvar_moduleInputImage'] 		= $this->d13->getResource($inputID, 'icon');
			$tvars['tvar_moduleInputDirectory'] 	= 'resources';
		} else if ($inputType == "component") {
			$tvars['tvar_moduleInputName'] 			= $this->d13->getLangGL("components", $inputID, 'name');
			$tvars['tvar_moduleInputImage'] 		= $this->d13->getComponent($this->node->data['faction'], $inputID, 'icon');
			$tvars['tvar_moduleInputDirectory'] 	= 'components/'.$this->node->data['faction'].'/';
		} else if ($inputType == "unit") {
			$tvars['tvar_moduleInputName'] 			= $this->d13->getLangGL("units", $inputID, 'name');
			$tvars['tvar_moduleInputImage'] 		= $this->d13->getUnit($this->node->data['faction'], $inputID, 'icon');
			$tvars['tvar_moduleInputDirectory'] 	= 'units/'.$this->node->data['faction'].'/';
		}
	
		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// getMinInput
	// 
	// ----------------------------------------------------------------------------------------
	private
	
	function getMinInput()
	{
		
		$args = array();
		$inputType 	= $this->data['inputType'][0]['object'];
		$inputID 	= $this->data['inputType'][0]['id'];
		
		$current 	= 0;
		$storage 	= 0;
		$min 		= 0;
		
		if ($inputType == "resource") {
			$current = $this->node->resources[$inputID]['value'];
			$storage = $this->node->storage[$inputID];
		} else if ($inputType == "component") {
			$current = $this->node->components[$inputID]['value'];
				
			$args['supertype'] 	= 'component';
			$args['id'] 		= $inputID;
			$tmp_object = $this->d13->createGameObject($args, $this->node);
			$storage = $tmp_object->getMaxProduction();
		} else if ($inputType == "unit") {
			$current = $this->node->units[$inputID]['value'];
			
			$args['supertype'] 	= 'unit';
			$args['id'] 		= $inputID;
			$tmp_object = $this->d13->createGameObject($args, $this->node);
			$storage = $tmp_object->getMaxProduction();
		}

		if ($current < $storage) {
			$min = $this->node->modules[$this->data['slotId']]['input'] - ($storage - $current);
			$min = max($min, 0);
		} else {
			$min = $this->node->modules[$this->data['slotId']]['input'];
		}
		
		return floor($min);
	
	}
	
	// ----------------------------------------------------------------------------------------
	// getMaxInput
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function getMaxInput()
	{
		$args = array();
		$inputType 	= $this->data['inputType'][0]['object'];
		$inputID 	= $this->data['inputType'][0]['id'];
		
		$current 	= 0;
		$storage 	= 0;
		$max 		= 0;
		
		if ($inputType == "resource") {
			$current = $this->node->resources[$inputID]['value'];
			$storage = $this->node->storage[$inputID];
		} else if ($inputType == "component") {
					
		} else if ($inputType == "unit") {
					
		}

		if ($current < $storage) {
			$max = $this->node->modules[$this->data['slotId']]['input'] + $current;
			$max = min($max, $this->data['maxInput']);
		} else {
			$max = $this->data['maxInput'];
		}
		
		return floor($max);

	}

	// ----------------------------------------------------------------------------------------
	// getInputSlider
	// Generate and return a Range Input Slider with given parameters for min/max/disabling
	// ----------------------------------------------------------------------------------------
	public

	function getInputSlider($action, $id, $min, $max, $value, $disabled=false, $tooltip=true)
	{
	
		$vars = array();		
		$vars['tvar_formAction'] 		= $action;
		$vars['tvar_sliderID'] 			= $id;
		$vars['tvar_sliderValue'] 		= max($min, $value);
		$vars['tvar_sliderMin'] 		= $min;
		$vars['tvar_sliderMax'] 		= $max;
		$vars['tvar_disableData']		= '';
		$vars['tvar_sliderTooltip']		= '';
		
		if ($tooltip) {
		$vars['tvar_sliderTooltip']		= $this->d13->misc->toolTip($this->d13->getLangUI("tipRangeSliderTooltip"));
		}
		
		if ($disabled || $max <= 0) {
			$vars['tvar_disableData']	= 'disabled';
			$vars['tvar_sliderTooltip']	= $this->d13->misc->toolTip($this->d13->getLangUI("tipRangeSliderDisabled"));
		}
		
		return $this->d13->templateSubpage("sub.range.slider", $vars);
				
	}

	// ----------------------------------------------------------------------------------------
	// getCheckDemolish
	// Checks if the module can be demolished and returns either an enabled or disabled button
	// ----------------------------------------------------------------------------------------
	public

	function getDemolish()
	{
		
		$html = '';
		
		if ($this->data['level'] > 0) {
			if ($this->d13->getGeneral('options', 'moduleDemolish')) {
				if ($this->node->modules[$this->data['slotId']]['input'] <= 0) {

					$vars['tvar_button_name'] 	 = $this->d13->getLangUI("removeModule");
					$vars['tvar_button_link'] 	 = "?p=module&action=remove&nodeId=".$this->node->data['id']."&slotId=".$this->data['slotId'];
					$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipDemolishModule"));
					$html = $this->d13->templateSubpage("button.external.enabled", $vars);
					
				} else {
					
					$vars['tvar_button_name'] 	 = $this->d13->getLangUI("removeModule");
					$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipRemoveWorkersfirst"));
					$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
				}
			}
		}

		return $html;
	}
	
	// ----------------------------------------------------------------------------------------
	// getModuleUpgrade
	// generates and returns either a build or an upgrade button template snippet
	// ----------------------------------------------------------------------------------------
	public

	function getModuleUpgrade()
	{
		
		$html = '';
		
		if ($this->data['level'] > 0 && $this->data['maxLevel'] == 1) {
			return $html;
		} else {
		
			$tvars = array();
			$tvars = array_merge($tvars, $this->getTemplateInput());
			$tvars = array_merge($tvars, $this->getTemplateCost());
			
			$max = $this->getMaxInput();
			$min = $this->getMinInput();
		
			if ($this->data['level'] <= 0) {
		
				if ($max > 0 && $this->data['costData']['ok'] && $this->data['reqData'] && ($this->node->getModuleCount($this->data['slotId'], $this->data['moduleId']) < $this->d13->getModule($this->node->data['faction'], $this->data['moduleId'], 'maxInstances'))) {
					
					$tvars['tvar_title'] 			= $this->d13->getLangUI("addModule");
					$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
					$tvars['tvar_costData'] = $this->getCostList();
					$tvars['tvar_requirementsData'] = $this->getRequirementsList();
					$tvars['tvar_moduleAction'] 	= '?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
					$tvars['tvar_id'] 				= $this->data['moduleId'];
					$tvars['tvar_moduleLimit'] 		= floor(min($max,$this->data['maxInput']));
					$tvars['tvar_disableData'] 		= '';
					$tvars['tvar_inputSlider'] 		= $this->getInputSlider(
						'?p=module&action=add&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'], 
						'b'.$this->data['slotId'].'_'.$this->data['moduleId'], 
						1, 
						floor($max), 
						0,
						false,
						false);
					
					$this->d13->templateInject($this->d13->templateSubpage("sub.popup.build" , $tvars, true));
									
					$vars['tvar_button_name'] 	 = $this->d13->getLangUI("addModule");
					$vars['tvar_list_id'] 	 	 = "build-".$this->data['moduleId'];
					$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("addModule") . ' ' . $this->d13->getLangUI("tipModuleBuildup"));
					$html.= $this->d13->templateSubpage("button.popup.enabled", $vars);
					
				} else {
					$vars['tvar_button_name'] 	 = $this->d13->getLangUI("addModule") . " " . $this->d13->getLangUI("impossible");
					$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipModuleBuildupDisabled"));
					$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
				}
			
			} else {
				
				if ($this->d13->getGeneral('options', 'moduleUpgrade')) {
					if ($this->data['level'] < $this->data['maxLevel']) {
						if ($max > 0 && $this->data['costData']['ok'] && $this->data['reqData'] && $this->node->modules[$this->data['slotId']]['level'] < $this->data['maxLevel'] && $this->data['maxLevel'] > 1) {
						
							$tvars['tvar_title'] 			= $this->d13->getLangUI("upgrade");
							$tvars['tvar_moduleDuration'] 	= $this->data['duration'];
							$tvars['tvar_costData'] 		= $this->getCostList(true);
							$tvars['tvar_requirementsData'] = $this->getRequirementsList();
							$tvars['tvar_moduleAction'] 	= '?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'];
							$tvars['tvar_id'] 				= $this->data['moduleId'];
							$tvars['tvar_moduleLimit'] 		= floor(min($max,$this->data['maxInput']));
							$tvars['tvar_disableData'] 		= '';
							$tvars['tvar_inputSlider']		= $this->getInputSlider(
								'?p=module&action=upgrade&nodeId=' . $this->node->data['id'] . '&moduleId=' . $this->data['moduleId'] . '&slotId=' . $this->data['slotId'], 
								'u'.$this->data['slotId'].'_'.$this->data['moduleId'], 
								1, 
								floor($max), 
								$this->node->modules[$this->data['slotId']]['input'],
								false,
								false);
				
							$this->d13->templateInject($this->d13->templateSubpage("sub.popup.build" , $tvars, true));
							
							$vars['tvar_button_name'] 	 = $this->d13->getLangUI("upgrade");
							$vars['tvar_list_id'] 	 	 = "build-".$this->data['moduleId'];
							$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("upgrade") . ' ' . $this->d13->getLangUI("tipModuleBuildup"));
							$html = $this->d13->templateSubpage("button.popup.enabled", $vars);
							
						} else {
							$vars['tvar_button_name'] 	 = $this->d13->getLangUI("upgrade") . " " . $this->d13->getLangUI("impossible");
							$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipModuleBuildupDisabled"));
							$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
						}
					} else {
						$vars['tvar_button_name'] 	 = $this->d13->getLangUI("maxModuleLevel");
						$vars['tvar_button_tooltip'] = $this->d13->misc->toolTip($this->d13->getLangUI("tipModuleMaxLevel"));
						$html = $this->d13->templateSubpage("button.popup.disabled", $vars);
					}
				}
			}
		
		}
		
		return $html;

	}

	// ----------------------------------------------------------------------------------------
	// getStoredResourceList
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getStoredResourceList()
	{

		$html = '';
		
		if (isset($this->data['storedResource'])) {
			foreach($this->data['storedResource'] as $res) {
				$tmp_res = $this->d13->getResource($res);
				if ($tmp_res['active']) {
					$html .= '<a class="tooltip-left" data-tooltip="' . $this->d13->getLangUI('storage') . ' ' . $this->d13->getLangGL("resources", $res, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $tmp_res['icon'] . '" title="' . $this->d13->getLangGL("resources", $res, "name") . '"></a>';
				}
			}
		}
		
		if (empty($html)) {
			$html = $this->d13->getLangUI("none");
		}
		
		$tvars = array();
		$tvars['tvar_listImage']	= 'storage.png';
		$tvars['tvar_listDirectory']= 'icon';
		$tvars['tvar_listLabel']	= $this->d13->getLangUI("storage");
		$tvars['tvar_listContent'] 	= $html;
		$html 						= $this->d13->templateSubpage("sub.module.listentry", $tvars);
		
		return $html;

	}
	
	// ----------------------------------------------------------------------------------------
	// getOutputResourceList
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getOutputResourceList()
	{

		$html = '';
				
		if (isset($this->data['outputResource'])) {
			foreach($this->data['outputResource'] as $res) {
				$tmp_res = $this->d13->getResource($res);
				if ($tmp_res['active']) {
					$html .= '<a class="tooltip-left" data-tooltip="' . $this->d13->getLangUI('production') . ' ' . $this->d13->getLangGL("resources", $res, "name") . '"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $tmp_res['icon'] . '" title="' . $this->d13->getLangGL("resources", $res, "name") . '"></a>';
				}
			}
		}

		if (empty($html)) {
			$html = $this->d13->getLangUI("none");
		}
		
		$tvars = array();
		$tvars['tvar_listImage']	= 'production.png';
		$tvars['tvar_listDirectory']= 'icon';
		$tvars['tvar_listLabel']	= $this->d13->getLangUI("production");
		$tvars['tvar_listContent'] 	= $html;
		$html 						= $this->d13->templateSubpage("sub.module.listentry", $tvars);
				
		return $html;

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
	
	
	
}

// =====================================================================================EOF
