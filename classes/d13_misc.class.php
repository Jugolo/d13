<?php

//========================================================================================
//
// MISC.CLASS
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Download & Updates..........: https://sourceforge.net/projects/d13/
// # Project Documentation.......: https://sourceforge.net/p/d13/wiki/Home/
// # Bugs & Suggestions..........: https://sourceforge.net/p/d13/tickets/
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

class misc
{

// 	//----------------------------------------------------------------------------------------
// 	// getlang
// 	// 3.0
// 	// @ A simple wrapper to get the right language string, this might change in the future.
// 	//----------------------------------------------------------------------------------------
// 	public static function getlang($constant)
// 	{	
// 		global $ui;
// 		if (!empty($constant) && isset($ui[$constant])) {
// 			return $ui[$constant];
// 		} else {
// 			return "--empty ui lang--";
// 		}
// 	}
	

	
	//----------------------------------------------------------------------------------------
	// time_format
	//----------------------------------------------------------------------------------------
	public static function time_format($secs=0) {
		if ($secs > 0) {
			$dtF = new DateTime('@0');
   			$dtT = new DateTime('@'.floor($secs));
    		$time = $dtF->diff($dtT)->format('%ad %hh %im %ss');
			$time = str_replace("0d","",$time);
			$time = str_replace("0h","",$time);
			$time = str_replace("0m","",$time);
			$time = str_replace("0s","",$time);
			return $time;
		}
	}
	//----------------------------------------------------------------------------------------
	// percentage
	//----------------------------------------------------------------------------------------
	public static function percentage($fraction, $total) {
		$percentage = 0;
		if ($fraction > 0 && $total > 0) {
			$percentage = $total * ($fraction/100);
		}
		return $percentage;
	}
	
	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------

 public static function clean($data, $type=0)
 {
  global $d13;
  if (is_array($data))
   foreach ($data as $key=>$value)
   {
    if (($type)&&($type=='numeric'))
     if (!is_numeric($value)) $value=0;
     else $value=floor(abs($value));
    $value=$d13->db->real_escape_string($value);
    $data[$key]=htmlspecialchars($value);
   }
  else
  {
   if (($type)&&($type=='numeric'))
    if (!is_numeric($data)) $data=0;
    else $data=floor(abs($data));
   $data=$d13->db->real_escape_string($data);
   $data=htmlspecialchars($data);
  }
  return $data;
 }
 
 	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
 /*
 public static function showMessage($message)
 {
  return '<div class="container" style="cursor: pointer;" onClick="this.style.display=\'none\'"><div class="message">'.$message.'</div></div>';
 }
  */
  
   	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------

 public static function newId($type)
 {
  global $d13;
  $result=$d13->db->query('select min(id) as id from free_ids where type="'.$type.'"');
  $id=$d13->db->fetch($result);
  if (isset($id['id']))
  {
   $d13->db->query('delete from free_ids where id="'.$id['id'].'" and type="'.$type.'"');
   return $id['id'];
  }
  else
  {
   $result=$d13->db->query('select max(id) as id from '.$type);
   $id=$d13->db->fetch($result);
   if (isset($id['id'])) return $id['id']+1;
   else return 1;
  }
 }

 
 	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
 public static function sToHMS($seconds)
 {
  $h=floor($seconds/3600);
  $m=floor($seconds%3600/60);
  $s=$seconds%3600%60;
  return array($h, $m, $s);
 }
 
 	//----------------------------------------------------------------------------------------
	// 
	//----------------------------------------------------------------------------------------
 public static function microTime()
 {
  list($usec, $sec)=explode(" ", microtime());
  return ((float)$usec+(float)$sec);
 }
}

//=====================================================================================EOF

?>