<?php

// ========================================================================================
//
// USER.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

class user

{
	public $data, $preferences, $blocklist;
	
	// ----------------------------------------------------------------------------------------
	//
	// ----------------------------------------------------------------------------------------

	public

	function get($idType, $id)
	{
		global $d13;
		
		$lower = '';
		if (is_string($id)) {
			$id = strtolower($id);
			$result = $d13->dbQuery('select * from users where LOWER(' . $idType . ')= LOWER("' . strtolower($id) . '")');
		} else {
			$result = $d13->dbQuery('select * from users where ' . $idType . '="' . $id . '"');
		}
		
		$this->data = $d13->dbFetch($result);
		if (isset($this->data['id'])) {
			$status = 'done';
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
		global $d13;
		$user = new user();
		if ($user->get('id', $this->data['id']) == 'done') {
			$d13->dbQuery('update users set name="' . $this->data['name'] . '", password="' . $this->data['password'] . '", email="' . $this->data['email'] . '", access="' . $this->data['access'] . '", joined="' . $this->data['joined'] . '", lastVisit="' . $this->data['lastVisit'] . '", ip="' . $this->data['ip'] . '", alliance="' . $this->data['alliance'] . '", template="' . $this->data['template'] . '", color="' . $this->data['color'] . '", locale="' . $this->data['locale'] . '", sitter="' . $this->data['sitter'] . '" where id="' . $this->data['id'] . '"');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
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
		global $d13;
		$user = new user();
		if ($user->get('name', $this->data['name']) == 'noUser') {
			if ($user->get('email', $this->data['email']) == 'noUser') {
				if (!blacklist::check('ip', $this->data['ip'])) {
					if (!blacklist::check('email', $this->data['email'])) {
						$ok = 1;
						$this->data['id'] = misc::newId('users');
						$d13->dbQuery('insert into users (id, name, password, email, access, joined, lastVisit, ip, template, color, locale) values ("' . $this->data['id'] . '", "' . $this->data['name'] . '", "' . $this->data['password'] . '", "' . $this->data['email'] . '", "' . $this->data['access'] . '", "' . $this->data['joined'] . '", "' . $this->data['lastVisit'] . '", "' . $this->data['ip'] . '", "' . $this->data['template'] . '", "' . $this->data['color'] . '", "' . $this->data['locale'] . '")');
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
						$preferences = array();
						foreach($d13->getGeneral('users', 'preferences') as $key => $preference) $preferences[] = '("' . $this->data['id'] . '", "' . $key . '", "' . $preference . '")';
						$preferences = implode(', ', $preferences);
						$d13->dbQuery('insert into preferences (user, name, value) values ' . $preferences);
						if ($d13->dbAffectedRows() == - 1) $ok = 0;
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
		global $d13;
		$user = new user();
		if ($user->get('id', $id) == 'done') {
			$result = $d13->dbQuery('select id from alliances where user="' . $id . '"');
			while ($row = $d13->dbFetch($result)) alliance::remove($row['id']);
			$result = $d13->dbQuery('select id from nodes where user="' . $id . '"');
			while ($row = $d13->dbFetch($result)) node::remove($row['id']);
			$ok = 1;
			$d13->dbQuery('delete from activations where user="' . $id . '"');
			$d13->dbQuery('delete from preferences where user="' . $id . '"');
			$d13->dbQuery('delete from blocklist where to="' . $id . '" or from="' . $id . '"');
			$messagesResult = $d13->dbQuery('select id from messages where recipient="' . $id . '" or sender="' . $id . '"');
			while ($row = $d13->dbFetch($messagesResult)) {
				$d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "users")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from users where id="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noUser';
		return $status;
	}

	public static

	function removeInactive($maxIdleTime)
	{
		global $d13;
		$fromWhen = time() - $maxIdleTime * 86400;
		$fromWhen = strftime('%Y-%m-%d %H:%M:%S', $fromWhen);
		$usersResult = $d13->dbQuery('select id from users where (lastVisit<"' . $fromWhen . '" or access=0) and access<2');
		$pendingCount = $removedCount = 0;
		while ($userRow = $d13->dbFetch($usersResult)) {
			$pendingCount++;
			$result = $d13->dbQuery('select id from nodes where user="' . $userRow['id'] . '"');
			while ($row = $d13->dbFetch($result)) node::remove($row['id']);
			$ok = 1;
			$d13->dbQuery('delete from activations where user="' . $userRow['id'] . '"');
			$messagesResult = $d13->dbQuery('select id from messages where recipient="' . $userRow['id'] . '" or sender="' . $userRow['id'] . '"');
			while ($row = $d13->dbFetch($messagesResult)) {
				$d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
				$d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
				if ($d13->dbAffectedRows() == - 1) $ok = 0;
			}

			$d13->dbQuery('insert into free_ids (id, type) values ("' . $userRow['id'] . '", "users")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from users where id="' . $userRow['id'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
			if ($ok) $removedCount++;
		}

		return array(
			'found' => $pendingCount,
			'removed' => $removedCount
		);
	}

	public

	function resetPassword($email, $newPass)
	{
		global $d13;
		if ($this->data['email'] == $email)
		if (time() - strtotime($this->data['lastVisit']) >= $d13->getGeneral('passwordResetIdle') * 60) {
			$this->data['lastVisit'] = strftime('%Y-%m-%d %H:%M:%S', time());
			$d13->dbQuery('update users set password=sha1("' . $newPass . '"), lastVisit="' . $this->data['lastVisit'] . '" where id="' . $this->data['id'] . '"');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'tryAgain';
		else $status = 'wrongEmail';
		return $status;
	}

	public

	function getPreferences($index)
	{
		global $d13;
		$result = $d13->dbQuery('select * from preferences where user="' . $this->data['id'] . '"');
		$this->preferences = array();
		if ($index == 'name')
		while ($row = $d13->dbFetch($result)) $this->preferences[$row['name']] = $row['value'];
		else
		for ($i = 0; $row = $d13->dbFetch($result); $i++) $this->preferences[$i] = $row;
	}

	public

	function setPreference($name, $value)
	{
		global $d13;
		$d13->dbQuery('update preferences set value="' . $value . '" where user="' . $this->data['id'] . '" and name="' . $name . '"');
		if ($d13->dbAffectedRows() > - 1) $status = 'done';
		else $status = 'error';
		return $status;
	}

	public

	function getBlocklist()
	{
		global $d13;
		$result = $d13->dbQuery('select * from blocklist where recipient="' . $this->data['id'] . '"');
		$this->blocklist = array();
		$user = new user();
		for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$this->blocklist[$i] = $row;
			if ($user->get('id', $this->blocklist[$i]['sender']) == 'done') $this->blocklist[$i]['senderName'] = $user->data['name'];
			else $this->blocklist[$i]['senderName'] = '[x]';
		}
	}

	public

	function isBlocked($recipient)
	{
		global $d13;
		$result = $d13->dbQuery('select count(*) as count from blocklist where recipient="' . $recipient . '" and sender="' . $this->data['id'] . '"');
		$row = $d13->dbFetch($result);
		return $row['count'];
	}

	public

	function setBlocklist($name)
	{
		global $d13;
		$user = new user();
		if ($user->get('name', $name) == 'done') {
			$result = $d13->dbQuery('select count(*) as count from blocklist where recipient="' . $this->data['id'] . '" and sender="' . $user->data['id'] . '"');
			$row = $d13->dbFetch($result);
			if ($row['count']) $d13->dbQuery('delete from blocklist where recipient="' . $this->data['id'] . '" and sender="' . $user->data['id'] . '"');
			else $d13->dbQuery('insert into blocklist (recipient, sender) values ("' . $this->data['id'] . '", "' . $user->data['id'] . '")');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noUser';
		return $status;
	}
}

// =====================================================================================EOF

?>