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

error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
date_default_timezone_set('Europe/Berlin');
ob_start();

require_once("config/d13_core.inc.php");

$d13->controller->fork_page();

//=====================================================================================EOF

?>