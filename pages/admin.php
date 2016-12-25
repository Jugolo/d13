<?php

//========================================================================================
//
// ADMIN
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

global $d13, $ui, $gl;

$message = NULL;

$d13->db->query('start transaction');

if ((isset($_SESSION[CONST_PREFIX.'User']['level']))&&($_SESSION[CONST_PREFIX.'User']['level']>=3))
{
 if (isset($_GET['action'], $_POST['password']))
 {
  foreach ($_POST as $key=>$value)
   if ($key=='maxIdleTime') $_POST[$key]=misc::clean($value, 'numeric');
   else $_POST[$key]=misc::clean($value);
  switch ($_GET['action'])
  {
   case 'vars':
    if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password'])) $message=$ui[flags::set($_POST['name'], $_POST['value'])];
    else $message=$d13->data->ui->get("wrongPassword");
   break;
   case 'bans':
    $user=new user();
    $status=$user->get('name', $_POST['name']);
    if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
     if ($status=='done')
     {
      if ($_POST['level']>-1)
      {
       $user->data['level']=$_POST['level'];
       $message=$ui[$user->set()];
      }
      else $message=$ui[user::remove($user->data['id'])];
     }
     else $message=$ui[$status];
    else $message=$d13->data->ui->get("wrongPassword");
   break;
   case 'accounts':
    if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
     if ($_POST['maxIdleTime']>0)
     {
      $output=user::removeInactive($_POST['maxIdleTime']);
      $message=$output['found'].' '.$d13->data->ui->get("accountsFound").', '.$output['removed'].' '.$d13->data->ui->get("removed");
     }
     else $message=$d13->data->ui->get("insufficientData");
    else $message=$d13->data->ui->get("wrongPassword");
   break;
   case 'username':
    if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
     if ($_POST['name']!='')
     {
      $user=new user();
      $status=$user->get('name', $_POST['name']);
      if ($status=='done')
       $message='<div>'.$user->data['name'].'</div><div><div class="cell">'.$d13->data->ui->get("ip").': </div><div class="cell">'.$user->data['ip'].'</div></div><div><div class="cell">'.$d13->data->ui->get("email").': </div><div class="cell">'.$user->data['email'].'</div></div>';
      else $message=$ui[$status];
     }
     else $message=$d13->data->ui->get("insufficientData");
    else $message=$d13->data->ui->get("wrongPassword");
   break;
   case 'blacklist':
    if (isset($_GET['blacklistAction'], $_POST['type'], $_POST['value']))
     if ($_SESSION[CONST_PREFIX.'User']['password']==sha1($_POST['password']))
      switch ($_GET['blacklistAction'])
      {
       case 'add':
        $message=$ui[blacklist::add($_POST['type'], $_POST['value'])];
       break;
       case 'remove':
        foreach ($_POST['value'] as $value)
         $message=$ui[blacklist::remove($_POST['type'], $value)];
       break;
      }
     else $message=$d13->data->ui->get("wrongPassword");
    else $message=$d13->data->ui->get("insufficientData");
   break;
  }
 }
 $flags=$d13->flags->get('id');
 $flagNames='';
 $flagValues=array();
 foreach ($flags as $key=>$flag)
 {
  $flagNames.='<option value="'.$flag['name'].'">'.$ui[$flag['name']].'</option>';
  $flagValues[$key]='"'.$flag['value'].'"';
 }
 $blacklist=array('ip'=>blacklist::get('ip'), 'email'=>blacklist::get('email'));
 $temp='';
 foreach ($blacklist['ip'] as $item) $temp.='<option value="'.$item['value'].'">'.$item['value'].'</option>';
 $blacklist['ip']=$temp;
 $temp='';
 foreach ($blacklist['email'] as $item) $temp.='<option value="'.$item['value'].'">'.$item['value'].'</option>';
 $blacklist['email']=$temp;
}
else header('Location: logout.php');
if ((isset($status))&&($status=='error')) $d13->db->query('rollback');
else $d13->db->query('commit');

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;


$tvars['tvar_flagNames'] 		= $flagNames;
$tvars['tvar_flagsValue'] 		= $flags[0]['value'];
$tvars['tvar_flagsIP'] 			= $blacklist['ip'];
$tvars['tvar_flagsEmail'] 		= $blacklist['email'];
$tvars['tvar_flagValues'] 		= implode(', ', $flagValues);





if (isset($_GET['action'])) {
	$tvars['tvar_getAction'] 			= 'document.getElementById("action").selectedIndex=indexOfSelectValue(document.getElementById("action"), "'.$_GET['action'].'");';
}
 
if (isset($_GET['blacklistAction'])) {
	$tvars['tvar_getblacklistAction'] 	= 'document.getElementById("blacklistAction").selectedIndex=indexOfSelectValue(document.getElementById("blacklistAction"), "'.$_GET['blacklistAction'].'");';
}
 
if (isset($_POST['type'])) {
	$tvars['tvar_postType'] 			= 'document.getElementById("blacklist_'.$_GET['blacklistAction'].'_type").selectedIndex=indexOfSelectValue(document.getElementById("blacklist_'.$_GET['blacklistAction'].'_type"), "'.$_POST['type'].'");';

}



//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->tpl->render_page("admin", $tvars);

//=====================================================================================EOF

?>