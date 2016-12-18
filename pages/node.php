<?php

//========================================================================================
//
// NODE
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
// PROCESS MODEL
//----------------------------------------------------------------------------------------

global $d13, $gl, $ui, $game;

$node 		= NULL;
$message 	= NULL;
$x 			= NULL;
$y 			= NULL;
$html 		= NULL;

$d13->db->query('start transaction');

if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action'])) {

	switch ($_GET['action']) {
 
 	//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET NODE DATA
	case 'get':
		if (isset($_GET['nodeId'])) {
			$node = new node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status=='done') {
				if ($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id']) {
					$node->checkAll(time());
					$node->getLocation();
					$node->getQueue('build');
					$node->getQueue('combat');
					$nodecount = $game['users']['maxModules'] * $game['users']['maxSectors'];
					$buildQueue=array();
					for ($i=0; $i < $nodecount; $i++) {
						$buildQueue[$i]=0;
					}
					foreach ($node->queue['build'] as $item) {
						$buildQueue[$item['slot']]=1;
					}
				} else {
					$message=misc::getlang("accessDenied");
				}
			} else {
				$message=misc::getlang($status);
			}
		} else {
			$message=misc::getlang("insufficientData");
		}
		break;
  
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET NODE DATA
  case 'set':
   if (isset($_GET['nodeId']))
   {
    $node=new node();
    $status=$node->get('id', $_GET['nodeId']);
    if ($status=='done')
    {
     if ((isset($_POST['name'], $_POST['focus']))&&($_POST['name']))
      if (in_array($_POST['focus'], array('hp', 'armor', 'damage')))
       if ($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
       {
        $oldName=$node->data['name'];
        $oldFocus=$node->data['focus'];
        $node->data['name']=$_POST['name'];
        $node->data['focus']=$_POST['focus'];
        $status=$node->set();
        if ($status!='done')
        {
         $node->data['name']=$oldName;
         $node->data['focus']=$oldFocus;
        }
        $message=misc::getlang($status);
       }
       else $message=misc::getlang("accessDenied");
      else $message=misc::getlang("invalidFocus");
    }
    else $message=misc::getlang($status);
   }
   else $message=misc::getlang("accessDenied");
  break;
  
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NEW NODE
  case 'add':
	if (isset($_POST['faction'], $_POST['name'], $_POST['x'], $_POST['y'])) {
		if ($_POST['faction'] != '' && !empty($_POST['name']) && !empty($_POST['x']) && !empty($_POST['y']) ) {
			$node=new node();
			$node->data['faction']=$_POST['faction'];
			$node->data['user']=$_SESSION[CONST_PREFIX.'User']['id'];
			$node->data['name']=$_POST['name'];
			$node->location['x']=$_POST['x'];
			$node->location['y']=$_POST['y'];
			$message=misc::getlang($node->add($_SESSION[CONST_PREFIX.'User']['id']));
		} else {
			$message=misc::getlang("insufficientData");
		}
	} else {
		$message=misc::getlang("insufficientData");
	}
	break;

  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NEW RANDOM NODE
  case 'random':
	if (isset($_POST['faction'])) {
		if ($_POST['faction'] != '') {
			$coord = array();
			$grid = new grid();
			$coord = $grid->getFree();
			$node = new node();
			$node->data['faction']=$_POST['faction'];
			$node->data['user']=$_SESSION[CONST_PREFIX.'User']['id'];
			$node->data['name']=$_SESSION[CONST_PREFIX.'User']['name'];
			$node->location['x']=$coord['x'];
			$node->location['y']=$coord['y'];
			$message=misc::getlang($node->add($_SESSION[CONST_PREFIX.'User']['id']));
			header('Location: ?p=node&action=list');
		} else {
			$message=misc::getlang("insufficientData");
		}
	}
	break;

  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE NODE
  case 'remove':
   if (isset($_GET['nodeId']))
   {
    $node=new node();
    $status=$node->get('id', $_GET['nodeId']);
    if ($status=='done')
    {
     if ((isset($_GET['go']))&&($_GET['go']))
      if ($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
      {
       $status=node::remove($_GET['nodeId']);
       if ($status=='done') header('location: node.php?action=list');
       else $message=misc::getlang($status);
      }
      else $message=misc::getlang("accessDenied");
    }
    else $message=misc::getlang($status);
   }
   else $message=misc::getlang("insufficientData");
  break;
  
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = MOVE NODE
  case 'move':
   if (isset($_GET['nodeId']))
   {
    $node=new node();
    $status=$node->get('id', $_GET['nodeId']);
    if ($status=='done')
    {
     if (isset($_POST['x'], $_POST['y']))
      if ($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])
       if ($game['factions'][$node->data['faction']]['costs']['move'][0]['resource']>-1)
        $message=misc::getlang($node->move($_POST['x'], $_POST['y']));
       else $message=misc::getlang("nodeMoveDisabled");
      else $message=misc::getlang("accessDenied");
    }
    else $message=misc::getlang($status);
   }
   else $message=misc::getlang("insufficientData");
  break;
  
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = LIST NODE
  case 'list':
   $nodes=node::getList($_SESSION[CONST_PREFIX.'User']['id']);
  break;
  
 }

} else {
	$message=misc::getlang("accessDenied");
}
if ((isset($status))&&($status=='error')) {
	$d13->db->query('rollback');
} else {
	$d13->db->query('commit');
}

