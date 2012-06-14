<?php
require ('../control/issues.php'); 
$c = new IssuesController();	

// load features from disk
$features=$c->db->getFeatures();
?>
<html>

<head>

    <title>Gantt Chart</title>
	<link rel="stylesheet" type="text/css" href="css/jsgantt.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.contextMenu.css" />
	<script language="javascript" src="js/jquery-1.4.2.min.js"></script>
	<script language="javascript" src="js/jquery.contextMenu.js"></script>
	<script language="javascript" src="js/jsgantt.js"></script>
	
	<script type="text/javascript">
		function updateTicket(action, repo, ticket_id){	
			var actions = action.split(':-:');
			if(actions[0]=="add"){
				$.get('update_ticket_features.php?repo='+repo+'&ticket='+ticket_id+'&feature='+actions[1], function(data) {
				  $('.result').html(data);
				  window.location.reload();
				});
			}else if(actions[0]=="remove"){
				$.get('update_ticket_features.php?repo='+repo+'&ticket='+ticket_id+'&feature='+actions[1]+'&mode=DELETE', function(data) {
				  $('.result').html(data);
				  window.location.reload();
				});
			}
		}
		
		function maybeReload(){
			<?php if(isset($_GET['batchmode']) && $_GET['batchmode']=="true"){?>
				  return;
			<?php } ?>
			window.location.reload();
		}
		
		function updateTicketPriority(action, repo, ticket_id){	
			var actions = action.split(':-:');
			if(actions[1]=="increase"){
				$.get('update_ticket_priority.php?repo='+repo+'&ticket='+ticket_id+'&action=increase', function(data) {
				  $('.result').html(data);
					maybeReload();
				});
			}else if(actions[1]=="decrease"){
				$.get('update_ticket_priority.php?repo='+repo+'&ticket='+ticket_id+'&action=decrease', function(data) {
				  $('.result').html(data);
				  maybeReload();
				});
			}else{
				$.get('update_ticket_priority.php?repo='+repo+'&ticket='+ticket_id+'&action=update&priority='+actions[1], function(data) {
				  $('.result').html(data);
				  maybeReload();
				});
			}
		}
		
		$(document).ready( function() {
			<?php foreach($c->issues as $issue) { ?>
			$("#<?php echo $issue->idForMenu; ?>").contextMenu({
				menu: "<?php echo $issue->idForMenu; ?>-menu"
			},
				function(action, el, pos) {
					updateTicketPriority(action, '<?php echo $issue->repo ?>', <?php echo $issue->number; ?>);
				}
			);
			<?php } ?>
		});
		
		
	</script>
</head>
<body>

<?php 
	foreach($c->issues as $issue) {
?>
	<ul id="<?php echo $issue->idForMenu; ?>-menu" class="contextMenu"> 
		<li class="separator">
			<a title="Increase Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:increase"><img src="images/add.png" width="20px"/></a>
			Increase Priority
		</li>
		 <li class="separator">
			<a title="Decrease Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:decrease"><img src="images/remove.png" width="20px"/></a>
			Decrease Priority
		</li>
	</ul>
<?php } ?>

<!-- setting priority
<?php 
	foreach($c->issues as $issue) {
?>
	<ul id="<?php echo $issue->idForMenu; ?>-menu" class="contextMenu"> 
	<?php foreach(array(0,1,2,3,4,5,6,7,8,9,10) as $priority){ ?>
    <li class="separator">
        <a title="Priority <?php echo $priority; ?>" style="display: inline; padding: 0; margin: 0;" href="#priority:-:<?php echo $priority; ?>"><img src="images/add.png" width="20px"/></a>
		Priority <?php echo $priority; ?>
    </li>
	<?php } ?>
</ul>
<?php } ?>
-->
<!-- old feature stuff
<?php 
	foreach($c->issues as $issue) {
		$issue_features=$c->db->getIssueFeatures($issue->repo,$issue->number);
?>
	<ul id="<?php echo $issue->id; ?>-menu" class="contextMenu"> 
	<?php foreach($issue_features as $feature){ ?>
	<li class="separator">
		<a title="Remove from <?php echo $feature->title; ?>"  style="display: inline; padding: 0; margin: 0;" href="#remove:-:<?php echo $feature->id; ?>"><img src="images/remove.png" width="20px"/></a>
		<?php echo substr($feature->title,0,50); ?>...
    </li>
	<?php } ?>
	<?php foreach($features as $feature){ 
		// dont ad the issue twice
		if(in_array($feature,$issue_features)) continue;
	?>
    <li class="separator">
        <a title="Add to <?php echo $feature->title; ?>" style="display: inline; padding: 0; margin: 0;" href="#add:-:<?php echo $feature->id; ?>"><img src="images/add.png" width="20px"/></a>
		<?php echo substr($feature->title,0,50); ?>...
    </li>
	<?php } ?>
</ul>
<?php } ?>
-->
<div id="selector">
<form method="get">
<?php if(isset($_GET['mode'])){ ?>
		<!--
		<input type="hidden" name="mode" value="<?php echo $_GET['mode']; ?>">
		-->
<?php } ?>
<table>
	<tr>
