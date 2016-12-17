<?php

//========================================================================================
//
// UNIT.DATA
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
	Required Stats for all Unit types:

		active
		priority
		type
		class
		hp
		damage
		armor
		speed
		duration
		upkeepResource
		upkeep
		salvage
		removeDuration
		
		cost
		requirements
		upgrades
		
	Optional stats for HERO type units:
	
		skills
		
*/



$game['units']=array(

//----------------------------------------------------------------------------------------
// FACTION 0
//----------------------------------------------------------------------------------------

0=>array(
  0=>array('active'=>true, 'priority'=>0, 'type'=>'unit', 'class'=>'spearman', 'hp'=>300, 'damage'=>100, 'armor'=>50, 'speed'=>100, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>20),
    1=>array('resource'=>1, 'value'=>40),
    2=>array('resource'=>2, 'value'=>20)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'components', 'id'=>0, 'value'=>1),
    1=>array('active'=>true, 'type'=>'components', 'id'=>7, 'value'=>1),
    2=>array('active'=>true, 'type'=>'components', 'id'=>2, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  1=>array('active'=>true, 'priority'=>1, 'type'=>'unit', 'class'=>'spearman', 'hp'=>300, 'damage'=>110, 'armor'=>70, 'speed'=>80, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>30),
    1=>array('resource'=>1, 'value'=>30),
    2=>array('resource'=>2, 'value'=>40)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'components', 'id'=>1, 'value'=>1),
    1=>array('active'=>true, 'type'=>'components', 'id'=>7, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  2=>array('active'=>true, 'priority'=>2, 'type'=>'unit', 'class'=>'duelist', 'hp'=>250, 'damage'=>150, 'armor'=>30, 'speed'=>150, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>20),
    1=>array('resource'=>1, 'value'=>20),
    2=>array('resource'=>2, 'value'=>30)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'components', 'id'=>0, 'value'=>1),
    1=>array('active'=>true, 'type'=>'components', 'id'=>3, 'value'=>2)
   ),
   'upgrades'=>array()
  ),
  3=>array('active'=>true, 'priority'=>3, 'type'=>'unit', 'class'=>'swordsman', 'hp'=>300, 'damage'=>120, 'armor'=>100, 'speed'=>70, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>30),
    1=>array('resource'=>1, 'value'=>40),
    2=>array('resource'=>2, 'value'=>40)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'components', 'id'=>1, 'value'=>1),
    1=>array('active'=>true, 'type'=>'components', 'id'=>3, 'value'=>1),
    2=>array('active'=>true, 'type'=>'components', 'id'=>2, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  4=>array('active'=>true, 'priority'=>4, 'type'=>'unit', 'class'=>'swordsman', 'hp'=>350, 'damage'=>130, 'armor'=>120, 'speed'=>50, 'duration'=>2, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>50),
    1=>array('resource'=>1, 'value'=>70),
    2=>array('resource'=>2, 'value'=>100)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'technologies', 'id'=>2, 'level'=>1),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>1, 'value'=>1),
    2=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>4, 'value'=>1),
    3=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>2, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  5=>array('active'=>true, 'priority'=>5, 'type'=>'unit', 'class'=>'archer', 'hp'=>250, 'damage'=>300, 'armor'=>30, 'speed'=>150, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>30),
    1=>array('resource'=>1, 'value'=>50),
    2=>array('resource'=>2, 'value'=>40)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>0, 'value'=>1),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>5, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  6=>array('active'=>true, 'priority'=>6, 'type'=>'unit', 'class'=>'archer', 'hp'=>250, 'damage'=>400, 'armor'=>70, 'speed'=>100, 'duration'=>2, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>30),
    1=>array('resource'=>1, 'value'=>40),
    2=>array('resource'=>2, 'value'=>50)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'technologies', 'id'=>2, 'level'=>1),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>0, 'value'=>1),
    2=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>6, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  7=>array('active'=>true, 'priority'=>7, 'type'=>'unit', 'class'=>'cavalry', 'hp'=>600, 'damage'=>140, 'armor'=>100, 'speed'=>250, 'duration'=>2, 'upkeepResource'=>5, 'upkeep'=>2, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>100),
    1=>array('resource'=>1, 'value'=>120),
    2=>array('resource'=>2, 'value'=>150)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>0, 'value'=>1),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>1, 'value'=>1),
    2=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>7, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  8=>array('active'=>true, 'priority'=>8, 'type'=>'unit', 'class'=>'cavalry', 'hp'=>600, 'damage'=>130, 'armor'=>120, 'speed'=>250, 'duration'=>2, 'upkeepResource'=>5, 'upkeep'=>2, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>110),
    1=>array('resource'=>1, 'value'=>140),
    2=>array('resource'=>2, 'value'=>170)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>1, 'value'=>2),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>4, 'value'=>1),
    2=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>2, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  9=>array('active'=>true, 'priority'=>9, 'type'=>'unit', 'class'=>'cavalry', 'hp'=>700, 'damage'=>140, 'armor'=>150, 'speed'=>220, 'duration'=>3, 'upkeepResource'=>5, 'upkeep'=>2, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>130),
    1=>array('resource'=>1, 'value'=>170),
    2=>array('resource'=>2, 'value'=>200)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'technologies', 'id'=>2, 'level'=>1),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>1, 'value'=>2),
    2=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>7, 'value'=>1),
    3=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>2, 'value'=>1)
   ),
   'upgrades'=>array()
  ),
  10=>array('active'=>true, 'priority'=>10, 'type'=>'hero', 'class'=>'duelist', 'hp'=>250, 'damage'=>100, 'armor'=>30, 'speed'=>170, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>50),
    1=>array('resource'=>1, 'value'=>20),
    2=>array('resource'=>2, 'value'=>30)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'priority'=>-1, 'type'=>'technologies', 'id'=>2, 'level'=>1),
    1=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>0, 'value'=>1),
    2=>array('active'=>true, 'priority'=>-1, 'type'=>'components', 'id'=>3, 'value'=>2)
   ),
   'upgrades'=>array()
  ),
  11=>array('active'=>true, 'priority'=>11, 'type'=>'unit', 'class'=>'spearman', 'hp'=>200, 'damage'=>50, 'armor'=>10, 'speed'=>100, 'duration'=>0.5, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>70),
    1=>array('resource'=>1, 'value'=>0),
    2=>array('resource'=>2, 'value'=>0)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  12=>array('active'=>true, 'priority'=>12, 'type'=>'hero', 'class'=>'duelist', 'hp'=>250, 'damage'=>80, 'armor'=>20, 'speed'=>110, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>100),
    1=>array('resource'=>1, 'value'=>0),
    2=>array('resource'=>2, 'value'=>0)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  13=>array('active'=>true, 'priority'=>13, 'type'=>'hero', 'class'=>'duelist', 'hp'=>350, 'damage'=>120, 'armor'=>50, 'speed'=>100, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>120),
    1=>array('resource'=>1, 'value'=>0),
    2=>array('resource'=>2, 'value'=>0)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  14=>array('active'=>true, 'priority'=>14, 'type'=>'unit', 'class'=>'archer', 'hp'=>250, 'damage'=>150, 'armor'=>20, 'speed'=>140, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>100),
    1=>array('resource'=>1, 'value'=>0),
    2=>array('resource'=>2, 'value'=>0)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  15=>array('active'=>true, 'priority'=>15, 'type'=>'unit', 'class'=>'archer', 'hp'=>300, 'damage'=>200, 'armor'=>50, 'speed'=>150, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>130),
    1=>array('resource'=>1, 'value'=>0),
    2=>array('resource'=>2, 'value'=>0)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  16=>array('active'=>true, 'priority'=>16, 'type'=>'unit', 'class'=>'duelist', 'hp'=>250, 'damage'=>70, 'armor'=>20, 'speed'=>170, 'duration'=>1, 'upkeepResource'=>5, 'upkeep'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>110),
    1=>array('resource'=>1, 'value'=>0),
    2=>array('resource'=>2, 'value'=>0)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  17=>array('active'=>true, 'priority'=>-1, 'type'=>'unit', 'class'=>'static', 'hp'=>1500, 'damage'=>500, 'armor'=>100, 'speed'=>00, 'duration'=>10, 'upkeepResource'=>5, 'upkeep'=>5, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>500),
    1=>array('resource'=>1, 'value'=>1000),
    2=>array('resource'=>2, 'value'=>1000)
   ),
   'requirements'=>array(0=>array('active'=>true, 'priority'=>-1, 'type'=>'technologies', 'id'=>2, 'level'=>1)),
   'upgrades'=>array()
  ),
  18=>array('active'=>true, 'priority'=>-1, 'type'=>'unit', 'class'=>'static', 'hp'=>3000, 'damage'=>100, 'armor'=>500, 'speed'=>00, 'duration'=>10, 'upkeepResource'=>5, 'upkeep'=>5, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>500),
    1=>array('resource'=>1, 'value'=>1000),
    2=>array('resource'=>2, 'value'=>1000)
   ),
   'requirements'=>array(0=>array('active'=>true, 'priority'=>-1, 'type'=>'technologies', 'id'=>2, 'level'=>1)),
   'upgrades'=>array()
  )
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