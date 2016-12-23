<?php

//========================================================================================
//
// DATA.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_data {

	private $data, $lang = array();
	
	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct() {
		
		global $game, $bw, $ui, $gl, $d13_upgrades;
		
		include(CONST_INCLUDE_PATH."data/d13_basic.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_component.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_module.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_technology.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_upgrade.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_unit.data.inc.php");

		include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_userinterface.locale.inc.php");
		include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_gamelang.locale.inc.php");
		include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_blockwords.locale.inc.php");

		$this->data = array();
		$this->lang = array();
		
		#$this->data[''] = $game[''];
		#$this->data[''] =  $game[''];
		$this->data['components'] 		= $game['components'];
		$this->data['modules'] 			= $game['modules'];
		$this->data['technologies'] 	= $game['technologies'];
		$this->data['upgrades'] 		= $d13_upgrades;
		
		$this->lang['bw'] 				= $bw;
		$this->lang['gl'] 				= $gl;
		$this->lang['ui'] 				= $ui;

	}
	
	
	//----------------------------------------------------------------------------------------
	// getData
	// 
	// @ A simple wrapper to get the right data, this might change in the future.
	//----------------------------------------------------------------------------------------
	public function getData($constant) {
		
	}
	
	//----------------------------------------------------------------------------------------
	// getGL
	// 
	// @ A simple wrapper to get the right language string, this might change in the future.
	//----------------------------------------------------------------------------------------
	public function getGL($constant) {
		if (!empty($constant) && isset($this->lang['gl'][$constant])) {
			return $this->lang['gl'][$constant];
		} else {
			return "ERROR: empty gl lang";
		}
	}
	
	//----------------------------------------------------------------------------------------
	// getUI
	// 
	// @ A simple wrapper to get the right language string, this might change in the future.
	//----------------------------------------------------------------------------------------
	public function getUI($constant) {	
		if (!empty($constant) && isset($this->lang['ui'][$constant])) {
			return $this->lang['ui'][$constant];
		} else {
			return "ERROR: empty ui lang";
		}
	}
	
	//----------------------------------------------------------------------------------------
	// getBW
	// 
	// @ A simple wrapper to get the right language string, this might change in the future.
	//----------------------------------------------------------------------------------------
	public function getBW($string) {	
		foreach ($this->lang['bw'] as $blockword) {
   			if (stristr($blockword, $string) !== false) {
    			return true;
    		}
		}
		return false;
	}	


}

//=====================================================================================EOF

?>