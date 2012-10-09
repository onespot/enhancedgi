<?php
require ('../control/issues.php'); 
$c = new IssuesController();	

$repo=$_GET['repo'];

foreach($c->issuesByMilestone as $milestoneName => $issues){
	if($milestoneName != "unassigned"){
		$milestone=$c->milestones[$milestoneName];
		// get the finish date of the final issue
		$lastIssue=$issues[sizeof($issues)-1];
		$c->githubv3->api('issue')->milestones()->update('onespot', $repo, $milestone->number,  array('due_on' => date(DATE_ATOM,$lastIssue->estimated_end_time)));
		echo $milestone->number." : ".$milestoneName ." : ". date(DATE_ATOM,$lastIssue->estimated_end_time)."\n";
	}
}
?>
