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

//----------------------------------------------------------------------------------------
// d13_data
// @ 
// 
//----------------------------------------------------------------------------------------
class d13_data {

	public $data, $lang = array();
	public $resources, $modules, $technologies, $units, $upgrades, $components, $general;
	public $bw, $gl, $ui;
	
	//----------------------------------------------------------------------------------------
	// construct
	// @ 
	// 
	//----------------------------------------------------------------------------------------
	public function __construct() {
		
		global $game, $bw, $ui, $gl, $d13_resources, $d13_upgrades;
		
		include(CONST_INCLUDE_PATH."data/d13_general.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_resource.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_component.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_module.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_technology.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_upgrade.data.inc.php");
		include(CONST_INCLUDE_PATH."data/d13_unit.data.inc.php");

		include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_userinterface.locale.inc.php");
		include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_gamelang.locale.inc.php");
		include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_blockwords.locale.inc.php");

		$this->general 		= new d13_collection($game);
		$this->upgrades 	= new d13_collection($d13_upgrades);
		$this->resources 	= new d13_collection($this->record_sort($d13_resources, "priority"));
		
		$tmp_modules = array();
		foreach ($game['modules'] as $object) {
			$tmp_modules[] = $this->record_sort($object, "priority");
		}
		$this->modules 		= new d13_collection($tmp_modules);
		
		$tmp_components = array();
		foreach ($game['components'] as $object) {
			$tmp_components[] = $this->record_sort($object, "priority");
		}
		$this->components 	= new d13_collection($tmp_components);
		
		$tmp_technologies = array();
		foreach ($game['technologies'] as $object) {
			$tmp_technologies[] = $this->record_sort($object, "priority");
		}
		$this->technologies = new d13_collection($tmp_technologies);
		
		$tmp_units = array();
		foreach ($game['units'] as $object) {
			$tmp_units[] = $this->record_sort($object, "priority");
		}
		$this->units = new d13_collection($tmp_units);
		
		$this->bw = new d13_collection($bw);
		$this->gl = new d13_collection($gl);
		$this->ui = new d13_collection($ui);
		
		/* -----------------------obsolete */
		$this->data = array();
		$this->lang = array();
		
		$this->data['components'] 		= $game['components'];
		$this->data['modules'] 			= $game['modules'];
		$this->data['technologies'] 	= $game['technologies'];
		$this->data['upgrades'] 		= $d13_upgrades;
		
		$this->lang['bw'] 				= $bw;
		$this->lang['gl'] 				= $gl;
		$this->lang['ui'] 				= $ui;
		/* -----------------------end obsolete */
		
	}
	
	//----------------------------------------------------------------------------------------
	// record_sort
	// 
	// @
	//----------------------------------------------------------------------------------------
	private function record_sort($records, $field, $reverse=false) {
		$hash = array();
		foreach($records as $key => $record) {
			$hash[$record[$field].$key] = $record;
		}
		($reverse)? krsort($hash) : ksort($hash);
		$records = array();
		foreach($hash as $record) {
			$records []= $record;
		}
		return $records;
	}

}

//----------------------------------------------------------------------------------------
// d13_data
// @ 
// 
//----------------------------------------------------------------------------------------
class d13_collection implements IteratorAggregate {
	
    private $data = array();
    
    public function __construct($data) {
    	$this->data = $data;
    }

    public function getIterator() {
        return new ArrayIterator($this->data);
    }
    
    public function getbyid($field, $id, $dimension=-1) {
		if ($dimension > -1) {
			$array = $this->data[$dimension];
		} else {
			$array = $this->data;
		}
		foreach ($array as $entry) {
			if ($entry['id'] == $id) {
				if (isset($entry[$field])) {
					return $entry[$field];
				} else {
					return NULL;
				}
			}
		}
		return  NULL;
 	}

 	//-- messy, refactor

 	public function getcount($key1=-1, $key2=-1, $key3=-1) {
    	if ($key1==-1 && $key2==-1 && $key3==-1) {
    		if (isset($this->data)){
    			return count($this->data);
        	}
        	return NULL;
    	} else if ($key2==-1 && $key3==-1) {
    		if (isset($this->data[$key1])){
            	return count($this->data[$key1]);
        	}
        	return NULL;
    	} else if ($key3==-1) {
    		if (isset($this->data[$key1][$key2])){
            	return count($this->data[$key1][$key2]);
        	}
        	return NULL;
    	} else {
    		if (isset($this->data[$key1][$key2][$key3])){
            	return count($this->data[$key1][$key2][$key3]);
        	}
        	return NULL;
    	}
    	return NULL;
    }
    
 	//-- messy, refactor
    public function get($key1, $key2=-1, $key3=-1, $key4=-1) {
    	if ($key4==-1 && $key3==-1 && $key2==-1) {
    		if (isset($this->data[$key1])){
            	return $this->data[$key1];
        	}
        	return NULL;
        } else if ($key4==-1 && $key3==-1) {
    		if (isset($this->data[$key1][$key2])){
            	return $this->data[$key1][$key2];
        	}
        	return NULL;
    	} else if ($key4==-1) {
    		if (isset($this->data[$key1][$key2][$key3])){
            	return $this->data[$key1][$key2][$key3];
        	}
        	return NULL;
    	} else {
    		if (isset($this->data[$key1][$key2][$key3][$key4])){
            	return $this->data[$key1][$key2][$key3][$key4];
        	}
        	return NULL;
    	}
    	return NULL;
    }
    
}

//=====================================================================================EOF

?>