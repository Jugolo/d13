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

class d13_factory_module extends d13_factory_base

{
	
	// ----------------------------------------------------------------------------------------
	// constructor
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct(d13_engine &$d13)
	{
		parent::__construct($d13);
	
	
	}

	// ----------------------------------------------------------------------------------------
	// create
	//
	// ----------------------------------------------------------------------------------------
    public

    function create($moduleId, $slotId, &$node)
    {

        
 
        $args = array();
        $args['type']		= $this->d13->getModule($node->data['faction'], $moduleId, 'type');
        $args['supertype'] 	= 'module';
        $args['id'] 		= $moduleId;
        $args['slotId'] 	= $slotId;

        switch ($args['type']) {
        
            case 'storage':
                return new d13_module_storage($args, $node, $this->d13);
                break;

            case 'harvest':
                return new d13_module_harvest($args, $node, $this->d13);
                break;

            case 'craft':
                return new d13_module_craft($args, $node, $this->d13);
                break;

            case 'train':
                return new d13_module_train($args, $node, $this->d13);
                break;

            case 'research':
                return new d13_module_research($args, $node, $this->d13);
                break;

            case 'alliance':
                return new d13_module_alliance($args, $node, $this->d13);
                break;

            case 'command':
                return new d13_module_command($args, $node, $this->d13);
                break;

            case 'warfare':
                return new d13_module_warfare($args, $node, $this->d13);
                break;

            case 'trade':
                return new d13_module_trade($args, $node, $this->d13);
                break;

            case 'storvest':
                return new d13_module_storvest($args, $node, $this->d13);
                break;

            case 'market':
                return new d13_module_market($args, $node, $this->d13);
                break;
			
			case 'defense':
                return new d13_module_defense($args, $node, $this->d13);
                break;
            
            /*
            case 'transform':
            	return new d13_module_transform($args, $node, $this->d13);
                break;
            
            case 'produce':
				return new d13_module_produce($args, $node, $this->d13);
                break;
             */
                
            default:
                return NULL;
                break;
                
        }
    }
    
}

// =====================================================================================EOF
