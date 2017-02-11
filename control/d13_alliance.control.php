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

class d13_allianceController extends d13_controller
{
	
	private $alliance, $ally_status, $own;
	
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
		$this->alliance 	= new d13_alliance();
		$this->ally_status 	= $this->alliance->get('id', $_SESSION[CONST_PREFIX . 'User']['alliance']);
		$this->own = true;
		
		$tvars = $this->doControl();
		$this->getTemplate($tvars);
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function doControl()
	{
	
		switch ($_GET['action']) {
		
			case 'get':
				return $this->allianceGet();
				break;
			
			case 'set':
				return $this->allianceSet();
				break;
			
			case 'add':
				return $this->allianceAdd();
				break;
			
			case 'remove':
				return $this->allianceRemove();
				break;
			
			case 'addInvitation':
				return $this->allianceAddInvitation();
				break;
			
			case 'removeInvitation':
				return $this->allianceRemoveInvitation();
				break;
			
			case 'acceptInvitation':
				return $this->allianceAcceptInvitation();
				break;
			
			case 'removeMember':
				return $this->allianceRemoveMember();
				break;
			
			case 'addWar':
				return $this->allianceAddWar();
				break;
			
			case 'proposePeace':
				return $this->allianceProposePeace();
				break;
			
			case 'removePeace':
				return $this->allianceRemovePeace();
				break;
				
			case 'acceptPeace':
				return $this->allianceAcceptPeace();
				break;

		}
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceGet()
	{
		
		
		
		$tvars = array();
		$html = "";
		
		if ($this->d13->node->checkOptions('allianceGet')) {
			if ($_SESSION[CONST_PREFIX . 'User']['alliance']) {
				if ($this->ally_status == 'done') {
					$this->alliance->getAll();
				} else {
					$message = $this->d13->getLangUI($this->ally_status);
				}
			} else {
				$invitations = d13_alliance::getInvitations('user', $_SESSION[CONST_PREFIX . 'User']['id']);
			}
		
			if (isset($this->alliance->data['id'])) {
				if ($this->alliance->members) {
					$html.= '<div class="section"> -> {{tvar_ui_members}}</div>';
					foreach($this->alliance->members as $member) {
						$html.= '<div class="right">' . $member['name'];
						if (($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) || ($member['id'] == $_SESSION[CONST_PREFIX . 'User']['id'])) $html.= ' | <a class="external" href="index.php?p=alliance&action=removeMember&user=' . $member['id'] . '">x</a>';
						$html.= '</div>';
					}
				}

				if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $html.= '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_invitations}} | <a class="external" href="index.php?p=alliance&action=addInvitation">{{tvar_ui_invite}}</a></div>';
				foreach($this->alliance->invitations as $invitation) {
					
					$user = $this->d13->createObject('user');
					
					if ($user->get('id', $invitation['user']) == 'done') {
						$accept = '';
						$removeLabel = 'x';
						if ($user->data['id'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
							$accept = '<a class="external" href="?p=alliance&action=acceptInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">{{tvar_ui_accept}}</a> | ';
							$removeLabel = $this->d13->getLangUI('decline');
						}

						$html.= '<div class="right"> ' . $user->data['name'] . ' | ' . $accept . '<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">' . $removeLabel . '</a></div>';
					}
				}

				if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $html.= '<div class="section" style="margin-top: 5px;"> -> {{tvar_ui_wars}} | <a class="external" href="index.php?p=alliance&action=addWar">{{tvar_ui_goToWar}}</a></div>';
				foreach($this->alliance->wars as $war) {
					$otherAlliance = new d13_alliance();
					if ($this->alliance->data['id'] == $war['sender']) $otherAllianceId = $war['recipient'];
					else $otherAllianceId = $war['sender'];
					if ($otherAlliance->get('id', $otherAllianceId) == 'done')
					if ($war['type']) $html.= '<div class="right">' . $otherAlliance->data['name'] . ' | <a class="external" href="index.php?p=alliance&action=proposePeace&recipient=' . $otherAlliance->data['id'] . '">{{tvar_ui_peace}}</a></div>';
					else {
						if ($this->alliance->data['id'] == $war['recipient']) $ad = '<a class="external" href="index.php?p=alliance&action=acceptPeace&sender=' . $war['sender'] . '&recipient=' . $war['recipient'] . '">{{tvar_ui_accept}}</a> / <a class="external" href="index.php?p=alliance&action=removePeace&recipient=' . $otherAlliance->data['id'] . '">{{tvar_ui_decline}}</a>';
						else $ad = '<a class="external" href="?p=alliance&action=removePeace&recipient=' . $otherAlliance->data['id'] . '">x</a>';
						$html.= '<div class="right">' . $otherAlliance->data['name'] . ' {{tvar_ui_peace}} | ' . $ad . '</div>';
					}
				}
			} else {
				$html.= '<div class="section"><a class="external" href="?p=alliance&action=add">{{tvar_ui_add}}</a></div>';
				if ($invitations) {
					$html.= '<div class="section"> -> {{tvar_ui_invitations}}</div>';
					foreach($invitations as $invitation) {
						$user = $this->d13->createObject('user');
						
						if ($user->get('id', $invitation['user']) == 'done') {
							$accept = '';
							$removeLabel = 'x';
							if ($user->data['id'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
								$accept = '<a class="external" href="?p=alliance&action=acceptInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">{{tvar_ui_accept}}</a> | ';
								$removeLabel = $this->d13->getLangUI('decline');
							}

							$html.= '<div class="right"> ' . $user->data['name'] . ' | ' . $accept . '<a class="external" href="index.php?p=alliance&action=removeInvitation&alliance=' . $invitation['alliance'] . '&user=' . $invitation['user'] . '">' . $removeLabel . '</a></div>';
						}
					}
				}
			}

			$tvars['tvar_allianceHTML'] = $html;

			
		
		}
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceSet()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('allianceSet')) {

			$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
			$nodeList = '';
			foreach($nodes as $node) {
				$nodeList.= '<option value="' . $node->data['id'] . '">' . $node->data['name'] . '</option>';
			}
			
			if (isset($_POST['nodeId'], $_POST['name']))
			if ($_POST['name'] != '')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$node = $this->d13->createNode();
				$status = $node->get('id', $_POST['nodeId']);
				if ($status == 'done')
				if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$this->alliance->data['name'] 	= $_POST['name'];
					$this->alliance->data['tag'] 	= $_POST['tag'];
					$this->alliance->data['avatar'] = $_POST['avatar'];
					$status = $this->alliance->set($node->data['id']);
					$message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("accessDenied");
				else $message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("accessDenied");
			else $message = $this->d13->getLangUI("insufficientData");
		}
		else {
			$message = $this->d13->getLangUI("accessDenied");
		}

		
		$costData = '';
		foreach($this->d13->getFaction($this->d13->node->data['faction'], 'costs', 'alliance') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $this->d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}
		
		if (isset($_GET['avatarId'])) {
			$tvars['tvar_allianceAvatar'] = $this->d13->getAlliance($_GET['avatarId'], 'image');
			$tvars['tvar_avatarId'] = $_GET['avatarId'];
		} else {
			$tvars['tvar_allianceAvatar'] = $this->d13->getAlliance($this->alliance->data['avatar'], 'image');
			$tvars['tvar_avatarId'] = $this->alliance->data['avatar'];
		}

		$tvars['tvar_nodeList'] = $nodeList;
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_allianceName'] = $this->alliance->data['name'];
		$tvars['tvar_allianceTag'] = $this->alliance->data['tag'];
		
		$tvars['tvar_nodeID'] = $this->d13->node->data['id'];
		$tvars['tvar_nodeName'] = $this->d13->node->data['name'];
		
		
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAdd()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('allianceAdd')) {
			if ($this->ally_status == 'noAlliance') {
			
				$nodeList = '';
				$nodes = d13_node::getList($_SESSION[CONST_PREFIX . 'User']['id']);
				if ($nodes) {
					
					foreach($nodes as $node) {
						$nodeList.= '<option value="' . $node->data['id'] . '">' . $node->data['name'] . '</option>';
					}
					
					if (isset($_POST['nodeId'], $_POST['name']))
					if ($_POST['name'] != '') {
						$alliance = new d13_alliance();
						$node = $this->d13->createNode();
						$status = $node->get('id', $_POST['nodeId']);
						if ($status == 'done') {
							if ($node->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
								$alliance->data['name'] = $_POST['name'];
								$alliance->data['user'] = $_SESSION[CONST_PREFIX . 'User']['id'];
								$status = $alliance->add($node->data['id']);
								if ($status == 'done') {
									$status = $alliance->get('name', $_POST['name']);
									if ($status == 'done') {
										$_SESSION[CONST_PREFIX . 'User']['alliance'] = $alliance->data['id'];
									}
								}
								$message = $this->d13->getLangUI($status);
							} else {
								$message = $this->d13->getLangUI("accessDenied");
							}
						} else {
							$message = $this->d13->getLangUI($status);
						}
					} else {
						$message = $this->d13->getLangUI("insufficientData");
					}
				} else {
					$message = $this->d13->getLangUI("noNode");
				}
			} else {
				$message = $this->d13->getLangUI("allianceSet");
			}
		} else {
			$message = $this->d13->getLangUI("accessDenied");
		}
	
		$costData = '';
		foreach($this->d13->getFaction($this->d13->node->data['faction'], 'costs', 'alliance') as $key => $cost) {
			$costData.= '<div class="cell">' . $cost['value'] . '</div><div class="cell"><img class="d13-resource" src="templates/' . $_SESSION[CONST_PREFIX . 'User']['template'] . '/images/resources/' . $cost['resource'] . '.png" title="' . $this->d13->getLangGL("resources", $cost['resource'], "name") . '"></div>';
		}

		$tvars['tvar_nodeList'] = $nodeList;
		$tvars['tvar_costData'] = $costData;
		$tvars['tvar_nodeID'] = $this->d13->node->data['id'];
		$tvars['tvar_nodeName'] = $this->d13->node->data['name'];
			
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemove()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('allianceRemove')) {
			if ((isset($_GET['go'])) && ($_GET['go']))
			if ($_SESSION[CONST_PREFIX . 'User']['alliance']) {
				if ($this->ally_status == 'done')
				if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
					$status = d13_alliance::remove($_SESSION[CONST_PREFIX . 'User']['alliance']);
					if ($status == 'done') {
						$_SESSION[CONST_PREFIX . 'User']['alliance'] = 0;
						header('Location ?p=alliance&action=get');
					}
					else $message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("accessDenied");
				else $message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("insufficientData");
		}
		else {
			$message = $this->d13->getLangUI("accessDenied");
		}

		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAddInvitation()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('allianceInvite')) {
			if (isset($_POST['name']))
			if ($_POST['name'] != '')
			if ($this->ally_status == 'done')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$user = $this->d13->createObject('user');
				if ($user->get('name', $_POST['name']) == 'done') {
					$status = $this->alliance->addInvitation($user->data['id']);
					if ($status == 'done') {
						$user->getPreferences('name');
						if ($user->preferences['allianceReports']) {
							$msg = new d13_message();
							$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
							$msg->data['recipient'] = $user->data['name'];
							$msg->data['subject'] = $this->d13->getLangUI("allianceInvitation");
							$msg->data['body'] = '<a class=\"link\" href=\"index.php?p=alliance&action=acceptInvitation&alliance=' . $this->alliance->data['id'] . '&user=' . $user->data['id'] . '\">' . $this->d13->getLangUI("accept") . '</a> ' . $this->alliance->data['name'] . ' ' . $this->d13->getLangUI("alliance");
							$msg->data['type'] = 'alliance';
							$msg->data['viewed'] = 0;
							$status = $msg->add();
						}
					}

					$message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("noUser");
			}
			else $message = $this->d13->getLangUI("accessDenied");
			else $message = $this->d13->getLangUI("noAlliance");
			else $message = $this->d13->getLangUI("insufficientData");
		}
		else {
			$message = $this->d13->getLangUI("accessDenied");
		}

		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemoveInvitation()
	{
		
		
		
		$tvars = array();
		
		if (isset($_GET['alliance'], $_GET['user'])) {
			$senderAlliance = new d13_alliance();
			if ($senderAlliance->get('id', $_GET['alliance']) == 'done')
			if (in_array($_SESSION[CONST_PREFIX . 'User']['id'], array(
				$_GET['user'],
				$senderAlliance->data['user']
			))) {
				$status = d13_alliance::removeInvitation($_GET['alliance'], $_GET['user']);
				if ($status == 'done') header('Location: ?p=alliance&action=get');
				else $message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("accessDenied");
			else $message = $this->d13->getLangUI("noAlliance");
		}
		else $message = $this->d13->getLangUI("insufficientData");
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAcceptInvitation()
	{
		
		
		
		$tvars = array();
		
		if (isset($_GET['alliance'], $_GET['user']))
		if ($_SESSION[CONST_PREFIX . 'User']['id'] == $_GET['user']) {
			$status = d13_alliance::acceptInvitation($_GET['alliance'], $_GET['user']);
			if ($status == 'done') $_SESSION[CONST_PREFIX . 'User']['alliance'] = $_GET['alliance'];
			$message = $this->d13->getLangUI($status);
		}
		else $message = $this->d13->getLangUI("accessDenied");
		else $message = $this->d13->getLangUI("insufficientData");
	
		return $tvars;
	
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemoveMember()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('allianceRemoveMember')) {
			if ($this->ally_status == 'done')
			if (isset($_GET['user']))
			if ((($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) && ($_GET['user'] != $_SESSION[CONST_PREFIX . 'User']['id'])) || (($this->alliance->data['user'] != $_SESSION[CONST_PREFIX . 'User']['id']) && ($_GET['user'] == $_SESSION[CONST_PREFIX . 'User']['id']))) {
				$status = $this->alliance->removeMember($_GET['user']);
				if ($status == 'done') {
					if ($_GET['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) $_SESSION[CONST_PREFIX . 'User']['alliance'] = 0;
					header('Location: ?p=alliance&action=get');
				}

				$message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("accessDenied");
			else $message = $this->d13->getLangUI("insufficientData");
			else $message = $this->d13->getLangUI("noAlliance");
		}
		else {
			$message = $this->d13->getLangUI("accessDenied");
		}
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAddWar()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('allianceWar')) {
			if (isset($_POST['name']))
			if ($_POST['name'] != '')
			if ($this->ally_status == 'done')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$recipientAlliance = new d13_alliance();
				if ($recipientAlliance->get('name', $_POST['name']) == 'done')
				if ($this->alliance->data['id'] != $recipientAlliance->data['id']) {
					$status = $this->alliance->addWar($recipientAlliance->data['id']);
					if ($status == 'done') {
						$user = $this->d13->createObject('user');
						if ($user->get('id', $recipientAlliance->data['user']) == 'done') {
							$user->getPreferences('name');
							if ($user->preferences['allianceReports']) {
								$msg = new d13_message();
								$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
								$msg->data['recipient'] = $user->data['name'];
								$msg->data['subject'] = $this->d13->getLangUI("warDeclaration");
								$msg->data['body'] = $this->d13->getLangUI("sender") . ': ' . $this->alliance->data['name'] . ' ' . $this->d13->getLangUI("alliance");
								$msg->data['type'] = 'alliance';
								$msg->data['viewed'] = 0;
								$status = $msg->add();
								if ($status == 'done') header('Location ?p=alliance&action=get');
							}
						}
						else $message = $this->d13->getLangUI("noUser");
					}

					$message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("accessDenied");
				else $message = $this->d13->getLangUI("noAlliance");
			}
			else $message = $this->d13->getLangUI("accessDenied");
			else $message = $this->d13->getLangUI("noAlliance");
			else $message = $this->d13->getLangUI("insufficientData");
		}
		else {
			$message = $this->d13->getLangUI("accessDenied");
		}
		
		return $tvars;
	
	}
	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceProposePeace()
	{
		
		
		
		$tvars = array();
		
		if ($this->d13->node->checkOptions('alliancePeace')) {
			if (isset($_GET['recipient']))
			if ($this->ally_status == 'done')
			if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
				$recipientAlliance = new d13_alliance();
				if ($recipientAlliance->get('id', $_GET['recipient']) == 'done') {
					$status = $this->alliance->proposePeace($recipientAlliance->data['id']);
					if ($status == 'done') header('Location ?p=alliance&action=get');
					$message = $this->d13->getLangUI($status);
				}
				else $message = $this->d13->getLangUI("noAlliance");
			}
			else $message = $this->d13->getLangUI("accessDenied");
			else $message = $this->d13->getLangUI("noAlliance");
			else $message = $this->d13->getLangUI("insufficientData");
		}
		else {
			$message = $this->d13->getLangUI("accessDenied");
		}
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceRemovePeace()
	{
		
		
		
		$tvars = array();
		
		if (isset($_GET['recipient']))
		if ($this->ally_status == 'done')
		if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
			$recipientAlliance = new d13_alliance();
			if ($recipientAlliance->get('id', $_GET['recipient']) == 'done') {
				$status = $this->alliance->removePeace($recipientAlliance->data['id']);
				if ($status == 'done') header('Location ?p=alliance&action=get');
				else $message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("noAlliance");
		}
		else $message = $this->d13->getLangUI("accessDenied");
		else $message = $this->d13->getLangUI("noAlliance");
		else $message = $this->d13->getLangUI("insufficientData");
	
		return $tvars;
		
	}

	// ----------------------------------------------------------------------------------------
	// 
	// @
	//
	// ----------------------------------------------------------------------------------------
	private

	function allianceAcceptPeace()
	{
	
		
	
		$tvars = array();
		
		if (isset($_GET['sender'], $_GET['recipient']))
		if ($this->ally_status == 'done')
		if (($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) && ($this->alliance->data['id'] == $_GET['recipient'])) {
			$senderAlliance = new d13_alliance();
			if ($senderAlliance->get('id', $_GET['sender']) == 'done') {
				$status = $this->alliance->acceptPeace($senderAlliance->data['id']);
				if ($status == 'done') {
					$user = $this->d13->createObject('user');
					if ($user->get('id', $senderAlliance->data['user']) == 'done') {
						$user->getPreferences('name');
						if ($user->preferences['allianceReports']) {
							$msg = new d13_message();
							$msg->data['sender'] = $_SESSION[CONST_PREFIX . 'User']['name'];
							$msg->data['recipient'] = $user->data['name'];
							$msg->data['subject'] = $this->d13->getLangUI("peaceAccepted");
							$msg->data['body'] = $this->d13->getLangUI("sender") . ': ' . $this->alliance->data['name'] . ' ' . $this->d13->getLangUI("alliance");
							$msg->data['type'] = 'alliance';
							$msg->data['viewed'] = 0;
							$status = $msg->add();
							if ($status == 'done') header('Location ?p=alliance&action=get');
						}
					}
					else $message = $this->d13->getLangUI("noUser");
				}

				$message = $this->d13->getLangUI($status);
			}
			else $message = $this->d13->getLangUI("noAlliance");
		}
		else $message = $this->d13->getLangUI("accessDenied");
		else $message = $this->d13->getLangUI("noAlliance");
		else $message = $this->d13->getLangUI("insufficientData");
	
		return $tvars;
	
	}
	
	
	// ----------------------------------------------------------------------------------------
	// getAvatarPopup
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function getAvatarPopup()
	{
	
		
		
		$html = '';
		
		if ($this->own) {
		
			$i = 0;
			$open = false;
			$tvars = array();
			$tvars['tvar_sub_popuplist'] = '';
			$tvars['tvar_listID'] = 1;
		
			foreach ($this->d13->getAlliance() as $avatar) {
				if ($avatar['active']) {

					if ($avatar['level'] <= $this->alliance->data['level']) {
						$vars = array();
						$vars['tvar_Image'] 		= "/alliances/" . $avatar['image'];
						$vars['tvar_Link'] 			= "?p=alliance&action=set&avatarId=" . $avatar['id'];
						
						if ($i %2 == 0 || $i == 0) {
							$open = true;
							$tvars['tvar_sub_popuplist'] .= '<div class="row">';
						}
						
						$tvars['tvar_sub_popuplist'] .= $this->d13->templateSubpage("sub.module.imagecontent", $vars);
						
						if ($i %2 != 0) {
							$tvars['tvar_sub_popuplist'] .= '</div>';
						}
						
						$i++;
					}

				}
			}
			
			if ($open) {
				$tvars['tvar_sub_popuplist'] .= '</div>';
			}
			
			if ($i > 0) {
				$this->d13->templateInject($this->d13->templateSubpage("sub.popup.list", $tvars));
					
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("set") . " " . $this->d13->getLangUI("avatar");
				$vars['tvar_list_id'] 	 	 = "list-1";
				$vars['tvar_button_tooltip'] = "";
				$html = $this->d13->templateSubpage("button.popup.enabled", $vars);
				
			} else {
				$vars['tvar_button_name'] 	 = $this->d13->getLangUI("set") . " " . $this->d13->getLangUI("avatar");
				$vars['tvar_button_tooltip'] = "";
				$html . $this->d13->templateSubpage("button.popup.disabled", $vars);
			}
		
		}
		
		return $html;

	}

	
	
	// ----------------------------------------------------------------------------------------
	// getTemplate
	// @
	//
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplate($tvars)
	{
	
		
		
		$tvars['tvar_avatarLink'] = $this->getAvatarPopup();
		
		$tvars['tvar_allianceGet'] = '<a class="external" href="?p=alliance&action=get">' . $this->alliance->data['name'] . '</a>';
		$tvars['tvar_allianceSet'] = "";
		$tvars['tvar_allianceRemove'] = "";

		if ($this->alliance->data['user'] == $_SESSION[CONST_PREFIX . 'User']['id']) {
			$tvars['tvar_allianceSet'] = '<a class="external" href="?p=alliance&action=set">' . $this->d13->getLangUI("set") . '</a>';
			$tvars['tvar_allianceRemove'] = '<a class="external" href="?p=alliance&action=remove">' . $this->d13->getLangUI("remove") . '</a>';
		}

		$tvars['tvar_tpl_allianceMenu'] = $this->d13->templateSubpage("alliance.menu", $tvars);

		
		switch ($_GET['action']) {
		
			case 'get':
				$this->d13->outputPage('alliance.get', $tvars);
				break;
				
			case 'set':
				$this->d13->outputPage('alliance.set', $tvars);
				break;
			
			case 'add':
				$this->d13->outputPage('alliance.add', $tvars);
				break;
			
			case 'remove':
				$this->d13->outputPage('alliance.remove', $tvars);
				break;
			
			case 'addInvitation':
				$this->d13->outputPage('alliance.addInvitation', $tvars);
				break;
			
			case 'addWar':
				$this->d13->outputPage('alliance.addWar', $tvars);
				break;
			
			default:
				$this->d13->outputPage('alliance', $tvars);
				break;

		}
		
	}

}

// =====================================================================================EOF

