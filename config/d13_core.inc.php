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
include(CONST_INCLUDE_PATH."classes/d13_controller.class.php");
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

//- - - - - INCLUDE GAME DATA
include(CONST_INCLUDE_PATH."data/d13_basic.data.inc.php");
include(CONST_INCLUDE_PATH."data/d13_component.data.inc.php");
include(CONST_INCLUDE_PATH."data/d13_module.data.inc.php");
include(CONST_INCLUDE_PATH."data/d13_technology.data.inc.php");
include(CONST_INCLUDE_PATH."data/d13_upgrade.data.inc.php");
include(CONST_INCLUDE_PATH."data/d13_unit.data.inc.php");

//- - - - - CREATE ENGINE
$d13 = new d13();

//- - - - - READ LANGUAGE FILES
include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_userinterface.locale.inc.php");
include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_gamelang.locale.inc.php");
include(CONST_INCLUDE_PATH."locales/".$_SESSION[CONST_PREFIX.'User']['locale']."/d13_blockwords.locale.inc.php");

//- - - - - INCLUDE TEMPLATE DEFINITIONS
include(CONST_INCLUDE_PATH."templates/".$_SESSION[CONST_PREFIX.'User']['template']."/template.php");

//=====================================================================================EOF

?>