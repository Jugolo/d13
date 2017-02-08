<?php

// ========================================================================================
//
// OBJECT.FACTORY.CLASS
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
// A simple factory class that is responsible for creating new instances of objects.
//
//
// ========================================================================================

class d13_factory_gameobject extends d13_factory_base

{

	// ----------------------------------------------------------------------------------------
	// constructor
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct()
	{
	
	
	}

	// ----------------------------------------------------------------------------------------
	// create
	//
	// ----------------------------------------------------------------------------------------
    public

    function create($args, &$node)
    {

        
        switch ($args['supertype']) {
        
            case 'resource':
                return new d13_gameobject_resource($args, $node);
                break;

            case 'technology':
                return new d13_gameobject_technology($args, $node);
                break;

            case 'component':
                return new d13_gameobject_component($args, $node);
                break;

            case 'unit':
                return new d13_gameobject_unit($args, $node);
                break;

            case 'turret':
                return new d13_gameobject_turret($args, $node);
                break;

			case 'shield':
                return new d13_gameobject_shield($args, $node);
                break;
            
            case 'buff':
                return new d13_gameobject_buff($args, $node);
                break;
            
            default:
                return NULL;
                break;
        }
        
    }
    
}

// =====================================================================================EOF
