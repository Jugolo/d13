<?php

// ----------------------------------------------------------------------------------------
// d13_module_factory
//
// ----------------------------------------------------------------------------------------

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
        $args['node'] 		= $node;
        $args['slotId'] 	= $slotId;

        switch ($args['type']) {
            case 'storage':
                return new d13_module_storage($args);
                break;

            case 'harvest':
                return new d13_module_harvest($args);
                break;

            case 'craft':
                return new d13_module_craft($args);
                break;

            case 'train':
                return new d13_module_train($args);
                break;

            case 'research':
                return new d13_module_research($args);
                break;

            case 'alliance':
                return new d13_module_alliance($args);
                break;

            case 'command':
                return new d13_module_command($args);
                break;

            case 'warfare':
                return new d13_module_warfare($args);
                break;

            case 'trade':
                return new d13_module_trade($args);
                break;

            case 'storvest':
                return new d13_module_storvest($args);
                break;

            case 'market':
                return new d13_module_market($args);
                break;
			
			case 'defense':
                return new d13_module_defense($args);
                break;
			
            default:
                return NULL;
                break;
        }
    }
}