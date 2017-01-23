<?php

// ========================================================================================
//
// DATA.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Author......................: BlackScorp
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================


// ----------------------------------------------------------------------------------------
// d13_Collection
// @
//
// ----------------------------------------------------------------------------------------

class d13_collection implements IteratorAggregate
{
	private $data = array();
	
	public

	function __construct($data)
	{
		$this->data = $data;
	}

	public

	function getIterator()
	{
		return new ArrayIterator($this->data);
	}

	public

	function getAll()
	{
		return $this->data;
	}

	public

	function getByKey($key, $field)
	{
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key][$field];
		}
		else {
			return NULL;
		}
	}

	function getByID($id, $field="")
	{
		foreach($this->data as $entry) {
			if ($entry['id'] == $id) {
				if ($field = "" || !isset($entry[$field])) {
					return $entry;
				} else {
					return $entry[$field];
				}
			}
		}

		return NULL;
	}

	public

	function get($indices)
	{
		
		if (empty($indices)) {
			return $this->data;
		}

		$array = $this->data;
		if (!is_array($indices)) {
			$indices = array(
				$indices
			);
		}

		foreach($indices as $index) {
			if (isset($array[$index])) {
				$array = $array[$index];
			}
			else {
				return FALSE;
			}
		}

		if ((array)$array === $array) {
			return (array)$array;
		} else if ((string)$array === $array) {
			return (string)$array;
		} else if ((integer)$array === $array) {
			return (integer)$array;
		}
		
		return $array;
		
	}

}

// ----------------------------------------------------------------------------------------
// d13_data
// @
//
// ----------------------------------------------------------------------------------------

class d13_data

{
	public $resources, $modules, $technologies, $units, $upgrades, $components, $general, $navigation, $shields, $factions, $leagues, $combat, $bw, $gl, $ui;

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct()
	{
		global $d13;
		
		$this->bw 			= $this->loadFromJSON(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/d13_blockwords.locale.json");
		$this->ui 			= $this->loadFromJSON(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/d13_userinterface.locale.json");
		$this->gl 			= $this->loadFromJSON(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/d13_gamelang.locale.json");
		$this->general 		= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_general.data.json");
		$this->upgrades 	= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_upgrade.data.json");
		$this->resources 	= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_resource.data.json");
		$this->modules 		= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_module.data.json");
		$this->components 	= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_component.data.json");
		$this->technologies = $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_technology.data.json");
		$this->units 		= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_unit.data.json");
		$this->navigation 	= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_navigation.data.json");
		$this->factions 	= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_factions.data.json");
		$this->shields 		= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_shields.data.json");
		$this->leagues 		= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_leagues.data.json");
		$this->combat 		= $this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_combat.data.json");
	}

	// ----------------------------------------------------------------------------------------
	// loadFromJSON
	//
	// @
	// ----------------------------------------------------------------------------------------

	private

	function loadFromJSON($url)
	{
   		
		$cacheFile = CONST_INCLUDE_PATH . 'cache/data' . DIRECTORY_SEPARATOR . 'dta_'.md5($url) . ".".basename($url).".php";
		
		if (is_file($cacheFile) && filemtime($url) < filemtime($cacheFile)) {
			$classConstruct = require_once $cacheFile;
			return $classConstruct;
		}
		
		$jsonString = file_get_contents($url);
		if (!$jsonString) {
			throw  new Exception('Cannot read url: ' . $url);
		}
		$jsonData = json_decode($jsonString, true);
		
		$name = "dta_".md5($url);
		$vars = var_export($jsonData, true);

		$cacheFileData = '';
		$cacheFileData .= '<?php '.PHP_EOL;
		$cacheFileData .= 'class d13_'.$name.' extends d13_collection {'.PHP_EOL;
		$cacheFileData .= '	public function __construct() {'.PHP_EOL;
		$cacheFileData .= '	$data = '.$vars.';'.PHP_EOL;
		$cacheFileData .= ' parent::__construct($data);';
		$cacheFileData .= '	}'.PHP_EOL;
		$cacheFileData .= '}'.PHP_EOL;
		$cacheFileData .= 'return new d13_'.$name.'();'.PHP_EOL;
		$cacheFileData .= '?>';
		
		file_put_contents($cacheFile, $cacheFileData);
		$classConstruct = require_once $cacheFile;
		return $classConstruct;
		
	}

}

// =====================================================================================EOF

?>