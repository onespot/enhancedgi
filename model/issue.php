<?php
require_once '../lib/misc.php';

class Issue{
	// the underlying issue
	public $id;
	public $number;
	public $repo;
	public $_issue;
	public $milestone_priority;
	public $feature_priority = 100000000;
	public $milestone_id;
	public $features;
	public $time;
	public $estimated_start_time;
	public $estimated_end_time;
	function __construct($db, $_issue){
		// this should be include repo name also
		$matches=array();
		if(preg_match("/[0-9a-zA-Z-_]+\/repos\/([0-9a-zA-Z-_]+)\/([0-9a-zA-Z-_]+)\/issues\/[0-9]+/",$_issue->url,$matches)==1){
			$this->id=$matches[1]."-".$matches[2]."-".$_issue->number;
			$this->repo=$matches[2];
		}else{
			die("invalid issue returned from api");
		}
		$this->number=$_issue->number;
		$this->_issue=$_issue;
		$this->milestone_priority = getPriorityFromMilestone($_issue->milestone);
		//$this->features = getFeaturesFromText($_issue->body);
		$this->features = $db->getIssueFeatures($matches[2],$_issue->number);
		foreach($this->features as $feature){
			if($this->feature_priority > $feature->priority){
				$this->feature_priority = $feature->priority;
			}
		}
		$this->milestone_id=$_issue->milestone->number;
		foreach($_issue->labels as $label){
			if(startsWith($label->name,"time:")){
				$timeStr=substr($label->name,5,strlen($label->name));
				$seconds += (strtotime("+".$timeStr)-time());
				$this->time=$seconds;
				break;
			}
		}
		// Default to 1 day
		if(empty($this->time)){
			$this->time=86400;
		}
	}
}