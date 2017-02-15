<?php

// ========================================================================================
//
// NODE.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// A node represents a town (planet etc.) in the game. This is one of the central classes
// and compromises a lot of functionality. It is like a turning point for modules (buildings),
// resources, technologies, task-queues, resource production and so on.
//
// ========================================================================================

class d13_node

{
	
	protected $d13;
	
	public $data, $status, $resources, $production, $storage, $queues, $moduleCounts;
	
	public $technologies, $modules, $components, $units, $buffs;
	
	private $updated = array();
	
	// ----------------------------------------------------------------------------------------
	// constructor
	// ----------------------------------------------------------------------------------------
	public
	
	function __construct(d13_engine &$d13)
	{
		$this->d13 = $d13;
		$this->data = array();
		$this->queues = new d13_queue($this, $d13);
		$this->moduleCounts = array();
		$this->buffs = array();
		$this->technologies = array();
		$this->modules = array();
		$this->units = array();
		$this->components = array();
		
		$this->updated['technologies'] = false;
		$this->updated['modules'] = false;
		$this->updated['components'] = false;
		$this->updated['units'] = false;
		$this->updated['buffs'] = false;
		
	}

	// ----------------------------------------------------------------------------------------
	// get
	// Get all node data
	// ----------------------------------------------------------------------------------------
	public

	function get($idType, $id)
	{

		$result = $this->d13->dbQuery('select * from nodes where ' . $idType . '="' . $id . '"');
		
		$this->data = $this->d13->dbFetch($result);
		$this->data['x'] = -1;
		$this->data['y'] = -1;
		
		if (isset($this->data['id'])) {
			$this->getAll();
			$this->status = 'done';
		} else {
			$this->status = 'noNode';
		}

		return $this->status;
	}

	// ----------------------------------------------------------------------------------------
	// add
	// Add a fresh node to the grid
	// ----------------------------------------------------------------------------------------
	public

	function add($userId)
	{
		
		$grid = new d13_grid($this->d13);
		$sector = $grid->getSector($this->location['x'], $this->location['y']);
		$node = $this->d13->createNode();
		$status = 0;
		if ($sector['type'] == 1) {
			if ($node->get('name', $this->data['name']) == 'noNode') {
				$nodes = $this->d13->getNodeList($userId); #$this->d13->getNodeList($userId);
				if (count($nodes) < $this->d13->getGeneral('users', 'maxNodes')) {
					$ok = 1;
					$this->data['id'] = $this->d13->misc->newId('nodes');
					
					//- - - - - Add to grid
					$this->d13->dbQuery('insert into nodes (id, faction, user, name, focus, lastCheck) values ("' . $this->data['id'] . '", "' . $this->data['faction'] . '", "' . $this->data['user'] . '", "' . $this->data['name'] . '", "hp", now())');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					$this->d13->dbQuery('update grid set type="2", id="' . $this->data['id'] . '" where x="' . $this->location['x'] . '" and y="' . $this->location['y'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
					//- - - - - Add to resources
					$query = array();
					$nr = count($this->d13->getResource());
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $this->d13->getResource($i, 'id') . '", "' . $this->d13->getResource($i, 'storage') . '")';
					}
					$this->d13->dbQuery('insert into resources (node, id, value) values ' . implode(', ', $query));
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
					//- - - - - Add to technologies
					$query = array();
					$nr = count($this->d13->getTechnology($this->data['faction']));
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $this->d13->getTechnology($this->data['faction'], $i, 'id') . '", "0")';
					}
					$this->d13->dbQuery('insert into technologies (node, id, level) values ' . implode(', ', $query));
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
					//- - - - - Add to components
					$query = array();
					$nr = count($this->d13->getComponent($this->data['faction']));
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $this->d13->getComponent($this->data['faction'], $i, 'id') . '", "0")';
					}
					$this->d13->dbQuery('insert into components (node, id, value) values ' . implode(', ', $query));
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
					//- - - - - Add to units
					$query = array();
					$nr = count($this->d13->getUnit($this->data['faction']));
					for ($i = 0; $i < $nr; $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $this->d13->getUnit($this->data['faction'], $i, 'id') . '", "0", "1")';
					}
					$this->d13->dbQuery('insert into units (node, id, value, level) values ' . implode(', ', $query));
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
					//- - - - - Add to modules
					$query = array();
					for ($i = 0; $i < $this->d13->getGeneral('users', 'maxModules') * $this->d13->getGeneral('users', 'maxSectors'); $i++) {
						$query[$i] = '("' . $this->data['id'] . '", "' . $i . '", "-1", "0", "0")';
					}
					$this->d13->dbQuery('insert into modules (node, slot, module, input, level) values ' . implode(', ', $query));
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
					//- - - - - Add to shields
					$shieldId = $this->d13->getFaction($this->data['faction'], 'shield');
					$ok = $this->setShield($shieldId);
					
					if ($ok) {
						$status = "done";
					} else {
						$status = 'error';
					}
				} else {
					$status = 'maxNodesReached';
				}
			} else {
				$status = 'nameTaken';
			}
		} else {
			$status = 'invalidGridSector';
		}

		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// remove
	// Remove node from grid
	// ----------------------------------------------------------------------------------------
	public static

