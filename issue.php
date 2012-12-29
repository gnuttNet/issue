<?php include("include/header.php");?>
<?php
	if($_GET['id'] != "new") {
		$result = $db->query("SELECT title,description,owner,assigned,createdate,updated FROM issues WHERE _ROWID_=$_GET[id]");
		$issue = $result->fetchArray(SQLITE3_ASSOC);
	
		$title = $issue['title'];	
		$description = $issue['description'];
		$createdate = $issue['createdate'];
		$updated = $issue['updated'];
		$owner = $issue['owner'];
		$assigned = $issue['assigned'];
	} else {
		$createdate = strftime("%s");
		$updated = strftime("%s");
		$owner = $_SESSION['UID'];
		$assigned = 0;

	}
?>
		<h1>Issue</h1>
		<form action="update.php" method="post">
			<input type="hidden" name="what" value="postissue" />
			<input type="hidden" name="issue" value="<?php echo $_GET['id'];?>" />
			<label for="title">Title:</label><input type="text" name="title" value="<?php echo $title;?>"/>
			<label for="assigned">Assigned to:</label>
			<select name="assigned">
				<option value="0">Unassigned</option>
<?php
	$users = $db->query("SELECT _ROWID_ as UID, email FROM users ORDER BY email");
	while($row = $users->fetchArray(SQLITE3_ASSOC)) {
		if($assigned == $row['UID']) {
			echo "<option value=\"$row[UID]\" SELECTED>$row[email]</option>\n";
		} else {
			echo "<option value=\"$row[UID]\">$row[email]</option>\n";
		}
	}
?>
			</select>
			<br />
			<label for="description">Description:</label><textarea name="description" rows="25" cols="80"><?php echo $description;?></textarea><br />
			<input type="submit" value="Post" />
		</form>
<?php include("include/footer.php");?>
