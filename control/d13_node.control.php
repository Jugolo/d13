<?php

// ========================================================================================
//
// EMPTY.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_nodeController extends d13_controller
{
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
		parent::__construct($d13);
		$tvars = array();
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
	}
	
	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function doControl()
	{
				
		switch ($_GET['action'])
		{
		
			case 'get':
				return $this->nodeGet();
				break;
				
			case 'set':
				return $this->nodeSet();
				break;
				
			case 'add':
				return $this->nodeAdd();
				break;
				
			case 'random':
				return $this->nodeRandom();
				break;
				
			case 'remove':
				return $this->nodeRemove();
				break;
				
			case 'move':
				return $this->nodeMove();
				break;
				
			case 'cancelShield':
				return $this->nodeCancelShield();
				break;
			
			case 'cancelBuff':
				return $this->nodeCancelBuff();
				break;
			
			case 'cancelMarket':
				return $this->nodeCancelMarket();
				break;
			
			case 'list':
				return $this->nodeList();
				break;
				
		}
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeGet()
	{
		
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$nodeId = $_GET['nodeId'];
		} else {
			$nodeId = $_SESSION[CONST_PREFIX . 'User']['node'];
		}
		
		$this->d13->node = $this->d13->createNode();
		$status = $this->d13->node->get('id', $nodeId);
		
		if ($status == 'done') {
			if ($this->d13->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$_SESSION[CONST_PREFIX . 'User']['node'] = $nodeId;
				$this->d13->node->checkAll(time());
				$this->d13->node->getLocation();
				$this->d13->node->queues->getQueue('build');
				$this->d13->node->queues->getQueue('combat');			
				$tvars = $this->nodeRender();
			} else {
				$message = $this->d13->getLangUI("accessDenied");
			}
		} else {
			$message = $this->d13->getLangUI($status);
		}
		
		return $tvars;
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeSet()
	{
		
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$this->d13->node = $this->d13->createNode();
			$status = $this->d13->node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_POST['name'], $_POST['focus'])) && ($_POST['name']))
				if (in_array($_POST['focus'], array(
					'hp',
					'armor',
					'damage'
				)))
				if ($this->d13->node->checkOptions('nodeEdit') && $this->d13->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$oldName = $this->d13->node->data['name'];
					$oldFocus = $this->d13->node->data['focus'];
					$this->d13->node->data['name'] = $_POST['name'];
					$this->d13->node->data['focus'] = $_POST['focus'];
					$status = $this->d13->node->set();
					if ($status != 'done') {
						$this->d13->node->data['name'] = $oldName;
						$this->d13->node->data['focus'] = $oldFocus;
					}

					$message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("accessDenied");
				else $message = $this->d13->getLangUI("invalidFocus");
			}
			else $message = $this->d13->getLangUI($status);
		}
		else $message = $this->d13->getLangUI("accessDenied");
		
		
		
		$costData = '';
		foreach($this->d13->getFaction($this->d13->node->data['faction'], 'costs', 'set') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $this->d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}

		$selectedFocus = array(
			'hp' => '',
			'damage' => '',
			'armor' => ''
		);
		
		$selectedFocus[$this->d13->node->data['focus']] = ' selected';
		$tvars['tvar_costData'] 		= $costData;
		$tvars['tvar_selFocusHP'] 		= $selectedFocus['hp'];
		$tvars['tvar_selFocusDamage'] 	= $selectedFocus['damage'];
		$tvars['tvar_selFocusArmor'] 	= $selectedFocus['armor'];
		$tvars['tvar_page'] 			= "node.set";
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeAdd()
	{
		
		$tvars = array();
		
		if (isset($_POST['faction'], $_POST['name'], $_POST['x'], $_POST['y'])) {
			if ($_POST['faction'] != '' && !empty($_POST['name']) && !empty($_POST['x']) && !empty($_POST['y'])) {
				$this->d13->node = $this->d13->createNode();
				$this->d13->node->data['faction'] = $_POST['faction'];
				$this->d13->node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$this->d13->node->data['name'] = $_POST['name'];
				$this->d13->node->location['x'] = $_POST['x'];
				$this->d13->node->location['y'] = $_POST['y'];
				$message = $this->d13->getLangUI($this->d13->node->add($_SESSION[CONST_PREFIX . 'User']['id']));
			}
			else {
				$message = $this->d13->getLangUI("insufficientData");
			}
		}
		else {
			$message = $this->d13->getLangUI("insufficientData");
		}
		
		
		$tvars['tvar_factionDescriptions'] = "";
		$tvars['tvar_factionOptions'] = "";
		foreach($this->d13->getLangGL('factions') as $key => $faction) {
			$tvars['tvar_factionOptions'].= '<option value="' . $key . '">' . $faction['name'] . '</option>';
		}

		foreach($this->d13->getLangGL('factions') as $key => $faction) {
			$descriptions[$key] = '"' . $faction['description'] . '"';
		}

		$tvars['tvar_factionText'] = $descriptions[0];
		$tvars['tvar_factionDescriptions'].= implode(', ', $descriptions);
		$tvars['tvar_page'] = "node.add";
		
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeRandom()
	{
		
		$tvars = array();
		
		if (isset($_POST['faction'])) {
			$factionSet = $_POST['faction'];
		} else {
			$factionSet = -1;
		}
		
		// - - - - Add Factions to Swiper Slide
		$factionId = - 1;
		$tvars['tvar_getHTMLFactions'] = "";
		$tvars['tvar_factionName'] = "";
		$tvars['tvar_factionText'] = "";
		$tvars['tvar_factionID'] = - 1;
		
		
		foreach($this->d13->getFaction() as $faction) {
			if ($faction['active']) {
				if (!$this->d13->getGeneral('options', 'factionFixation') || ($this->d13->getGeneral('options', 'factionFixation') && $faction['id'] == $factionId) || ($this->d13->getGeneral('options', 'factionFixation') && $factionId == - 1)) {
					$tvars['tvar_factionName'] = $this->d13->getLangGL('factions', $faction['id'], 'name');
					$tvars['tvar_factionText'] = $this->d13->getLangGL('factions', $faction['id'], 'description');
					$tvars['tvar_factionID'] = $faction['id'];
					$tvars['tvar_getHTMLFactions'].= $this->d13->templateSubpage("sub.node.faction", $tvars);
					$factionId = $faction['id'];
				}
			}
		}
		
		// - - - - Auto-set faction if only one is available and skip selection
		if ($factionId > -1 && $factionSet == -1) {
			$factionSet = $factionId;
		}
		
		if (isset($factionSet)) {
			if ($factionSet > -1) {
				$coord = array();
				$grid = new d13_grid($this->d13);
				$coord = $grid->getFree();
				$this->d13->node = $this->d13->createNode();
				$this->d13->node->data['faction'] = $factionSet;
				$this->d13->node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$this->d13->node->data['name'] = $_SESSION[CONST_PREFIX . 'User']['name'];
				$this->d13->node->location['x'] = $coord['x'];
				$this->d13->node->location['y'] = $coord['y'];
				$message = $this->d13->getLangUI($this->d13->node->add($_SESSION[CONST_PREFIX . 'User']['id']));
				header('Location: ?p=node&action=list');
				exit();
			}
			else {
				$message = $this->d13->getLangUI("insufficientData");
			}
		}
				
		// - - - - Check for Faction Fixation
		if ($this->d13->getGeneral('options', 'factionFixation')) {
			$nodes = $this->d13->getNodeList($_SESSION[CONST_PREFIX . 'User']['id']); #$this->d13->getNodeList($_SESSION[CONST_PREFIX . 'User']['id']);
			$t = count($nodes);
			if ($t > 0) {
				$factionId = $nodes[0]->data['faction'];
			}
		}

		

		$this->d13->templateInject($this->d13->templateSubpage("sub.swiper.horizontal", $tvars));
		$tvars['tvar_page'] = "node.random";
					
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeRemove()
	{
		
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$this->d13->node = $this->d13->createNode();
			$status = $this->d13->node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_GET['go'])) && ($_GET['go'])) {
					if ($this->d13->node->checkOptions('nodeRemove') && $this->d13->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
						$status = $this->d13->node->remove($_GET['nodeId']);
						if ($status == 'done') {
							header('location: p=node&action=list');
							exit();
							
						}
						else {
							$message = $this->d13->getLangUI($status);
						}
					}
					else {
						$message = $this->d13->getLangUI("accessDenied");
					}
				}
				else {
					$message = $this->d13->getLangUI($status);
				}
			}
			else {
				$message = $this->d13->getLangUI("insufficientData");
			}
		}
		
		$tvars['tvar_page'] = "node.remove";
		
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeMove()
	{
		
		$tvars = array();
		
		if (isset($_GET['nodeId'])) {
			$this->d13->node = $this->d13->createNode();
			$status = $this->d13->node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if (isset($_POST['x'], $_POST['y']))
				if ($this->d13->node->checkOptions('nodeMove') && $this->d13->node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])
					if ($this->d13->getFaction($this->d13->node->data['faction'], 'costs', 'move', 0, 'resource') > - 1) {
						$message = $this->d13->getLangUI($this->d13->node->move($_POST['x'], $_POST['y']));
					}
				else $message = $this->d13->getLangUI("nodeMoveDisabled");
				else $message = $this->d13->getLangUI("accessDenied");
			}
			else $message = $this->d13->getLangUI($status);
		}
		else $message = $this->d13->getLangUI("insufficientData");
		
		$costData = '';
		foreach($this->d13->getFaction($this->d13->node->data['faction'], 'costs', 'move') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $this->d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}
		$this->d13->node->getLocation();
		$tvars['tvar_nodeX'] = $this->d13->node->location['x'];
		$tvars['tvar_nodeY'] = $this->d13->node->location['y'];
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_page'] = "node.move";
				
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeCancelShield()
	{
		
		$tvars = array();
		
		if (isset($_GET['shieldId'])) {
			$this->d13->node = $this->d13->createNode();
			$status = $this->d13->node->get('id', $_GET['nodeId']);
			$status = $this->d13->node->cancelShield($_GET['shieldId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
				exit();
				
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
		else $message = $this->d13->getLangUI("insufficientData");
			
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeCancelBuff()
	{
		
		$tvars = array();
		
		if (isset($_GET['buffId']) && isset($_GET['nodeId'])) {
			$this->d13->node = $this->d13->createNode();
			$status = $this->d13->node->get('id', $_GET['nodeId']);
			$status = $this->d13->node->cancelBuff($_GET['buffId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
				exit();
				
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
		else $message = $this->d13->getLangUI("insufficientData");
			
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeCancelMarket()
	{
		
		$tvars = array();
		
		if (isset($_GET['slotId']) && isset($_GET['nodeId'])) {
			$this->d13->node = $this->d13->createNode();
			$status = $this->d13->node->get('id', $_GET['nodeId']);
			$status = $this->d13->node->cancelMarket($_GET['slotId']);
			if ($status == 'done') {
				header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
				exit();
				
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
		else $message = $this->d13->getLangUI("insufficientData");
			
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function nodeList()
	{
		
		$tvars = array();
		$tvars['tvar_nodeList'] = "";
		
		$nodes = $this->d13->getNodeList($_SESSION[CONST_PREFIX . 'User']['id']); #$this->d13->getNodeList($_SESSION[CONST_PREFIX . 'User']['id']);
		$t = count($nodes);

		// - - - - - - - - - - - - - - - - - - - - 0 towns - create new town

		if ($t == 0) {
			header("location: index.php?p=node&action=random");
			exit();
		// - - - - - - - - - - - - - - - - - - - - 1 town - jump to town

		} else if ($t == 1) {
			$link = "?p=node&action=get&nodeId=" . $nodes[0]->data['id'];
			header("location: " . $link);
			exit();
			
		// - - - - - - - - - - - - - - - - - - - - 2+ towns - list all towns

		} else {
			foreach($nodes as $key => $node) {
				$tvars['tvar_nodeList'].= '<div><a class="external" href="index.php?p=node&action=get&nodeId=' . $node->data['id'] . '">' . $node->data['name'] . '</a></div>';
			}

			$tvars['tvar_page'] = "node.list";
		}
	
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function nodeRender()
	{
		
		$tvars = array();
		
		$nodecount = $this->d13->getGeneral('users', 'maxModules') * $this->d13->getGeneral('users', 'maxSectors');
		$buildQueue = array();
		
		for ($i = 0; $i < $nodecount; $i++) {
			$buildQueue[$i]['check'] = 0;
			$buildQueue[$i]['image'] = '';
		}
		
		foreach($this->d13->node->queues->queue['build'] as $item) {
			$buildQueue[$item['slot']]['check'] = 1;
			$buildQueue[$item['slot']]['image'] = $this->d13->getModule($this->d13->node->data['faction'], $item['obj_id'], 'images');
		}
				
		$tvars['tvar_getHTMLSectors'] = "";
		for ($sector = 1; $sector <= $this->d13->getGeneral('users', 'maxSectors'); $sector++) {

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Render Sector View
			
			$tvars['tvar_getHTMLNode'] = "";
			
			$offset_start = ($sector - 1) * $this->d13->getGeneral('users', 'maxModules');
			$offset_end = $offset_start + $this->d13->getGeneral('users', 'maxModules');
			
			$add = true;
			$limit = 4;
			$i = 0;
			
			foreach($this->d13->node->modules as $module) {
				if ($module['slot'] >= $offset_start && $module['slot'] <= $offset_end) {
					
					// - - - end current row
					if ($i == $limit) {
						$tvars['tvar_getHTMLNode'].= '</div>';
						$i = 0;
						if ($add) {
							$limit++;
							$add = false;
						} else {
							$limit--;
							$add = true;
						}
					}
					// - - - begin new row
					if ($i == 0) {
						$tvars['tvar_getHTMLNode'].= '<div class="row no-gutter">';
					}
					
					// - - - Module under construction
					if ($buildQueue[$module['slot']]['check']) {
						$tvars['tvar_moduleLink'] = "";
						$tvars['tvar_moduleImage'] = $buildQueue[$module['slot']]['image'][0]['image'];
						$tvars['tvar_moduleSpinner'] = '<img class="spinner resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/gear.png">';
						$tvars['tvar_moduleLabel'] = $this->d13->getLangUI("underConstruction");
						$tvars['tvar_getHTMLNode'].= $this->d13->templateSubpage("sub.node.module", $tvars);
						$tvars['tvar_moduleClass'] = "";
					// - - - Module built
					} else if ($module['module'] > - 1) {
					
						$the_module = $this->d13->createModule($module['module'], $module['slot'], $this->d13->node);
						
						$tvars['tvar_moduleLink'] = "index.php?p=module&action=get&nodeId=" . $this->d13->node->data['id'] . "&slotId=" . $module['slot'];
						$tvars['tvar_moduleImage'] = $the_module->data['image'];
						if (($module['input'] <= 0 && $this->d13->getModule($this->d13->node->data['faction'], $module['module'], 'maxInput') > 0) || ($this->d13->getModule($this->d13->node->data['faction'], $module['module'], 'type') == 'defense' && $module['input'] < $this->d13->getModule($this->d13->node->data['faction'], $module['module'], 'maxInput'))) {
							$tvars['tvar_moduleSpinner'] = '<a href="#" class="tooltip-left" data-tooltip="' . $this->d13->getLangUI("no") . " " . $this->d13->getLangGL('resources', $the_module->data['inputResource'], 'name') . '"><img class="animated bounce resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/exclamation.png"></a>';
						} else {
							$tvars['tvar_moduleSpinner'] = "";
						}
						if (isset($_GET['focusId']) && $_GET['focusId'] == $module['slot']) {
							$tvars['tvar_moduleClass'] = "animated tada";
						} else {
							$tvars['tvar_moduleClass'] = "";
						}
						if ($the_module->data['maxLevel'] > 1) {
							$tvars['tvar_moduleLabel'] = $this->d13->getLangGL("modules", $this->d13->node->data['faction'], $module['module'], "name") . " [L" . $module['level'] . "]";
						} else {
							$tvars['tvar_moduleLabel'] = $this->d13->getLangGL("modules", $this->d13->node->data['faction'], $module['module'], "name");
						}
						$tvars['tvar_getHTMLNode'].= $this->d13->templateSubpage("sub.node.module", $tvars);
					
					// - - - Empty Slot
					} else {
						$tvars['tvar_moduleLink'] = "index.php?p=module&action=list&nodeId=" . $this->d13->node->data['id'] . "&slotId=" . $module['slot'];
						mt_srand($module['slot']*$sector);
						$imgid = mt_rand(0,9);
						$tvars['tvar_moduleImage'] = "module_empty_".$imgid.".png";
						$tvars['tvar_moduleClass'] = "";
						$tvars['tvar_moduleSpinner'] = "";
						$tvars['tvar_moduleLabel'] = $this->d13->getLangUI("emptySlot") . ' ' . $this->d13->getLangUI("clickToBuild");
						$tvars['tvar_getHTMLNode'].= $this->d13->templateSubpage("sub.node.module", $tvars);
					}

					// - - - advance or new row
					if ($i != $limit) {
					
						$i++;
					}
					
					$offset_start++;
					if ($offset_start == $offset_end) {
						$i = 0;
						break;
					}
				}
			}

			if ($i > 0 && $i < $limit) {
				$i = $limit - $i;
				for ($j = $i; $j <= $limit; $j++) {
					$tvars['tvar_getHTMLNode'].= $this->d13->templateSubpage("sub.node.filler", $tvars);
				}
				$tvars['tvar_getHTMLNode'].= '</div>';
			} else if ($i == 0) {
				$tvars['tvar_getHTMLNode'].= '</div>';
			}
			$tvars['tvar_getHTMLSectors'].= $this->d13->templateSubpage("sub.node.sector", $tvars);
		}
		
		$tvars['tvar_page'] = "node.get";
		$this->d13->templateInject($this->d13->templateSubpage("sub.swiper.horizontal", $tvars));
		
		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		
		
		$tvars['tvar_nodeID'] 		= $this->d13->node->data['id'];
		$tvars['tvar_nodeName'] 	= $this->d13->node->data['name'];
		$tvars['tvar_nodeX'] 		= $this->d13->node->data['x'];	// TODO; not always available
		$tvars['tvar_nodeY'] 		= $this->d13->node->data['y'];	// TODO: not always available
	
		$this->d13->outputPage($tvars['tvar_page'], $tvars, $this->d13->node);
		
	}

}

// =====================================================================================EOF

