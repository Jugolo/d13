<?php

// ========================================================================================
//
// EMPTY.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_accountController extends d13_controller
{
		
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
		parent::__construct($d13);
		$tvars = array();
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
		
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{
	
		$tvars = array();
		
		$tvars['tvar_locales']		 	= $this->accountLocales();
		$tvars['tvar_templates'] 		= $this->accountTemplates();
		$tvars['tvar_colors'] 			= $this->accountColors();
		$tvars['tvar_datas'] 			= $this->accountDatas();
		
		$userData						= $this->accountUser();
		
		$tvars['tvar_preferenceNames'] 	= $userData[0];
		$tvars['tvar_user_preferences'] = $userData[1];
		$tvars['tvar_blocklistNames'] 	= $userData[2];
		$tvars['tvar_preferenceValues'] = $userData[3];
		
		return $tvars;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function accountLocales()
	{
		$dir = 'locales/'. $_SESSION[CONST_PREFIX . 'User']['template'];
		$locales = '';
		$locales = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['locale'] . '">' . $_SESSION[CONST_PREFIX . 'User']['locale'] . '</option>';
		if ($handle = opendir($dir)) {
			while (false != ($file = readdir($handle))) {
				if (is_dir($file)) {
					$fileName = explode('.', $file);
					$fileName = $fileName[0];
					if (($file != '.') && ($file != '..') && ($_SESSION[CONST_PREFIX . 'User']['locale'] != $fileName)) {
						$locales.= '<option value="' . $fileName . '">' . $fileName . '</option>';
					}
				}
			}
			closedir($handle);
		}
		
		return $locales;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function accountTemplates()
	{
		$dir = 'templates';
		$templates = '';
		$templates = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['template'] . '">' . $_SESSION[CONST_PREFIX . 'User']['template'] . '</option>';
		if ($handle = opendir($dir)) {
			while (false != ($file = readdir($handle))) {
				if ((strpos($file, '.') === false) && ($_SESSION[CONST_PREFIX . 'User']['template'] != $file)) {
					$templates.= '<option value="' . $file . '">' . $file . '</option>';
				}
			}
			closedir($handle);
		}
		
		return $templates;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function accountDatas()
	{
		$dir = 'data';
		$datas = '';
		$datas = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['data'] . '">' . $_SESSION[CONST_PREFIX . 'User']['data'] . '</option>';
		if ($handle = opendir($dir)) {
			while (false != ($file = readdir($handle))) {
				#if (is_dir($file)) {
					$fileName = explode('.', $file);
					$fileName = $fileName[0];
					if (($file != '.') && ($file != '..') && ($_SESSION[CONST_PREFIX . 'User']['data'] != $fileName)) {
						$datas.= '<option value="' . $fileName . '">' . $fileName . '</option>';
					}
				#}
			}
			closedir($handle);
		}
		
		return $datas;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function accountColors()
	{
		$colors = '';
		$colors = '<option value="' . $_SESSION[CONST_PREFIX . 'User']['color'] . '">' . $_SESSION[CONST_PREFIX . 'User']['color'] . '</option>';
		foreach($this->d13->getGeneral('colors') as $color) {
			if ($_SESSION[CONST_PREFIX . 'User']['color'] != $color) {
				$colors.= '<option value="' . $color . '">' . $color . '</option>';
			}
		}
		
		return $colors;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function accountUser()
	{
		
		$args = array();
		$args['id'] = $_SESSION[CONST_PREFIX . 'User']['id'];
		$user = $this->d13->createObject('user', $args);
		
		if (isset($_GET['action'], $_POST['password'])) {
		
			switch ($_GET['action']) {

			case 'misc':
				if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) {
					$user->data['email'] = $_SESSION[CONST_PREFIX . 'User']['email'] = $_POST['email'];
					$user->data['sitter'] = $_SESSION[CONST_PREFIX . 'User']['sitter'] = $_POST['sitter'];
					$user->data['locale'] = $_SESSION[CONST_PREFIX . 'User']['locale'] = $_POST['locale'];
					$user->data['color'] = $_SESSION[CONST_PREFIX . 'User']['color'] = $_POST['color'];
					$user->data['template'] = $_SESSION[CONST_PREFIX . 'User']['template'] = $_POST['template'];
					$user->data['data'] = $_SESSION[CONST_PREFIX . 'User']['data'] = $_POST['data'];
					$message = $this->d13->getLangUI($user->set());
				}
				else {
					$message = $this->d13->getLangUI("wrongPassword");
				}

				break;

			case 'preferences':
				if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) $message = $this->d13->getLangUI($user->setPreference($_POST['name'], $_POST['value']));
				else $message = $this->d13->getLangUI("wrongPassword");
				break;

			case 'blocklist':
				if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) $message = $this->d13->getLangUI($user->setBlocklist($_POST['name']));
				else $message = $this->d13->getLangUI("wrongPassword");
				break;

			case 'password':
				if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password']))
				if ($_POST['newPassword'] == $_POST['rePassword']) {
					$user->data['password'] = $_SESSION[CONST_PREFIX . 'User']['password'] = sha1($_POST['newPassword']);
					$message = $this->d13->getLangUI($user->set());
				}
				else $message = $this->d13->getLangUI("rePassNotMatch");
				else $message = $this->d13->getLangUI("wrongPassword");
				break;

			case 'remove':
				if ($_SESSION[CONST_PREFIX . 'User']['password'] == sha1($_POST['password'])) {
					$status = d13_user::remove($user->data['id']);
					if ($status == 'done') header('Location: index.php?p=logout');
					else $message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("wrongPassword");
				break;
			}
		}

		$user->getPreferences('id');
		$preferenceNames = '';
		$preferenceValues = array();
		foreach($user->preferences as $key => $preference) {
			$preferenceNames.= '<option value="' . $preference['name'] . '">' . $this->d13->getLangUI($preference['name']) . '</option>';
			$preferenceValues[$key] = '"' . $preference['value'] . '"';
		}

		$user->getBlocklist();
		$blocklistNames = '';
		foreach($user->blocklist as $item) {
			$blocklistNames.= $item['senderName'] . ' ';
		}
		
		return array($preferenceNames, $user->preferences[0]['value'], $blocklistNames, implode(', ', $preferenceValues));
		
	}

	
	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		
		
		
		$tvars['tvar_user_email'] = $_SESSION[CONST_PREFIX . 'User']['email'];
		$tvars['tvar_user_sitter'] = $_SESSION[CONST_PREFIX . 'User']['sitter'];
		
		$this->d13->outputPage("account", $tvars);
		
	}

}

// =====================================================================================EOF

