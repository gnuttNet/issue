<?php include("include/header.php"); ?>
		<h1>List issues</h1>
		<form action="update.php" method="post">
			<input type="hidden" name="what" value="closeissues" />
			<input type="submit" value="Close" />
<?php
	$result = $db->query("SELECT issues._ROWID_ as issue, issues.title as title, issues.assigned as assigned, issues.updated as updated, status as status FROM issues");
	echo "<table>\n";
	echo "<tr>\n";
	echo "<th />\n";
	echo "<th>Issue #1</th>\n";
	echo "<th>Status</th>\n";
	echo "<th>Title</th>\n";
	echo "<th>Assigned to</th>\n";
	echo "<th>Last updated</th>\n";
	echo "</tr>\n";
	while($row = $result->fetchArray(SQLITE3_ASSOC)) {
		if(strlen($row['title']) == 0) {$row['title'] = "&lt;Untitled&gt;";}
		echo "<tr>\n";
		echo "<td><input type=\"checkbox\" name=\"close[$row[issue]]\" /></td>\n";
		echo "<td>$row[issue]</td>\n";
		echo "<td>";
		if($row['status'] == 0) {echo "NEW";}
		if($row['status'] == 1) {echo "Accepted";}
		if($row['status'] == 2) {echo "Closed";}
		echo "</td>\n";
		echo "<td><a href=\"issue.php?id=$row[issue]\">$row[title]</a></td>\n";
		echo "<td>";
		if($row['assigned'] == -1) {
			echo "Unassigned";
		} else {
			$username = $db->querySingle("SELECT email FROM users WHERE _ROWID_=$row[assigned]");
			echo $username;
		}
		echo "</td>";
		echo "<td>" . strftime("%Y-%m-%d %H:%I:%S", $row['updated']) . "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
?>
		</form>
<?php include("include/footer.php"); ?>
