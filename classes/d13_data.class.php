<?php

// ========================================================================================
//
// DATA.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Author......................: BlackScorp
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
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
	
	public $json;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct()
	{
		global $d13;
		
		$this->loadFiles();
		
	}

	// ----------------------------------------------------------------------------------------
	// loadFiles
	//
	// @
	// ----------------------------------------------------------------------------------------
	private

	function loadFiles()
	{

		$this->json = array();
		
		//- - - read language files
		$dir = new DirectoryIterator(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/");
		foreach ($dir as $fileinfo) {
    		if (!$fileinfo->isDot()) {
        		$name = $this->getRealName($fileinfo->getFilename());
        		if ($name) {
        			$this->json[$name] = $this->loadFromJSON($fileinfo->getPath() , $fileinfo->getFilename());
        		}
    		}
		}

		//- - - read data files
		$dir = new DirectoryIterator(CONST_INCLUDE_PATH . "data/");
		foreach ($dir as $fileinfo) {
    		if (!$fileinfo->isDot()) {
       		 	$name = $this->getRealName($fileinfo->getFilename());
       		 	if ($name) {
       		 		$this->json[$name] = $this->loadFromJSON($fileinfo->getPath() , $fileinfo->getFilename());
       		 	}
    		}
		}

	}
	
	// ----------------------------------------------------------------------------------------
	// getFileName
	//
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function getRealName($url)
	{
		
		if (substr($url, 0, 4) === "d13_") {
			$url = basename($url);
			$url = str_ireplace("d13_", "", $url);
			$url = str_ireplace(".data", "", $url);
			$url = str_ireplace(".locale", "", $url);
			$url = str_ireplace(".json", "", $url);
			$url = str_ireplace(".", "_", $url);
		} else {
			$url = "";
		}
		
		return $url;
	
	}

	// ----------------------------------------------------------------------------------------
	// loadFromJSON
	//
	// @
	// ----------------------------------------------------------------------------------------

	private

	function loadFromJSON($path, $name)
	{
   		
   		
   		$url = $path . "/" . $name;
		$cacheFile = CONST_INCLUDE_PATH . 'cache/data' . DIRECTORY_SEPARATOR . basename($url). ".php";
		
		if (is_file($cacheFile) && filemtime($url) < filemtime($cacheFile)) {
			$classConstruct = require_once $cacheFile;
			return $classConstruct;
		}
		
		$jsonString = file_get_contents($url);
		if (!$jsonString) {
			throw  new Exception('Cannot read url: ' . $url);
		}
		$jsonData = json_decode($jsonString, true);
		
		
		$vars = var_export($jsonData, true);
		$name = "d13_".$this->getRealName($name) ."_cache";
		
		$cacheFileData = '';
		$cacheFileData .= '<?php '.PHP_EOL;
		$cacheFileData .= 'class '.$name.' extends d13_collection {'.PHP_EOL;
		$cacheFileData .= '	public function __construct() {'.PHP_EOL;
		$cacheFileData .= '	$data = '.$vars.';'.PHP_EOL;
		$cacheFileData .= ' parent::__construct($data);';
		$cacheFileData .= '	}'.PHP_EOL;
		$cacheFileData .= '}'.PHP_EOL;
		$cacheFileData .= 'return new '.$name.'();'.PHP_EOL;
		$cacheFileData .= '?>';
		
		file_put_contents($cacheFile, $cacheFileData);
		$classConstruct = require_once $cacheFile;
		return $classConstruct;
		
	}

}

// =====================================================================================EOF

?>