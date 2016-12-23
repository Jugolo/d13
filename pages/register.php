<?php

//========================================================================================
//
// REGISTER
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------

global $d13, $game;

$message = NULL;

$d13->db->query('start transaction');
$flags=$d13->flags->get('name');

if ($flags['register']) {
	if (isset($_POST['email'], $_POST['name'], $_POST['password'])) {
 		
		foreach ($_POST as $key=>$value) {
			if ($key=='name') $value=preg_replace('/[^a-zA-Z0-9]/', '', $value);
			$_POST[$key]=misc::clean($value);
		}
  
		if (!empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['password'])) {
			$user=new user();
			if (!$d13->data->getBW($_POST['name'])) {
				
					if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
						$user->data['name']=$_POST['name'];
						$user->data['email']=$_POST['email'];
						$user->data['password']=sha1($_POST['password']);
						if ($flags['activation']) {
							$user->data['level'] = 0;
						} else { 
							$user->data['level'] = 1;
						}
						$user->data['joined']=strftime('%Y-%m-%d', time());
						$user->data['lastVisit']=strftime('%Y-%m-%d %H:%M:%S', time());
						$user->data['ip']=$_SERVER['REMOTE_ADDR'];
						$user->data['template']		= CONST_DEFAULT_TEMPLATE;
						$user->data['color']		= CONST_DEFAULT_COLOR;
						$user->data['locale']		= CONST_DEFAULT_LOCALE;
						$status = $user->add();
						
						$message = $d13->data->getUI($status);

						if ($flags['activation']) {
							if ($status=='done') {
								include(CONST_INCLUDE_PATH."api/email.api.php");
								$user->get('name', $user->data['name']);
								$code=rand(1000000000, 9999999999);
								$link=CONST_SERVER_PATH.'activate.php?user='.$user->data['name'].'&code='.$code;
								$body=CONST_GAME_TITLE.' '.$d13->data->getUI("accountActivationLink").': <a href="'.$link.'" target="_blank">'.$link.'</a>';
								$activation=new d13_activation();
								$activation->data['user']=$user->data['id'];
								$activation->data['code']=$code;
								$status=$activation->add();
								if ($status=='done') {
									$status=email(CONST_GAME_TITLE, $user->data['email'], CONST_GAME_TITLE.' '.$d13->data->getUI("registration"), $body);
								}
								
							}
						} else {
							setcookie(CONST_PREFIX.'Name', $user->data['name'], (CONST_COOKIE_LIFETIME+time()));
         					setcookie(CONST_PREFIX.'Password', $user->data['password'], (CONST_COOKIE_LIFETIME+time()));
							
						}
	
					} else {
						$message=$d13->data->getUI("invalidEmail");
					}
				
			} else {
				$message=$d13->data->getUI("invalidName");
			}
		} else {
			$message=$d13->data->getUI("insufficientData");
		}
	}
	
} else {
	$message=$d13->data->getUI("registrationDisabled");
}

if ((isset($status)) && ($status=='error')) {
	$d13->db->query('rollback');
} else { 
	$d13->db->query('commit');
}
						
//----------------------------------------------------------------------------------------
// Setup Template Variables
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

//----------------------------------------------------------------------------------------
// Parse & Render Template
//----------------------------------------------------------------------------------------

// - - - - Inject Window Code at page bottom
$d13->tpl->inject($d13->tpl->render_subpage("sub.register", $tvars));

$d13->tpl->render_page("register", $tvars);

//=====================================================================================EOF

?>
