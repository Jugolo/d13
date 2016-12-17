<?php

//========================================================================================
//
// BLACKLIST.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class blacklist
{
 public static function check($type, $value)
 {
  global $d13;
  $result=$d13->db->query('select count(*) as count from blacklist where type="'.$type.'" and value="'.$value.'"');
  $row=$d13->db->fetch($result);
  return $row['count'];
 }
 public static function get($type)
 {
  global $d13;
  $result=$d13->db->query('select * from blacklist where type="'.$type.'"');
  $blacklist=array();
  for ($i=0; $row=$d13->db->fetch($result); $i++) $blacklist[$i]=$row;
  return $blacklist;
 }
 public static function add($type, $value)
 {
  global $d13;
  if (!blacklist::check($type, $value))
  {
   $d13->db->query('insert into blacklist (type, value) values ("'.$type.'", "'.$value.'")');
   if ($d13->db->affected_rows()>-1) $status='done';
   else $status='error';
  }
  else $status='duplicateEntry';
  return $status;
 }
 public static function remove($type, $value)
 {
  global $d13;
  if (blacklist::check($type, $value))
  {
   $d13->db->query('delete from blacklist where type="'.$type.'" and value="'.$value.'"');
   if ($d13->db->affected_rows()>-1) $status='done';
   else $status='error';
  }
  else $status='noEntry';
  return $status;
 }
}

//=====================================================================================EOF

?>