<?php

//========================================================================================
//
// UPGRADE.DATA
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

/*
	Required Stats for all Upgrade types:

		active
		type
		id
		stats
		
	Unit upgrades and Module upgrades for Defensive modules:
		
		stats	stat, value
		
		stat:	all, hp, armor, damage, speed
		
*/

$d13_upgrades = array(

//----------------------------------------------------------------------------------------
// FACTION 0
//----------------------------------------------------------------------------------------

0=>array(
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - SPEARMAN UPGRADE
	0=>array('active'=>true, 'type'=>'unit', 'id'=>0, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - PIKEMAN UPGRADE
	1=>array('active'=>true, 'type'=>'unit', 'id'=>1, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - FENCER UPGRADE
	2=>array('active'=>true, 'type'=>'unit', 'id'=>2, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - SWORDSMAN UPGRADE
	3=>array('active'=>true, 'type'=>'unit', 'id'=>3, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - KINGHT UPGRADE
	4=>array('active'=>true, 'type'=>'unit', 'id'=>4, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - BOWMAN UPGRADE
	5=>array('active'=>true, 'type'=>'unit', 'id'=>5, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - LONGBOWMAN UPGRADE
	6=>array('active'=>true, 'type'=>'unit', 'id'=>6, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MOUNTED SPEARMAN UPGRADE
	7=>array('active'=>true, 'type'=>'unit', 'id'=>7, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MOUNTED SWORDSMAN UPGRADE
	8=>array('active'=>true, 'type'=>'unit', 'id'=>8, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MOUNTED KNIGHT UPGRADE
	9=>array('active'=>true, 'type'=>'unit', 'id'=>9, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - SPY UPGRADE
	10=>array('active'=>true, 'type'=>'unit', 'id'=>10, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MILITIA UPGRADE
	11=>array('active'=>true, 'type'=>'unit', 'id'=>11,
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - THUG UPGRADE
	12=>array('active'=>true, 'type'=>'unit', 'id'=>12, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - BANDIT UPGRADE
	13=>array('active'=>true, 'type'=>'unit', 'id'=>13, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MILITIA BOWMAN UPGRADE
	14=>array('active'=>true, 'type'=>'unit', 'id'=>14, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - RANGER UPGRADE
	15=>array('active'=>true, 'type'=>'unit', 'id'=>15, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		),
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - THIEF UPGRADE
	16=>array('active'=>true, 'type'=>'unit', 'id'=>16, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		)
		),
		
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Padding for future unit upgrades
	17=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	18=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	19=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	20=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	21=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	22=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	23=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	24=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	25=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	26=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	27=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	28=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	29=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	30=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	31=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	32=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	33=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	34=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	35=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	36=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	37=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	38=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	39=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	40=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	41=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	42=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	43=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	44=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	45=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	46=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	47=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	48=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	49=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	50=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	51=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	52=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	53=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	54=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	55=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	56=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	57=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	58=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	59=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	60=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	61=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	62=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	63=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	64=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	65=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	66=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	67=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	68=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	69=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	70=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	71=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	72=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	73=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	74=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	75=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	76=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	77=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	78=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	79=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	80=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	81=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	82=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	83=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	84=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	85=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	86=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	87=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	88=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	89=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	90=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	91=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	92=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	93=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	94=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	95=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	96=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	97=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	98=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),
	99=>array('active'=>false, 'type'=>'unit', 'id'=>-1, 'stats'=>array()),


	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - GOLD MINE UPGRADE
	100=>array('active'=>true, 'type'=>'module', 'id'=>0, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		)
	),

	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - LUMBER MILL UPGRADE
	101=>array('active'=>true, 'type'=>'module', 'id'=>1, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		)
	),

	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - IRON MINE UPGRADE
	102=>array('active'=>true, 'type'=>'module', 'id'=>2, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		)
	),









	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ARCHER TOWER UPGRADE
	103=>array('active'=>true, 'type'=>'module', 'id'=>16, 
		'stats'=>array(
			0=>array('stat'=>'all', 'value'=>2)
		)
	),
	
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Add new upgrades here
		
	)
	
//----------------------------------------------------------------------------------------
// FACTION 1
//----------------------------------------------------------------------------------------
 
 
//----------------------------------------------------------------------------------------
// FACTION 2
//----------------------------------------------------------------------------------------
 
 
 
//----------------------------------------------------------------------------------------

);
 
//=====================================================================================EOF

?>