<?php
	mkdir("db");
	$db = new SQLite3("db/users.sqlite");
	$db->query("CREATE TABLE users(email text unique, realname text, password text, salt text, admin integer);");
	$db->query("INSERT INTO users(email, realname, password, salt, admin) values(\"admin@localhost\",\"Dummy Administrator user\", \"" . sha1("adminsalt") . "\", \"salt\", 1)");
	echo "Adding Administrator user with email: admin@localhost, password: admin\n";
	$db->close();
?>
