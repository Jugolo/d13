<?php

// ========================================================================================
//
// SUB.NAVBAR
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
// sub_navbar
// ----------------------------------------------------------------------------------------

function sub_navbar($node)
{
	global $d13;
	$html = '';
	$html_left = '';
	$html_right = '';
	$nodeId = 0;

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
				
				$nodeId = 0;
				
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
				
				$html = '<a class="tooltip-bottom link external" data-tooltip="' . $d13->getLangUI($nav['name']) . '" href="index.php?p=' . $nav['link'] . '&nodeId=' . $nodeId . '"><span><img class="'.$class.' resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/' . $icon . '"></span></a>';
				if ($nav['class'] == 'left') {
					$html_left.= $html;
				} else {
					$html_right.= $html;
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// Setup Template Variables
	// ----------------------------------------------------------------------------------------

	$tvars = array();
	$tvars['tvar_nodeNavbarLeft'] = $html_left;
	$tvars['tvar_nodeNavbarRight'] = $html_right;

	// ----------------------------------------------------------------------------------------
	// Parse & Render Template
	// ----------------------------------------------------------------------------------------

	$subpage = $d13->templateSubpage("sub.navbar", $tvars);
	return $subpage;
}

// =====================================================================================EOF

?>