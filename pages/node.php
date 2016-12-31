<?php

// ========================================================================================
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
// ========================================================================================
// ----------------------------------------------------------------------------------------
// PROCESS MODEL
// ----------------------------------------------------------------------------------------

global $d13;
$node = NULL;
$message = NULL;
$x = NULL;
$y = NULL;
$html = NULL;
$d13->dbQuery('start transaction');

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET NODE DATA

	case 'get':
		if (isset($_GET['nodeId'])) {
			$node = new node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$node->checkAll(time());
					$node->getLocation();
					$node->getQueue('build');
					$node->getQueue('combat');
					$nodecount = $d13->getGeneral('users', 'maxModules') * $d13->getGeneral('users', 'maxSectors');
					$buildQueue = array();
					for ($i = 0; $i < $nodecount; $i++) {
						$buildQueue[$i] = 0;
					}

					foreach($node->queue['build'] as $item) {
						$buildQueue[$item['slot']] = 1;
					}
				}
				else {
					$message = $d13->getLangUI("accessDenied");
				}
			}
			else {
				$message = $d13->getLangUI($status);
			}
		}
		else {
			$message = $d13->getLangUI("insufficientData");
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET NODE DATA

	case 'set':
		if (isset($_GET['nodeId'])) {
			$node = new node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_POST['name'], $_POST['focus'])) && ($_POST['name']))
				if (in_array($_POST['focus'], array(
					'hp',
					'armor',
					'damage'
				)))
				if ($node->checkOptions('nodeEdit') && $node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$oldName = $node->data['name'];
					$oldFocus = $node->data['focus'];
					$node->data['name'] = $_POST['name'];
					$node->data['focus'] = $_POST['focus'];
					$status = $node->set();
					if ($status != 'done') {
						$node->data['name'] = $oldName;
						$node->data['focus'] = $oldFocus;
					}

					$message = $d13->getLangUI($status);
				}
				else $message = $d13->getLangUI("accessDenied");
				else $message = $d13->getLangUI("invalidFocus");
			}
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("accessDenied");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NEW NODE

	case 'add':
		if (isset($_POST['faction'], $_POST['name'], $_POST['x'], $_POST['y'])) {
			if ($_POST['faction'] != '' && !empty($_POST['name']) && !empty($_POST['x']) && !empty($_POST['y'])) {
				$node = new node();
				$node->data['faction'] = $_POST['faction'];
				$node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$node->data['name'] = $_POST['name'];
				$node->location['x'] = $_POST['x'];
				$node->location['y'] = $_POST['y'];
				$message = $d13->getLangUI($node->add($_SESSION[CONST_PREFIX . 'User']['id']));
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}
		else {
			$message = $d13->getLangUI("insufficientData");
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NEW RANDOM NODE

	case 'random':
		if (isset($_POST['faction'])) {
			if ($_POST['faction'] != '') {
				$coord = array();
				$grid = new grid();
				$coord = $grid->getFree();
				$node = new node();
				$node->data['faction'] = $_POST['faction'];
				$node->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
				$node->data['name'] = $_SESSION[CONST_PREFIX . 'User']['name'] . ' ' . rand(1, 9999); //TODO
				$node->location['x'] = $coord['x'];
				$node->location['y'] = $coord['y'];
				$message = $d13->getLangUI($node->add($_SESSION[CONST_PREFIX . 'User']['id']));
				header('Location: ?p=node&action=list');
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE NODE

	case 'remove':
		if (isset($_GET['nodeId'])) {
			$node = new node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if ((isset($_GET['go'])) && ($_GET['go'])) {
					if ($node->checkOptions('nodeRemove') && $node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
						$status = node::remove($_GET['nodeId']);
						if ($status == 'done') {
							header('location: node.php?action=list');
						}
						else {
							$message = $d13->getLangUI($status);
						}
					}
					else {
						$message = $d13->getLangUI("accessDenied");
					}
				}
				else {
					$message = $d13->getLangUI($status);
				}
			}
			else {
				$message = $d13->getLangUI("insufficientData");
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = MOVE NODE

	case 'move':
		if (isset($_GET['nodeId'])) {
			$node = new node();
			$status = $node->get('id', $_GET['nodeId']);
			if ($status == 'done') {
				if (isset($_POST['x'], $_POST['y']))
				if ($node->checkOptions('nodeMove') && $node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])
				if ($d13->getGeneral('factions', $node->data['faction'], 'costs') ['move'][0]['resource'] > - 1) $message = $d13->getLangUI($node->move($_POST['x'], $_POST['y']));
				else $message = $d13->getLangUI("nodeMoveDisabled");
				else $message = $d13->getLangUI("accessDenied");
			}
			else $message = $d13->getLangUI($status);
		}
		else $message = $d13->getLangUI("insufficientData");
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = LIST NODE

	case 'list':
		$nodes = node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
		break;
	}
}
else {
	$message = $d13->getLangUI("accessDenied");
}

if ((isset($status)) && ($status == 'error')) {
	$d13->dbQuery('rollback');
}
else {
	$d13->dbQuery('commit');
}

// ----------------------------------------------------------------------------------------
// PROCESS VIEW
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$page = "node";

if (isset($node)) {
	$tvars['tvar_nodeFaction'] = $node->data['faction'];
	$tvars['tvar_nodeID'] = $node->data['id'];
	$tvars['tvar_nodeName'] = $node->data['name'];
	if (isset($node->data['x'])) {
		$tvars['tvar_nodeX'] = $node->data['x'];
	}

	if (isset($node->data['y'])) {
		$tvars['tvar_nodeY'] = $node->data['y'];
	}
}

if (isset($_SESSION[CONST_PREFIX . 'User']['id'], $_GET['action'])) {
	switch ($_GET['action']) {

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = GET NODE

	case 'get':
		if ((isset($node->data['id'])) && ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id'])) {
			$tvars['tvar_getHTMLSectors'] = "";
			for ($sector = 1; $sector <= $d13->getGeneral('users', 'maxSectors'); $sector++) {

				// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Render Sector View

				$offset_start = ($sector - 1) * $d13->getGeneral('users', 'maxModules');
				$offset_end = $offset_start + $d13->getGeneral('users', 'maxModules');
				$node->getModules();
				$i = 0;
				$s = 5;
				$tvars['tvar_getHTMLNode'] = "";
				foreach($node->modules as $module) {
					if ($module['slot'] >= $offset_start && $module['slot'] <= $offset_end) {
						
						$the_module = d13_module_factory::create($module['module'], $module['slot'], $node);
						if ($i == $s) {
							$tvars['tvar_getHTMLNode'].= '</div>';
							$i = 0;
						}

						if ($i == 0) {
							$tvars['tvar_getHTMLNode'].= '<div class="row no-gutter">';
						}

						if ($buildQueue[$module['slot']]) {
							$tvars['tvar_moduleLink'] = "";
							$tvars['tvar_moduleImage'] = "pending.png";
							$tvars['tvar_moduleClass'] = '<img class="spinner resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/gear.png">';
							$tvars['tvar_moduleLabel'] = $d13->getLangUI("underConstruction");
							$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
						}
						else
						if ($module['module'] > - 1) {
							$tvars['tvar_moduleLink'] = "index.php?p=module&action=get&nodeId=" . $node->data['id'] . "&slotId=" . $module['slot'];
							$tvars['tvar_moduleImage'] = $the_module->data['image'];
							if (($module['input'] <= 0 && $d13->getModule($node->data['faction'], $module['module'], 'maxInput') > 0) || ($d13->getModule($node->data['faction'], $module['module'], 'type') == 'defense' && $module['input'] < $d13->getModule($node->data['faction'], $module['module'], 'maxInput'))) {
								$tvars['tvar_moduleClass'] = '<a href="#" class="tooltip-left" data-tooltip="' . $d13->getLangUI("no") . " " . $d13->getLangGL('resources', $the_module->data['inputResource'], 'name') . '"><img class="animated bounce resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/icon/exclamation.png"></a>';
							}
							else {
								$tvars['tvar_moduleClass'] = "";
							}

							$tvars['tvar_moduleLabel'] = $d13->getLangGL("modules", $node->data['faction'], $module['module'], "name") . " [L" . $module['level'] . "]";
							$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
						}
						else {
							$tvars['tvar_moduleLink'] = "index.php?p=module&action=list&nodeId=" . $node->data['id'] . "&slotId=" . $module['slot'];
							$tvars['tvar_moduleImage'] = "empty.png";
							$tvars['tvar_moduleClass'] = "";
							$tvars['tvar_moduleLabel'] = $d13->getLangUI("emptySlot");
							$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.module", $tvars);
						}

						$i++;
						$offset_start++;
						if ($offset_start == $offset_end) {
							$i = 0;
							break;
						}
					}
				}

				if ($i > 0 && $i < $s) {
					$i = $s - $i;
					for ($j = $i; $j <= $s; $j++) {
						$tvars['tvar_getHTMLNode'].= $d13->templateSubpage("sub.node.filler", $tvars);
					}

					$tvars['tvar_getHTMLNode'].= '</div>';
				}
				else
				if ($i == 0) {
					$tvars['tvar_getHTMLNode'].= '</div>';
				}

				$tvars['tvar_getHTMLSectors'].= $d13->templateParse($d13->templateGet("sub.node.sector") , $tvars);
			}

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Build Queue of all Sectors

			$queue = 0;
			$tvars['tvar_winClass'] = '';
			$tvars['tvar_winTitle'] = '';
			$tvars['tvar_winContent'] = '';
			$tvars['tvar_getHTMLBuild'] = '';
			if (count($node->queue['build'])) {
				$queue++;
				$tvars['tvar_winId'] = $queue;
				$tvars['tvar_winClass'] = 'd13-queue queue-' . $queue;
				$tvars['tvar_winTitle'] = $d13->getLangUI("active") . ' ' . $d13->getLangUI("add");
				$tvars['tvar_winContent'] = '';
				foreach($node->queue['build'] as $item) {
					if ($item['action'] == 'build') {
						$action = 'add';
					} else if ($item['action'] == 'upgrade') {
						$action = 'upgrade';
					} else if ($item['action'] == 'remove') {
						$action = 'remove';
					}
					$images = array();
					$images = $d13->getModule($node->data['faction'], $item['module'], 'images');
					$image = $images[0]['image'];
					$remaining = $item['start'] + $item['duration'] * 60 - time();
					if ($remaining > 0) {
						$tvars['tvar_winContent'].= '<div class="cell"><img class="resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/modules/' . $node->data['faction'] . '/' . $image . '"> ' . $d13->getLangUI($action) . ' ' . $d13->getLangGL("modules", $node->data['faction'], $item['module'], "name") . '</div><div class="cell"><span id="build_' . $item['node'] . '_' . $item['slot'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("build_' . $item['node'] . '_' . $item['slot'] . '", "index.php?p=node&action=get&nodeId=' . $node->data['id'] . '");</script></div><div class="cell"><a class="external" href="index.php?p=module&action=cancel&nodeId=' . $node->data['id'] . '&slotId=' . $item['slot'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
					}
				}
				$tvars['tvar_getHTMLBuild'] = $d13->templateParse($d13->templateGet("sub.queue") , $tvars);
			}

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Research Queue of all Sectors

			$tvars['tvar_winClass'] = '';
			$tvars['tvar_winTitle'] = '';
			$tvars['tvar_winContent'] = '';
			$tvars['tvar_getHTMLResearch'] = '';
			if (count($node->queue['research'])) {
				$queue++;
				$tvars['tvar_winId'] = $queue;
				$tvars['tvar_winClass'] = 'd13-queue queue-' . $queue;
				$tvars['tvar_winTitle'] = $d13->getLangUI("active") . ' ' . $d13->getLangUI("research");
				$tvars['tvar_winContent'] = '';
				foreach($node->queue['research'] as $item) {
					$action = 'research';
					$remaining = $item['start'] + $item['duration'] * 60 - time();
					$tvars['tvar_winContent'].= '<div class="cell"><img class="resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/technologies/' . $node->data['faction'] . '/' . $item['technology'] . '.png"> ' . $d13->getLangUI($action) . ' ' . $d13->getLangGL("technologies", $node->data['faction'], $item['technology'], "name") . '</div><div class="cell"><span id="build_' . $item['node'] . '_' . $item['technology'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("build_' . $item['node'] . '_' . $item['technology'] . '", "index.php?p=node&action=get&nodeId=' . $node->data['id'] . '");</script></div>';
				}
				$tvars['tvar_getHTMLResearch'] = $d13->templateParse($d13->templateGet("sub.queue") , $tvars);
			}

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Craft Queue of all Sectors

			$tvars['tvar_winClass'] = '';
			$tvars['tvar_winTitle'] = '';
			$tvars['tvar_winContent'] = '';
			$tvars['tvar_getHTMLCraft'] = '';
			if (count($node->queue['craft'])) {
				$queue++;
				$tvars['tvar_winId'] = $queue;
				$tvars['tvar_winClass'] = 'd13-queue queue-' . $queue;
				$tvars['tvar_winTitle'] = $d13->getLangUI("active") . ' ' . $d13->getLangUI("craft");
				$tvars['tvar_winContent'] = '';
				foreach($node->queue['craft'] as $item) {
					if ($item['stage'] == 0) {
					#if ($node->modules[$item['component']]['module'] == - 1) {
						$action = 'add';
					} else {
						$action = 'remove';
					}
					$remaining = $item['start'] + $item['duration'] * 60 - time();
					$tvars['tvar_winContent'].= '<div class="cell"><img class="resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/components/' . $node->data['faction'] . '/' . $item['component'] . '.png"> ' . $d13->getLangUI($action) . ' ' . $d13->getLangGL("components", $node->data['faction'], $item['component'], "name") . '</div><div class="cell"><span id="build_' . $item['node'] . '_' . $item['component'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("build_' . $item['node'] . '_' . $item['component'] . '", "index.php?p=node&action=get&nodeId=' . $node->data['id'] . '");</script></div>';
				}
				$tvars['tvar_getHTMLCraft'] = $d13->templateParse($d13->templateGet("sub.queue") , $tvars);
			}

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Train Queue of all Sectors

			$tvars['tvar_winClass'] = '';
			$tvars['tvar_winTitle'] = '';
			$tvars['tvar_winContent'] = '';
			$tvars['tvar_getHTMLTrain'] = '';
			if (count($node->queue['train'])) {
				$queue++;
				$tvars['tvar_winId'] = $queue;
				$tvars['tvar_winClass'] = 'd13-queue queue-' . $queue;
				$tvars['tvar_winTitle'] = $d13->getLangUI("active") . ' ' . $d13->getLangUI("train");
				$tvars['tvar_winContent'] = '';
				foreach($node->queue['train'] as $item) {
					if ($item['stage'] == 0) {
					#if ($node->modules[$item['unit']]['module'] == - 1) {
						$action = 'add';
					} else {
						$action = 'remove';
					}
					$remaining = $item['start'] + $item['duration'] * 60 - time();
					$tvars['tvar_winContent'].= '<div class="cell"><img class="resource" src="' . CONST_DIRECTORY . 'templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/units/' . $node->data['faction'] . '/' . $item['unit'] . '.png"> ' . $d13->getLangUI($action) . ' ' . $d13->getLangGL("units", $node->data['faction'], $item['unit'], "name") . '</div><div class="cell"><span id="build_' . $item['node'] . '_' . $item['unit'] . '">' . implode(':', misc::sToHMS($remaining)) . '</span><script type="text/javascript">timedJump("build_' . $item['node'] . '_' . $item['unit'] . '", "index.php?p=node&action=get&nodeId=' . $node->data['id'] . '");</script></div>';
				}

				$tvars['tvar_getHTMLTrain'] = $d13->templateParse($d13->templateGet("sub.queue") , $tvars);
			}

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Combat Queue of all Sectors

			$tvars['tvar_winClass'] = '';
			$tvars['tvar_winTitle'] = '';
			$tvars['tvar_winContent'] = '';
			$tvars['tvar_getHTMLCombat'] = '';
			if (count($node->queue['combat'])) {
				$queue++;
				$tvars['tvar_winId'] = $queue;
				$tvars['tvar_winClass'] = 'd13-queue queue-' . $queue;
				$tvars['tvar_winTitle'] = $d13->getLangUI("active") . ' ' . $d13->getLangUI("combat");
				$tvars['tvar_winContent'] = '';
				foreach($node->queue['combat'] as $item) {
					$action = '';
					$cancel = '';
					if (!$item['stage']) {
						if ($item['sender'] == $node->data['id']) {
							$action = 'outgoing';
							$cancel = '<div class="cell"><a class="external" href="?p=combat&action=cancel&nodeId=' . $node->data['id'] . '&combatId=' . $item['id'] . '"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a></div>';
						}
						else {
							$action = 'incoming';
						}
					}
					else
					if ($item['sender'] == $node->data['id']) {
						$action = 'returning';
					}

					$remaining = $item['start'] + $item['duration'] * 60 - time();
					$otherNode = new node();
					if ($item['sender'] == $node->data['id']) {
						$status = $otherNode->get('id', $item['recipient']);
					}
					else {
						$status = $otherNode->get('id', $item['sender']);
					}

					if ($status == 'done') {
						$tvars['tvar_winContent'].= '<div><div class="cell">' . $d13->getLangUI($action) . ' ' . $d13->getLangUI("combat") . '</div><div class="cell">"' . $otherNode->data['name'] . '"</div><div class="cell"><span id="combat_' . $item['id'] . '">[' . implode(':', misc::sToHMS($remaining)) . ']</span><script type="text/javascript">timedJump("combat_' . $item['id'] . '", "?p=node&action=get&nodeId=' . $node->data['id'] . '");</script></div>' . $cancel . '</div>';
					}
				}

				$tvars['tvar_getHTMLCombat'] = $d13->templateParse($d13->templateGet("sub.queue") , $tvars);
			}

			// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		}

		$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));
		$page = "node.get";
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = SET NODE

	case 'set':
		if (isset($node->data['id'])) {
			if ($node->checkOptions('nodeRemove')) {
				$costData = '';
				foreach($d13->getGeneral('factions', $node->data['faction'], 'costs') ['set'] as $key => $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
				}

				$selectedFocus = array(
					'hp' => '',
					'damage' => '',
					'armor' => ''
				);
				$selectedFocus[$node->data['focus']] = ' selected';
				$tvars['tvar_costData'] = $costData;
				$tvars['tvar_selFocusHP'] = $selectedFocus['hp'];
				$tvars['tvar_selFocusDamage'] = $selectedFocus['damage'];
				$tvars['tvar_selFocusArmor'] = $selectedFocus['armor'];
				$page = "node.set";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD NODE

	case 'add':
		$tvars['tvar_factionDescriptions'] = "";
		$tvars['tvar_factionOptions'] = "";
		foreach($d13->getLangGL('factions') as $key => $faction) {
			$tvars['tvar_factionOptions'].= '<option value="' . $key . '">' . $faction['name'] . '</option>';
		}

		foreach($d13->getLangGL('factions') as $key => $faction) {
			$descriptions[$key] = '"' . $faction['description'] . '"';
		}

		$tvars['tvar_factionText'] = $descriptions[0];
		$tvars['tvar_factionDescriptions'].= implode(', ', $descriptions);
		$page = "node.add";
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = ADD RANDOM NODE

	case 'random':
		$tvars['tvar_getHTMLFactions'] = "";
		$tvars['tvar_factionName'] = "";
		$tvars['tvar_factionText'] = "";
		$tvars['tvar_factionID'] = - 1;
		$factionId = - 1;

		// - - - - Check for Faction Fixation

		if ($d13->getGeneral('options', 'factionFixation')) {
			$nodes = node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
			$t = count($nodes);
			if ($t > 0) {
				$factionId = $nodes[0]->data['faction'];
			}
		}

		// - - - - Add Factions to Swiper Slide

		foreach($d13->getGeneral('factions') as $faction) {
			if ($faction['active']) {
				if (!$d13->getGeneral('options', 'factionFixation') || ($d13->getGeneral('options', 'factionFixation') && $faction['id'] == $factionId) || ($d13->getGeneral('options', 'factionFixation') && $factionId == - 1)) {
					$tvars['tvar_factionName'] = $d13->getLangGL('factions', $faction['id'], 'name');
					$tvars['tvar_factionText'] = $d13->getLangGL('factions', $faction['id'], 'description');
					$tvars['tvar_factionID'] = $faction['id'];
					$tvars['tvar_getHTMLFactions'].= $d13->templateParse($d13->templateGet("sub.node.faction") , $tvars);
				}
			}
		}

		$d13->templateInject($d13->templateParse($d13->templateGet("sub.swiper.horizontal") , $tvars));
		$page = "node.random";
		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = REMOVE NODE

	case 'remove':
		if (isset($node->data['id'])) {
			if ($node->checkOptions('nodeRemove')) {
				$page = "node.remove";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = MOVE NODE

	case 'move':
		if (isset($node->data['id'])) {
			if ($node->checkOptions('nodeMove')) {
				$costData = '';
				foreach($d13->getGeneral('factions', $node->data['faction'], 'costs') ['move'] as $key => $cost) {
					$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
				}
				$node->getLocation();
				$tvars['tvar_nodeX'] = $node->location['x'];
				$tvars['tvar_nodeY'] = $node->location['y'];
				$tvars['tvar_costData'] = $costData;
				$page = "node.move";
			}
		}

		break;

		// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = LIST TOWNS

	case 'list':
		$tvars['tvar_nodeList'] = "";
		$t = count($nodes);

		// - - - - - - - - - - - - - - - - - - - - 0 towns - create new town

		if ($t == 0) {
			if ($d13->getGeneral('options', 'gridSystem') == 1) {
				header("location: index.php?p=node&action=add");
			}
			else {
				header("location: index.php?p=node&action=random");
			}

			// - - - - - - - - - - - - - - - - - - - - 1 town - jump to town

		}
		else
		if ($t == 1) {
			$link = "?p=node&action=get&nodeId=" . $nodes[0]->data['id'];
			header("location: " . $link);

			// - - - - - - - - - - - - - - - - - - - - 2+ towns - list all towns

		}
		else {
			foreach($nodes as $key => $node) {
				$tvars['tvar_nodeList'].= '<div><a class="external" href="index.php?p=node&action=get&nodeId=' . $node->data['id'] . '">' . $node->data['name'] . '</a></div>';
			}

			$page = "node.list";
		}

		break;
	}
}

// ----------------------------------------------------------------------------------------
// RENDER OUTPUT
// ----------------------------------------------------------------------------------------

$d13->templateRender($page, $tvars, $node);

// =====================================================================================EOF

?>