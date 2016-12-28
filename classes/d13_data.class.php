<?php

// ========================================================================================
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
// ========================================================================================
// ----------------------------------------------------------------------------------------
// d13_data
// @
//
// ----------------------------------------------------------------------------------------

class d13_data

{
	public $resources, $modules, $technologies, $units, $upgrades, $components, $general, $bw, $gl, $ui;

	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct()
	{
		$this->bw = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/d13_blockwords.locale.json"));
		$this->ui = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/d13_userinterface.locale.json"));
		$this->gl = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/d13_gamelang.locale.json"));
		$this->general = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_general.data.json"));
		$this->upgrades = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_upgrade.data.json"));
		$this->resources = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_resource.data.json"));
		$this->modules = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_module.data.json"));
		$this->components = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_component.data.json"));
		$this->technologies = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_technology.data.json"));
		$this->units = new d13_collection($this->loadFromJSON(CONST_INCLUDE_PATH . "data/d13_unit.data.json"));
	}

	// ----------------------------------------------------------------------------------------
	// loadFromJSON
	//
	// @
	// ----------------------------------------------------------------------------------------

	private
	function loadFromJSON($file)
	{
		return json_decode(file_get_contents($file) , true);
	}

	// ----------------------------------------------------------------------------------------
	// record_sort
	//
	// @
	// ----------------------------------------------------------------------------------------

	private
	function record_sort($records, $field, $reverse = false)
	{
		$hash = array();
		foreach($records as $key => $record) {
			$hash[$record[$field] . $key] = $record;
		}

		($reverse) ? krsort($hash) : ksort($hash);
		$records = array();
		foreach($hash as $record) {
			$records[] = $record;
		}

		return $records;
	}
}

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

	public

	function getByID($id, $field)
	{
		foreach($this->data as $entry) {
			if ($entry['id'] == $id) {
				if (isset($entry[$field])) {
					return $entry[$field];
				}
				else {
					return NULL;
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

		if ((array)$array !== $array) {
			return (string)$array;
		}
		else {
			return $array;
		}
	}
}

// =====================================================================================EOF

?>