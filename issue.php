<?php include("include/header.php");?>
<?php
	if($_GET['id'] != "new") {
		$result = $db->query("SELECT title,description,owner,assigned,createdate,updated,status FROM issues WHERE _ROWID_=$_GET[id]");
		$issue = $result->fetchArray(SQLITE3_ASSOC);
	
		$title = $issue['title'];	
		$description = $issue['description'];
		$createdate = $issue['createdate'];
		$updated = $issue['updated'];
		$owner = $issue['owner'];
		$assigned = $issue['assigned'];
		$status = $issue['status'];
	} else {
		$createdate = strftime("%s");
		$updated = strftime("%s");
		$owner = $_SESSION['UID'];
		$assigned = 0;
	}
?>
		<h1>Issue</h1>
<?php
	if(isset($_SESSION['message'])) {
		echo "<p>$_SESSION[message]</p>\n";
		unset($_SESSION['message']);
	}
?>
		<form action="database/update.php" method="post">
			<input type="hidden" name="what" value="postissue" />
			<input type="hidden" name="issue" value="<?php echo $_GET['id'];?>" />
			<label for="title">Title:</label><input type="text" name="title" value="<?php echo $title;?>" size="40" />
			<label for="status">Status</label>
			<select name="status">
				<option value="0" <?php if($status == 0) {echo "SELECTED";}?>>NEW</option>
				<option value="1" <?php if($status == 1) {echo "SELECTED";}?>>Accepted</option>
				<option value="2" <?php if($status == 2) {echo "SELECTED";}?>>Closed</option>
			</select>
			<label for="assigned">Assigned to:</label>
			<select name="assigned">
				<option value="0">Unassigned</option>
<?php
	$users = $db->query("SELECT _ROWID_ as UID, email FROM users ORDER BY email");
	while($row = $users->fetchArray(SQLITE3_ASSOC)) {
		if($assigned == $row['UID']) {
			echo "\t\t\t\t<option value=\"$row[UID]\" SELECTED>$row[email]</option>\n";
		} else {
			echo "\t\t\t\t<option value=\"$row[UID]\">$row[email]</option>\n";
		}
	}
?>
			</select>
			<br />
			<label for="description">Description:</label><textarea name="description" rows="25" cols="80"><?php echo $description;?></textarea><br />
			<input type="submit" value="Post" />
<?php
	$strCDate = strftime("%Y-%m-%d %H:%I:%S", $createdate);
	$strMDate = strftime("%Y-%m-%d %H:%I:%S", $updated);
	$strCreator = $db->querySingle("SELECT email FROM users WHERE _ROWID_=$owner");
	echo "\t\t\t<p>Issue created $strCDate by $strCreator. Last updated $strMDate</p>\n";
?>
		</form>
<?php include("include/footer.php");?>
