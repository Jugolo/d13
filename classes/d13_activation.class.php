<?php

//========================================================================================
//
// ACTIVATION.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

//----------------------------------------------------------------------------------------
// activation
//----------------------------------------------------------------------------------------
class d13_activation
{
 public $data;
 
 public function get($user)
 {
  global $d13;
  $result=$d13->db->query('select * from activations where user="'.$user.'"');
  $this->data=$d13->db->fetch($result);
  if (isset($this->data['user'])) $status='done';
  else $status='noActivation';
  return $status;
 }
 
 public function add()
 {
  global $d13;
  $d13->db->query('insert into activations (user, code) values ("'.$this->data['user'].'", "'.$this->data['code'].'")');
  if ($d13->db->affected_rows()>-1) $status='done';
  else $status='error';
  return $status;
 }
 
 public function activate($code)
 {
  global $d13;
  if ($this->data['code']==$code)
  {
   $ok=1;
   $d13->db->query('update users set level=level+1 where id="'.$this->data['user'].'"');
   if ($d13->db->affected_rows()==-1) $ok=0;
   $d13->db->query('delete from activations where user="'.$this->data['user'].'"');
   if ($d13->db->affected_rows()==-1) $ok=0;
   if ($ok) $status='done';
   else $status='error';
  }
  else $status='wrongCode';
  return $status;
 }
 
}

//=====================================================================================EOF

?>