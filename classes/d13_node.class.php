<?php

// ========================================================================================
//
// NODE.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo (soon!).........: https://github.com/Fhizbang/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class node

{
	public $data, $resources, $production, $storage, $technologies, $modules, $components, $queue = array(

		'research',
		'build',
		'craft',
		'train',
		'trade'
	);

	// ----------------------------------------------------------------------------------------
	// GET
	// ----------------------------------------------------------------------------------------

	public

	function get($idType, $id)
	{
		global $d13;
		$result = $d13->dbQuery('select * from nodes where ' . $idType . '="' . $id . '"');
		$this->data = $d13->dbFetch($result);
		if (isset($this->data['id'])) {
			$status = 'done';
		}
		else {
			$status = 'noNode';
		}

		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// SET
	// ----------------------------------------------------------------------------------------

	public

	function set()
	{
		global $d13;
		$this->getResources();
		$setCost = $d13->getGeneral('factions', $this->data['faction'], 'costs') ['set'];
		$setCostData = $this->checkCost($setCost, 'set');
		if ($setCostData['ok']) {
			$node = new node();
			if ($node->get('id', $this->data['id']) == 'done')
			if (($node->data['name'] == $this->data['name']) || ($node->get('name', $this->data['name']) == 'noNode')) {
				$ok = 1;
				foreach($setCost as $cost) {
					$this->resources[$cost['resource']]['value']-= $cost['value'] * $d13->getGeneral('users', 'cost', 'set');
					$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}

				$d13->dbQuery('update nodes set name="' . $this->data['name'] . '", focus="' . $this->data['focus'] . '" where id="' . $this->data['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				if ($ok) $status = 'done';
				else $status = 'error';
			}
			else $status = 'nameTaken';
			else $status = 'noNode';
		}
		else $status = 'notEnoughResources';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// ADD
	// ----------------------------------------------------------------------------------------

	public

	function add($userId)
	{
		global $d13;
		$sector = grid::getSector($this->location['x'], $this->location['y']);
		$node = new node();
		$status = 0;
		if ($sector['type'] == 1) {
			if ($node->get('name', $this->data['name']) == 'noNode') {
				$nodes = node::getList($userId);
				if (count($nodes) < $d13->getGeneral('users', 'maxNodes')) {
					$ok = 1;
					$this->data['id'] = misc::newId('nodes');
					
					$d13->dbQuery('insert into nodes (id, faction, user, name, focus, lastCheck) values ("' . $this->data['id'] . '", "' . $this->data['faction'] . '", "' . $this->data['user'] . '", "' . $this->data['name'] . '", "hp", now())');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					$d13->dbQuery('update grid set type="2", id="' . $this->data['id'] . '" where x="' . $this->location['x'] . '" and y="' . $this->location['y'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					
					$query = array();
					$nr = count($d13->getResource());
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $d13->getResourceByID($i, 'id') . '", "' . $d13->getResource($i, 'storage') . '")';
					}
					$d13->dbQuery('insert into resources (node, id, value) values ' . implode(', ', $query));
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					
					$query = array();
					$nr = count($d13->getTechnology($this->data['faction']));
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $d13->getTechnology($this->data['faction'], $i, 'id') . '", "0")';
					}
					$d13->dbQuery('insert into technologies (node, id, level) values ' . implode(', ', $query));
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					
					$query = array();
					$nr = count($d13->getComponent($this->data['faction']));
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $d13->getComponent($this->data['faction'], $i, 'id') . '", "0")';
					}
					$d13->dbQuery('insert into components (node, id, value) values ' . implode(', ', $query));
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					
					$query = array();
					$nr = count($d13->getUnit($this->data['faction']));
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $d13->getUnit($this->data['faction'], $i, 'id') . '", "0", "1")';
					}
					$d13->dbQuery('insert into units (node, id, value, level) values ' . implode(', ', $query));
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					
					$query = array();
					for ($i = 0; $i < $d13->getGeneral('users', 'maxModules') * $d13->getGeneral('users', 'maxSectors'); $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $i . '", "-1", "0", "0")';
					}

					$d13->dbQuery('insert into modules (node, slot, module, input, level) values ' . implode(', ', $query));
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					if ($ok) {
						$status = "done";
					}
					else {
						$status = 'error';
					}
				}
				else {
					$status = 'maxNodesReached';
				}
			}
			else {
				$status = 'nameTaken';
			}
		}
		else {
			$status = 'invalidGridSector';
		}

		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function remove($id)
	{
		global $d13;
		$node = new node();
		if ($node->get('id', $id) == 'done') {
			$ok = 1;
			$node->getLocation();
			$d13->dbQuery('delete from research where node="' . $id . '"');
			$d13->dbQuery('delete from build where node="' . $id . '"');
			$d13->dbQuery('delete from craft where node="' . $id . '"');
			$d13->dbQuery('delete from train where node="' . $id . '"');
			$d13->dbQuery('delete from trade where node="' . $id . '"');
			$d13->dbQuery('delete from combat_units where combat in (select id from combat where sender="' . $id . '" or recipient="' . $id . '")');
			$d13->dbQuery('delete from combat where sender="' . $id . '" or recipient="' . $id . '"');
			$d13->dbQuery('delete from resources where node="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from technologies where node="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from modules where node="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from components where node="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from units where node="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "nodes")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from nodes where id="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('update grid set type="1", id=floor(1+rand()*9) where x="' . $node->location['x'] . '" and y="' . $node->location['y'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = "done";
			else $status = 'error';
		}
		else $status = 'noNode';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function getList($userId, $otherNode = FALSE)
	{
		global $d13;
		if ($otherNode) {
			$result = $d13->dbQuery('select * from nodes where user != "' . $userId . '"');
		}
		else {
			$result = $d13->dbQuery('select * from nodes where user = "' . $userId . '"');
		}

		$nodes = array();
		for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$nodes[$i] = new node();
			$nodes[$i]->data = $row;
		}

		return $nodes;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getLocation()
	{
		global $d13;
		$result = $d13->dbQuery('select x, y from grid where type="2" and id="' . $this->data['id'] . '"');
		$row = $d13->dbFetch($result);
		if (isset($row['x'])) {
			$this->location = $row;
			$status = 'done';
		}
		else $status = 'noNode';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// getResources
	// ----------------------------------------------------------------------------------------

	public

	function getResources()
	{
		global $d13;
		$this->resources = array();
		$this->production = array();
		$this->storage = array();
		$tmp_resources = array();
		$result = $d13->dbQuery('select * from resources where node="' . $this->data['id'] . '" order by id asc');
		for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$tmp_resources[$i] = $row;
		}

		foreach($d13->getAllResources() as $resource) {
			$this->production[$resource['id']] = 0;
			$this->production[$resource['id']] += $d13->getResourceByID($resource['id'], 'autoproduction');
			$this->storage[$resource['id']] = $d13->getResourceByID($resource['id'], 'storage');
			$this->resources[$resource['id']] = $tmp_resources[$resource['id']];
		}

		if ($this->modules) {
			foreach($this->modules as $module) {
				if ($module['module'] > - 1) {
					$tmp_module = d13_module_factory::create($module['module'], $module['slot'], $this);
					if ($d13->getModule($this->data['faction'], $module['module'], 'storedResource')) {
						foreach($d13->getModule($this->data['faction'], $module['module'], 'storedResource') as $res) {
							$this->storage[$res]+= $tmp_module->data['ratio'] * $d13->getGeneral('factors', 'storage') * $module['input'];
						}
					}

					if ($d13->getModule($this->data['faction'], $module['module'], 'outputResource')) {
						foreach($d13->getModule($this->data['faction'], $module['module'], 'outputResource') as $res) {
							$this->production[$res]+= $tmp_module->data['ratio'] * $d13->getGeneral('factors', 'production') * $module['input'];
						}
					}
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getTechnologies()
	{
		global $d13;
		$this->technologies = array();
		$result = $d13->dbQuery('select * from technologies where node="' . $this->data['id'] . '" order by id asc');
		for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->technologies[$i] = $row;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getModules()
	{
		global $d13;
		$this->modules = array();
		$result = $d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" order by slot asc');
		while ($row = $d13->dbFetch($result)) $this->modules[$row['slot']] = $row;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getComponents()
	{
		global $d13;
		$this->components = array();
		$result = $d13->dbQuery('select * from components where node="' . $this->data['id'] . '" order by id asc');
		for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->components[$i] = $row;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getUnits()
	{
		global $d13;
		$this->units = array();
		$result = $d13->dbQuery('select * from units where node="' . $this->data['id'] . '" order by id asc');
		while ($row = $d13->dbFetch($result)) $this->units[$row['id']] = $row;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getAll()
	{
		$this->getLocation();
		$this->getResources();
		$this->getTechnologies();
		$this->getModules();
		$this->getComponents();
		$this->getUnits();
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getQueue($type, $field = 0, $values = 0)
	{
		global $d13;
		$this->queue[$type] = array();
		switch ($type) {
		case 'combat':
			$result = $d13->dbQuery('select * from ' . $type . ' where sender="' . $this->data['id'] . '" or recipient="' . $this->data['id'] . '" order by start asc');
			break;

		default:
			if ($field) {
				$values = '(' . implode(', ', $values) . ')';
				$result = $d13->dbQuery('select * from ' . $type . ' where node="' . $this->data['id'] . '" and ' . $field . ' in ' . $values . ' order by start asc');
			}
			else $result = $d13->dbQuery('select * from ' . $type . ' where node="' . $this->data['id'] . '" order by start asc');
			break;
		}

		for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$this->queue[$type][$i] = $row;
			$this->queue[$type][$i]['start'] = strtotime($this->queue[$type][$i]['start']);
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function addTechnology($technologyId, $slotId)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getTechnologies();
		$this->getComponents();
		$technology = array();
		if (isset($this->technologies[$technologyId])) {
			$okModule = 0;
			if (isset($this->modules[$slotId]['module']))
			if (in_array($technologyId, $d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'technologies'))) $okModule = 1;
			if ($okModule)
			if ($this->technologies[$technologyId]['level'] < $d13->getTechnology($this->data['faction'], $technologyId,'maxLevel')) {
				$result = $d13->dbQuery('select count(*) as count from research where node="' . $this->data['id'] . '" and technology="' . $technologyId . '"');
				$row = $d13->dbFetch($result);
				if (!$row['count']) {
					$technology['requirementsData'] = $this->checkRequirements($d13->getTechnology($this->data['faction'],$technologyId,'requirements'));
					if ($technology['requirementsData']['ok']) {
						$technology['costData'] = $this->checkCost($d13->getTechnology($this->data['faction'],$technologyId,'cost'), 'research');
						if ($technology['costData']['ok']) {
							$ok = 1;
							foreach($d13->getTechnology($this->data['faction'], $technologyId, 'cost') as $cost) {
								$this->resources[$cost['resource']]['value']-= $cost['value'] * $d13->getGeneral('users', 'cost', 'research');
								$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
							}

							foreach($d13->getTechnology($this->data['faction'], $technologyId, 'requirements') as $requirement)
							if ($requirement['type'] == 'components') {
								$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
								$this->resources[$storageResource]['value']+= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
								$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']-= $requirement['value'];
								$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
							}

							$this->getQueue('research', 'technology', $d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'technologies'));
							$lastResearch = count($this->queue['research']) - 1;
							if ($lastResearch > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['research'][$lastResearch]['start'] + floor($this->queue['research'][$lastResearch]['duration'] * 60));
							else $start = strftime('%Y-%m-%d %H:%M:%S', time());
							$totalIR = 0;
							foreach($this->modules as $key => $item)
							if ($item['module'] == $this->modules[$slotId]['module']) $totalIR+= $item['input'] * $d13->getModule($this->data['faction'], $item['module'], 'ratio');
							$duration = $d13->getTechnology($this->data['faction'],$technologyId,'duration');
							$duration = ($duration - $duration * $totalIR) * $d13->getGeneral('users', 'speed', 'research');
							$d13->dbQuery('insert into research (node, technology, start, duration, slot) values ("' . $this->data['id'] . '", "' . $technologyId . '", "' . $start . '", "' . $duration . '", "' . $slotId . '")');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							if ($ok) $status = 'done';
							else $status = 'error';
						}
						else $status = 'notEnoughResources';
					}
					else $status = 'requirementsNotMet';
				}
				else $status = 'technologyBusy';
			}
			else $status = 'maxTechnologyTierMet';
			else $status = 'requirementsNotMet';
		}
		else $status = 'noTechnology';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function cancelTechnology($technologyId, $moduleId)
	{
		global $d13;
		$this->getResources();
		$this->getComponents();
		$result = $d13->dbQuery('select * from research where node="' . $this->data['id'] . '" and technology="' . $technologyId . '"');
		$entry = $d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			foreach($d13->getTechnology($this->data['faction'], $entry['technology'], 'cost') as $cost) {
				$this->resources[$cost['resource']]['value']+= $cost['value'] * $d13->getGeneral('users', 'cost', 'research');
				$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			foreach($d13->getTechnology($this->data['faction'], $entry['technology'], 'requirements') as $requirement)
			if ($requirement['type'] == 'components') {
				$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
				$this->resources[$storageResource]['value']-= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
				$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$this->components[$requirement['id']]['value']+= $requirement['value'];
				$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->getQueue('research', 'technology', $d13->getModule($this->data['faction'], $moduleId) ['technologies']);
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queue['research'] as $queueEntry)
			if ($queueEntry['start'] > $entry['start']) {
				$d13->dbQuery('update research set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where node="' . $this->data['id'] . '" and technology="' . $queueEntry['technology'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$d13->dbQuery('delete from research where node="' . $this->data['id'] . '" and technology="' . $technologyId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// setModule
	// ----------------------------------------------------------------------------------------

	public

	function setModule($slotId, $input)
	{
		global $d13;
		$this->getResources();
		$result = $d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $d13->dbFetch($result);
		if (isset($module['module'])) {
		if ($module['module'] > - 1) {
			$tmp_module = d13_module_factory::create($module['module'], $slotId, $this);
			$result = $d13->dbQuery('select * from resources where node="' . $this->data['id'] . '" and id="' . $tmp_module->data['inputResource'] . '"');
			$resource = $d13->dbFetch($result);
			if ($resource['value'] + $module['input'] >= $this->modules[$slotId]['input'])
			if ($this->modules[$slotId]['input'] <= $tmp_module->data['maxInput']) //$d13->getModule($this->data['faction'], $module['module'])['maxInput'])
			{
				$ok = 1;
				$this->resources[$resource['id']]['value']+= $module['input'] - $input;
				$d13->dbQuery('update resources set value="' . $this->resources[$resource['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $resource['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$d13->dbQuery('update modules set input="' . $input . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$this->checkModuleDependencies($module['module'], $slotId, 1);
				if ($ok) $status = 'done';
				else $status = 'error';
			}
			else $status = 'maxInputExceeded';
			else $status = 'notEnoughResources';
		} else {
		$status = 'emptySlot';
		}
		} else {
			$status = 'noSlot';
		}
		
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getModuleCount($slotId, $moduleId)
	{
		global $d13;
		$result = $d13->dbQuery('select count(*) as count from modules where node="' . $this->data['id'] . '" and module="' . $moduleId . '"');
		$row = $d13->dbFetch($result);
		return $row['count'];
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function addModule($slotId, $moduleId, $input=1)
	{
		global $d13;
		
		$result = $d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $d13->dbFetch($result);
		if (isset($module['module']))
		if ($module['module'] == - 1) {
			$result = $d13->dbQuery('select count(*) as count from modules where node="' . $this->data['id'] . '" and module="' . $moduleId . '"');
			$row = $d13->dbFetch($result);
			if ($row['count'] < $d13->getModule($this->data['faction'], $moduleId) ['maxInstances']) {
				$result = $d13->dbQuery('select count(*) as count from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
				$row = $d13->dbFetch($result);
				if (!$row['count']) {
					$this->getModules();
					$this->getResources();
					$this->getTechnologies();
					$this->getComponents();
					$module['requirementsData'] = $this->checkRequirements($d13->getModule($this->data['faction'], $moduleId, 'requirements'));
					if ($module['requirementsData']['ok']) {
						$module['costData'] = $this->checkCost($d13->getModule($this->data['faction'], $moduleId, 'cost'), 'build');
						if ($module['costData']['ok']) {
							$ok = 1;
							$tmp_module = d13_module_factory::create($moduleId, $slotId, $this);
							
							foreach($tmp_module->data['cost'] as $cost) {
								$this->resources[$cost['resource']]['value'] -= $cost['value'] * $d13->getGeneral('users', 'cost', 'build');
								$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
							}

							foreach($d13->getModule($this->data['faction'], $moduleId) ['requirements'] as $requirement)
							if ($requirement['type'] == 'components') {
								$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
								$this->resources[$storageResource]['value']+= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
								$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']-= $requirement['value'];
								$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
							}
							
							$this->resources[$tmp_module->data['inputResource']]['value'] -= $input;
							$d13->dbQuery('update resources set value="' . $this->resources[$tmp_module->data['inputResource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $tmp_module->data['inputResource'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							$d13->dbQuery('update modules set input=input+"' . $input . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							
							$this->getQueue('build');
							#$lastBuild = count($this->queue['build']) - 1;
							#if ($lastBuild > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['build'][$lastBuild]['start'] + floor($this->queue['build'][$lastBuild]['duration'] * 60));
							#else $start = strftime('%Y-%m-%d %H:%M:%S', time());
							$start = strftime('%Y-%m-%d %H:%M:%S', time());
							$duration = ceil(($tmp_module->data['duration'] * $d13->getGeneral('users', 'speed', 'build')) / $input);
							

							$d13->dbQuery('insert into build (node, slot, module, start, duration, action) values ("' . $this->data['id'] . '", "' . $slotId . '", "' . $moduleId . '", "' . $start . '", "' . $duration . '", "build")');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							if ($ok) {
								$status = 'done';
							} else {
								$status = 'error';
							}
							
						}
						else $status = 'notEnoughResources';
					}
					else $status = 'requirementsNotMet';
				}
				else $status = 'slotBusy';
			}
			else $status = 'maxModuleInstancesMet';
		}
		else $status = 'notEmptySlot';
		else $status = 'noSlot';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// upgradeModule
	// ----------------------------------------------------------------------------------------

	public

	function upgradeModule($slotId, $moduleId, $input=1)
	{
		global $d13;
		$result = $d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $d13->dbFetch($result);
		if (isset($module['module']))
		if ($module['module'] > - 1) {
			$result = $d13->dbQuery('select count(*) as count from modules where node="' . $this->data['id'] . '" and module="' . $moduleId . '"');
			$row = $d13->dbFetch($result);
			$result = $d13->dbQuery('select count(*) as count from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			$row = $d13->dbFetch($result);
			if (!$row['count']) {
				$this->getModules();
				$this->getResources();
				$this->getTechnologies();
				$this->getComponents();
				$tmp_module = d13_module_factory::create($moduleId, $slotId, $this);
				$module['requirementsData'] = $this->checkRequirements($d13->getModule($this->data['faction'], $moduleId, 'requirements'));
				if ($module['requirementsData']['ok']) {
					$module['costData'] = $this->checkCost($d13->getModule($this->data['faction'], $moduleId, 'cost'), 'build');
					if ($module['costData']['ok']) {
						$ok = 1;
						$tmp_module = d13_module_factory::create($moduleId, $slotId, $this);
						
						foreach($tmp_module->data['cost'] as $cost) {
							$this->resources[$cost['resource']]['value']-= $cost['value'] * $d13->getGeneral('users', 'cost', 'build');
							$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}

						foreach($d13->getModule($this->data['faction'], $moduleId) ['requirements'] as $requirement)
						if ($requirement['type'] == 'components') {
							$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$this->resources[$storageResource]['value']+= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
							$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							$this->components[$requirement['id']]['value']-= $requirement['value'];
							$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}
						
						
						if ($input != $this->modules[$slotId]['input']) {
								
							$status = $this->setModule($slotId, 0);
							if ($status == 'done') {
								$status = $this->setModule($slotId, $input);
							}
							
							#$this->resources[$tmp_module->data['inputResource']]['value'] += ($this->modules[$slotId]['input'] - $input);
							#$d13->dbQuery('update resources set value="' . $this->resources[$tmp_module->data['inputResource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $tmp_module->data['inputResource'] . '"');
							#if ($d13->dbAffectedRows() == - 1) $ok = 0;
							#$d13->dbQuery('update modules set input=input-"' . ($this->modules[$slotId]['input'] - $input) . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
							#if ($d13->dbAffectedRows() == - 1) $ok = 0;
						
						}

						$start = strftime('%Y-%m-%d %H:%M:%S', time());
						$duration = ceil(($tmp_module->data['duration'] * $d13->getGeneral('users', 'speed', 'build')) / $input);
						
						$d13->dbQuery('insert into build (node, slot, module, start, duration, action) values ("' . $this->data['id'] . '", "' . $slotId . '", "' . $moduleId . '", "' . $start . '", "' . $duration . '", "upgrade")');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
						if ($ok) $status = 'done';
						else $status = 'error';
					}
					else $status = 'notEnoughResources';
				}
				else $status = 'requirementsNotMet';
			}
			else $status = 'slotBusy';
		}
		else $status = 'notEmptySlot';
		else $status = 'noSlot';
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function cancelModule($slotId)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$result = $d13->dbQuery('select * from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$entry = $d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			$tmp_module = d13_module_factory::create($entry['module'], $slotId, $this);
			
			if ($this->modules[$slotId == - 1]) {
			
				foreach($tmp_module->data['cost'] as $cost) {
					$this->resources[$cost['resource']]['value']+= $cost['value'] * $d13->getGeneral('users', 'cost', 'build');
					$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}

				foreach($d13->getModule($this->data['faction'], $entry['module'], 'requirements') as $requirement)
				if ($requirement['type'] == 'components') {
					$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
					$this->resources[$storageResource]['value']-= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
					$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					$this->components[$requirement['id']]['value']+= $requirement['value'];
					$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}
			
			$input = $tmp_module->data['input'];
			$this->resources[$tmp_module->data['inputResource']]['value'] -= $input;
			$d13->dbQuery('update resources set value=value+"' . $this->resources[$tmp_module->data['inputResource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $tmp_module->data['inputResource'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('update modules set input=input-"' . $input . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;

			
			$this->getQueue('build');
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queue['build'] as $queueEntry) {
				if ($queueEntry['start'] > $entry['start']) {
					$d13->dbQuery('update build set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where node="' . $this->data['id'] . '" and slot="' . $queueEntry['slot'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}

			$d13->dbQuery('delete from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function removeModule($slotId)
	{
		global $d13;
		$result = $d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $d13->dbFetch($result);
		if (isset($module['module']))
		if ($module['module'] > - 1) {
			$result = $d13->dbQuery('select count(*) as count from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			$row = $d13->dbFetch($result);
			if (!$row['count']) {
				$this->getQueue('build');
				$lastBuild = count($this->queue['build']) - 1;
				if ($lastBuild > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['build'][$lastBuild]['start'] + floor($this->queue['build'][$lastBuild]['duration'] * 60));
				else $start = strftime('%Y-%m-%d %H:%M:%S', time());
				$ok = 1;
				$d13->dbQuery('insert into build (node, slot, module, start, duration, action) values ("' . $this->data['id'] . '", "' . $slotId . '", "' . $module['module'] . '", "' . $start . '", "' . ($d13->getModule($this->data['faction'], $module['module'], 'removeDuration') * $d13->getGeneral('users', 'speed', 'build')) . '", "remove")');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				if ($ok) $status = 'done';
				else $status = 'error';
			}
			else $status = 'slotBusy';
		}
		else $status = 'emptySlot';
		else $status = 'noSlot';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function addComponent($componentId, $quantity, $slotId)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getTechnologies();
		$this->getComponents();
		$component = array();
		if (isset($this->components[$componentId])) {
			$okModule = 0;
			if (isset($this->modules[$slotId]['module']))
			if (in_array($componentId, $d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'components'))) $okModule = 1;
			if ($okModule) {
				$component['requirementsData'] = $this->checkRequirements($d13->getComponent($this->data['faction'], $componentId, 'requirements') , $quantity);
				if ($component['requirementsData']['ok'])
				if ($this->resources[$d13->getComponent($this->data['faction'], $componentId, 'storageResource') ]['value'] >= $d13->getComponent($this->data['faction'], $componentId, 'storage') * $quantity) {
					$component['costData'] = $this->checkCost($d13->getComponent($this->data['faction'], $componentId, 'cost') , 'craft', $quantity);
					if ($component['costData']['ok']) {
						$ok = 1;
						$storageResource = $d13->getComponent($this->data['faction'], $componentId, 'storageResource');
						$this->resources[$storageResource]['value']-= $d13->getComponent($this->data['faction'], $componentId, 'storage') * $quantity;
						$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
						foreach($d13->getComponent($this->data['faction'], $componentId, 'cost') as $cost) {
							$this->resources[$cost['resource']]['value']-= $cost['value'] * $quantity * $d13->getGeneral('users', 'cost', 'craft');
							$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}

						foreach($d13->getComponent($this->data['faction'], $componentId, 'requirements') as $requirement)
						if ($requirement['type'] == 'components') {
							$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$this->resources[$storageResource]['value']+= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $quantity;
							$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							$this->components[$requirement['id']]['value']-= $requirement['value'] * $quantity;
							$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}

						$this->getQueue('craft', 'component', $d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'components'));
						$lastCraft = count($this->queue['craft']) - 1;
						if ($lastCraft > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['craft'][$lastCraft]['start'] + floor($this->queue['craft'][$lastCraft]['duration'] * 60));
						else $start = strftime('%Y-%m-%d %H:%M:%S', time());
						$totalIR = 0;
						foreach($this->modules as $key => $item)
						if ($item['module'] == $this->modules[$slotId]['module']) $totalIR+= $item['input'] * $d13->getModule($this->data['faction'], $item['module'], 'ratio');
						$duration = $d13->getComponent($this->data['faction'], $componentId, 'duration') * $quantity;
						$duration = ($duration - $duration * $totalIR) * $d13->getGeneral('users', 'speed', 'craft');
						$result = $d13->dbQuery('select max(id) as id from craft where node="' . $this->data['id'] . '"');
						$row = $d13->dbFetch($result);
						$id = $row['id'] + 1;
						$d13->dbQuery('insert into craft (id, node, component, quantity, stage, start, duration, slot) values ("' . $id . '", "' . $this->data['id'] . '", "' . $componentId . '", "' . $quantity . '", 0, "' . $start . '", "' . $duration . '", "' . $slotId . '")');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
						if ($ok) $status = 'done';
						else $status = 'error';
					}
					else $status = 'notEnoughResources';
				}
				else $status = 'notEnoughStorageResource';
				else $status = 'requirementsNotMet';
			}
			else $status = 'requirementsNotMet';
		}
		else $status = 'noComponent';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function removeComponent($componentId, $quantity, $moduleId)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$component = array();
		if (isset($this->components[$componentId]))
		if ($this->components[$componentId]['value'] >= $quantity) {
			$ok = 1;
			$storageResource = $d13->getComponent($this->data['faction'], $componentId, 'storageResource');
			$this->resources[$storageResource]['value']+= $d13->getComponent($this->data['faction'], $componentId, 'storage') * $quantity;
			$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$this->components[$componentId]['value']-= $quantity;
			$d13->dbQuery('update components set value="' . $this->components[$componentId]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $componentId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$this->getQueue('craft', 'component', $d13->getModule($this->data['faction'], $moduleId) ['components']);
			$lastCraft = count($this->queue['craft']) - 1;
			if ($lastCraft > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['craft'][$lastCraft]['start'] + floor($this->queue['craft'][$lastCraft]['duration'] * 60));
			else $start = strftime('%Y-%m-%d %H:%M:%S', time());
			$totalIR = 0;
			foreach($this->modules as $key => $item)
			if ($item['module'] == $moduleId) $totalIR+= $item['input'] * $d13->getModule($this->data['faction'], $item['module'], 'ratio');
			$duration = $d13->getComponent($this->data['faction'], $componentId, 'removeDuration') * $quantity;
			$duration = ($duration - $duration * $totalIR) * $d13->getGeneral('users', 'speed', 'craft');
			$result = $d13->dbQuery('select max(id) as id from craft where node="' . $this->data['id'] . '"');
			$row = $d13->dbFetch($result);
			$id = $row['id'] + 1;
			$d13->dbQuery('insert into craft (id, node, component, quantity, stage, start, duration) values ("' . $id . '", "' . $this->data['id'] . '", "' . $componentId . '", "' . $quantity . '", 1, "' . $start . '", "' . $duration . '")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'notEnoughComponents';
		else $status = 'noComponent';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function cancelComponent($craftId, $moduleId)
	{
		global $d13;
		$this->getResources();
		$this->getComponents();
		$result = $d13->dbQuery('select * from craft where id="' . $craftId . '"');
		$entry = $d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			$storageResource = $d13->getComponent($this->data['faction'], $entry['component'], 'storageResource');
			$storage = $d13->getComponent($this->data['faction'], $entry['component'], 'storage') * $entry['quantity'];
			if (!$entry['stage']) $this->resources[$storageResource]['value']+= $storage;
			else $this->resources[$storageResource]['value']-= $storage;
			$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if (!$entry['stage']) {
				foreach($d13->getComponent($this->data['faction'], $entry['component'], 'cost') as $cost) {
					$this->resources[$cost['resource']]['value']+= $cost['value'] * $entry['quantity'] * $d13->getGeneral('users', 'cost', 'craft');
					$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}

				foreach($d13->getComponent($this->data['faction'], $entry['component'], 'requirements') as $requirement)
				if ($requirement['type'] == 'components') {
					$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
					$this->resources[$storageResource]['value']-= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
					$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					$this->components[$requirement['id']]['value']+= $requirement['value'] * $entry['quantity'];
					$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}
			else {
				$this->components[$entry['component']]['value']+= $entry['quantity'];
				$d13->dbQuery('update components set value="' . $this->components[$entry['component']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['component'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->getQueue('craft', 'component', $d13->getModule($this->data['faction'], $moduleId) ['components']);
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queue['craft'] as $queueEntry) {
				if ($queueEntry['start'] > $entry['start']) {
					$d13->dbQuery('update craft set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where id="' . $queueEntry['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}

			$d13->dbQuery('delete from craft where id="' . $craftId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// addUnit
	// ----------------------------------------------------------------------------------------

	public

	function addUnit($unitId, $quantity, $slotId)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getTechnologies();
		$this->getComponents();
		$this->getUnits();
		$unit = array();
		if (isset($this->units[$unitId])) {
			$okModule = 0;
			if (isset($this->modules[$slotId]['module']))
			if (in_array($unitId, $d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'units'))) $okModule = 1;
			if ($okModule) {
				$unit['requirementsData'] = $this->checkRequirements($d13->getUnit($this->data['faction'], $unitId, 'requirements') , $quantity);
				if ($unit['requirementsData']['ok'])
				if ($this->resources[$d13->getUnit($this->data['faction'], $unitId, 'upkeepResource') ]['value'] >= $d13->getUnit($this->data['faction'], $unitId, 'upkeep') * $quantity) {
					$unit['costData'] = $this->checkCost($d13->getUnit($this->data['faction'], $unitId, 'cost') , 'train', $quantity);
					if ($unit['costData']['ok']) {
						$ok = 1;
						$upkeepResource = $d13->getUnit($this->data['faction'], $unitId, 'upkeepResource');
						$this->resources[$upkeepResource]['value'] -= $d13->getUnit($this->data['faction'], $unitId, 'upkeep') * $quantity;
						$d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
						foreach($d13->getUnit($this->data['faction'], $unitId, 'cost') as $cost) {
							$this->resources[$cost['resource']]['value']-= $cost['value'] * $quantity * $d13->getGeneral('users', 'cost', 'train');
							$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}

						foreach($d13->getUnit($this->data['faction'], $unitId, 'requirements') as $requirement)
						if ($requirement['type'] == 'components') {
							$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$this->resources[$storageResource]['value'] += $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $quantity;
							$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							$this->components[$requirement['id']]['value']-= $requirement['value'] * $quantity;
							$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}

						$this->getQueue('train', 'unit', $d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'units'));
						$lastTrain = count($this->queue['train']) - 1;
						if ($lastTrain > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['train'][$lastTrain]['start'] + floor($this->queue['train'][$lastTrain]['duration'] * 60));
						else $start = strftime('%Y-%m-%d %H:%M:%S', time());
						$totalIR = 0;
						foreach($this->modules as $key => $item)
						if ($item['module'] == $this->modules[$slotId]['module']) $totalIR+= $item['input'] * $d13->getModule($this->data['faction'], $item['module'], 'ratio');
						$duration = $d13->getUnit($this->data['faction'], $unitId, 'duration') * $quantity;
						$duration = ($duration - $duration * $totalIR) * $d13->getGeneral('users', 'speed', 'train');
						$result = $d13->dbQuery('select max(id) as id from train where node="' . $this->data['id'] . '"');
						$row = $d13->dbFetch($result);
						$id = $row['id'] + 1;
						$d13->dbQuery('insert into train (id, node, unit, quantity, stage, start, duration, slot) values ("' . $id . '", "' . $this->data['id'] . '", "' . $unitId . '", "' . $quantity . '", 0, "' . $start . '", "' . $duration . '", "' . $slotId . '")');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
						if ($ok) $status = 'done';
						else $status = 'error';
					}
					else $status = 'notEnoughResources';
				}
				else $status = 'notEnoughUpkeepResource';
				else $status = 'requirementsNotMet';
			}
			else $status = 'requirementsNotMet';
		}
		else $status = 'noUnit';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function removeUnit($unitId, $quantity, $moduleId)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$this->getUnits();
		$unit = array();
		if (isset($this->units[$unitId]))
		if ($this->units[$unitId]['value'] >= $quantity) {
			$ok = 1;
			$upkeepResource = $d13->getUnit($this->data['faction'], $unitId, 'upkeepResource');
			$this->resources[$upkeepResource]['value']+= $d13->getUnit($this->data['faction'], $unitId, 'upkeep') * $quantity;
			$d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$this->units[$unitId]['value']-= $quantity;
			$d13->dbQuery('update units set value="' . $this->units[$unitId]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $unitId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$this->getQueue('train', 'unit', $d13->getModule($this->data['faction'], $moduleId) ['units']);
			$lastTrain = count($this->queue['train']) - 1;
			if ($lastTrain > - 1) $start = strftime('%Y-%m-%d %H:%M:%S', $this->queue['train'][$lastTrain]['start'] + floor($this->queue['train'][$lastTrain]['duration'] * 60));
			else $start = strftime('%Y-%m-%d %H:%M:%S', time());
			$totalIR = 0;
			foreach($this->modules as $key => $item)
			if ($item['module'] == $moduleId) $totalIR+= $item['input'] * $d13->getModule($this->data['faction'], $item['module'], 'ratio');
			$duration = $d13->getUnit($this->data['faction'], $unitId, 'removeDuration') * $quantity;
			$duration = ($duration - $duration * $totalIR) * $d13->getGeneral('users', 'speed', 'train');
			$result = $d13->dbQuery('select max(id) as id from train where node="' . $this->data['id'] . '"');
			$row = $d13->dbFetch($result);
			$id = $row['id'] + 1;
			$d13->dbQuery('insert into train (id, node, unit, quantity, stage, start, duration) values ("' . $id . '", "' . $this->data['id'] . '", "' . $unitId . '", "' . $quantity . '", 1, "' . $start . '", "' . $duration . '")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'notEnoughUnits';
		else $status = 'noUnit';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function cancelUnit($trainId, $moduleId)
	{
		global $d13;
		$this->getResources();
		$this->getComponents();
		$this->getUnits();
		$result = $d13->dbQuery('select * from train where id="' . $trainId . '"');
		$entry = $d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			$upkeepResource = $d13->getUnit($this->data['faction'], $entry['unit'], 'upkeepResource');
			$upkeep = $d13->getUnit($this->data['faction'], $entry['unit'], 'upkeep') * $entry['quantity'];
			if (!$entry['stage']) $this->resources[$upkeepResource]['value']+= $upkeep;
			else $this->resources[$upkeepResource]['value']-= $upkeep;
			$d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if (!$entry['stage']) {
				foreach($d13->getUnit($this->data['faction'], $entry['unit'], 'cost') as $cost) {
					$this->resources[$cost['resource']]['value']+= $cost['value'] * $entry['quantity'] * $d13->getGeneral('users', 'cost', 'train');
					$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}

				foreach($d13->getUnit($this->data['faction'], $entry['unit'], 'requirements') as $requirement)
				if ($requirement['type'] == 'components') {
					$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
					$this->resources[$storageResource]['value']-= $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $entry['quantity'];
					$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					$this->components[$requirement['id']]['value']+= $requirement['value'] * $entry['quantity'];
					$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}
			else {
				$this->units[$entry['unit']]['value']+= $entry['quantity'];
				$d13->dbQuery('update units set value="' . $this->units[$entry['unit']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['unit'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->getQueue('train', 'unit', $d13->getModule($this->data['faction'], $moduleId) ['units']);
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queue['train'] as $queueEntry) {
				if ($queueEntry['start'] > $entry['start']) {
					$d13->dbQuery('update train set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where id="' . $queueEntry['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}

			$d13->dbQuery('delete from train where id="' . $trainId . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function getCombat($combatId)
	{
		global $d13;
		$result = $d13->dbQuery('select * from combat where id="' . $combatId . '"');
		$combat = $d13->dbFetch($result);
		return $combat;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function addCombat($nodeId, $data, $type, $slotId)
	{
		global $d13;
		$this->getResources();
		$this->getUnits();
		$this->getLocation();
		$node = new node();
		$okUnits = 1;
		
		$army = array();
		foreach($data['input']['attacker']['groups'] as $group) {
			if (isset($army[$group['unitId']])) {
				$army[$group['unitId']]+= $group['quantity'];
			} else {
				$army[$group['unitId']] = $group['quantity'];
			}
		}
		
		$speed = 999999999;
		foreach($this->units as $key => $group) {
			if (isset($army[$key])) {
				if ($army[$key] > $group['value']) {
					$okUnits = 0;
				}
				if ($d13->getUnit($this->data['faction'], $key, 'speed') < $speed) {
					$speed = $d13->getUnit($this->data['faction'], $key, 'speed');
				}
			}
		}
		
		$combatCost = $d13->getGeneral('factions', $this->data['id'], 'costs', $type);
		$okCombatCost = $this->checkCost($combatCost, 'combat');
		
		if ($okUnits)
		if ($okCombatCost['ok'])
		if ($node->get('id', $nodeId) == 'done') {
			$node->getLocation();
			$distance = sqrt(pow(abs($this->location['x'] - $node->location['x']) , 2) + pow(abs($this->location['y'] - $node->location['y']) , 2));
			$duration = ($distance * $d13->getGeneral('factors', 'movement')) / ($speed * $d13->getGeneral('users', 'speed', 'combat'));
			$combatId = misc::newId('combat');
			$ok = 1;
			$cuBuffer = array();
			
			foreach($army as $key => $value) {
				$cuBuffer[] = '("' . $combatId . '", "' . $key . '", "' . $value . '")';
				$this->units[$key]['value']-= $value;
				$d13->dbQuery('update units set value="' . $this->units[$key]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $key . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$upkeepResource = $d13->getUnit($this->data['faction'], $key, 'upkeepResource');
				$upkeep = $d13->getUnit($this->data['faction'], $key, 'upkeep');
				$this->resources[$upkeepResource]['value']+= $upkeep * $value;
				$d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			foreach($combatCost as $cost) {
				$this->resources[$cost['resource']]['value']-= $cost['value'] * $d13->getGeneral('users', 'cost', 'combat');
				$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$d13->dbQuery('insert into combat (id, sender, recipient, focus, stage, start, duration, type, slot) values ("' . $combatId . '", "' . $this->data['id'] . '", "' . $node->data['id'] . '", "' . $data['input']['attacker']['focus'] . '", "0", "' . strftime('%Y-%m-%d %H:%M:%S', time()) . '", "' . $duration . '", "' . $type . '", "' . $slotId . '")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('insert into combat_units (combat, id, value) values ' . implode(', ', $cuBuffer));
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noNode';
		else $status = 'notEnoughResources';
		else $status = 'notEnoughUnits';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function cancelCombat($combatId)
	{
		global $d13;
		$result = $d13->dbQuery('select * from combat where stage=0 and id="' . $combatId . '"');
		$row = $d13->dbFetch($result);
		if (isset($row['id'])) {
			$elapsed = (time() - strtotime($row['start'])) / 60;
			$start = strftime('%Y-%m-%d %H:%M:%S', time());
			$d13->dbQuery('update combat set stage=1, start="' . $start . '", duration="' . $elapsed . '" where id="' . $combatId . '"');
			if ($d13->dbAffectedRows() == - 1) $status = 'error';
			else $status = 'done';
		}
		else $status = 'noCombat';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// checkResources
	// ----------------------------------------------------------------------------------------

	public

	function checkResources($time)
	{
		global $d13;
		$d13->dbQuery('start transaction');
		$this->getModules();
		$this->getResources();
		$elapsed = ($time - strtotime($this->data['lastCheck'])) / 3600;
		$ok = 1;
		foreach($d13->getResource() as $resource) {
			if ($resource['active'] && $resource['type'] == 'dynamic') {
				$this->resources[$resource['id']]['value']+= $this->production[$resource['id']] * $elapsed;
				if ($this->storage[$resource['id']]) {
				
					if ($resource['limited'] == true && $this->resources[$resource['id']]['value'] > $this->storage[$resource['id']]) {
						$this->resources[$resource['id']]['value'] = $this->storage[$resource['id']];
					}

					$d13->dbQuery('update resources set value="' . $this->resources[$resource['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $resource['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) {
						$ok = 0;
					}

					$d13->dbQuery('update nodes set lastCheck="' . strftime('%Y-%m-%d %H:%M:%S', $time) . '" where id="' . $this->data['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) {
						$ok = 0;
					}
				}
			}
		}

		if ($ok) {
			$d13->dbQuery('commit');
		}
		else {
			$d13->dbQuery('rollback');
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkResearch($time)
	{
		global $d13;
		$d13->dbQuery('start transaction');
		$this->getTechnologies();
		$this->getQueue('research');
		$ok = 1;
		foreach($this->queue['research'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration'] * 60);
			if ($entry['end'] <= $time) {
				$this->technologies[$entry['technology']]['level']++;
				$d13->dbQuery('update technologies set level="' . $this->technologies[$entry['technology']]['level'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['technology'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$d13->dbQuery('delete from research where node="' . $this->data['id'] . '" and technology="' . $entry['technology'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		if ($ok) $d13->dbQuery('commit');
		else $d13->dbQuery('rollback');
	}

	// ----------------------------------------------------------------------------------------
	// checkBuild
	// ----------------------------------------------------------------------------------------

	public

	function checkBuild($time)
	{
		global $d13;
		$d13->dbQuery('start transaction');
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$this->getQueue('build');
		$ok = 1;
		foreach($this->queue['build'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration'] * 60);
			if ($entry['end'] <= $time) {

				// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - BUILD

				if ($entry['action'] == 'build' && $this->modules[$entry['slot']]['module'] == - 1) {
					$this->modules[$entry['slot']]['module'] = $entry['module'];
					$d13->dbQuery('update modules set module="' . $entry['module'] . '", level=1 where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;

					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - UPGRADE

				}
				else
				if ($entry['action'] == 'upgrade' && $this->modules[$entry['slot']]['module'] > - 1) {
					$this->modules[$entry['slot']]['module'] = $entry['module'];
					$d13->dbQuery('update modules set module="' . $entry['module'] . '", level=level+1 where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;

					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - REMOVE

				}
				else
				if ($entry['action'] == 'remove') {
					foreach($d13->getModule($this->data['faction'], $entry['module'], 'cost') as $cost) {
						$this->resources[$cost['resource']]['value']+= $cost['value'] * $d13->getGeneral('users', 'cost', 'build') * $d13->getModule($this->data['faction'], $entry['module'], 'salvage');
						$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
					}

					foreach($d13->getModule($this->data['faction'], $entry['module'], 'requirements') as $requirement) {
						if ($requirement['type'] == 'components') {
							$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$storage = $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
							if ($this->resources[$storageResource]['value'] - $storage >= 0) {
								$this->resources[$storageResource]['value']-= $storage;
								$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']+= $requirement['value'];
								$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($d13->dbAffectedRows() == - 1) $ok = 0;
							}
						}

						if ($this->modules[$entry['slot']]['input'] > 0) {
							$inputResource = $d13->getModule($this->data['faction'], $entry['module'], 'inputResource');
							$this->resources[$inputResource]['value']+= $this->modules[$entry['slot']]['input'];
							$d13->dbQuery('update resources set value="' . $this->resources[$inputResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $inputResource . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}

						$this->modules[$entry['slot']]['module'] = - 1;
						$this->checkModuleDependencies($entry['module'], $entry['slot']);
						$d13->dbQuery('update modules set module="-1", input="0" where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
					}
				}

				$d13->dbQuery('delete from build where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		if ($ok) {
			$d13->dbQuery('commit');
		}
		else {
			$d13->dbQuery('rollback');
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkCraft($time)
	{
		global $d13;
		$d13->dbQuery('start transaction');
		$this->getResources();
		$this->getComponents();
		$this->getQueue('craft');
		$ok = 1;
		foreach($this->queue['craft'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration'] * 60);
			if ($entry['end'] <= $time) {
				if (!$entry['stage']) {
					$this->components[$entry['component']]['value']+= $entry['quantity'];
					$d13->dbQuery('update components set value="' . $this->components[$entry['component']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['component'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
				else {
					foreach($d13->getComponent($this->data['faction'], $entry['component'], 'cost') as $cost) {
						$this->resources[$cost['resource']]['value']+= $cost['value'] * $entry['quantity'] * $d13->getGeneral('users', 'cost', 'craft') * $d13->getComponent($this->data['faction'], $entry['component'], 'salvage');
						$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
					}

					foreach($d13->getComponent($this->data['faction'], $entry['component'], 'requirements') as $requirement)
					if ($requirement['type'] == 'components') {
						$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
						$storage = $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
						if ($this->resources[$storageResource]['value'] - $storage >= 0) {
							$this->resources[$storageResource]['value']-= $storage;
							$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							$this->components[$requirement['id']]['value']+= $requirement['value'] * $entry['quantity'];
							$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}
					}
				}

				$d13->dbQuery('delete from craft where id="' . $entry['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		if ($ok) $d13->dbQuery('commit');
		else $d13->dbQuery('rollback');
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkTrain($time)
	{
		global $d13;
		$d13->dbQuery('start transaction');
		$this->getResources();
		$this->getComponents();
		$this->getUnits();
		$this->getQueue('train');
		$ok = 1;
		foreach($this->queue['train'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration'] * 60);
			if ($entry['end'] <= $time) {
				if (!$entry['stage']) {
					$this->units[$entry['unit']]['value']+= $entry['quantity'];
					$d13->dbQuery('update units set value="' . $this->units[$entry['unit']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['unit'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
				}
				else {
					foreach($d13->getUnit($this->data['faction'], $entry['unit'], 'cost') as $cost) {
						$this->resources[$cost['resource']]['value']+= $cost['value'] * $entry['quantity'] * $d13->getGeneral('users', 'cost', 'train') * $d13->getUnit($this->data['faction'], $entry['unit'], 'salvage');
						$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
					}

					foreach($d13->getUnit($this->data['faction'], $entry['unit'], 'requirements') as $requirement)
					if ($requirement['type'] == 'components') {
						$storageResource = $d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
						$storage = $d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
						if ($this->resources[$storageResource]['value'] - $storage >= 0) {
							$this->resources[$storageResource]['value']-= $storage;
							$d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
							$this->components[$requirement['id']]['value']+= $requirement['value'] * $entry['quantity'];
							$d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
							if ($d13->dbAffectedRows() == - 1) $ok = 0;
						}
					}
				}

				$d13->dbQuery('delete from train where id="' . $entry['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		if ($ok) $d13->dbQuery('commit');
		else $d13->dbQuery('rollback');
	}

	// ----------------------------------------------------------------------------------------
	// checkCombat
	// ----------------------------------------------------------------------------------------

	public

	function checkCombat($time)
	{
		global $d13;
		$d13->dbQuery('start transaction');
		$this->getQueue('combat');
		$ok = 1;
		
		foreach($this->queue['combat'] as $combat) {
			$combat['end'] = $combat['start'] + floor($combat['duration'] * 60);
			if ($combat['end'] <= $time) {
				$otherNode = new node();
				if ($combat['sender'] == $this->data['id']) {
					$nodes = array(
						'attacker' => 'this',
						'defender' => 'otherNode'
					);
					$status = $otherNode->get('id', $combat['recipient']);
				}
				else {
					$nodes = array(
						'attacker' => 'otherNode',
						'defender' => 'this'
					);
					$status = $otherNode->get('id', $combat['sender']);
				}

				if (!$combat['stage']) {
					if ($status == 'done') {

						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - GATHER ATTACKER DATA

						$data = array();
						$data['input']['attacker']['groups'] = array();
						$data['input']['attacker']['focus'] = $combat['focus'];
						$data['input']['attacker']['faction'] = $$nodes['attacker']->data['faction'];
						$data['input']['attacker']['nodeId'] = $$nodes['attacker']->data['id'];
						$otherResult = $d13->dbQuery('select * from combat_units where combat="' . $combat['id'] . '"');
						while ($group = $d13->dbFetch($otherResult)) {
							$data['input']['attacker']['groups'][] = array(
								'unitId' => $group['id'],
								'quantity' => $group['value']
							);
						}

						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - GATHER DEFENDER DATA

						$data['input']['defender']['groups'] = array();
						$data['input']['defender']['focus'] = $$nodes['defender']->data['focus'];
						$data['input']['defender']['faction'] = $$nodes['defender']->data['faction'];
						$data['input']['defender']['nodeId'] = $$nodes['defender']->data['id'];

						// - - - - - DEFENDER UNITS

						if (!$d13->getGeneral('options', 'unitAttackOnly')) {
							$otherNode->getUnits();
							foreach($$nodes['defender']->units as $group) {
								$data['input']['defender']['groups'][] = array(
									'unitId' => $group['id'],
									'type' => 'unit',
									'quantity' => $group['value']
								);
							}
						}

						// - - - - - DEFENDER MODULES

						$otherNode->getModules();
						foreach($$nodes['defender']->modules as $group) {
							if ($group['module'] > - 1) {
								if ($d13->getModule($$nodes['defender']->data['faction'], $group['module'], 'type') == 'defense' && $group['input'] > 0) {
									$data['input']['defender']['groups'][] = array(
										'moduleId' => $group['module'],
										'unitId' => $d13->getModule($$nodes['defender']->data['faction'], $group['module'], 'unitId') ,
										'type' => 'module',
										'level' => $group['level'],
										'input' => $group['input'],
										'maxInput' => $d13->getModule($$nodes['defender']->data['faction'], $group['module'], 'maxInput')
									);
								}
							}
						}

						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - CALCULATE COMBAT

						$battle = new d13_combat();
						$data = $battle->doCombat($data);

						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - COMBAT AFTERMATH

						$overkill = true;
						$$nodes['defender']->getResources();

						// - - - - - Defender Groups

						foreach($data['output']['defender']['groups'] as $key => $group) {

							// - - - - - Units

							if ($group['type'] == 'unit') {
								$d13->dbQuery('update units set value="' . $group['quantity'] . '" where node="' . $$nodes['defender']->data['id'] . '" and id="' . $group['unitId'] . '"');
								if ($d13->dbAffectedRows() == - 1) {
									$ok = 0;
								}

								$lostCount = $data['input']['defender']['groups'][$key]['quantity'] - $group['quantity'];
								if ($lostCount > 0) {
									$upkeepResource = $d13->getUnit($$nodes['defender']->data['faction'], $group['unitId'], 'upkeepResource');
									$upkeep = $d13->getUnit($$nodes['defender']->data['faction'], $group['unitId'], 'upkeep');
									$$nodes['defender']->resources[$upkeepResource]['value']+= $upkeep * $lostCount;
									$d13->dbQuery('update resources set value="' . $$nodes['defender']->resources[$upkeepResource]['value'] . '" where node="' . $$nodes['defender']->data['id'] . '" and id="' . $upkeepResource . '"');
									if ($d13->dbAffectedRows() == - 1) {
										$ok = 0;
									}
								}

								if ($group['quantity']) {
									$overkill = false;
								}

								// - - - - - Modules

							}
							else
							if ($group['type'] == 'module') {
								$d13->dbQuery('update modules set input="' . $group['input'] . '" where node="' . $$nodes['defender']->data['id'] . '" and module="' . $group['moduleId'] . '"');
								if ($d13->dbAffectedRows() == - 1) {
									$ok = 0;
								}

								// no lost count for modules

								if ($group['input']) {
									$overkill = false;
								}
							}
						}

						// - - - - - Attacker Groups

						foreach($data['output']['attacker']['groups'] as $key => $group) {
							$d13->dbQuery('update combat_units set value="' . $group['quantity'] . '" where combat="' . $combat['id'] . '" and id="' . $group['unitId'] . '"');
							if ($d13->dbAffectedRows() == - 1) {
								$ok = 0;
							}
						}

						// - - - - - Check Battle Result

						$start = strftime('%Y-%m-%d %H:%M:%S', $combat['end']);
						$d13->dbQuery('update combat set stage=1, start="' . $start . '" where id="' . $combat['id'] . '"');
						if ($d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}

						// scout
						// -> trigger scout report without harm if 'scout check' passed
						// -> otherwise trigger battle
						
						// raid
						// -> gain resources
						// -> gain no resources
						
						// sabotage
						// -> trigger special effect if scout check passed
						// -> otherwise trigger battle
						
						// skirmish
						// -> deal damage without harm if scout check passed
						// -> otherwise trigger battle
						
						// raze
						if ($combat['type'] == 'raze' && $overkill) {
							$this->remove($$nodes['defender']->data['id']);
						}
						
						// conquer
						if ($combat['type'] == 'conquer' && $overkill) {
							$d13->dbQuery('update nodes set user="' . $$nodes['attacker']->data['user'] . '" where id="' . $$nodes['defender']->data['id'] . '"');
						}

						if ($d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}

						// - - - - -

						$attackerUser = new user();
						if ($attackerUser->get('id', $$nodes['attacker']->data['user']) == 'done') {
							$attackerUser->getPreferences('name');
							if ($attackerUser->preferences['combatReports']) {
								$msg = new message();
								$msg->data['sender'] = $attackerUser->data['name'];
								$msg->data['recipient'] = $attackerUser->data['name'];
								$msg->data['subject'] = $d13->getLangUI("combatReport") . ' - ' . $$nodes['defender']->data['name'];
								$msg->data['body'] = $battle->assembleReport($data, $$nodes['attacker'], $$nodes['defender'], $combat['type']);
								$msg->data['viewed'] = 0;
								$msg->add();
							}
						}

						// - - - - -

						$defenderUser = new user();
						if ($defenderUser->get('id', $$nodes['defender']->data['user']) == 'done') {
							$defenderUser->getPreferences('name');
							if ($defenderUser->preferences['combatReports']) {
								$msg = new message();
								$msg->data['sender'] = $defenderUser->data['name'];
								$msg->data['recipient'] = $defenderUser->data['name'];
								$msg->data['subject'] = $d13->getLangUI("combatReport") . ' - ' . $$nodes['defender']->data['name'];
								$msg->data['body'] = $battle->assembleReport($data, $$nodes['attacker'], $$nodes['defender'], $combat['type'], true);
								$msg->data['viewed'] = 0;
								$msg->add();
							}
						}

					}
				}
				else {
					$$nodes['attacker']->getResources();
					$result = $d13->dbQuery('select * from combat_units where combat="' . $combat['id'] . '"');
					while ($group = $d13->dbFetch($result)) {
						$d13->dbQuery('update units set value=value+"' . $group['value'] . '" where node="' . $combat['sender'] . '" and id="' . $group['id'] . '"');
						if ($d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}

						$upkeepResource = $d13->getUnit($$nodes['attacker']->data['faction'], $group['id'], 'upkeepResource');
						$upkeep = $d13->getUnit($$nodes['attacker']->data['faction'], $group['id'], 'upkeep');
						$this->resources[$upkeepResource]['value']-= $upkeep * $group['value'];
						$d13->dbQuery('update resources set value="' . $$nodes['attacker']->resources[$upkeepResource]['value'] . '" where node="' . $$nodes['attacker']->data['id'] . '" and id="' . $upkeepResource . '"');
						if ($d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}
					}

					$d13->dbQuery('delete from combat_units where combat="' . $combat['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) {
						$ok = 0;
					}

					$d13->dbQuery('delete from combat where id="' . $combat['id'] . '"');
					if ($d13->dbAffectedRows() == - 1) {
						$ok = 0;
					}
				}
			}
		}

		if ($ok) {
			$d13->dbQuery('commit');
		}
		else {
			$d13->dbQuery('rollback');
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkAll($time)
	{
		$this->checkResources($time);
		$this->checkResearch($time);
		$this->checkBuild($time);
		$this->checkCraft($time);
		$this->checkTrain($time);

		// $this->checkTrade($time);

		$this->checkCombat($time);
	}

	// ----------------------------------------------------------------------------------------
	// checkOptions
	// Scan all modules on this node and determine if a specific option is available
	// ----------------------------------------------------------------------------------------

	public

	function checkOptions($option)
	{
		global $d13;
		$this->getModules();
		foreach($this->modules as $module) {
			$options = $d13->getModule($this->data['faction'], $module['module'], 'options');
			if (isset($options[$option])) {
				return $options[$option];
			}
		}

		return FALSE;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkRequirements($requirements, $quantity = 1)
	{
		global $d13;
		$data = array(
			'ok' => 1,
			'requirements' => $requirements
		);
		foreach($data['requirements'] as $key => $requirement)
		if (isset($requirement['value']) || isset($requirement['level'])) switch ($requirement['type']) {

			// - - - - -

		case 'technologies':
			if ($this->technologies[$requirement['id']]['level'] < $requirement['level']) {
				$data['requirements'][$key]['ok'] = 0;
				$data['ok'] = 0;
			}
			else {
				$data['requirements'][$key]['ok'] = 1;
			}

			break;

			// - - - - -

		case 'modules':
			if (isset($requirement['level'])) {
				foreach($this->modules as $module) {
					if ($module['module'] == $requirement['id']) {
						if ($module['level'] < $requirement['level']) {
							$data['requirements'][$key]['ok'] = 0;
							$data['ok'] = 0;
						}
						else {
							$data['requirements'][$key]['ok'] = 1;
						}

						break;
					}
				}
			}
			else
			if (isset($requirement['value'])) {
				$moduleCount = 0;
				foreach($this->modules as $module) {
					if ($module['module'] == $requirement['id']) {
						$moduleCount++;
					}
				}

				if ($moduleCount < $requirement['value']) {
					$data['requirements'][$key]['ok'] = 0;
					$data['ok'] = 0;
				}
				else {
					$data['requirements'][$key]['ok'] = 1;
				}
			}

			break;

			// - - - - -

		case 'components':
			if ($this->components[$requirement['id']]['value'] < $requirement['value'] * $quantity) {
				$data['requirements'][$key]['ok'] = 0;
				$data['ok'] = 0;
			}
			else $data['requirements'][$key]['ok'] = 1;
			break;
		}
		else $data['requirements'][$key]['ok'] = 1;
		return $data;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkCost($cost, $costType, $quantity = 1)
	{
		global $d13;
		$data = array(
			'ok' => 1,
			'cost' => $cost
		);
		foreach($data['cost'] as $key => $cost) {
			if ($this->resources[$cost['resource']]['value'] < $cost['value'] * $quantity * $d13->getGeneral('users', 'cost', $costType)) {
				$data['cost'][$key]['ok'] = 0;
				$data['ok'] = 0;
			}
			else {
				$data['cost'][$key]['ok'] = 1;
			}
		}

		return $data;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkCostMax($cost, $costType)
	{
		$limit = 1;
		$lastlimit = 0;
		$max = 1000;
		$finished = false;
		$check = $this->checkCost($cost, $costType, $limit);
		if (!$check['ok']) {
			return $lastlimit;
		}
		else {
			$limit = floor($max / 2);
			$lastlimit = floor($max / 2);
			$check = $this->checkCost($cost, $costType, $limit);
			if (!$check['ok']) {
				$limit = 0;
				$lastlimit = 1;
			}

			while ($finished == false) {
				$limit+= 5;
				$check = $this->checkCost($cost, $costType, $limit);
				if (!$check['ok']) {
					return $lastlimit;
				}
				else {
					$lastlimit = $limit;
				}

				if ($limit >= $max) {
					$finished = true;
				}
			}
		}

		return $limit;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function checkRequirementsMax($cost)
	{
		$limit = 1;
		$lastlimit = 0;
		$max = 1000;
		$finished = false;
		$check = $this->checkRequirements($cost, $limit);
		if (!$check['ok']) {
			return $lastlimit;
		}
		else {
			$limit = floor($max / 2);
			$lastlimit = floor($max / 2);
			$check = $this->checkRequirements($cost, $limit);
			if (!$check['ok']) {
				$limit = 0;
				$lastlimit = 1;
			}

			while ($finished == false) {
				if ($limit <= 10) {
					$limit++;
				} else {
					$limit+= 5;
				}
				$check = $this->checkRequirements($cost, $limit);
				if (!$check['ok']) {
					return $lastlimit;
				}
				else {
					$lastlimit = $limit;
				}

				if ($limit >= $max) {
					$finished = true;
				}
			}
		}

		return $limit;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	private
	function checkModuleDependencies($moduleId, $slotId, $useOldIR = 0)
	{
		global $d13;
		switch ($d13->getModule($this->data['faction'], $moduleId) ['type']) {
		case 'research':
			$this->getQueue('research', 'technology', $d13->getModule($this->data['faction'], $moduleId) ['technologies']);
			$nr = count($this->queue['research']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queue['research'][$i]['start'] = $this->queue['research'][$i - 1]['start'] + floor($this->queue['research'][$i - 1]['duration'] * 60);
					$this->queue['research'][$i]['duration'] = $d13->getTechnology($this->data['faction'], $this->queue['research'][$i]['technology'], 'duration');
					$this->queue['research'][$i]['duration'] = ($this->queue['research'][$i]['duration'] - $this->queue['research'][$i]['duration'] * $newIR) * $d13->getGeneral('users', 'speed', 'research');
					$d13->dbQuery('update research set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queue['research'][$i]['start']) . '", duration="' . $this->queue['research'][$i]['duration'] . '" where node="' . $this->queue['research'][$i]['node'] . '" and technology="' . $this->queue['research'][$i]['technology'] . '"');
					if (!$moduleCount) $this->cancelTechnology($this->queue['research'][$i]['technology'], $moduleId);
				}
			}

			break;

		case 'craft':
			$this->getQueue('craft', 'component', $d13->getModule($this->data['faction'], $moduleId) ['components']);
			$nr = count($this->queue['craft']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queue['craft'][$i]['start'] = $this->queue['craft'][$i - 1]['start'] + floor($this->queue['craft'][$i - 1]['duration'] * 60);
					$this->queue['craft'][$i]['duration'] = $d13->getComponent($this->data['faction'], $this->queue['craft'][$i]['component'], 'duration') * $this->queue['craft'][$i]['quantity'];
					$this->queue['craft'][$i]['duration'] = ($this->queue['craft'][$i]['duration'] - $this->queue['craft'][$i]['duration'] * $newIR) * $d13->getGeneral('users', 'speed', 'craft');
					$d13->dbQuery('update craft set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queue['craft'][$i]['start']) . '", duration="' . $this->queue['craft'][$i]['duration'] . '" where id="' . $this->queue['craft'][$i]['id'] . '"');
					if (!$moduleCount) $this->cancelComponent($this->queue['craft'][$i]['id'], $moduleId);
				}
			}

			break;

		case 'train':
			$this->getQueue('train', 'unit', $d13->getModule($this->data['faction'], $moduleId) ['units']);
			$nr = count($this->queue['train']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queue['train'][$i]['start'] = $this->queue['train'][$i - 1]['start'] + floor($this->queue['train'][$i - 1]['duration'] * 60);
					$this->queue['train'][$i]['duration'] = $d13->getUnit($this->data['faction'], $this->queue['train'][$i]['unit'], 'duration') * $this->queue['train'][$i]['quantity'];
					$this->queue['train'][$i]['duration'] = ($this->queue['train'][$i]['duration'] - $this->queue['train'][$i]['duration'] * $newIR) * $d13->getGeneral('users', 'speed', 'train');
					$d13->dbQuery('update train set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queue['train'][$i]['start']) . '", duration="' . $this->queue['train'][$i]['duration'] . '" where id="' . $this->queue['train'][$i]['id'] . '"');
					if (!$moduleCount) $this->cancelComponent($this->queue['train'][$i]['id'], $moduleId);
				}
			}

			break;

		case 'trade':
			$this->getQueue('trade');
			$nr = count($this->queue['trade']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queue['trade'][$i]['start'] = $this->queue['trade'][$i - 1]['start'] + floor($this->queue['trade'][$i - 1]['duration'] * 60);
					$this->queue['trade'][$i]['duration'] = $d13->getGeneral('users', 'speed', 'trade') * $this->queue['trade'][$i]['distance'];
					$this->queue['trade'][$i]['duration'] = $this->queue['trade'][$i]['duration'] - $this->queue['trade'][$i]['duration'] * $newIR;
					$d13->dbQuery('update trade set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queue['trade'][$i]['start']) . '", duration="' . $this->queue['trade'][$i]['duration'] . '" where id="' . $this->queue['trade'][$i]['id'] . '"');

					// if (!$moduleCount) $this->cancelTrade($this->queue['trade'][$i]['id'], $moduleId);

				}
			}

			break;
		}
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function move($x, $y)
	{
		global $d13;
		$this->getModules();
		$this->getResources();
		$this->getLocation();
		$moveCost = $d13->getGeneral('factions', $this->data['faction'], 'costs') ['move'];
		$distance = ceil(sqrt(pow($this->location['x'] - $x, 2) + pow($this->location['y'] - $y, 2)));
		$moveCostData = $this->checkCost($moveCost, 'move');
		if ($moveCostData['ok']) {
			$node = new node();
			if ($node->get('id', $this->data['id']) == 'done') {
				$sector = grid::getSector($x, $y);
				if ($sector['type'] == 1) {
					$ok = 1;
					$d13->dbQuery('update grid set type="1", id=floor(1+rand()*9) where x="' . $this->location['x'] . '" and y="' . $this->location['y'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					$this->location['x'] = $x;
					$this->location['y'] = $y;
					$d13->dbQuery('update grid set type="2", id="' . $this->data['id'] . '" where x="' . $this->location['x'] . '" and y="' . $this->location['y'] . '"');
					if ($d13->dbAffectedRows() == - 1) $ok = 0;
					foreach($moveCost as $cost) {
						$this->resources[$cost['resource']]['value']-= $cost['value'] * $d13->getGeneral('users', 'cost', 'move');
						$d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
					}

					if ($ok) $status = 'done';
					else $status = 'error';
				}
				else $status = 'invalidGridSector';
			}
			else $status = 'noNode';
		}
		else $status = 'notEnoughResources';
		return $status;
	}
}

// =====================================================================================EOF

?>