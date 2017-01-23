<?php

//========================================================================================
//
// STATUS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//----------------------------------------------------------------------------------------
// PROCESS MODEL
//----------------------------------------------------------------------------------------

global $d13;

$message = NULL;

$tvars = array();
$tvars['tvar_userRankings'] = '';

if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {


	$limit = 8;
	if (isset($_GET['page'])) {
		$offset = $limit * $_GET['page'];
	} else {
		$offset = 0;
	}
	
	$users = array();
	
	$result = $d13->dbQuery('select * from users order by trophies desc, level desc limit ' . $limit . ' offset ' . $offset);
	for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$row['league'] = misc::getLeague($row['level'], $row['trophies']);
			$users[] = $row;	
	}
		
	foreach ($users as $user) {
		$vars = array();
		$vars['tvar_listAvatar'] 	= $user['avatar'];
		$vars['tvar_listLeague']	= $d13->getLeague($user['league'], 'image');
		$vars['tvar_listName'] 		= $d13->getLangGL('leagues', $user['league'], 'name');
		$vars['tvar_listLink']		= '?p=status&userId='.$user['id'];
		$vars['tvar_listLabel'] 	= $user['name'];
		$vars['tvar_listAmount'] 	= $user['trophies'];
		$tvars['tvar_userRankings'] .= $d13->templateSubpage("sub.module.leaguecontent", $vars);
	}


} else {
	$message = $d13->getLangUI("accessDenied");
}

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------


$tvars['tvar_global_message'] = $message;

//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->templateRender("ranking", $tvars);

//=====================================================================================EOF

?>