<?php

//========================================================================================
//
// GRID
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

global $d13, $grid;

$message = NULL;

$d13->db->query('start transaction');

if (isset($_GET['x'], $_GET['y'])) {
	$x=misc::clean($_GET['x'], 'numeric');
	$y=misc::clean($_GET['y'], 'numeric');
	$vars='x='.$x.'&y='.$y;
}

$grid=new grid();
$grid->getAll();
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

$sc=count($grid->data);
$rc=sqrt($sc);
$minimap="";

for ($i=0; $i<$sc; $i++) {
	switch ($grid->data[$i]['type']) {
		case 0: $sectorColor='blue'; break;
		case 1: $sectorColor='green'; break;
		case 2: $sectorColor='brown'; break;
	}
	$minimap.='<div class="sector" style="background-color: '.$sectorColor.';" id="sector_'.$grid->data[$i]['x'].'_'.$grid->data[$i]['y'].'" onClick="fetch(\''.CONST_DIRECTORY.'index.php?p=getGrid&x='.$grid->data[$i]['x'].'&y='.$grid->data[$i]['y'].'\')"></div>';
	
	if (!(($i+1)%$rc)) {
		$minimap .= '<br>';
	}
}

$tvars['tvar_gridHTML'] = $minimap;

//- - -
$tvars['tvar_vars'] = "";
if (!empty($vars)) {
	$tvars['tvar_vars'] = ",".$vars;
}

//----------------------------------------------------------------------------------------
// Parse & Render Template
//----------------------------------------------------------------------------------------

$d13->tpl->render_page("grid", $tvars);

//=====================================================================================EOF

?>