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

	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - METALLURGY
	0=>array('id'=>0, 'image'=>'0.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>500),
			1=>array('resource'=>1, 'value'=>500),
			2=>array('resource'=>2, 'value'=>700)
		),
		'requirements'=>array(),
		'upgrades'=>array()
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MILITARY TRAINING
	1=>array('id'=>1, 'image'=>'1.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
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
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ADVANCED MILITARY TRAINING
	2=>array('id'=>2, 'image'=>'2.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>1,
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
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - HORSE BREEDING
	3=>array('id'=>3, 'image'=>'3.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>1,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>700),
			1=>array('resource'=>1, 'value'=>1000),
			2=>array('resource'=>2, 'value'=>500)
		),
		'requirements'=>array(),
		'upgrades'=>array()
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CURRENCY
	4=>array('id'=>4, 'image'=>'4.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>1,
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
		
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Padding for future technologies
	5=>array('id'=>5, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	6=>array('id'=>6, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	7=>array('id'=>7, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	8=>array('id'=>8, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	9=>array('id'=>9, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	10=>array('id'=>10, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	11=>array('id'=>11, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	12=>array('id'=>12, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	13=>array('id'=>13, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	14=>array('id'=>14, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	15=>array('id'=>15, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	16=>array('id'=>16, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	17=>array('id'=>17, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	18=>array('id'=>18, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	19=>array('id'=>19, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	20=>array('id'=>20, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	21=>array('id'=>21, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	22=>array('id'=>22, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	23=>array('id'=>23, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	24=>array('id'=>24, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	25=>array('id'=>25, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	26=>array('id'=>26, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	27=>array('id'=>27, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	28=>array('id'=>28, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	29=>array('id'=>29, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	30=>array('id'=>30, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	31=>array('id'=>31, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	32=>array('id'=>32, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	33=>array('id'=>33, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	34=>array('id'=>34, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	35=>array('id'=>35, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	36=>array('id'=>36, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	37=>array('id'=>37, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	38=>array('id'=>38, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	39=>array('id'=>39, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	40=>array('id'=>40, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	41=>array('id'=>41, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	42=>array('id'=>42, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	43=>array('id'=>43, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	44=>array('id'=>44, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	45=>array('id'=>45, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	46=>array('id'=>46, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	47=>array('id'=>47, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	48=>array('id'=>48, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	49=>array('id'=>49, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	50=>array('id'=>50, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	51=>array('id'=>51, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	52=>array('id'=>52, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	53=>array('id'=>53, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	54=>array('id'=>54, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	55=>array('id'=>55, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	56=>array('id'=>56, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	57=>array('id'=>57, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	58=>array('id'=>58, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	59=>array('id'=>59, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	60=>array('id'=>60, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	61=>array('id'=>61, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	62=>array('id'=>62, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	63=>array('id'=>63, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	64=>array('id'=>64, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	65=>array('id'=>65, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	66=>array('id'=>66, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	67=>array('id'=>67, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	68=>array('id'=>68, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	69=>array('id'=>69, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	70=>array('id'=>70, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	71=>array('id'=>71, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	72=>array('id'=>72, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	73=>array('id'=>73, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	74=>array('id'=>74, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	75=>array('id'=>75, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	76=>array('id'=>76, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	77=>array('id'=>77, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	78=>array('id'=>78, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	79=>array('id'=>79, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	80=>array('id'=>80, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	81=>array('id'=>81, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	82=>array('id'=>82, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	83=>array('id'=>83, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	84=>array('id'=>84, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	85=>array('id'=>85, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	86=>array('id'=>86, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	87=>array('id'=>87, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	88=>array('id'=>88, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	89=>array('id'=>89, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	90=>array('id'=>90, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	91=>array('id'=>91, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	92=>array('id'=>92, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	93=>array('id'=>93, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	94=>array('id'=>94, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	95=>array('id'=>95, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	96=>array('id'=>96, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	97=>array('id'=>97, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	98=>array('id'=>98, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	99=>array('id'=>99, 'image'=>'0.png', 'active'=>false, 'priority'=>-1, 'duration'=>0, 'maxLevel'=>0, 'cost'=>array(), 'requirements'=>array(), 'upgrades'=>array()),
	
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - SPEARMAN TRAINING
	100=>array('id'=>100, 'image'=>'100.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(0)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - PIKEMAN TRAINING
	101=>array('id'=>101, 'image'=>'101.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(1)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - FENCER TRAINING
	102=>array('id'=>102, 'image'=>'102.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(2)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - SWORDSMAN TRAINING
	103=>array('id'=>103, 'image'=>'103.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(3)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - KINGHT TRAINING
	104=>array('id'=>104, 'image'=>'104.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(4)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - BOWMAN TRAINING
	105=>array('id'=>105, 'image'=>'105.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(5)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - LONGBOWMAN TRAINING
	106=>array('id'=>106, 'image'=>'106.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(6)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MOUNTED SPEARMAN TRAINING
	107=>array('id'=>107, 'image'=>'107.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(7)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MOUNTED SWORDSMAN TRAINING
	108=>array('id'=>108, 'image'=>'108.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(8)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MOUNTED KNIGHT TRAINING
	109=>array('id'=>109, 'image'=>'109.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(9)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - SPY TRAINING
	110=>array('id'=>110, 'image'=>'110.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(10)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MILITIA TRAINING
	111=>array('id'=>111, 'image'=>'111.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(11)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - THUG TRAINING
	112=>array('id'=>112, 'image'=>'112.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(12)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - BANDIT TRAINING
	113=>array('id'=>113, 'image'=>'113.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(13)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - MILITIA BOWMAN TRAINING
	114=>array('id'=>114, 'image'=>'114.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(14)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - RANGER TRAINING
	115=>array('id'=>115, 'image'=>'115.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(15)
		),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - THIEF TRAINING
	116=>array('id'=>116, 'image'=>'116.png', 'active'=>true, 'priority'=>-1, 'duration'=>15, 'maxLevel'=>3,
		'cost'=>array(
			0=>array('resource'=>0, 'value'=>100),
			1=>array('resource'=>1, 'value'=>100),
			2=>array('resource'=>2, 'value'=>100)
		),
		'requirements'=>array(),
		'upgrades'=>array(16)
	),
	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Add new technologies here
	
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