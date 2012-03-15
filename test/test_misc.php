<?php

require ('../lib/misc.php'); 

$milestone=new stdClass();
$milestone->description="This is a test description priority:1";
echo "prio=".getPriorityFromMilestone($milestone)."<br />";
echo getPriorityFromMilestone($milestone)==1?"PASS":"FAIL";
echo "<br />";

$milestone=new stdClass();
$milestone->description="This is a test description priority:2";
echo "prio=".getPriorityFromMilestone($milestone)."<br />";
echo getPriorityFromMilestone($milestone)==2?"PASS":"FAIL";
echo "<br />";

$milestone=new stdClass();
$milestone->description="This is a test description priority: 4";
echo "prio=".getPriorityFromMilestone($milestone)."<br />";
echo getPriorityFromMilestone($milestone)==4?"PASS":"FAIL";
echo "<br />";

$milestone=new stdClass();
$milestone->description="This is a test description priority:    5";
echo "prio=".getPriorityFromMilestone($milestone)."<br />";
echo getPriorityFromMilestone($milestone)==5?"PASS":"FAIL";
echo "<br />";


$text="This is a test description priority:    5\n Feature next feature:This_is_a_feature next bit";
$features=getFeaturesFromText($text);
echo "feature=".$features[0]."<br />";
echo $features[0]=="This_is_a_feature"?"PASS":"FAIL";
echo "<br />";

$text="This is a test description priority:    5 Feature next feature:  This_is_a_feature\n feature: This_is_another_feature\n more text feature: This_is_yet_another_feature\n";
$features=getFeaturesFromText($text);
echo "feature=".$features[0]."<br />";
echo "feature=".$features[1]."<br />";
echo "feature=".$features[2]."<br />";
echo $features[0]=="This_is_a_feature"?"PASS":"FAIL";
echo $features[1]=="This_is_another_feature"?"PASS":"FAIL";
echo $features[2]=="This_is_yet_another_feature"?"PASS":"FAIL";
echo "<br />";

$matches=array();
if(preg_match("/[0-9a-zA-Z-_]+\/repos\/([0-9a-zA-Z-_]+)\/([0-9a-zA-Z-_]+)\/issues\/[0-9]+/","https://api.github.com/repos/octocat/Hello-World/issues/1",$matches)>0){
	echo $matches[1]."<br />";
	echo $matches[2]."<br />";
}else{
	echo "FAIL";
}