<?php

// ========================================================================================
//
// MESSAGE.CLASS
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
// Responsible for sending and retrieving text messages to/from users. Also takes blocklist
// and profanity filter into consideration. A player cannot receive messages from users on
// his/her blocklist and a player cannot send messages that include words from the blockwords
// list. Blockwords list is located in locales directory as JSON file (cached as data).
//
// a few functions are still missing, but not vital:
//
// 1. sending a message to all alliance (guild/clan) members.
// 2. sending a message to all players (admin only).
// 3. adding resources to a message (gifts for friends or gifts from admin).
//
// ========================================================================================

class d13_message

{
	public $data;
	
	protected $d13;
	
	// ----------------------------------------------------------------------------------------
	// constructor
	// ----------------------------------------------------------------------------------------
	public

	function __construct(d13_engine &$d13)
	{
		$this->d13 = $d13;
	
	}
	
	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------

	public

	function get($id)
	{
		
		$result = $this->d13->dbQuery('select * from messages where id="' . $id . '"');
		$this->data = $this->d13->dbFetch($result);
		$this->data['body'] = $this->textDecode($this->data['body']);
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
		
		$message = $this->d13->createObject('message');
		if ($message->get($this->data['id']) == 'done') {
			$this->d13->dbQuery('update messages set viewed="' . $this->data['viewed'] . '" where id="' . $this->data['id'] . '"');
			if ($this->d13->dbAffectedRows() > - 1) $status = 'done';
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
		
		$recipient = $this->d13->createObject('user');
		if ($recipient->get('name', $this->data['recipient']) == 'done') {
			$sender = $this->d13->createObject('user');
			if ($sender->get('name', $this->data['sender']) == 'done') {
				if (!$sender->isBlocked($recipient->data['id'])) {
					$this->data['body'] = $this->textEncode($this->data['body']);
					$this->data['id'] = $this->d13->misc->newId('messages');
					$sent = strftime('%Y-%m-%d %H:%M:%S', time());
					$this->d13->dbQuery('insert into messages (id, sender, recipient, subject, body, sent, viewed, type) values ("' . $this->data['id'] . '", "' . $sender->data['id'] . '", "' . $recipient->data['id'] . '", "' . $this->data['subject'] . '", "' . $this->data['body'] . '", "' . $sent . '", "' . $this->data['viewed'] . '", "' . $this->data['type'] . '")');
					if ($this->d13->dbAffectedRows() > - 1) {
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

	public

	function remove($id)
	{
		
		$message = $this->d13->createObject('message');
		if ($message->get($id) == 'done') {
			$ok = 1;
			$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $id . '", "messages")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from messages where id="' . $id . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
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
	public

	function removeAll($userId)
	{
		
		$result = $this->d13->dbQuery('select id from messages where recipient="' . $userId . '"');
		$ok = 1;
		while ($row = $this->d13->dbFetch($result)) {
			$this->d13->dbQuery('insert into free_ids (id, type) values ("' . $row['id'] . '", "messages")');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
			$this->d13->dbQuery('delete from messages where id="' . $row['id'] . '"');
			if ($this->d13->dbAffectedRows() == - 1) $ok = 0;
		}

		if ($ok) $status = 'done';
		else $status = 'error';
		return $status;
	}

	// ----------------------------------------------------------------------------------------
	//
	//
	// ----------------------------------------------------------------------------------------
	public

	function getList($recipient, $limit, $offset, $type="all")
	{
		
		$messages = array();
		$messages['messages'] = array();
		$result = $this->d13->dbQuery('select count(*) as count from messages where recipient="' . $recipient . '"');
		$row = $this->d13->dbFetch($result);
		$messages['count'] = $row['count'];
		
		if ($type == "outbox") {
			$type = "message";
			$result = $this->d13->dbQuery('select * from messages where sender="' . $recipient . '" and type="' . $type . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		
		} else if ($type == "all" || $type == "") {
			$result = $this->d13->dbQuery('select * from messages where recipient="' . $recipient . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		} else {
			$result = $this->d13->dbQuery('select * from messages where recipient="' . $recipient . '" and type="' . $type . '" order by sent desc limit ' . $limit . ' offset ' . $offset);
		}
		
		
		
		for ($i = 0; $row = $this->d13->dbFetch($result); $i++) {
			$messages['messages'][$i] = $this->d13->createObject('message');
			$messages['messages'][$i]->data = $row;
		}

		return $messages;
	}

	// ----------------------------------------------------------------------------------------
	// getUnreadCount
	// ----------------------------------------------------------------------------------------
	public

	function getUnreadCount($recipient)
	{
		
		$result = $this->d13->dbQuery('select count(*) as count from messages where recipient="' . $recipient . '" and viewed=0');
		$row = $this->d13->dbFetch($result);
		return $row['count'];
	}
	
	
	// ----------------------------------------------------------------------------------------
	// textEncode
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function textEncode($text)
	{	
		
		$text = htmlentities($text);
		$text = $this->d13->dbRealEscapeString($text);
		
		return $text;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// textDecode
	//
	// ----------------------------------------------------------------------------------------
	private
	
	function textDecode($text)
	{
	
		$text = html_entity_decode($text);
		
		return $text;
	
	}
	
	
}

// =====================================================================================EOF