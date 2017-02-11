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

class d13_factory_object extends d13_factory_base

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

    function create($type, $id=NULL)
    {

        
        switch ($type) {
        
            case 'user':
                return new d13_user($id);
                break;

            
            
            default:
                return NULL;
                break;
        }
        
    }
    
}

// =====================================================================================EOF
