<?php

//========================================================================================
//
// TECHNOLOGY.DATA
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
	Required Stats for all Technology types:

		active
		priority
		duration
		maxLevel
		
		cost
		requirements
		upgrades
		
*/

$game['technologies']=array(

//----------------------------------------------------------------------------------------
// FACTION 0
//----------------------------------------------------------------------------------------

0=>array(

	//METALLURGY
	0=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>500),
			1=>array('resource'=>1, 'value'=>500),
			2=>array('resource'=>2, 'value'=>700)
		),
		'requirements'=>array(),
		'upgrades'=>array()
		),
	//MILITARY TRAINING
	1=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>500),
			1=>array('resource'=>1, 'value'=>700),
			2=>array('resource'=>2, 'value'=>500)
		),
		'requirements'=>array(
			0=>array('type'=>'technologies', 'id'=>0, 'level'=>1)
		),
		'upgrades'=>array()
		),
	//ADVANCED MILITARY TRAINING
	2=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>1,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>700),
			1=>array('resource'=>1, 'value'=>1000),
			2=>array('resource'=>2, 'value'=>800)
		),
		'requirements'=>array(
			0=>array('type'=>'technologies', 'id'=>1, 'level'=>1)
		),
		'upgrades'=>array()
		),
	//HORSE BREEDING
	3=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>1,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>700),
			1=>array('resource'=>1, 'value'=>1000),
			2=>array('resource'=>2, 'value'=>500)
		),
		'requirements'=>array(),
		'upgrades'=>array()
		),
	//CURRENCY
	4=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>1,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>200),
			1=>array('resource'=>1, 'value'=>400),
			2=>array('resource'=>2, 'value'=>300)
		),
		'requirements'=>array(
			0=>array('type'=>'technologies', 'id'=>0, 'level'=>1)
		),
		'upgrades'=>array()
		),

	5=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	6=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	7=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	8=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	9=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	10=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	11=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	12=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	13=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	14=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	15=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	16=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	17=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	18=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	19=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	20=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	21=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	22=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	23=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	24=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	25=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	26=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	27=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	28=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	29=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	30=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	31=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	32=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	33=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	34=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	35=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	36=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	37=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	38=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	39=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	40=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	41=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	42=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	43=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	44=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	45=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	46=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	47=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	48=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	49=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	50=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	51=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	52=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	53=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	54=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	55=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	56=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	57=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	58=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	59=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	60=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	61=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	62=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	63=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	64=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	65=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	66=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	67=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	68=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	69=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	70=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	71=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	72=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	73=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	74=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	75=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	76=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	77=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	78=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	79=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	80=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	81=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	82=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	83=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	84=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	85=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	86=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	87=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	88=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	89=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	90=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	91=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	92=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	93=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	94=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	95=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	96=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	97=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	98=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	99=>array('active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	
	//SPEARMAN TRAINING
	100=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(0)
		),
	//PIKEMAN TRAINING
	101=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(1)
		),
	//FENCER TRAINING
	102=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(2)
		),
	//SWORDSMAN TRAINING
	103=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(3)
		),
	//KINGHT TRAINING
	104=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(4)
		),
	//BOWMAN TRAINING
	105=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(5)
		),
	//LONGBOWMAN TRAINING
	106=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(6)
		),
	//MOUNTED SPEARMAN TRAINING
	107=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(7)
		),
	//MOUNTED SWORDSMAN TRAINING
	108=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(8)
		),
	//MOUNTED KNIGHT TRAINING
	109=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(9)
		),
	//SPY TRAINING
	110=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(10)
		),
	//MILITIA TRAINING
	111=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(11)
		),
	//THUG TRAINING
	112=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(12)
		),
	//BANDIT TRAINING
	113=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(13)
		),
	//MILITIA BOWMAN TRAINING
	114=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(14)
		),
	//RANGER TRAINING
	115=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(15)
		),
	//THIEF TRAINING
	116=>array('active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(16)
		),
		
	),

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