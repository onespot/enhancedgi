<?php
/*
require ('../control/page.php'); 
$c = new PageController();	

//$repo=$_GET['repo'];
//$issue_id=$_GET['ticket'];
//$priority=$_GET['priority'];

$from="amplify-ui";
$to="amplify-back-end";

$issues = $c->githubv3->api('issue')->all('onespot', $from, array('state' => 'open'));

$count=0;
foreach($issues as $issue){
	if($count==20) break;
	$count++;
	
	echo "Migrating issue : ".$issue['number']." ".$issue['title']."\n";
//	echo "title : ".$issue['title']."\n";
//	echo "body : ".$issue['body']."\n";
//	echo "owner : ".$issue['user']['login']."\n";
//	echo "assignee : ".$issue['assignee']['login']."\n";
	
	$labels=array();
	foreach($issue['labels'] as $label){
		$labels[]=$label['name'];
//		echo "label : ".$label['name']."\n";
	}
	
	$newIssue = $c->githubv3->api('issue')->create('onespot', $to, array('title' => $issue['title'], 'user' => $issue['user']['login'] ,'body' => "[owner: @".$issue['user']['login']." ".$issue['html_url']."]\n\n".$issue['body'], 'assignee' => $issue['assignee']['login'], 'labels' => $labels ));
	if(isset($newIssue['errors'])){
		echo "Error creating issue ".$issue['number']." in new repo";
		print_r($newIssue['errors']);
		continue;
	}
	$comments = $c->githubv3->api('issue')->comments()->all('onespot', $from, $issue['number']);
	foreach($comments as $comment){
//		echo "comment user : ".$comment['user']['login']."\n";
//		echo "comment body : ".$comment['body']."\n\n";
//		echo "Creating comment on ".$newIssue['number']. " for user ".$comment['user']['login']."\n";
		$c->githubv3->api('issue')->comments()->create('onespot', $to, $newIssue['number'], array('body' => "[commenter: @".$comment['user']['login']."]\n\n".$comment['body'], 'user' => $comment['user']['login']));
	}
	echo "\n\n";
	
	// close the issue
	$c->githubv3->api('issue')->update('onespot', $from, $issue['number'], array('state' => 'closed'));
	
}

// add the new label
//$c->githubv3->api('issue')->labels()->add('onespot', $repo, $issue_id, 'priority: '.$priority);
*/
?>