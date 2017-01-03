<?php

// ========================================================================================
//
// SIMULATOR
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
//
// ----------------------------------------------------------------------------------------

global $d13;
$message = NULL;

if (isset($_POST['attackerGroupUnitIds'], $_POST['defenderGroupUnitIds'])) {
	foreach($_POST as $key => $value)
	if (!in_array($key, array(
		'attackerGroupUnitIds',
		'defenderGroupUnitIds',
		'attackerGroups',
		'defenderGroups',
		'attackerFocus',
		'defenderFocus'
	))) $_POST[$key] = misc::clean($value, 'numeric');
	else
	if (!in_array($key, array(
		'attackerFocus',
		'defenderFocus'
	))) {
		$nr = count($_POST[$key]);
		for ($i = 0; $i < $nr; $i++) $_POST[$key][$i] = misc::clean($_POST[$key][$i], 'numeric');
	}
	else $_POST[$key] = misc::clean($value);
	$data = array();
	$data['input']['attacker']['focus'] = $_POST['attackerFocus'];
	$data['input']['attacker']['faction'] = $_POST['attackerFaction'];
	foreach($_POST['attackerGroupUnitIds'] as $key => $unitId) $data['input']['attacker']['groups'][$key] = array(
		'unitId' => $unitId,
		'quantity' => $_POST['attackerGroups'][$key]
	);
	$data['input']['defender']['focus'] = $_POST['defenderFocus'];
	$data['input']['defender']['faction'] = $_POST['defenderFaction'];
	foreach($_POST['defenderGroupUnitIds'] as $key => $unitId) $data['input']['defender']['groups'][$key] = array(
		'unitId' => $unitId,
		'quantity' => $_POST['defenderGroups'][$key]
	);
	$data = node::doCombat($data);
}

// ----------------------------------------------------------------------------------------
// Setup Template Variables
// ----------------------------------------------------------------------------------------

$tvars = array();
$tvars['tvar_global_message'] = $message;
$tvars['tvar_attackerArray'] = "";
$tvars['tvar_defenderArray'] = "";
$tvars['tvar_focusData'] = "";

// - - - -

if (isset($_POST['attackerGroupUnitIds'], $_POST['defenderGroupUnitIds'])) {
	$attackerData = array();
	$defenderData = array();
	foreach($_POST['attackerGroupUnitIds'] as $key => $unitId) {
		$attackerData[$key] = 'new Group(' . $unitId . ', ' . $_POST['attackerGroups'][$key] . ')';
		$attackerData = implode(', ', $attackerData);
		$tvars['tvar_attackerArray'] = 'attacker=new Array(' . $attackerData . ');';
	}

	foreach($_POST['defenderGroupUnitIds'] as $key => $unitId) {
		$defenderData[$key] = 'new Group(' . $unitId . ', ' . $_POST['defenderGroups'][$key] . ')';
		$defenderData = implode(', ', $defenderData);
		$tvars['tvar_defenderArray'] = 'defender=new Array(' . $defenderData . ');';
	}
}

// - - - -

$units = array();

foreach($d13->getLangGL('factions') as $fkey => $faction) {
	$units[$fkey] = '"';
	foreach($d13->getLangGL('units', $fkey) as $ukey => $unit) $units[$fkey].= '<option value=\'' . $ukey . '\'>' . $unit['name'] . '</option>';
	$units[$fkey].= '"';
}

$tvars['tvar_units'] = 'var units=new Array(' . implode(', ', $units) . ');';

// - - - -

$factions = '';

foreach($d13->getLangGL('factions') as $key => $faction) {
	$factions.= '<option value="' . $key . '">' . $faction['name'] . '</option>';
}

$tvars['tvar_factions'] = $factions;
$attacker = array(
	'output' => '',
	'outcome' => ''
);
$defender = array(
	'output' => '',
	'outcome' => ''
);

if (isset($data['output'])) {
	$showOutput = ' style="display: table;"';
	foreach($data['output']['attacker']['groups'] as $group) $attacker['output'].= '<div class="cell" style="text-align: center;"><div class="unitBlock">' . $group['quantity'] . '</div></div>';
	if ($data['output']['attacker']['winner']) $attacker['outcome'] = $d13->getLangUI('won');
	else $attacker['outcome'] = $d13->getLangUI('lost');
	foreach($data['output']['defender']['groups'] as $group) $defender['output'].= '<div class="cell" style="text-align: center;"><div class="unitBlock">' . $group['quantity'] . '</div></div>';
	if ($data['output']['defender']['winner']) $defender['outcome'] = $d13->getLangUI('won');
	else $defender['outcome'] = $d13->getLangUI('lost');
}
else $tvars['tvar_showOutput'] = ' style="display: none;"';
$tvars['tvar_attacker_output'] = $attacker['output'];
$tvars['tvar_attacker_outcome'] = $attacker['outcome'];
$tvars['tvar_defender_output'] = $defender['output'];
$tvars['tvar_defender_outcome'] = $defender['outcome'];

if (isset($_POST['attackerFaction'], $_POST['defenderFaction'], $_POST['attackerFocus'], $_POST['defenderFocus'])) $tvars['tvar_focusData'] = '
  var focusTypes=new Array("hp", "damage", "armor");
  document.getElementById("attackerFocus").selectedIndex=focusTypes.indexOf("' . $_POST['attackerFocus'] . '");
  document.getElementById("defenderFocus").selectedIndex=focusTypes.indexOf("' . $_POST['defenderFocus'] . '");
  document.getElementById("attackerFaction").selectedIndex=' . $_POST['attackerFaction'] . ';
  document.getElementById("defenderFaction").selectedIndex=' . $_POST['defenderFaction'] . ';
 ';

// ----------------------------------------------------------------------------------------
// Parse & Render Template
// ----------------------------------------------------------------------------------------

$d13->templateRender("simulator", $tvars);

// =====================================================================================EOF

?>