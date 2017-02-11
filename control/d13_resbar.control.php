<?php

// ========================================================================================
//
// RESBAR.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_resBarController extends d13_controller
{
	
	private $user;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
		parent::__construct($d13);
		
		
		
		$this->user = $this->d13->createObject('user', $_SESSION[CONST_PREFIX . 'User']['id']);
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{
	
		
		
		$html = '';
		$tvars = array();
		
		$tvars['tvar_resEntry'] 	= '';
		
		//- - - - - Left Panel (not used ATM)
		$tvars['tvar_leftOptions'] 	= '';
		
		//- - - - - Player Stats
		if ($this->user->user_status == 'done') {
			foreach($this->d13->getGeneral("userstats") as $stat) {
				if ($stat['active'] && $stat['visible']) {
					$tvars['tvar_resImage'] 		= $stat['icon'];
					$tvars['tvar_resValue'] 		= $this->user->data[$stat['value']];
					$tvars['tvar_resColor'] 		= $stat['color'];
					if ($stat['isExp']) {
					$tvars['tvar_resPercentage'] 	= d13_misc::percentage(floor($this->user->data[$stat['value']]), d13_misc::nextlevelexp($this->user->data['level']));
					} else {
					$tvars['tvar_resPercentage'] 	= 0;
					}
					$tvars['tvar_resTooltip'] 		= $this->d13->getLangUI($stat['name']);
					if ($stat['isExp']) {
					$tvars['tvar_resTooltip'] 		.= ' '. floor($this->user->data[$stat['value']]) . '/' . d13_misc::nextlevelexp($this->user->data['level']);
					} else {
					$tvars['tvar_resTooltip'] 		.= ' '. floor($this->user->data[$stat['value']]) ;
					}
					$tvars['tvar_resEntry']			.= $this->d13->templateSubpage("sub.resource.entry", $tvars);
				}
			}
		}
	
		//- - - - - Resources
		if (isset($this->d13->node) && isset($this->d13->node->resources)) {
			foreach($this->d13->node->resources as $resource) {
				if ($this->d13->getResource($resource['id'], 'active') && $this->d13->getResource($resource['id'], 'visible')) {
				
					$tvars['tvar_resImage'] = $this->d13->getResource($resource['id'], 'icon');
					$tvars['tvar_resColor'] = $this->d13->getResource($resource['id'], 'color');
					$tvars['tvar_resValue'] 	= 0;
					$tvars['tvar_resPercentage'] = 0;
					$tvars['tvar_resTooltip'] 	= '';
				
					$tvars['tvar_resTooltip'] .= $this->d13->getLangGL('resources', $resource['id'], 'name') . ' ';
					if ($this->d13->getResource($resource['id'], 'limited')) {
						$tvars['tvar_resValue'] = floor($resource['value']) . '/' . floor($this->d13->node->storage[$resource['id']]);
						$tvars['tvar_resPercentage'] = d13_misc::percentage(floor($resource['value']), floor($this->d13->node->storage[$resource['id']]));
						$tvars['tvar_resTooltip'] .= floor($resource['value']) . '/' . floor($this->d13->node->storage[$resource['id']]);
					} else {
						$tvars['tvar_resValue'] = floor($resource['value']);
						$tvars['tvar_resTooltip'] .= floor($resource['value']);
					
					}
					if ($this->d13->node->production[$resource['id']]) {
						if (floor($resource['value']) < $this->d13->node->storage[$resource['id']]) {
							$tvars['tvar_resTooltip'] .= ' [+' . round($this->d13->node->production[$resource['id']]) . $this->d13->getLangUI('perHour') . ']';
						}
						else {
							if ($this->d13->getResource($resource['id'], 'limited')) {
								$tvars['tvar_resTooltip'] .= ' [' . $this->d13->getLangUI("full") . ']';
							}
						}
					}

					$tvars['tvar_resEntry'].= $this->d13->templateSubpage("sub.resource.entry", $tvars);
				}
			}
		}
	
		$tvars['tvar_nodeResources'] = $tvars['tvar_resEntry'];

		//- - - - - Open Right Panel
		$tvars['tvar_rightOptions'] = '';
		$html = $this->d13->node->queues->getQueueExpireNext();
	
		if ($this->d13->node->queues->getQueueCount() > 1) {
		$tvars['tvar_linkClass'] 	= 'open-panel"';
		$tvars['tvar_linkData'] 	= 'data-panel="right"';
		$tvars['tvar_linkImage'] 	= 'previous.png';
		$tvars['tvar_linkTooltip'] 	= $this->d13->getLangUI('active') . " " . $this->d13->getLangUI('task');
		$tvars['tvar_linkLabel'] 	= '';
		$tvars['tvar_rightOptions']	= $this->d13->templateSubpage("sub.resource.link", $tvars);
		}
		if (!empty($html)) {
		$tvars['tvar_rightOptions'] .= $html;
		}
		
		return $this->d13->templateSubpage("sub.resources", $tvars);
		
	}

}

// =====================================================================================EOF

