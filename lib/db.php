<?php 
class Database{
    private $dbh;
    
		/*
	public function __construct($dbname = 'enhancedgi',$dbuser="enhancedgi",$dbpass="poiulkjh"){
        $this->dbh = mysql_connect('localhost', $dbuser, $dbpass) 
            or die("Unable to connect to MySQL");
        mysql_select_db($dbname);
    }
    	*/
		
		
	public function __construct($dbname = 'enhancedgi',$dbuser="enhancedgi",$dbpass="poiulkjh19792012stak"){
		global $DEBUG_MODE;
		if($DEBUG_MODE || (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']=="penrose")){
			echo "DEBUG DB MODE";
			$this->dbh = mysql_connect('localhost', $dbuser, "poiulkjh") 
            or die("Unable to connect to MySQL");
		}else{
			$this->dbh = mysql_connect('amplifydev.cuzjy8kiwhgl.us-east-1.rds.amazonaws.com', $dbuser, $dbpass) 
				or die("Unable to connect to MySQL");
		}
        mysql_select_db($dbname);
    }


	
    function __destruct() {
       mysql_close($this->dbh);
    }
   
    function getFeature($id){
        $query = "SELECT * FROM features WHERE id=".$id;
        $result = mysql_query($query);
        if(!$result) {
            die("Error getting feature from db : " . mysql_error());
        }
        $row = mysql_fetch_object($result);
        return $row;
    }
	
	function getDevAvailability($devname,$date){
        $query = "SELECT * FROM dev_availability WHERE developer_name='$devname' AND (effective_date < '".date("Y-m-d",$date)."' OR effective_date IS NULL) ORDER BY effective_date DESC LIMIT 1";
        $result = mysql_query($query);
        if(!$result) {
            die("Error getting feature from db : " . mysql_error());
        }
        $row = mysql_fetch_object($result);
		if(isset($row)){
			return isset($row->available_days_per_week)?$row->available_days_per_week:5;
		}else{
			return 5;
		}
    }
	
    function getFeatures(){
		// ignore the unassigned default feature
        $query = "SELECT * FROM features where id>1";
        $result = mysql_query($query);
        if(!$result) {
            die("Error getting features from db : " . mysql_error());
        }
        $results = array();
        while($row = mysql_fetch_object($result)){
            $results[]=$row;
        }
        return $results;
    }
	
	function addIssueFeature($repo,$issue_id,$feature_id){
		$insert="INSERT INTO features_issues VALUES($feature_id,'$repo',$issue_id)";
        $result = mysql_query($insert);
        if(!$result) {
            die("Error adding issues features : " . mysql_error());
        }
	}
	
	function updateIssuePriority($repo,$issue_id,$priority,$owner="nobody",$tag_priority=0,$milestone_id=null){
		if(isset($milestone_id)){
			$insert="INSERT INTO issue_priority(issue_repo,issue_id,priority,owner,tag_priority,milestone_id) VALUES('$repo',$issue_id,$priority,'$owner',$tag_priority,$milestone_id) ON DUPLICATE KEY UPDATE priority=$priority";
		}else{
			$insert="INSERT INTO issue_priority(issue_repo,issue_id,priority,owner,tag_priority) VALUES('$repo',$issue_id,$priority,'$owner',$tag_priority) ON DUPLICATE KEY UPDATE priority=$priority";
		}
	   $result = mysql_query($insert);
        if(!$result) {
            die("Error updating issue priority : " . mysql_error());
        }
	}
	
	function updateMilestoneProgress($milestone,$start_date,$finish_date,$issue_count){
		$today=date("Y-m-d");
	   $insert="INSERT INTO milestone_progress VALUES('$today','$milestone','$start_date','$finish_date',$issue_count) ON DUPLICATE KEY UPDATE start_date='$start_date',finish_date='$finish_date',issue_count='$issue_count'";
	   $result = mysql_query($insert);
        if(!$result) {
            die("Error updating milestone progress : " . mysql_error());
        }
	}
	
	function increaseIssuePriority($repo,$issue_id){
	   $insert="INSERT INTO issue_priority VALUES('$repo',$issue_id,1) ON DUPLICATE KEY UPDATE priority=priority+1";
	   $result = mysql_query($insert);
        if(!$result) {
            die("Error updating issue priority : " . mysql_error());
        }
	}
	
	function decreaseIssuePriority($repo,$issue_id){
	   $insert="INSERT INTO issue_priority VALUES('$repo',$issue_id,0) ON DUPLICATE KEY UPDATE priority=priority-1";
	   $result = mysql_query($insert);
        if(!$result) {
            die("Error updating issue priority : " . mysql_error());
        }
	}
	
	function deleteIssuePriority($repo,$issue_id){
		$delete="DELETE FROM issue_priority where issue_repo='$repo' and issue_id=$issue_id";
        $result = mysql_query($delete);
        if(!$result) {
            die("Error deleting issues features : " . mysql_error());
        }
	}
	
	function removeIssueFeature($repo,$issue_id,$feature_id){
		$delete="DELETE FROM features_issues where feature_id=$feature_id and issue_repo='$repo' and issue_id=$issue_id";
        $result = mysql_query($delete);
        if(!$result) {
            die("Error deleting issues features : " . mysql_error());
        }
	}
	
	function getIssueFeatures($repo,$issue_id){
        $query = "SELECT features.* 
				  FROM features 
				  LEFT JOIN features_issues 
					on features.id = features_issues.feature_id 
				  WHERE features_issues.issue_repo='$repo' AND 
					    features_issues.issue_id=$issue_id";
        $result = mysql_query($query);
        if(!$result) {
            die("Error getting features from db : " . mysql_error());
        }
        $results = array();
        while($row = mysql_fetch_object($result)){
            $results[]=$row;
        }
        return $results;
    }
	
	function createIssuePriority($repo,$issue_id,$owner,$tag_prio,$milestone_id = null){
        $query = "SELECT max(priority) as max_priority
				  FROM issue_priority
				  WHERE owner='$owner' and tag_priority=$tag_prio";
        $result = mysql_query($query);
        if(!$result) {
             die("Error getting features from db : " . mysql_error());
        }
        $row = mysql_fetch_object($result);
		$new_priority=0;
		if(isset($row->max_priority)){
			$new_priority = $row->max_priority + 1;
		}
		if(isset($milestone_id)){
			$insert="insert into issue_priority(issue_repo,issue_id,priority,owner,tag_priority,milestone_id) values('$repo','$issue_id',$new_priority,'$owner','$tag_prio','".$milestone_id."')";
		}else{
			$insert="insert into issue_priority(issue_repo,issue_id,priority,owner,tag_priority) values('$repo','$issue_id',$new_priority,'$owner','$tag_prio')";
		}
		$result = mysql_query($insert);
        if(!$result) {
             die("Error inserting issue priority : " . mysql_error());
        }
		return $new_priority;
    }
	
	function swapPrioritys($repo1,$issue1,$repo2,$issue2){
		$prio1=$this->getIssuePriority($repo1,$issue1)->priority;
		$prio2=$this->getIssuePriority($repo2,$issue2)->priority;
		$this->updateIssuePriority($repo2,$issue2,$prio1);
		$this->updateIssuePriority($repo1,$issue1,$prio2);
	}
	
	function upPriority($repo,$issue_id){
		$prio=$this->getIssuePriority($repo,$issue_id);
		if($prio->priority>0){
			$query = "SELECT *
				  FROM issue_priority
				  WHERE issue_priority.owner='$prio->owner' and tag_priority=".$prio->tag_priority." and issue_priority.priority<".$prio->priority." order by priority desc limit 1";
			$result = mysql_query($query);
			if(!$result) {
				 die("Error getting next priority from db : " . mysql_error());
			}
			$row = mysql_fetch_object($result);
			if(isset($row)){
				$this->swapPrioritys($repo,$issue_id,$row->issue_repo,$row->issue_id);
			}else{
				die("Issue with priority ".($prio->priority-1)." doesnt exist for user ".$prio->owner);
			}
		}else{
			return false;
		}
		return true;
	}
	
	function upPriorityMs($repo,$issue_id){
		$prio=$this->getIssuePriority($repo,$issue_id);
		if($prio->priority>0){
			$query = "SELECT *
				  FROM issue_priority
				  WHERE issue_priority.owner='$prio->owner' and tag_priority=".$prio->tag_priority." and issue_priority.priority<".$prio->priority." and issue_priority.milestone_id=$prio->milestone_id order by priority desc limit 1";
			$result = mysql_query($query);
			if(!$result) {
				 die("Error getting next priority from db : " . mysql_error());
			}
			$row = mysql_fetch_object($result);
			if(isset($row)){
				// get the priority of the next one and upPriority until we reach there.
				for($i=0;$i<($prio->priority - $row->priority -1);$i++){
					$this->upPriority($repo,$issue_id);
				}
			}else{
				//die("Issue with priority ".($prio->priority-1)." doesnt exist for user ".$prio->owner);
				return false;
			}
		}else{
			return false;
		}
		return true;
	}
	
	function bury($repo,$issue_id){
		while($this->downPriority($repo,$issue_id)){
			// burying
		}
	}
	
	function boost($repo,$issue_id){
		while($this->upPriority($repo,$issue_id)){
			// boosting
		}
	}
	
	function downPriority($repo,$issue_id){
		$prio=$this->getIssuePriority($repo,$issue_id);
		$query = "SELECT *
			  FROM issue_priority
			  WHERE issue_priority.owner='$prio->owner' and tag_priority=".$prio->tag_priority." and issue_priority.priority>".$prio->priority." order by priority asc limit 1";
		$result = mysql_query($query);
		if(!$result) {
			 die("Error getting features from db : " . mysql_error());
		}
		$row = mysql_fetch_object($result);
		if(!empty($row)){
			$this->swapPrioritys($repo,$issue_id,$row->issue_repo,$row->issue_id);
		}else{
			return false;
		}
		return true;
	}
	
	function getIssuePriority($repo,$issue_id){
        $query = "SELECT *
				  FROM issue_priority
				  WHERE issue_priority.issue_repo='$repo' AND 
					    issue_priority.issue_id=$issue_id";
        $result = mysql_query($query);
        if(!$result) {
             die("Error getting features from db : " . mysql_error());
        }
        $row = mysql_fetch_object($result);
		if(isset($row)){
			return $row;
		}else{
			return null;
		}
    }
}
?>