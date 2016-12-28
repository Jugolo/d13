<?php

// ========================================================================================
//
// RESET
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
//
// ----------------------------------------------------------------------------------------

global $d13;
$d13->dbQuery('start transaction');

if (isset($_POST['name'], $_POST['email'])) {
	foreach($_POST as $key => $value) {
		$_POST[$key] = misc::clean($value);
	}

	if ((($_POST['name'] != '')) && ($_POST['email'] != '')) {
		$user = new user();
		$status = $user->get('name', $_POST['name']);
		if ($status == 'done') {
			$newPass = rand(1000000000, 9999999999);
			$status = $user->resetPassword($_POST['email'], $newPass);
			include (CONST_INCLUDE_PATH . 'api/email.api.php');

			$body = CONST_GAME_TITLE . ' ' . $d13->getLangUI("newPassword") . ': ' . $newPass;
			if ($status == 'done') {
				$status = email(CONST_EMAIL, CONST_GAME_TITLE, $user->data['email'], CONST_GAME_TITLE . ' ' . $d13->getLangUI("resetPassword") , $body);
				$message = $$d13->getLangUI($status);
			}
		}
		else {
			$message = $$d13->getLangUI($status);
		}
	}
	else {
		$message = $d13->getLangUI("insufficientData");
	}
}

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// Setup Template Variables
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$tvars['tvar_user_email'] = $_SESSION[CONST_PREFIX . 'User']['email'];
$tvars['tvar_user_name'] = $_SESSION[CONST_PREFIX . 'User']['name'];

// ----------------------------------------------------------------------------------------
// Parse & Render Template
// ----------------------------------------------------------------------------------------

$d13->templateRender("reset", $tvars);

// =====================================================================================EOF

?>