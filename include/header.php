<?php
	ini_set('display_errors', 1);
	// Whine about all errors
	error_reporting( E_ALL );
	include_once("settings.php");
	include_once("cookies.php");
	include_once("functions.php");
	include_once("user.php");
	
	$db = new SQLite3("db/tracker.sqlite");
	User::Init($db);
?>
<html>
	</head>
		<title><?php echo $TRACK_TITLE?></title>
		<link rel="StyleSheet" href="include/styelsheet.css" type="text/css" />
	</head>
	<body>
	<div id="header">
<?php
	$user = User::GetUserFromSession();
	if( !$user->IsLoggedIn() ) {
		echo "<a href=\"login.php\">Login</a>";
	} else {
		echo "<a href=\"issue.php?id=new\">[+] New Issue</a> | ";
		echo "<a href=\"listissues.php\">List Issues</a> | ";
		echo "<a href=\"usersettings.php\">$_SESSION[EMAIL]</a> | ";
		if( $user->IsAdmin() )
		{
			echo "<a href=\"usermanagement.php\">User management</a> |";
		}
		echo "<a href=\"logout.php\">Logout</a>";
	}
?>
	</div>
