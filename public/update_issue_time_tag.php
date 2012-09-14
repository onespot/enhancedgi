<?php
require ('../control/page.php'); 
$c = new PageController();	

$repo=$_GET['repo'];
$issue_id=$_GET['ticket'];
$time=urldecode($_GET['time']);

$issue = $c->githubv3->api('issue')->show('onespot', $repo, $issue_id);
$labels = $issue['labels'];
foreach($labels as $label){
	if(startswith($label['name'],"time:")){
		if($label['name']=='time:'.$time){
			// Label is already set
			echo "Label already set\n";
			break;
		}else{
			echo "Removing old label ".$label['name']."\n";
			// remove the old label
			$c->githubv3->api('issue')->labels()->remove('onespot', $repo, $issue_id, $label['name']);
		}
	}
}
echo "Adding new label ".'time: '.$time."\n";
// add the new label
$c->githubv3->api('issue')->labels()->add('onespot', $repo, $issue_id, 'time: '.$time);
?>