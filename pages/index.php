<?php

//========================================================================================
//
// INDEX
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//----------------------------------------------------------------------------------------
// PROCESS MODEL
//----------------------------------------------------------------------------------------

global $d13;

/*
if (!isset($_SESSION[CONST_PREFIX.'User']['id'])) {
	if (isset($_COOKIE[CONST_PREFIX.'Name'], $_COOKIE[CONST_PREFIX.'Password'])) {
		header('Location: index.php?p=login&action=login');
	}
}
*/

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------


//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->tpl->render_page("index");

//=====================================================================================EOF

?>