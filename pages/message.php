<?php

//========================================================================================
//
// MESSAGE
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

global $d13, $ui;

$message = NULL;

$d13->db->query('start transaction');
if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action']))
{
 foreach ($_POST as $key=>$value) $_POST[$key]=misc::clean($value);
 foreach ($_GET as $key=>$value)
  if (in_array($key, array('page', 'messageId'))) $_GET[$key]=misc::clean($value, 'numeric');
  else $_GET[$key]=misc::clean($value);
  
 switch ($_GET['action'])
 {
 
  case 'get':
   if (isset($_GET['messageId']))
   {
    $msg=new message();
    $status=$msg->get($_GET['messageId']);
    if ($status=='done')
     if ($msg->data['recipient']==$_SESSION[CONST_PREFIX.'User']['id'])
     {
      if (!$msg->data['viewed'])
      {
       $msg->data['viewed']=1;
       $msg->set();
      }
      $user=new user();
      $status=$user->get('id', $msg->data['sender']);
      if ($status=='done') $msg->data['senderName']=$user->data['name'];
      else $msg->data['senderName']=misc::getlang("game");
      $msg->data['body']=str_replace("\r\n", "<br>", $msg->data['body']);
      $msg->data['body']=str_replace("\n", "<br>", $msg->data['body']);
     }
     else $message=misc::getlang("accessDenied");
    else $message=$ui[$status];
   }
   else $message=misc::getlang("insufficientData");
  break;
  
  	// - - - - - - - - - 
	case 'add':
	
	if (isset($_GET['messageId'])) {
		$msg=new message();
    	$status=$msg->get($_GET['messageId']);
    	if ($status!='done') {
    		$msg=0;
    	} else if ($msg->data['recipient']!=$_SESSION[CONST_PREFIX.'User']['id']) {
    		$msg=0;
		}
	}
   
	if (isset($_POST['recipient'], $_POST['subject'], $_POST['body'])) {
    	if ($_POST['recipient']!='' && $_POST['subject']!='' && $_POST['body']!='' && !misc::blockword($_POST['body']) && !misc::blockword($_POST['subject'])) {
			$msg=new message();
   		 	$msg->data['sender']=$_SESSION[CONST_PREFIX.'User']['name'];
   		 	$msg->data['recipient']=$_POST['recipient'];
   		 	$msg->data['subject']=$_POST['subject'];
   	 		$msg->data['body']=$_POST['body'];
   		 	$msg->data['viewed']=0;
    		$message=$ui[$msg->add()];
    	} else {
    		$message=misc::getlang("insufficientData");
  		}
  	} else {
    	$message=misc::getlang("insufficientData");
  	}
  break;
  
  case 'remove':
   if (isset($_GET['messageId']))
   {
    $msg=new message();
    $status=$msg->get($_GET['messageId']);
    if ($status=='done')
    {
     if ($msg->data['recipient']==$_SESSION[CONST_PREFIX.'User']['id'])
     {
      $status=message::remove($_GET['messageId']);
      if ($status=='done') header('location: ?p=message&action=list');
      else $message=$ui[$status];
     }
     else $message=misc::getlang("accessDenied");
    }
    else $message=misc::getlang("noMessage");
   }
   else if (isset($_POST['messageId']))
   {
    foreach ($_POST['messageId'] as $id) message::remove($id);
    header('location: ?p=message&action=list');
   }
   else $message=misc::getlang("insufficientData");
  break;
  case 'removeAll':
   $status=message::removeAll($_SESSION[CONST_PREFIX.'User']['id']);
   if ($status=='done') header('location: ?p=message&action=list');
   else $message=$ui[$status];
  break;
  case 'list':
   $limit=20;
   if (isset($_GET['page'])) $offset=$limit*$_GET['page'];
   else $offset=0;
   $messages=message::getList($_SESSION[CONST_PREFIX.'User']['id'], $limit, $offset);
   $pageCount=ceil($messages['count']/$limit);
  break;
 }
}
else $message=misc::getlang("accessDenied");
if ((isset($status))&&($status=='error')) $d13->db->query('rollback');
else $d13->db->query('commit');

