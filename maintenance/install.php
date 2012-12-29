<?php
	$db = new SQLite3(../db/users.sqlite, SQLITE3_OPEN_CREATE);

	$db->close();
?>
