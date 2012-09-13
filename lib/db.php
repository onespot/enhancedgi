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
        $this->dbh = mysql_connect('amplifydev.cuzjy8kiwhgl.us-east-1.rds.amazonaws.com', $dbuser, $dbpass) 
            or die("Unable to connect to MySQL");
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
	
	function updateIssuePriority($repo,$issue_id,$priority){
	   $insert="INSERT INTO issue_priority VALUES('$repo',$issue_id,$priority) ON DUPLICATE KEY UPDATE priority=$priority";
	   $result = mysql_query($insert);
        if(!$result) {
            die("Error updating issue priority : " . mysql_error());
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
	
	function getIssuePriority($repo,$issue_id){
        $query = "SELECT issue_priority.priority
				  FROM issue_priority
				  WHERE issue_priority.issue_repo='$repo' AND 
					    issue_priority.issue_id=$issue_id";
        $result = mysql_query($query);
        if(!$result) {
             die("Error getting features from db : " . mysql_error());
        }
        $row = mysql_fetch_object($result);
		if(isset($row->priority)){
			return $row->priority;
		}else{
			return 0;
		}
    }
}
?>