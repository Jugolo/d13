<?php

//========================================================================================
//
// CORE.INC
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//- - - - - INCLUDES
include("d13_config.inc.php");

spl_autoload_register(function ($className){

    $className = str_replace('Controller','.control',$className);
    $fullPath = CONST_INCLUDE_PATH."classes/".$className.".class.php";
    if(is_file($fullPath)){
        require_once  $fullPath;
        return;
    }
    $fullPath = CONST_INCLUDE_PATH."control/".strtolower($className).".php";

    if(is_file($fullPath)){
        require_once  $fullPath;
        return;
    }
});


/*
//- - - - - INCLUDE CLASSES (change later to autoload)
include(CONST_INCLUDE_PATH."classes/d13_session.class.php");
include(CONST_INCLUDE_PATH."classes/d13_db.class.php");
include(CONST_INCLUDE_PATH."classes/d13_data.class.php");
include(CONST_INCLUDE_PATH."classes/d13_tpl.class.php");
include(CONST_INCLUDE_PATH."classes/d13_router.class.php");
include(CONST_INCLUDE_PATH."classes/d13_profiler.class.php");
include(CONST_INCLUDE_PATH."classes/d13_engine.class.php");
include(CONST_INCLUDE_PATH."classes/d13_controller.class.php");
include(CONST_INCLUDE_PATH."classes/d13_activation.class.php");
include(CONST_INCLUDE_PATH."classes/d13_alliance.class.php");
include(CONST_INCLUDE_PATH."classes/d13_blacklist.class.php");
include(CONST_INCLUDE_PATH."classes/d13_grid.class.php");
include(CONST_INCLUDE_PATH."classes/d13_message.class.php");
include(CONST_INCLUDE_PATH."classes/d13_queue.class.php");
include(CONST_INCLUDE_PATH."classes/d13_misc.class.php");
include(CONST_INCLUDE_PATH."classes/d13_node.class.php");
include(CONST_INCLUDE_PATH."classes/d13_unit.class.php");
include(CONST_INCLUDE_PATH."classes/d13_modulit.class.php");
include(CONST_INCLUDE_PATH."classes/d13_component.class.php");
include(CONST_INCLUDE_PATH."classes/d13_technology.class.php");
include(CONST_INCLUDE_PATH."classes/d13_combat.class.php");
include(CONST_INCLUDE_PATH."classes/d13_user.class.php");
include(CONST_INCLUDE_PATH."classes/d13_notes.class.php");
include(CONST_INCLUDE_PATH."classes/d13_module.class.php");
include(CONST_INCLUDE_PATH."classes/d13_logger.class.php");
include(CONST_INCLUDE_PATH."classes/d13_flags.class.php");

//- - - - - INCLUDE CONTROLLERS (change later to autoload)

include(CONST_INCLUDE_PATH."control/d13_navbar.control.php");
include(CONST_INCLUDE_PATH."control/d13_resbar.control.php");
include(CONST_INCLUDE_PATH."control/d13_modulelist.control.php");
include(CONST_INCLUDE_PATH."control/d13_login.control.php");
include(CONST_INCLUDE_PATH."control/d13_logout.control.php");
include(CONST_INCLUDE_PATH."control/d13_account.control.php");
include(CONST_INCLUDE_PATH."control/d13_activate.control.php");
include(CONST_INCLUDE_PATH."control/d13_admin.control.php");
include(CONST_INCLUDE_PATH."control/d13_alliance.control.php");
include(CONST_INCLUDE_PATH."control/d13_combat.control.php");
include(CONST_INCLUDE_PATH."control/d13_contact.control.php");
include(CONST_INCLUDE_PATH."control/d13_credits.control.php");
include(CONST_INCLUDE_PATH."control/d13_grid.control.php");
include(CONST_INCLUDE_PATH."control/d13_index.control.php");
include(CONST_INCLUDE_PATH."control/d13_message.control.php");
include(CONST_INCLUDE_PATH."control/d13_module.control.php");
include(CONST_INCLUDE_PATH."control/d13_node.control.php");
include(CONST_INCLUDE_PATH."control/d13_ranking.control.php");
include(CONST_INCLUDE_PATH."control/d13_register.control.php");
include(CONST_INCLUDE_PATH."control/d13_reset.control.php");
include(CONST_INCLUDE_PATH."control/d13_status.control.php");
include(CONST_INCLUDE_PATH."control/d13_terms.control.php");

*/
//- - - - - CREATE ENGINE
$d13 = new d13_engine();

//=====================================================================================EOF

?>