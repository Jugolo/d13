<?php

// ========================================================================================
//
// MODUELIST.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_moduleListController extends d13_controller
{
	
	private $slodId;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args, d13_engine &$d13)
	{
		parent::__construct($d13);
		
		$this->slotId 	= $args['slotId'];	
	}


	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{

		return "module.list";
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplateVariables()
	{
	
		
		
		$tvars = array();
		
		$tvars['tvar_sub_list'] = "";
		$tvars['tvar_nodeFaction'] = $this->d13->node->data['faction'];
		
		$this->d13->node->queues->getQueue("build");
		
		foreach($this->d13->getModule($this->d13->node->data['faction']) as $module) {
		
			$count = $this->d13->node->getModuleCount($module['id']);
			
			if (count($this->d13->node->queues->queue["build"])) {
				foreach($this->d13->node->queues->queue["build"] as $item) {
					if ($item['obj_id'] == $module['id']) {
						$count++;
					}
				}
			}
			
			if ($count < $this->d13->getModule($this->d13->node->data['faction'], $module['id'], 'maxInstances')) {
				$tmp_module = NULL;
				$tmp_module = $this->d13->createModule($module['id'], $this->slotId, $this->d13->node);
				$tvars['tvar_sub_list'].= $this->d13->templateSubpage("sub.module.list", $tmp_module->getTemplateVariables());
			}
		}
		
		// - - - - Add Slider Initalize at bottom of the page
		$this->d13->templateInject($this->d13->templateSubpage("sub.swiper.horizontal", $tvars));
				
		return $tvars;
	}
	
}

// =====================================================================================EOF

