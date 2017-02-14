<?php

// ========================================================================================
//
// MODULE.CONTROLLER
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class d13_moduleController extends d13_controller
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
		
		$tvars 				= array();
		
				
		$tvars = $this->doControl();
		$this->getTemplate($tvars);

	}
	
	// ----------------------------------------------------------------------------------------
	// doControl
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function doControl()
	{

		switch ($_GET['action'])
		{
		
			case 'get':
				return $this->moduleGet();
				break;
				
			case 'set':
				return $this->moduleSet();
				break;
			
			case 'add':
				return $this->moduleAdd();
				break;
		
			case 'upgrade':
				return $this->moduleUpgrade();
				break;
			
			case 'remove':
				return $this->moduleRemove();
				break;
		
			case 'cancel':
				return $this->moduleCancel();
				break;
		
			case 'list':
				return $this->moduleList();
				break;
				
			case 'addMarket':
				return $this->moduleAddMarket();
				break;
			
			case 'buyMarket':
				return $this->moduleBuyMarket();
				break;
			
			case 'cancelMarket':
				return $this->moduleCancelMarket();
				break;
			
			case 'addTechnology':
				return $this->moduleAddTechnology();
				break;
		
			case 'cancelTechnology':
				return $this->moduleCancelTechnology();
				break;
		
			case 'addComponent':
				return $this->moduleAddComponent();
				break;
			
			case 'removeComponent':
				return $this->moduleRemoveComponent();
				break;
			
			case 'cancelComponent':
				return $this->moduleCancelComponent();
				break;
			
			case 'addUnit':
				return $this->moduleAddUnit();
				break;
			
			case 'removeUnit':
				return $this->moduleRemoveUnit();
				break;
			
			case 'cancelUnit':
				return $this->moduleCancelUnit();
				break;
		
		}
	
	}

	// ----------------------------------------------------------------------------------------
	// moduleGet
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleGet()
	{
	
		$tvars = array();
		
		if (isset($_GET['moduleId'])) {
			$moduleId = $_GET['moduleId'];
		} else {
			$moduleId = $this->d13->node->modules[$_GET['slotId']]['module'];
		}
		
		$tmp_module = $this->d13->createModule($moduleId, $_GET['slotId'], $this->d13->node);
		
		$tvars = $tmp_module->getTemplateVariables();
		$tvars['tvar_page'] = $tmp_module->getTemplate();
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// moduleSet
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleSet()
	{
		
		$tvars = array();
		
		if (isset($_POST['input'])) {
			$status = $this->d13->node->setModule($_GET['slotId'], $_POST['input']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// moduleAdd
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAdd()
	{
		
		$tvars = array();
		
		if (isset($_GET['moduleId'])) {
			$status = $this->d13->node->addModule($_GET['slotId'], $_GET['moduleId'], $_POST['input']);
			if ($status == 'done') {
				
				header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
				
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleUpgrade()
	{
		
		$tvars = array();
		
		if (isset($_GET['slotId']) && isset($_GET['moduleId'])) {
			$status = $this->d13->node->upgradeModule($_GET['slotId'], $_GET['moduleId'], $_POST['input']);
			if ($status == 'done') {
				
				header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
				
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleRemove()
	{
		
		$tvars = array();
		
		$status = $this->d13->node->removeModule($_GET['slotId']);
		if ($status == 'done') {
			
			header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
			exit();
		} else {
			$message = $this->d13->getLangUI($status);
		}
				
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancel()
	{
		
		$tvars = array();
		
		$status = $this->d13->node->cancelModule($_GET['slotId']);
		if ($status == 'done') {
			
			header('Location: index.php?p=node&action=get&nodeId=' . $this->d13->node->data['id']);
			exit();
		} else {
			$message = $this->d13->getLangUI($status);
		}
			
		return $tvars;

	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleList()
	{
		
		$args = array();
		$args['slotId'] = $_GET['slotId'];
		$moduleList = $this->d13->createController('d13_moduleListController', $args);
		
		$tvars = array();
		$tvars = $moduleList->getTemplateVariables();
		$tvars['tvar_page'] = $moduleList->getTemplate();
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddMarket()
	{
		
		$tvars = array();
		
		if (isset($_GET['slotId'])) {
			$status = $this->d13->node->addMarket($_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}


	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleBuyMarket()
	{
		
		$tvars = array();
		
		if (isset($_GET['slotId']) && isset($_GET['objType']) && isset($_GET['objId'])) {
			$status = $this->d13->node->buyMarket($_GET['slotId'], $_GET['objType'], $_GET['objId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelMarket()
	{
		
		$tvars = array();
		
		if (isset($_GET['slotId'])) {
			$status = $this->d13->node->cancelMarket($_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}

		return $tvars;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddTechnology()
	{
		
		$tvars = array();
		
		if (isset($_GET['technologyId'])) {
			$status = $this->d13->node->addTechnology($_GET['technologyId'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelTechnology()
	{
		
		$tvars = array();
		
		if (isset($_GET['technologyId'])) {
			$status = $this->d13->node->cancelTechnology($_GET['technologyId'], $this->d13->node->modules[$_GET['slotId']]['module']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}

		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddComponent()
	{
		
		$tvars = array();
		
		if (isset($_GET['componentId'], $_POST['quantity'])) {
			if ($_POST['quantity'] > 0) {
			
				if (isset($_POST['autoCraft']) && $_POST['autoCraft'] != 0) {
					$auto = 1;
				} else {
					$auto = 0;
				}
				
				$status = $this->d13->node->addComponent($_GET['componentId'], $_POST['quantity'], $_GET['slotId'], $auto);
				if ($status == 'done') {
					
					header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
					exit();
				} else {
					$message = $this->d13->getLangUI($status);
				}
			} else {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			}
		}
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleRemoveComponent()
	{
		
		$tvars = array();
		
		if (isset($_GET['componentId'], $_POST['quantity'])) {
			$status = $this->d13->node->removeComponent($_GET['componentId'], $_POST['quantity'], $node->modules[$_GET['slotId']]['module'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelComponent()
	{
		
		$tvars = array();
		
		if (isset($_GET['craftId'])) {
			$status = $this->d13->node->cancelComponent($_GET['craftId'], $this->d13->node->modules[$_GET['slotId']]['module']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleAddUnit()
	{
		
		$tvars = array();
		
		if (isset($_GET['unitId'], $_POST['quantity']) && $_POST['quantity'] > 0) {
			
			if (isset($_POST['autoTrain']) && $_POST['autoTrain'] != 0) {
				$auto = 1;
			} else {
				$auto = 0;
			}
				
			$status = $this->d13->node->addUnit($_GET['unitId'], $_POST['quantity'], $_GET['slotId'], $auto);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		} else {
			
			header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
			exit();
		}

		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleRemoveUnit()
	{
		
		$tvars = array();
		
		if (isset($_GET['unitId'], $_POST['quantity'])) {
			$status = $this->d13->node->removeUnit($_GET['unitId'], $_POST['quantity'], $this->d13->node->modules[$_GET['slotId']]['module'], $_GET['slotId']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
				
		return $tvars;

	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function moduleCancelUnit()
	{
		
		$tvars = array();
		
		if (isset($_GET['trainId'])) {
			$status = $this->d13->node->cancelUnit($_GET['trainId'], $this->d13->node->modules[$_GET['slotId']]['module']);
			if ($status == 'done') {
				
				header('Location: index.php?p=module&action=get&nodeId=' . $this->d13->node->data['id'] . '&slotId=' . $_GET['slotId']);
				exit();
			} else {
				$message = $this->d13->getLangUI($status);
			}
		}
			
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
		
		
		
		$this->d13->outputPage($tvars['tvar_page'] , $tvars, $this->d13->node);
		
		
	}

}

// =====================================================================================EOF

