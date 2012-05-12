<?php
require ('../control/xml.php'); 
$c = new XmlController();
?>
<project>
<?php 
	$id=0;
	foreach($c->issuesByUser as $key => $val){ 
	$id++;
	$parent_id=$id;
	$milestone=$c->milestones[$key];
?>
	<task>
		<pID><?php echo xmlEscape($id); ?></pID>
		<pName><?php echo xmlEscape($key); ?></pName>
		<pStart><?php echo date("m/d/Y",$val[0]->estimated_start_time); ?></pStart>
		<pEnd><?php echo date("m/d/Y",$val[sizeof($val)-1]->estimated_end_time); ?></pEnd>
		<pColor>0000ff</pColor>
		<!-- <pLink><?php echo "https://github.com/".$_ACCOUNT."/".$_REPO."/issues/milestones/".$milestone->number; ?>/edit</pLink> -->
		<pMile>0</pMile>
		<pRes><?php //echo $val[0]->_issue['assignee']['login'] ?></pRes>
		<pComp>0</pComp>
		<pGroup>1</pGroup>
		<pParent></pParent>
		<pOpen>1</pOpen>
		<pDepend></pDepend>
	</task>
	<?php
		foreach($val as $issue){ 
		$id++;
	?>
		<task>
			<pID><?php echo xmlEscape($id); ?></pID>
			<pName><?php echo xmlEscape($issue->_issue->title); ?></pName>
			<pStart><?php echo date("m/d/Y",$issue->estimated_start_time); ?></pStart>
			<pEnd><?php echo date("m/d/Y",$issue->estimated_end_time); ?></pEnd>
			<pColor><?php echo strToHexColor($issue->_issue->assignee->login); ?></pColor>
			<pLink><?php echo $issue->_issue->html_url; ?></pLink>
			<pMile>0</pMile>
			<pRes><?php echo xmlEscape($issue->_issue->assignee->login) ?> [<?php echo ceil(($issue->time)/86400) ?> d] [p: <?php echo $issue->priority; ?>]</pRes>
			<pComp>0</pComp>
			<pGroup>0</pGroup>
			<pParent><?php echo $parent_id; ?></pParent>
			<pOpen>1</pOpen>
			<pDepend><?php echo $c->xmlKeyMap[$issue->dependsOnKey]; ?></pDepend>
			<pGhId><?php echo $issue->idForMenu; ?></pGhId>
		</task>
<?php 
		}
	} 
?>
</project>