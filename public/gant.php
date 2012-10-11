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
	<!-- <script language="javascript" src="js/jquery-1.4.2.min.js"></script> -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	<script language="javascript" src="js/jquery.contextMenu.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
	<script language="javascript" src="js/jsgantt.js"></script>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css" media="all" />
	<script>
	$(function() {
	currentText: "Now"
		$( "#datepicker" ).datepicker( {dateFormat: "yy-mm-dd"} );
	});
	</script>
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
			}else if(actions[1]=="Low"){
				$.get('update_issue_priority_tag.php?repo='+repo+'&ticket='+ticket_id+'&priority=Low', function(data) {
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="Medium"){
				$.get('update_issue_priority_tag.php?repo='+repo+'&ticket='+ticket_id+'&priority=Medium', function(data) {
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="High"){
				$.get('update_issue_priority_tag.php?repo='+repo+'&ticket='+ticket_id+'&priority=High', function(data) {
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="Urgent"){
				$.get('update_issue_priority_tag.php?repo='+repo+'&ticket='+ticket_id+'&priority=Urgent', function(data) {
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="2h"){
				$.get('update_issue_time_tag.php?repo='+repo+'&ticket='+ticket_id+'&time='+escape('2 hours'), function(data) {
				  console.log(data);
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="4h"){
				$.get('update_issue_time_tag.php?repo='+repo+'&ticket='+ticket_id+'&time='+escape('4 hours'), function(data) {
				  console.log(data);
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="1d"){
				$.get('update_issue_time_tag.php?repo='+repo+'&ticket='+ticket_id+'&time='+escape('1 day'), function(data) {
				console.log(data);
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="2d"){
				$.get('update_issue_time_tag.php?repo='+repo+'&ticket='+ticket_id+'&time='+escape('2 days'), function(data) {
				  console.log(data);
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="3d"){
				$.get('update_issue_time_tag.php?repo='+repo+'&ticket='+ticket_id+'&time='+escape('3 days'), function(data) {
				  console.log(data);
				  $('.result').html(data);
				  maybeReload();
				});
			}else if(actions[1]=="1w"){
				$.get('update_issue_time_tag.php?repo='+repo+'&ticket='+ticket_id+'&time='+escape('1 week'), function(data) {
				  console.log(data);
				  $('.result').html(data);
				  maybeReload();
				});
			}else{
				//$.get('update_ticket_priority.php?repo='+repo+'&ticket='+ticket_id+'&action=update&priority='+actions[1], function(data) {
				 // $('.result').html(data);
				  //maybeReload();
				//});
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
			Move Up
		</li>
		<li class="separator">
			<a title="Decrease Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:decrease"><img src="images/remove.png" width="20px"/></a>
			Move Down
		</li>
		<li class="separator" style="background-color:#33ff33;">
			<a title="Low Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:Low">Low Priority</a>
		</li>
		<li class="separator" style="background-color:#0088ff;">
			<a title="Medium Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:Medium">Medium Priority</a>
		</li>
		<li class="separator" style="background-color:#ff8800;">
			<a title="High Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:High">High Priority</a>
		</li>
		<li class="separator" style="background-color:#ff0000;">
			<a title="Urgent Priority" style="display: inline; padding: 0; margin: 0;" href="#priority:-:Urgent">Urgent Priority</a>
		</li>
		<li class="separator">
			<a title="2 Hours" style="display: inline; padding: 0; margin: 0;" href="#priority:-:2h">2 Hours</a>
		</li>
		<li class="separator">
			<a title="4 Hours" style="display: inline; padding: 0; margin: 0;" href="#priority:-:4h">4 Hours</a>
		</li>
		<li class="separator">
			<a title="1 Day" style="display: inline; padding: 0; margin: 0;" href="#priority:-:1d">1 Day</a>
		</li>
		<li class="separator">
			<a title="2 Days" style="display: inline; padding: 0; margin: 0;" href="#priority:-:2d">2 Days</a>
		</li>
		<li class="separator">
			<a title="3 Days" style="display: inline; padding: 0; margin: 0;" href="#priority:-:3d">3 Days</a>
		</li>
		<li class="separator">
			<a title="1 Week" style="display: inline; padding: 0; margin: 0;" href="#priority:-:1w">1 Week</a>
		</li>
	</ul>
<?php } ?>

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
 <input type="checkbox" name="showmine" value="true" <?php echo (isset($_GET['showmine']) && $_GET['showmine']=="true")?"checked":"" ?> />Show Mine<br />
 Last Reviewed<br />
 <input type="text" name="last_review" id="datepicker" value="<?php echo $_GET['last_review']; ?>"><br />
 <input type="submit" value="submit" />
</form>
<a target="_blank" href="update_milestones.php?repo=amplify-back-end">Update Milestones</a>
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
<?php if(isset($_GET['mode']) && $_GET['mode']=="milestone") {?>
JSGantt.parseXML("gant_xml.php<?php echo $getvals ?>",g);
<?php }else{?>
JSGantt.parseXML("gant_xml_by_user.php<?php echo $getvals ?>last_review=<?php echo $_GET['last_review']; ?>&showmine=<?php echo $_GET['showmine']; ?>",g);
<?php } ?>
g.Draw();	
g.DrawDependencies();

</script>

</body>

</html>


