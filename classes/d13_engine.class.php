<?php

// ========================================================================================
//
// ENGINE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13

{
	public $flags, $data;

	private $db, $tpl, $router, $logger, $session, $profiler;

	// ----------------------------------------------------------------------------------------
	// constructor
	//
	// ----------------------------------------------------------------------------------------

	public

	function __construct()
	{
		$this->db = new d13_db();
		$this->tpl = new d13_tpl();
		#$this->flags = new d13_flags();
		$this->router = new d13_router();
		$this->session = new d13_session();
		$this->logger = new d13_logger();
		$this->data = new d13_data();
		if (CONST_FLAG_PROFILER) {
			$this->profiler = new d13_profiler();
		}
	}

	// ========================================================================================
	//								LANG WRAPPER METHODS
	// ========================================================================================

	public

	function getLangBW($index)
	{
		return $this->data->bw->get($index);
	}

	public

	function getLangUI($index)
	{
		return $this->data->ui->get($index);
	}

	public

	function getLangGL()
	{
		return $this->data->gl->get(func_get_args());
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

	function templateSubpage($template, $vars = "")
	{
		return $this->tpl->render($template, $vars);
	}

	public

	function templateInject($content)
	{
		$this->tpl->inject($content);
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

	// ========================================================================================
	//								DATA WRAPPER METHODS
	// ========================================================================================

	public

	function getResource()
	{
		return $this->data->resources->get(func_get_args());
	}

	public

	function getGeneral()
	{
		return $this->data->general->get(func_get_args());
	}

	public

	function getLeague()
	{
		return $this->data->leagues->get(func_get_args());
	}

	public

	function getCombat()
	{
		return $this->data->combat->get(func_get_args());
	}
	
	public

	function getNavigation()
	{
		return $this->data->navigation->get(func_get_args());
	}

	public

	function getFaction()
	{
		return $this->data->factions->get(func_get_args());
	}

	public

	function getShield()
	{
		return $this->data->shields->get(func_get_args());
	}

	public

	function getUpgrade()
	{
		return $this->data->upgrades->get(func_get_args());
	}

	public

	function getModule()
	{
		return $this->data->modules->get(func_get_args());
	}

	public

	function getComponent()
	{
		return $this->data->components->get(func_get_args());
	}

	public

	function getTechnology()
	{
		return $this->data->technologies->get(func_get_args());
	}

	public

	function getUnit()
	{
		return $this->data->units->get(func_get_args());
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
}

// =====================================================================================EOF

?>