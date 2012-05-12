<?php

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function cmp_milestone_priority( $a, $b )
{ 
  if(  $a->milestone_priority ==  $b->milestone_priority ){
	if(  $a->milestone_id ==  $b->milestone_id ){ return 0 ; } 
	return ($a->milestone_id < $b->milestone_id) ? -1 : 1;
  } 
  return ($a->milestone_priority < $b->milestone_priority) ? -1 : 1;
} 

function cmp_feature_priority( $a, $b )
{ 
  if(  $a->feature_priority ==  $b->feature_priority ){
	return 0;
  } 
  return ($a->feature_priority < $b->feature_priority) ? -1 : 1;
} 

function cmp_issue_priority( $a, $b )
{ 
  if(  $a->priority ==  $b->priority ){
	return 0;
  } 
  return ($a->priority > $b->priority) ? -1 : 1;
} 

function cmp_finish_date( $a, $b )
{ 
  if(  $a->estimated_end_time ==  $b->estimated_end_time ){
	return 0;
  }
  return ($a->estimated_end_time < $b->estimated_end_time) ? -1 : 1;
} 

function strToHexColor($string)
{
    $hex='';
    for ($i=0; $i < strlen($string); $i++)
    {
        $hex .= dechex(ord($string[$i]));
    }
    return substr($hex,0,6);
}

function getPriorityFromMilestone($milestone){
	$matches=array();
	if(preg_match("/priority:[ ]*([0-9])/",$milestone->description,$matches)==1){
		return $matches[1];
	}else{
		return -1;
	}
}

function getFeaturesfromText($text){
	$matches=array();
	if(preg_match_all("/feature:[ ]*([0-9a-zA-z_-]*)/",$text,$matches)>0){
		return $matches[1];
	}else{
		return null;
	}
}

function clearCache(){
	$dir = '../cache/';
	foreach(glob($dir.'*') as $v){
		unlink($v);
	}
}

/**
* Updates the start and end dates for a list of issues based on each of their durations
**/
function updateIssueTimes($issues){
	$runningcount=0;
	$time=strtotime(date("Y-m-d"));
	foreach($issues as $issue){
		$issue->estimated_start_time=$time+$runningcount;
		$runningcount+=$issue->time;
		$issue->estimated_end_time=$time+$runningcount-1;
		$weekend_days=weekendDays($issue->estimated_start_time,$issue->estimated_end_time);
		$issue->estimated_end_time+=($weekend_days*86400);
		//$runningcount+=weekendDays($issue->estimated_start_time,$issue->estimated_end_time)*86400;
		$runningcount+=($weekend_days*86400);
		//echo $issue->_issue['title']." : ".date("Y-m-d h:m",$issue->estimated_start_time)." -> ".date("Y-m-d h:m",$issue->estimated_end_time)." [".date("Y-m-d h:m",$time+$runningcount)."] "."<br />";
	}
}

/**
*  Returns the number of weekend days in a time period
**/
function weekendDays($start,$end) 
{ 
	$weekend_days=0;
	$days=ceil(doubleval($end-$start)/86400.0);
	$time=$start;
    for ($i=0;$i<$days;$i++) {
        // Check for a saturday and count 2 weekend days then skip sunday
		if (in_array(date('w', $time+($i*86400)), array(6))){
			$weekend_days+=2;
			$i++;
			continue;
		}
		// Check for a sunday (this will only happen if sunday is the start day)
		if (in_array(date('w', $time+($i*86400)), array(0))){
			$weekend_days++;
		}
       // foreach ($holidays as $h) { // iterate through holidays 
       //     if ($time>=$h && $time<$h+86400) continue 2; // skip holidays 
       // }
    } 
    return $weekend_days; 
} 

function array_to_obj($array, &$obj){
	foreach ($array as $key => $value)
	{
	  if (is_array($value))
	  {
	  $obj->$key = new stdClass();
	  array_to_obj($value, $obj->$key);
	  }
	  else
	  {
		$obj->$key = $value;
	  }
	}
	return $obj;
}

function arrayToObject($array) {
	 $object= new stdClass();
	 return array_to_obj($array,$object);
}


function hashSomething($mixed){
	return md5(json_encode($mixed));
}