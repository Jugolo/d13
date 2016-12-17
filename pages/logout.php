<?php

//========================================================================================
//
// LOGOUT
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

global $d13;

unset($_POST);
unset($_GET);
session_unset();

header('Location: '.CONST_DIRECTORY.'index.php?p=index');

//=====================================================================================EOF

?>