	function remove($id)
	{
				
		$node = $this->d13->createNode();
		
		if ($node->get('id', $id) == 'done') {
		
			$ok = 1;
			$node->getLocation();
			
			//- - - - - Remove Queues
			$this->d13->dbQuery('delete from research where node="' . $id . '"');
			$this->d13->dbQuery('delete from build where node="' . $id . '"');
			$this->d13->dbQuery('delete from craft where node="' . $id . '"');
			$this->d13->dbQuery('delete from train where node="' . $id . '"');
			$this->d13->dbQuery('delete from trade where node="' . $id . '"');
			$this->d13->dbQuery('delete from shield where node="' . $id . '"');
			$this->d13->dbQuery('delete from buff where node="' . $id . '"');
			$this->d13->dbQuery('delete from combat_units where combat in (select id from combat where sender="' . $id . '" or recipient="' . $id . '")');
			$this->d13->dbQuery('delete from combat where sender="' . $id . '" or recipient="' . $id . '"');
			
			//- - - - - Remove Objects
			$this->d13->dbQuery('delete from resources where node="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from technologies where node="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from modules where node="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from components where node="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from units where node="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "nodes")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from nodes where id="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			
			//- - - - - Update Map
			$this->d13->dbQuery('update grid set type="1", id=floor(1+rand()*9) where x="' . $node->location['x'] . '" and y="' . $node->location['y'] . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
		
			if ($ok) {
				$status = "done";
			} else {
				$status = 'error';
			}
		
		} else {
			$status = 'noNode';
		}
		
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	// addMarket
	// ----------------------------------------------------------------------------------------
	public
	
	function addMarket($slotId)
	{
		
		
		$this->getModules();
		
		$ok 		= 1;
		
		if ($this->modules[$slotId]['module'] > -1) {
			
			$tmp_module = $this->d13->createModule($this->modules[$slotId]['module'], $slotId, $this);
			
			if (isset($tmp_module->data['inventory']) && $tmp_module->data['inventory']) {
				
				$inventory = $tmp_module->data['inventory'];
				
				$duration = 24 - $tmp_module->data['totalIR'];				# TODO: move to config later
				$duration = max($duration, 1) * 60;
				$limit	  = max($tmp_module->data['totalIR'], 1); 					# could go to config later
				
				$tmp_inventory = array();

    			for ($i = 1; $i <= $limit; $i++) {
    				$key = array_rand($inventory);
    				$tmp_inventory[] = $inventory[$key];
    			}

				$inventory = json_encode($tmp_inventory);
				$start 		= strftime('%Y-%m-%d %H:%M:%S', time());
		
				$this->d13->dbQuery("insert into market (node, slot, start, duration, inventory) values ('" . $this->data['id'] . "', '" . $slotId . "', '" . $start . "', '" . $duration . "', '" . $inventory . "')");
		
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;

			} else {
				$ok = 0;
			}
		} else {
			$ok = 0;
		}
		
		if ($ok) {
			$status = 'done';
		} else {
			$status = 'error';
		}
		
		return $status;

	}
	
	// ----------------------------------------------------------------------------------------
	// buyMarket
	// @ 
	// ----------------------------------------------------------------------------------------
	public

	function buyMarket($slotId, $objectType, $objectId)
	{

		
	
		$this->getModules();
		$ok 		= 1;
		
		if ($this->modules[$slotId]['module'] > -1) {
			$tmp_module = $this->d13->createModule($this->modules[$slotId]['module'], $slotId, $this);
			if (isset($tmp_module->data['inventory']) && $tmp_module->data['inventory']) {
	
				$ok = 0;
				
				foreach ($tmp_module->data['inventory'] as $key => $item) {
					if ($item['object'] == $objectType && $item['id'] == $objectId) {
						$ok = 1;
						
						$args = array();
						$args['supertype'] = $item['object'];
						$args['id'] = $item['id'];
						
						$tmp_object = $this->d13->createGameObject($args, $this);
						$total = $item['amount'] * $tmp_module->data['priceModifier'];
						
						if ($tmp_object->getCheckConvertedCost($tmp_module->data['paymentResource'], $total)) {
	
							$cost = $tmp_object->getConvertedCost($tmp_module->data['paymentResource'], false, $total);
							
							// Pay amount of resources
							$this->resources[$cost['resource']]['value'] -= $cost['value'];
							$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							
							// Switch Item type
							switch ($item['object'])
							{
							
								case 'resource':
                					$status = $this->setResources($item['id'], $item['amount']);
									break;

								case 'technology':
			   						$status = $this->setTechnology($item['id']);
									break;

								case 'component':
			   						$status = $this->setComponent($item['id'], $item['amount']);
									break;

								case 'unit':
			   						$status = $this->setUnit($item['id'], $item['amount']);
									break;

								case 'shield':
			  						$status = $this->setShield($item['id']);
									break;
			
								case 'buff':
									$status = $this->setBuff($item['id']);
									break;
									
								default:
									$status = 'error';
									break;

							}
					
							if ($status == 'done') {
								unset($tmp_module->data['inventory'][$key]);
								$inventory = json_encode($tmp_module->data['inventory']);
								$this->d13->dbQuery("update market set inventory='" . $inventory . "' where node='" . $this->data['id'] . "' and slot='" . $slotId . "'");
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							} else {
								$ok = 0;
							}
							
						
						}
					
						break;
					}
				}
	
			} else {
				$ok = 0;
			}
		} else {
			$ok = 0;
		}
		
		if ($ok) {
			$status = 'done';
		} else {
			$status = 'error';
		}
		
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	// addTechnology
	// @ insert a fresh technology task into the research queue
	// ----------------------------------------------------------------------------------------
	public

	function addTechnology($technologyId, $slotId)
	{
	
		
		
		$this->getModules();
		$this->getResources();
		$this->getTechnologies();
		$this->getComponents();
		$technology = array();
		
		if (isset($this->technologies[$technologyId])) {
			$okModule = 0;
			if (isset($this->modules[$slotId]['module']))
			if (in_array($technologyId, $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'technologies'))) $okModule = 1;
			if ($okModule)
			if ($this->technologies[$technologyId]['level'] < $this->d13->getTechnology($this->data['faction'], $technologyId,'maxLevel')) {
				$result = $this->d13->dbQuery('select count(*) as count from research where node="' . $this->data['id'] . '" and obj_id="' . $technologyId . '"');
				$row = $this->d13->dbFetch($result);
				if (!$row['count']) {
					
					$args = array();
					$args['supertype'] = 'technology';
					$args['id'] = $technologyId;
					
					$tmp_technology = $this->d13->createGameObject($args, $this);
					
					$technology['requirementsData'] = $this->checkRequirements($this->d13->getTechnology($this->data['faction'],$technologyId,'requirements'));
					if ($technology['requirementsData']['ok']) {
						$technology['costData'] = $this->checkCost($this->d13->getTechnology($this->data['faction'],$technologyId,'cost'), 'research');
						if ($technology['costData']['ok']) {

							$ok = 1;
							
							foreach($tmp_technology->data['cost'] as $cost) {
								$this->resources[$cost['resource']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'research') * $this->getBuff('efficiency', 'research');
								$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}

							foreach($tmp_technology->data['requirements'] as $requirement) {
								if ($requirement['type'] == 'components') {
									$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
									$this->resources[$storageResource]['value']+= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
									$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
									if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
									$this->components[$requirement['id']]['value']-= $requirement['value'];
									$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
									if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
								}
							}
														
							$start = strftime('%Y-%m-%d %H:%M:%S', time());
							$duration = $tmp_technology->data['duration'];
							
							$totalIR = $this->modules[$slotId]['input'] * $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'ratio');
							$duration = ($duration - $duration * $totalIR) * $this->d13->getGeneral('users', 'duration', 'research') * $this->getBuff('duration', 'research') * 60;
							
							$this->d13->dbQuery('insert into research (node, obj_id, start, duration, slot) values ("' . $this->data['id'] . '", "' . $technologyId . '", "' . $start . '", "' . $duration . '", "' . $slotId . '")');
							
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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
	// setModule
	// @Set module input to new input (can be either a resource, component or a unit)
	// ----------------------------------------------------------------------------------------
	public

	function setModule($slotId, $input)
	{
		
		$result = $this->d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $this->d13->dbFetch($result);
		
		if (isset($module['module'])) {
			if ($module['module'] > - 1) {
				
				$tmp_module = $this->d13->createModule($module['module'], $slotId, $this);
				$inputType = $tmp_module->data['inputType'][0]['object'];
				$inputID = $tmp_module->data['inputType'][0]['id'];
				
				if ($inputType == "resource") {
					$this->getResources();
					$table = "resources";
				} else if ($inputType == "component") {
					$this->getComponents();
					$table = "components";
				} else if ($inputType == "unit") {
					$this->getUnits();
					$table = "units";
				}
				
				$result = $this->d13->dbQuery('select * from '. $table . ' where node="' . $this->data['id'] . '" and id="' . $inputID . '"');
				$resource = $this->d13->dbFetch($result);
								
				if ($resource['value'] + $module['input'] >= $this->modules[$slotId]['input']) {
					if ($this->modules[$slotId]['input'] <= $tmp_module->data['maxInput']) {
						
						$ok = 1;
						
						if ($inputType == "resource") {
							$this->resources[$resource['id']]['value'] += ($module['input'] - $input);
							$value = $this->resources[$resource['id']]['value'];
						} else if ($inputType == "component") {
							$this->components[$resource['id']]['value'] += ($module['input'] - $input);
							$value = $this->components[$resource['id']]['value'];
						} else if ($inputType == "unit") {
							$this->units[$resource['id']]['value'] += ($module['input'] - $input);
							$value = $this->units[$resource['id']]['value'];
						}

						$this->d13->dbQuery('update '. $table .' set value="' . $value . '" where node="' . $this->data['id'] . '" and id="' . $resource['id'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						$this->d13->dbQuery('update modules set input="' . $input . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						$this->checkModuleDependencies($module['module'], $slotId, 1);
			
						if ($ok) {
							$status = 'done';
						} else { 
							$status = 'error';
						}
			
					} else {
						$status = 'maxInputExceeded';
					}
				} else {
					$status = 'notEnoughResources';
				}
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

	function getModuleCount($moduleId)
	{
		
		
		if (isset($this->moduleCounts[$moduleId])) {
			return $this->moduleCounts[$moduleId];
		} else {
			$result = $this->d13->dbQuery('select count(*) as count from modules where node="' . $this->data['id'] . '" and module = "' . $moduleId . '"');
			$row = $this->d13->dbFetch($result);
			$this->moduleCounts[$moduleId] = $row['count'];
			return $this->moduleCounts[$moduleId];
		}
		
		
	}

	// ----------------------------------------------------------------------------------------
	// addModule
	// ----------------------------------------------------------------------------------------
	public

	function addModule($slotId, $moduleId, $input=1)
	{
		
		
		$result = $this->d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $this->d13->dbFetch($result);
		if (isset($module['module']))
		if ($module['module'] == - 1) {
			
			$count = $this->getModuleCount($moduleId);
			
			$this->queues->getQueue("build");
			if (count($this->queues->queue["build"])) {
				foreach($this->queues->queue["build"] as $item) {
					if ($item['obj_id'] == $moduleId) {
						$count++;
					}
				}
			}
			
			if ($count < $this->d13->getModule($this->data['faction'], $moduleId, 'maxInstances')) {
				$result = $this->d13->dbQuery('select count(*) as count from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
				$row = $this->d13->dbFetch($result);
				if (!$row['count']) {
					$this->getModules();
					$this->getResources();
					$this->getTechnologies();
					$this->getComponents();
					$module['requirementsData'] = $this->checkRequirements($this->d13->getModule($this->data['faction'], $moduleId, 'requirements'));
					if ($module['requirementsData']['ok']) {
						$module['costData'] = $this->checkCost($this->d13->getModule($this->data['faction'], $moduleId, 'cost'), 'build');
						if ($module['costData']['ok']) {
						
							$ok = 1;
							$tmp_module = $this->d13->createModule($moduleId, $slotId, $this);
							
							foreach($tmp_module->data['cost'] as $cost) {
								$this->resources[$cost['resource']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'build') * $this->getBuff('efficiency', 'build');
								$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}

							foreach($this->d13->getModule($this->data['faction'], $moduleId) ['requirements'] as $requirement)
							if ($requirement['type'] == 'components') {
								$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
								$this->resources[$storageResource]['value']+= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']-= $requirement['value'];
								$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}
														
							$inputType = $tmp_module->data['inputType'][0]['object'];
							$inputID = $tmp_module->data['inputType'][0]['id'];
				
							if ($inputType == "resource") {
								$this->getResources();
								$table = "resources";
							} else if ($inputType == "component") {
								$this->getComponents();
								$table = "components";
							} else if ($inputType == "unit") {
								$this->getUnits();
								$table = "units";
							}
				
							$result = $this->d13->dbQuery('select * from '. $table . ' where node="' . $this->data['id'] . '" and id="' . $inputID . '"');
							$resource = $this->d13->dbFetch($result);
								
							if ($resource['value'] + $module['input'] >= $this->modules[$slotId]['input']) {
								if ($this->modules[$slotId]['input'] <= $tmp_module->data['maxInput']) {
						
									$ok = 1;
						
									if ($inputType == "resource") {
										$this->resources[$resource['id']]['value'] += ($module['input'] - $input);
										$value = $this->resources[$resource['id']]['value'];
									} else if ($inputType == "component") {
										$this->components[$resource['id']]['value'] += ($module['input'] - $input);
										$value = $this->components[$resource['id']]['value'];
									} else if ($inputType == "unit") {
										$this->units[$resource['id']]['value'] += ($module['input'] - $input);
										$value = $this->units[$resource['id']]['value'];
									}

									$this->d13->dbQuery('update '. $table .' set value="' . $value . '" where node="' . $this->data['id'] . '" and id="' . $resource['id'] . '"');
									if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
									$this->d13->dbQuery('update modules set input="' . $input . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
									if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
									$this->checkModuleDependencies($module['module'], $slotId, 1);
			
									if ($ok) {
										$status = 'done';
									} else { 
										$status = 'error';
									}
			
								} else {
									$status = 'maxInputExceeded';
								}
							} else {
								$status = 'notEnoughResources';
							}
							
							
							
							
							$start = strftime('%Y-%m-%d %H:%M:%S', time());
							$duration = ceil( ($tmp_module->data['duration'] * $this->d13->getGeneral('users', 'duration', 'build') * $this->getBuff('duration', 'build') * 60 ) / $input);
							
							$this->d13->dbQuery('insert into build (node, slot, obj_id, start, duration, action) values ("' . $this->data['id'] . '", "' . $slotId . '", "' . $moduleId . '", "' . $start . '", "' . $duration . '", "build")');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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
		
		$result = $this->d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $this->d13->dbFetch($result);
		if (isset($module['module']))
		if ($module['module'] > - 1) {
			$result = $this->d13->dbQuery('select count(*) as count from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			$row = $this->d13->dbFetch($result);
			if (!$row['count']) {
				$this->getModules();
				$this->getResources();
				$this->getTechnologies();
				$this->getComponents();
				$tmp_module = $this->d13->createModule($moduleId, $slotId, $this);
				$module['requirementsData'] = $this->checkRequirements($this->d13->getModule($this->data['faction'], $moduleId, 'requirements'));
				if ($module['requirementsData']['ok']) {
					$module['costData'] = $this->checkCost($this->d13->getModule($this->data['faction'], $moduleId, 'cost'), 'build');
					if ($module['costData']['ok']) {
						$ok = 1;
						
						foreach($tmp_module->data['cost'] as $cost) {
							$this->resources[$cost['resource']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'build') * $this->getBuff('efficiency', 'build');
							$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						}

						foreach($this->d13->getModule($this->data['faction'], $moduleId) ['requirements'] as $requirement)
						if ($requirement['type'] == 'components') {
							$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$this->resources[$storageResource]['value']+= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
							$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							$this->components[$requirement['id']]['value']-= $requirement['value'];
							$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						}
						
						if ($input != $this->modules[$slotId]['input']) {
							$status = $this->setModule($slotId, 0);
							if ($status == 'done') {
								$status = $this->setModule($slotId, $input);
							}
						}

						$start = strftime('%Y-%m-%d %H:%M:%S', time());
						$duration = ceil( ($tmp_module->data['duration'] * $this->d13->getGeneral('users', 'duration', 'build') * $this->getBuff('duration', 'build') * 60 ) / $input);
						
						$this->d13->dbQuery('insert into build (node, slot, obj_id, start, duration, action) values ("' . $this->data['id'] . '", "' . $slotId . '", "' . $moduleId . '", "' . $start . '", "' . $duration . '", "upgrade")');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function removeModule($slotId)
	{
		
		$result = $this->d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$module = $this->d13->dbFetch($result);
		if (isset($module['module']))
		if ($module['module'] > - 1) {
			$result = $this->d13->dbQuery('select count(*) as count from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			$row = $this->d13->dbFetch($result);
			if (!$row['count']) {
				$start = strftime('%Y-%m-%d %H:%M:%S', time());
				$ok = 1;
				$duration = $this->d13->getModule($this->data['faction'], $module['module'], 'removeDuration') * $this->d13->getGeneral('users', 'duration', 'build') * $this->getBuff('duration', 'build');
				$this->d13->dbQuery('insert into build (node, slot, obj_id, start, duration, action) values ("' . $this->data['id'] . '", "' . $slotId . '", "' . $module['module'] . '", "' . $start . '", "' . $duration . '", "remove")');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function addComponent($componentId, $quantity, $slotId, $auto=0)
	{
		
		$this->getModules();
		$this->getResources();
		$this->getTechnologies();
		$this->getComponents();
		$component = array();
		if (isset($this->components[$componentId])) {
			$okModule = 0;
			if (isset($this->modules[$slotId]['module']))
			if (in_array($componentId, $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'components'))) $okModule = 1;
			if ($okModule) {
				
				$args = array();
				$args['supertype'] = 'component';
				$args['id'] = $componentId;
				
				$tmp_component = $this->d13->createGameObject($args, $this);
			
				$component['requirementsData'] = $this->checkRequirements($this->d13->getComponent($this->data['faction'], $componentId, 'requirements') , $quantity);
				if ($component['requirementsData']['ok'])
				if ($this->resources[$this->d13->getComponent($this->data['faction'], $componentId, 'storageResource') ]['value'] >= $this->d13->getComponent($this->data['faction'], $componentId, 'storage') * $quantity) {
					$component['costData'] = $this->checkCost($this->d13->getComponent($this->data['faction'], $componentId, 'cost') , 'craft', $quantity);
					if ($component['costData']['ok']) {
						$ok = 1;
						$storageResource = $this->d13->getComponent($this->data['faction'], $componentId, 'storageResource');
						$this->resources[$storageResource]['value']-= $this->d13->getComponent($this->data['faction'], $componentId, 'storage') * $quantity;
						$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						
						foreach($tmp_component->data['cost'] as $cost) {
							$this->resources[$cost['resource']]['value']-= $cost['value'] * $quantity * $this->d13->getGeneral('users', 'efficiency', 'craft') * $this->getBuff('efficiency', 'craft');
							$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						}

						foreach($tmp_component->data['requirements'] as $cost) {
							if ($requirement['type'] == 'components') {
								$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
								$this->resources[$storageResource]['value']+= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $quantity;
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']-= $requirement['value'] * $quantity;
								$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}
						}
						
						$start = strftime('%Y-%m-%d %H:%M:%S', time());
						$duration = $this->d13->getComponent($this->data['faction'], $componentId, 'duration') * $quantity;
						
						$totalIR = $this->modules[$slotId]['input'] * $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'ratio');
						$duration = ($duration - $duration * $totalIR) * $this->d13->getGeneral('users', 'duration', 'craft') * $this->getBuff('duration', 'craft') * 60;
						
						$this->d13->dbQuery('insert into craft (node, obj_id, quantity, stage, start, duration, slot, auto) values ("' . $this->data['id'] . '", "' . $componentId . '", "' . $quantity . '", 0, "' . $start . '", "' . $duration . '", "' . $slotId . '", "' . $auto . '")');
						
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function removeComponent($componentId, $quantity, $moduleId, $slotId)
	{
		
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$component = array();
		if (isset($this->components[$componentId]))
		if ($this->components[$componentId]['value'] >= $quantity) {
			$ok = 1;
			$storageResource = $this->d13->getComponent($this->data['faction'], $componentId, 'storageResource');
			$this->resources[$storageResource]['value']+= $this->d13->getComponent($this->data['faction'], $componentId, 'storage') * $quantity;
			$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->components[$componentId]['value']-= $quantity;
			$this->d13->dbQuery('update components set value="' . $this->components[$componentId]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $componentId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			
			$start = strftime('%Y-%m-%d %H:%M:%S', time());
			$duration = $this->d13->getComponent($this->data['faction'], $componentId, 'removeDuration') * $quantity;
			
			$totalIR = $this->modules[$slotId]['input'] * $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'ratio');
			$duration = ($duration - $duration * $totalIR) * $this->d13->getGeneral('users', 'duration', 'craft') * $this->getBuff('duration', 'craft') * 60;
			
			$this->d13->dbQuery('insert into craft (node, obj_id, quantity, stage, start, duration, slot) values ("' . $this->data['id'] . '", "' . $componentId . '", "' . $quantity . '", 1, "' . $start . '", "' . $duration . '", "' . $slotId . '")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'notEnoughComponents';
		else $status = 'noComponent';
		return $status;
	}


	// ----------------------------------------------------------------------------------------
	// addUnit
	// ----------------------------------------------------------------------------------------

	public

	function addUnit($unitId, $quantity, $slotId, $auto=0)
	{
		
		$this->getModules();
		$this->getResources();
		$this->getTechnologies();
		$this->getComponents();
		$this->getUnits();
		$unit = array();
		if (isset($this->units[$unitId])) {
			$okModule = 0;
			if (isset($this->modules[$slotId]['module']))
			if (in_array($unitId, $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'units'))) $okModule = 1;
			if ($okModule) {
				$unit['requirementsData'] = $this->checkRequirements($this->d13->getUnit($this->data['faction'], $unitId, 'requirements') , $quantity);
				if ($unit['requirementsData']['ok'])
				if ($this->resources[$this->d13->getUnit($this->data['faction'], $unitId, 'upkeepResource') ]['value'] >= $this->d13->getUnit($this->data['faction'], $unitId, 'upkeep') * $quantity) {
					$unit['costData'] = $this->checkCost($this->d13->getUnit($this->data['faction'], $unitId, 'cost') , 'train', $quantity);
					if ($unit['costData']['ok']) {
						$ok = 1;
						$upkeepResource = $this->d13->getUnit($this->data['faction'], $unitId, 'upkeepResource');
						$this->resources[$upkeepResource]['value'] -= $this->d13->getUnit($this->data['faction'], $unitId, 'upkeep') * $quantity;
						$this->d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						foreach($this->d13->getUnit($this->data['faction'], $unitId, 'cost') as $cost) {
							$this->resources[$cost['resource']]['value'] -= $cost['value'] * $quantity * $this->d13->getGeneral('users', 'efficiency', 'train') * $this->getBuff('efficiency', 'train');
							$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						}

						foreach($this->d13->getUnit($this->data['faction'], $unitId, 'requirements') as $requirement) {
							if ($requirement['type'] == 'components') {
								$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
								$this->resources[$storageResource]['value'] += $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $quantity;
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($this->d13->dbAffectedRows() == - 1) {
									$ok = 0;
								}
								$this->components[$requirement['id']]['value'] -= $requirement['value'] * $quantity;
								$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) {
									$ok = 0;
								}
							}
						}
						
						$this->queues->getQueue('train', 'obj_id', $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'units'));
						
						$start = strftime('%Y-%m-%d %H:%M:%S', time());
						$duration = $this->d13->getUnit($this->data['faction'], $unitId, 'duration') * $quantity;
						
						$totalIR = $this->modules[$slotId]['input'] * $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'ratio');
						$duration = ($duration - $duration * $totalIR) * $this->d13->getGeneral('users', 'duration', 'train') * $this->getBuff('duration', 'train') * 60;
						
						$this->d13->dbQuery('insert into train (node, obj_id, quantity, stage, start, duration, slot, auto) values ("' . $this->data['id'] . '", "' . $unitId . '", "' . $quantity . '", 0, "' . $start . '", "' . $duration . '", "' . $slotId . '", "' . $auto . '")');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function removeUnit($unitId, $quantity, $moduleId, $slotId)
	{
		
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$this->getUnits();
		$unit = array();
		if (isset($this->units[$unitId]))
		if ($this->units[$unitId]['value'] >= $quantity) {
			$ok = 1;
			$upkeepResource = $this->d13->getUnit($this->data['faction'], $unitId, 'upkeepResource');
			$this->resources[$upkeepResource]['value']+= $this->d13->getUnit($this->data['faction'], $unitId, 'upkeep') * $quantity;
			$this->d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->units[$unitId]['value']-= $quantity;
			$this->d13->dbQuery('update units set value="' . $this->units[$unitId]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $unitId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->queues->getQueue('train', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId) ['units']);
			
			$start = strftime('%Y-%m-%d %H:%M:%S', time());
			$duration = $this->d13->getUnit($this->data['faction'], $unitId, 'removeDuration');
			
			$totalIR = $this->modules[$slotId]['input'] * $this->d13->getModule($this->data['faction'], $this->modules[$slotId]['module'], 'ratio');
			$duration = ($duration - $duration * $totalIR) * $this->d13->getGeneral('users', 'duration', 'train') * $this->getBuff('duration', 'train') * 60;
			
			$this->d13->dbQuery('insert into train (node, obj_id, quantity, stage, start, duration, slot) values ("' . $this->data['id'] . '", "' . $unitId . '", "' . $quantity . '", 1, "' . $start . '", "' . $duration . '", "' . $slotId . '")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	public static

	function getCombat($combatId)
	{
		
		$result = $this->d13->dbQuery('select * from combat where id="' . $combatId . '"');
		$combat = $this->d13->dbFetch($result);
		return $combat;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function addCombat($nodeId, $data, $type, $slotId)
	{
		
		$this->getResources();
		$this->getUnits();
		$this->getLocation();
		$node = $this->d13->createNode();
		$node->get('id', $nodeId);
		$okUnits = 1;
		
		$army = array();
		foreach($data['input']['attacker']['groups'] as $group) {
			if (isset($army[$group['unitId']])) {
				$army[$group['unitId']]+= $group['quantity'];
			} else {
				$army[$group['unitId']] = $group['quantity'];
			}
		}
		
		//- - - - - - - - - - - - get lowest speed and fuel requirement
		$totalFuel = 0;
		$speed = 999999999;
		foreach($this->units as $key => $group) {
			if (isset($army[$key])) {
				if ($army[$key] > $group['value']) {
					$okUnits = 0;
				} else if ($army[$key] > 0) {
					
					$args = array();
					$args['supertype'] = 'unit';
					$args['id'] = $key;
					
					$tmp_unit = $this->d13->createGameObject($args, $node);
					
					$totalFuel += $tmp_unit->data['fuel'] * $army[$key];
					if ($tmp_unit->data['speed'] < $speed) {
						$speed = $tmp_unit->data['speed'];
					}
				}
			}
		}
		
		//- - - - - - - - - - - - check fixed costs and fuel cost
		$combatCost 	= $this->d13->getFaction($this->data['faction'], 'costs', $type);
		$okCombatCost 	= $this->checkCost($combatCost, 'combat', 1, $totalFuel);
		//- - - - - - - - - - - - check shields (own and other)
		$otherShield 	= $node->getShield($type);
		$ownShield 		= $this->getShield('cannotAttack');

		//- - - - - - - - - - - - 
		if (!$otherShield && !$ownShield) {
			if ($okUnits) {
				if ($okCombatCost['ok']) {
					if ($node->get('id', $nodeId) == 'done') {
						
						$node->getLocation();
						$distance = sqrt(pow(abs($this->location['x'] - $node->location['x']) , 2) + pow(abs($this->location['y'] - $node->location['y']) , 2));
						$duration = ($distance * $this->d13->getGeneral('factors', 'distance')) / ($speed * $this->d13->getGeneral('users', 'duration', 'combat') * $this->getBuff('duration', 'combat'));
						$combatId = $this->d13->misc->newId('combat');
						$ok = 1;
						$cuBuffer = array();
			
						foreach($army as $key => $value) {
							$cuBuffer[] = '("' . $combatId . '", "' . $key . '", "' . $value . '")';
							$this->units[$key]['value']-= $value;
							$this->d13->dbQuery('update units set value="' . $this->units[$key]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $key . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							$upkeepResource = $this->d13->getUnit($this->data['faction'], $key, 'upkeepResource');
							$upkeep = $this->d13->getUnit($this->data['faction'], $key, 'upkeep');
							$this->resources[$upkeepResource]['value']+= $upkeep * $value;
							$this->d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						}
						
						// - - - - deduct required resources
						foreach ($combatCost as $cost) {
						
							if (isset($cost['isFuel']) && isset($cost['resource'])) {
						
								if ($cost['isFuel']) {
									$this->resources[$cost['resource']]['value'] -= $cost['value'] * $totalFuel * $this->d13->getGeneral('users', 'efficiency', 'combat') * $this->getBuff('efficiency', 'combat');
								} else {
									$this->resources[$cost['resource']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'combat') * $this->getBuff('efficiency', 'combat');
								}
							
								$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							
							} else if (isset($cost['component'])) {
									
								if (isset($cost['isFuel']) && $cost['isFuel']) {
									$this->components[$cost['component']]['value'] -= $cost['value'] * $totalFuel * $this->d13->getGeneral('users', 'efficiency', 'combat') * $this->getBuff('efficiency', 'combat');
								} else {
									$this->components[$cost['component']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'combat') * $this->getBuff('efficiency', 'combat');
								}
								
								$storageResource = $this->d13->getComponent($this->data['faction'], $cost['component'], 'storageResource');
								$this->resources[$storageResource]['value']+= $this->d13->getComponent($this->data['faction'], $cost['component'], 'storage') * $cost['value'];
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								
								$this->d13->dbQuery('update components set value="' . $this->components[$cost['component']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['component'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;

									
							}
						}

						$this->d13->dbQuery('insert into combat (id, sender, recipient, focus, stage, start, duration, type, slot) values ("' . $combatId . '", "' . $this->data['id'] . '", "' . $node->data['id'] . '", "' . $data['input']['attacker']['focus'] . '", "0", "' . strftime('%Y-%m-%d %H:%M:%S', time()) . '", "' . $duration . '", "' . $type . '", "' . $slotId . '")');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						$this->d13->dbQuery('insert into combat_units (combat, id, value) values ' . implode(', ', $cuBuffer));
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					
						if ($ok) {
							$status = 'done';
						} else {
							$status = 'error';
						}
		
					
					} else {
						$status = 'noNode';
					}
				} else {
					$status = 'notEnoughResources';
				}
			} else {
				$status = 'notEnoughUnits';
			}
		
		
		} else {
			$status = 'error';
		}
		
		return $status;
	}




	// ----------------------------------------------------------------------------------------
	// checkRequirements
	// ----------------------------------------------------------------------------------------
	public

	function checkRequirements($requirements, $quantity = 1)
	{
		
		$data = array(
			'ok' => 1,
			'requirements' => $requirements
		);
		foreach($data['requirements'] as $key => $requirement)
		if (isset($requirement['value']) || isset($requirement['level'])) switch ($requirement['type']) {

		// - - - - -
		case 'technologies':
			$this->getTechnologies();
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
	// checkCost
	// ----------------------------------------------------------------------------------------
	public

	function checkCost($cost, $costType, $quantity=1, $fuel=1)
	{
	
		
		
		$data = array(
			'ok' => 1,
			'cost' => $cost
		);

		foreach($cost as $key => $thecost) {
			
			$value = 0;
			$costObj = '';
			$totalcost = 0;
			
			if (isset($thecost['resource'])) {
				$costObj = 'resource';
				$value = $this->resources[$thecost[$costObj]]['value'];
			} else if (isset($thecost['component'])) {
				$costObj = 'component';
				$value = $this->components[$thecost[$costObj]]['value'];
			}
			
				if (isset($thecost['isFuel']) && $thecost['isFuel']) {
					$tmp_quantity = $quantity * $fuel;
				} else {
					$tmp_quantity = $quantity;
				}
			
				$totalcost = $thecost['value'] * $tmp_quantity * $this->d13->getGeneral('users', 'efficiency', $costType) * $this->getBuff('efficiency', $costType);
				
				if ($value < $totalcost) {
					$data['cost'][$key]['ok'] = 0;
					$data['ok'] = 0;
				} else {
					$data['cost'][$key]['ok'] = 1;
				}
			
		}
		
		return $data;
	}

	// ----------------------------------------------------------------------------------------
	// checkConvertedCost
	// ----------------------------------------------------------------------------------------
	public

	function checkConvertedCost($cost, $costType, $quantity=1, $fuel=1)
	{
	
		
		
		$pass = true;
		
		$value = 0;
		$costObj = '';
		$totalcost = 0;
		
		if (isset($cost['resource'])) {
			$costObj = 'resource';
			$value = $this->resources[$cost[$costObj]]['value'];
		} else if (isset($cost['component'])) {
			$costObj = 'component';
			$value = $this->components[$cost[$costObj]]['value'];
		}
		
		if (isset($cost['isFuel']) && $cost['isFuel']) {
			$tmp_quantity = $quantity * $fuel;
		} else {
			$tmp_quantity = $quantity;
		}
	
		$totalcost = $cost['value'] * $tmp_quantity * $this->d13->getGeneral('users', 'efficiency', $costType) * $this->getBuff('efficiency', $costType);
		
		if ($value < $totalcost) {
			$pass = false;
		}

		return $pass;
	}

	// ----------------------------------------------------------------------------------------
	// checkCostMax
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
				if ($limit < 10) {
					$limit++;
				} else {
					$limit+= 5;
				}
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
	// checkRequirementsMax
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
	// checkModuleDependencies
	// ----------------------------------------------------------------------------------------
	private
	
	function checkModuleDependencies($moduleId, $slotId, $useOldIR = 0)
	{
		
		switch ($this->d13->getModule($this->data['faction'], $moduleId) ['type']) {
		case 'research':
			$this->queues->getQueue('research', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId) ['technologies']);
			$nr = count($this->queues->queue['research']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queues->queue['research'][$i]['start'] = $this->queues->queue['research'][$i - 1]['start'] + floor($this->queues->queue['research'][$i - 1]['duration'] * 60);
					$this->queues->queue['research'][$i]['duration'] = $this->d13->getTechnology($this->data['faction'], $this->queues->queue['research'][$i]['technology'], 'duration');
					$this->queues->queue['research'][$i]['duration'] = ($this->queues->queue['research'][$i]['duration'] - $this->queues->queue['research'][$i]['duration'] * $newIR) * $this->d13->getGeneral('users', 'duration', 'research') * $this->getBuff('duration', 'research');
					$this->d13->dbQuery('update research set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queues->queue['research'][$i]['start']) . '", duration="' . $this->queues->queue['research'][$i]['duration'] . '" where node="' . $this->queues->queue['research'][$i]['node'] . '" and technology="' . $this->queues->queue['research'][$i]['technology'] . '"');
					if (!$moduleCount) $this->cancelTechnology($this->queues->queue['research'][$i]['technology'], $moduleId);
				}
			}

			break;

		case 'craft':
			$this->queues->getQueue('craft', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId) ['components']);
			$nr = count($this->queues->queue['craft']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queues->queue['craft'][$i]['start'] = $this->queues->queue['craft'][$i - 1]['start'] + floor($this->queues->queue['craft'][$i - 1]['duration'] * 60);
					$this->queues->queue['craft'][$i]['duration'] = $this->d13->getComponent($this->data['faction'], $this->queues->queue['craft'][$i]['component'], 'duration') * $this->queues->queue['craft'][$i]['quantity'];
					$this->queues->queue['craft'][$i]['duration'] = ($this->queues->queue['craft'][$i]['duration'] - $this->queues->queue['craft'][$i]['duration'] * $newIR) * $this->d13->getGeneral('users', 'duration', 'craft') * $this->getBuff('duration', 'craft');
					$this->d13->dbQuery('update craft set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queues->queue['craft'][$i]['start']) . '", duration="' . $this->queues->queue['craft'][$i]['duration'] . '" where id="' . $this->queues->queue['craft'][$i]['id'] . '"');
					if (!$moduleCount) $this->cancelComponent($this->queues->queue['craft'][$i]['id'], $moduleId);
				}
			}

			break;

		case 'train':
			$this->queues->getQueue('train', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId) ['units']);
			$nr = count($this->queues->queue['train']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queues->queue['train'][$i]['start'] = $this->queues->queue['train'][$i - 1]['start'] + floor($this->queues->queue['train'][$i - 1]['duration'] * 60);
					$this->queues->queue['train'][$i]['duration'] = $this->d13->getUnit($this->data['faction'], $this->queues->queue['train'][$i]['unit'], 'duration') * $this->queues->queue['train'][$i]['quantity'];
					$this->queues->queue['train'][$i]['duration'] = ($this->queues->queue['train'][$i]['duration'] - $this->queues->queue['train'][$i]['duration'] * $newIR) * $this->d13->getGeneral('users', 'duration', 'train') * $this->getBuff('duration', 'train');
					$this->d13->dbQuery('update train set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queues->queue['train'][$i]['start']) . '", duration="' . $this->queues->queue['train'][$i]['duration'] . '" where id="' . $this->queues->queue['train'][$i]['id'] . '"');
					if (!$moduleCount) $this->cancelComponent($this->queues->queue['train'][$i]['id'], $moduleId);
				}
			}

			break;

		case 'trade':
			$this->queues->getQueue('trade');
			$nr = count($this->queues->queue['trade']);
			if ($nr) {
				$newIR = $oldIR = 0;
				$moduleCount = 0;
				foreach($this->modules as $key => $module)
				if ($module['module'] == $moduleId) {
					if ($module['slot'] != $slotId) $newIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$oldIR+= $module['input'] * $this->d13->getModule($this->data['faction'], $module['module'], 'ratio');
					$moduleCount++;
				}

				if ($useOldIR) $newIR = $oldIR;
				for ($i = 0; $i < $nr; $i++) {
					if ($i) $this->queues->queue['trade'][$i]['start'] = $this->queues->queue['trade'][$i - 1]['start'] + floor($this->queues->queue['trade'][$i - 1]['duration'] * 60);
					$this->queues->queue['trade'][$i]['duration'] = $this->d13->getGeneral('users', 'duration', 'trade') * $this->getBuff('duration', 'trade') * $this->queues->queue['trade'][$i]['distance'];
					$this->queues->queue['trade'][$i]['duration'] = $this->queues->queue['trade'][$i]['duration'] - $this->queues->queue['trade'][$i]['duration'] * $newIR;
					$this->d13->dbQuery('update trade set start="' . strftime('%Y-%m-%d %H:%M:%S', $this->queues->queue['trade'][$i]['start']) . '", duration="' . $this->queues->queue['trade'][$i]['duration'] . '" where id="' . $this->queues->queue['trade'][$i]['id'] . '"');

					// if (!$moduleCount) $this->cancelTrade($this->queues->queue['trade'][$i]['id'], $moduleId);

				}
			}

			break;
		}
	}

	// ----------------------------------------------------------------------------------------
	// move
	// move node to new location on grid
	// ----------------------------------------------------------------------------------------
	public

	function move($x, $y)
	{
		
		$this->getModules();
		$this->getResources();
		$this->getLocation();
		$moveCost = $this->d13->getFaction($this->data['faction'], 'costs', 'move');
		$distance = ceil(sqrt(pow($this->location['x'] - $x, 2) + pow($this->location['y'] - $y, 2)));
		$moveCostData = $this->checkCost($moveCost, 'move');
		if ($moveCostData['ok']) {
			$node = $this->d13->createNode();
			if ($node->get('id', $this->data['id']) == 'done') {
				$sector = d13_grid::getSector($x, $y);
				if ($sector['type'] == 1) {
					$ok = 1;
					$this->d13->dbQuery('update grid set type="1", id=floor(1+rand()*9) where x="' . $this->location['x'] . '" and y="' . $this->location['y'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					$this->location['x'] = $x;
					$this->location['y'] = $y;
					$this->d13->dbQuery('update grid set type="2", id="' . $this->data['id'] . '" where x="' . $this->location['x'] . '" and y="' . $this->location['y'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					foreach($moveCost as $cost) {
						$this->resources[$cost['resource']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'move') * $this->getBuff('efficiency', 'move');
						$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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
	
	// ========================================================================================
	//										GET FUNCTIONS
	// ========================================================================================

	// ----------------------------------------------------------------------------------------
	// getAll
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
		$this->getBuffs();
	}

	// ----------------------------------------------------------------------------------------
	// getLocation
	// ----------------------------------------------------------------------------------------
	public

	function getLocation()
	{
		
		$result = $this->d13->dbQuery('select x, y from grid where type="2" and id="' . $this->data['id'] . '"');
		$row = $this->d13->dbFetch($result);
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
		
		$this->resources = array();
		$this->production = array();
		$this->storage = array();
		$tmp_resources = array();
		
		$this->getModules();
		
		$result = $this->d13->dbQuery('select * from resources where node="' . $this->data['id'] . '" order by id asc');
		
		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) {
			$tmp_resources[$i] = $row;
		}
		
		foreach($this->d13->getResource() as $resource) {
			$this->production[$resource['id']] = 0;
			$this->production[$resource['id']] += $this->d13->getResource($resource['id'], 'autoproduction');
			$this->storage[$resource['id']] = $this->d13->getResource($resource['id'], 'storage');
			$this->resources[$resource['id']] = $tmp_resources[$resource['id']];
		}
		
		if ($this->modules) {
			foreach($this->modules as $module) {
				if ($module['module'] > - 1) {
					$tmp_module = $this->d13->createModule($module['module'], $module['slot'], $this);
					if ($this->d13->getModule($this->data['faction'], $module['module'], 'storedResource')) {
						foreach($this->d13->getModule($this->data['faction'], $module['module'], 'storedResource') as $res) {
							$this->storage[$res] += $tmp_module->data['ratio'] * $this->d13->getGeneral('users', 'efficiency', 'storage') * $this->getBuff('efficiency', 'storage') * $module['input'];
						}
					}

					if ($this->d13->getModule($this->data['faction'], $module['module'], 'outputResource')) {
						foreach($this->d13->getModule($this->data['faction'], $module['module'], 'outputResource') as $res) {
							$this->production[$res] += $tmp_module->data['ratio'] * $this->d13->getGeneral('users', 'efficiency', 'harvest') * $this->getBuff('efficiency', 'harvest') * $module['input'];
						}
					}
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// getBuffs
	// ----------------------------------------------------------------------------------------
	public

	function getBuffs()
	{
	
		
		
		if (!$this->updated['buffs']) {
			$result = $this->d13->dbQuery('select * from buff where node="' . $this->data['id'] . '" order by id asc');
			for ($i = 0; $row = $this->d13->dbFetch($result); $i++) $this->buffs[$i] = $row;
			$this->updated['buffs'] = true;
		}
	
	}

	// ----------------------------------------------------------------------------------------
	// getTechnologies
	// ----------------------------------------------------------------------------------------
	public

	function getTechnologies()
	{
	
		
		
		if (!$this->updated['technologies']) {
			$result = $this->d13->dbQuery('select * from technologies where node="' . $this->data['id'] . '" order by id asc');
			for ($i = 0; $row = $this->d13->dbFetch($result); $i++) $this->technologies[$i] = $row;
			$this->updated['technologies'] = true;
		}

	}

	// ----------------------------------------------------------------------------------------
	// getModules
	// ----------------------------------------------------------------------------------------
	public

	function getModules()
	{
	
		
		
		if (!$this->updated['modules']) {
			$result = $this->d13->dbQuery('select * from modules where node="' . $this->data['id'] . '" order by slot asc');
			while ($row = $this->d13->dbFetch($result)) $this->modules[$row['slot']] = $row;
			$this->updated['modules'] = true;
		}
		
	}

	// ----------------------------------------------------------------------------------------
	// getComponents
	// ----------------------------------------------------------------------------------------
	public

	function getComponents()
	{
	
		
		
		if (!$this->updated['components']) {
			$result = $this->d13->dbQuery('select * from components where node="' . $this->data['id'] . '" order by id asc');
			for ($i = 0; $row = $this->d13->dbFetch($result); $i++) $this->components[$i] = $row;
			$this->updated['components'] = true;
		}

	}

	// ----------------------------------------------------------------------------------------
	// getUnits
	// ----------------------------------------------------------------------------------------
	public

	function getUnits()
	{
	
		
		
		if (!$this->updated['units']) {
			$result = $this->d13->dbQuery('select * from units where node="' . $this->data['id'] . '" order by id asc');
			while ($row = $this->d13->dbFetch($result)) $this->units[$row['id']] = $row;
			$this->updated['units'] = true;
		}
		
	}

	// ----------------------------------------------------------------------------------------
	// getBuff
	// ----------------------------------------------------------------------------------------
	public
	
	function getBuff($type, $buff, $nofactor=false)
	{

		
		
		if ($nofactor) {
			$value = 0;
		} else {
			$value = 1;
		}
		
		$this->checkBuff(time());
		$this->queues->getQueue('buff');
		
		foreach($this->queues->queue['buff'] as $entry) {
			if ($this->d13->getBuff($entry['obj_id'])) {
				$tmp_buff = $this->d13->getBuff($entry['obj_id']);
				if ($tmp_buff['type'] == $type && $tmp_buff['buff'] == $buff) {
					$value += $tmp_buff['modifier'];
				}
			}
		}
		
		return $value;
		
	}

	// ----------------------------------------------------------------------------------------
	// getShield
	// ----------------------------------------------------------------------------------------
	public
	
	function getShield($type)
	{
	
		
	
		$this->checkShield(time());
		$this->queues->getQueue('shield');

		foreach($this->queues->queue['shield'] as $entry) {
			if ($this->d13->getShield($entry['obj_id'], $type)) {
				return true;
			} else {
				return false;
			}
		}

		return false;

	}

	// ----------------------------------------------------------------------------------------
	// getMarket
	// ----------------------------------------------------------------------------------------
	public
	
	function getMarket($slot)
	{

		
		
		$inventory = array();
		
		$result = $this->d13->dbQuery('select * from market where node="' . $this->data['id'] . '" and slot="' . $slot . '" order by id asc');
		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) {
			$inventory[] = $row;
		}
		
		return $inventory;
		
	}


	
	
	
	// ========================================================================================
	//										CANCEL FUNCTIONS
	// ========================================================================================

	// ----------------------------------------------------------------------------------------
	// cancelBuff
	// ----------------------------------------------------------------------------------------
	public

	function cancelBuff($buffId)
	{
		
		
		$result = $this->d13->dbQuery('select * from buff where node="' . $this->data['id'] . '" and obj_id="' . $buffId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$ok = 1;
			$this->d13->dbQuery('delete from buff where node="' . $this->data['id'] . '" and obj_id="' . $buffId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		} else {
			$status = 'noEntry';
		}
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	// cancelShield
	// ----------------------------------------------------------------------------------------
	public

	function cancelShield($shieldId)
	{
		
		
		$result = $this->d13->dbQuery('select * from shield where node="' . $this->data['id'] . '" and obj_id="' . $shieldId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$ok = 1;
			$this->d13->dbQuery('delete from shield where node="' . $this->data['id'] . '" and obj_id="' . $shieldId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noEntry';
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	// cancelMarket
	// ----------------------------------------------------------------------------------------
	public

	function cancelMarket($slotId)
	{
		
		
		$result = $this->d13->dbQuery('select * from market where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$ok = 1;
			$this->d13->dbQuery('delete from market where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		} else {
			$status = 'noEntry';
		}
		
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function cancelTechnology($technologyId, $moduleId)
	{
		
		$this->getResources();
		$this->getComponents();
		$result = $this->d13->dbQuery('select * from research where node="' . $this->data['id'] . '" and obj_id="' . $technologyId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			foreach($this->d13->getTechnology($this->data['faction'], $entry['obj_id'], 'cost') as $cost) {
				$this->resources[$cost['resource']]['value'] += $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'research') * $this->getBuff('efficiency', 'research');
				$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			foreach($this->d13->getTechnology($this->data['faction'], $entry['obj_id'], 'requirements') as $requirement)
			if ($requirement['type'] == 'components') {
				$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
				$this->resources[$storageResource]['value']-= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
				$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				$this->components[$requirement['id']]['value']+= $requirement['value'];
				$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->queues->getQueue('research', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId) ['technologies']);
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queues->queue['research'] as $queueEntry)
			if ($queueEntry['start'] > $entry['start']) {
				$this->d13->dbQuery('update research set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where node="' . $this->data['id'] . '" and obj_id="' . $queueEntry['technology'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->d13->dbQuery('delete from research where node="' . $this->data['id'] . '" and obj_id="' . $technologyId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function cancelModule($slotId)
	{
		
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		$result = $this->d13->dbQuery('select * from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			$tmp_module = $this->d13->createModule($entry['module'], $slotId, $this);
			
			if ($this->modules[$slotId == - 1]) {
			
				foreach($tmp_module->data['cost'] as $cost) {
					$this->resources[$cost['resource']]['value'] += $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'build') * $this->getBuff('efficiency', 'build');
					$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}

				foreach($this->d13->getModule($this->data['faction'], $entry['module'], 'requirements') as $requirement)
				if ($requirement['type'] == 'components') {
					$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
					$this->resources[$storageResource]['value']-= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
					$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					$this->components[$requirement['id']]['value']+= $requirement['value'];
					$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}
			
			$input = $tmp_module->data['input'];
			$this->resources[$tmp_module->data['inputResource']]['value'] -= $input;
			$this->d13->dbQuery('update resources set value=value+"' . $this->resources[$tmp_module->data['inputResource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $tmp_module->data['inputResource'] . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('update modules set input=input-"' . $input . '" where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;

			
			$this->queues->getQueue('build');
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queues->queue['build'] as $queueEntry) {
				if ($queueEntry['start'] > $entry['start']) {
					$this->d13->dbQuery('update build set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where node="' . $this->data['id'] . '" and slot="' . $queueEntry['slot'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}

			$this->d13->dbQuery('delete from build where node="' . $this->data['id'] . '" and slot="' . $slotId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function cancelComponent($craftId, $moduleId)
	{
		
		$this->getResources();
		$this->getComponents();
		$result = $this->d13->dbQuery('select * from craft where id="' . $craftId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			$storageResource = $this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'storageResource');
			$storage = $this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'storage') * $entry['quantity'];
			if (!$entry['stage']) $this->resources[$storageResource]['value']+= $storage;
			else $this->resources[$storageResource]['value']-= $storage;
			$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if (!$entry['stage']) {
			
				foreach($this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'cost') as $cost) {
					$this->resources[$cost['resource']]['value'] += $cost['value'] * $entry['quantity'] * $this->d13->getGeneral('users', 'efficiency', 'craft') * $this->getBuff('efficiency', 'craft');
					$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}

				foreach($this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'requirements') as $requirement)
				if ($requirement['type'] == 'components') {
					$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
					$this->resources[$storageResource]['value'] -= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
					$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					$this->components[$requirement['id']]['value'] += $requirement['value'] * $entry['quantity'];
					$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}
			else {
				$this->components[$entry['obj_id']]['value']+= $entry['quantity'];
				$this->d13->dbQuery('update components set value="' . $this->components[$entry['obj_id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['obj_id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->queues->getQueue('craft', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId, 'components'));
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queues->queue['craft'] as $queueEntry) {
				if ($queueEntry['start'] > $entry['start']) {
					$this->d13->dbQuery('update craft set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where id="' . $queueEntry['id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}

			$this->d13->dbQuery('delete from craft where id="' . $craftId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function cancelUnit($trainId, $moduleId)
	{
		
		$this->getResources();
		$this->getComponents();
		$this->getUnits();
		$result = $this->d13->dbQuery('select * from train where id="' . $trainId . '"');
		$entry = $this->d13->dbFetch($result);
		if (isset($entry['start'])) {
			$entry['start'] = strtotime($entry['start']);
			$ok = 1;
			$upkeepResource = $this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'upkeepResource');
			$upkeep = $this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'upkeep') * $entry['quantity'];
			if (!$entry['stage']) $this->resources[$upkeepResource]['value']+= $upkeep;
			else $this->resources[$upkeepResource]['value']-= $upkeep;
			$this->d13->dbQuery('update resources set value="' . $this->resources[$upkeepResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $upkeepResource . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if (!$entry['stage']) {
				foreach($this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'cost') as $cost) {
					$this->resources[$cost['resource']]['value'] += $cost['value'] * $entry['quantity'] * $this->d13->getGeneral('users', 'efficiency', 'train') * $this->getBuff('efficiency', 'train');
					$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}

				foreach($this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'requirements') as $requirement) {
					if ($requirement['type'] == 'components') {
						$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
						$this->resources[$storageResource]['value']-= $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
						$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
						if ($this->d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}
						$this->components[$requirement['id']]['value'] += $requirement['value'] * $entry['quantity'];
						$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}
					}
				}
			}
			else {
				$this->units[$entry['obj_id']]['value']+= $entry['quantity'];
				$this->d13->dbQuery('update units set value="' . $this->units[$entry['obj_id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['obj_id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->queues->getQueue('train', 'obj_id', $this->d13->getModule($this->data['faction'], $moduleId) ['units']);
			$entry['duration'] = floor($entry['duration'] * 60);
			foreach($this->queues->queue['train'] as $queueEntry) {
				if ($queueEntry['start'] > $entry['start']) {
					$this->d13->dbQuery('update train set start="' . strftime('%Y-%m-%d %H:%M:%S', $queueEntry['start'] - $entry['duration']) . '" where id="' . $queueEntry['id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}
			}

			$this->d13->dbQuery('delete from train where id="' . $trainId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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

	function cancelCombat($combatId)
	{
		
		$result = $this->d13->dbQuery('select * from combat where stage=0 and id="' . $combatId . '"');
		$row = $this->d13->dbFetch($result);
		if (isset($row['id'])) {
			$elapsed = (time() - strtotime($row['start'])) / 60;
			$start = strftime('%Y-%m-%d %H:%M:%S', time());
			$this->d13->dbQuery('update combat set stage=1, start="' . $start . '", duration="' . $elapsed . '" where id="' . $combatId . '"');
			if ($this->d13->dbAffectedRows() == - 1) $status = 'error';
			else $status = 'done';
		}
		else $status = 'noCombat';
		return $status;
	}


	// ========================================================================================
	//										SET FUNCTIONS
	// ========================================================================================

	// ----------------------------------------------------------------------------------------
	// setNode
	// Update Node data to DB
	// ----------------------------------------------------------------------------------------
	public

	function set()
	{
		
		
		$this->getResources();
		$setCost = $this->d13->getFaction($this->data['faction'], 'costs', 'set');
		$setCostData = $this->checkCost($setCost, 'set');
		if ($setCostData['ok']) {
			$node = $this->d13->createNode();
			if ($node->get('id', $this->data['id']) == 'done')
			if (($node->data['name'] == $this->data['name']) || ($node->get('name', $this->data['name']) == 'noNode')) {
				$ok = 1;
				foreach($setCost as $cost) {
					$this->resources[$cost['resource']]['value'] -= $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'set') * $this->getBuff('efficiency', 'set');
					$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				}

				$this->d13->dbQuery('update nodes set name="' . $this->data['name'] . '", focus="' . $this->data['focus'] . '" where id="' . $this->data['id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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
	// setResources
	// Alter one or more resources and update data to DB
	// ----------------------------------------------------------------------------------------
	public

	function setResources($resources, $add=-1)
	{
		
		
		$ok = 1;
		$this->getResources();

		foreach ($resources as $res) {
			$this->resources[$res['resource']]['value'] += ($res['value'] * $add);
			$this->d13->dbQuery('update resources set value="' . $this->resources[$res['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $res['resource'] . '"');
			if ($this->d13->dbAffectedRows() == - 1) {
				$ok = 0;
			}
		}
		
		return $ok;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// setTechnology
	// Add a specific technologyl level to the node and udpate data to DB
	// ----------------------------------------------------------------------------------------
	public
	
	function setTechnology($id)
	{
		$this->technologies[$id]['level']++;
		$this->d13->dbQuery('update technologies set level="' . $this->technologies[$id]['level'] . '" where node="' . $this->data['id'] . '" and id="' . $id . '"');
		if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
		return $ok;
	}
	
	// ----------------------------------------------------------------------------------------
	// setComponent
	// Add a specific amount of components to the node and update data to DB
	// ----------------------------------------------------------------------------------------
	public

	function setComponent($id, $amount)
	{
		$this->components[$id]['value'] += $amount;
		$this->d13->dbQuery('update components set value="' . $this->components[$id]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $id . '"');
		if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
		return $ok;
	}
	
	// ----------------------------------------------------------------------------------------
	// setUnit
	// Add a specific amount of units to the node and update data to DB
	// ----------------------------------------------------------------------------------------
	public

	function setUnit($id, $amount)
	{
		$this->units[$id]['value'] += $amount;
		$this->d13->dbQuery('update units set value="' . $this->units[$id]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $id . '"');
		if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
		return $ok;
	}

	// ----------------------------------------------------------------------------------------
	// setBuff
	// Add a new buff to the node and update data to DB
	// ----------------------------------------------------------------------------------------
	public
	
	function setBuff($buffId)
	{
		
		
		$ok 		= 1;
		$duration 	= $this->d13->getBuff($buffId, 'duration') *  $this->d13->getGeneral('users', 'duration', 'buff') * $this->getBuff('duration', 'buff') * 60;
		$start 		= strftime('%Y-%m-%d %H:%M:%S', time());
		
		$this->d13->dbQuery('insert into buff (node, obj_id, start, duration) values ("' . $this->data['id'] . '", "' . $buffId . '", "' . $start . '", "' . $duration . '")');
		
		if ($this->d13->dbAffectedRows() == - 1) {
			$ok = 0;
		}
		
		if ($ok) {
			$status = 'done';
		} else {
			$status = 'error';
		}
		
		return $status;

	}
	
	// ----------------------------------------------------------------------------------------
	// setShield
	// Add a new shield to the node and update data to DB
	// ----------------------------------------------------------------------------------------
	public
	
	function setShield($shieldId)
	{
		
		
		$ok 		= 1;
		$duration 	= $this->d13->getShield($shieldId, 'duration') *  $this->d13->getGeneral('users', 'duration', 'shield') * $this->getBuff('duration', 'shield') * 60;
		$start 		= strftime('%Y-%m-%d %H:%M:%S', time());
		
		$this->d13->dbQuery('insert into shield (node, obj_id, start, duration) values ("' . $this->data['id'] . '", "' . $shieldId . '", "' . $start . '", "' . $duration . '")');
		
		if ($this->d13->dbAffectedRows() == - 1) {
			$ok = 0;
		}
		
		if ($ok) {
			$status = 'done';
		} else {
			$status = 'error';
		}
		
		return $status;

	}	
	
	// ========================================================================================
	//								CHECK FUNCTIONS
	// ========================================================================================
	
	// ----------------------------------------------------------------------------------------
	// checkOptions
	// Scan all modules on this node and determine if a specific option is available
	// ----------------------------------------------------------------------------------------
	public

	function checkOptions($option)
	{
		
		$this->getModules();
		foreach($this->modules as $module) {
			if ($module['level'] > 0) {
				$options = $this->d13->getModule($this->data['faction'], $module['module'], 'options');
				if (isset($options[$option])) {
					return $options[$option];
				}
			}
		}

		return FALSE;
	}
	
	// ----------------------------------------------------------------------------------------
	// checkAll
	// ----------------------------------------------------------------------------------------
	public

	function checkAll($time)
	{
		$this->checkResources($time);
		$this->checkResearch($time);
		$this->checkBuild($time);
		$this->checkCraft($time);
		$this->checkTrain($time);
		$this->checkShield($time);
		$this->checkBuff($time);
		$this->checkMarket($time);
		// $this->checkTrade($time);
		$this->checkCombat($time);
	}

	// ----------------------------------------------------------------------------------------
	// checkResources
	// ----------------------------------------------------------------------------------------
	public

	function checkResources($time)
	{
		
		#$this->d13->dbQuery('start transaction');
		$this->getModules();
		$this->getResources();
		$elapsed = ($time - strtotime($this->data['lastCheck'])) / 3600;
		$ok = 1;
		foreach($this->d13->getResource() as $resource) {
			if ($resource['active'] && $resource['type'] == 'dynamic') {
				$this->resources[$resource['id']]['value']+= $this->production[$resource['id']] * $elapsed;
				if ($this->storage[$resource['id']]) {
				
					if ($resource['limited'] == true && $this->resources[$resource['id']]['value'] > $this->storage[$resource['id']]) {
						$this->resources[$resource['id']]['value'] = $this->storage[$resource['id']];
					}

					$this->d13->dbQuery('update resources set value="' . $this->resources[$resource['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $resource['id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) {
						$ok = 0;
					}

					
				}
			}
		}
		
		$this->d13->dbQuery('update nodes set lastCheck="' . strftime('%Y-%m-%d %H:%M:%S', $time) . '" where id="' . $this->data['id'] . '"');
		if ($this->d13->dbAffectedRows() == - 1) {
			$ok = 0;
		}
		
		#if ($ok) {
		#	$this->d13->dbQuery('commit');
		#}
		#else {
		#	$this->d13->dbQuery('rollback');
		#}
	}
	
	// ----------------------------------------------------------------------------------------
	// checkBuff
	// ----------------------------------------------------------------------------------------
	public

	function checkBuff($time)
	{

		
		
		$this->queues->getQueue('buff');
		
		#$this->d13->dbQuery('start transaction');
		
		
		$ok = 1;
		foreach($this->queues->queue['buff'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			if ($entry['end'] <= $time) {
				$this->d13->dbQuery('delete from buff where node="' . $this->data['id'] . '" and obj_id="' . $entry['obj_id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		#if ($ok) $this->d13->dbQuery('commit');
		#else $this->d13->dbQuery('rollback');

	}
	
	// ----------------------------------------------------------------------------------------
	// checkShield
	// ----------------------------------------------------------------------------------------
	public

	function checkShield($time)
	{

		
		#$this->d13->dbQuery('start transaction');
		
		$this->queues->getQueue('shield');
		$ok = 1;
		foreach($this->queues->queue['shield'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			if ($entry['end'] <= $time) {
				$this->d13->dbQuery('delete from shield where node="' . $this->data['id'] . '" and obj_id="' . $entry['obj_id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		#if ($ok) $this->d13->dbQuery('commit');
		#else $this->d13->dbQuery('rollback');

	}

	// ----------------------------------------------------------------------------------------
	// checkMarket
	// ----------------------------------------------------------------------------------------
	public

	function checkMarket($time)
	{

		
		#$this->d13->dbQuery('start transaction');
		
		$this->queues->getQueue('market');
		$ok = 1;
		
		foreach($this->queues->queue['market'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			if ($entry['end'] <= $time) {
				$this->d13->dbQuery('delete from market where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		#if ($ok) $this->d13->dbQuery('commit');
		#else $this->d13->dbQuery('rollback');

	}

	// ----------------------------------------------------------------------------------------
	// checkResearch
	// ----------------------------------------------------------------------------------------
	public

	function checkResearch($time)
	{
		
		#$this->d13->dbQuery('start transaction');
		$this->getTechnologies();
		$this->queues->getQueue('research');
		$ok = 1;
		foreach($this->queues->queue['research'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			if ($entry['end'] <= $time) {
				$this->technologies[$entry['obj_id']]['level']++;
				$this->d13->dbQuery('update technologies set level="' . $this->technologies[$entry['obj_id']]['level'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['obj_id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				$this->d13->dbQuery('delete from research where node="' . $this->data['id'] . '" and obj_id="' . $entry['obj_id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				if ($ok) { #experience gain
					$tmp_user = $this->d13->createObject('user', $_SESSION[CONST_PREFIX . 'User']['id']);
					$ok = $tmp_user->gainExperience($this->d13->getTechnology($this->data['faction'],  $entry['obj_id'], 'cost'), $this->technologies[$entry['obj_id']]['level']);
				}
			}
		}

		#if ($ok) $this->d13->dbQuery('commit');
		#else $this->d13->dbQuery('rollback');
	}

	// ----------------------------------------------------------------------------------------
	// checkBuild
	// ----------------------------------------------------------------------------------------
	public

	function checkBuild($time)
	{
		
		$this->d13->dbQuery('start transaction');
		$this->getModules();
		$this->getResources();
		$this->getComponents();
		
		$this->queues->getQueue('build');
		
		$ok = 1;

		foreach($this->queues->queue['build'] as $entry) {
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			if ($entry['end'] <= $time) {
				
				$tmp_user = $this->d13->createObject('user', $_SESSION[CONST_PREFIX . 'User']['id']);
				
				// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - BUILD

				if ($entry['action'] == 'build' && $this->modules[$entry['slot']]['module'] == - 1) {
					$this->modules[$entry['slot']]['obj_id'] = $entry['obj_id'];
					$this->d13->dbQuery('update modules set module="' . $entry['obj_id'] . '", level=1 where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					if ($ok) { #experience gain
						$ok = $tmp_user->gainExperience($this->d13->getModule($this->data['faction'],  $entry['obj_id'], 'cost'), 1);
					}
				
					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - UPGRADE

				} else if ($entry['action'] == 'upgrade' && $this->modules[$entry['slot']]['module'] > - 1) {
					$this->modules[$entry['slot']]['obj_id'] = $entry['obj_id'];
					$this->d13->dbQuery('update modules set module="' . $entry['obj_id'] . '", level=level+1 where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					if ($ok) { #experience gain
						$ok = $tmp_user->gainExperience($this->d13->getModule($this->data['faction'],  $entry['obj_id'], 'cost'), $this->modules[$entry['obj_id']]['level']+1);
					}
				
					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - REMOVE

				} else if ($entry['action'] == 'remove') {
				
					foreach($this->d13->getModule($this->data['faction'], $entry['obj_id'], 'cost') as $cost) {
						$this->resources[$cost['resource']]['value'] += $cost['value'] * $this->d13->getGeneral('users', 'efficiency', 'build') * $this->getBuff('efficiency', 'build') * $this->d13->getModule($this->data['faction'], $entry['obj_id'], 'salvage');
						$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					}

					foreach($this->d13->getModule($this->data['faction'], $entry['obj_id'], 'requirements') as $requirement) {
						if ($requirement['type'] == 'components') {
							$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$storage = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'];
							if ($this->resources[$storageResource]['value'] - $storage >= 0) {
								$this->resources[$storageResource]['value']-= $storage;
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']+= $requirement['value'];
								$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}
						}

						if ($this->modules[$entry['slot']]['input'] > 0) {
							$inputResource = $this->d13->getModule($this->data['faction'], $entry['obj_id'], 'inputResource');
							$this->resources[$inputResource]['value']+= $this->modules[$entry['slot']]['input'];
							$this->d13->dbQuery('update resources set value="' . $this->resources[$inputResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $inputResource . '"');
							if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						}

					}
				
					$this->modules[$entry['slot']]['module'] = - 1;
					$this->checkModuleDependencies($entry['obj_id'], $entry['slot']);
					$this->d13->dbQuery('update modules set module="-1", input="0", level="0" where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						
					if ($ok) { #experience loss
						$ok = $tmp_user->gainExperience($this->d13->getModule($this->data['faction'],  $entry['obj_id'], 'cost'), -$this->modules[$entry['obj_id']]['level']);
					}
				
				}

				$this->d13->dbQuery('delete from build where node="' . $this->data['id'] . '" and slot="' . $entry['slot'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}
		
		if ($ok) {
			$this->d13->dbQuery('commit');
		}
		else {
			$this->d13->dbQuery('rollback');
		}
	}

	// ----------------------------------------------------------------------------------------
	// checkCraft
	// ----------------------------------------------------------------------------------------
	public

	function checkCraft($time)
	{
		
		
		$this->d13->dbQuery('start transaction');
		$this->getResources();
		$this->getComponents();
		$this->queues->getQueue('craft');
		$ok = 1;
		
		foreach($this->queues->queue['craft'] as $entry) {
		
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			
			if ($entry['end'] <= $time) {
				if (!$entry['stage']) {
					$this->components[$entry['obj_id']]['value']+= $entry['quantity'];
					$this->d13->dbQuery('update components set value="' . $this->components[$entry['obj_id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['obj_id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				} else {
				
					foreach($this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'cost') as $cost) {
						$this->resources[$cost['resource']]['value']+= $cost['value'] * $entry['quantity'] * $this->d13->getGeneral('users', 'efficiency', 'craft') * $this->getBuff('efficiency', 'craft') * $this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'salvage');
						$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}
					}

					foreach($this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'requirements') as $requirement) {
						if ($requirement['type'] == 'components') {
							$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$storage = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
							if ($this->resources[$storageResource]['value'] - $storage >= 0) {
								$this->resources[$storageResource]['value']-= $storage;
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']+= $requirement['value'] * $entry['quantity'];
								$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}
						}
					}
					
				}
				
				if ($ok && $this->d13->getComponent($this->data['faction'], $entry['obj_id'], 'gainExperience')) { #experience gain
					$tmp_user = $this->d13->createObject('user', $_SESSION[CONST_PREFIX . 'User']['id']);
					$ok = $tmp_user->gainExperience($this->d13->getComponent($this->data['faction'],  $entry['obj_id'], 'cost'), $entry['quantity']);
				}
				
				/*
					auto craft
				*/
				if ($entry['auto'] != 0) {
					$this->addComponent($entry['obj_id'], $entry['quantity'], $entry['slot'], 1);
				}
				
				$this->d13->dbQuery('delete from craft where id="' . $entry['id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		if ($ok) {
			$this->d13->dbQuery('commit');
		} else {
			$this->d13->dbQuery('rollback');
		}
	}

	// ----------------------------------------------------------------------------------------
	// checkTrain
	// ----------------------------------------------------------------------------------------
	public

	function checkTrain($time)
	{
	
		$this->d13->dbQuery('start transaction');
		$this->getResources();
		$this->getComponents();
		$this->getUnits();
		$this->queues->getQueue('train');
		$ok = 1;
		
		foreach($this->queues->queue['train'] as $entry) {
		
			$entry['end'] = $entry['start'] + floor($entry['duration']);
			
			if ($entry['end'] <= $time) {
			
				if (!$entry['stage']) {
					$this->units[$entry['obj_id']]['value']+= $entry['quantity'];
					$this->d13->dbQuery('update units set value="' . $this->units[$entry['obj_id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $entry['obj_id'] . '"');
					if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				} else {
				
					foreach($this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'cost') as $cost) {
						$this->resources[$cost['resource']]['value'] += $cost['value'] * $entry['quantity'] * $this->d13->getGeneral('users', 'efficiency', 'train') * $this->getBuff('efficiency', 'train') * $this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'salvage');
						$this->d13->dbQuery('update resources set value="' . $this->resources[$cost['resource']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $cost['resource'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
					}

					foreach($this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'requirements') as $requirement) {
						if ($requirement['type'] == 'components') {
							$storageResource = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storageResource');
							$storage = $this->d13->getComponent($this->data['faction'], $requirement['id'], 'storage') * $requirement['value'] * $entry['quantity'];
							if ($this->resources[$storageResource]['value'] - $storage >= 0) {
								$this->resources[$storageResource]['value']-= $storage;
								$this->d13->dbQuery('update resources set value="' . $this->resources[$storageResource]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $storageResource . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
								$this->components[$requirement['id']]['value']+= $requirement['value'] * $entry['quantity'];
								$this->d13->dbQuery('update components set value="' . $this->components[$requirement['id']]['value'] . '" where node="' . $this->data['id'] . '" and id="' . $requirement['id'] . '"');
								if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
							}
						}
					}

				}
				
				if ($ok && $this->d13->getUnit($this->data['faction'], $entry['obj_id'], 'gainExperience')) { #experience gain
					$tmp_user = $this->d13->createObject('user', $_SESSION[CONST_PREFIX . 'User']['id']);
					$ok = $tmp_user->gainExperience($this->d13->getUnit($this->data['faction'],  $entry['obj_id'], 'cost'), $entry['quantity']);
				}
				
				/*
					auto train
				*/
				if ($entry['auto'] != 0) {
					$this->addUnit($entry['obj_id'], $entry['quantity'], $entry['slot'], 1);
				}
				
				$this->d13->dbQuery('delete from train where id="' . $entry['id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}
		}

		if ($ok) {
			$this->d13->dbQuery('commit');
		} else {
			$this->d13->dbQuery('rollback');
		}
	}

	// ----------------------------------------------------------------------------------------
	// checkCombat
	// ----------------------------------------------------------------------------------------

	public

	function checkCombat($time)
	{
		
		
		$this->queues->getQueue('combat');
		$ok = 1;
		
		foreach($this->queues->queue['combat'] as $combat) {
		
			$combat['end'] = $combat['start'] + floor($combat['duration']);
			if ($combat['end'] <= $time) {
			
				$otherNode = $this->d13->createNode();
				if ($combat['sender'] == $this->data['id']) {
					$nodes = array(
						'attacker' => 'this',
						'defender' => 'otherNode'
					);
					$status = $otherNode->get('id', $combat['recipient']);
				} else {
					$nodes = array(
						'attacker' => 'otherNode',
						'defender' => 'this'
					);
					$status = $otherNode->get('id', $combat['sender']);
				}
				
				if (!$combat['stage']) {
					if ($status == 'done') {
						
						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - GATHER COMBAT DATA
						
						$data = array();
						
						$data['combat'] = $this->d13->getCombat($combat['type']);
						$data['combat']['end'] = $combat['end'];
						$data['combat']['cid'] = $combat['id'];
						
						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - GATHER ATTACKER DATA

						$data['input']['attacker']['userId'] 	= ${$nodes['attacker']}->data['user'];
						$data['input']['attacker']['groups'] 	= array();
						$data['input']['attacker']['focus'] 	= $combat['focus'];
						$data['input']['attacker']['faction'] 	= ${$nodes['attacker']}->data['faction'];
						$data['input']['attacker']['nodeId'] 	= ${$nodes['attacker']}->data['id'];
						$data['output']['attacker']['resources'] = array();
						
						// - - - - - ATTACKER UNITS
						
						$otherResult = $this->d13->dbQuery('select * from combat_units where combat="' . $combat['id'] . '"');
						while ($group = $this->d13->dbFetch($otherResult)) {
							$data['input']['attacker']['groups'][] = array(
								'unitId' => $group['id'],
								'quantity' => $group['value']
							);
						}

						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - GATHER DEFENDER DATA
						
						$data['input']['defender']['userId'] 	= ${$nodes['defender']}->data['user'];
						$data['input']['defender']['groups'] 	= array();
						$data['input']['defender']['focus'] 	= ${$nodes['defender']}->data['focus'];
						$data['input']['defender']['faction'] 	= ${$nodes['defender']}->data['faction'];
						$data['input']['defender']['nodeId'] 	= ${$nodes['defender']}->data['id'];
						$data['output']['defender']['resources'] = array();
						
						// - - - - - DEFENDER UNITS

						if (!$this->d13->getGeneral('options', 'unitAttackOnly')) {
							$otherNode->getUnits();
							foreach(${$nodes['defender']}->units as $group) {
								$data['input']['defender']['groups'][] = array(
									'unitId' => $group['id'],
									'type' => 'unit',
									'quantity' => $group['value']
								);
							}
						}

						// - - - - - DEFENDER MODULES

						$otherNode->getModules();
						foreach(${$nodes['defender']}->modules as $group) {
							if ($group['module'] > - 1) {
								if ($this->d13->getModule(${$nodes['defender']}->data['faction'], $group['module'], 'type') == 'defense' && $group['input'] > 0) {
									$data['input']['defender']['groups'][] = array(
										'moduleId' => $group['module'],
										'unitId' => $this->d13->getModule(${$nodes['defender']}->data['faction'], $group['module'], 'unitId') ,
										'type' => 'module',
										'level' => $group['level'],
										'input' => $group['input'],
										'maxInput' => $this->d13->getModule(${$nodes['defender']}->data['faction'], $group['module'], 'maxInput')
									);
								}
							}
						}

						// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - PROCESS COMBAT

						$battle = new d13_combat($data);
						
					}
					
				//- - - - - Recalculate Resource Requirements and Units
				} else {
				
					if ($status == 'done') {
					
						$this->d13->dbQuery('start transaction');
						
						${$nodes['attacker']}->getResources();
						$result = $this->d13->dbQuery('select * from combat_units where combat="' . $combat['id'] . '"');
						while ($group = $this->d13->dbFetch($result)) {
							$this->d13->dbQuery('update units set value=value+"' . $group['value'] . '" where node="' . $combat['sender'] . '" and id="' . $group['id'] . '"');
							if ($this->d13->dbAffectedRows() == - 1) {
								$ok = 0;
							}

							$upkeepResource = $this->d13->getUnit(${$nodes['attacker']}->data['faction'], $group['id'], 'upkeepResource');
							$upkeep = $this->d13->getUnit(${$nodes['attacker']}->data['faction'], $group['id'], 'upkeep');
							$this->resources[$upkeepResource]['value']-= $upkeep * $group['value'];
							$this->d13->dbQuery('update resources set value="' . ${$nodes['attacker']}->resources[$upkeepResource]['value'] . '" where node="' . ${$nodes['attacker']}->data['id'] . '" and id="' . $upkeepResource . '"');
							if ($this->d13->dbAffectedRows() == - 1) {
								$ok = 0;
							}
						}

						$this->d13->dbQuery('delete from combat_units where combat="' . $combat['id'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}

						$this->d13->dbQuery('delete from combat where id="' . $combat['id'] . '"');
						if ($this->d13->dbAffectedRows() == - 1) {
							$ok = 0;
						}
						
						if ($ok) {
							$this->d13->dbQuery('commit');
						} else {
							$this->d13->dbQuery('rollback');
						}
					
					}
				}
			}
		}

		
	}

	// ========================================================================================
	//								
	// ========================================================================================












	// ========================================================================================
	//								
	// ========================================================================================








}

// =====================================================================================EOF