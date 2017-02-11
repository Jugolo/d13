<?php

// ========================================================================================
//
// ENGINE.CLASS
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
// This class holds several utility classes that are required all the time (like the database,
// the template and the game data itself). features a series of wrapper functions to access
// the classes within.
//
// ========================================================================================

class d13_engine

{

	public $flags, $node;

	private $data, $db, $tpl, $router, $logger, $session, $profiler, $factory;

	// ----------------------------------------------------------------------------------------
	// constructor
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct()
	{
		$this->db 		= new d13_db($this);
		$this->tpl 		= new d13_tpl($this);
		$this->router 	= new d13_router($this);
		$this->session 	= new d13_session($this);
		$this->data 	= new d13_data($this);
		$this->factory  = new d13_factory($this);
		$this->flags 	= new d13_flags($this);
		$this->logger 	= new d13_logger($this);
		$this->misc		= new d13_misc($this);
		$this->message	= new d13_message($this);
	}
	
	
	// ----------------------------------------------------------------------------------------
	// initPage
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function initPage()
	{
	
		if (empty($this->node)) {
			if (isset($_SESSION[CONST_PREFIX . 'User']['node']) && $_SESSION[CONST_PREFIX . 'User']['node'] > 0) {
				$this->node	= $this->createNode(); #new d13_node();
				$status = $this->node->get('id', $_SESSION[CONST_PREFIX . 'User']['node']);
				
			}
		}
		
		$this->routerRoute();
	
	}
	
	// ----------------------------------------------------------------------------------------
	// outputPage
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function outputPage($page, $tvars, &$node=NULL)
	{
	
		$this->templateRender($page, $tvars);
	
	}

	// ========================================================================================
	//								FACTORY WRAPPER METHODS
	// ========================================================================================

	public
	
	function createController($type, $args=NULL)
	{
		return $this->factory->createController($type, $args);
	}

	public
	
	function createObject($type, $id=NULL)
	{
		return $this->factory->createObject($type, $id);
	}

	public
	
	function createGameObject($args, &$node)
	{
		return $this->factory->createGameObject($args, $node);
	}
	
	public
	
	function createModule($moduleId, $slotId, &$node)
	{
		return $this->factory->createModule($moduleId, $slotId, $node);
	}
	
	public
	
	function createNode()
	{
		return $this->factory->createNode(func_get_args());
	}
	
	
	// ========================================================================================
	//								NODE WRAPPER METHODS
	// ========================================================================================
	public
	
	function getNodeList($id, $otherNode=FALSE)
	{
		return $this->factory->getNodeList($id, $otherNode);
	}
	
	// ========================================================================================
	//								LANG WRAPPER METHODS
	// ========================================================================================

	public

	function getLangBW($index=NULL)
	{
		return $this->data->json['blockwords']->get($index);
	}

	public

	function getLangUI($index=NULL)
	{
		return $this->data->json['userinterface']->get($index);
	}

	public

	function getLangGL()
	{
		return $this->data->json['gamelang']->get(func_get_args());
	}

	// ========================================================================================
	//								TEMPLATE WRAPPER METHODS
	// ========================================================================================

	public

	function templateRender($template, $vars = "", $node = "")
	{
		$this->tpl->render_page($template, $vars, $node);
	}

	public

	function templateParse($template, $vars)
	{
		return $this->tpl->parse($template, $vars);
	}

	public

	function templateGet($template)
	{
		return $this->tpl->get($template);
	}

	public

	function templateSubpage($template, $vars = "", $cache=true, $cache_num=0)
	{
		return $this->tpl->render($template, $vars, $cache, $cache_num);
	}

	public

	function templateInject($content, $cache=0)
	{
		$this->tpl->inject($content, $cache);
	}

	// ========================================================================================
	//								DATABASE WRAPPER METHODS
	// ========================================================================================

	public

	function dbQuery($query)
	{
		return $this->db->query($query);
	}

	public

	function dbFetch($result)
	{
		return $this->db->fetch($result);
	}

	public

	function dbRealEscapeString($string)
	{
		return $this->db->real_escape_string($string);
	}

	public

	function dbAffectedRows()
	{
		return $this->db->affected_rows();
	}

	public

	function getQueries()
	{
		return $this->db->getQueries();
	}

	public

	function getQueryCount()
	{
		return $this->db->getQueryCount();
	}

	// ========================================================================================
	//								DATA WRAPPER METHODS
	// ========================================================================================

	public

	function getResource()
	{
		return $this->data->json['resource']->get(func_get_args());
	}

	public

	function getGeneral()
	{
		return $this->data->json['general']->get(func_get_args());
	}

	public

	function getLeague()
	{
		return $this->data->json['leagues']->get(func_get_args());
	}

	public

	function getCombat()
	{
		return $this->data->json['combat']->get(func_get_args());
	}
	
	public

	function getNavigation()
	{
		return $this->data->json['navigation']->get(func_get_args());
	}

	public

	function getFaction()
	{
		return $this->data->json['factions']->get(func_get_args());
	}

	public

	function getShield()
	{
		return $this->data->json['shields']->get(func_get_args());
	}
	
	public

	function getBuff()
	{
		return $this->data->json['buff']->get(func_get_args());
	}
	
	public

	function getAvatar()
	{
		return $this->data->json['avatars']->get(func_get_args());
	}
	
	public

	function getAlliance()
	{
		return $this->data->json['alliances']->get(func_get_args());
	}
	
	public

	function getUpgradeUnit()
	{
		return $this->data->json['upgrade_unit']->get(func_get_args());
	}

	public

	function getUpgradeTurret()
	{
		return $this->data->json['upgrade_turret']->get(func_get_args());
	}

	public

	function getUpgradeModule()
	{
		return $this->data->json['upgrade_module']->get(func_get_args());
	}

	public

	function getUpgradeTechnology()
	{
		return $this->data->json['upgrade_technology']->get(func_get_args());
	}

	public

	function getUpgradeComponent()
	{
		return $this->data->json['upgrade_component']->get(func_get_args());
	}

	public

	function getModule()
	{
		return $this->data->json['module']->get(func_get_args());
	}

	public

	function getComponent()
	{
		return $this->data->json['component']->get(func_get_args());
	}

	public

	function getTechnology()
	{
		return $this->data->json['technology']->get(func_get_args());
	}

	public

	function getUnit()
	{
		return $this->data->json['unit']->get(func_get_args());
	}
	
	public

	function getRoute()
	{
		return $this->data->json['router']->get(func_get_args());
	}
	
	// ========================================================================================
	//								OTHER WRAPPER METHODS
	// ========================================================================================

	public

	function routerRoute()
	{
		$this->router->route();
	}

	public

	function logger($string)
	{
		$this->logger->log($string);
	}

	public

	function profileGet()
	{
		return $this->profiler->profile_get();
	}
	
	public
	
	function debugLog($page)
	{
		$this->logger->debugLog($page);
	}
	
	public
	
	function generateCallTrace()
	{
		$this->profiler->generateCallTrace();
	}
	
	
}

// =====================================================================================EOF
