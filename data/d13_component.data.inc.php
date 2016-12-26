<?php

//========================================================================================
//
// COMPONENT.DATA
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

$game['components']=array(

//----------------------------------------------------------------------------------------
// FACTION 0
//----------------------------------------------------------------------------------------

 0=>array(
 
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Cloth Armor
  0=>array('id'=>0, 'image'=>'0.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>3, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>3),
    1=>array('resource'=>1, 'value'=>3),
    2=>array('resource'=>2, 'value'=>2)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Plate Armor
  1=>array('id'=>1, 'image'=>'1.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>3, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>5),
    1=>array('resource'=>1, 'value'=>5),
    2=>array('resource'=>2, 'value'=>2)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Shield
  2=>array('id'=>2, 'image'=>'2.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>2, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>10),
    1=>array('resource'=>1, 'value'=>10),
    2=>array('resource'=>2, 'value'=>5)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Short Sword
  3=>array('id'=>3, 'image'=>'3.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>5),
    1=>array('resource'=>1, 'value'=>5),
    2=>array('resource'=>2, 'value'=>3)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Long Sword
  4=>array('id'=>4, 'image'=>'4.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>5),
    1=>array('resource'=>1, 'value'=>5),
    2=>array('resource'=>2, 'value'=>3)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Bow
  5=>array('id'=>5, 'image'=>'5.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>4),
    1=>array('resource'=>1, 'value'=>7),
    2=>array('resource'=>2, 'value'=>2)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Longbow
  6=>array('id'=>6, 'image'=>'6.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>6),
    1=>array('resource'=>1, 'value'=>7),
    2=>array('resource'=>2, 'value'=>4)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  ),
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Spear
  7=>array('id'=>7, 'image'=>'7.png', 'active'=>true, 'priority'=>-1, 'type'=>'component', 'duration'=>1, 'storageResource'=>4, 'storage'=>1, 'salvage'=>0.5, 'removeDuration'=>1,
   'cost'=>array(
    0=>array('resource'=>0, 'value'=>8),
    1=>array('resource'=>1, 'value'=>8),
    2=>array('resource'=>2, 'value'=>4)
   ),
   'requirements'=>array(),
   'upgrades'=>array()
  )
  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Add new components here
  
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