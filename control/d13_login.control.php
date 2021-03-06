<?php

// ========================================================================================
//
// LOGIN.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_loginController extends d13_controller
{
	
	private $actionId;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
		parent::__construct($d13);
		$this->actionId = $_GET['action'];
		$this->doControl();
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{
	
		switch ($this->actionId)
		{
		
			case 'sit':
				$this->doAssist();
				break;
				
			case 'login':
				$this->doLogin();
				break;
			
		}
		
		$this->getTemplate();

	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doLogin()
	{
				
		if (isset($_POST['name'], $_POST['password'])) {
			$name = strtolower($_POST['name']);
			$pass = sha1($_POST['password']);
			if (isset($_POST['remember'])) $remember = 1;
			else $remember = 0;
		}
		else
		if (isset($_COOKIE[CONST_PREFIX . 'Name'], $_COOKIE[CONST_PREFIX . 'Password'])) {
			$name = $this->d13->misc->clean($_COOKIE[CONST_PREFIX . 'Name']);
			$pass = $this->d13->misc->clean($_COOKIE[CONST_PREFIX . 'Password']);
			$remember = 1;
		}

		if (isset($name, $pass)) {
			$args = array();
			$args['key'] = 'name';
			$args['value'] = $name;
			$user = $this->d13->createObject('user', $args);
			
			if ($user->user_status == 'done')
			if ($this->d13->getGeneral('options', 'enabledLogin') || $user->data['access'] == 3)
			if ($user->data['password'] == $pass)
			if ($user->data['access']) {
				$user->data['ip'] = $_SERVER['REMOTE_ADDR'];
				$user->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
				$user->set();
				$_SESSION[CONST_PREFIX . 'User'] = $user->data;
				if ($remember) {
					setcookie(CONST_PREFIX . 'Name', $name, (CONST_COOKIE_LIFETIME + time()));
					setcookie(CONST_PREFIX . 'Password', $pass, (CONST_COOKIE_LIFETIME + time()));
				}
				else {
					setcookie(CONST_PREFIX . 'Name', $name, (time() - 1));
					setcookie(CONST_PREFIX . 'Password', $pass, (time() - 1));
				}

				header('Location: index.php?p=node&action=list');
			}
			else $message = $this->d13->getLangUI("inactive");
			else $message = $this->d13->getLangUI("wrongPassword");
			else $message = $this->d13->getLangUI("loginDisabled");
			else $message = $this->d13->getLangUI($status);
		}
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doAssist()
	{
	
		
		
		if (isset($_POST['user'], $_POST['sitter'], $_POST['password'])) {
			$user = $this->d13->createObject('user');
			$status = $user->get('name', $_POST['user']);
			if ($status == 'done') {
				$sitter = $this->d13->createObject('user');
				$status = $sitter->get('name', $_POST['sitter']);
				if ($status == 'done')
				if (sha1($_POST['password']) == $sitter->data['password'])
				if ($user->data['sitter'] == $sitter->data['name']) {
					$user->data['ip'] = $_SERVER['REMOTE_ADDR'];
					$user->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
					$user->set();
					$_SESSION[CONST_PREFIX . 'User'] = $user->data;
					header('Location: index.php');
				}
				else $message = $this->d13->getLangUI("accessDenied");
				else $message = $this->d13->getLangUI("wrongPassword");
				else $message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI($status);
		}
	
	
	}
	
	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate()
	{
	
		
		
		$tvars = array();
		
		switch ($this->actionId)
		{
	
			case 'sit':

			$this->d13->templateInject($this->d13->templateSubpage("sub.assist", $tvars));
			$this->d13->outputPage("login", $tvars);
			break;

			case 'login':

			$this->d13->templateInject($this->d13->templateSubpage("sub.login", $tvars));
			$this->d13->outputPage("login", $tvars);
			break;
		
			default:
			
			$this->d13->templateInject($this->d13->templateSubpage("sub.login", $tvars));
			$this->d13->outputPage("login", $tvars);
			break;
		
		}
		
	}
}

// =====================================================================================EOF

