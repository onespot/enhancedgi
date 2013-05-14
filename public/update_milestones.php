<?php
require ('../control/issues.php'); 
$c = new IssuesController();	

$repo=$_GET['repo'];

foreach($c->issuesByMilestone as $milestoneName => $issues){
	if($milestoneName != "unassigned"){
		$milestone=$c->milestones[$milestoneName];
		// get the finish date of the final issue
		$lastIssue=$issues[sizeof($issues)-1];
		$totalTime=0;
		foreach($issues as $issue){
			$totalTime+=$issue->time;
		}
		$matches=array();
		$title=$milestone->title;
		if(preg_match("/(.*)( \(.*\))/",$title,$matches)){
			$title = $matches[1];
		}
		$days=ceil($totalTime/86400);
		$title=$title . " (" . $days . " day".($days>1?"s":"").")";
		echo "Updating ".$milestone->number." ".$title."<br />";
		$c->githubv3->api('issue')->milestones()->update('onespot', $repo, $milestone->number,  array('title' => $title,'due_on' => date(DATE_ATOM,$lastIssue->estimated_end_time)));
		echo $title." - ".$milestone->number." : ". date(DATE_ATOM,$lastIssue->estimated_end_time)."<br /><br />";
	}
}
?>
