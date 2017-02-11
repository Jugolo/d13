<?php

// ========================================================================================
//
// EMPTY.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_activateController extends d13_controller
{
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
		parent::__construct($d13);
		$this->doControl();
		$this->getTemplate();
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------

	private
	
	function doControl()
	{
	
		
		$this->d13->dbQuery('start transaction');

		if (isset($_GET['user'], $_GET['code'])) {
			foreach($_GET as $key => $value) $_GET[$key] = d13_misc::clean($value);
			if ((($_GET['user'] != '')) && ($_GET['code'] != '')) {
			
				$user = $this->d13->createObject('user');
				$status = $user->get('name', $_GET['user']);
				
				if ($status == 'done') {
					$activation = new d13_activation();
					$status = $activation->get($user->data['id']);
					if ($status == 'done') $status = $activation->activate($_GET['code']);
					$message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("insufficientData");
		}

		if ((isset($status)) && ($status == 'error')) {
			$this->d13->dbQuery('rollback');
		} else {
			$this->d13->dbQuery('commit');
		}
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{
	
		
		
		$tvars = array();
		$this->d13->outputPage("activate", $tvars);
		
		
	}

}

// =====================================================================================EOF

