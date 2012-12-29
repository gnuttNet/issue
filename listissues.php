<?php include("include/header.php"); ?>
		<h1>List issues</h1>
<?php
	$result = $db->query("SELECT issues._ROWID_ as issue, issues.title as title, issues.assigned as assigned, issues.updated as updated FROM issues");
	echo "<table>\n";
	echo "<tr>\n";
	echo "<th>Issue #1</th>\n";
	echo "<th>Title</th>\n";
	echo "<th>Assigned to</th>\n";
	echo "<th>Last updated</th>\n";
	echo "</tr>\n";
	while($row = $result->fetchArray(SQLITE3_ASSOC)) {
		echo "<tr>\n";
		echo "<td>$row[issue]</td>\n";
		echo "<td>$row[title]</td>\n";
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
<?php include("include/footer.php"); ?>
