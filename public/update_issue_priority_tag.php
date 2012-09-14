<?php
require ('../control/page.php'); 
$c = new PageController();	

$repo=$_GET['repo'];
$issue_id=$_GET['ticket'];
$priority=$_GET['priority'];

$issue = $c->githubv3->api('issue')->show('onespot', $repo, $issue_id);
$labels = $issue['labels'];
echo $labels."<br />";
foreach($labels as $label){
	if(startswith($label['name'],"priority")){
		echo($label['name']."<br />");
		if($label['name']=='priority: '.$priority){
			// Label is already set
			break;
		}else{
			// remove the old label
			$c->githubv3->api('issue')->labels()->remove('onespot', $repo, $issue_id, $label['name']);
		}
	}
}
// add the new label
$c->githubv3->api('issue')->labels()->add('onespot', $repo, $issue_id, 'priority: '.$priority);
?>