<?php
	include("include/cookies.php");
	include_once("include/user.php");
	
	User::Init( new SQLite3("db/tracker.sqlite", SQLITE3_OPEN_READWRITE) );
	
	User::Logout();
		
	header("Location: $_SERVER[HTTP_REFERER]");
	die();
?>
