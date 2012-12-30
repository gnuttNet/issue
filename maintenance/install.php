<?php
	include_once( "include/password_functions.php" );
	
	$randomPassword = generateRandomString( 8 );
	
	mkdir("db");
	
	$db = new SQLite3("db/tracker.sqlite");
	$db->exec("CREATE TABLE users(email text unique, realname text, password text, salt text, admin integer);");
	$db->exec("INSERT INTO users(email, realname, password, salt, admin) values(\"admin@localhost\",\"Dummy Administrator user\", \"" . sha1($randomPassword."salt") . "\", \"salt\", 1)");
	echo "Adding Administrator user with email: admin@localhost, password: ".$randomPassword."\n";
	$db->exec("CREATE TABLE issues(title text, description text, owner integer, assigned integer, createdate integer, updated integer)");
	$db->close();

	chgrp("db/tracker.sqlite", "www-data");
	chmod("db/tracker.sqlite", 0664);
	chmod("db", 0775);
	chgrp("db", "www-data");
?>
