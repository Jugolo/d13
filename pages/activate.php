<?php

// ========================================================================================
//
// ACTIVATE
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
// PROCESS MODEL
// ----------------------------------------------------------------------------------------

global $d13;
$d13->dbQuery('start transaction');

if (isset($_GET['user'], $_GET['code'])) {
	foreach($_GET as $key => $value) $_GET[$key] = misc::clean($value);
	if ((($_GET['user'] != '')) && ($_GET['code'] != '')) {
		$user = new user();
		$status = $user->get('name', $_GET['user']);
		if ($status == 'done') {
			$activation = new d13_activation();
			$status = $activation->get($user->data['id']);
			if ($status == 'done') $status = $activation->activate($_GET['code']);
			$message = $$d13->getLangUI($status);
		}
		else $message = $$d13->getLangUI($status);
	}
	else $message = $d13->getLangUI("insufficientData");
}

if ((isset($status)) && ($status == 'error')) $d13->dbQuery('rollback');
else $d13->dbQuery('commit');

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender("activate", $tvars);

// =====================================================================================EOF

?>