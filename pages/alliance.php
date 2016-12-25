<?php

//========================================================================================
//
// ALLIANCE
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

global $d13, $ui, $gl, $game;

$message = NULL;

$d13->db->query('start transaction');
if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action']))
{
 foreach ($_POST as $key=>$value) $_POST[$key]=misc::clean($value);
 foreach ($_GET as $key=>$value) $_GET[$key]=misc::clean($value);
 $alliance=new alliance();
 $status=$alliance->get('id', $_SESSION[CONST_PREFIX.'User']['alliance']);
 switch ($_GET['action'])
 {
  case 'get':
   if ($_SESSION[CONST_PREFIX.'User']['alliance'])
   {
    if ($status=='done') $alliance->getAll();
    else $message=$ui[$status];
   }
   else $invitations=alliance::getInvitations('user', $_SESSION[CONST_PREFIX.'User']['id']);
  break;
  case 'set':
   $nodes=node::getList($_SESSION[CONST_PREFIX.'User']['id']);
   $nodeList='';
   foreach ($nodes as $node)
    $nodeList.='<option value="'.$node->data['id'].'">'.$node->data['name'].'</option>';
   if (($status=='done')&&(isset($_POST['nodeId'], $_POST['name'])))
    if ($_POST['name']!='')
     if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
     {
      $node=new node();
      $status=$node->get('id', $_POST['nodeId']);
      if ($status=='done')
       if ($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
       {
        $alliance->data['name']=$_POST['name'];
        $status=$alliance->set($node->data['id']);
        $message=$ui[$status];
       }
       else $message=$d13->data->ui->get("accessDenied");
      else $message=$ui[$status];
     }
     else $message=$d13->data->ui->get("accessDenied");
    else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'add':
   if ($status=='noAlliance')
   {
    $nodes=node::getList($_SESSION[CONST_PREFIX.'User']['id']);
    if ($nodes)
    {
     $nodeList='';
     foreach ($nodes as $node)
      $nodeList.='<option value="'.$node->data['id'].'">'.$node->data['name'].'</option>';
     if (isset($_POST['nodeId'], $_POST['name']))
      if ($_POST['name']!='')
      {
       $alliance=new alliance();
       $node=new node();
       $status=$node->get('id', $_POST['nodeId']);
       if ($status=='done')
        if ($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
        {
         $alliance->data['name']=$_POST['name'];
         $alliance->data['user']=$_SESSION[CONST_PREFIX.'User']['id'];
         $status=$alliance->add($node->data['id']);
         if ($status=='done')
         {
          $status=$alliance->get('name', $_POST['name']);
          if ($status=='done') $_SESSION[CONST_PREFIX.'User']['alliance']=$alliance->data['id'];
         }
         $message=$ui[$status];
        }
        else $message=$d13->data->ui->get("accessDenied");
       else $message=$ui[$status];
      }
      else $message=$d13->data->ui->get("insufficientData");
    }
    else $message=$d13->data->ui->get("noNode");
   }
   else $message=$d13->data->ui->get("allianceSet");
  break;
  case 'remove':
   if ((isset($_GET['go']))&&($_GET['go']))
    if ($_SESSION[CONST_PREFIX.'User']['alliance'])
    {
     if ($status=='done')
      if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
      {
       $status=alliance::remove($_SESSION[CONST_PREFIX.'User']['alliance']);
       if ($status=='done')
       {
        $_SESSION[CONST_PREFIX.'User']['alliance']=0;
        header('location: alliance.php?action=get');
       }
       else $message=$ui[$status];
      }
      else $message=$d13->data->ui->get("accessDenied");
     else $message=$ui[$status];
    }
    else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'addInvitation':
   if (isset($_POST['name']))
    if ($_POST['name']!='')
     if ($status=='done')
      if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
      {
       $user=new user();
       if ($user->get('name', $_POST['name'])=='done')
       {
        $status=$alliance->addInvitation($user->data['id']);
        if ($status=='done')
        {
         $user->getPreferences('name');
         if ($user->preferences['allianceReports'])
         {
          $msg=new message();
          $msg->data['sender']=$_SESSION[CONST_PREFIX.'User']['name'];
          $msg->data['recipient']=$user->data['name'];
          $msg->data['subject']=$d13->data->ui->get("allianceInvitation");
          $msg->data['body']='<a class=\"link\" href=\"index.php?p=alliance&action=acceptInvitation&alliance='.$alliance->data['id'].'&user='.$user->data['id'].'\">'.$d13->data->ui->get("accept").'</a> '.$alliance->data['name'].' '.$d13->data->ui->get("alliance");
          $msg->data['viewed']=0;
          $status=$msg->add();
         }
        }
        $message=$d13->data->ui->get($status);
       }
       else $message=$d13->data->ui->get("noUser");
      }
      else $message=$d13->data->ui->get("accessDenied");
     else $message=$d13->data->ui->get("noAlliance");
    else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'removeInvitation':
   if (isset($_GET['alliance'], $_GET['user']))
   {
    $senderAlliance=new alliance();
    if ($senderAlliance->get('id', $_GET['alliance'])=='done')
     if (in_array($_SESSION[CONST_PREFIX.'User']['id'], array($_GET['user'], $senderAlliance->data['user'])))
     {
      $status=alliance::removeInvitation($_GET['alliance'], $_GET['user']);
      if ($status=='done') header('Location: alliance.php?action=get');
      else $message=$ui[$status];
     }
     else $message=$d13->data->ui->get("accessDenied");
    else $message=$d13->data->ui->get("noAlliance");
   }
   else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'acceptInvitation':
   if (isset($_GET['alliance'], $_GET['user']))
    if ($_SESSION[CONST_PREFIX.'User']['id']==$_GET['user'])
    {
     $status=alliance::acceptInvitation($_GET['alliance'], $_GET['user']);
     if ($status=='done') $_SESSION[CONST_PREFIX.'User']['alliance']=$_GET['alliance'];
     $message=$ui[$status];
    }
    else $message=$d13->data->ui->get("accessDenied");
   else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'removeMember':
   if ($status=='done')
    if (isset($_GET['user']))
     if ((($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])&&($_GET['user']!=$_SESSION[CONST_PREFIX.'User']['id']))||(($alliance->data['user']!=$_SESSION[CONST_PREFIX.'User']['id'])&&($_GET['user']==$_SESSION[CONST_PREFIX.'User']['id'])))
     {
      $status=$alliance->removeMember($_GET['user']);
      if ($status=='done')
      {
       if ($_GET['user']==$_SESSION[CONST_PREFIX.'User']['id']) $_SESSION[CONST_PREFIX.'User']['alliance']=0;
       header('Location: alliance.php?action=get');
      }
      $message=$ui[$status];
     }
     else $message=$d13->data->ui->get("accessDenied");
    else $message=$d13->data->ui->get("insufficientData");
   else $message=$d13->data->ui->get("noAlliance");
  break;
  case 'addWar':
   if (isset($_POST['name']))
    if ($_POST['name']!='')
     if ($status=='done')
      if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
      {
       $recipientAlliance=new alliance();
       if ($recipientAlliance->get('name', $_POST['name'])=='done')
        if ($alliance->data['id']!=$recipientAlliance->data['id'])
        {
         $status=$alliance->addWar($recipientAlliance->data['id']);
         if ($status=='done')
         {
          $user=new user();
          if ($user->get('id', $recipientAlliance->data['user'])=='done')
          {
           $user->getPreferences('name');
           if ($user->preferences['allianceReports'])
           {
            $msg=new message();
            $msg->data['sender']=$_SESSION[CONST_PREFIX.'User']['name'];
            $msg->data['recipient']=$user->data['name'];
            $msg->data['subject']=$d13->data->ui->get("warDeclaration");
            $msg->data['body']=$d13->data->ui->get("sender").': '.$alliance->data['name'].' '.$d13->data->ui->get("alliance");
            $msg->data['viewed']=0;
            $status=$msg->add();
            if ($status=='done') header('Location: alliance.php?action=get');
           }
          }
          else $message=$d13->data->ui->get("noUser");
         }
         $message=$ui[$status];
        }
        else $message=$d13->data->ui->get("accessDenied");
       else $message=$d13->data->ui->get("noAlliance");
      }
      else $message=$d13->data->ui->get("accessDenied");
     else $message=$d13->data->ui->get("noAlliance");
    else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'proposePeace':
   if (isset($_GET['recipient']))
    if ($status=='done')
     if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
     {
      $recipientAlliance=new alliance();
      if ($recipientAlliance->get('id', $_GET['recipient'])=='done')
      {
       $status=$alliance->proposePeace($recipientAlliance->data['id']);
       if ($status=='done') header('Location: alliance.php?action=get');
       $message=$ui[$status];
      }
      else $message=$d13->data->ui->get("noAlliance");
     }
     else $message=$d13->data->ui->get("accessDenied");
    else $message=$d13->data->ui->get("noAlliance");
   else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'removePeace':
   if (isset($_GET['recipient']))
    if ($status=='done')
     if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
     {
      $recipientAlliance=new alliance();
      if ($recipientAlliance->get('id', $_GET['recipient'])=='done')
      {
       $status=$alliance->removePeace($recipientAlliance->data['id']);
       if ($status=='done') header('Location: alliance.php?action=get');
       else $message=$ui[$status];
      }
      else $message=$d13->data->ui->get("noAlliance");
     }
     else $message=$d13->data->ui->get("accessDenied");
    else $message=$d13->data->ui->get("noAlliance");
   else $message=$d13->data->ui->get("insufficientData");
  break;
  case 'acceptPeace':
   if (isset($_GET['sender'], $_GET['recipient']))
    if ($status=='done')
     if (($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])&&($alliance->data['id']==$_GET['recipient']))
     {
      $senderAlliance=new alliance();
      if ($senderAlliance->get('id', $_GET['sender'])=='done')
      {
       $status=$alliance->acceptPeace($senderAlliance->data['id']);
       if ($status=='done')
       {
        $user=new user();
        if ($user->get('id', $senderAlliance->data['user'])=='done')
        {
         $user->getPreferences('name');
         if ($user->preferences['allianceReports'])
         {
          $msg=new message();
          $msg->data['sender']=$_SESSION[CONST_PREFIX.'User']['name'];
          $msg->data['recipient']=$user->data['name'];
          $msg->data['subject']=$d13->data->ui->get("peaceAccepted");
          $msg->data['body']=$d13->data->ui->get("sender").': '.$alliance->data['name'].' '.$d13->data->ui->get("alliance");
          $msg->data['viewed']=0;
          $status=$msg->add();
          if ($status=='done') header('Location: alliance.php?action=get');
         }
        }
        else $message=$d13->data->ui->get("noUser");
       }
       $message=$ui[$status];
      }
      else $message=$d13->data->ui->get("noAlliance");
     }
     else $message=$d13->data->ui->get("accessDenied");
    else $message=$d13->data->ui->get("noAlliance");
   else $message=$d13->data->ui->get("insufficientData");
  break;
 }
}
else $message=$d13->data->ui->get("accessDenied");
if ((isset($status))&&($status=='error')) $d13->db->query('rollback');
else $d13->db->query('commit');

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

