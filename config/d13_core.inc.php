<?php

//========================================================================================
//
// CORE.INC
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//- - - - - INCLUDES
include("d13_config.inc.php");

//- - - - - INCLUDE CLASSES
include(CONST_INCLUDE_PATH."classes/d13_session.class.php");
include(CONST_INCLUDE_PATH."classes/d13_database.class.php");
include(CONST_INCLUDE_PATH."classes/d13_template.class.php");
include(CONST_INCLUDE_PATH."classes/d13_router.class.php");
include(CONST_INCLUDE_PATH."classes/d13_profiler.class.php");
include(CONST_INCLUDE_PATH."classes/d13_engine.class.php");
include(CONST_INCLUDE_PATH."classes/d13_activation.class.php");
include(CONST_INCLUDE_PATH."classes/d13_alliance.class.php");
include(CONST_INCLUDE_PATH."classes/d13_blacklist.class.php");
include(CONST_INCLUDE_PATH."classes/d13_flags.class.php");
include(CONST_INCLUDE_PATH."classes/d13_grid.class.php");
include(CONST_INCLUDE_PATH."classes/d13_message.class.php");
include(CONST_INCLUDE_PATH."classes/d13_misc.class.php");
include(CONST_INCLUDE_PATH."classes/d13_node.class.php");
include(CONST_INCLUDE_PATH."classes/d13_unit.class.php");
include(CONST_INCLUDE_PATH."classes/d13_modulit.class.php");
include(CONST_INCLUDE_PATH."classes/d13_combat.class.php");
include(CONST_INCLUDE_PATH."classes/d13_user.class.php");
include(CONST_INCLUDE_PATH."classes/d13_data.class.php");
include(CONST_INCLUDE_PATH."classes/d13_module.class.php");
include(CONST_INCLUDE_PATH."classes/d13_logger.class.php");

//- - - - - CREATE ENGINE
$d13 = new d13();

//- - - - - INCLUDE TEMPLATE DEFINITIONS
include(CONST_INCLUDE_PATH."templates/".$_SESSION[CONST_PREFIX.'User']['template']."/template.php");

//=====================================================================================EOF

?>