<table>
<tr>
<th>
<?php echo $label; ?>
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
foreach($typeIssues as $label => $issues){
?>
<tr>
<td>
<?php echo $label; ?>
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