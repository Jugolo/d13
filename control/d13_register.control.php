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

class d13_registerController extends d13_controller
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
		$this->doControl();
		$this->getTemplate();
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{
	
		
		
		$message = NULL;
		$this->d13->dbQuery('start transaction');

		if ($this->d13->getGeneral('options', 'enabledRegister')) {
			if (isset($_POST['email'], $_POST['name'], $_POST['password'])) {
		
				if (!empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['password'])) {
					
					$user = $this->d13->createObject('user');
					
					if (!$this->d13->getLangBW($_POST['name'])) {
						if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
							$user->data['name'] = $_POST['name'];
							$user->data['email'] = $_POST['email'];
							$user->data['password'] = sha1($_POST['password']);
							if ($this->d13->GetGeneral('activationRequired')) {
								$user->data['access'] = 0;
							}
							else {
								$user->data['access'] = 1;
							}

							$user->data['joined'] = strftime('%Y-%m-%d', time());
							$user->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
							$user->data['ip'] = $_SERVER['REMOTE_ADDR'];
							$user->data['template'] = CONST_DEFAULT_TEMPLATE;
							$user->data['color'] = CONST_DEFAULT_COLOR;
							$user->data['locale'] = CONST_DEFAULT_LOCALE;
							$user->data['data'] = CONST_DEFAULT_DATA;
							$status = $user->add();
							$message = $this->d13->getLangUI($status);
							if ($this->d13->GetGeneral('activationRequired')) {
								if ($status == 'done') {
									include (CONST_INCLUDE_PATH . "api/email.api.php");

									$user->get('name', $user->data['name']);
									$code = rand(1000000000, 9999999999);
									$link = CONST_SERVER_PATH . 'activate.php?user=' . $user->data['name'] . '&code=' . $code;
									$body = CONST_GAME_TITLE . ' ' . $this->d13->getLangUI("accountActivationLink") . ': <a href="' . $link . '" target="_blank">' . $link . '</a>';
									$activation = new d13_activation();
									$activation->data['user'] = $user->data['id'];
									$activation->data['code'] = $code;
									$status = $activation->add();
									if ($status == 'done') {
										$status = d13_api::email(CONST_GAME_TITLE, $user->data['email'], CONST_GAME_TITLE . ' ' . $this->d13->getLangUI("registration") , $body);
									}
								}
							}
							else {
								setcookie(CONST_PREFIX . 'Name', $user->data['name'], (CONST_COOKIE_LIFETIME + time()));
								setcookie(CONST_PREFIX . 'Password', $user->data['password'], (CONST_COOKIE_LIFETIME + time()));
							}
						}
						else {
							$message = $this->d13->getLangUI("invalidEmail");
						}
					}
					else {
						$message = $this->d13->getLangUI("invalidName");
					}
				}
				else {
					$message = $this->d13->getLangUI("insufficientData");
				}
			}
		}
		else {
			$message = $this->d13->getLangUI("registrationDisabled");
		}

		if ((isset($status)) && ($status == 'error')) {
			$this->d13->dbQuery('rollback');
		}
		else {
			$this->d13->dbQuery('commit');
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
		
		$this->d13->templateInject($this->d13->templateSubpage("sub.register", $tvars));
		$this->d13->outputPage("register", $tvars);
		
	}

}

// =====================================================================================EOF
