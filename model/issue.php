<?php
require_once '../lib/misc.php';

class Issue{
	// the underlying issue
	public $id;
	public $idForMenu;
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
	public $priority;
	public $tag_priority = 0;
	public $dependsOnUser;
	public $dependsOnRepo;
	public $dependsOnTicket;
	public $dependsOnKey;
	public $milestone_version;
	public $color;
	
	function __construct($db, $_issue){
		global $VERSION_COLORS, $PRIORITY_COLORS; // in lib/misc.php
		// this should be include repo name also
		$matches=array();
		if(preg_match("/[0-9a-zA-Z-_]+\/repos\/([0-9a-zA-Z-_]+)\/([0-9a-zA-Z-_]+)\/issues\/[0-9]+/",$_issue->url,$matches)==1){
			$this->id=$matches[1]."/".$matches[2]."#".$_issue->number;
			$this->idForMenu=$matches[1]."-".$matches[2]."-".$_issue->number;
			$this->repo=$matches[2];
		}else{
			die("invalid issue returned from api");
		}
		// get the depends on tag
		$matches2=array();
		if(preg_match("/depends:[ ]*([0-9a-zA-z_#-\\/]*)/",$_issue->body,$matches2)==1){
			$matches3=array();
			if(preg_match("/([0-9a-zA-Z_-]+)\/([0-9a-zA-Z_-]+)#([0-9]+)/",$matches2[1],$matches3)==1){
				$this->dependsOnUser=$matches3[1];
				$this->dependsOnRepo=$matches3[2];
				$this->dependsOnTicket=$matches3[3];
				$this->dependsOnKey=$this->dependsOnUser."/".$this->dependsOnRepo."#".$this->dependsOnTicket;
			}else if(preg_match("/#([0-9]+)/",$matches2[1],$matches3)==1){
				// use this tickets
				$this->dependsOnUser=$matches[1];
				// use this tickets
				$this->dependsOnRepo=$matches[2];
				$this->dependsOnTicket=$matches3[1];
				$this->dependsOnKey=$this->dependsOnUser."/".$this->dependsOnRepo."#".$this->dependsOnTicket;
			}
		}
		
		$this->number=$_issue->number;
		$this->_issue=$_issue;
		$this->milestone_priority = getPriorityFromMilestone($_issue->milestone);
		
		$seconds=0;
		foreach($_issue->labels as $label){
			if(startsWith($label->name,"time: ")){
				$timeStr=substr($label->name,6,strlen($label->name));
				$seconds += (strtotime("+".$timeStr)-time());
				$this->time=$seconds;
			}else if(startsWith($label->name,"priority:")){
				$prioStr=substr($label->name,10,strlen($label->name));
				switch($prioStr){
					case "Low":
						$this->tag_priority=1;
						break;
					case "Medium":
						$this->tag_priority=2;
						break;
					case "High":
						$this->tag_priority=3;
						break;
					case "Urgent":
						$this->tag_priority=4;
						break;
				}
			}
		}
		
		if($this->tag_priority==0){
			if(strstr(strtolower($_issue->title),"urgent") || strstr(strtolower($_issue->title),"emergency") || strstr(strtolower($_issue->title),"priority")){
				$this->tag_priority=4;
			}
		}
		
		$this->color=isset($PRIORITY_COLORS[$this->tag_priority])?$PRIORITY_COLORS[$this->tag_priority]:"000000";

		$this->milestone_id=isset($_issue->milestone)?$_issue->milestone->number:null;
		$issue_prio = $db->getIssuePriority($matches[2],$_issue->number);
		$assignee = isset($this->_issue->assignee)?$this->_issue->assignee->login:"nobody";
		if(!empty($issue_prio) && ($issue_prio->owner != $assignee || $issue_prio->tag_priority != $this->tag_priority || $issue_prio->milestone_id != $this->milestone_id)){
			$db->deleteIssuePriority($matches[2],$this->_issue->number);
			$db->createIssuePriority($matches[2],$this->_issue->number,$assignee,$this->tag_priority,$this->milestone_id);
		}else if(empty($issue_prio)){
			$db->createIssuePriority($matches[2],$this->_issue->number,$assignee,$this->tag_priority,$this->milestone_id);
		}
		$issue_prio = $db->getIssuePriority($matches[2],$_issue->number);
		$this->priority=$issue_prio->priority;
		
		
		//$this->features = getFeaturesFromText($_issue->body);
		/*
		$this->features = $db->getIssueFeatures($matches[2],$_issue->number);
		foreach($this->features as $feature){
			if($this->feature_priority > $feature->priority){
				$this->feature_priority = $feature->priority;
			}
		}
		$this->milestone_id=isset($_issue->milestone)?$_issue->milestone->number:0;
		$msversion=array();
		if(preg_match("/.*([0-9]\.[0-9]|Bug)$/",isset($_issue->milestone)?$_issue->milestone->title:"",$msversion)==1){
			$this->milestone_version=$msversion[1];
		}
		*/
		//$this->color=isset($VERSION_COLORS[$this->milestone_version])?$VERSION_COLORS[$this->milestone_version]:"000000";
		
		/*
		switch($this->milestone_version){
			case "1.0":
				$this->color=$VERSION_COLORS["1.0"];
				break;
			case "2.0":
				$this->color=$VERSION_COLORS["2.0"];
				break;
			default:
				$this->color="000000";
			break;
		}
		*/
		// Default to 1 day
		if(empty($this->time)){
			$this->time=0;
		}
	}
}