//----------------------------------------------------------------------------------------
// PROCESS VIEW
//----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$page = "node";

if (isset($node)) {
	$tvars['tvar_nodeFaction'] 	= $node->data['faction'];
	$tvars['tvar_nodeID'] 		= $node->data['id'];
	$tvars['tvar_nodeName'] 	= $node->data['name'];
	if (isset($node->data['x'])) { $tvars['tvar_nodeX'] 		= $node->data['x']; }
	if (isset($node->data['y'])) { $tvars['tvar_nodeY'] 		= $node->data['y']; }
}

if (isset($_SESSION[CONST_PREFIX.'User']['id'], $_GET['action'])) {

	switch ($_GET['action']) {
 
	//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET NODE
	case 'get':
  		
		if ((isset($node->data['id']))&&($node->data['user']==$_SESSION[CONST_PREFIX.'User']['id'])) {
		
		$tvars['tvar_getHTMLSectors'] = "";
		for ($sector=1; $sector <= $game['users']['maxSectors']; $sector++) {
			//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Render Sector View
			$offset_start = ($sector-1)*$game['users']['maxModules'];
			$offset_end = $offset_start + $game['users']['maxModules'];
			
			$i=0;
			$s=5;
			$tvars['tvar_getHTMLNode'] = "";
			foreach ($node->modules as $module) {
			
				if ($module['slot'] >= $offset_start && $module['slot'] <= $offset_end) {
				
					if ($i==$s) {
						$tvars['tvar_getHTMLNode'] .= '</div>';
						$i=0;
					} 
					if ($i==0) {
						$tvars['tvar_getHTMLNode'] .= '<div class="row no-gutter">';
					}
					 if ($buildQueue[$module['slot']]) {
						
						$tvars['tvar_moduleAction']	= "cancel";
						$tvars['tvar_moduleSlot'] 	= $module['slot'];
						$tvars['tvar_moduleImage'] 	= "pending.png";
						$tvars['tvar_moduleClass']	= '<img class="spinner resource" src="'.CONST_DIRECTORY.'templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/icon/gear.png">';
						$tvars['tvar_moduleLabel'] 	= misc::getlang("underConstruction");
						$tvars['tvar_getHTMLNode'] .= $d13->tpl->render_subpage("sub.node.module", $tvars);
					 } else if ($module['module']>-1) {
						
						$tvars['tvar_moduleAction']	= "get";
						$tvars['tvar_moduleSlot'] 	= $module['slot'];
						$tvars['tvar_moduleImage'] 	= $module['module'].".png";
						if (($module['input'] <= 0 && $game['modules'][$node->data['faction']][$module['module']]['maxInput'] > 0) || ($game['modules'][$node->data['faction']][$module['module']]['type'] == 'defense' && $module['input'] < $game['modules'][$node->data['faction']][$module['module']]['maxInput'])) {
							$tvars['tvar_moduleClass']	= '<img class="animated bounce resource" src="'.CONST_DIRECTORY.'templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/icon/exclamation.png">';
						} else {
							$tvars['tvar_moduleClass']	= "";
						}
						$tvars['tvar_moduleLabel'] 	= $gl["modules"][$node->data['faction']][$module['module']]["name"];
						$tvars['tvar_getHTMLNode'] .= $d13->tpl->render_subpage("sub.node.module", $tvars);
					 } else {
						
						$tvars['tvar_moduleAction']	= "list";
						$tvars['tvar_moduleSlot'] 	= $module['slot'];
						$tvars['tvar_moduleImage'] 	= "empty.png";
						$tvars['tvar_moduleClass']	= "";
						$tvars['tvar_moduleLabel'] 	= misc::getlang("emptySlot");
						$tvars['tvar_getHTMLNode'] .= $d13->tpl->render_subpage("sub.node.module", $tvars);
					 }
					 $i++;
				 	
				 	$offset_start++;
				 	if ($offset_start == $offset_end) {
				 		$i=0;
				 		break;
					 }
				 	
				 }
				  
			}
			
			if ($i > 0 && $i < $s) {
				$i = $s-$i;
				for ($j=$i; $j<=$s; $j++) {
					$tvars['tvar_getHTMLNode'] .= $d13->tpl->render_subpage("sub.node.filler", $tvars);
				}
				$tvars['tvar_getHTMLNode'] .= '</div>';
			} else if ($i == 0) {
				$tvars['tvar_getHTMLNode'] .= '</div>';
			}
			$tvars['tvar_getHTMLSectors'] .= $d13->tpl->parse($d13->tpl->get("sub.node.sector"), $tvars);
		}
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Build Queue of all Sectors
		$queue=0;
		$tvars['tvar_winClass'] 	= '';
		$tvars['tvar_winTitle'] 	= '';
		$tvars['tvar_winContent'] 	= '';
		$tvars['tvar_getHTMLBuild'] = '';
		if (count($node->queue['build'])) {
			$queue++;
			$tvars['tvar_winId'] = $queue;
			$tvars['tvar_winClass'] = 'd13-queue queue-'.$queue;
			$tvars['tvar_winTitle'] = misc::getlang("active").' '.misc::getlang("add");
			$tvars['tvar_winContent'] = '';
		 	foreach ($node->queue['build'] as $item) {
		  		if ($node->modules[$item['slot']]['module']==-1) {
					$action='add';
		  		} else {
					$action='remove';
		  		}
		  		$remaining=$item['start']+$item['duration']*60-time();
		  		$tvars['tvar_winContent'] .= '<div class="cell"><img class="resource" src="'.CONST_DIRECTORY.'templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/modules/'.$node->data['faction'].'/'.$item['module'].'.png"> '.misc::getlang($action).' '.$gl["modules"][$node->data['faction']][$item['module']]["name"].'</div><div class="cell"><span id="build_'.$item['node'].'_'.$item['slot'].'">['.implode(':', misc::sToHMS($remaining)).']</span><script type="text/javascript">timedJump("build_'.$item['node'].'_'.$item['slot'].'", "index.php?p=node&action=get&nodeId='.$node->data['id'].'");</script></div><div class="cell"><a class="external" href="index.php?p=module&action=cancel&nodeId='.$node->data['id'].'&slotId='.$item['slot'].'"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
		 	}
		 	$tvars['tvar_getHTMLBuild'] = $d13->tpl->parse($d13->tpl->get("sub.queue"), $tvars);
		}
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Research Queue of all Sectors
		$tvars['tvar_winClass'] 	= '';
		$tvars['tvar_winTitle'] 	= '';
		$tvars['tvar_winContent'] 	= '';
		$tvars['tvar_getHTMLResearch'] = '';
		if (count($node->queue['research'])) {
			$queue++;
			$tvars['tvar_winId'] = $queue;
			$tvars['tvar_winClass'] = 'd13-queue queue-'.$queue;
			$tvars['tvar_winTitle'] = misc::getlang("active").' '.misc::getlang("research");
			$tvars['tvar_winContent'] = '';
		 	 foreach ($node->queue['research'] as $item) {
				$action='research';
		  		$remaining=$item['start']+$item['duration']*60-time();
		  		$tvars['tvar_winContent'] .= '<div class="cell"><img class="resource" src="'.CONST_DIRECTORY.'templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/technologies/'.$node->data['faction'].'/'.$item['technology'].'.png"> '.misc::getlang($action).' '.$gl["technologies"][$node->data['faction']][$item['technology']]["name"].'</div><div class="cell"><span id="build_'.$item['node'].'_'.$item['technology'].'">['.implode(':', misc::sToHMS($remaining)).']</span><script type="text/javascript">timedJump("build_'.$item['node'].'_'.$item['technology'].'", "index.php?p=node&action=get&nodeId='.$node->data['id'].'");</script></div>';
		 	}
		 	$tvars['tvar_getHTMLResearch'] = $d13->tpl->parse($d13->tpl->get("sub.queue"), $tvars);
		}
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Craft Queue of all Sectors
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Train Queue of all Sectors
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Buff Queue of all Sectors
		
		//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Combat Queue of all Sectors
		$tvars['tvar_winClass'] 	= '';
		$tvars['tvar_winTitle'] 	= '';
		$tvars['tvar_winContent'] 	= '';
		$tvars['tvar_getHTMLCombat'] = '';
		if (count($node->queue['combat'])) {
			$queue++;
			$tvars['tvar_winId'] = $queue;
			$tvars['tvar_winClass'] = 'd13-queue queue-'.$queue;
			$tvars['tvar_winTitle'] = misc::getlang("active").' '.misc::getlang("combat");
			$tvars['tvar_winContent'] = '';
		 	foreach ($node->queue['combat'] as $item) {
		 		$action='';
				$cancel='';
		  		if (!$item['stage']) {
					if ($item['sender']==$node->data['id']) {
						$action = 'outgoing';
						$cancel = '<div class="cell"><a class="external" href="?p=combat&action=cancel&nodeId='.$node->data['id'].'&combatId='.$item['id'].'"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
					} else {
						$action = 'incoming';
					}
		 	 	} else if ($item['sender']==$node->data['id']) {
					$action = 'returning';
		 		}
		  		$remaining = $item['start']+$item['duration']*60-time();
		  		$otherNode = new node();
		  		if ($item['sender']==$node->data['id']) {
					$status = $otherNode->get('id', $item['recipient']);
		  		} else { 
					$status = $otherNode->get('id', $item['sender']);
		  		}
				if ($status == 'done') {
					$tvars['tvar_winContent'] .= '<div><div class="cell">'.misc::getlang($action).' '.misc::getlang("combat").'</div><div class="cell">"'.$otherNode->data['name'].'"</div><div class="cell"><span id="combat_'.$item['id'].'">['.implode(':', misc::sToHMS($remaining)).']</span><script type="text/javascript">timedJump("combat_'.$item['id'].'", "?p=node&action=get&nodeId='.$node->data['id'].'");</script></div>'.$cancel.'</div>';
				}
		 	}
		 	$tvars['tvar_getHTMLCombat'] = $d13->tpl->parse($d13->tpl->get("sub.queue"), $tvars);
		}
		
	   	//- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
		}
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
		$page = "node.get";
		break;
	  
	//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET NODE
	case 'set':
		if ($game['options']['nodeEdit']) {
		if (isset($node->data['id'])) {
			$costData='';
			foreach ($game['factions'][$node->data['faction']]['costs']['set'] as $key=>$cost) {
				$costData.='<div class="cell">'.$cost['value'].'</div><div class="cell"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></div>';
			}
			$selectedFocus=array('hp'=>'', 'damage'=>'', 'armor'=>'');
			$selectedFocus[$node->data['focus']]=' selected';
			
			$tvars['tvar_costData'] 		= $costData;
			$tvars['tvar_selFocusHP'] 		= $selectedFocus['hp'];
			$tvars['tvar_selFocusDamage'] 	= $selectedFocus['damage'];
			$tvars['tvar_selFocusArmor'] 	= $selectedFocus['armor'];
		}
		$page = "node.set";
		}
		break;
		
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NODE
  case 'add':
  		$tvars['tvar_factionDescriptions'] = "";
  		$tvars['tvar_factionOptions'] = "";
		foreach ($gl['factions'] as $key=>$faction) {
			$tvars['tvar_factionOptions'] .= '<option value="'.$key.'">'.$faction['name'].'</option>';
		}
		foreach ($gl['factions'] as $key=>$faction) {
			$descriptions[$key]='"'.$faction['description'].'"';
		}
		$tvars['tvar_factionText'] = $descriptions[0];
		$tvars['tvar_factionDescriptions'] .=  implode(', ', $descriptions);
		$page = "node.add";
		break;
	
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD RANDOM NODE
  case 'random':
  		$tvars['tvar_getHTMLFactions'] = "";
  		$tvars['tvar_factionName'] = "";
  		$tvars['tvar_factionText'] = "";
  		$tvars['tvar_factionID'] = -1;
  		$factionId = -1;
  		//- - - - Check for Faction Fixation
  		if ($game['options']['factionFixation']) {
  			$nodes=node::getList($_SESSION[CONST_PREFIX.'User']['id']);
  			$t = count($nodes);
  			if ($t > 0) {
  				$factionId = $nodes[0]->data['faction'];
  			}
  		}
  		//- - - - Add Factions to Swiper Slide
  		foreach ($gl['factions'] as $key=>$faction) {
  			if ($game['factions'][$key]['active']) {
  				if (!$game['options']['factionFixation'] || ($game['options']['factionFixation'] && $factionId == $key) || ($game['options']['factionFixation'] && $factionId == -1)) {
					$tvars['tvar_factionName'] = $faction['name'];
  					$tvars['tvar_factionText'] = $faction['description'];
  					$tvars['tvar_factionID'] = $key;
					$tvars['tvar_getHTMLFactions'] .= $d13->tpl->parse($d13->tpl->get("sub.node.faction"), $tvars);
				}
			}
		}
		$d13->tpl->inject($d13->tpl->parse($d13->tpl->get("sub.swiper.horizontal"), $tvars));
		$page = "node.random";
		break;
	
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE NODE
  	case 'remove':
  		if ($game['options']['nodeRemove']) {
			if (isset($node->data['id'])) {
				$page = "node.remove";
			}
		}
		break;
		
  //= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = MOVE NODE
  	case 'move':
  		if ($game['options']['nodeMove']) {
			if (isset($node->data['id'])) {
				$costData='';
				foreach ($game['factions'][$node->data['faction']]['costs']['move'] as $key=>$cost) {
					$costData.='<div class="cell">'.$cost['value'].'</div><div class="cell"><img class="resource" src="templates/'.$_SESSION[CONST_PREFIX.'User']['template'].'/images/resources/'.$cost['resource'].'.png" title="'.$gl["resources"][$cost['resource']]["name"].'"></div>';
				}
				$tvars['tvar_costData'] = $costData;
			}
			$page = "node.move";
		}
		break;
  
	//= = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = LIST TOWNS
  	case 'list':
  		$tvars['tvar_nodeList'] = "";
  		$t = count($nodes);
  		
  		//0 towns - create new town
  		if ($t == 0) {
  			if ($game['options']['gridSystem']==1) {
  				header("location: index.php?p=node&action=add");
  			} else {
  				header("location: index.php?p=node&action=random");
  			}
  			
  		//1 town - jump to town
  		} else if ($t == 1) {
  			$link = "?p=node&action=get&nodeId=".$nodes[0]->data['id'];
  			header("location: ".$link);
  			
  		//2+ towns - list all towns
  		} else {
  			foreach ($nodes as $key=>$node) {
				$tvars['tvar_nodeList'] .=  '<div><a class="external" href="index.php?p=node&action=get&nodeId='.$node->data['id'].'">'.$node->data['name'].'</a></div>';
			}
			$page = "node.list";
  		}
	  	break;
 	}
 
 }

//----------------------------------------------------------------------------------------
// RENDER OUTPUT
//----------------------------------------------------------------------------------------

$d13->tpl->render_page($page, $tvars, $node);

//=====================================================================================EOF

?>