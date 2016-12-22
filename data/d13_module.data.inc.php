<?php

//========================================================================================
//
// MODULE.DATA
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
	Required Stats for all Module types:
	
			active
			priority
			type
			inputResource
			ratio
			maxInput
			duration
			salvage
			removeDuration
			maxInstances
			
			cost
			requirements
			options
			upgrades
			
	Harvest Modules only:
	
			outputResource

	Craft Modules only:
			
			components
			
	Train Modules only:
	
			units
			
	Research Modules only:
	
			technologies
			
	Storage Modules only:
			
			storedResource
			
	Defensive Modules only:
	
			unitId
			
	Alliance and Command Modules only:
	
			options

*/

$game['modules']=array(

//----------------------------------------------------------------------------------------
// FACTION 0
//----------------------------------------------------------------------------------------

 0=>array(
 
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Gold Mine
  0=>array('active'=>true, 'priority'=>-1, 'type'=>'harvest', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>7, 'maxInput'=>30, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'outputResource'=>array(0),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>100),
    1=>array('resource'=>1, 'value'=>300),
    2=>array('resource'=>2, 'value'=>200)
   ),
	'requirements'=>array(),
	'options'=>array(
		'inventoryList'=>true
	),
	'upgrades'=>array(100)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Lumber Mill
  1=>array('active'=>true, 'priority'=>-1, 'type'=>'harvest', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>10, 'maxInput'=>30, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'outputResource'=>array(1),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>100),
    1=>array('resource'=>1, 'value'=>300),
    2=>array('resource'=>2, 'value'=>200)
   ),
	'requirements'=>array(),
	'options'=>array(
		'inventoryList'=>true
	),
	'upgrades'=>array(101)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Iron Mine
  2=>array('active'=>true, 'priority'=>-1, 'type'=>'harvest', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>6, 'maxInput'=>30, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'outputResource'=>array(2),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>300),
    2=>array('resource'=>2, 'value'=>100)
   ),
	'requirements'=>array(),
	'options'=>array(
		'inventoryList'=>true
	),
	'upgrades'=>array(102)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Armor Smith
  3=>array('active'=>true, 'priority'=>-1, 'type'=>'craft', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>700),
    2=>array('resource'=>2, 'value'=>500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1),
    3=>array('active'=>true, 'type'=>'technologies', 'id'=>0, 'level'=>1)
   ),
	'options'=>array(
		'inventoryList'=>true
	),
	'upgrades'=>array(),
	'components'=>array(0, 1, 2)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Weapon Shop
  4=>array('active'=>true, 'priority'=>-1, 'type'=>'craft', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>700),
    2=>array('resource'=>2, 'value'=>500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1),
    3=>array('active'=>true, 'type'=>'technologies', 'id'=>0, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array(),
	'components'=>array(3, 4, 5, 6, 7)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Barracks
  5=>array('active'=>true, 'priority'=>-1, 'type'=>'train', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>400),
    1=>array('resource'=>1, 'value'=>900),
    2=>array('resource'=>2, 'value'=>600)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>3, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>4, 'level'=>1),
    2=>array('active'=>true, 'type'=>'technologies', 'id'=>1, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array(),
   'units'=>array(0, 1, 2, 3, 4, 5, 6, 10, 17, 18)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Stables
  6=>array('active'=>true, 'priority'=>-1, 'type'=>'train', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>700),
    2=>array('resource'=>2, 'value'=>500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>5, 'level'=>1),
    1=>array('active'=>true, 'type'=>'technologies', 'id'=>3, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array(),
   'units'=>array(7, 8, 9)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Mercenary Tent
  7=>array('active'=>true, 'priority'=>-1, 'type'=>'train', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>1000),
    1=>array('resource'=>1, 'value'=>2000),
    2=>array('resource'=>2, 'value'=>1500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array(),
   'units'=>array(11, 12, 13, 14, 15, 16)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Laboratory
  8=>array('active'=>true, 'priority'=>-1, 'type'=>'research', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>500),
    1=>array('resource'=>1, 'value'=>800),
    2=>array('resource'=>2, 'value'=>700)
   ),
   'requirements'=>array(),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array(),
   'technologies'=>array(0, 1, 2, 3, 4)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Marketplace
  9=>array('active'=>true, 'priority'=>-1, 'type'=>'trade', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.1, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>600),
    1=>array('resource'=>1, 'value'=>600),
    2=>array('resource'=>2, 'value'=>300)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
   ),
   'options'=>array(),
	'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Gold Storage
  10=>array('active'=>true, 'priority'=>-1, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'storedResource'=>array(0),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>700),
    2=>array('resource'=>2, 'value'=>500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1),
    3=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Lumber Storage
  11=>array('active'=>true, 'priority'=>-1, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'storedResource'=>array(1),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>700),
    2=>array('resource'=>2, 'value'=>500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1),
    3=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Iron Storage
  12=>array('active'=>true, 'priority'=>-1, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'storedResource'=>array(2),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>300),
    1=>array('resource'=>1, 'value'=>700),
    2=>array('resource'=>2, 'value'=>500)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1),
    3=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
   ),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Guild House
  13=>array('active'=>true, 'priority'=>-1, 'type'=>'alliance', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
	'storedResource'=>array(0,1,2),
	'cost'=>array(
		0=>array('resource'=>0, 'value'=>300),
		1=>array('resource'=>1, 'value'=>700),
		2=>array('resource'=>2, 'value'=>500)
	),
	'requirements'=>array(
		0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    	1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    	2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1),
    	3=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
	),
	'options'=>array(
		'allianceGet'=>true,
		'allianceEdit'=>true,
		'allianceRemove'=>true,
		'allianceInvite'=>true,
		'allianceWar'=>true
	),
	'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Townhall
  14=>array('active'=>true, 'priority'=>-1, 'type'=>'command', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.1, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
	'outputResource'=>array(0,1,2,3),
	'storedResource'=>array(0,1,2),
	'cost'=>array(
		0=>array('resource'=>0, 'value'=>300),
		1=>array('resource'=>1, 'value'=>700),
		2=>array('resource'=>2, 'value'=>500)
	),
	'requirements'=>array(),
	'upgrades'=>array(),
	'options'=>array(
		'inventoryList'=>true,
		'nodeMove'=>true,
		'nodeRemove'=>true,
		'nodeEdit'=>true
	)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Archer Tower
  15=>array('active'=>true, 'priority'=>-1, 'type'=>'defense', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0, 'maxInput'=>5, 'unitId'=>17, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3, 'unitId'=>17,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>200),
    1=>array('resource'=>1, 'value'=>600),
    2=>array('resource'=>2, 'value'=>400)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'modules', 'id'=>0, 'level'=>1),
    1=>array('active'=>true, 'type'=>'modules', 'id'=>1, 'level'=>1),
    2=>array('active'=>true, 'type'=>'modules', 'id'=>2, 'level'=>1)
   ),
   'options'=>array(),
	'upgrades'=>array(103)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Farm
  16=>array('active'=>true, 'priority'=>-1, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.25, 'maxInput'=>20, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'storedResource'=>array(5),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>150),
    1=>array('resource'=>1, 'value'=>350),
    2=>array('resource'=>2, 'value'=>250)
   ),
   'requirements'=>array(
   0=>array('active'=>true, 'type'=>'modules', 'id'=>14, 'level'=>1),
   ),
   'options'=>array(),
	'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - War Academy
  17=>array('active'=>true, 'priority'=>-1, 'type'=>'research', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>2,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>800),
    1=>array('resource'=>1, 'value'=>200),
    2=>array('resource'=>2, 'value'=>600)
   ),
   'requirements'=>array(),
   'options'=>array(
   	'inventoryList'=>true
   ),
	'upgrades'=>array(),
   'technologies'=>array(100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Wargate
  18=>array('active'=>true, 'priority'=>-1, 'type'=>'warfare', 'maxLevel'=>1, 'inputResource'=>0, 'ratio'=>0, 'maxInput'=>0, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
	'cost'=>array(
		0=>array('resource'=>0, 'value'=>300),
		1=>array('resource'=>1, 'value'=>700),
		2=>array('resource'=>2, 'value'=>500)
	),
	'requirements'=>array(
		0=>array('active'=>true, 'type'=>'modules', 'id'=>14, 'level'=>2)
	),
	'upgrades'=>array(),
	'options'=>array(
		'combatRaid'=>true
	
	)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Add new modules here
  
  
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