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
  0=>array('id'=>0, 'active'=>true, 'priority'=>-1, 'type'=>'harvest', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>7, 'maxInput'=>30, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
	'images'=>array(
		0=>array('level'=>0, 'image'=>'module_goldmine_0.png'),
	),
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
  1=>array('id'=>1, 'active'=>true, 'priority'=>1, 'type'=>'harvest', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>10, 'maxInput'=>30, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_lumbermill_0.png'),
	),
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
  2=>array('id'=>2, 'active'=>true, 'priority'=>2, 'type'=>'harvest', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>6, 'maxInput'=>30, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_ironmine_0.png'),
	),
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
  3=>array('id'=>3, 'active'=>true, 'priority'=>3, 'type'=>'craft', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_armorsmith_0.png'),
	),
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
	'upgrades'=>array(103),
	'components'=>array(0, 1, 2)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Weapon Shop
  4=>array('id'=>4, 'active'=>true, 'priority'=>4, 'type'=>'craft', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_weaponshop_0.png'),
	),
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
	'upgrades'=>array(104),
	'components'=>array(3, 4, 5, 6, 7)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Barracks
  5=>array('id'=>5, 'active'=>true, 'priority'=>5, 'type'=>'train', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_barracks_0.png'),
	),
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
	'upgrades'=>array(105),
   'units'=>array(0, 1, 2, 3, 4, 5, 6, 10, 17, 18)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Stables
  6=>array('id'=>6, 'active'=>true, 'priority'=>6, 'type'=>'train', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_stables_0.png'),
	),
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
	'upgrades'=>array(106),
   'units'=>array(7, 8, 9)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Mercenary Tent
  7=>array('id'=>7, 'active'=>true, 'priority'=>7, 'type'=>'train', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_mercenarytent_0.png'),
	),
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
	'upgrades'=>array(107),
   'units'=>array(11, 12, 13, 14, 15, 16)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Laboratory
  8=>array('id'=>8, 'active'=>true, 'priority'=>8, 'type'=>'research', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_laboratory_0.png'),
	),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>500),
    1=>array('resource'=>1, 'value'=>800),
    2=>array('resource'=>2, 'value'=>700)
   ),
   'requirements'=>array(),
   'options'=>array(
		'inventoryList'=>true
   ),
	'upgrades'=>array(108),
   'technologies'=>array(0, 1, 2, 3, 4)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Marketplace
  9=>array('id'=>9, 'active'=>true, 'priority'=>9, 'type'=>'trade', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.1, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_marketplace_0.png'),
	),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>600),
    1=>array('resource'=>1, 'value'=>600),
    2=>array('resource'=>2, 'value'=>300)
   ),
   'requirements'=>array(
    0=>array('active'=>true, 'type'=>'technologies', 'id'=>4, 'level'=>1)
   ),
   'options'=>array(),
	'upgrades'=>array(109)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Gold Storage
  10=>array('id'=>10, 'active'=>true, 'priority'=>10, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_goldstorage_0.png'),
	),
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
	'upgrades'=>array(110)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Lumber Storage
  11=>array('id'=>11, 'active'=>true, 'priority'=>11, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_lumberstorage_0.png'),
	),
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
	'upgrades'=>array(111)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Iron Storage
  12=>array('id'=>12, 'active'=>true, 'priority'=>12, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_ironstorage_0.png'),
	),
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
	'upgrades'=>array(112)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Guild House
  13=>array('id'=>13, 'active'=>true, 'priority'=>13, 'type'=>'alliance', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>100, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
	'images'=>array(
		0=>array('level'=>0, 'image'=>'module_guildhouse_0.png'),
	),
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
	'upgrades'=>array(113)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Townhall
  14=>array('id'=>14, 'active'=>true, 'priority'=>14, 'type'=>'command', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.1, 'maxInput'=>10, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
	'images'=>array(
		0=>array('level'=>0, 'image'=>'module_townhall_0.png'),
	),
	'outputResource'=>array(0,1,2,3),
	'storedResource'=>array(0,1,2),
	'cost'=>array(
		0=>array('resource'=>0, 'value'=>300),
		1=>array('resource'=>1, 'value'=>700),
		2=>array('resource'=>2, 'value'=>500)
	),
	'requirements'=>array(),
	'upgrades'=>array(114),
	'options'=>array(
		'inventoryList'=>true,
		'nodeMove'=>true,
		'nodeRemove'=>true,
		'nodeEdit'=>true
	)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Archer Tower
  15=>array('id'=>15, 'active'=>true, 'priority'=>15, 'type'=>'defense', 'maxLevel'=>3, 'inputResource'=>3, 'ratio'=>0, 'maxInput'=>5, 'unitId'=>17, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3, 'unitId'=>17,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_archertower_0.png'),
		1=>array('level'=>2, 'image'=>'module_archertower_1.png'),
		2=>array('level'=>3, 'image'=>'module_archertower_2.png')
	),
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
	'upgrades'=>array(115)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Farm
  16=>array('id'=>16, 'active'=>true, 'priority'=>16, 'type'=>'storage', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.25, 'maxInput'=>20, 'duration'=>1, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>3,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_farm_0.png'),
	),
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
	'upgrades'=>array(116)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - War Academy
  17=>array('id'=>17, 'active'=>true, 'priority'=>17, 'type'=>'research', 'maxLevel'=>1, 'inputResource'=>3, 'ratio'=>0.05, 'maxInput'=>10, 'duration'=>10, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>2,
   'images'=>array(
		0=>array('level'=>0, 'image'=>'module_waracademy_0.png'),
	),
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>800),
    1=>array('resource'=>1, 'value'=>200),
    2=>array('resource'=>2, 'value'=>600)
   ),
   'requirements'=>array(),
   'options'=>array(
   	'inventoryList'=>true
   ),
	'upgrades'=>array(117),
   'technologies'=>array(100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116)
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Wargate
  18=>array('id'=>18, 'active'=>true, 'priority'=>18, 'type'=>'warfare', 'maxLevel'=>1, 'inputResource'=>0, 'ratio'=>0, 'maxInput'=>0, 'duration'=>5, 'salvage'=>0.5, 'removeDuration'=>1, 'maxInstances'=>1,
	'images'=>array(
		0=>array('level'=>0, 'image'=>'module_wargate_0.png'),
	),
	'cost'=>array(
		0=>array('resource'=>0, 'value'=>300),
		1=>array('resource'=>1, 'value'=>700),
		2=>array('resource'=>2, 'value'=>500)
	),
	'requirements'=>array(
		0=>array('active'=>true, 'type'=>'modules', 'id'=>14, 'level'=>2)
	),
	'upgrades'=>array(118),
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