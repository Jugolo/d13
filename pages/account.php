<?php

//========================================================================================
//
// ACCOUNT
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
// PROCESS MODEL
//----------------------------------------------------------------------------------------

global $d13, $ui, $gl, $template;

$message = NULL;

$d13->db->query('start transaction');

if (isset($_SESSION[CONST_PREFIX.'User']['id'])) {
	
	//- - - - - Locales
	$locales='<option value="'.$_SESSION[CONST_PREFIX.'User']['locale'].'">'.$_SESSION[CONST_PREFIX.'User']['locale'].'</option>';
	if ($handle=opendir('locales')) {
		while (false!=($file=readdir($handle))) {
			$fileName=explode('.', $file); $fileName=$fileName[0];
			if (($file!='.')&&($file!='..')&&($_SESSION[CONST_PREFIX.'User']['locale']!=$fileName)) {
				$locales.='<option value="'.$fileName.'">'.$fileName.'</option>';
			}
		}
		closedir($handle);
	}
	
	//- - - - - Templates
	$templates='<option value="'.$_SESSION[CONST_PREFIX.'User']['template'].'">'.$_SESSION[CONST_PREFIX.'User']['template'].'</option>';
	if ($handle=opendir('templates')) {
		while (false!=($file=readdir($handle))) {
			if ((strpos($file, '.')===false)&&($_SESSION[CONST_PREFIX.'User']['template']!=$file)) {
				$templates.='<option value="'.$file.'">'.$file.'</option>';
			}
		}
		closedir($handle);
	}
	
	//- - - - - Colors
	$colors='<option value="'.$_SESSION[CONST_PREFIX.'User']['color'].'">'.$_SESSION[CONST_PREFIX.'User']['color'].'</option>';
	foreach ($template['colors-ios'] as $color) {
		if ($_SESSION[CONST_PREFIX.'User']['color']!=$color['name']) {
			$colors.='<option value="'.$color['name'].'">'.$color['name'].'</option>';
		}
	}
	
	//- - - - - User
	$user=new user();
	$user->get('id', $_SESSION[CONST_PREFIX.'User']['id']);
	if (isset($_GET['action'], $_POST['password']))
	{
	foreach ($_POST as $key=>$value) $_POST[$key]=misc::clean($value);

switch ($_GET['action']) {

	//- - - - - 
	case 'misc':
		if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password'])) {
			$user->data['email']=$_SESSION[CONST_PREFIX.'User']['email']=$_POST['email'];
			$user->data['sitter']=$_SESSION[CONST_PREFIX.'User']['sitter']=$_POST['sitter'];
			$user->data['locale']=$_SESSION[CONST_PREFIX.'User']['locale']=$_POST['locale'];
			$user->data['color']=$_SESSION[CONST_PREFIX.'User']['color']=$_POST['color'];
			$user->data['template']=$_SESSION[CONST_PREFIX.'User']['template']=$_POST['template'];
			$message=$ui[$user->set()];
		} else {
			$message=misc::getlang("wrongPassword");
		}
	break;
	
	//- - - - - 
	case 'preferences':
		if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
	 		$message=$ui[$user->setPreference($_POST['name'], $_POST['value'])];
		else $message=misc::getlang("wrongPassword");
		break;
		
	//- - - - - 
	case 'blocklist':
		if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
		$message=$ui[$user->setBlocklist($_POST['name'])];
		else $message=misc::getlang("wrongPassword");
		break;
		
	//- - - - - 
	case 'password':
		if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
		 if ($_POST['newPassword']==$_POST['rePassword'])
		 {
		  $user->data['password']=$_SESSION[CONST_PREFIX.'User']['password']=sha1($_POST['newPassword']);
		  $message=$ui[$user->set()];
		 }
		 else $message=misc::getlang("rePassNotMatch");
		else $message=misc::getlang("wrongPassword");
		break;
		
	//- - - - - 
	case 'remove':
		if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
		{
		 $status=user::remove($user->data['id']);
		 if ($status=='done') header('Location: index.php?p=logout');
		 else $message=$ui[$status];
		}
		else $message=misc::getlang("wrongPassword");
		break;
	}
}

$user->getPreferences('id');
$preferenceNames='';
$preferenceValues=array();

foreach ($user->preferences as $key=>$preference) {
	$preferenceNames.='<option value="'.$preference['name'].'">'.$ui[$preference['name']].'</option>';
	$preferenceValues[$key]='"'.$preference['value'].'"';
}

$user->getBlocklist();
$blocklistNames='';

foreach ($user->blocklist as $item)
	$blocklistNames.=$item['senderName'].' ';
}

else header('Location: ?p=logout');

if ((isset($status))&&($status=='error')) {
	$d13->db->query('rollback');
} else {
	$d13->db->query('commit');
}

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

$tvars['tvar_user_email'] 			= $_SESSION[CONST_PREFIX.'User']['email'];
$tvars['tvar_user_sitter'] 			= $_SESSION[CONST_PREFIX.'User']['sitter'];

$tvars['tvar_locales'] 				= $locales;
$tvars['tvar_templates'] 			= $templates;
$tvars['tvar_colors'] 				= $colors;
$tvars['tvar_preferenceNames'] 		= $preferenceNames;
$tvars['tvar_user_preferences'] 	= $user->preferences[0]['value'];
$tvars['tvar_blocklistNames'] 		= $blocklistNames;

$tvars['tvar_preferenceValues'] = implode(', ', $preferenceValues);

//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->tpl->render_page("account", $tvars);

//=====================================================================================EOF

?>