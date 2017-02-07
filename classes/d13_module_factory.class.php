<?php

// ========================================================================================
//
// MODULE.FACTORY.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// A simple factory class that is responsible for creating new instances of modules.
//
// this whole class could be removed by refactoring actually as all module classes use
// the same constructor arguments.
//
// ========================================================================================

class d13_module_factory

{
    public static

    function create($moduleId, $slotId, $node)
    {

        global $d13;
        
        $args = array();
        $args['type']		= $d13->getModule($node->data['faction'], $moduleId, 'type');
        $args['supertype'] 	= 'module';
        $args['obj_id'] 	= $moduleId;
        $args['slotId'] 	= $slotId;

        switch ($args['type']) {
            case 'storage':
                return new d13_module_storage($args, $node);
                break;

            case 'harvest':
                return new d13_module_harvest($args, $node);
                break;

            case 'craft':
                return new d13_module_craft($args, $node);
                break;

            case 'train':
                return new d13_module_train($args, $node);
                break;

            case 'research':
                return new d13_module_research($args, $node);
                break;

            case 'alliance':
                return new d13_module_alliance($args, $node);
                break;

            case 'command':
                return new d13_module_command($args, $node);
                break;

            case 'warfare':
                return new d13_module_warfare($args, $node);
                break;

            case 'trade':
                return new d13_module_trade($args, $node);
                break;

            case 'storvest':
                return new d13_module_storvest($args, $node);
                break;

            case 'market':
                return new d13_module_market($args, $node);
                break;
			
			case 'defense':
                return new d13_module_defense($args, $node);
                break;
			
            default:
                return NULL;
                break;
        }
    }
}

