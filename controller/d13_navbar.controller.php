<?php

// ========================================================================================
//
// NAVBAR.CONTROLLER
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_navBarController extends d13_controller
{
	
	private $node, $nodeId;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($node)
	{
		if (isset($node) && !empty($node)) {
			$this->node = $node;
			$this->nodeId = $this->node->data['id'];
		} else {
			$this->nodeId = 0;
		}	
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{
	
		global $d13;
		
		$tvars = array();
		$html = '';
		$html_left = '';
		$html_right = '';
		

		// - - - Place Profiler

		if (CONST_FLAG_PROFILER) {
			$html_right.= "<div class=\"color-gray\">[" . $d13->profileGet() . ' ' . $d13->getLangUI("ms") . "]</div>&nbsp;";
		}

		// - - - Place Admin Panel

		if ((isset($_SESSION[CONST_PREFIX . 'User']['access'])) && ($_SESSION[CONST_PREFIX . 'User']['access'] >= 3)) {
			$html_right.= "<a class=\"external\" href=\"" . CONST_BASE_PATH . "index.php?p=admin\">" . $d13->getLangUI("adminPanel") . "</a>";
		}
	
		// - - - Place all Navigation Options
		foreach($d13->getNavigation() as $nav) {
			if ($nav['active']) {
				if (($nav['login'] && isset($_SESSION[CONST_PREFIX . 'User']['id'])) || (!$nav['login'] && !isset($_SESSION[CONST_PREFIX . 'User']['id']))) {

					//- - - No Trigger
					if ($nav['trigger'] == '') {
						$icon = $nav['images'][0]['image'];
						$class = $nav['images'][0]['class'];
					
					//- - - Message Trigger
					} else if ($nav['trigger'] == 'messages' && isset($_SESSION[CONST_PREFIX . 'User']['id'])) {
				
						$umc = message::getUnreadCount($_SESSION[CONST_PREFIX . 'User']['id']);
						if ($umc <= 0) {
							$icon = $nav['images'][0]['image'];
							$class = $nav['images'][0]['class'];
						} else {
							$icon = $nav['images'][1]['image'];
							$class = $nav['images'][1]['class'];
						}
					
					}
				
					$html = '<a class="tooltip-bottom link external" data-tooltip="' . $d13->getLangUI($nav['name']) . '" href="index.php?p=' . $nav['link'] . '&nodeId=' . $this->nodeId . '"><span><img class="'.$class.' d13-icon" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/' . $icon . '"></span></a>';
					if ($nav['class'] == 'left') {
						$html_left.= $html;
					} else {
						$html_right.= $html;
					}
				}
			}
		}

		$tvars['tvar_nodeNavbarLeft'] = $html_left;
		$tvars['tvar_nodeNavbarRight'] = $html_right;

		$subpage = $d13->templateSubpage("sub.navbar", $tvars);
		
		return $subpage;
		
	}

}

// =====================================================================================EOF

?>