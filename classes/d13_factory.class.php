<?php

// ========================================================================================
//
// FACTORY.CLASS
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
// 
//
// ========================================================================================

class d13_factory

{
	
	protected $d13;
	
	private $nodeFactory, $moduleFactory, $objectFactory, $gameObjectFactory;
	
	// ----------------------------------------------------------------------------------------
	// constructor
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct(d13_engine &$d13)
	{
		$this->d13 					= $d13;
		$this->nodeFactory 			= new d13_factory_node($d13);
		$this->moduleFactory		= new d13_factory_module($d13);
		$this->GameObjectFactory 	= new d13_factory_gameobject($d13);
		$this->objectFactory		= new d13_factory_object($d13);
		$this->controllerFactory	= new d13_factory_controller($d13);
	
	}
	
	// ----------------------------------------------------------------------------------------
	// createController
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function createController($type, $args=NULL)
	{
		return $this->controllerFactory->create($type, $args);
	}
	
	// ----------------------------------------------------------------------------------------
	// createObject
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function createObject($type, $args=NULL)
	{
		return $this->objectFactory->create($type, $args);
	}
	
	// ----------------------------------------------------------------------------------------
	// createGameObject
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function createGameObject($args, &$node)
	{
		
		return $this->GameObjectFactory->create($args, $node);
		
	}
	
	// ----------------------------------------------------------------------------------------
	// createModule
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function createModule($moduleId, $slotId, &$node)
	{
	
		return $this->moduleFactory->create($moduleId, $slotId, $node);
	
	}
	
	// ----------------------------------------------------------------------------------------
	// createNode
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function createNode()
	{
		return $this->nodeFactory->create();
	
	}

	// ----------------------------------------------------------------------------------------
	// getNodeList
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getNodeList($id, $otherNode=FALSE)
	{
		return $this->nodeFactory->getNodeList($id, $otherNode);
	
	}



}

// =====================================================================================EOF

