<?php

// ========================================================================================
//
// USER.CLASS
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
// The player (user) class organizes access and retrieval of user related data. Also allows
// to add a new user or remove a user. Add experience to a user or alter it's avatar or other
// database fields.
//
// ========================================================================================

class d13_user

{

	protected $d13;

	public $data, $alliance, $ally_status, $user_status, $preferences, $blocklist;
	
	// ----------------------------------------------------------------------------------------
	// constructor
	// ----------------------------------------------------------------------------------------	
	public
	
	function __construct($args=NULL, d13_engine &$d13)
	{
	
		$this->d13 = $d13;
		
		if (isset($args['key']) && isset($args['value'])) {
			$this->user_status = $this->get($args['key'], $args['value']);
		}
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	public

	function get($idType, $id=NULL)
	{
		
		
		if (!empty($id) && $id != NULL) {
		
			$lower = '';
			if (is_string($id)) {
				$id = strtolower($id);
				$result = $this->d13->dbQuery('select * from users where LOWER(' . $idType . ')= LOWER("' . strtolower($id) . '")');
			} else {
				$result = $this->d13->dbQuery('select * from users where ' . $idType . '="' . $id . '"');
			}
		
			$this->data = $this->d13->dbFetch($result);
		
			if (isset($this->data['id'])) {
				$this->alliance	= $this->d13->createObject('alliance');
				$this->ally_status 	= $this->alliance->get('id', $this->data['alliance']);
				$status = 'done';
			} else {
				$status = 'noUser';
			}
		
		} else {
			$status = 'noUser';
		}
		
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function set()
	{
		$args = array();
		$args['key'] = 'id';
		$args['value'] = $this->data['id'];
		$user = $this->d13->createObject('user', $args);
		
		if ($user->user_status == 'done') {
			$this->d13->dbQuery('update users set name="' . $this->data['name'] . '", password="' . $this->data['password'] . '", email="' . $this->data['email'] . '", access="' . $this->data['access'] . '", joined="' . $this->data['joined'] . '", lastVisit="' . $this->data['lastVisit'] . '", ip="' . $this->data['ip'] . '", alliance="' . $this->data['alliance'] . '", template="' . $this->data['template'] . '", color="' . $this->data['color'] . '", locale="' . $this->data['locale'] . '", data="' . $this->data['data'] . '", sitter="' . $this->data['sitter'] . '" where id="' . $this->data['id'] . '"');
			if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noUser';
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function add()
	{
		
		$user = $this->d13->createObject('user');
		if ($user->get('name', $this->data['name']) == 'noUser') {
			if ($user->get('email', $this->data['email']) == 'noUser') {
				if (!$this->d13->blacklist->check('ip', $this->data['ip'])) {
					if (!$this->d13->blacklist->check('email', $this->data['email'])) {
						$ok = 1;
						$this->data['id'] = $this->d13->misc->newId('users');
						$this->d13->dbQuery('insert into users (id, name, password, email, access, joined, lastVisit, ip, template, color, locale, data, trophies) values ("' . $this->data['id'] . '", "' . $this->data['name'] . '", "' . $this->data['password'] . '", "' . $this->data['email'] . '", "' . $this->data['access'] . '", "' . $this->data['joined'] . '", "' . $this->data['lastVisit'] . '", "' . $this->data['ip'] . '", "' . $this->data['template'] . '", "' . $this->data['color'] . '", "' . $this->data['locale'] . '", "' . $this->data['data'] . '", "500")');
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						$preferences = array();
						foreach($this->d13->getGeneral('users', 'preferences') as $key => $preference) $preferences[] = '("' . $this->data['id'] . '", "' . $key . '", "' . $preference . '")';
						$preferences = implode(', ', $preferences);
						$this->d13->dbQuery('insert into preferences (user, name, value) values ' . $preferences);
						if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
						if ($ok) $status = 'done';
						else $status = 'error';
					}
					else {
						$status = 'emailBanned';
					}
				}
				else {
					$status = 'ipBanned';
				}
			}
			else {
				$status = 'emailInUse';
			}
		}
		else {
			$status = 'nameTaken';
		}

		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public static

	function remove($id)
	{
		
		$user = $this->d13->createObject('user', $this->d13);
		if ($user->get('id', $id) == 'done') {
			$result = $this->d13->dbQuery('select id from alliances where user="' . $id . '"');
			while ($row = $this->d13->dbFetch($result)) d13_alliance::remove($row['id']);
			$result = $this->d13->dbQuery('select id from nodes where user="' . $id . '"');
			while ($row = $this->d13->dbFetch($result)) d13_node::remove($row['id']);
			$ok = 1;
			$this->d13->dbQuery('delete from activations where user="' . $id . '"');
			$this->d13->dbQuery('delete from preferences where user="' . $id . '"');
			$this->d13->dbQuery('delete from blocklist where to="' . $id . '" or from="' . $id . '"');
			$messagesResult = $this->d13->dbQuery('select id from messages where recipient="' . $id . '" or sender="' . $id . '"');
			while ($row = $this->d13->dbFetch($messagesResult)) {
				$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				$this->d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "users")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from users where id="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noUser';
		return $status;
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public static

	function removeInactive($maxIdleTime)
	{
		
		$fromWhen = time() - $maxIdleTime * 86400;
		$fromWhen = strftime('%Y-%m-%d %H:%M:%S', $fromWhen);
		$usersResult = $this->d13->dbQuery('select id from users where (lastVisit<"' . $fromWhen . '" or access=0) and access<2');
		$pendingCount = $removedCount = 0;
		while ($userRow = $this->d13->dbFetch($usersResult)) {
			$pendingCount++;
			$result = $this->d13->dbQuery('select id from nodes where user="' . $userRow['id'] . '"');
			while ($row = $this->d13->dbFetch($result)) d13_node::remove($row['id']);
			$ok = 1;
			$this->d13->dbQuery('delete from activations where user="' . $userRow['id'] . '"');
			$messagesResult = $this->d13->dbQuery('select id from messages where recipient="' . $userRow['id'] . '" or sender="' . $userRow['id'] . '"');
			while ($row = $this->d13->dbFetch($messagesResult)) {
				$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
				$this->d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
				if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $userRow['id'] . '", "users")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from users where id="' . $userRow['id'] . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
			if ($ok) $removedCount++;
		}

		return array(
			'found' => $pendingCount,
			'removed' => $removedCount
		);
	}
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function resetPassword($email, $newPass)
	{
		
		if ($this->data['email'] == $email)
		if (time() - strtotime($this->data['lastVisit']) >= $this->d13->getGeneral('passwordResetIdle') * 60) {
			$this->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
			$this->d13->dbQuery('update users set password=sha1("' . $newPass . '"), lastVisit="' . $this->data['lastVisit'] . '" where id="' . $this->data['id'] . '"');
			if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'tryAgain';
		else $status = 'wrongEmail';
		return $status;
	}
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function getPreferences($index)
	{
		
		$result = $this->d13->dbQuery('select * from preferences where user="' . $this->data['id'] . '"');
		$this->preferences = array();
		if ($index == 'name')
		while ($row = $this->d13->dbFetch($result)) $this->preferences[$row['name']] = $row['value'];
		else
		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) $this->preferences[$i] = $row;
	}
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function setPreference($name, $value)
	{
		
		$this->d13->dbQuery('update preferences set value="' . $value . '" where user="' . $this->data['id'] . '" and name="' . $name . '"');
		if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
		else $status = 'error';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------
	public

	function getBlocklist()
	{
		
		$result = $this->d13->dbQuery('select * from blocklist where recipient="' . $this->data['id'] . '"');
		$this->blocklist = array();
		$user = $this->d13->createObject('user');
		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) {
			$this->blocklist[$i] = $row;
			if ($user->get('id', $this->blocklist[$i]['sender']) == 'done') $this->blocklist[$i]['senderName'] = $user->data['name'];
			else $this->blocklist[$i]['senderName'] = '[x]';
		}
	}
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function isBlocked($recipient)
	{
		
		$result = $this->d13->dbQuery('select count(*) as count from blocklist where recipient="' . $recipient . '" and sender="' . $this->data['id'] . '"');
		$row = $this->d13->dbFetch($result);
		return $row['count'];
	}
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function setBlocklist($name)
	{
		
		$user = $this->d13->createObject('user', $this->d13);
		if ($user->get('name', $name) == 'done') {
			$result = $this->d13->dbQuery('select count(*) as count from blocklist where recipient="' . $this->data['id'] . '" and sender="' . $user->data['id'] . '"');
			$row = $this->d13->dbFetch($result);
			if ($row['count']) $this->d13->dbQuery('delete from blocklist where recipient="' . $this->data['id'] . '" and sender="' . $user->data['id'] . '"');
			else $this->d13->dbQuery('insert into blocklist (recipient, sender) values ("' . $this->data['id'] . '", "' . $user->data['id'] . '")');
			if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noUser';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	// setStat
	// ----------------------------------------------------------------------------------------
	public
	
	function setStat($stat, $value)	
	{
	
		

		$status = 0;
		

			$this->d13->dbQuery('update users set '.$stat.'="'.$value.'" where id="' . $this->data['id'] . '"');
			
			if ($this->d13->dbAffectedRows() > - 1) {
				$status = 1;
			}
			
		
		
		return $status;
	
	}

	// ----------------------------------------------------------------------------------------
	// addStat
	// ----------------------------------------------------------------------------------------
	public
	
	function addStat($stat, $value)	
	{
	
		

		$status = 0;
		$tmp_stat = $this->d13->getGeneral('userstats', $stat);
		
		if (!empty($tmp_stat)) {
		
			$this->data[$tmp_stat['name']] += $value;
			
			if ($tmp_stat['isExp']) {
				if ($this->data[$tmp_stat['name']] >= $this->d13->misc->nextlevelexp($this->data['level'])) {
					$this->addStat('level', 1);
				}
			}
			
			if ($this->data[$tmp_stat['name']] < 0) {
				$this->data[$tmp_stat['name']] = 0;
			}
			
			$this->d13->dbQuery('update users set '.$tmp_stat['name'].'="'.$this->data[$tmp_stat['name']].'" where id="' . $this->data['id'] . '"');
			
			if ($this->d13->dbAffectedRows() > - 1) {
				$status = 1;
			}
			
		}
		
		return $status;
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	public
	
	function gainExperience($cost, $level)
	{
	
		
		
		$status = 0;
		$value = 0;
		$i = 0;
		
		foreach ($cost as $entry) {
			$value += $entry['value'];
			$i++;
		}
		
		$value = abs(floor((($value/$i)*$level)/$this->d13->getGeneral('factors', 'experience')));
	
		$status = $this->addStat('experience', $value);
		
		return $status;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// getTemplateVariables
	// ----------------------------------------------------------------------------------------
	public
	
	function getTemplateVariables()
	{
	
		
				
		$tvars = array();
	
		//- - - - - Player Name & Avatar
		$tvars['tvar_userName'] 		= $this->data['name'];
		$tvars['tvar_userImage'] 		= $this->d13->getAvatar($this->data['avatar'], 'image');
		$tvars['tvar_userTrophies']		= $this->data['trophies'];
		$tvars['tvar_userID']			= $this->data['id'];
		
		//- - - - - Player Alliance
		if ($this->ally_status == "done") {
		
			$tvars['tvar_userAllianceName'] 	= $this->alliance->data['name'];
			$tvars['tvar_userAllianceTag'] 		= " [" . $this->alliance->data['tag'] . "]";
			$tvars['tvar_userAllianceImage'] 	= $this->d13->getAlliance($this->alliance->data['avatar'], 'image');
			$tvars['tvar_userName'] 			.= " [" . $this->alliance->data['tag'] . "]";
		
		} else {
		
			$tvars['tvar_userAllianceName']	= $this->d13->getLangUI("none");
			$tvars['tvar_userAllianceImage'] = "alliance0.png";
			$tvars['tvar_userAllianceTag'] = "";
			
		}

		//- - - - - Player League
		$league = array();
		$league = $this->d13->getLeague($this->d13->misc->getLeague($this->data['level'], $this->data['trophies']));
		
		$tvars['tvar_userLeague']		= $this->d13->getLangGL('leagues', $league['id'], 'name');
		$tvars['tvar_userImageLeague'] 	= $league['image'];
		
		//- - - - - Player Stats
	
		foreach($this->d13->getGeneral("userstats") as $stat) {
			if ($stat['active']) {
				$tvars['tvar_userImage'.$stat['name']] 		= $stat['icon'];
				$tvars['tvar_userPercentage'.$stat['name']] = '';
				if ($stat['isExp']) {
				$tvars['tvar_user'.$stat['name']] 			= $this->data[$stat['value']] . '/' . $this->d13->misc->nextlevelexp($this->data['level']);
				$tvars['tvar_userPercentage'.$stat['name']] = $this->d13->misc->percentage(floor($this->data[$stat['value']]), $this->d13->misc->nextlevelexp($this->data['level']));
				$tvars['tvar_userColor'] 					= $stat['color'];
				} else {
				$tvars['tvar_user'.$stat['name']] 			= $this->data[$stat['value']];
				}
				
			}
		}

		return $tvars;
	
	}
	
}

// =====================================================================================EOF