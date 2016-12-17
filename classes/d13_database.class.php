<?php

//========================================================================================
//
// DATABASE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_db {

	private $db;
	
	public function __construct() {
		$this->create(CONST_DB_HOST, CONST_DB_USER, CONST_DB_PASS, CONST_DB_NAME);
	}
	
	public function create($dbHost, $dbUser, $dbPass, $dbName) {
		$this->db=new mysqli($dbHost, $dbUser, $dbPass, $dbName);
		if ($this->db->connect_error) {
			die('Connect Error ('.$this->db->connect_errno .') '.$mysqli->connect_error);
		}
	}
	
	public function query($query) {
		if ($result=$this->db->query($query)) {
			return $result;
		} else {
			echo $this->db->error;
		}
	}
	
	public function fetch($result) {
		return $result->fetch_array(MYSQLI_ASSOC);
	}
	
	public function real_escape_string($string) {
		return $this->db->real_escape_string($string);
	}
	
	public function affected_rows() {
		return $this->db->affected_rows;
	}
	
}

//=====================================================================================EOF

?>