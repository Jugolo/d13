<?php

// ========================================================================================
//
// MESSAGE.CLASS
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
//
// ----------------------------------------------------------------------------------------

class message

{
	public $data;
	
	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function get($id)
	{
		global $d13;
		$result = $d13->dbQuery('select * from messages where id="' . $id . '"');
		$this->data = $d13->dbFetch($result);
		if (isset($this->data['id'])) $status = 'done';
		else $status = 'noMessage';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function set()
	{
		global $d13;
		$message = new message();
		if ($message->get($this->data['id']) == 'done') {
			$d13->dbQuery('update messages set viewed="' . $this->data['viewed'] . '" where id="' . $this->data['id'] . '"');
			if ($d13->dbAffectedRows() > - 1) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noMessage';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function add()
	{
		global $d13;
		$recipient = new user();
		if ($recipient->get('name', $this->data['recipient']) == 'done') {
			$sender = new user();
			if ($sender->get('name', $this->data['sender']) == 'done') {
				if (!$sender->isBlocked($recipient->data['id'])) {
					$this->data['id'] = misc::newId('messages');
					$sent = strftime('%Y-%m-%d %H:%M:%S', time());
					$d13->dbQuery('insert into messages (id, sender, recipient, subject, body, sent, viewed) values ("' . $this->data['id'] . '", "' . $sender->data['id'] . '", "' . $recipient->data['id'] . '", "' . $this->data['subject'] . '", "' . $this->data['body'] . '", "' . $sent . '", "' . $this->data['viewed'] . '")');
					if ($d13->dbAffectedRows() > - 1) {
						$status = 'done';
					}
					else {
						$status = 'error';
					}
				}
				else {
					$status = 'blocked';
				}
			}
			else {
				$status = 'noSender';
			}
		}
		else {
			$status = 'noRecipient';
		}

		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function remove($id)
	{
		global $d13;
		$message = new message();
		if ($message->get($id) == 'done') {
			$ok = 1;
			$d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "messages")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from messages where id="' . $id . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'noMessage';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function removeAll($userId)
	{
		global $d13;
		$result = $d13->dbQuery('select id from messages where recipient="' . $userId . '"');
		$ok = 1;
		while ($row = $d13->dbFetch($result)) {
			$d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
		}

		if ($ok) $status = 'done';
		else $status = 'error';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function getList($recipient, $limit, $offset)
	{
		global $d13;
		$messages = array();
		$messages['messages'] = array();
		$result = $d13->dbQuery('select count(*) as count from messages where recipient="' . $recipient . '"');
		$row = $d13->dbFetch($result);
		$messages['count'] = $row['count'];
		$result = $d13->dbQuery('select * from messages where recipient="' . $recipient . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		for ($i = 0; $row = $d13->dbFetch($result); $i++) {
			$messages['messages'][$i] = new message();
			$messages['messages'][$i]->data = $row;
		}

		return $messages;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public static

	function getUnreadCount($recipient)
	{
		global $d13;
		$result = $d13->dbQuery('select count(*) as count from messages where recipient="' . $recipient . '" and viewed=0');
		$row = $d13->dbFetch($result);
		return $row['count'];
	}
}

// =====================================================================================EOF

?>