<?php

// ========================================================================================
//
// NOTES.CLASS
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
// currently empty, work in progress.
//
// planned to organize notifications displayed to a user, this includes error messages and
// other notes.
//
// ========================================================================================

class d13_notes

{
	
	private $notes = array();
	
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
	// ----------------------------------------------------------------------------------------
	public

	function noteAdd($note)
	{
		if (!empty($note)) {
			$this->notes[] = $note;
		}
	}
		
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	public

	function notesAdd($notes)
	{
		foreach ($notes as $note) {
			if (!empty($note)) {
				$this->notes[] = $note;
			}
		}
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	public
	
	function noteGet()
	{
	
		return end($this->notes);
	
	
	}
	
	// ----------------------------------------------------------------------------------------
	// 
	// ----------------------------------------------------------------------------------------
	public

	function noteGetAll()
	{
		return $this->notes;
	
	}	
	

	#noteAdd
	#noteGet
	#noteCheck
	#noteGetAll
	#noteGetList



}

// =====================================================================================EOF