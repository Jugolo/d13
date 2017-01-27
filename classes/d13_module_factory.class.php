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
        $type = $d13->getModule($node->data['faction'], $moduleId, 'type');

        switch ($type) {
            case 'storage':
                return new d13_module_storage($moduleId, $slotId, $type, $node);
                break;

            case 'harvest':
                return new d13_module_harvest($moduleId, $slotId, $type, $node);
                break;

            case 'craft':
                return new d13_module_craft($moduleId, $slotId, $type, $node);
                break;

            case 'train':
                return new d13_module_train($moduleId, $slotId, $type, $node);
                break;

            case 'research':
                return new d13_module_research($moduleId, $slotId, $type, $node);
                break;

            case 'alliance':
                return new d13_module_alliance($moduleId, $slotId, $type, $node);
                break;

            case 'command':
                return new d13_module_command($moduleId, $slotId, $type, $node);
                break;

            case 'defense':
                return new d13_module_defense($moduleId, $slotId, $type, $node);
                break;

            case 'warfare':
                return new d13_module_warfare($moduleId, $slotId, $type, $node);
                break;

            case 'trade':
                return new d13_module_trade($moduleId, $slotId, $type, $node);
                break;

            case 'storvest':
                return new d13_module_storvest($moduleId, $slotId, $type, $node);
                break;

            case 'market':
                return new d13_module_market($moduleId, $slotId, $type, $node);
                break;

            default:
                return NULL;
                break;
        }
    }
}