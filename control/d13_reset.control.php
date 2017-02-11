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

class d13_resetController extends d13_controller
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
	}

	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	private function
	
	doControl()
	{
	
		$this->d13->dbQuery('start transaction');

		if (isset($_POST['name'], $_POST['email'])) {
			
			if ((($_POST['name'] != '')) && ($_POST['email'] != '')) {
				$user = $this->d13->createObject('user');
				$status = $user->get('name', $_POST['name']);
				if ($status == 'done') {
					$newPass = rand(1000000000, 9999999999);
					$status = $user->resetPassword($_POST['email'], $newPass);
					include (CONST_INCLUDE_PATH . 'api/email.api.php');

					$body = CONST_GAME_TITLE . ' ' . $this->d13->getLangUI("newPassword") . ': ' . $newPass;			//TODO move to template
					
					if ($status == 'done') {
						$status = email(CONST_EMAIL, CONST_GAME_TITLE, $user->data['email'], CONST_GAME_TITLE . ' ' . $this->d13->getLangUI("resetPassword") , $body);
						$message = $this->d13->getLangUI($status);
					}
				}
				else {
					$message = $this->d13->getLangUI($status);
				}
			}
			else {
				$message = $this->d13->getLangUI("insufficientData");
			}
		}

		if ((isset($status)) && ($status == 'error')) {
			$this->d13->dbQuery('rollback');
		}
		else {
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
		$tvars['tvar_user_email'] = '';
		$tvars['tvar_user_name'] = '';

		if (isset($_SESSION[CONST_PREFIX . 'User']['email'], $_SESSION[CONST_PREFIX . 'User']['name'])) {
			$tvars['tvar_user_email'] 	= $_SESSION[CONST_PREFIX . 'User']['email'];
			$tvars['tvar_user_name']	= $_SESSION[CONST_PREFIX . 'User']['name'];
		}
		
		$this->d13->outputPage("reset", $tvars);
		
	}

}

// =====================================================================================EOF

