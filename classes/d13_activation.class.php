<?php

// ========================================================================================
//
// ACTIVATION.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ========================================================================================

// ----------------------------------------------------------------------------------------
// activation
// ----------------------------------------------------------------------------------------

class d13_activation

{
	public $data;

	public

	function get($user)
	{
		global $d13;
		$result = $d13->dbQuery('select * from activations where user="' . $user . '"');
		$this->data = $d13->dbFetch($result);
		if (isset($this->data['user'])) $status = 'done';
		else $status = 'noActivation';
		return $status;
	}

	public

	function add()
	{
		global $d13;
		$d13->dbQuery('insert into activations (user, code) values ("' . $this->data['user'] . '", "' . $this->data['code'] . '")');
		if ($d13->dbAffectedRows() > - 1) $status = 'done';
		else $status = 'error';
		return $status;
	}

	public

	function activate($code)
	{
		global $d13;
		if ($this->data['code'] == $code) {
			$ok = 1;
			$d13->dbQuery('update users set level=level+1 where id="' . $this->data['user'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			$d13->dbQuery('delete from activations where user="' . $this->data['user'] . '"');
			if ($d13->dbAffectedRows() == - 1) $ok = 0;
			if ($ok) $status = 'done';
			else $status = 'error';
		}
		else $status = 'wrongCode';
		return $status;
	}
}

// =====================================================================================EOF

