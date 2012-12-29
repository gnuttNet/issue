<?php include("include/header.php"); ?>
		<h1>List issues</h1>
<?php
	$result = $db->query("SELECT * FROM issues");
	while($row = $result->fetchArray(SQLITE3_ASSOC)) {
		print_r($row);
	}
?>
<?php include("include/footer.php"); ?>