//----------------------------------------------------------------------------------------
// Setup Template Variables
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

$page = "message";

//- - - - - 
if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {
	
		case 'get':
			if (isset($msg->data['recipient'])) {
				$tvars['tvar_senderName'] = $msg->data['senderName'];
				$tvars['tvar_subject'] = $msg->data['subject'];
				$tvars['tvar_body'] = $msg->data['body'];
				$tvars['tvar_id'] = $msg->data['id'];
				$page = "message.get";
			}
			break;
		
		case 'add':
			$recipient=$subject=$body='';
			if (isset($msg->data['id'])) {
				$user=new user();
				$status=$user->get('id', $msg->data['sender']);
				if ($status=='done') {
					$recipient=$user->data['name'];
				}
				$subject='re: '.$msg->data['subject'];
				$body="\r\n\r\n-----\r\n".$msg->data['body'];
				
			}
			$tvars['tvar_subject'] = $subject;
			$tvars['tvar_recipient'] = $recipient;
			$tvars['tvar_body'] = $body;
			$page = "message.add";
			break;
		
		  case 'list':
		  
		  	$tvars['tvar_removeAll'] = "";
		  	$tvars['tvar_messages'] = "";
		  	$tvars['tvar_remove'] = "";
		 	$tvars['tvar_controls'] = "";
		  
			if (count($messages['messages'])) {
				$removeAll=' | <a class="external" href="?p=message&action=removeAll">'.misc::getlang("removeAll").'</a>';
			} else { 
				$removeAll='';
			}
			$tvars['tvar_removeAll'] = $removeAll;

			foreach ($messages['messages'] as $message) {
				if (!$message->data['viewed']) {
					$new=' style="text-decoration: underline;"';
				} else {
					$new='';
				}
				$hours=round((time()-strtotime($message->data['sent']))/3600, 2);
				$tvars['tvar_messages'] .= '<div><div class="cell"><input type="checkbox" name="messageId[]" value="'.$message->data['id'].'"></div><div class="cell"><a class="external" href="index.php?p=message&action=get&messageId='.$message->data['id'].'"'.$new.'>'.$message->data['subject'].'</a></div><div class="cell">'.$hours.' {{tvar_ui_hours}}</div><div class="cell"><a class="external" href="?p=message&action=remove&messageId='.$message->data['id'].'">x</a></div></div>';
			}

			if (count($messages['messages'])) {
				$tvars['tvar_remove'] ='<a class="external" href="javascript: document.getElementById(\'messageList\').submit()">'.misc::getlang("remove").'</a>';
			}
		
			if ($pageCount > 1) {
				$previous='';
				$next='';
				if (isset($_GET['page'])) {
					if ($_GET['page']) {
						$previous='<a class="external" href="?p=message&action=list&page='.($_GET['page']-1).'">'.misc::getlang("previous").'</a>';
					}
				} else if (!isset($_GET['page'])) {
						if ($pageCount) {
							$next='<a class="external" href="?p=message&action=list&page=1">'.misc::getlang("next").'</a>';
						}
				}
				
				if (isset($_GET['page']) && $pageCount-$_GET['page']-1) {
					$next='<a class="external" href="?p=message&action=list&page='.($_GET['page']+1).'">'.misc::getlang("next").'</a>';
				}
				
				$tvars['tvar_controls'] .= misc::getlang("tvar_ui_page").$previous.' <select class="dropdown" id="page" onChange="window.location.href=\'index.php?p=message&action=list&page=\'+this.value">';
				for ($i=0; $i<$pageCount; $i++) {
					$tvars['tvar_controls'] .= '<option value="'.$i.'">'.$i.'</option>';
				}
				$tvars['tvar_controls'] .= '</select> '.$next;
				if (isset($_GET['page'])) {
					$tvars['tvar_controls'] .= '<script type="text/javascript">document.getElementById("page").selectedIndex='.$_GET['page'].'</script>';
				}
			}

			$page = "message.list";
			break;
		
	  }
}


//----------------------------------------------------------------------------------------
// Parse & Render Template
//----------------------------------------------------------------------------------------

$d13->tpl->render_page($page, $tvars);

//=====================================================================================EOF

?>