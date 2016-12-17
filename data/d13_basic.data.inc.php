<?php

//========================================================================================
//
// BASIC.DATA
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
// USERS
//----------------------------------------------------------------------------------------

$game['users']=array(
 'maxNodes'				=>1,														// max nodes per user
 'maxModules'			=>15,														// max modules per sector
 'maxSectors'			=>3,														// max sectors per node
 'idle'					=>60,														// max idle time [days] before auto-deletion
 'passwordResetIdle'	=>15,														// time between allowed password resets
 'preferences'			=>array('allianceReports'=>1,'combatReports'=>1, 'tradeReports'=>1),
 'speed'				=>array('research'=>1, 'build'=>1, 'craft'=>1, 'train'=>1, 'trade'=>1, 'combat'=>1),	//speed mofifiers
 'cost'					=>array('research'=>1, 'build'=>1, 'craft'=>1, 'train'=>1, 'trade'=>1, 'combat'=>1, 'set'=>1, 'move'=>1, 'alliance'=>1)//cost modifiers
);

//----------------------------------------------------------------------------------------
// OPTIONS
//----------------------------------------------------------------------------------------
$game['options']=array(
	'gridSystem'=>0,									// 0=abstract, 1=map-based (WIP)
	'factionFixation'=>true,							// true = new (conquered) nodes must be of same faction as old ones
	'moduleDemolish'=>true,								// true = allowed to remove (destroy) own modules
	'moduleUpgrade'=>true,								// true = allowed to upgrade own modules
	'unitAttackOnly'=>true,								// true = units are only allowed to attack, not defend
	'defensiveModuleDamage'=>true,						// true = defensive modules take damage in form of reduced workers
);

//----------------------------------------------------------------------------------------
// NAVIGATION
//----------------------------------------------------------------------------------------

$game['navigation'] = array(
	0=>array('active'=>true, 'login'=>false, 	'node'=>false, 	'class'=>'left', 	'name'=>'home', 		'link'=>'index', 				'icon'=>'home'),
	1=>array('active'=>true, 'login'=>false, 	'node'=>false, 	'class'=>'left', 	'name'=>'login', 		'link'=>'login&action=login', 	'icon'=>'exitRight'),
	2=>array('active'=>true, 'login'=>false, 	'node'=>false, 	'class'=>'left', 	'name'=>'register', 	'link'=>'register', 			'icon'=>'key'),
	3=>array('active'=>true, 'login'=>false, 	'node'=>false, 	'class'=>'right', 	'name'=>'terms', 		'link'=>'terms', 				'icon'=>'buttonX'),
	4=>array('active'=>true, 'login'=>false, 	'node'=>false, 	'class'=>'right', 	'name'=>'credits', 		'link'=>'credits', 				'icon'=>'buttonY'),
	5=>array('active'=>true, 'login'=>true, 	'node'=>true, 	'class'=>'left', 	'name'=>'nodes', 		'link'=>'node&action=list', 	'icon'=>'home'),
	6=>array('active'=>true, 'login'=>true, 	'node'=>false, 	'class'=>'left', 	'name'=>'messages', 	'link'=>'message&action=list',	'icon'=>'message{{tvar_global_umcl}}'),
	7=>array('active'=>true, 'login'=>true, 	'node'=>false, 	'class'=>'left', 	'name'=>'account', 		'link'=>'account', 				'icon'=>'wrench'),
	8=>array('active'=>true, 'login'=>true, 	'node'=>false,  'class'=>'right', 	'name'=>'logout', 		'link'=>'logout', 				'icon'=>'door')
);

//----------------------------------------------------------------------------------------
// RESOURCES
//----------------------------------------------------------------------------------------

$game['resources']=array(
 0=>array('type'=>'dynamic', 	'visible'=>1),		//type=>'dynamic'
 1=>array('type'=>'dynamic', 	'visible'=>1),		//type=>'dynamic'
 2=>array('type'=>'dynamic', 	'visible'=>1),		//type=>'dynamic'
 3=>array('type'=>'dynamic', 	'visible'=>1),		//type=>'static'
 4=>array('type'=>'dynamic', 	'visible'=>1),		//type=>'static'
 5=>array('type'=>'dynamic', 	'visible'=>1)		//type=>'static'
);

//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------

//factions
$game['factions']=array(
 0=>array(
	'active'=>true,
	'costs'=>array(
   		'move'		=>array(0=>array('resource'=>0, 'value'=>100)),
   		'set'		=>array(0=>array('resource'=>0, 'value'=>100)),
   		'alliance'	=>array(0=>array('resource'=>0, 'value'=>2000)),
   		'combat'	=>array(0=>array('resource'=>0, 'value'=>500))),
   		'storage'	=>array(0=>5000, 1=>5000, 2=>5000, 3=>100, 4=>100, 5=>200)),
		
 1=>array(
 	'active'=>false,
	'costs'=>array(
   		'move'		=>array(0=>array('resource'=>0, 'value'=>100)),
   		'set'		=>array(0=>array('resource'=>0, 'value'=>100)),
   		'alliance'	=>array(0=>array('resource'=>0, 'value'=>2000)),
   		'combat'	=>array(0=>array('resource'=>0, 'value'=>500))),
   		'storage'	=>array(0=>6000, 1=>6000, 2=>6000, 3=>100, 4=>80, 5=>150)),
		
 2=>array(
 	'active'=>false,
	'costs'=>array(
   		'move'		=>array(0=>array('resource'=>0, 'value'=>100)),
   		'set'		=>array(0=>array('resource'=>0, 'value'=>100)),
   		'alliance'	=>array(0=>array('resource'=>0, 'value'=>2000)),
   		'combat'	=>array(0=>array('resource'=>0, 'value'=>500))),
   		'storage'	=>array(0=>4000, 1=>4000, 2=>4000, 3=>100, 4=>150, 5=>250)),
		
);

//----------------------------------------------------------------------------------------
//  //unit types (unit limit, 0 = no limit)
//----------------------------------------------------------------------------------------

$game['types']=array(
 'hero'=>array('limit'=>1),
 'unit'=>array('limit'=>99999),
 'component'=>array('limit'=>99999)
);

//----------------------------------------------------------------------------------------
// //unit classes (vulnerableTo=>bonusDamage)
//----------------------------------------------------------------------------------------

$game['classes']=array(
 'spearman'=>array('duelist'=>0.5, 'archer'=>0.5),
 'swordsman'=>array('duelist'=>0.5, 'archer'=>-0.5),
 'duelist'=>array('archer'=>0.5, 'cavalry'=>0.5),
 'archer'=>array('cavalry'=>0.5, 'duelist'=>0.5),
 'cavalry'=>array('spearman'=>0.5),
 'static'=>array('archer'=>-0.5)
);

//----------------------------------------------------------------------------------------
// Unit Stats
//----------------------------------------------------------------------------------------
$game['stats'] = array(
	'hp', 'damage', 'armor', 'speed'
	);




//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------



//=====================================================================================EOF

?>