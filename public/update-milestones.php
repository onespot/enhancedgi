<?php
require_once 'lib/github-api/lib/Github/Autoloader.php';

class Issue{
	// the underlying issue
	public $id;
	public $_issue;
	public $priority;
	public $milestone_id;
	public $time;
	public $estimated_start_time;
	public $estimated_end_time;
	function __construct($_issue){
		// this should be include repo name also
		$this->id=$_issue['number'];
		$this->_issue=$_issue;
		$this->priority = $this->getPriorityFromMilestone($_issue['milestone']);
		$this->milestone_id=$_issue['milestone']['number'];
		foreach($_issue['labels'] as $label){
			if(startsWith($label['name'],"time:")){
				$timeStr=substr($label['name'],5,strlen($label['name']));
				$seconds += (strtotime("+".$timeStr)-time());
				$this->time=$seconds;
				break;
			}
		}
	}
	
	function getPriorityFromMilestone($milestone){
		$matches=array();
		if(preg_match("/priority:([0-9])/",$milestone['description'],$matches)==1){
			return $matches[1];
		}else{
			return -1;
		}
	}
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function calculateIssueTimes($issues){
	$runningcount=0;
	foreach($issues as $issue){
		$issue->estimated_start_time=time()+$runningcount;
		$runningcount+=$issue->time;
		$issue->estimated_end_time=time()+$runningcount;
	}
}

function cmp_priority( $a, $b )
{ 
  if(  $a->priority ==  $b->priority ){
	if(  $a->milestone_id ==  $b->milestone_id ){ return 0 ; } 
	return ($a->milestone_id < $b->milestone_id) ? -1 : 1;
  } 
  return ($a->priority < $b->priority) ? -1 : 1;
} 

function cmp_finish_date( $b, $a )
{ 
  if(  $a->estimated_end_time ==  $b->estimated_end_time ){
	return 0;
  }
  return ($a->estimated_end_time < $b->estimated_end_time) ? -1 : 1;
} 

Github_Autoloader::register();

$github = new Github_Client();
$github->authenticate("clarkeandrew", "Womble21", Github_Client::AUTH_HTTP_PASSWORD);
$issues = $github->getIssueApi()->getList('onespot', 'amplify-back-end', 'open');

// Create the Issue Objects
$prio_issues=array();
foreach($issues as $issue){
	// create Issue objects
	$prio_issue = new Issue($issue);
	$prio_issues[]=$prio_issue;
}
usort($prio_issues,'cmp_priority');

$users=array();
// Organize the issues by user
$issuesByUser = array();
foreach($prio_issues as $issue){
	if(!array_key_exists($issue->_issue['assignee']['login'],$issuesByUser)){
		$users[$issue->_issue['assignee']['login']]=$issue->_issue['assignee'];
		$issuesByUser[$issue->_issue['assignee']['login']]=array();
		$issuesByUser[$issue->_issue['assignee']['login']][]=$issue;
	}else{
		$issuesByUser[$issue->_issue['assignee']['login']][]=$issue;
	}
}

// sort the user issues by milestone priority
foreach($issuesByUser as $key => $val){
	usort($val,'cmp_priority');
	$issuesByUser[$key]=$val;
}

// Calculate finish imes for each user
foreach($issuesByUser as $key => $val){
	calculateIssueTimes($val);
}

// Organize the issues by milestone
$milestones = array();
$issuesByMilestone = array();
foreach($prio_issues as $issue){
	if(!array_key_exists($issue->_issue['milestone']['title'],$issuesByMilestone)){
		$milestones[$issue->_issue['milestone']['title']]=$issue->_issue['milestone'];
		$issuesByMilestone[$issue->_issue['milestone']['title']]=array();
		$issuesByMilestone[$issue->_issue['milestone']['title']][]=$issue;
	}else{
		$issuesByMilestone[$issue->_issue['milestone']['title']][]=$issue;
	}
}
// sort the milestone issues by finish date desc
foreach($issuesByMilestone as $key => $val){
	usort($val,'cmp_finish_date');
	$issuesByMilestone[$key]=$val;
}

//echo "Milestone finish dates<br /><br />";
foreach($issuesByMilestone as $key => $val){
	//echo $key." [".$val[0]->priority."] : ".date("F j, Y",$val[0]->estimated_end_time)."<br />";
}
//echo "<br /><br />";

//echo "Issue Dates finish dates<br /><br />";
foreach($prio_issues as $issue){
	//echo $issue->_issue['title']." : ".date("F j, Y",$issue->estimated_start_time)." : ".date("F j, Y",$issue->estimated_end_time)."<br />";
}
?>
<html>
<head>
</head>
<body>

<table>
<tr>
<th>
Feature
</th>
<th>
Priority
</th>
<th>
Start date
</th>
<th>
End date
</th>
<th>
Tickets
</th>
</tr>

<?php foreach($issuesByMilestone as $key => $val){ ?>
	<tr>
	<td>
		<?php echo $key; ?>
	</td>
	<td>
		<?php echo $val[0]->priority; ?>
	</td>
	<td>
		<?php echo date("F j, Y",$val[sizeof($val)-1]->estimated_start_time); ?>
	</td>
	<td>
		<?php echo date("F j, Y",$val[0]->estimated_end_time); ?>
	</td>
	<td>
		<?php echo sizeof($val); ?>
	</td>
	</tr>
<?php } ?>

</table>

</body>
</html>
