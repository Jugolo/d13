<?php

// ========================================================================================
//
// EMPTY.CONTROLLER
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_statusController extends d13_controller
{
	
	private $user;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct()
	{
		
		if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {

			if (isset($_GET['userId'])) {
				$userId = $_GET['userId'];
			} else {
				$userId = $_SESSION[CONST_PREFIX . 'User']['id'];
			}

			$this->user = new user($userId);

		} else {
			$message = $d13->getLangUI("accessDenied");
		}
		
		$this->getTemplate();
		
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{
	
		global $d13;
		
		$tvars = array();
		
		$d13->templateRender("status", $this->user->getTemplateVariables());

	}

}

// =====================================================================================EOF

?>