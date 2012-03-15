<?php
require_once 'lib/github-api/lib/Github/Autoloader.php';
Github_Autoloader::register();

$github = new Github_Client();
$github->authenticate("clarkeandrew", "Womble21", Github_Client::AUTH_HTTP_PASSWORD);
$issues = $github->getIssueApi()->getList('onespot', 'reader-ui', 'open');

$users=array();
// sort the issues
$issuesByUser = array();
foreach($issues as $issue){
	if(!array_key_exists($issue['assignee']['login'],$issuesByUser)){
		$users[$issue['assignee']['login']]=$issue['assignee'];
		$issuesByUser[$issue['assignee']['login']]=array();
		$issuesByUser[$issue['assignee']['login']][]=(object)$issue;
	}else{
		$issuesByUser[$issue['assignee']['login']][]=(object)$issue;
	}
}

$issuesByMilestone = array();
foreach($issues as $issue){
	if(!array_key_exists($issue['milestone']['title'],$issuesByMilestone)){
		$issuesByMilestone[$issue['milestone']['title']]=array();
		$issuesByMilestone[$issue['milestone']['title']][]=(object)$issue;
	}else{
		$issuesByMilestone[$issue['milestone']['title']][]=(object)$issue;
	}
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function countTime($issues){
	$seconds=0;
	foreach($issues as $issue){
		if(sizeof($issue->labels)==0){
			continue;
		}
		foreach($issue->labels as $label){
			if(startsWith($label['name'],"time:")){
				$timeStr=substr($label['name'],5,strlen($label['name']));
				$seconds += (strtotime("+".$timeStr)-time());
			}
		}
	}
	return $seconds;
}

?>
<html>
<head>
</head>
<body>

<table>
<tr>
<th>
Developers
</th>
<th>
Ticket count
</th>
<th>
Time estimate
</th>
<th>
Completion date
</th>
</tr>
</tr>
<?php
foreach($issuesByUser as $userName => $issues){
$user=$users[$userName];
?>
<tr>
<td>
<img width="20px;" src="<?php echo $user['avatar_url']; ?>" /><a href="<?php echo $user['url']; ?>"><?php echo $user['login']; ?></a>
</td>
<td>
<?php echo sizeof($issues); ?>
</td>
<td>
<?php echo countTime($issues)/(60*60) ?> hours
</td>
<td>
<?php echo date("F j, Y",time()+countTime($issues)) ?>
</td>
</tr>
<?php
}
?>

</table>

<br /><br />
<?php
$label="Milestone";
$typeIssues=$issuesByMilestone;
include 'parts/issues_table.php';
?>

</body>
</html>