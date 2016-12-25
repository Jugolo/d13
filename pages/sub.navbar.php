<?php

//========================================================================================
//
// SUB.NAVBAR
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
// sub_navbar
//----------------------------------------------------------------------------------------

function sub_navbar($node) {

	global $d13, $game;
	
	$html = '';
	$html_left = '';
	$html_right = '';
	$nodeId = 0;
	
	//- - - Place Profiler
	if (CONST_FLAG_PROFILER) {
		$html_right 	.= "<div class=\"color-gray\">[".$d13->profile->profile_get().' '. $d13->data->ui->get("ms"). "]</div>&nbsp;";
	}
	
	//- - - Place Admin Panel
	if ((isset($_SESSION[CONST_PREFIX.'User']['level'])) && ($_SESSION[CONST_PREFIX.'User']['level'] >= 3)) {
		$html_right 	.= "<a class=\"external\" href=\"".CONST_BASE_PATH."index.php?p=admin\">".$d13->data->ui->get("adminPanel")."</a>";
	}
	
	//- - - Place all Navigation Options
	foreach ($game['navigation'] as $nav) {
		if ($nav['active']) {
			if ( ($nav['login'] && isset($_SESSION[CONST_PREFIX.'User']['id'])) ||(!$nav['login'] && !isset($_SESSION[CONST_PREFIX.'User']['id'])) ) {
				$pass = true;
				$nodeId = 0;
				$html = '<a class="tooltip-bottom link external" data-tooltip="'.$d13->data->ui->get($nav['name']).'" href="index.php?p='.$nav['link'].'&nodeId='.$nodeId.'"><span><img class="resource" src="'.CONST_DIRECTORY.'templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/icon/'.$nav['icon'].'.png"></span></a>';
				if ($nav['class'] == 'left') {
					$html_left .= $html;
				} else {
					$html_right .= $html;
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------
	// Setup Template Variables
	//----------------------------------------------------------------------------------------
	$tvars = array();
	$tvars['tvar_nodeNavbarLeft'] 		= $html_left;
	$tvars['tvar_nodeNavbarRight'] 		= $html_right;
	
	//----------------------------------------------------------------------------------------
	// Parse & Render Template
	//----------------------------------------------------------------------------------------
	$subpage = $d13->tpl->render_subpage("sub.navbar", $tvars);
	
	return $subpage;

}

//=====================================================================================EOF

?>