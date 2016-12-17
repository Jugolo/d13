<?php

//========================================================================================
//
// CONTROLLER.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_controller {

	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
	public function fork_page() {
		$this->sanitize_vars();
		if (isset($_GET['p'])) {
			$page = CONST_INCLUDE_PATH."pages/".$_GET['p'].".php";
		} else {
			$page = CONST_INCLUDE_PATH."pages/index.php";
		}
		include_once($page);
	}
	
	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
	public function sanitize_vars() {
		foreach ($_POST as $key=>$value) {
			if ($key=='name' || $key=='p' || $key=='action') {
		  		$value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
		  	}
		  	if (in_array($key, array('x', 'y', 'faction'))) {
		  		$_POST[$key]=$this->clean($value, 'numeric');
		  	} else {
				$_POST[$key]=$this->clean($value);
			}
		}
		foreach ($_GET as $key=>$value) {
			if ($key=='nodeId') {
		  		$_GET[$key]=$this->clean($value, 'numeric');
		  	} else {
		  		$_GET[$key]=$this->clean($value);
		  	}
		}
	}
	
	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
	public static function clean($data, $type=0) {
		
		global $d13;
		
		if (is_array($data)) {
			foreach ($data as $key=>$value) {
				if ($type && $type=='numeric') {
		 			if (!is_numeric($value)) {
		 				$value = 0;
		 			} else {
		 				$value=floor(abs($value));
		 			}
				}
				$value=$d13->db->real_escape_string($value);
				$data[$key]=htmlspecialchars($value);
			}
		} else {
			if ($type && $type=='numeric') {
				if (!is_numeric($data)) {
					$data = 0;
				} else {
					$data=floor(abs($data));
				}
			}
			$data=$d13->db->real_escape_string($data);
			$data=htmlspecialchars($data);
		}
		return $data;
	}

}

//=====================================================================================EOF

?>