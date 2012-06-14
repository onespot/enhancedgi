<?php
require ('../control/xml.php'); 
$c = new XmlController();
?>
<project>
<?php 
	$id=0;
	foreach($c->issuesByFeature as $key => $val){ 
	$id++;
	$parent_id=$id;
	$feature=$c->db->getFeature($key);
?>
	<task>
		<pID><?php echo xmlEscape($id); ?></pID>
		<pName><?php echo xmlEscape($feature->title); ?></pName>
		<pStart><?php echo date("m/d/Y",$val[0]->estimated_start_time); ?></pStart>
		<pEnd><?php echo date("m/d/Y",$val[sizeof($val)-1]->estimated_end_time); ?></pEnd>
		<pColor>0000ff</pColor>
		<pLink></pLink>
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
			<pColor><?php echo $issue->color; ?></pColor>
			<pLink><?php echo $issue->_issue->html_url; ?></pLink>
			<pMile>0</pMile>
			<pRes><?php echo xmlEscape($issue->_issue->assignee->login) ?> [<?php echo ceil(($issue->time)/86400) ?> days]</pRes>
			<pComp>0</pComp>
			<pGroup>0</pGroup>
			<pParent><?php echo $parent_id; ?></pParent>
			<pOpen>1</pOpen>
			<pDepend></pDepend>
			<pGhId><?php echo $issue->id; ?></pGhId>
		</task>
<?php 
		}
	} 
?>
</project>