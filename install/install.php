<?php

//========================================================================================
//
// INSTALL
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

global $d13;

require_once("../core/d13_core.inc.php");

$action = "";

//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------

chdir("../");

if (isset($_POST['email'], $_POST['name'], $_POST['password'], $_POST['rePassword']))
{

 foreach ($_POST as $key=>$value)
 {
  if ($_POST['name']==$_POST[$key]) $value=preg_replace('/[^a-zA-Z0-9]/', '', $value);
  $_POST[$key]=misc::clean($value);
 }
 
 if ((($_POST['email']!=''))&&($_POST['name']!='')&&(($_POST['password']!='')))
 {
  $user=new user(); $user->get('name', $_POST['name']);
  if ($_POST['password']==$_POST['rePassword'])
    if (!$user->data['id'])
    {
     $user->data['name']=$_POST['name'];
     $user->data['email']=$_POST['email'];
     $user->data['password']=sha1($_POST['password']);
     $user->data['level']=3;
     $user->data['joined']=strftime('%Y-%m-%d', time());
     $user->data['lastVisit']=strftime('%Y-%m-%d %H:%M:%S', time());
     $user->data['ip']=$_SERVER['REMOTE_ADDR'];
     $user->data['template']	= CONST_DEFAULT_TEMPLATE;
     $user->data['color']		= CONST_DEFAULT_COLOR;
     $user->data['locale']		= CONST_DEFAULT_LOCALE;
     $imageStats=getimagesize('install/grid.png');
     $image=imagecreatefrompng('install/grid.png');
     $query=array();
     for ($i=0; $i<$imageStats[0]; $i++)
      for ($j=0; $j<$imageStats[1]; $j++)
      {
       $pixelRGB=imagecolorat($image, $i, $j);
       $pixelG=($pixelRGB>>8)&0xFF;
       $pixelB=$pixelRGB&0xFF;
       if ($pixelB)
       {
        $sectorType=0;
        $sectorId=rand(1, 4);
       }
       else if ($pixelG)
       {
        $sectorType=1;
        $sectorId=rand(1, 10);
       }
       array_push($query, '('.$i.', '.$j.', '.$sectorType.', '.$sectorId.')');
      }
     $d13->db->query('insert into grid (x, y, type, id) values '.implode(', ', $query));
     $user->add();
     $message=misc::getlang("installed");
    }
    else $message=misc::getlang("nameTaken");
  else $message=misc::getlang("rePassNotMatch");
 }
 else $message=misc::getlang("insufficientData");
}

//----------------------------------------------------------------------------------------
// Setup Template Variables
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;

//----------------------------------------------------------------------------------------
// Parse & Render Template
//----------------------------------------------------------------------------------------

$d13->tpl->render_page("install", $tvars);

//=====================================================================================EOF


?>
