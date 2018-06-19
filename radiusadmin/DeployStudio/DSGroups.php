<?php include "../auth/checkLevel1.php";?>
<html>
<head>
<title>Manage Group Workflow Settings</title>
<link rel="stylesheet" href=../style.css>
<script>
function reload() {
	window.location = window.location.href;
}
</script>
</head>
<?php
include '../conf.php';
require_once('./DSFunctions.php');

function printWorkflowSelector($editFlag,$workflows=null,$workflowid=null) {
	$output = "\n<select name=workflow ";
	if (!$editFlag) $output = $output . "disabled ";
	$output = $output . ">\n";
	if ($workflowid) $output = $output . "<option value=\"none\"></option>\n";
	else $output = $output . "<option value=\"none\" selected></option>\n";
	if(!$workflows) $workflows = DSGetWorkflows();
	foreach($workflows as $key => $data) {
		if ($data['group'] == 'admin') continue;  //skip workflows that can't be accessed normally
		$output = $output . "<option value=\"$key\"";
		if ($key == $workflowid) $output = $output . " selected";
		$output = $output . ">" . $data['title'] . "</option>\n";
	}
	$output = $output . "</select>\v\n";
	return($output);
}

function printRow($id,$group,$workflowid,$workflows,$editFlag) {
	if ($editFlag) {
		printf("<tr bgcolor=red><td>%s</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Save\" OnClick=\"this.form.edit.value='1'\"><input type=hidden name=id value=%d></td></tr>\n",$group,printWorkflowSelector($editFlag,$workflows,$workflowid),$id);
	} else {
		printf("<tr><td>%s</td><td>%s</td><td><input type=\"Submit\" class=\"button\" value=\"Edit\" OnClick=\"this.form.edit.value='%d'\"></td></tr>\n",$group,printWorkflowSelector($editFlag,$workflows,$workflowid),$id);
	}
}

if (isset($_POST['edit'])) {$edit = $_POST['edit'];}else{$edit=null;}

if ($edit == 1) {
	DSSetWorkflow($_POST['id'],$_POST['workflow']);
	$edit=null;
	echo '<script language="javascript">window.location = window.location.href;</script>';
}

/*
if ($_POST['sync']) {
	//DSSync();
}
*/
?>

<body style="font-family:Courier">
<center>Deploy Studio Server Workflow Settings</center>
<form method="post">
<input type=hidden name=edit value=null>
<input type=hidden name=sync value=null>
<table>
<tr><th>Group</th><th>Workflow</th><th></th></tr>
<?php
DSSyncWorkflows();
DSSyncGroupData();
DSClearStaleGroups();
$workflows = DSGetWorkflows();
$query = "SELECT * FROM $wgdb.DSGroups ORDER BY DSGroup";
$result = mysqli_query($query) or die("$query - " . mysqli_error());
while($row = mysqli_fetch_assoc($result)) {
	if ($edit == $row['id']) printRow($row['id'],$row['DSGroup'],$row['DSWorkflow'],$workflows,TRUE);
	else printRow($row['id'],$row['DSGroup'],$row['DSWorkflow'],$workflows,false);
}
?>
</table>
<?php 

//if (DSGenerateGroups()) echo"<br>New groups available.  Please refresh.  <input type=button value=\"refresh\" onClick=\"window.location.reload()\">";
//else if (!$edit) echo "<input type=button value=\"refresh\" onClick=\"this.form.sync.value=1\">";

if (!$edit) echo "<input type=button value=\"refresh\" onClick=\"window.location.reload()\">";
else echo"<input type=button value=\"cancel\" onClick=\"reload()\">"

?>
</form>
</body>
</html>