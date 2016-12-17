<?php

//========================================================================================
//
// LOGIN
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

global $d13;

$message = NULL;


$d13->db->query('start transaction');
$flags=$d13->flags->get('name');

if (isset($_GET['action']))
{
 
 switch ($_GET['action'])
 {
 
  case 'login':
   if (isset($_POST['name'], $_POST['password']))
   {
    $name=$_POST['name'];
    $pass=sha1($_POST['password']);
    if (isset($_POST['remember'])) $remember=1;
    else $remember=0;
   }
   else if (isset($_COOKIE[CONST_PREFIX.'Name'], $_COOKIE[CONST_PREFIX.'Password']))
   {
    $name=misc::clean($_COOKIE[CONST_PREFIX.'Name']);
    $pass=misc::clean($_COOKIE[CONST_PREFIX.'Password']);
    $remember=1;
   }
   if (isset($name, $pass))
   {
    $user=new user();
    $status=$user->get('name', $name);
    if ($status=='done')
     if (($flags['login'])||($user->data['level']==3))
      if ($user->data['password']==$pass)
       if ($user->data['level'])
       {
        $user->data['ip']=$_SERVER['REMOTE_ADDR'];
        $user->data['lastVisit']=strftime('%Y-%m-%d %H:%M:%S', time());
        $user->set();
        $_SESSION[CONST_PREFIX.'User']=$user->data;
        if ($remember)
        {
         setcookie(CONST_PREFIX.'Name', $name, (CONST_COOKIE_LIFETIME+time()));
         setcookie(CONST_PREFIX.'Password', $pass, (CONST_COOKIE_LIFETIME+time()));
        }
        else
        {
         setcookie(CONST_PREFIX.'Name', $name, (time()-1));
         setcookie(CONST_PREFIX.'Password', $pass, (time()-1));
        }
        header('Location: index.php?p=node&action=list');
       }
       else $message=misc::getlang("inactive");
      else $message=misc::getlang("wrongPassword");
     else $message=misc::getlang("loginDisabled");
    else $message=misc::getlang($status);
   }
  break;
  
  case 'sit':
   if (isset($_POST['user'], $_POST['sitter'], $_POST['password']))
   {
    $user=new user();
    $status=$user->get('name', $_POST['user']);
    if ($status=='done')
    {
     $sitter=new user();
     $status=$sitter->get('name', $_POST['sitter']);
     if ($status=='done')
      if (sha1($_POST['password'])==$sitter->data['password'])
       if ($user->data['sitter']==$sitter->data['name'])
       {
        $user->data['ip']=$_SERVER['REMOTE_ADDR'];
        $user->data['lastVisit']=strftime('%Y-%m-%d %H:%M:%S', time());
        $user->set();
        $_SESSION[CONST_PREFIX.'User']=$user->data;
        header('Location: index.php');
       }
       else $message=misc::getlang("accessDenied");
      else $message=misc::getlang("wrongPassword");
     else $message=misc::getlang($status);
    }
    else $message=misc::getlang($status);
   }
  break;
 }
}
if ((isset($status))&&($status=='error')) $d13->db->query('rollback');
else $d13->db->query('commit');

//----------------------------------------------------------------------------------------
// Setup Template Variables
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

//----------------------------------------------------------------------------------------
// Parse & Render Template
//----------------------------------------------------------------------------------------
if (isset($_GET['action'])) {
	switch ($_GET['action']) {
	
		case 'sit':
			// - - - - Inject Window Code at page bottom
			$d13->tpl->inject($d13->tpl->render_subpage("sub.assist", $tvars));
			$d13->tpl->render_page("login", $tvars);
			break;
		case 'login':
			// - - - - Inject Window Code at page bottom
			$d13->tpl->inject($d13->tpl->render_subpage("sub.login", $tvars));
			$d13->tpl->render_page("login", $tvars);
			break;
	}
} else {

	$d13->tpl->inject($d13->tpl->render_subpage("sub.login", $tvars));
	$d13->tpl->render_page("login", $tvars);
	
}
//=====================================================================================EOF

?>