$page = "alliance";

$tvars['tvar_allianceGet'] 		= '<a class="external" href="index.php?p=alliance&action=get">'.$alliance->data['name'].'</a>';
$tvars['tvar_allianceSet'] 		= "";
$tvars['tvar_allianceRemove'] 	= "";

 if ($alliance->data['user'] == $_SESSION[CONST_PREFIX.'User']['id']) {
    $tvars['tvar_allianceSet'] 		= '<a class="external" href="index.php?p=alliance&action=set">'.$d13->data->ui->get("set").'</a>';
    $tvars['tvar_allianceRemove'] 	= '<a class="external" href="index.php?p=alliance&action=remove">'.$d13->data->ui->get("remove").'</a>';
 }

$tvars['tvar_tpl_allianceMenu'] 	= $d13->tpl->parse($d13->tpl->get("alliance.menu"), $tvars);

//- - - - 
if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action'])) {

 switch ($_GET['action'])
 {
  case 'get':
  		$html = "";
	   if (isset($alliance->data['id']))
	   {
		if ($alliance->members)
		{
		 $html .= '<div class="section"> -> {{tvar_ui_members}}</div>';
		 foreach ($alliance->members as $member)
		 {
		  $html .=  '<div class="right">'.$member['name'];
		  if (($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])||($member['id']==$_SESSION[CONST_PREFIX.'User']['id']))
		   $html .=  ' | <a class="external" href="index.php?p=alliance&action=removeMember&user='.$member['id'].'">x</a>';
		  $html .=  '</div>';
		 }
		}
		if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
		 $html .=  '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_invitations}} | <a class="external" href="index.php?p=alliance&action=addInvitation">{{tvar_ui_invite}}</a></div>';
		foreach ($alliance->invitations as $invitation)
		{
		 $user=new user();
		 if ($user->get('id', $invitation['user'])=='done')
		 {
		  $accept='';
		  $removeLabel='x';
		  if ($user->data['id']==$_SESSION[CONST_PREFIX.'User']['id'])
		  {
		   $accept='<a class="external" href="index.php?p=alliance&action=acceptInvitation&alliance='.$invitation['alliance'].'&user='.$invitation['user'].'">{{tvar_ui_accept}}</a> | ';
		   $removeLabel=$ui['decline'];
		  }
		  $html .=  '<div class="right"> '.$user->data['name'].' | '.$accept.'<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance='.$invitation['alliance'].'&user='.$invitation['user'].'">'.$removeLabel.'</a></div>';
		 }
		}
		if ($alliance->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
		 $html .=  '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_wars}} | <a class="external" href="index.php?p=alliance&action=addWar">{{tvar_ui_goToWar}}</a></div>';
		foreach ($alliance->wars as $war)
		{
		 $otherAlliance=new alliance();
		 if ($alliance->data['id']==$war['sender']) $otherAllianceId=$war['recipient'];
		 else $otherAllianceId=$war['sender'];
		 if ($otherAlliance->get('id', $otherAllianceId)=='done')
		  if ($war['type']) $html .=  '<div class="right">'.$otherAlliance->data['name'].' | <a class="external" href="index.php?p=alliance&action=proposePeace&recipient='.$otherAlliance->data['id'].'">{{tvar_ui_peace}}</a></div>';
		  else
		  {
		   if ($alliance->data['id']==$war['recipient']) $ad='<a class="external" href="index.php?p=alliance&action=acceptPeace&sender='.$war['sender'].'&recipient='.$war['recipient'].'">{{tvar_ui_accept}}</a> / <a class="external" href="index.php?p=alliance&action=removePeace&recipient='.$otherAlliance->data['id'].'">{{tvar_ui_decline}}</a>';
		   else $ad='<a class="external" href="index.php?p=alliance&action=removePeace&recipient='.$otherAlliance->data['id'].'">x</a>';
		   $html .=  '<div class="right">'.$otherAlliance->data['name'].' {{tvar_ui_peace}} | '.$ad.'</div>';
		  }
		}
	   }
	   else
	   {
		$html .=  '<div class="section"><a class="external" href="index.php?p=alliance&action=add">{{tvar_ui_add}}</a></div>';
		if ($invitations)
		{
		 $html .=  '<div class="section"> -> {{tvar_ui_invitations}}</div>';
		 foreach ($invitations as $invitation)
		 {
		  $user=new user();
		  if ($user->get('id', $invitation['user'])=='done')
		  {
		   $accept='';
		   $removeLabel='x';
		   if ($user->data['id']==$_SESSION[CONST_PREFIX.'User']['id'])
		   {
			$accept='<a class="external" href="index.php?p=alliance&action=acceptInvitation&alliance='.$invitation['alliance'].'&user='.$invitation['user'].'">{{tvar_ui_accept}}</a> | ';
			$removeLabel=$ui['decline'];
		   }
		   $html .=  '<div class="right"> '.$user->data['name'].' | '.$accept.'<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance='.$invitation['alliance'].'&user='.$invitation['user'].'">'.$removeLabel.'</a></div>';
		  }
		 }
		}
	   }
	   $tvars['tvar_allianceHTML'] = $html;
	   $page = "alliance.get";
	   break;
  
  case 'set':
	   if (isset($alliance->data['id'])) {
		$costData='';
		foreach ($game['factions'][$node->data['faction']]['costs']['alliance'] as $key=>$cost) {
		  $costData.='<div class="cell">'.$cost['value'].'</div><div class="cell"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></div>';
		}
		$tvars['tvar_nodeList'] 	= $nodeList;
		$tvars['tvar_costData'] 	= $costData;
		$tvars['tvar_allianceName'] = $alliance->data['name'];
		$tvars['tvar_nodeID'] 		= $node->data['id'];
		$tvars['tvar_nodeName'] 	= $node->data['name'];
	   }
	   $page = "alliance.set";
	   break;
  
  case 'add':
	   if (isset($nodeList)) {
		$costData='';
		foreach ($game['factions'][$node->data['faction']]['costs']['alliance'] as $key=>$cost) {
			$costData.='<div class="cell">'.$cost['value'].'</div><div class="cell"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></div>';
		}
		$tvars['tvar_nodeList'] = $nodeList;
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_nodeID'] 	= $node->data['id'];
		$tvars['tvar_nodeName'] = $node->data['name'];
	   }
	   $page = "alliance.add";
	   break;
  
  case 'remove':
		$page = "alliance.remove";
		break;
	 
  case 'addInvitation':
		$page = "alliance.addInvitation";
		break;
  
  case 'addWar':
		$page = "alliance.addWar";
		break;
  
 }

}

//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->tpl->render_page($page, $tvars);

//=====================================================================================EOF
?>