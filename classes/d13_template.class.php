<?php

//========================================================================================
//
// TEMPLATE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class d13_tpl {

	//----------------------------------------------------------------------------------------
	// tpl_get
	// @ Retrieves the content of a template file and returns it
	// 3.0
	//----------------------------------------------------------------------------------------
	public function get($templatename) { 
		$output = "";
		if ($templatename != NULL && $templatename != "") {
			$filename = CONST_INCLUDE_PATH."templates/".$_SESSION[CONST_PREFIX.'User']['template']."/tpl/" . $templatename . ".tpl";
			if (file_exists($filename)) {
				$output = trim(file_get_contents($filename));
			}
		}
		return $output;
	}

	//----------------------------------------------------------------------------------------
	// tpl_parse
	// @ Replaces all array variables inside a template file that must be provided
	// 3.0
	//----------------------------------------------------------------------------------------
	public function parse($template, $vars) {
		if (isset($template) && isset($vars)) {
			foreach($vars as $a => $b) {
				$template = str_replace("{{{$a}}}", $b, $template);
			}
		}
		return $template;
	}

	//----------------------------------------------------------------------------------------
	// tpl_inject
	// @ Is used to manually inject markup at the bottom of the current page (like popups)
	// 3.0
	//----------------------------------------------------------------------------------------
	public function inject($content) {
		if (!empty($content)) {
			if (!isset($GLOBALS["cache"])) {
				$GLOBALS["cache"] = array();		
			}
			$GLOBALS["cache"][] = $content;
		}
	}

	//----------------------------------------------------------------------------------------
	// tpl_inject_get
	// @ Returns the injected markup and clears the temporary page cache
	// 3.0
	//----------------------------------------------------------------------------------------
	private function inject_get() {
		$html = "";
		if (isset($GLOBALS["cache"])) {
			foreach ($GLOBALS["cache"] as $cache) {
				$html .= $cache;
				$html .= "\n";
			}
		}
		unset($GLOBALS["cache"]);
		return $html;
	}

	//----------------------------------------------------------------------------------------
	// tpl_merge_ui_vars
	// @ 
	// 3.0
	//----------------------------------------------------------------------------------------
	private function merge_ui_vars() {
		global $ui;
		$vars = array();
		foreach ($ui as $key => $var) {
			$vars["tvar_ui_".$key] = $var;
		}
		return $vars;
	}

	//----------------------------------------------------------------------------------------
	// tpl_global_vars
	// @ Iterates and replaces all global template variables and components
	// 3.0
	//----------------------------------------------------------------------------------------
	private function global_vars($vars="") {
		
		global $d13;
		
		$tvars = array();
		$tvars["tvar_global_pagetitle"] 	= CONST_GAME_TITLE;
		$tvars["tvar_global_directory"] 	= CONST_DIRECTORY;
		$tvars["tvar_global_basepath"] 		= CONST_BASE_PATH;
		$tvars["tvar_global_template"] 		= $_SESSION[CONST_PREFIX.'User']['template'];
		$tvars["tvar_global_color"] 		= $_SESSION[CONST_PREFIX.'User']['color'];
		
		//- - - Hide Bottom Toolbar while logged in
		if (isset($_SESSION[CONST_PREFIX.'User']['id'])) {
			$tvars["tvar_global_notoolbar"]		= "no-toolbar";
		} else {
			$tvars["tvar_global_notoolbar"]		= "";
		}
		
		//- - - Setup the Message Box
		if (isset($vars["tvar_global_message"]) && !empty($vars["tvar_global_message"])) {
			$tvars["tpl_pvar_message"] 	= $this->parse($this->get("sub.messagebox"), $vars);
		} else {
			$tvars["tpl_pvar_message"]	= "";
		}
	
		//- - - Setup Messagebox
		if (isset($_SESSION[CONST_PREFIX.'User']['id'])) {
			$umcl = "";
			$umc=message::getUnreadCount($_SESSION[CONST_PREFIX.'User']['id']);
			if ($umc > 0) {
				$tvars["tvar_global_umcl"] = "_on";
			} else {
				$tvars["tvar_global_umcl"] = "_off";
			}
		} else {
			$tvars["tvar_global_umcl"] = "";
		}

		return $tvars;
	}

	//----------------------------------------------------------------------------------------
	// tpl_render_subpage
	// @ Renders a page according to the given template and variables and returns it
	// 3.0
	//----------------------------------------------------------------------------------------
	public function render_subpage($template, $vars="") {
	
		$tvars = array();
	
		if (!empty($vars)) {
			$tvars = array_merge($tvars, $vars);
		}
	
		$tvars = array_merge($tvars, $this->global_vars($vars));
		$tvars = array_merge($tvars, $this->merge_ui_vars());
	
		return $this->parse($this->get($template), $tvars);

	}

	//----------------------------------------------------------------------------------------
	// tpl_render
	// @ Renders a page according to the given template and variables and sends it to the client
	// 3.0
	//----------------------------------------------------------------------------------------
	public function render_page($template, $vars="", $node="") {
	
		//- - - - - Merge all Template Variables
		$tvars = array();
	
		if (!empty($vars)) {
			$tvars = array_merge($tvars, $vars);
		}
		$tvars = array_merge($tvars, $this->global_vars($vars));
		$tvars = array_merge($tvars, $this->merge_ui_vars());
		$tvars["tpl_pvar_name"]	= $template;

		//- - - - - Setup the top Navbar
		$subnavbar = "";
		$includepath = CONST_INCLUDE_PATH."pages/sub.navbar.php";
		include($includepath);
		$subnavbar = sub_navbar($node);
		$tvars["tpl_page_navbar"] = $subnavbar;

		//- - - - - Setup the Resource Bar only if accessing a Node
		$subnavbar = "";
		$tvars["tpl_pvar_subnavbar"]	= "";
		$tvars["tpl_page_subbar"]		= "";
		if (!empty($node)) {
			$includepath = CONST_INCLUDE_PATH."pages/sub.resources.php";
			include($includepath);
			$subnavbar = sub_resources($node);
			if (!empty($subnavbar)) {
				$tvars["tpl_pvar_subnavbar"]		= "with-subnavbar";
			}
		}
		$tvars["tpl_page_subbar"]		= $subnavbar;
		
		//- - - - - Setup the rest
		$tvars["tpl_page_meta_header"] 		= $this->parse($this->get("meta.header"), $tvars);
		$tvars["tpl_page_meta_footer"] 		= $this->parse($this->get("meta.footer"), $tvars);
		
		$tvars["tpl_page_cache"] 			= $this->inject_get();
		$tvars["tpl_page_content"] 			= $this->parse($this->get($template), $tvars);

		echo $this->parse($this->get("page"), $tvars);

	}

}

//=====================================================================================EOF

?>