<?php 
	$getvals="?";
	if(isset($_GET['repos'])){
		foreach($_GET['repos'] as $repo){
			$getvals=$getvals."repos[]=".$repo."&";
		}
	}
	$row=1;
	foreach($c->repos as $repo){
?>
	<td><input type="checkbox" name="repos[]" value="<?php echo $repo['name']; ?>" <?php if((!isset($_GET['repos']))||in_array($repo['name'],$_GET['repos'])){echo "checked";} ?> /> <?php echo $repo['name']; ?></td>
 <?php 
		if($row%6==0){
			echo "</tr>";
			echo "<tr>";
		}
		$row++;
	}
	
 ?>
	</tr>
 </table>
 <input type="radio" name="mode" value="user" <?php echo ((!isset($_GET['mode'])) || ($_GET['mode']=="user"))?"checked":"" ?> /> Developer<br />
 <input type="radio" name="mode" value="milestone" <?php echo ($_GET['mode']=="milestone")?"checked":"" ?> /> Milestone<br />
 <input type="checkbox" name="batchmode" value="true" <?php echo (isset($_GET['batchmode']) && $_GET['batchmode']=="true")?"checked":"" ?> />Batch Mode<br />
 <input type="submit" value="submit" />
</form>
</div>

<?php foreach($VERSION_COLORS as $key=>$val){?>
	<span style="color: #<?php echo $val; ?>;"><?php echo $key; ?></span>
<?php } ?>
<div style="position:relative" class="gantt" id="GanttChartDIV"></div>

<script language="javascript">

var g = new JSGantt.GanttChart('g',document.getElementById('GanttChartDIV'), 'day');
g.setShowRes(0); // Show/Hide Responsible (0/1)
g.setShowDur(0); // Show/Hide Duration (0/1)
g.setShowComp(0); // Show/Hide % Complete(0/1)
g.setCaptionType('Resource');  // Set to Show Caption (None,Caption,Resource,Duration,Complete)
g.setShowStartDate(0); // Show/Hide Start Date(0/1)
g.setShowEndDate(0); // Show/Hide End Date(0/1)
g.setDateInputFormat('mm/dd/yyyy');  // Set format of input dates ('mm/dd/yyyy', 'dd/mm/yyyy', 'yyyy-mm-dd')
g.setDateDisplayFormat('mm/dd/yyyy'); // Set format to display dates ('mm/dd/yyyy', 'dd/mm/yyyy', 'yyyy-mm-dd')
g.setFormatArr("day","week","month"); // Set format options (up to 4 : "minute","hour","day","week","month","quarter")

//g.AddTaskItem(new JSGantt.TaskItem(1,   'Define Chart API',     '',          '',          'ff0000', 'http://help.com', 0, 'Brian',     0, 1, 0, 1));
//g.AddTaskItem(new JSGantt.TaskItem(11,  'Chart Object',         '2/10/2008', '2/10/2008', 'ff00ff', 'http://www.yahoo.com', 1, 'Shlomy',  100, 0, 1, 1, "121,122", "My Caption"));
<?php if($_GET['mode']=="milestone") {?>
JSGantt.parseXML("gant_xml.php<?php echo $getvals ?>",g);
<?php }else{ ?>
JSGantt.parseXML("gant_xml_by_user.php<?php echo $getvals ?>",g);
<?php } ?>
g.Draw();	
g.DrawDependencies();

</script>

</body>

</html>


