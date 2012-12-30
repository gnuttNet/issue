<?php
	include_once("settings.php");
	include_once("cookies.php");
	include_once("functions.php");
	
	$db = new SQLite3("db/tracker.sqlite");
?>
<html>
	</head>
		<title><?php echo $TRACK_TITLE?></title>
		<link rel="StyleSheet" href="include/styelsheet.css" type="text/css" />
	</head>
	<body>
	<div id="header">
<?php
	if(!isset($_SESSION['UID'])) {
		echo "<a href=\"login.php\">Login</a>";
	} else {
		echo "<a href=\"issue.php?id=new\">[+] New Issue</a> | ";
		echo "<a href=\"listissues.php\">List Issues</a> | ";
		echo "<a href=\"usersettings.php\">$_SESSION[EMAIL]</a> | ";
		echo "<a href=\"logout.php\">Logout</a>";
	}
?>
	</div>
