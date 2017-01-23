<?php

// ========================================================================================
//
// CONTROLLER.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_router

{

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function route()
	{
		$this->sanitize_vars();
		if (isset($_GET['p'])) {
			$page = CONST_INCLUDE_PATH . "pages/" . $_GET['p'] . ".php";
		} else {
			$page = CONST_INCLUDE_PATH . "pages/index.php";
		}
		
		if (file_exists($page)) {
			include_once ($page);
		} else {
			header("location:index.php");
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function sanitize_vars()
	{
		foreach($_POST as $key => $value) {
			if ($key == 'name' || $key == 'p' || $key == 'action') {
				$value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
			}

			if (in_array($key, array(
				'x',
				'y',
				'faction'
				))) {
				$_POST[$key] = $this->clean($value, 'numeric');
			}
			else {
				if (!in_array($key, array(
				'msgbody'
				))) {
				$_POST[$key] = $this->clean($value);
				}
			}
		}

		foreach($_GET as $key => $value) {
			if ($key == 'nodeId') {
				$_GET[$key] = $this->clean($value, 'numeric');
			}
			else {
				$_GET[$key] = $this->clean($value);
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function clean($data, $type = 0)
	{
		global $d13;
		if (is_array($data)) {
			foreach($data as $key => $value) {
				if ($type && $type == 'numeric') {
					if (!is_numeric($value)) {
						$value = 0;
					}
					else {
						$value = floor(abs($value));
					}
				} else if (is_string($data)) {

					$data = basename($data);
				}

				$value = $d13->dbRealEscapeString($value);
				$data[$key] = htmlspecialchars($value);
			}
		}
		else {
			if ($type && $type == 'numeric') {
				if (!is_numeric($data)) {
					$data = 0;
				}
				else {
					$data = floor(abs($data));
				}
			} else if (is_string($data)) {

				$data = basename($data);
			}
			
			$data = $d13->dbRealEscapeString($data);
			$data = htmlspecialchars($data);
		}

		return $data;
	}
}

// =====================================================================================EOF

?>